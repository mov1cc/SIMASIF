CREATE TYPE jenis_harga_layanan AS ENUM ('tunggal', 'pilihan');
CREATE TYPE keterangan_harga_layanan AS ENUM ('layanan', 'foto');

ALTER TABLE layanan
    ADD COLUMN jenis_harga            jenis_harga_layanan      NOT NULL DEFAULT 'tunggal',
    ADD COLUMN keterangan_harga       keterangan_harga_layanan NOT NULL DEFAULT 'layanan',
    ADD COLUMN durasi_nilai           INTEGER,
    ADD COLUMN durasi_satuan          VARCHAR(10) NOT NULL DEFAULT 'Menit',
    ADD COLUMN estimasi_hasil_nilai   INTEGER,
    ADD COLUMN estimasi_hasil_satuan  VARCHAR(10) NOT NULL DEFAULT 'Hari',
    ADD COLUMN kebutuhan_jadwal       BOOLEAN NOT NULL DEFAULT TRUE,
    ADD CONSTRAINT layanan_durasi_satuan_check CHECK (durasi_satuan IN ('Menit','Jam','Hari')),
    ADD CONSTRAINT layanan_hasil_satuan_check  CHECK (estimasi_hasil_satuan IN ('Menit','Jam','Hari','Minggu')),
    ADD CONSTRAINT layanan_durasi_nilai_check  CHECK (durasi_nilai > 0),
    ADD CONSTRAINT layanan_hasil_nilai_check   CHECK (estimasi_hasil_nilai > 0),
    ADD CONSTRAINT layanan_maks_orang_max      CHECK (maks_orang <= 999);

COMMENT ON COLUMN layanan.jenis_harga IS 'tunggal = satu harga di layanan.harga; pilihan = harga ada di layanan_harga_pilihan (layanan.harga diisi harga terendah sebagai "mulai dari")';
COMMENT ON COLUMN layanan.keterangan_harga IS 'Satuan harga yang ditampilkan: /Layanan atau /Foto';
COMMENT ON COLUMN layanan.durasi_nilai IS 'Nilai durasi sesuai input form; estimasi_durasi_menit tetap diisi (hasil konversi) untuk jadwal';
COMMENT ON COLUMN layanan.estimasi_hasil_nilai IS 'Nilai estimasi hasil sesuai input form; estimasi_hasil_hari tetap diisi (dibulatkan ke atas, min 1)';
COMMENT ON COLUMN layanan.kebutuhan_jadwal IS 'TRUE = layanan membutuhkan jadwal sesi';

-- Data lama
UPDATE layanan SET durasi_nilai = estimasi_durasi_menit WHERE estimasi_durasi_menit IS NOT NULL;
UPDATE layanan SET estimasi_hasil_nilai = estimasi_hasil_hari WHERE estimasi_hasil_hari IS NOT NULL;

CREATE UNIQUE INDEX uq_layanan_nama_lower ON layanan (lower(nama));

CREATE TABLE layanan_harga_grup (
    id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    layanan_id INTEGER NOT NULL REFERENCES layanan(id) ON DELETE CASCADE,
    nama VARCHAR(100) NOT NULL CHECK (nama <> ''),
    urutan INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX idx_harga_grup_layanan ON layanan_harga_grup (layanan_id, urutan);

CREATE TABLE layanan_harga_pilihan (
    id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    grup_id INTEGER NOT NULL REFERENCES layanan_harga_grup(id) ON DELETE CASCADE,
    nama VARCHAR(100) NOT NULL CHECK (nama <> ''),
    harga NUMERIC(12,2) NOT NULL CHECK (harga >= 1),
    urutan INTEGER NOT NULL DEFAULT 0
);
CREATE UNIQUE INDEX uq_harga_pilihan_nama ON layanan_harga_pilihan (grup_id, lower(nama));

CREATE TABLE layanan_output (
    id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    layanan_id INTEGER NOT NULL REFERENCES layanan(id) ON DELETE CASCADE,
    nama VARCHAR(150) NOT NULL CHECK (nama <> ''),
    tipe VARCHAR(10) NOT NULL DEFAULT 'non' CHECK (tipe IN ('non','pilihan')),
    urutan INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX idx_output_layanan ON layanan_output (layanan_id, urutan);

CREATE TABLE layanan_output_pilihan (
    id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    output_id INTEGER NOT NULL REFERENCES layanan_output(id) ON DELETE CASCADE,
    nama VARCHAR(150) NOT NULL CHECK (nama <> ''),
    urutan INTEGER NOT NULL DEFAULT 0
);
CREATE UNIQUE INDEX uq_output_pilihan_nama ON layanan_output_pilihan (output_id, lower(nama));

COMMENT ON TABLE layanan_harga_grup IS 'Kelompok harga untuk layanan berjenis harga pilihan (mis. Hard File, Soft File)';
COMMENT ON TABLE layanan_harga_pilihan IS 'Pilihan + harga dalam satu kelompok harga';
COMMENT ON TABLE layanan_output IS 'Output/deliverables layanan; tipe pilihan punya sub pilihan';
COMMENT ON TABLE layanan_output_pilihan IS 'Sub pilihan sebuah output (mis. warna background)';

-- Pindahkan deliverables lama (satu baris = satu output) ke layanan_output
INSERT INTO layanan_output (layanan_id, nama, tipe, urutan)
SELECT l.id, btrim(t.baris), 'non', t.n - 1
  FROM layanan l,
       LATERAL regexp_split_to_table(l.deliverables, E'\\r?\\n') WITH ORDINALITY AS t(baris, n)
 WHERE l.deliverables IS NOT NULL AND btrim(t.baris) <> '';