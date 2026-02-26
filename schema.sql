CREATE DATABASE IF NOT EXISTS sistema_hospital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_hospital;

CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    dob DATE NULL,
    phone VARCHAR(40) NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    department VARCHAR(120) NOT NULL,
    doctor VARCHAR(120) NOT NULL,
    status VARCHAR(40) NOT NULL,
    notes VARCHAR(255) NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lab_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    test_name VARCHAR(120) NOT NULL,
    result_value VARCHAR(80) NOT NULL,
    unit VARCHAR(40) NULL,
    reference_range VARCHAR(80) NULL,
    result_date DATE NOT NULL,
    status VARCHAR(40) NOT NULL,
    notes VARCHAR(255) NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS medical_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    condition_name VARCHAR(120) NOT NULL,
    diagnosed_date DATE NULL,
    status VARCHAR(40) NOT NULL,
    notes VARCHAR(255) NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS medication_reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    medication_name VARCHAR(120) NOT NULL,
    dosage VARCHAR(80) NOT NULL,
    frequency VARCHAR(80) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    instructions VARCHAR(255) NULL,
    next_refill_date DATE NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB;
