-- Safe schema export for repository use
-- No real employee data or local credentials are included here.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `pegawai` (
  `nama` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `nip` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `gol_ruang_cpns` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tmt_cpns` date DEFAULT NULL,
  `pangkat_terakhir` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'L',
  `jabatan` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `eselon` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `diklat_penjenjangan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `instansi_pembayar` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `role` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `foto_profil` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `foto_posisi` varchar(30) COLLATE utf8mb4_general_ci DEFAULT 'center center',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`nip`),
  KEY `idx_pegawai_nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pegawai_drh` (
  `nip` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tingkat_pendidikan` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jurusan` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun_lulus` year DEFAULT NULL,
  `alumni` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`nip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pegawai_pending` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nip` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `gol_ruang_cpns` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tmt_cpns` date DEFAULT NULL,
  `pangkat_terakhir` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'L',
  `jabatan` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `eselon` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `diklat_penjenjangan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `instansi_pembayar` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `tempat_lahir` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `status_kawin` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `agama` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_general_ci,
  `no_telp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tingkat_pendidikan` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jurusan` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun_lulus` year DEFAULT NULL,
  `alumni` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `approved_by` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pegawai_pending_nip` (`nip`),
  KEY `idx_pegawai_pending_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pegawai_pribadi` (
  `nip` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tempat_lahir` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `status_kawin` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `agama` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_general_ci,
  `no_telp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`nip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pengajuan_surat_sakit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nip` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_surat` date NOT NULL,
  `tanggal_izin` date NOT NULL,
  `alasan` text COLLATE utf8mb4_general_ci NOT NULL,
  `penandatangan` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `nomor_surat` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nomor_surat_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pengajuan_surat_sakit_nip` (`nip`),
  KEY `idx_pengajuan_surat_sakit_nomor_surat` (`nomor_surat`),
  KEY `idx_pengajuan_surat_sakit_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `surat_pegawai` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nip` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_surat` date NOT NULL,
  `tanggal_izin` date NOT NULL,
  `alasan` text COLLATE utf8mb4_general_ci NOT NULL,
  `penandatangan_nip` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_surat_pegawai_nip` (`nip`),
  KEY `idx_surat_pegawai_penandatangan` (`penandatangan_nip`),
  KEY `idx_surat_pegawai_tanggal_surat` (`tanggal_surat`),
  KEY `idx_surat_pegawai_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `template_surat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_template` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_template` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `sub_menu` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `sort_order` tinyint NOT NULL DEFAULT '0',
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_original_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_mime` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_template_surat_kode` (`kode_template`),
  KEY `idx_template_surat_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `template_surat` (`kode_template`, `nama_template`, `sub_menu`, `sort_order`) VALUES
('surat_keterangan_sakit', 'Template Surat Keterangan Sakit', 'Surat Sakit', 1),
('usulan_kenaikan_pangkat', 'Template Usulan Kenaikan Pangkat', 'Usulan Kenaikan Pangkat', 2),
('usulan_cuti_tahun', 'Template Usulan Cuti Tahun', 'Usulan Cuti Tahun', 3),
('usulan_alasan_penting', 'Template Usulan Alasan Penting', 'Usulan Alasan Penting', 4),
('usulan_kenaikan_gaji_berkala', 'Template Usulan Kenaikan Gaji Berkala', 'Usulan Kenaikan Gaji Berkala', 5);

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `role` enum('admin','petugas','viewer') NOT NULL DEFAULT 'petugas',
  `nama_lengkap` varchar(200) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `foto_profil` varchar(255) DEFAULT NULL,
  `foto_posisi` varchar(30) DEFAULT 'center center',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

COMMIT;
