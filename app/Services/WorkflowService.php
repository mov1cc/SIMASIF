<?php

namespace App\Services;

use App\Core\Database;
use App\Helpers\FormatHelper;
use App\Models\WorkflowLayanan;
use PDO;
use App\Models\TransaksiPembayaran;

class WorkflowService
{
    private PDO $db;
    private WorkflowLayanan $workflow;

    public function __construct()
    {
        $this->db       = Database::getInstance()->getConnection();
        $this->workflow = new WorkflowLayanan();
    }

    /** @return string[] kode status berurutan sesuai alur */
    public static function urutan(): array
    {
        return array_keys(FormatHelper::daftarStatusPemesanan());
    }

    /** Indeks status dalam alur, -1 kalau tidak dikenal */
    public static function indeks(?string $status): int
    {
        $i = array_search($status, self::urutan(), true);

        return $i === false ? -1 : $i;
    }

    /** Status sesudahnya, null kalau sudah di tahap terakhir / tidak dikenal */
    public static function statusBerikutnya(?string $status): ?string
    {
        $i = self::indeks($status);

        return $i < 0 ? null : (self::urutan()[$i + 1] ?? null);
    }

    /** Persentase progres untuk progress bar (tahap ke-n dari total) */
    public static function persen(?string $status): int
    {
        $i = self::indeks($status);

        return $i < 0 ? 0 : (int) round(($i + 1) / count(self::urutan()) * 100);
    }

    /**
     * Daftar tahap untuk stepper.
     * state: 'selesai' | 'aktif' | 'menunggu'
     *
     * @return array<int, array{kode:string, label:string, state:string}>
     */
    public static function tahapan(?string $status): array
    {
        $aktif   = self::indeks($status);
        $terakhir = count(self::urutan()) - 1;
        $hasil   = [];

        foreach (FormatHelper::daftarStatusPemesanan() as $kode => $label) {
            $i = self::indeks($kode);

            if ($aktif === $terakhir || $i < $aktif) {
                $state = 'selesai';
            } elseif ($i === $aktif) {
                $state = 'aktif';
            } else {
                $state = 'menunggu';
            }

            $hasil[] = ['kode' => $kode, 'label' => $label, 'state' => $state];
        }

        return $hasil;
    }

    /**
     * Ubah status pemesanan satu langkah ke depan.
     *
     * @return array{ok:bool, error?:string, dari?:string, ke?:string}
     */
    public function ubahStatus(
        int $pemesananId,
        string $statusBaru,
        int $userId,
        ?string $catatan = null
    ): array {
        try {
            $this->db->beginTransaction();

            $saatIni = $this->workflow->kunciStatus($pemesananId);

            if ($saatIni === null) {
                $this->db->rollBack();

                return $this->gagal('Pemesanan tidak ditemukan.');
            }

            $berikutnya = self::statusBerikutnya($saatIni);

            if ($berikutnya === null) {
                $this->db->rollBack();

                return $this->gagal('Pemesanan sudah selesai, status tidak bisa diubah lagi.');
            }

            // Halaman bisa saja sudah usang (status sudah dimajukan orang lain)
            if ($statusBaru !== $berikutnya) {
                $this->db->rollBack();

                return $this->gagal(sprintf(
                    'Status tidak bisa diubah ke "%s". Status saat ini "%s", tahap berikutnya adalah "%s".',
                    FormatHelper::statusPemesanan($statusBaru),
                    FormatHelper::statusPemesanan($saatIni),
                    FormatHelper::statusPemesanan($berikutnya)
                ));
            }

                        // Tahap 10: tidak boleh masuk "pembayaran diverifikasi" sebelum transaksi lunas
            if ($berikutnya === STATUS_PEMBAYARAN_DIVERIFIKASI) {
                $trx = (new TransaksiPembayaran())->findByPemesanan($pemesananId);

                if ($trx === null || $trx['status_pembayaran'] !== STATUS_LUNAS) {
                    $this->db->rollBack();

                    return $this->gagal(
                        'Pembayaran belum diverifikasi lunas. Verifikasi dulu lewat menu Transaksi.'
                    );
                }
            }
            // tambahkan syarat transaksi berstatus 'lunas'.

            $this->workflow->ubahStatusPemesanan($pemesananId, $berikutnya, $userId);
            $this->workflow->create($pemesananId, $saatIni, $berikutnya, $catatan, $userId);

            $this->db->commit();

            return ['ok' => true, 'dari' => $saatIni, 'ke' => $berikutnya];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    private function gagal(string $pesan): array
    {
        return ['ok' => false, 'error' => $pesan];
    }
}