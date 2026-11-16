--
-- PostgreSQL database dump
--

\restrict ChlPrtrfch7TFyCnQVAoY8ZT8bSn01BBcX8ZgYhB19i3TQXLNGoZ0gnE2dERKtz

-- Dumped from database version 18.6 (Ubuntu 18.6-0ubuntu0.26.04.1)
-- Dumped by pg_dump version 18.6 (Ubuntu 18.6-0ubuntu0.26.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE IF EXISTS ONLY public.workflow_layanan DROP CONSTRAINT IF EXISTS workflow_layanan_pemesanan_id_fkey;
ALTER TABLE IF EXISTS ONLY public.workflow_layanan DROP CONSTRAINT IF EXISTS workflow_layanan_diubah_oleh_fkey;
ALTER TABLE IF EXISTS ONLY public.transaksi_pembayaran DROP CONSTRAINT IF EXISTS transaksi_pembayaran_pemesanan_id_fkey;
ALTER TABLE IF EXISTS ONLY public.transaksi_pembayaran DROP CONSTRAINT IF EXISTS transaksi_pembayaran_diverifikasi_oleh_fkey;
ALTER TABLE IF EXISTS ONLY public.pemesanan DROP CONSTRAINT IF EXISTS pemesanan_pelanggan_id_fkey;
ALTER TABLE IF EXISTS ONLY public.pemesanan DROP CONSTRAINT IF EXISTS pemesanan_pegawai_id_fkey;
ALTER TABLE IF EXISTS ONLY public.pemesanan DROP CONSTRAINT IF EXISTS pemesanan_layanan_id_fkey;
ALTER TABLE IF EXISTS ONLY public.pemesanan_addon DROP CONSTRAINT IF EXISTS pemesanan_addon_pemesanan_id_fkey;
ALTER TABLE IF EXISTS ONLY public.pemesanan_addon DROP CONSTRAINT IF EXISTS pemesanan_addon_addon_id_fkey;
ALTER TABLE IF EXISTS ONLY public.layanan_output_pilihan DROP CONSTRAINT IF EXISTS layanan_output_pilihan_output_id_fkey;
ALTER TABLE IF EXISTS ONLY public.layanan_output DROP CONSTRAINT IF EXISTS layanan_output_layanan_id_fkey;
ALTER TABLE IF EXISTS ONLY public.layanan_harga_pilihan DROP CONSTRAINT IF EXISTS layanan_harga_pilihan_grup_id_fkey;
ALTER TABLE IF EXISTS ONLY public.layanan_harga_grup DROP CONSTRAINT IF EXISTS layanan_harga_grup_layanan_id_fkey;
ALTER TABLE IF EXISTS ONLY public.layanan_gambar DROP CONSTRAINT IF EXISTS layanan_gambar_layanan_id_fkey;
ALTER TABLE IF EXISTS ONLY public.layanan_gambar DROP CONSTRAINT IF EXISTS layanan_gambar_diunggah_oleh_fkey;
ALTER TABLE IF EXISTS ONLY public.file_fotografi DROP CONSTRAINT IF EXISTS file_fotografi_pemesanan_id_fkey;
ALTER TABLE IF EXISTS ONLY public.file_fotografi DROP CONSTRAINT IF EXISTS file_fotografi_diunggah_oleh_fkey;
ALTER TABLE IF EXISTS ONLY public.audit_log DROP CONSTRAINT IF EXISTS audit_log_user_id_fkey;
ALTER TABLE IF EXISTS ONLY public.addon_layanan DROP CONSTRAINT IF EXISTS addon_layanan_layanan_id_fkey;
DROP TRIGGER IF EXISTS sync_biaya_tambahan ON public.pemesanan_addon;
DROP TRIGGER IF EXISTS set_updated_at_users ON public.users;
DROP TRIGGER IF EXISTS set_updated_at_transaksi_pembayaran ON public.transaksi_pembayaran;
DROP TRIGGER IF EXISTS set_updated_at_pemesanan ON public.pemesanan;
DROP TRIGGER IF EXISTS set_updated_at_pelanggan ON public.pelanggan;
DROP TRIGGER IF EXISTS set_updated_at_layanan ON public.layanan;
DROP TRIGGER IF EXISTS set_updated_at_addon_layanan ON public.addon_layanan;
DROP INDEX IF EXISTS public.uq_output_pilihan_nama;
DROP INDEX IF EXISTS public.uq_layanan_nama_lower;
DROP INDEX IF EXISTS public.uq_layanan_gambar_cover;
DROP INDEX IF EXISTS public.uq_harga_pilihan_nama;
DROP INDEX IF EXISTS public.idx_workflow_pemesanan;
DROP INDEX IF EXISTS public.idx_workflow_diubah_oleh;
DROP INDEX IF EXISTS public.idx_workflow_created_at;
DROP INDEX IF EXISTS public.idx_transaksi_pembayaran_verifikasi;
DROP INDEX IF EXISTS public.idx_transaksi_pembayaran_status;
DROP INDEX IF EXISTS public.idx_pemesanan_status;
DROP INDEX IF EXISTS public.idx_pemesanan_pelanggan;
DROP INDEX IF EXISTS public.idx_pemesanan_pegawai;
DROP INDEX IF EXISTS public.idx_pemesanan_jadwal;
DROP INDEX IF EXISTS public.idx_pemesanan_addon_addon;
DROP INDEX IF EXISTS public.idx_output_layanan;
DROP INDEX IF EXISTS public.idx_layanan_gambar_layanan;
DROP INDEX IF EXISTS public.idx_harga_grup_layanan;
DROP INDEX IF EXISTS public.idx_file_pengunggah;
DROP INDEX IF EXISTS public.idx_file_pemesanan;
DROP INDEX IF EXISTS public.idx_file_jenis;
DROP INDEX IF EXISTS public.idx_audit_user;
DROP INDEX IF EXISTS public.idx_audit_modul;
DROP INDEX IF EXISTS public.idx_audit_created;
DROP INDEX IF EXISTS public.idx_addon_layanan_layanan;
ALTER TABLE IF EXISTS ONLY public.workflow_layanan DROP CONSTRAINT IF EXISTS workflow_layanan_pkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_pkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_email_key;
ALTER TABLE IF EXISTS ONLY public.pemesanan_addon DROP CONSTRAINT IF EXISTS uq_pemesanan_addon;
ALTER TABLE IF EXISTS ONLY public.addon_layanan DROP CONSTRAINT IF EXISTS uq_addon_layanan_nama;
ALTER TABLE IF EXISTS ONLY public.transaksi_pembayaran DROP CONSTRAINT IF EXISTS transaksi_pembayaran_pkey;
ALTER TABLE IF EXISTS ONLY public.transaksi_pembayaran DROP CONSTRAINT IF EXISTS transaksi_pembayaran_pemesanan_id_key;
ALTER TABLE IF EXISTS ONLY public.pemesanan DROP CONSTRAINT IF EXISTS pemesanan_pkey;
ALTER TABLE IF EXISTS ONLY public.pemesanan DROP CONSTRAINT IF EXISTS pemesanan_kode_unik_key;
ALTER TABLE IF EXISTS ONLY public.pemesanan_addon DROP CONSTRAINT IF EXISTS pemesanan_addon_pkey;
ALTER TABLE IF EXISTS ONLY public.pelanggan DROP CONSTRAINT IF EXISTS pelanggan_pkey;
ALTER TABLE IF EXISTS ONLY public.pelanggan DROP CONSTRAINT IF EXISTS pelanggan_no_hp_key;
ALTER TABLE IF EXISTS ONLY public.layanan DROP CONSTRAINT IF EXISTS layanan_pkey;
ALTER TABLE IF EXISTS ONLY public.layanan_output DROP CONSTRAINT IF EXISTS layanan_output_pkey;
ALTER TABLE IF EXISTS ONLY public.layanan_output_pilihan DROP CONSTRAINT IF EXISTS layanan_output_pilihan_pkey;
ALTER TABLE IF EXISTS ONLY public.layanan_harga_pilihan DROP CONSTRAINT IF EXISTS layanan_harga_pilihan_pkey;
ALTER TABLE IF EXISTS ONLY public.layanan_harga_grup DROP CONSTRAINT IF EXISTS layanan_harga_grup_pkey;
ALTER TABLE IF EXISTS ONLY public.layanan_gambar DROP CONSTRAINT IF EXISTS layanan_gambar_pkey;
ALTER TABLE IF EXISTS ONLY public.file_fotografi DROP CONSTRAINT IF EXISTS file_fotografi_pkey;
ALTER TABLE IF EXISTS ONLY public.audit_log DROP CONSTRAINT IF EXISTS audit_log_pkey;
ALTER TABLE IF EXISTS ONLY public.addon_layanan DROP CONSTRAINT IF EXISTS addon_layanan_pkey;
DROP VIEW IF EXISTS public.v_riwayat_workflow;
DROP TABLE IF EXISTS public.workflow_layanan;
DROP VIEW IF EXISTS public.v_detail_pemesanan;
DROP VIEW IF EXISTS public.v_detail_pembayaran;
DROP TABLE IF EXISTS public.users;
DROP TABLE IF EXISTS public.transaksi_pembayaran;
DROP TABLE IF EXISTS public.pemesanan_addon;
DROP TABLE IF EXISTS public.pemesanan;
DROP TABLE IF EXISTS public.pelanggan;
DROP TABLE IF EXISTS public.layanan_output_pilihan;
DROP TABLE IF EXISTS public.layanan_output;
DROP TABLE IF EXISTS public.layanan_harga_pilihan;
DROP TABLE IF EXISTS public.layanan_harga_grup;
DROP TABLE IF EXISTS public.layanan_gambar;
DROP TABLE IF EXISTS public.layanan;
DROP TABLE IF EXISTS public.file_fotografi;
DROP TABLE IF EXISTS public.audit_log;
DROP TABLE IF EXISTS public.addon_layanan;
DROP FUNCTION IF EXISTS public.trigger_sync_biaya_tambahan();
DROP FUNCTION IF EXISTS public.trigger_set_updated_at();
DROP TYPE IF EXISTS public.user_role;
DROP TYPE IF EXISTS public.status_pemesanan;
DROP TYPE IF EXISTS public.status_pembayaran;
DROP TYPE IF EXISTS public.status_file_fotografi;
DROP TYPE IF EXISTS public.metode_pembayaran;
DROP TYPE IF EXISTS public.keterangan_harga_layanan;
DROP TYPE IF EXISTS public.jenis_harga_layanan;
DROP TYPE IF EXISTS public.jenis_file_fotografi;
--
-- Name: jenis_file_fotografi; Type: TYPE; Schema: public; Owner: simasif_user
--

CREATE TYPE public.jenis_file_fotografi AS ENUM (
    'referensi',
    'hasil'
);


ALTER TYPE public.jenis_file_fotografi OWNER TO simasif_user;

--
-- Name: jenis_harga_layanan; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.jenis_harga_layanan AS ENUM (
    'tunggal',
    'pilihan'
);


ALTER TYPE public.jenis_harga_layanan OWNER TO postgres;

--
-- Name: keterangan_harga_layanan; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.keterangan_harga_layanan AS ENUM (
    'layanan',
    'foto'
);


ALTER TYPE public.keterangan_harga_layanan OWNER TO postgres;

--
-- Name: metode_pembayaran; Type: TYPE; Schema: public; Owner: simasif_user
--

CREATE TYPE public.metode_pembayaran AS ENUM (
    'transfer',
    'langsung'
);


ALTER TYPE public.metode_pembayaran OWNER TO simasif_user;

--
-- Name: status_file_fotografi; Type: TYPE; Schema: public; Owner: simasif_user
--

CREATE TYPE public.status_file_fotografi AS ENUM (
    'belum_tersedia',
    'tersedia'
);


ALTER TYPE public.status_file_fotografi OWNER TO simasif_user;

--
-- Name: status_pembayaran; Type: TYPE; Schema: public; Owner: simasif_user
--

CREATE TYPE public.status_pembayaran AS ENUM (
    'menunggu_verifikasi',
    'lunas',
    'ditolak'
);


ALTER TYPE public.status_pembayaran OWNER TO simasif_user;

--
-- Name: status_pemesanan; Type: TYPE; Schema: public; Owner: simasif_user
--

CREATE TYPE public.status_pemesanan AS ENUM (
    'pemesanan_dibuat',
    'menunggu_pembayaran',
    'pembayaran_diverifikasi',
    'terjadwal',
    'sesi_foto',
    'proses_editing',
    'hasil_siap',
    'selesai'
);


ALTER TYPE public.status_pemesanan OWNER TO simasif_user;

--
-- Name: user_role; Type: TYPE; Schema: public; Owner: simasif_user
--

CREATE TYPE public.user_role AS ENUM (
    'owner',
    'pegawai'
);


ALTER TYPE public.user_role OWNER TO simasif_user;

--
-- Name: trigger_set_updated_at(); Type: FUNCTION; Schema: public; Owner: simasif_user
--

CREATE FUNCTION public.trigger_set_updated_at() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

BEGIN

    NEW.updated_at = NOW();

    RETURN NEW;

END;

$$;


ALTER FUNCTION public.trigger_set_updated_at() OWNER TO simasif_user;

--
-- Name: FUNCTION trigger_set_updated_at(); Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON FUNCTION public.trigger_set_updated_at() IS 'Mengubah nilai kolom updated_at menjadi waktu saat ini sebelum UPDATE dilakukan.';


--
-- Name: trigger_sync_biaya_tambahan(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.trigger_sync_biaya_tambahan() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_pemesanan_id INTEGER;
BEGIN
    IF TG_OP = 'DELETE' THEN
        v_pemesanan_id := OLD.pemesanan_id;
    ELSE
        v_pemesanan_id := NEW.pemesanan_id;
    END IF;

    UPDATE pemesanan
       SET biaya_tambahan = (
            SELECT COALESCE(SUM(subtotal), 0)
              FROM pemesanan_addon
             WHERE pemesanan_id = v_pemesanan_id
       )
     WHERE id = v_pemesanan_id;

    RETURN NULL;
END;
$$;


ALTER FUNCTION public.trigger_sync_biaya_tambahan() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: addon_layanan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.addon_layanan (
    id integer NOT NULL,
    layanan_id integer NOT NULL,
    nama character varying(150) NOT NULL,
    deskripsi text,
    harga numeric(12,2) NOT NULL,
    tambahan_durasi_menit integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT addon_layanan_durasi_check CHECK ((tambahan_durasi_menit >= 0)),
    CONSTRAINT addon_layanan_harga_check CHECK ((harga >= (0)::numeric)),
    CONSTRAINT addon_layanan_nama_check CHECK (((nama)::text <> ''::text))
);


ALTER TABLE public.addon_layanan OWNER TO postgres;

--
-- Name: TABLE addon_layanan; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.addon_layanan IS 'Opsi tambahan berbayar milik satu layanan (mis. tambah orang, cetak ekstra)';


--
-- Name: COLUMN addon_layanan.tambahan_durasi_menit; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.addon_layanan.tambahan_durasi_menit IS 'Tambahan durasi sesi jika add-on dipilih, 0 = tidak menambah durasi';


--
-- Name: COLUMN addon_layanan.is_active; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.addon_layanan.is_active IS 'Add-on nonaktif tidak bisa dipilih pelanggan baru';


--
-- Name: addon_layanan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.addon_layanan ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.addon_layanan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: audit_log; Type: TABLE; Schema: public; Owner: simasif_user
--

CREATE TABLE public.audit_log (
    id integer NOT NULL,
    user_id integer,
    modul character varying(50) NOT NULL,
    aktivitas character varying(150) NOT NULL,
    detail jsonb,
    ip_address character varying(45),
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT chk_audit_aktivitas_not_empty CHECK (((aktivitas)::text <> ''::text)),
    CONSTRAINT chk_audit_modul_not_empty CHECK (((modul)::text <> ''::text))
);


ALTER TABLE public.audit_log OWNER TO simasif_user;

--
-- Name: TABLE audit_log; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON TABLE public.audit_log IS 'Pencatatan aktivitas pengguna internal untuk keamanan dan transparansi';


--
-- Name: COLUMN audit_log.user_id; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.audit_log.user_id IS 'User yang melakukan aktivitas, NULL apabila akun sudah dihapus';


--
-- Name: COLUMN audit_log.modul; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.audit_log.modul IS 'Kategori modul terkait aktivitas, misalnya Pemesanan, Pembayaran, Workflow, Layanan, Pelanggan, atau Auth, dipakai untuk memudahkan filter riwayat di halaman Owner';


--
-- Name: COLUMN audit_log.aktivitas; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.audit_log.aktivitas IS 'Deskripsi singkat aktivitas yang dilakukan pengguna';


--
-- Name: COLUMN audit_log.detail; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.audit_log.detail IS 'Informasi tambahan dalam format JSON, misalnya data sebelum dan sesudah perubahan atau metadata aktivitas';


--
-- Name: COLUMN audit_log.created_at; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.audit_log.created_at IS 'Waktu aktivitas dicatat';


--
-- Name: audit_log_id_seq; Type: SEQUENCE; Schema: public; Owner: simasif_user
--

ALTER TABLE public.audit_log ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.audit_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: file_fotografi; Type: TABLE; Schema: public; Owner: simasif_user
--

CREATE TABLE public.file_fotografi (
    id integer NOT NULL,
    pemesanan_id integer NOT NULL,
    jenis_file public.jenis_file_fotografi NOT NULL,
    nama_file_asli character varying(255) NOT NULL,
    nama_file character varying(255) NOT NULL,
    path_file character varying(255) NOT NULL,
    ukuran_file bigint,
    mime_type character varying(100),
    status_file public.status_file_fotografi DEFAULT 'belum_tersedia'::public.status_file_fotografi NOT NULL,
    diunggah_oleh integer,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT chk_nama_file_asli_not_empty CHECK (((nama_file_asli)::text <> ''::text)),
    CONSTRAINT chk_nama_file_not_empty CHECK (((nama_file)::text <> ''::text)),
    CONSTRAINT chk_path_file_not_empty CHECK (((path_file)::text <> ''::text)),
    CONSTRAINT chk_pengunggah_file CHECK ((((jenis_file = 'referensi'::public.jenis_file_fotografi) AND (diunggah_oleh IS NULL)) OR ((jenis_file = 'hasil'::public.jenis_file_fotografi) AND (diunggah_oleh IS NOT NULL)))),
    CONSTRAINT file_fotografi_ukuran_file_check CHECK ((ukuran_file > 0))
);


ALTER TABLE public.file_fotografi OWNER TO simasif_user;

--
-- Name: TABLE file_fotografi; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON TABLE public.file_fotografi IS 'File referensi dari pelanggan dan hasil dari pegawai';


--
-- Name: COLUMN file_fotografi.nama_file_asli; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.file_fotografi.nama_file_asli IS 'Nama file asli sebagaimana diunggah pengguna, misalnya IMG_12345.jpg, dipakai saat file diunduh kembali supaya pengguna melihat nama yang familiar';


--
-- Name: COLUMN file_fotografi.nama_file; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.file_fotografi.nama_file IS 'Nama file sebagaimana disimpan di server, biasanya sudah diacak untuk mencegah tabrakan nama dan path traversal, misalnya A92KLS83.jpg';


--
-- Name: COLUMN file_fotografi.path_file; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.file_fotografi.path_file IS 'Path relatif di storage/upload-pelanggan/ atau storage/hasil-foto/, tidak bisa diakses langsung lewat URL';


--
-- Name: COLUMN file_fotografi.ukuran_file; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.file_fotografi.ukuran_file IS 'Ukuran file dalam satuan byte, dipakai untuk validasi dan ditampilkan ke pengguna';


--
-- Name: COLUMN file_fotografi.mime_type; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.file_fotografi.mime_type IS 'Tipe MIME file, misalnya image/jpeg, image/png, atau application/zip, dipakai saat proses download supaya header Content-Type benar';


--
-- Name: COLUMN file_fotografi.diunggah_oleh; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.file_fotografi.diunggah_oleh IS 'Kosong (NULL) kalau file diunggah pelanggan, berisi id pegawai kalau jenis_file = hasil';


--
-- Name: file_fotografi_id_seq; Type: SEQUENCE; Schema: public; Owner: simasif_user
--

ALTER TABLE public.file_fotografi ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.file_fotografi_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: layanan; Type: TABLE; Schema: public; Owner: simasif_user
--

CREATE TABLE public.layanan (
    id integer NOT NULL,
    nama character varying(150) NOT NULL,
    deskripsi text,
    harga numeric(12,2) NOT NULL,
    estimasi_durasi_menit integer,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    estimasi_hasil_hari integer,
    deliverables text,
    maks_orang integer,
    jenis_harga public.jenis_harga_layanan DEFAULT 'tunggal'::public.jenis_harga_layanan NOT NULL,
    keterangan_harga public.keterangan_harga_layanan DEFAULT 'layanan'::public.keterangan_harga_layanan NOT NULL,
    durasi_nilai integer,
    durasi_satuan character varying(10) DEFAULT 'Menit'::character varying NOT NULL,
    estimasi_hasil_nilai integer,
    estimasi_hasil_satuan character varying(10) DEFAULT 'Hari'::character varying NOT NULL,
    kebutuhan_jadwal boolean DEFAULT true NOT NULL,
    CONSTRAINT layanan_durasi_check CHECK ((estimasi_durasi_menit > 0)),
    CONSTRAINT layanan_durasi_nilai_check CHECK ((durasi_nilai > 0)),
    CONSTRAINT layanan_durasi_satuan_check CHECK (((durasi_satuan)::text = ANY ((ARRAY['Menit'::character varying, 'Jam'::character varying, 'Hari'::character varying])::text[]))),
    CONSTRAINT layanan_estimasi_hasil_check CHECK ((estimasi_hasil_hari > 0)),
    CONSTRAINT layanan_harga_check CHECK ((harga >= (0)::numeric)),
    CONSTRAINT layanan_hasil_nilai_check CHECK ((estimasi_hasil_nilai > 0)),
    CONSTRAINT layanan_hasil_satuan_check CHECK (((estimasi_hasil_satuan)::text = ANY ((ARRAY['Menit'::character varying, 'Jam'::character varying, 'Hari'::character varying, 'Minggu'::character varying])::text[]))),
    CONSTRAINT layanan_maks_orang_check CHECK ((maks_orang > 0)),
    CONSTRAINT layanan_maks_orang_max CHECK ((maks_orang <= 999)),
    CONSTRAINT layanan_nama_check CHECK (((nama)::text <> ''::text))
);


ALTER TABLE public.layanan OWNER TO simasif_user;

--
-- Name: TABLE layanan; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON TABLE public.layanan IS 'Paket/layanan fotografi yang ditawarkan Studio Flamboyan';


--
-- Name: COLUMN layanan.nama; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.nama IS 'Nama paket fotografi yang ditawarkan kepada pelanggan';


--
-- Name: COLUMN layanan.deskripsi; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.deskripsi IS 'Informasi detail paket fotografi';


--
-- Name: COLUMN layanan.harga; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.harga IS 'Harga paket fotografi dalam Rupiah';


--
-- Name: COLUMN layanan.estimasi_durasi_menit; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.estimasi_durasi_menit IS 'Perkiraan lama sesi fotografi dalam satuan menit';


--
-- Name: COLUMN layanan.is_active; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.is_active IS 'Layanan nonaktif tidak ditampilkan di halaman publik';


--
-- Name: COLUMN layanan.estimasi_hasil_hari; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.estimasi_hasil_hari IS 'Estimasi hasil foto siap diterima pelanggan, dalam satuan hari (dihitung setelah sesi foto)';


--
-- Name: COLUMN layanan.deliverables; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.deliverables IS 'Poin hasil/output layanan, satu baris = satu poin. Dipisah dari deskripsi';


--
-- Name: COLUMN layanan.maks_orang; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.maks_orang IS 'Batas maksimal jumlah orang dalam satu sesi, boleh kosong (NULL = tidak dibatasi / tidak ditentukan)';


--
-- Name: COLUMN layanan.jenis_harga; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.jenis_harga IS 'tunggal = satu harga di layanan.harga; pilihan = harga ada di layanan_harga_pilihan (layanan.harga diisi harga terendah sebagai "mulai dari")';


--
-- Name: COLUMN layanan.keterangan_harga; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.keterangan_harga IS 'Satuan harga yang ditampilkan: /Layanan atau /Foto';


--
-- Name: COLUMN layanan.durasi_nilai; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.durasi_nilai IS 'Nilai durasi sesuai input form; estimasi_durasi_menit tetap diisi (hasil konversi) untuk jadwal';


--
-- Name: COLUMN layanan.estimasi_hasil_nilai; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.estimasi_hasil_nilai IS 'Nilai estimasi hasil sesuai input form; estimasi_hasil_hari tetap diisi (dibulatkan ke atas, min 1)';


--
-- Name: COLUMN layanan.kebutuhan_jadwal; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.layanan.kebutuhan_jadwal IS 'TRUE = layanan membutuhkan jadwal sesi';


--
-- Name: layanan_gambar; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.layanan_gambar (
    id integer NOT NULL,
    layanan_id integer NOT NULL,
    nama_file_asli character varying(255) NOT NULL,
    nama_file character varying(255) NOT NULL,
    path_file character varying(255) NOT NULL,
    ukuran_file bigint,
    mime_type character varying(100),
    keterangan character varying(150),
    urutan integer DEFAULT 0 NOT NULL,
    is_cover boolean DEFAULT false NOT NULL,
    diunggah_oleh integer,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT chk_layanan_gambar_nama_asli_not_empty CHECK (((nama_file_asli)::text <> ''::text)),
    CONSTRAINT chk_layanan_gambar_nama_not_empty CHECK (((nama_file)::text <> ''::text)),
    CONSTRAINT chk_layanan_gambar_path_not_empty CHECK (((path_file)::text <> ''::text)),
    CONSTRAINT layanan_gambar_ukuran_file_check CHECK ((ukuran_file > 0))
);


ALTER TABLE public.layanan_gambar OWNER TO postgres;

--
-- Name: TABLE layanan_gambar; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.layanan_gambar IS 'Galeri/contoh foto untuk satu layanan, ditampilkan di halaman publik';


--
-- Name: COLUMN layanan_gambar.path_file; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.layanan_gambar.path_file IS 'Path relatif gambar. Berbeda dengan file pelanggan, gambar layanan boleh publik (mis. public/assets/uploads/layanan/)';


--
-- Name: COLUMN layanan_gambar.urutan; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.layanan_gambar.urutan IS 'Urutan tampil, angka kecil tampil lebih dulu';


--
-- Name: COLUMN layanan_gambar.is_cover; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.layanan_gambar.is_cover IS 'Gambar utama yang tampil di kartu layanan';


--
-- Name: layanan_gambar_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.layanan_gambar ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.layanan_gambar_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: layanan_harga_grup; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.layanan_harga_grup (
    id integer NOT NULL,
    layanan_id integer NOT NULL,
    nama character varying(100) NOT NULL,
    urutan integer DEFAULT 0 NOT NULL,
    CONSTRAINT layanan_harga_grup_nama_check CHECK (((nama)::text <> ''::text))
);


ALTER TABLE public.layanan_harga_grup OWNER TO postgres;

--
-- Name: TABLE layanan_harga_grup; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.layanan_harga_grup IS 'Kelompok harga untuk layanan berjenis harga pilihan (mis. Hard File, Soft File)';


--
-- Name: layanan_harga_grup_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.layanan_harga_grup ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.layanan_harga_grup_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: layanan_harga_pilihan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.layanan_harga_pilihan (
    id integer NOT NULL,
    grup_id integer NOT NULL,
    nama character varying(100) NOT NULL,
    harga numeric(12,2) NOT NULL,
    urutan integer DEFAULT 0 NOT NULL,
    CONSTRAINT layanan_harga_pilihan_harga_check CHECK ((harga >= (1)::numeric)),
    CONSTRAINT layanan_harga_pilihan_nama_check CHECK (((nama)::text <> ''::text))
);


ALTER TABLE public.layanan_harga_pilihan OWNER TO postgres;

--
-- Name: TABLE layanan_harga_pilihan; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.layanan_harga_pilihan IS 'Pilihan + harga dalam satu kelompok harga';


--
-- Name: layanan_harga_pilihan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.layanan_harga_pilihan ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.layanan_harga_pilihan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: layanan_id_seq; Type: SEQUENCE; Schema: public; Owner: simasif_user
--

ALTER TABLE public.layanan ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.layanan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: layanan_output; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.layanan_output (
    id integer NOT NULL,
    layanan_id integer NOT NULL,
    nama character varying(150) NOT NULL,
    tipe character varying(10) DEFAULT 'non'::character varying NOT NULL,
    urutan integer DEFAULT 0 NOT NULL,
    CONSTRAINT layanan_output_nama_check CHECK (((nama)::text <> ''::text)),
    CONSTRAINT layanan_output_tipe_check CHECK (((tipe)::text = ANY ((ARRAY['non'::character varying, 'pilihan'::character varying])::text[])))
);


ALTER TABLE public.layanan_output OWNER TO postgres;

--
-- Name: TABLE layanan_output; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.layanan_output IS 'Output/deliverables layanan; tipe pilihan punya sub pilihan';


--
-- Name: layanan_output_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.layanan_output ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.layanan_output_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: layanan_output_pilihan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.layanan_output_pilihan (
    id integer NOT NULL,
    output_id integer NOT NULL,
    nama character varying(150) NOT NULL,
    urutan integer DEFAULT 0 NOT NULL,
    CONSTRAINT layanan_output_pilihan_nama_check CHECK (((nama)::text <> ''::text))
);


ALTER TABLE public.layanan_output_pilihan OWNER TO postgres;

--
-- Name: TABLE layanan_output_pilihan; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.layanan_output_pilihan IS 'Sub pilihan sebuah output (mis. warna background)';


--
-- Name: layanan_output_pilihan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.layanan_output_pilihan ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.layanan_output_pilihan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: pelanggan; Type: TABLE; Schema: public; Owner: simasif_user
--

CREATE TABLE public.pelanggan (
    id integer NOT NULL,
    nama character varying(100) NOT NULL,
    no_hp character varying(20) NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.pelanggan OWNER TO simasif_user;

--
-- Name: TABLE pelanggan; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON TABLE public.pelanggan IS 'Data pelanggan yang melakukan pemesanan, diidentifikasi lewat nomor HP unik';


--
-- Name: pelanggan_id_seq; Type: SEQUENCE; Schema: public; Owner: simasif_user
--

ALTER TABLE public.pelanggan ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.pelanggan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: pemesanan; Type: TABLE; Schema: public; Owner: simasif_user
--

CREATE TABLE public.pemesanan (
    id integer NOT NULL,
    kode_unik character varying(50) NOT NULL,
    pelanggan_id integer NOT NULL,
    layanan_id integer NOT NULL,
    pegawai_id integer,
    tanggal_jadwal date NOT NULL,
    jam_jadwal time without time zone NOT NULL,
    catatan_khusus text,
    status public.status_pemesanan DEFAULT 'pemesanan_dibuat'::public.status_pemesanan NOT NULL,
    harga_saat_pesan numeric(12,2) NOT NULL,
    biaya_tambahan numeric(12,2) DEFAULT 0 NOT NULL,
    diskon numeric(12,2) DEFAULT 0 NOT NULL,
    total_tagihan numeric(12,2) GENERATED ALWAYS AS (((harga_saat_pesan + biaya_tambahan) - diskon)) STORED,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT chk_kode_unik_not_empty CHECK (((kode_unik)::text <> ''::text)),
    CONSTRAINT chk_total_tagihan_non_negative CHECK ((total_tagihan >= (0)::numeric)),
    CONSTRAINT pemesanan_biaya_tambahan_check CHECK ((biaya_tambahan >= (0)::numeric)),
    CONSTRAINT pemesanan_diskon_check CHECK ((diskon >= (0)::numeric)),
    CONSTRAINT pemesanan_harga_saat_pesan_check CHECK ((harga_saat_pesan >= (0)::numeric))
);


ALTER TABLE public.pemesanan OWNER TO simasif_user;

--
-- Name: TABLE pemesanan; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON TABLE public.pemesanan IS 'Entitas utama yang menghubungkan pelanggan, layanan, dan jadwal';


--
-- Name: COLUMN pemesanan.kode_unik; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.pemesanan.kode_unik IS 'Dipakai pelanggan untuk akses tanpa login (tracking, upload, download)';


--
-- Name: COLUMN pemesanan.pegawai_id; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.pemesanan.pegawai_id IS 'Pegawai yang menangani pemesanan ini, boleh kosong di awal';


--
-- Name: COLUMN pemesanan.harga_saat_pesan; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.pemesanan.harga_saat_pesan IS 'Salinan harga dari tabel layanan pada saat booking dibuat, supaya perubahan harga layanan di kemudian hari tidak mengubah nilai pemesanan lama. Nilainya diisi oleh kode PHP saat proses booking (Tahap 7), bukan otomatis oleh database';


--
-- Name: COLUMN pemesanan.biaya_tambahan; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.pemesanan.biaya_tambahan IS 'Biaya tambahan di luar harga dasar layanan, misalnya cetak foto ekstra, defaultnya 0';


--
-- Name: COLUMN pemesanan.diskon; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.pemesanan.diskon IS 'Potongan harga jika ada, defaultnya 0';


--
-- Name: COLUMN pemesanan.total_tagihan; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.pemesanan.total_tagihan IS 'Dihitung otomatis oleh database dari harga_saat_pesan + biaya_tambahan - diskon, tidak bisa diisi manual lewat INSERT atau UPDATE';


--
-- Name: CONSTRAINT chk_total_tagihan_non_negative ON pemesanan; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON CONSTRAINT chk_total_tagihan_non_negative ON public.pemesanan IS 'Berjaga-jaga terhadap kasus diskon lebih besar dari harga plus biaya tambahan, yang akan membuat total_tagihan menjadi negatif';


--
-- Name: pemesanan_addon; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pemesanan_addon (
    id integer NOT NULL,
    pemesanan_id integer NOT NULL,
    addon_id integer NOT NULL,
    nama_saat_pesan character varying(150) NOT NULL,
    harga_saat_pesan numeric(12,2) NOT NULL,
    jumlah integer DEFAULT 1 NOT NULL,
    subtotal numeric(12,2) GENERATED ALWAYS AS ((harga_saat_pesan * (jumlah)::numeric)) STORED,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT pemesanan_addon_harga_saat_pesan_check CHECK ((harga_saat_pesan >= (0)::numeric)),
    CONSTRAINT pemesanan_addon_jumlah_check CHECK ((jumlah > 0))
);


ALTER TABLE public.pemesanan_addon OWNER TO postgres;

--
-- Name: TABLE pemesanan_addon; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.pemesanan_addon IS 'Add-on yang dipilih pada suatu pemesanan (snapshot nama dan harga saat dipesan)';


--
-- Name: COLUMN pemesanan_addon.harga_saat_pesan; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.pemesanan_addon.harga_saat_pesan IS 'Salinan harga add-on saat booking, diisi kode PHP, supaya perubahan harga tidak mengubah pemesanan lama';


--
-- Name: COLUMN pemesanan_addon.subtotal; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.pemesanan_addon.subtotal IS 'Dihitung otomatis: harga_saat_pesan x jumlah';


--
-- Name: pemesanan_addon_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.pemesanan_addon ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.pemesanan_addon_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: pemesanan_id_seq; Type: SEQUENCE; Schema: public; Owner: simasif_user
--

ALTER TABLE public.pemesanan ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.pemesanan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: transaksi_pembayaran; Type: TABLE; Schema: public; Owner: simasif_user
--

CREATE TABLE public.transaksi_pembayaran (
    id integer NOT NULL,
    pemesanan_id integer NOT NULL,
    metode_pembayaran public.metode_pembayaran NOT NULL,
    jumlah_bayar numeric(12,2) NOT NULL,
    nomor_referensi character varying(100),
    bukti_transfer character varying(255),
    status_pembayaran public.status_pembayaran DEFAULT 'menunggu_verifikasi'::public.status_pembayaran NOT NULL,
    diverifikasi_oleh integer,
    diverifikasi_at timestamp without time zone,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT chk_bukti_transfer_wajib_untuk_transfer CHECK ((((metode_pembayaran = 'transfer'::public.metode_pembayaran) AND (bukti_transfer IS NOT NULL)) OR (metode_pembayaran = 'langsung'::public.metode_pembayaran))),
    CONSTRAINT chk_verifikasi_pembayaran_transfer CHECK (((status_pembayaran = 'menunggu_verifikasi'::public.status_pembayaran) OR ((status_pembayaran = ANY (ARRAY['lunas'::public.status_pembayaran, 'ditolak'::public.status_pembayaran])) AND (diverifikasi_oleh IS NOT NULL)))),
    CONSTRAINT transaksi_pembayaran_jumlah_bayar_check CHECK ((jumlah_bayar > (0)::numeric))
);


ALTER TABLE public.transaksi_pembayaran OWNER TO simasif_user;

--
-- Name: TABLE transaksi_pembayaran; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON TABLE public.transaksi_pembayaran IS 'Data pembayaran, satu pemesanan hanya punya satu transaksi';


--
-- Name: COLUMN transaksi_pembayaran.nomor_referensi; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.transaksi_pembayaran.nomor_referensi IS 'Nomor referensi transaksi dari bank atau catatan internal, opsional, dipakai Owner untuk mencocokkan mutasi rekening secara manual';


--
-- Name: COLUMN transaksi_pembayaran.bukti_transfer; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.transaksi_pembayaran.bukti_transfer IS 'Nama file bukti transfer, file fisik disimpan di storage/bukti-transfer/';


--
-- Name: transaksi_pembayaran_id_seq; Type: SEQUENCE; Schema: public; Owner: simasif_user
--

ALTER TABLE public.transaksi_pembayaran ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.transaksi_pembayaran_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: simasif_user
--

CREATE TABLE public.users (
    id integer NOT NULL,
    nama character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    password character varying(255) NOT NULL,
    no_telepon character varying(20),
    role public.user_role DEFAULT 'pegawai'::public.user_role NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    last_login_at timestamp without time zone,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    reset_token character varying(255),
    reset_token_expires_at timestamp without time zone,
    CONSTRAINT users_email_check CHECK (((email)::text <> ''::text))
);


ALTER TABLE public.users OWNER TO simasif_user;

--
-- Name: TABLE users; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON TABLE public.users IS 'Akun internal Owner dan Pegawai Studio Flamboyan';


--
-- Name: COLUMN users.password; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.users.password IS 'Disimpan dalam bentuk hash bcrypt, tidak pernah plaintext';


--
-- Name: COLUMN users.no_telepon; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.users.no_telepon IS 'Informasi kontak';


--
-- Name: COLUMN users.last_login_at; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.users.last_login_at IS 'Wkatu login terakhir, diperbarui oleh AuthService setiap kali login berhasil, dipakai Owner untuk memantau aktivitas pegawai';


--
-- Name: COLUMN users.reset_token; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.users.reset_token IS 'Token sementara untuk fitur lupa password, dipakai di tahap 13';


--
-- Name: COLUMN users.reset_token_expires_at; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.users.reset_token_expires_at IS 'Batas waktu berlaku reset_token, dipakai di Tahap 13';


--
-- Name: CONSTRAINT users_email_check ON users; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON CONSTRAINT users_email_check ON public.users IS 'Mencegah string kosong lolos sebagai email, UNIQUE saja tidak menjamin ini karena string kosong dianggap satu nilai valid yang berbeda dari NULL';


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: simasif_user
--

ALTER TABLE public.users ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: v_detail_pembayaran; Type: VIEW; Schema: public; Owner: simasif_user
--

CREATE VIEW public.v_detail_pembayaran AS
 SELECT t.id AS transaksi_id,
    t.pemesanan_id,
    p.kode_unik,
    p.status AS status_pemesanan,
    pl.nama AS pelanggan_nama,
    pl.no_hp AS pelanggan_no_hp,
    l.nama AS layanan_nama,
    p.total_tagihan,
    t.metode_pembayaran,
    t.jumlah_bayar,
    (t.jumlah_bayar - p.total_tagihan) AS selisih_bayar,
    t.nomor_referensi,
    t.bukti_transfer,
    t.status_pembayaran,
    t.diverifikasi_oleh,
    v.nama AS diverifikator_nama,
    t.diverifikasi_at,
    t.created_at,
    t.updated_at
   FROM ((((public.transaksi_pembayaran t
     JOIN public.pemesanan p ON ((p.id = t.pemesanan_id)))
     JOIN public.pelanggan pl ON ((pl.id = p.pelanggan_id)))
     JOIN public.layanan l ON ((l.id = p.layanan_id)))
     LEFT JOIN public.users v ON ((v.id = t.diverifikasi_oleh)));


ALTER VIEW public.v_detail_pembayaran OWNER TO simasif_user;

--
-- Name: VIEW v_detail_pembayaran; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON VIEW public.v_detail_pembayaran IS 'Detail transaksi pembayaran beserta pemesanan, pelanggan, dan verifikator. Dipakai halaman verifikasi transaksi pegawai dan laporan pendapatan.';


--
-- Name: v_detail_pemesanan; Type: VIEW; Schema: public; Owner: simasif_user
--

CREATE VIEW public.v_detail_pemesanan AS
 SELECT p.id AS pemesanan_id,
    p.kode_unik,
    p.status,
    p.tanggal_jadwal,
    p.jam_jadwal,
    (p.jam_jadwal + ((((COALESCE(l.estimasi_durasi_menit, 60) + COALESCE(ad.tambahan_durasi, (0)::bigint)))::integer)::double precision * '00:01:00'::interval)) AS jam_selesai,
    p.catatan_khusus,
    p.harga_saat_pesan,
    p.biaya_tambahan,
    p.diskon,
    p.total_tagihan,
    pl.id AS pelanggan_id,
    pl.nama AS pelanggan_nama,
    pl.no_hp AS pelanggan_no_hp,
    l.id AS layanan_id,
    l.nama AS layanan_nama,
    l.estimasi_durasi_menit,
    u.id AS pegawai_id,
    u.nama AS pegawai_nama,
    t.id AS transaksi_id,
    t.metode_pembayaran,
    t.jumlah_bayar,
    t.status_pembayaran,
    t.diverifikasi_at,
    p.created_at,
    p.updated_at,
    ((COALESCE(l.estimasi_durasi_menit, 60) + COALESCE(ad.tambahan_durasi, (0)::bigint)))::integer AS durasi_total_menit,
    (COALESCE(ad.jumlah_addon, (0)::bigint))::integer AS jumlah_addon
   FROM (((((public.pemesanan p
     JOIN public.pelanggan pl ON ((pl.id = p.pelanggan_id)))
     JOIN public.layanan l ON ((l.id = p.layanan_id)))
     LEFT JOIN public.users u ON ((u.id = p.pegawai_id)))
     LEFT JOIN public.transaksi_pembayaran t ON ((t.pemesanan_id = p.id)))
     LEFT JOIN ( SELECT pa.pemesanan_id,
            sum((a.tambahan_durasi_menit * pa.jumlah)) AS tambahan_durasi,
            count(*) AS jumlah_addon
           FROM (public.pemesanan_addon pa
             JOIN public.addon_layanan a ON ((a.id = pa.addon_id)))
          GROUP BY pa.pemesanan_id) ad ON ((ad.pemesanan_id = p.id)));


ALTER VIEW public.v_detail_pemesanan OWNER TO simasif_user;

--
-- Name: VIEW v_detail_pemesanan; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON VIEW public.v_detail_pemesanan IS 'Detail lengkap pemesanan (pelanggan, layanan, pegawai, ringkasan pembayaran). Dipakai halaman daftar/detail pemesanan pegawai dan tracking.';


--
-- Name: workflow_layanan; Type: TABLE; Schema: public; Owner: simasif_user
--

CREATE TABLE public.workflow_layanan (
    id integer NOT NULL,
    pemesanan_id integer NOT NULL,
    status_sebelumnya public.status_pemesanan,
    status public.status_pemesanan NOT NULL,
    catatan text,
    diubah_oleh integer,
    created_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.workflow_layanan OWNER TO simasif_user;

--
-- Name: TABLE workflow_layanan; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON TABLE public.workflow_layanan IS 'Riwayat setiap perubahan status pemesanan, bersifat log historis';


--
-- Name: COLUMN workflow_layanan.status_sebelumnya; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.workflow_layanan.status_sebelumnya IS 'Status sebelum perubahan ini terjadi, kosong (NULL) untuk entri pertama karena belum ada status sebelumnya';


--
-- Name: COLUMN workflow_layanan.status; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.workflow_layanan.status IS 'Status baru setelah perubahan dilakukan';


--
-- Name: COLUMN workflow_layanan.catatan; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.workflow_layanan.catatan IS 'Catatan tambahan terkait perubahan status, bersifat opsional';


--
-- Name: COLUMN workflow_layanan.diubah_oleh; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.workflow_layanan.diubah_oleh IS 'User (Owner atau Pegawai) yang melakukan perubahan status';


--
-- Name: COLUMN workflow_layanan.created_at; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON COLUMN public.workflow_layanan.created_at IS 'Waktu perubahan status dicatat ke dalam sistem';


--
-- Name: v_riwayat_workflow; Type: VIEW; Schema: public; Owner: simasif_user
--

CREATE VIEW public.v_riwayat_workflow AS
 SELECT w.id AS workflow_id,
    w.pemesanan_id,
    p.kode_unik,
    pl.nama AS pelanggan_nama,
    row_number() OVER (PARTITION BY w.pemesanan_id ORDER BY w.created_at, w.id) AS urutan_ke,
    w.status_sebelumnya,
    w.status,
    w.catatan,
    w.diubah_oleh,
    u.nama AS diubah_oleh_nama,
    u.role AS diubah_oleh_role,
    w.created_at
   FROM (((public.workflow_layanan w
     JOIN public.pemesanan p ON ((p.id = w.pemesanan_id)))
     JOIN public.pelanggan pl ON ((pl.id = p.pelanggan_id)))
     LEFT JOIN public.users u ON ((u.id = w.diubah_oleh)));


ALTER VIEW public.v_riwayat_workflow OWNER TO simasif_user;

--
-- Name: VIEW v_riwayat_workflow; Type: COMMENT; Schema: public; Owner: simasif_user
--

COMMENT ON VIEW public.v_riwayat_workflow IS 'Riwayat perubahan status pemesanan. diubah_oleh NULL = dibuat pelanggan/sistem. Dipakai detail pemesanan, tracking pelanggan, dan audit.';


--
-- Name: workflow_layanan_id_seq; Type: SEQUENCE; Schema: public; Owner: simasif_user
--

ALTER TABLE public.workflow_layanan ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.workflow_layanan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Data for Name: addon_layanan; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.addon_layanan (id, layanan_id, nama, deskripsi, harga, tambahan_durasi_menit, is_active, created_at, updated_at) FROM stdin;
1	10	Tambah File Edit	Tambahan file hasil editing foto	50000.00	15	t	2026-10-14 10:06:53.651732	2026-10-14 20:41:15.541628
8	10	Cetak Hasil Foto	Cetak ukuran tertentu	10000.00	5	t	2026-10-15 18:41:29.220469	2026-10-18 21:47:24.663732
2	10	Edit Prioritas	Pengerjaan editing lebih cepat	75000.00	0	t	2026-10-14 10:06:53.651732	2026-10-18 21:47:25.380674
\.


--
-- Data for Name: audit_log; Type: TABLE DATA; Schema: public; Owner: simasif_user
--

COPY public.audit_log (id, user_id, modul, aktivitas, detail, ip_address, created_at) FROM stdin;
1	1	Auth	Login berhasil	{"role": "owner", "email": "owner@simasif.local"}	127.0.0.1	2026-11-14 17:43:43.078665
2	1	Auth	Logout	\N	127.0.0.1	2026-11-14 18:04:51.201483
3	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-14 18:04:57.690255
4	2	Pembayaran	Tolak pembayaran	{"metode": "langsung", "kode_unik": "SF-Y8XVVK", "jumlah_bayar": "10000.00", "transaksi_id": 4}	127.0.0.1	2026-11-14 18:05:06.192037
5	2	Auth	Logout	\N	127.0.0.1	2026-11-14 18:05:07.120029
6	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-14 18:07:13.949021
7	2	Auth	Logout	\N	127.0.0.1	2026-11-14 18:07:48.845288
8	1	Auth	Login berhasil	{"role": "owner", "email": "owner@simasif.local"}	127.0.0.1	2026-11-14 18:07:52.048371
9	1	Auth	Logout	\N	127.0.0.1	2026-11-14 18:09:21.217656
10	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-14 18:10:50.336561
11	2	Auth	Logout	\N	127.0.0.1	2026-11-14 18:13:43.315794
12	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-14 18:15:28.716611
13	2	Auth	Logout	\N	127.0.0.1	2026-11-14 18:15:35.734895
14	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-15 13:18:56.088669
15	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-15 14:34:57.020531
16	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-15 16:57:41.874864
17	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-16 16:14:37.132438
18	2	Auth	Logout	\N	127.0.0.1	2026-11-16 16:16:05.594246
19	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-16 16:17:12.861256
20	2	Auth	Logout	\N	127.0.0.1	2026-11-16 16:17:36.969864
21	1	Auth	Login berhasil	{"role": "owner", "email": "owner@simasif.local"}	127.0.0.1	2026-11-16 16:19:37.534369
22	1	User	Ubah akun pegawai	{"id": 2, "perubahan": {"nama": {"ke": "Ayu Wardatul Widad", "dari": "Pegawai Demo"}}}	127.0.0.1	2026-11-16 16:20:20.531127
23	1	Auth	Logout	\N	127.0.0.1	2026-11-16 16:20:22.930793
24	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-16 16:20:26.123301
25	2	Auth	Logout	\N	127.0.0.1	2026-11-16 16:21:39.959531
26	1	Auth	Login berhasil	{"role": "owner", "email": "owner@simasif.local"}	127.0.0.1	2026-11-16 16:21:53.533227
27	1	Auth	Logout	\N	127.0.0.1	2026-11-16 16:27:48.110736
28	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-16 16:28:28.253575
29	2	Auth	Logout	\N	127.0.0.1	2026-11-16 16:28:43.753152
30	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-16 16:34:51.991549
31	2	Auth	Logout	\N	127.0.0.1	2026-11-16 16:35:31.280908
32	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-16 16:35:44.145023
33	2	Auth	Logout	\N	127.0.0.1	2026-11-16 16:37:28.699495
34	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-16 16:43:37.356392
35	2	Auth	Logout	\N	127.0.0.1	2026-11-16 16:44:29.708926
36	1	Auth	Login berhasil	{"role": "owner", "email": "owner@simasif.local"}	127.0.0.1	2026-11-16 16:44:33.704307
37	1	Auth	Logout	\N	127.0.0.1	2026-11-16 16:46:49.055646
38	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-16 16:46:51.605093
39	2	Auth	Logout	\N	127.0.0.1	2026-11-16 16:47:30.107042
40	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-16 16:47:48.397843
41	2	Auth	Logout	\N	127.0.0.1	2026-11-16 16:57:45.63378
42	1	Auth	Login berhasil	{"role": "owner", "email": "owner@simasif.local"}	127.0.0.1	2026-11-16 16:57:48.9117
43	1	Auth	Logout	\N	127.0.0.1	2026-11-16 16:58:14.490778
44	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-16 16:58:36.100165
45	2	Pembayaran	Verifikasi pembayaran (lunas)	{"metode": "langsung", "kode_unik": "SF-9KNJQ6", "jumlah_bayar": "25000.00", "transaksi_id": 5}	127.0.0.1	2026-11-16 17:00:15.250889
46	2	Auth	Logout	\N	127.0.0.1	2026-11-16 17:04:05.524471
47	2	Auth	Login berhasil	{"role": "pegawai", "email": "pegawai@simasif.local"}	127.0.0.1	2026-11-16 17:06:42.189954
48	2	Auth	Logout	\N	127.0.0.1	2026-11-16 17:14:01.288195
\.


--
-- Data for Name: file_fotografi; Type: TABLE DATA; Schema: public; Owner: simasif_user
--

COPY public.file_fotografi (id, pemesanan_id, jenis_file, nama_file_asli, nama_file, path_file, ukuran_file, mime_type, status_file, diunggah_oleh, created_at) FROM stdin;
1	12	referensi	WhatsApp Image 2026-11-13 at 08.36.20.jpeg	e72ef99aca6f2390fe135fa80e041173.jpg	upload-pelanggan/e72ef99aca6f2390fe135fa80e041173.jpg	173164	image/jpeg	belum_tersedia	\N	2026-11-13 16:58:04.383542
2	11	hasil	Foto Studio w_bestie.jpeg	b45857c659b09515bf59c80beeb31cf5.jpg	hasil-foto/b45857c659b09515bf59c80beeb31cf5.jpg	59008	image/jpeg	tersedia	2	2026-11-13 19:29:50.287242
\.


--
-- Data for Name: layanan; Type: TABLE DATA; Schema: public; Owner: simasif_user
--

COPY public.layanan (id, nama, deskripsi, harga, estimasi_durasi_menit, is_active, created_at, updated_at, estimasi_hasil_hari, deliverables, maks_orang, jenis_harga, keterangan_harga, durasi_nilai, durasi_satuan, estimasi_hasil_nilai, estimasi_hasil_satuan, kebutuhan_jadwal) FROM stdin;
10	Foto Editing	Proses memperbaiki, menyempurnakan, atau mengubah gambar digital yang telah diambil agar tampil lebih estetis, jelas, dan sesuai dengan tujuan penggunaannya tanpa harus menghilangkan karakter asli dari foto tersebut.	15000.00	30	t	2026-10-07 17:42:37.453916	2026-11-16 17:28:30.197127	1	1 hasil Editing\r\n2 abc\r\n3 abc	2	tunggal	layanan	30	Menit	1	Hari	t
19	Cetak Kilat	Proses memperbaiki, menyempurnakan, atau mengubah gambar digital yang telah diambil agar tampil lebih estetis, jelas, dan sesuai dengan tujuan penggunaannya tanpa harus menghilangkan karakter asli dari foto tersebut.	10000.00	15	f	2026-10-28 08:46:19.779575	2026-11-16 17:28:30.197127	1	4x6 1 paket\r\n3x4 1 paket\r\n2x3 1 paket \r\n3x3 1 paket	\N	tunggal	layanan	15	Menit	1	Hari	t
16	PAS Foto	Proses memperbaiki, menyempurnakan, atau mengubah gambar digital yang telah diambil agar tampil lebih estetis, jelas, dan sesuai dengan tujuan penggunaannya tanpa harus menghilangkan karakter asli dari foto tersebut.	30000.00	20	t	2026-10-28 08:40:31.378636	2026-11-16 17:28:30.197127	1	1 Paket cetak \r\n1 Soft file	\N	tunggal	layanan	20	Menit	1	Hari	t
18	Home Service	Proses memperbaiki, menyempurnakan, atau mengubah gambar digital yang telah diambil agar tampil lebih estetis, jelas, dan sesuai dengan tujuan penggunaannya tanpa harus menghilangkan karakter asli dari foto tersebut.	120000.00	45	t	2026-10-28 08:44:58.817007	2026-11-16 17:28:30.197127	3	1 Cetak 10R\r\n20 Soft file	7	tunggal	layanan	45	Menit	3	Hari	t
\.


--
-- Data for Name: layanan_gambar; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.layanan_gambar (id, layanan_id, nama_file_asli, nama_file, path_file, ukuran_file, mime_type, keterangan, urutan, is_cover, diunggah_oleh, created_at) FROM stdin;
9	10	light through the curtain.jpeg	6f3995ddcbab7f1bcbb2a062fafb9c4e.jpg	assets/uploads/layanan/6f3995ddcbab7f1bcbb2a062fafb9c4e.jpg	122417	image/jpeg	\N	0	f	2	2026-10-28 08:27:46.948483
10	10	jpeg(5)	0953a58abad49d412ee92efec92334df.jpg	assets/uploads/layanan/0953a58abad49d412ee92efec92334df.jpg	70076	image/jpeg	\N	1	f	2	2026-10-28 08:35:30.857914
11	10	jpeg(6)	ac6e2eaf8437c3f1bae81b3408d386d3.jpg	assets/uploads/layanan/ac6e2eaf8437c3f1bae81b3408d386d3.jpg	56465	image/jpeg	\N	2	t	2	2026-10-28 08:35:48.81125
12	19	nothing but thieves moral panic artwork.jpeg	cbe486e92b43699f510233069167716d.jpg	assets/uploads/layanan/cbe486e92b43699f510233069167716d.jpg	58355	image/jpeg	\N	0	t	2	2026-11-07 22:12:07.975721
13	19	Serenara Collection.jpeg	9b6a383e01afbef83cea0f172ab2d781.jpg	assets/uploads/layanan/9b6a383e01afbef83cea0f172ab2d781.jpg	87122	image/jpeg	\N	1	f	2	2026-11-07 22:12:18.753768
\.


--
-- Data for Name: layanan_harga_grup; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.layanan_harga_grup (id, layanan_id, nama, urutan) FROM stdin;
\.


--
-- Data for Name: layanan_harga_pilihan; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.layanan_harga_pilihan (id, grup_id, nama, harga, urutan) FROM stdin;
\.


--
-- Data for Name: layanan_output; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.layanan_output (id, layanan_id, nama, tipe, urutan) FROM stdin;
1	10	1 hasil Editing	non	0
2	10	2 abc	non	1
3	10	3 abc	non	2
4	19	4x6 1 paket	non	0
5	19	3x4 1 paket	non	1
6	19	2x3 1 paket	non	2
7	19	3x3 1 paket	non	3
8	16	1 Paket cetak	non	0
9	16	1 Soft file	non	1
10	18	1 Cetak 10R	non	0
11	18	20 Soft file	non	1
\.


--
-- Data for Name: layanan_output_pilihan; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.layanan_output_pilihan (id, output_id, nama, urutan) FROM stdin;
\.


--
-- Data for Name: pelanggan; Type: TABLE DATA; Schema: public; Owner: simasif_user
--

COPY public.pelanggan (id, nama, no_hp, created_at, updated_at) FROM stdin;
4	Movic	081234567890	2026-10-14 12:33:09.749037	2026-10-14 12:41:48.438644
5	Sri	089808202	2026-10-28 08:49:18.325286	2026-10-28 08:49:18.325286
6	Cak sunari	087773145334	2026-10-28 09:04:37.442476	2026-10-28 10:03:19.373598
7	ahmad subhean	081111145	2026-10-28 09:14:08.844092	2026-10-28 10:03:31.093613
8	rivaldo bauwo	089877877777	2026-10-28 09:16:11.038523	2026-10-28 10:03:47.395116
9	coba	0832176548	2026-11-01 10:52:19.131557	2026-11-01 10:52:19.131557
10	abc	081233434	2026-11-01 10:52:56.183889	2026-11-01 10:52:56.183889
11	hfjgkasgd	09347237483	2026-11-01 10:54:14.22622	2026-11-01 10:54:14.22622
12	gccjhkhjhlh	08786774488	2026-11-01 10:55:21.527387	2026-11-01 10:55:21.527387
13	abc	08767564654	2026-11-01 12:37:20.71119	2026-11-01 12:37:20.71119
14	mop	087797297286	2026-11-05 19:26:05.205647	2026-11-05 19:26:05.205647
15	abc	089767668	2026-11-05 19:41:59.706791	2026-11-05 19:41:59.706791
17	Movic	08123239298	2026-11-12 20:00:36.188628	2026-11-12 20:00:36.188628
18	Ibra	081281817281	2026-11-13 19:38:59.645516	2026-11-13 19:38:59.645516
19	mopic	081234568738748	2026-11-16 16:17:01.571903	2026-11-16 16:17:01.571903
20	m	08126512671	2026-11-16 16:29:45.149751	2026-11-16 16:29:45.149751
21	cba	08155536272	2026-11-16 16:33:58.231052	2026-11-16 16:33:58.231052
\.


--
-- Data for Name: pemesanan; Type: TABLE DATA; Schema: public; Owner: simasif_user
--

COPY public.pemesanan (id, kode_unik, pelanggan_id, layanan_id, pegawai_id, tanggal_jadwal, jam_jadwal, catatan_khusus, status, harga_saat_pesan, biaya_tambahan, diskon, created_at, updated_at) FROM stdin;
1	SF-JERCGP	4	10	\N	2026-10-15	10:00:00	\N	pemesanan_dibuat	30000.00	125000.00	0.00	2026-10-14 12:33:09.749037	2026-10-14 12:33:09.749037
2	SF-CZ4XXR	4	10	\N	2026-10-15	13:00:00	lorem ipsum	pemesanan_dibuat	30000.00	125000.00	0.00	2026-10-14 12:36:38.469439	2026-10-14 12:36:38.469439
4	SF-EPM2P2	6	10	\N	2026-10-29	10:00:00	abc	pemesanan_dibuat	15000.00	85000.00	0.00	2026-10-28 09:04:37.442476	2026-10-28 09:04:37.442476
5	SF-4RSXT8	7	18	\N	2026-10-29	10:45:00	abc	pemesanan_dibuat	120000.00	0.00	0.00	2026-10-28 09:14:08.844092	2026-10-28 09:14:08.844092
6	SF-QACJ5Z	8	16	\N	2026-10-29	14:15:00	\N	pemesanan_dibuat	30000.00	0.00	0.00	2026-10-28 09:16:11.038523	2026-10-28 09:16:11.038523
11	SF-4W7X8G	13	10	2	2026-11-25	10:00:00	asdfdfsgfgfggdsf	selesai	15000.00	135000.00	0.00	2026-11-01 12:37:20.71119	2026-11-13 19:29:36.43605
7	SF-8KCD7J	9	10	\N	2026-11-02	10:00:00	abc	pemesanan_dibuat	15000.00	135000.00	0.00	2026-11-01 10:52:19.131557	2026-11-01 10:52:19.131557
9	SF-LDC4AK	11	10	\N	2026-11-03	11:00:00	sfskjdfjdsf	pemesanan_dibuat	15000.00	135000.00	0.00	2026-11-01 10:54:14.22622	2026-11-01 10:54:14.22622
12	SF-NR5Y6D	14	10	2	2026-11-06	09:09:00	abcdefg	selesai	15000.00	135000.00	0.00	2026-11-05 19:26:05.205647	2026-11-13 19:38:19.850509
15	SF-ADTBWV	18	10	2	2026-11-14	11:00:00	abcdefghijklmno	menunggu_pembayaran	15000.00	135000.00	0.00	2026-11-13 19:39:42.836633	2026-11-13 19:57:04.294729
16	SF-5WSDJQ	19	10	\N	2026-11-17	10:00:00	abckaskasj	pemesanan_dibuat	15000.00	10000.00	0.00	2026-11-16 16:17:01.571903	2026-11-16 16:17:01.571903
13	SF-3AFWY7	15	10	2	2026-11-19	11:00:00	\N	selesai	15000.00	135000.00	0.00	2026-11-05 19:41:59.706791	2026-11-12 18:50:46.110705
14	SF-F5X6TN	17	10	\N	2026-11-13	10:00:00	\N	pemesanan_dibuat	15000.00	10000.00	0.00	2026-11-12 20:00:36.188628	2026-11-12 20:00:36.188628
10	SF-T7GYKJ	12	10	\N	2026-11-10	10:00:00	hchgjgkhjbjblj	menunggu_pembayaran	15000.00	10000.00	0.00	2026-11-01 10:55:21.527387	2026-11-12 20:25:57.78522
8	SF-ZUL4ZV	10	10	2	2026-11-09	09:30:00	BCas	menunggu_pembayaran	15000.00	135000.00	0.00	2026-11-01 10:52:56.183889	2026-11-12 20:40:03.856308
3	SF-Y8XVVK	5	19	2	2026-12-03	10:00:00	5R	selesai	10000.00	0.00	0.00	2026-10-28 08:49:18.325286	2026-11-12 20:40:25.267577
17	SF-PWYHAY	20	10	\N	2026-11-27	10:00:00	abc	pemesanan_dibuat	15000.00	135000.00	0.00	2026-11-16 16:29:45.149751	2026-11-16 16:29:45.149751
18	SF-9KNJQ6	21	10	2	2026-11-25	11:00:00	\N	pembayaran_diverifikasi	15000.00	10000.00	0.00	2026-11-16 16:33:58.231052	2026-11-16 17:00:15.245338
\.


--
-- Data for Name: pemesanan_addon; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pemesanan_addon (id, pemesanan_id, addon_id, nama_saat_pesan, harga_saat_pesan, jumlah, created_at) FROM stdin;
1	1	1	Tambah File Edit	50000.00	1	2026-10-14 12:33:09.749037
2	1	2	Edit Prioritas	75000.00	1	2026-10-14 12:33:09.749037
3	2	1	Tambah File Edit	50000.00	1	2026-10-14 12:36:38.469439
4	2	2	Edit Prioritas	75000.00	1	2026-10-14 12:36:38.469439
5	4	2	Edit Prioritas	75000.00	1	2026-10-28 09:04:37.442476
6	4	8	Cetak Hasil Foto	10000.00	1	2026-10-28 09:04:37.442476
7	7	1	Tambah File Edit	50000.00	1	2026-11-01 10:52:19.131557
8	7	2	Edit Prioritas	75000.00	1	2026-11-01 10:52:19.131557
9	7	8	Cetak Hasil Foto	10000.00	1	2026-11-01 10:52:19.131557
10	8	1	Tambah File Edit	50000.00	1	2026-11-01 10:52:56.183889
11	8	2	Edit Prioritas	75000.00	1	2026-11-01 10:52:56.183889
12	8	8	Cetak Hasil Foto	10000.00	1	2026-11-01 10:52:56.183889
13	9	1	Tambah File Edit	50000.00	1	2026-11-01 10:54:14.22622
14	9	2	Edit Prioritas	75000.00	1	2026-11-01 10:54:14.22622
15	9	8	Cetak Hasil Foto	10000.00	1	2026-11-01 10:54:14.22622
16	10	8	Cetak Hasil Foto	10000.00	1	2026-11-01 10:55:21.527387
17	11	1	Tambah File Edit	50000.00	1	2026-11-01 12:37:20.71119
18	11	2	Edit Prioritas	75000.00	1	2026-11-01 12:37:20.71119
19	11	8	Cetak Hasil Foto	10000.00	1	2026-11-01 12:37:20.71119
20	12	1	Tambah File Edit	50000.00	1	2026-11-05 19:26:05.205647
21	12	2	Edit Prioritas	75000.00	1	2026-11-05 19:26:05.205647
22	12	8	Cetak Hasil Foto	10000.00	1	2026-11-05 19:26:05.205647
23	13	1	Tambah File Edit	50000.00	1	2026-11-05 19:41:59.706791
24	13	2	Edit Prioritas	75000.00	1	2026-11-05 19:41:59.706791
25	13	8	Cetak Hasil Foto	10000.00	1	2026-11-05 19:41:59.706791
26	14	8	Cetak Hasil Foto	10000.00	1	2026-11-12 20:00:36.188628
27	15	1	Tambah File Edit	50000.00	1	2026-11-13 19:39:42.836633
28	15	2	Edit Prioritas	75000.00	1	2026-11-13 19:39:42.836633
29	15	8	Cetak Hasil Foto	10000.00	1	2026-11-13 19:39:42.836633
30	16	8	Cetak Hasil Foto	10000.00	1	2026-11-16 16:17:01.571903
31	17	1	Tambah File Edit	50000.00	1	2026-11-16 16:29:45.149751
32	17	2	Edit Prioritas	75000.00	1	2026-11-16 16:29:45.149751
33	17	8	Cetak Hasil Foto	10000.00	1	2026-11-16 16:29:45.149751
34	18	8	Cetak Hasil Foto	10000.00	1	2026-11-16 16:33:58.231052
\.


--
-- Data for Name: transaksi_pembayaran; Type: TABLE DATA; Schema: public; Owner: simasif_user
--

COPY public.transaksi_pembayaran (id, pemesanan_id, metode_pembayaran, jumlah_bayar, nomor_referensi, bukti_transfer, status_pembayaran, diverifikasi_oleh, diverifikasi_at, created_at, updated_at) FROM stdin;
1	11	transfer	150000.00	\N	1ed48cf70e17f7e578fe502316f4148e.jpg	lunas	2	2026-11-12 20:20:54.558764	2026-11-12 20:20:25.403821	2026-11-12 20:20:54.558764
2	10	transfer	25000.00	\N	8998dc697176090fe4d65298d101636d.jpg	ditolak	2	2026-11-12 20:26:10.216736	2026-11-12 20:25:57.78522	2026-11-12 20:26:10.216736
3	12	langsung	150000.00	\N	\N	lunas	2	2026-11-13 19:37:12.902607	2026-11-13 19:36:56.592429	2026-11-13 19:37:12.902607
4	3	langsung	10000.00	\N	\N	ditolak	2	2026-11-14 18:05:06.185278	2026-11-13 19:41:07.916358	2026-11-14 18:05:06.185278
5	18	langsung	25000.00	\N	\N	lunas	2	2026-11-16 17:00:15.245338	2026-11-16 16:34:32.036202	2026-11-16 17:00:15.245338
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: simasif_user
--

COPY public.users (id, nama, email, password, no_telepon, role, is_active, last_login_at, created_at, updated_at, reset_token, reset_token_expires_at) FROM stdin;
1	Sitta Laila Safitri	owner@simasif.local	$2y$12$ig/pYxguLEsZwi4AonHFvOBfMFpLHObSuvPI1XALNKSE6GJ2crMt6	\N	owner	t	2026-11-16 16:57:48.909388	2026-09-30 11:10:29.905059	2026-11-16 16:57:48.909388	\N	\N
2	Ayu Wardatul Widad	pegawai@simasif.local	$2y$12$ig/pYxguLEsZwi4AonHFvOBfMFpLHObSuvPI1XALNKSE6GJ2crMt6	\N	pegawai	t	2026-11-16 17:06:42.184789	2026-09-30 11:10:29.905059	2026-11-16 17:06:42.184789	\N	\N
\.


--
-- Data for Name: workflow_layanan; Type: TABLE DATA; Schema: public; Owner: simasif_user
--

COPY public.workflow_layanan (id, pemesanan_id, status_sebelumnya, status, catatan, diubah_oleh, created_at) FROM stdin;
1	1	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-10-14 12:33:09.749037
2	2	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-10-14 12:36:38.469439
3	3	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-10-28 08:49:18.325286
4	4	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-10-28 09:04:37.442476
5	5	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-10-28 09:14:08.844092
6	6	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-10-28 09:16:11.038523
7	7	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-01 10:52:19.131557
8	8	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-01 10:52:56.183889
9	9	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-01 10:54:14.22622
10	10	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-01 10:55:21.527387
11	11	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-01 12:37:20.71119
12	12	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-05 19:26:05.205647
13	13	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-05 19:41:59.706791
14	3	pemesanan_dibuat	menunggu_pembayaran	\N	2	2026-11-12 18:44:40.480926
15	3	menunggu_pembayaran	pembayaran_diverifikasi	\N	2	2026-11-12 18:46:10.919771
16	13	pemesanan_dibuat	menunggu_pembayaran	\N	2	2026-11-12 18:50:18.260242
17	13	menunggu_pembayaran	pembayaran_diverifikasi	\N	2	2026-11-12 18:50:31.530974
18	13	pembayaran_diverifikasi	terjadwal	\N	2	2026-11-12 18:50:35.539818
19	13	terjadwal	sesi_foto	\N	2	2026-11-12 18:50:37.321358
20	13	sesi_foto	proses_editing	\N	2	2026-11-12 18:50:40.075494
21	13	proses_editing	hasil_siap	\N	2	2026-11-12 18:50:42.956866
22	13	hasil_siap	selesai	\N	2	2026-11-12 18:50:46.110705
23	14	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-12 20:00:36.188628
24	11	pemesanan_dibuat	menunggu_pembayaran	Pelanggan mengunggah bukti transfer	\N	2026-11-12 20:20:25.403821
25	11	menunggu_pembayaran	pembayaran_diverifikasi	Pembayaran diverifikasi	2	2026-11-12 20:20:54.558764
26	3	pembayaran_diverifikasi	terjadwal	\N	2	2026-11-12 20:23:49.538905
27	10	pemesanan_dibuat	menunggu_pembayaran	Pelanggan mengunggah bukti transfer	\N	2026-11-12 20:25:57.78522
28	8	pemesanan_dibuat	menunggu_pembayaran	\N	2	2026-11-12 20:40:03.856308
29	3	terjadwal	sesi_foto	\N	2	2026-11-12 20:40:14.783471
30	3	sesi_foto	proses_editing	\N	2	2026-11-12 20:40:18.686702
31	3	proses_editing	hasil_siap	\N	2	2026-11-12 20:40:22.071832
32	3	hasil_siap	selesai	\N	2	2026-11-12 20:40:25.267577
33	11	pembayaran_diverifikasi	terjadwal	\N	2	2026-11-12 20:46:47.990034
34	11	terjadwal	sesi_foto	\N	2	2026-11-13 19:28:16.671055
35	11	sesi_foto	proses_editing	\N	2	2026-11-13 19:29:24.899171
36	11	proses_editing	hasil_siap	\N	2	2026-11-13 19:29:29.852618
37	11	hasil_siap	selesai	\N	2	2026-11-13 19:29:36.43605
38	12	pemesanan_dibuat	menunggu_pembayaran	Pelanggan memilih bayar langsung di studio	\N	2026-11-13 19:36:56.592429
39	12	menunggu_pembayaran	pembayaran_diverifikasi	Pembayaran diverifikasi	2	2026-11-13 19:37:12.902607
40	12	pembayaran_diverifikasi	terjadwal	\N	2	2026-11-13 19:38:10.684639
41	12	terjadwal	sesi_foto	\N	2	2026-11-13 19:38:13.023302
42	12	sesi_foto	proses_editing	\N	2	2026-11-13 19:38:14.478212
43	12	proses_editing	hasil_siap	\N	2	2026-11-13 19:38:16.047033
44	12	hasil_siap	selesai	\N	2	2026-11-13 19:38:19.850509
45	15	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-13 19:39:42.836633
46	15	pemesanan_dibuat	menunggu_pembayaran	abcdsjdkajsa	2	2026-11-13 19:57:04.294729
47	16	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-16 16:17:01.571903
48	17	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-16 16:29:45.149751
49	18	\N	pemesanan_dibuat	Pemesanan dibuat oleh pelanggan	\N	2026-11-16 16:33:58.231052
50	18	pemesanan_dibuat	menunggu_pembayaran	Pelanggan memilih bayar langsung di studio	\N	2026-11-16 16:34:32.036202
51	18	menunggu_pembayaran	pembayaran_diverifikasi	Pembayaran diverifikasi	2	2026-11-16 17:00:15.245338
\.


--
-- Name: addon_layanan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.addon_layanan_id_seq', 12, true);


--
-- Name: audit_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: simasif_user
--

SELECT pg_catalog.setval('public.audit_log_id_seq', 48, true);


--
-- Name: file_fotografi_id_seq; Type: SEQUENCE SET; Schema: public; Owner: simasif_user
--

SELECT pg_catalog.setval('public.file_fotografi_id_seq', 2, true);


--
-- Name: layanan_gambar_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.layanan_gambar_id_seq', 13, true);


--
-- Name: layanan_harga_grup_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.layanan_harga_grup_id_seq', 1, false);


--
-- Name: layanan_harga_pilihan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.layanan_harga_pilihan_id_seq', 1, false);


--
-- Name: layanan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: simasif_user
--

SELECT pg_catalog.setval('public.layanan_id_seq', 20, true);


--
-- Name: layanan_output_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.layanan_output_id_seq', 11, true);


--
-- Name: layanan_output_pilihan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.layanan_output_pilihan_id_seq', 1, false);


--
-- Name: pelanggan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: simasif_user
--

SELECT pg_catalog.setval('public.pelanggan_id_seq', 21, true);


--
-- Name: pemesanan_addon_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pemesanan_addon_id_seq', 34, true);


--
-- Name: pemesanan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: simasif_user
--

SELECT pg_catalog.setval('public.pemesanan_id_seq', 18, true);


--
-- Name: transaksi_pembayaran_id_seq; Type: SEQUENCE SET; Schema: public; Owner: simasif_user
--

SELECT pg_catalog.setval('public.transaksi_pembayaran_id_seq', 5, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: simasif_user
--

SELECT pg_catalog.setval('public.users_id_seq', 2, true);


--
-- Name: workflow_layanan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: simasif_user
--

SELECT pg_catalog.setval('public.workflow_layanan_id_seq', 51, true);


--
-- Name: addon_layanan addon_layanan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_layanan
    ADD CONSTRAINT addon_layanan_pkey PRIMARY KEY (id);


--
-- Name: audit_log audit_log_pkey; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.audit_log
    ADD CONSTRAINT audit_log_pkey PRIMARY KEY (id);


--
-- Name: file_fotografi file_fotografi_pkey; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.file_fotografi
    ADD CONSTRAINT file_fotografi_pkey PRIMARY KEY (id);


--
-- Name: layanan_gambar layanan_gambar_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.layanan_gambar
    ADD CONSTRAINT layanan_gambar_pkey PRIMARY KEY (id);


--
-- Name: layanan_harga_grup layanan_harga_grup_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.layanan_harga_grup
    ADD CONSTRAINT layanan_harga_grup_pkey PRIMARY KEY (id);


--
-- Name: layanan_harga_pilihan layanan_harga_pilihan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.layanan_harga_pilihan
    ADD CONSTRAINT layanan_harga_pilihan_pkey PRIMARY KEY (id);


--
-- Name: layanan_output_pilihan layanan_output_pilihan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.layanan_output_pilihan
    ADD CONSTRAINT layanan_output_pilihan_pkey PRIMARY KEY (id);


--
-- Name: layanan_output layanan_output_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.layanan_output
    ADD CONSTRAINT layanan_output_pkey PRIMARY KEY (id);


--
-- Name: layanan layanan_pkey; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.layanan
    ADD CONSTRAINT layanan_pkey PRIMARY KEY (id);


--
-- Name: pelanggan pelanggan_no_hp_key; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.pelanggan
    ADD CONSTRAINT pelanggan_no_hp_key UNIQUE (no_hp);


--
-- Name: pelanggan pelanggan_pkey; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.pelanggan
    ADD CONSTRAINT pelanggan_pkey PRIMARY KEY (id);


--
-- Name: pemesanan_addon pemesanan_addon_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pemesanan_addon
    ADD CONSTRAINT pemesanan_addon_pkey PRIMARY KEY (id);


--
-- Name: pemesanan pemesanan_kode_unik_key; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.pemesanan
    ADD CONSTRAINT pemesanan_kode_unik_key UNIQUE (kode_unik);


--
-- Name: pemesanan pemesanan_pkey; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.pemesanan
    ADD CONSTRAINT pemesanan_pkey PRIMARY KEY (id);


--
-- Name: transaksi_pembayaran transaksi_pembayaran_pemesanan_id_key; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.transaksi_pembayaran
    ADD CONSTRAINT transaksi_pembayaran_pemesanan_id_key UNIQUE (pemesanan_id);


--
-- Name: transaksi_pembayaran transaksi_pembayaran_pkey; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.transaksi_pembayaran
    ADD CONSTRAINT transaksi_pembayaran_pkey PRIMARY KEY (id);


--
-- Name: addon_layanan uq_addon_layanan_nama; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_layanan
    ADD CONSTRAINT uq_addon_layanan_nama UNIQUE (layanan_id, nama);


--
-- Name: pemesanan_addon uq_pemesanan_addon; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pemesanan_addon
    ADD CONSTRAINT uq_pemesanan_addon UNIQUE (pemesanan_id, addon_id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: workflow_layanan workflow_layanan_pkey; Type: CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.workflow_layanan
    ADD CONSTRAINT workflow_layanan_pkey PRIMARY KEY (id);


--
-- Name: idx_addon_layanan_layanan; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_addon_layanan_layanan ON public.addon_layanan USING btree (layanan_id);


--
-- Name: idx_audit_created; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_audit_created ON public.audit_log USING btree (created_at);


--
-- Name: idx_audit_modul; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_audit_modul ON public.audit_log USING btree (modul);


--
-- Name: idx_audit_user; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_audit_user ON public.audit_log USING btree (user_id);


--
-- Name: idx_file_jenis; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_file_jenis ON public.file_fotografi USING btree (jenis_file);


--
-- Name: idx_file_pemesanan; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_file_pemesanan ON public.file_fotografi USING btree (pemesanan_id);


--
-- Name: idx_file_pengunggah; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_file_pengunggah ON public.file_fotografi USING btree (diunggah_oleh);


--
-- Name: idx_harga_grup_layanan; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_harga_grup_layanan ON public.layanan_harga_grup USING btree (layanan_id, urutan);


--
-- Name: idx_layanan_gambar_layanan; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_layanan_gambar_layanan ON public.layanan_gambar USING btree (layanan_id, urutan);


--
-- Name: idx_output_layanan; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_output_layanan ON public.layanan_output USING btree (layanan_id, urutan);


--
-- Name: idx_pemesanan_addon_addon; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pemesanan_addon_addon ON public.pemesanan_addon USING btree (addon_id);


--
-- Name: idx_pemesanan_jadwal; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_pemesanan_jadwal ON public.pemesanan USING btree (tanggal_jadwal, jam_jadwal);


--
-- Name: idx_pemesanan_pegawai; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_pemesanan_pegawai ON public.pemesanan USING btree (pegawai_id);


--
-- Name: idx_pemesanan_pelanggan; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_pemesanan_pelanggan ON public.pemesanan USING btree (pelanggan_id);


--
-- Name: idx_pemesanan_status; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_pemesanan_status ON public.pemesanan USING btree (status);


--
-- Name: idx_transaksi_pembayaran_status; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_transaksi_pembayaran_status ON public.transaksi_pembayaran USING btree (status_pembayaran);


--
-- Name: idx_transaksi_pembayaran_verifikasi; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_transaksi_pembayaran_verifikasi ON public.transaksi_pembayaran USING btree (diverifikasi_oleh);


--
-- Name: idx_workflow_created_at; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_workflow_created_at ON public.workflow_layanan USING btree (created_at);


--
-- Name: idx_workflow_diubah_oleh; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_workflow_diubah_oleh ON public.workflow_layanan USING btree (diubah_oleh);


--
-- Name: idx_workflow_pemesanan; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE INDEX idx_workflow_pemesanan ON public.workflow_layanan USING btree (pemesanan_id);


--
-- Name: uq_harga_pilihan_nama; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX uq_harga_pilihan_nama ON public.layanan_harga_pilihan USING btree (grup_id, lower((nama)::text));


--
-- Name: uq_layanan_gambar_cover; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX uq_layanan_gambar_cover ON public.layanan_gambar USING btree (layanan_id) WHERE is_cover;


--
-- Name: uq_layanan_nama_lower; Type: INDEX; Schema: public; Owner: simasif_user
--

CREATE UNIQUE INDEX uq_layanan_nama_lower ON public.layanan USING btree (lower((nama)::text));


--
-- Name: uq_output_pilihan_nama; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX uq_output_pilihan_nama ON public.layanan_output_pilihan USING btree (output_id, lower((nama)::text));


--
-- Name: addon_layanan set_updated_at_addon_layanan; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER set_updated_at_addon_layanan BEFORE UPDATE ON public.addon_layanan FOR EACH ROW EXECUTE FUNCTION public.trigger_set_updated_at();


--
-- Name: layanan set_updated_at_layanan; Type: TRIGGER; Schema: public; Owner: simasif_user
--

CREATE TRIGGER set_updated_at_layanan BEFORE UPDATE ON public.layanan FOR EACH ROW EXECUTE FUNCTION public.trigger_set_updated_at();


--
-- Name: pelanggan set_updated_at_pelanggan; Type: TRIGGER; Schema: public; Owner: simasif_user
--

CREATE TRIGGER set_updated_at_pelanggan BEFORE UPDATE ON public.pelanggan FOR EACH ROW EXECUTE FUNCTION public.trigger_set_updated_at();


--
-- Name: pemesanan set_updated_at_pemesanan; Type: TRIGGER; Schema: public; Owner: simasif_user
--

CREATE TRIGGER set_updated_at_pemesanan BEFORE UPDATE ON public.pemesanan FOR EACH ROW EXECUTE FUNCTION public.trigger_set_updated_at();


--
-- Name: transaksi_pembayaran set_updated_at_transaksi_pembayaran; Type: TRIGGER; Schema: public; Owner: simasif_user
--

CREATE TRIGGER set_updated_at_transaksi_pembayaran BEFORE UPDATE ON public.transaksi_pembayaran FOR EACH ROW EXECUTE FUNCTION public.trigger_set_updated_at();


--
-- Name: users set_updated_at_users; Type: TRIGGER; Schema: public; Owner: simasif_user
--

CREATE TRIGGER set_updated_at_users BEFORE UPDATE ON public.users FOR EACH ROW EXECUTE FUNCTION public.trigger_set_updated_at();


--
-- Name: pemesanan_addon sync_biaya_tambahan; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER sync_biaya_tambahan AFTER INSERT OR DELETE OR UPDATE ON public.pemesanan_addon FOR EACH ROW EXECUTE FUNCTION public.trigger_sync_biaya_tambahan();


--
-- Name: addon_layanan addon_layanan_layanan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_layanan
    ADD CONSTRAINT addon_layanan_layanan_id_fkey FOREIGN KEY (layanan_id) REFERENCES public.layanan(id) ON DELETE CASCADE;


--
-- Name: audit_log audit_log_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.audit_log
    ADD CONSTRAINT audit_log_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: file_fotografi file_fotografi_diunggah_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.file_fotografi
    ADD CONSTRAINT file_fotografi_diunggah_oleh_fkey FOREIGN KEY (diunggah_oleh) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: file_fotografi file_fotografi_pemesanan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.file_fotografi
    ADD CONSTRAINT file_fotografi_pemesanan_id_fkey FOREIGN KEY (pemesanan_id) REFERENCES public.pemesanan(id) ON DELETE CASCADE;


--
-- Name: layanan_gambar layanan_gambar_diunggah_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.layanan_gambar
    ADD CONSTRAINT layanan_gambar_diunggah_oleh_fkey FOREIGN KEY (diunggah_oleh) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: layanan_gambar layanan_gambar_layanan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.layanan_gambar
    ADD CONSTRAINT layanan_gambar_layanan_id_fkey FOREIGN KEY (layanan_id) REFERENCES public.layanan(id) ON DELETE CASCADE;


--
-- Name: layanan_harga_grup layanan_harga_grup_layanan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.layanan_harga_grup
    ADD CONSTRAINT layanan_harga_grup_layanan_id_fkey FOREIGN KEY (layanan_id) REFERENCES public.layanan(id) ON DELETE CASCADE;


--
-- Name: layanan_harga_pilihan layanan_harga_pilihan_grup_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.layanan_harga_pilihan
    ADD CONSTRAINT layanan_harga_pilihan_grup_id_fkey FOREIGN KEY (grup_id) REFERENCES public.layanan_harga_grup(id) ON DELETE CASCADE;


--
-- Name: layanan_output layanan_output_layanan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.layanan_output
    ADD CONSTRAINT layanan_output_layanan_id_fkey FOREIGN KEY (layanan_id) REFERENCES public.layanan(id) ON DELETE CASCADE;


--
-- Name: layanan_output_pilihan layanan_output_pilihan_output_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.layanan_output_pilihan
    ADD CONSTRAINT layanan_output_pilihan_output_id_fkey FOREIGN KEY (output_id) REFERENCES public.layanan_output(id) ON DELETE CASCADE;


--
-- Name: pemesanan_addon pemesanan_addon_addon_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pemesanan_addon
    ADD CONSTRAINT pemesanan_addon_addon_id_fkey FOREIGN KEY (addon_id) REFERENCES public.addon_layanan(id) ON DELETE RESTRICT;


--
-- Name: pemesanan_addon pemesanan_addon_pemesanan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pemesanan_addon
    ADD CONSTRAINT pemesanan_addon_pemesanan_id_fkey FOREIGN KEY (pemesanan_id) REFERENCES public.pemesanan(id) ON DELETE CASCADE;


--
-- Name: pemesanan pemesanan_layanan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.pemesanan
    ADD CONSTRAINT pemesanan_layanan_id_fkey FOREIGN KEY (layanan_id) REFERENCES public.layanan(id) ON DELETE RESTRICT;


--
-- Name: pemesanan pemesanan_pegawai_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.pemesanan
    ADD CONSTRAINT pemesanan_pegawai_id_fkey FOREIGN KEY (pegawai_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: pemesanan pemesanan_pelanggan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.pemesanan
    ADD CONSTRAINT pemesanan_pelanggan_id_fkey FOREIGN KEY (pelanggan_id) REFERENCES public.pelanggan(id) ON DELETE RESTRICT;


--
-- Name: transaksi_pembayaran transaksi_pembayaran_diverifikasi_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.transaksi_pembayaran
    ADD CONSTRAINT transaksi_pembayaran_diverifikasi_oleh_fkey FOREIGN KEY (diverifikasi_oleh) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: transaksi_pembayaran transaksi_pembayaran_pemesanan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.transaksi_pembayaran
    ADD CONSTRAINT transaksi_pembayaran_pemesanan_id_fkey FOREIGN KEY (pemesanan_id) REFERENCES public.pemesanan(id) ON DELETE CASCADE;


--
-- Name: workflow_layanan workflow_layanan_diubah_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.workflow_layanan
    ADD CONSTRAINT workflow_layanan_diubah_oleh_fkey FOREIGN KEY (diubah_oleh) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: workflow_layanan workflow_layanan_pemesanan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: simasif_user
--

ALTER TABLE ONLY public.workflow_layanan
    ADD CONSTRAINT workflow_layanan_pemesanan_id_fkey FOREIGN KEY (pemesanan_id) REFERENCES public.pemesanan(id) ON DELETE CASCADE;


--
-- Name: TABLE addon_layanan; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.addon_layanan TO simasif_user;


--
-- Name: SEQUENCE addon_layanan_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.addon_layanan_id_seq TO simasif_user;


--
-- Name: TABLE layanan_gambar; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.layanan_gambar TO simasif_user;


--
-- Name: SEQUENCE layanan_gambar_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.layanan_gambar_id_seq TO simasif_user;


--
-- Name: TABLE layanan_harga_grup; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.layanan_harga_grup TO simasif_user;


--
-- Name: SEQUENCE layanan_harga_grup_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.layanan_harga_grup_id_seq TO simasif_user;


--
-- Name: TABLE layanan_harga_pilihan; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.layanan_harga_pilihan TO simasif_user;


--
-- Name: SEQUENCE layanan_harga_pilihan_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.layanan_harga_pilihan_id_seq TO simasif_user;


--
-- Name: TABLE layanan_output; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.layanan_output TO simasif_user;


--
-- Name: SEQUENCE layanan_output_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.layanan_output_id_seq TO simasif_user;


--
-- Name: TABLE layanan_output_pilihan; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.layanan_output_pilihan TO simasif_user;


--
-- Name: SEQUENCE layanan_output_pilihan_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.layanan_output_pilihan_id_seq TO simasif_user;


--
-- Name: TABLE pemesanan_addon; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.pemesanan_addon TO simasif_user;


--
-- Name: SEQUENCE pemesanan_addon_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.pemesanan_addon_id_seq TO simasif_user;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO simasif_user;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT SELECT,INSERT,DELETE,UPDATE ON TABLES TO simasif_user;


--
-- PostgreSQL database dump complete
--

\unrestrict ChlPrtrfch7TFyCnQVAoY8ZT8bSn01BBcX8ZgYhB19i3TQXLNGoZ0gnE2dERKtz

