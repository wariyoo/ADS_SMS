-- MySQL Schema for ADB_SMS
-- (Note: Ensure the database 'sm_system' exists before running this)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Table: DEPARTMENT
CREATE TABLE IF NOT EXISTS DEPARTMENT (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    dept_name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: ACADEMIC_YEAR
CREATE TABLE IF NOT EXISTS ACADEMIC_YEAR (
    year_id INT AUTO_INCREMENT PRIMARY KEY,
    year_label VARCHAR(20) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: SEMESTER
CREATE TABLE IF NOT EXISTS SEMESTER (
    semester_id INT AUTO_INCREMENT PRIMARY KEY,
    year_id INT NOT NULL,
    semester_label VARCHAR(50) NOT NULL,
    UNIQUE(year_id, semester_label),
    FOREIGN KEY (year_id) REFERENCES ACADEMIC_YEAR(year_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: SUBJECT
CREATE TABLE IF NOT EXISTS SUBJECT (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL UNIQUE,
    total_mark INT DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: SUBJECT_OFFERING
CREATE TABLE IF NOT EXISTS SUBJECT_OFFERING (
    offering_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    semester_id INT NOT NULL,
    UNIQUE(subject_id, semester_id),
    FOREIGN KEY (subject_id) REFERENCES SUBJECT(subject_id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES SEMESTER(semester_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: ADMIN
CREATE TABLE IF NOT EXISTS ADMIN (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: TEACHER
CREATE TABLE IF NOT EXISTS TEACHER (
    teacher_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department_id INT,
    assigned_class VARCHAR(20),
    FOREIGN KEY (department_id) REFERENCES DEPARTMENT(department_id) ON SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: STUDENT
CREATE TABLE IF NOT EXISTS STUDENT (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id_formatted VARCHAR(20) UNIQUE,
    name VARCHAR(100) NOT NULL,
    gender ENUM('M', 'F') NOT NULL,
    grade VARCHAR(20) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    status ENUM('Pending', 'Active', 'Rejected') DEFAULT 'Pending',
    requested_year_id INT,
    requested_semester_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (requested_year_id) REFERENCES ACADEMIC_YEAR(year_id) ON DELETE SET NULL,
    FOREIGN KEY (requested_semester_id) REFERENCES SEMESTER(semester_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: ENROLLMENT
CREATE TABLE IF NOT EXISTS ENROLLMENT (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    year_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(student_id, year_id),
    FOREIGN KEY (student_id) REFERENCES STUDENT(student_id) ON DELETE CASCADE,
    FOREIGN KEY (year_id) REFERENCES ACADEMIC_YEAR(year_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: STUDENT_SUBJECT
CREATE TABLE IF NOT EXISTS STUDENT_SUBJECT (
    student_id INT NOT NULL,
    offering_id INT NOT NULL,
    PRIMARY KEY (student_id, offering_id),
    FOREIGN KEY (student_id) REFERENCES STUDENT(student_id) ON DELETE CASCADE,
    FOREIGN KEY (offering_id) REFERENCES SUBJECT_OFFERING(offering_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: MARK
CREATE TABLE IF NOT EXISTS MARK (
    student_id INT NOT NULL,
    offering_id INT NOT NULL,
    mark DECIMAL(5, 2) NOT NULL,
    PRIMARY KEY (student_id, offering_id),
    CHECK (mark >= 0 AND mark <= 100),
    FOREIGN KEY (student_id) REFERENCES STUDENT(student_id) ON DELETE CASCADE,
    FOREIGN KEY (offering_id) REFERENCES SUBJECT_OFFERING(offering_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO ADMIN (username, password) VALUES ('admin', '$2y$10$EhpCIGwApXGsbXC23q6R.OuTv145Uf9v.Xqu4gF5XjU6GQh32i28G');


--=============================================
-- 1. Disable foreign key checks to prevent errors during conversion
SET foreign_key_checks = 0;
-- 2. Set the Database default collation
ALTER DATABASE sm_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 3. Convert every table in your schema to the correct collation
ALTER TABLE ACADEMIC_YEAR CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE ADMIN CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE DEPARTMENT CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE ENROLLMENT CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE MARK CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE SEMESTER CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE STUDENT CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE STUDENT_SUBJECT CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE SUBJECT CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE SUBJECT_OFFERING CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE TEACHER CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 4. Re-enable foreign key checks
SET foreign_key_checks = 1;
--=============================================


COMMIT;
