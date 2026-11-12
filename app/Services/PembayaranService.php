<?php

namespace App\Services;

use App\Core\Database;
use App\Models\TransaksiPembayaran;
use App\Models\WorkflowLayanan;
use PDO;
use PDOException;

class PembayaranService
{
    /** Batas ukuran bukti: 5 MB */
    public const MAKS_BYTE = 5 * 1024 * 1024;

    private PDO $db;
    private TransaksiPembayaran $transaksi;
    private WorkflowLayanan $workflow;

    public function __construct()
    {
        $this->db        = Database::getInstance()->getConnection();
        $this->transaksi = new TransaksiPembayaran();
        $this->workflow  = new WorkflowLayanan();
    }

    /** Folder fisik bukti (di luar public/) */
    public static function dirAbs(): string
    {
        return BASE_PATH . '/storage/bukti-transfer';
    }

    /** Info rekening studio, dibaca dari .env */
    public static function rekening(): array
    {
        return [
            'bank'      => $_ENV['PAYMENT_BANK']        ?? '-',
            'nomor'     => $_ENV['PAYMENT_NO_REKENING'] ?? '-',
            'atas_nama' => $_ENV['PAYMENT_ATAS_NAMA']   ?? '-',
        ];
    }

    /**
     * Pelanggan mengirim pembayaran (atau kirim ulang setelah ditolak).
     *
     * @param array      $pemesanan baris dari Pemesanan::findByKodeUnik (punya id, total_tagihan)
     * @param array|null $file      entri $_FILES['bukti']
     * @return array{ok:bool, error?:string}
     */
    public function kirim(array $pemesanan, string $metode, ?string $noRef, ?array $file): array
    {
        $pemesananId = (int) $pemesanan['id'];
        $lama        = $this->transaksi->findByPemesanan($pemesananId);

        if ($lama !== null && $lama['status_pembayaran'] !== STATUS_DITOLAK) {
            return $this->gagal(
                $lama['status_pembayaran'] === STATUS_LUNAS
                    ? 'Pembayaran untuk pesanan ini sudah lunas.'
                    : 'Pembayaran Anda sedang menunggu verifikasi.'
            );
        }

        if (!in_array($metode, ['transfer', 'langsung'], true)) {
            return $this->gagal('Metode pembayaran tidak valid.');
        }

        $jumlah = (float) $pemesanan['total_tagihan'];

        if ($jumlah <= 0) {
            return $this->gagal('Total tagihan tidak valid.');
        }

        // Upload dulu (hanya untuk transfer). Kalau gagal, tidak ada yang tersimpan.
        $bukti = null;

        if ($metode === 'transfer') {
            $up = UploadService::simpan(
                $file,
                self::dirAbs(),
                UploadService::MIME_BUKTI,
                self::MAKS_BYTE
            );

            if (!$up['ok']) {
                return $this->gagal($up['error']);
            }

            $bukti = $up['nama_file'];
        }

        $data = [
            'pemesanan_id'    => $pemesananId,
            'metode'          => $metode,
            'jumlah'          => (string) $jumlah,
            'nomor_referensi' => ($noRef !== null && $noRef !== '') ? $noRef : null,
            'bukti'           => $bukti,
        ];

        try {
            $this->db->beginTransaction();

            $saatIni = $this->workflow->kunciStatus($pemesananId);

            if ($saatIni === null) {
                $this->db->rollBack();
                $this->hapusBukti($bukti);

                return $this->gagal('Pemesanan tidak ditemukan.');
            }

            if ($lama === null) {
                $this->transaksi->create($data);
            } else {
                $this->transaksi->kirimUlang((int) $lama['id'], $data);
            }

            // Pemesanan baru dibuat -> otomatis masuk "menunggu pembayaran"
            if ($saatIni === STATUS_PEMESANAN_DIBUAT) {
                $this->workflow->ubahStatusOtomatis($pemesananId, STATUS_MENUNGGU_PEMBAYARAN);
                $this->workflow->create(
                    $pemesananId,
                    $saatIni,
                    STATUS_MENUNGGU_PEMBAYARAN,
                    $metode === 'transfer'
                        ? 'Pelanggan mengunggah bukti transfer'
                        : 'Pelanggan memilih bayar langsung di studio',
                    null
                );
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            $this->hapusBukti($bukti);

            // 23505 = unique_violation (dua request bersamaan untuk pemesanan yang sama)
            if ($e instanceof PDOException && (string) $e->getCode() === '23505') {
                return $this->gagal('Pembayaran untuk pesanan ini sudah tercatat.');
            }

            throw $e;
        }

        // Bukti lama (yang ditolak) tidak dipakai lagi
        if ($lama !== null && !empty($lama['bukti_transfer'])) {
            $this->hapusBukti($lama['bukti_transfer']);
        }

        return ['ok' => true];
    }

    /**
     * Pegawai memverifikasi pembayaran -> lunas, status pemesanan otomatis
     * maju sampai "pembayaran_diverifikasi".
     *
     * @return array{ok:bool, error?:string}
     */
    public function verifikasi(int $transaksiId, int $pegawaiId): array
    {
        try {
            $this->db->beginTransaction();

            $t = $this->transaksi->kunci($transaksiId);

            if ($t === null) {
                $this->db->rollBack();

                return $this->gagal('Transaksi tidak ditemukan.');
            }

            if ($t['status_pembayaran'] !== STATUS_PENDING) {
                $this->db->rollBack();

                return $this->gagal('Transaksi ini sudah diproses sebelumnya.');
            }

            $this->transaksi->updateStatus($transaksiId, STATUS_LUNAS, $pegawaiId);

            $this->majukanSampai(
                (int) $t['pemesanan_id'],
                STATUS_PEMBAYARAN_DIVERIFIKASI,
                $pegawaiId,
                'Pembayaran diverifikasi'
            );

            $this->db->commit();

            return ['ok' => true];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Pegawai menolak pembayaran. Status pemesanan tetap "menunggu pembayaran";
     * pelanggan bisa mengirim ulang.
     *
     * @return array{ok:bool, error?:string}
     */
    public function tolak(int $transaksiId, int $pegawaiId): array
    {
        try {
            $this->db->beginTransaction();

            $t = $this->transaksi->kunci($transaksiId);

            if ($t === null) {
                $this->db->rollBack();

                return $this->gagal('Transaksi tidak ditemukan.');
            }

            if ($t['status_pembayaran'] !== STATUS_PENDING) {
                $this->db->rollBack();

                return $this->gagal('Transaksi ini sudah diproses sebelumnya.');
            }

            $this->transaksi->updateStatus($transaksiId, STATUS_DITOLAK, $pegawaiId);

            $this->db->commit();

            return ['ok' => true];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    // ------------------------------------------------------------------

    /**
     * Majukan status pemesanan tahap demi tahap sampai $tujuan (kalau belum sampai),
     * setiap langkah dicatat di workflow_layanan. Harus di dalam transaksi.
     */
    private function majukanSampai(int $pemesananId, string $tujuan, int $pegawaiId, string $catatan): void
    {
        $saatIni = $this->workflow->kunciStatus($pemesananId);
        $target  = WorkflowService::indeks($tujuan);

        while ($saatIni !== null && WorkflowService::indeks($saatIni) < $target) {
            $berikut = WorkflowService::statusBerikutnya($saatIni);

            if ($berikut === null) {
                break;
            }

            $this->workflow->ubahStatusPemesanan($pemesananId, $berikut, $pegawaiId);
            $this->workflow->create($pemesananId, $saatIni, $berikut, $catatan, $pegawaiId);

            $saatIni = $berikut;
        }
    }

    private function hapusBukti(?string $namaFile): void
    {
        if ($namaFile !== null && $namaFile !== '') {
            UploadService::hapus(self::dirAbs(), $namaFile);
        }
    }

    private function gagal(string $pesan): array
    {
        return ['ok' => false, 'error' => $pesan];
    }
}