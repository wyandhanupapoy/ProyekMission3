-- Database Setup untuk Sistem Akademik
-- Buat database terlebih dahulu
CREATE DATABASE IF NOT EXISTS akademik_db;
USE akademik_db;

-- Struktur dari tabel users
CREATE TABLE users (
    user_id bigint(20) NOT NULL AUTO_INCREMENT,
    username varchar(100) NOT NULL,
    password varchar(255) NOT NULL,
    role enum('Admin','Mahasiswa') NOT NULL,
    full_name varchar(255) NOT NULL,
    email varchar(255) DEFAULT NULL,
    phone varchar(20) DEFAULT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (user_id),
    UNIQUE KEY username (username),
    UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Password untuk admin: admin123
-- Password untuk mahasiswa: mhs123
INSERT INTO users (user_id, username, password, role, full_name, email, phone) VALUES
(1, 'admin', '$2y$10$Y5/P9A/h..83A4/9R.gL9eS.wL0qj5t7eG6hGf/8d.yRwwQCoG3Gq', 'Admin', 'Administrator Web', 'admin@akademik.ac.id', '08123456789'),
(2, 'mahasiswa', '$2y$10$vQ3pG.oDkXyvA.1tJ5.r8OKiF0qg.A.s4dF8F2.j/z.K9.r5Qz3rO', 'Mahasiswa', 'Budi Santoso', 'budi@student.ac.id', '08234567890'),
(3, 'sari', '$2y$10$vQ3pG.oDkXyvA.1tJ5.r8OKiF0qg.A.s4dF8F2.j/z.K9.r5Qz3rO', 'Mahasiswa', 'Sari Indah', 'sari@student.ac.id', '08345678901'),
(4, 'andi', '$2y$10$vQ3pG.oDkXyvA.1tJ5.r8OKiF0qg.A.s4dF8F2.j/z.K9.r5Qz3rO', 'Mahasiswa', 'Andi Rahman', 'andi@student.ac.id', '08456789012');

-- Struktur dari tabel students
CREATE TABLE students (
    student_id bigint(20) NOT NULL,
    nim varchar(20) NOT NULL,
    entry_year year(4) NOT NULL,
    major varchar(100) DEFAULT NULL,
    status enum('Aktif','Tidak Aktif','Lulus','Drop Out') DEFAULT 'Aktif',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (student_id),
    UNIQUE KEY nim (nim),
    CONSTRAINT students_ibfk_1 FOREIGN KEY (student_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk tabel students
INSERT INTO students (student_id, nim, entry_year, major, status) VALUES
(2, '230001', 2023, 'Teknik Informatika', 'Aktif'),
(3, '230002', 2023, 'Sistem Informasi', 'Aktif'),
(4, '230003', 2023, 'Teknik Informatika', 'Aktif');

-- Struktur dari tabel courses
CREATE TABLE courses (
    course_id bigint(20) NOT NULL AUTO_INCREMENT,
    course_code varchar(20) NOT NULL,
    course_name varchar(255) NOT NULL,
    credits tinyint(4) NOT NULL,
    semester tinyint(4) DEFAULT NULL,
    description text DEFAULT NULL,
    status enum('Aktif','Tidak Aktif') DEFAULT 'Aktif',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (course_id),
    UNIQUE KEY course_code (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data untuk tabel courses
INSERT INTO courses (course_id, course_code, course_name, credits, semester, description, status) VALUES
(1, 'TIF101', 'Dasar Pemrograman', 3, 1, 'Mata kuliah dasar pemrograman menggunakan bahasa pemrograman modern', 'Aktif'),
(2, 'TIF201', 'Struktur Data dan Algoritma', 4, 3, 'Mempelajari struktur data dan algoritma fundamental dalam pemrograman', 'Aktif'),
(3, 'TIF202', 'Basis Data', 3, 4, 'Konsep dan implementasi sistem basis data relasional', 'Aktif'),
(4, 'TIF301', 'Jaringan Komputer', 3, 5, 'Dasar-dasar jaringan komputer dan protokol komunikasi', 'Aktif'),
(5, 'TIF102', 'Matematika Diskrit', 3, 2, 'Matematika untuk ilmu komputer dan logika', 'Aktif');

-- Struktur dari tabel takes
CREATE TABLE takes (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    student_id bigint(20) NOT NULL,
    course_id bigint(20) NOT NULL,
    enroll_date date NOT NULL,
    grade varchar(2) DEFAULT NULL,
    status enum('Enrolled','Completed','Dropped') DEFAULT 'Enrolled',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (id),
    UNIQUE KEY unique_enrollment (student_id, course_id),
    KEY student_id (student_id),
    KEY course_id (course_id),
    CONSTRAINT takes_ibfk_1 FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT takes_ibfk_2 FOREIGN KEY (course_id) REFERENCES courses (course_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data sample untuk enrollment
INSERT INTO takes (student_id, course_id, enroll_date, status) VALUES
(2, 1, '2024-01-15', 'Enrolled'),
(2, 2, '2024-01-20', 'Enrolled'),
(3, 1, '2024-01-15', 'Enrolled'),
(3, 3, '2024-02-01', 'Enrolled');