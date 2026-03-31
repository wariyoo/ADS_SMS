-- ============================================
-- COMPLETE FIX - PROPER TRUNCATION ORDER
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Truncate tables in reverse order of dependencies (child tables first)
TRUNCATE TABLE MARK;
TRUNCATE TABLE STUDENT_SUBJECT;
TRUNCATE TABLE ENROLLMENT;
TRUNCATE TABLE STUDENT;
TRUNCATE TABLE TEACHER;
TRUNCATE TABLE ADMIN;
TRUNCATE TABLE SUBJECT_OFFERING;
TRUNCATE TABLE SUBJECT;
TRUNCATE TABLE SEMESTER;
TRUNCATE TABLE ACADEMIC_YEAR;
TRUNCATE TABLE DEPARTMENT;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- NOW INSERT DATA IN CORRECT ORDER
-- ============================================

-- 1. Insert Departments (Using IGNORE to prevent duplicate errors)
INSERT IGNORE INTO DEPARTMENT (dept_name) VALUES 
('Natural Science Dept'),
('Mathematics Dept'),
('Physics Dept'),
('Chemistry Dept'),
('Biology Dept'),
('Social Science Dept'),
('English Dept'),
('Geography Dept'),
('History Dept'),
('Economics Dept'),
('Civics Dept');

-- 2. Insert Subjects
INSERT IGNORE INTO SUBJECT (subject_name, total_mark) VALUES 
('Mathematics', 100),
('English', 100),
('Physics', 100),
('Chemistry', 100),
('Biology', 100),
('Geography', 100),
('History', 100),
('Civics and Ethical Education', 100),
('Physical Education', 50),
('Mathematics (Natural)', 100),
('English (Natural)', 100),
('Physics (Advanced)', 100),
('Chemistry (Advanced)', 100),
('Biology (Advanced)', 100),
('Applied Mathematics', 100),
('Mathematics (Social)', 100),
('English (Social)', 100),
('Economics', 100),
('Geography (Advanced)', 100),
('History (Advanced)', 100),
('Civics (Advanced)', 100),
('Business Studies', 100),
('Accounting', 100);

-- 3. Insert Academic Years
INSERT IGNORE INTO ACADEMIC_YEAR (year_label) VALUES 
('2016 E.C'),
('2017 E.C'),
('2018 E.C');

-- 4. Insert Semesters
INSERT IGNORE INTO SEMESTER (year_id, semester_label) VALUES 
(1, 'Semester I - 2016 E.C'),
(1, 'Semester II - 2016 E.C'),
(2, 'Semester I - 2017 E.C'),
(2, 'Semester II - 2017 E.C'),
(3, 'Semester I - 2018 E.C'),
(3, 'Semester II - 2018 E.C');

-- 5. Insert Subject Offerings
INSERT IGNORE INTO SUBJECT_OFFERING (subject_id, semester_id) VALUES 
-- Grade 9 - Semester I (2016 E.C, semester_id=1)
(1, 1), (2, 1), (3, 1), (4, 1), (5, 1), (6, 1), (7, 1), (8, 1), (9, 1),
-- Grade 9 - Semester II (2016 E.C, semester_id=2)
(1, 2), (2, 2), (3, 2), (4, 2), (5, 2), (6, 2), (7, 2), (8, 2), (9, 2),
-- Grade 10 - Semester I (2017 E.C, semester_id=3)
(1, 3), (2, 3), (3, 3), (4, 3), (5, 3), (6, 3), (7, 3), (8, 3),
-- Grade 10 - Semester II (2017 E.C, semester_id=4)
(1, 4), (2, 4), (3, 4), (4, 4), (5, 4), (6, 4), (7, 4), (8, 4),
-- Grade 11 Natural - Semester I (2018 E.C, semester_id=5)
(10, 5), (11, 5), (12, 5), (13, 5), (14, 5), (15, 5),
-- Grade 11 Natural - Semester II (2018 E.C, semester_id=6)
(10, 6), (11, 6), (12, 6), (13, 6), (14, 6),
-- Grade 11 Social - Semester I (2018 E.C, semester_id=5)
(16, 5), (17, 5), (18, 5), (19, 5), (20, 5), (21, 5), (22, 5),
-- Grade 11 Social - Semester II (2018 E.C, semester_id=6)
(16, 6), (17, 6), (18, 6), (19, 6), (20, 6), (21, 6), (23, 6);

-- 6. Insert Admin users
INSERT IGNORE INTO ADMIN (username, password) VALUES 
('admin', '$2y$10$EhpCIGwApXGsbXC23q6R.OuTv145Uf9v.Xqu4gF5XjU6GQh32i28G'),
('principal', '$2y$10$EhpCIGwApXGsbXC23q6R.OuTv145Uf9v.Xqu4gF5XjU6GQh32i28G');

-- 7. Insert Teachers
INSERT IGNORE INTO TEACHER (name, username, password, department_id, assigned_class) VALUES 
('Ato Tekle Berhan', 'tberhan', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 2, 'Grade 9A'),
('W/ro Genet Assefa', 'gassefa', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 7, 'Grade 9B'),
('Dr. Abebe Kebede', 'akebede', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 3, 'Grade 10 Natural'),
('Ato Fikre Alemayehu', 'falemayehu', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 4, 'Grade 10 Natural'),
('W/ro Mulu Worku', 'mworku', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 5, 'Grade 10 Natural'),
('Ato Dawit Haile', 'dhaile', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 6, 'Grade 11 Natural'),
('W/ro Tigist Worku', 'tworku', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 1, 'Grade 11 Social'),
('Ato Yonas Ayele', 'yayele', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 8, 'Grade 12 Social'),
('W/ro Helen Bekele', 'hbekele', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 9, 'Grade 12 Natural'),
('Dr. Getachew Tesfaye', 'gtesfaye', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 10, 'Grade 11-12 Economics');

-- 8. Insert Students
INSERT IGNORE INTO STUDENT (student_id_formatted, name, gender, grade, username, password, status, requested_year_id, requested_semester_id) VALUES 
-- Grade 9 Students
('AA/9A/001/16', 'Abebe Bekele', 'M', 'Grade 9', 'abebe.b', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 1, 1),
('AA/9A/002/16', 'Almaz Tesfaye', 'F', 'Grade 9', 'almaz.t', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 1, 1),
('AA/9A/003/16', 'Biruk Alemu', 'M', 'Grade 9', 'biruk.a', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 1, 2),
('AA/9B/001/16', 'Chaltu Hassen', 'F', 'Grade 9', 'chaltu.h', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 1, 2),
('AA/9B/002/16', 'Dawit Mekonnen', 'M', 'Grade 9', 'dawit.m', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 1, 1),
('AA/9B/003/16', 'Eden Girma', 'F', 'Grade 9', 'eden.g', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 1, 2),
-- Grade 10 Students
('AA/10N/001/16', 'Fikru Assefa', 'M', 'Grade 10', 'fikru.a', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 2, 3),
('AA/10N/002/16', 'Gelila Wondimu', 'F', 'Grade 10', 'gelila.w', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 2, 3),
('AA/10N/003/16', 'Henok Tsegaye', 'M', 'Grade 10', 'henok.t', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 2, 4),
('AA/10N/004/16', 'Ibsa Mohammed', 'M', 'Grade 10', 'ibsa.m', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 2, 4),
-- Grade 11 Natural Science Students
('AA/11N/001/16', 'Jemal Ahmed', 'M', 'Grade 11', 'jemal.a', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 3, 5),
('AA/11N/002/16', 'Kalkidan Yilma', 'F', 'Grade 11', 'kalkidan.y', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 3, 5),
('AA/11N/003/16', 'Lemma Desta', 'M', 'Grade 11', 'lemma.d', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 3, 6),
-- Grade 11 Social Science Students
('AA/11S/001/16', 'Mahlet Berhan', 'F', 'Grade 11', 'mahlet.b', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 3, 5),
('AA/11S/002/16', 'Natnael Teshome', 'M', 'Grade 11', 'natnael.t', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 3, 5),
('AA/11S/003/16', 'Obsa Ali', 'M', 'Grade 11', 'obsa.a', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Active', 3, 6),
-- Pending Students
('AA/9A/004/16', 'Tigist Alemu', 'F', 'Grade 9', 'tigist.a', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Pending', 1, 1),
('AA/10N/005/16', 'Yonas Tadesse', 'M', 'Grade 10', 'yonas.t', '$2y$10$MTw9Rp6dR5qCtVmqHwSyB.TZdI6x1s0sWyViYYEeFZA7HIM1rk4lS', 'Pending', 2, 3);

-- 9. Insert Enrollments
INSERT IGNORE INTO ENROLLMENT (student_id, year_id) VALUES 
(1, 1), (2, 1), (3, 1), (4, 1), (5, 1), (6, 1),
(7, 2), (8, 2), (9, 2), (10, 2),
(11, 3), (12, 3), (13, 3), (14, 3), (15, 3), (16, 3);

-- 10. Insert Student Subjects using SELECT
INSERT IGNORE INTO STUDENT_SUBJECT (student_id, offering_id)
SELECT 1, offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 LIMIT 9;

INSERT IGNORE INTO STUDENT_SUBJECT (student_id, offering_id)
SELECT 2, offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 LIMIT 9;

INSERT IGNORE INTO STUDENT_SUBJECT (student_id, offering_id)
SELECT 7, offering_id FROM SUBJECT_OFFERING WHERE semester_id = 3 LIMIT 8;

INSERT IGNORE INTO STUDENT_SUBJECT (student_id, offering_id)
SELECT 11, offering_id FROM SUBJECT_OFFERING WHERE semester_id = 5 AND subject_id IN (10,11,12,13,14,15);

INSERT IGNORE INTO STUDENT_SUBJECT (student_id, offering_id)
SELECT 14, offering_id FROM SUBJECT_OFFERING WHERE semester_id = 5 AND subject_id IN (16,17,18,19,20,21,22);

-- 11. Insert Marks with proper mapping
-- For student 1
INSERT IGNORE INTO MARK (student_id, offering_id, mark)
SELECT s.student_id, s.offering_id,
    CASE s.offering_id
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 1 LIMIT 1) THEN 85.50
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 2 LIMIT 1) THEN 78.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 3 LIMIT 1) THEN 92.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 4 LIMIT 1) THEN 88.50
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 5 LIMIT 1) THEN 79.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 6 LIMIT 1) THEN 91.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 7 LIMIT 1) THEN 84.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 8 LIMIT 1) THEN 95.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 9 LIMIT 1) THEN 45.00
    END
FROM STUDENT_SUBJECT s
WHERE s.student_id = 1;

-- For student 2
INSERT IGNORE INTO MARK (student_id, offering_id, mark)
SELECT s.student_id, s.offering_id,
    CASE s.offering_id
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 1 LIMIT 1) THEN 92.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 2 LIMIT 1) THEN 88.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 3 LIMIT 1) THEN 86.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 4 LIMIT 1) THEN 94.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 5 LIMIT 1) THEN 90.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 6 LIMIT 1) THEN 87.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 7 LIMIT 1) THEN 91.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 8 LIMIT 1) THEN 93.00
        WHEN (SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = 1 AND subject_id = 9 LIMIT 1) THEN 48.00
    END
FROM STUDENT_SUBJECT s
WHERE s.student_id = 2;

-- Commit all changes
COMMIT;

-- ============================================
-- VERIFICATION
-- ============================================
SELECT '=== DATABASE SETUP COMPLETE ===' as Status;
SELECT 'Total students:' as Metric, COUNT(*) as Count FROM STUDENT;
SELECT 'Total teachers:' as Metric, COUNT(*) as Count FROM TEACHER;
SELECT 'Total enrollments:' as Metric, COUNT(*) as Count FROM ENROLLMENT;
SELECT 'Total student-subject registrations:' as Metric, COUNT(*) as Count FROM STUDENT_SUBJECT;
SELECT 'Total marks entered:' as Metric, COUNT(*) as Count FROM MARK;