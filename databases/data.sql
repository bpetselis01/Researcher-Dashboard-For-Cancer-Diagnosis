-- Create Users table (for oncologists and researchers)
DROP TABLE IF EXISTS Users;
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Oncologist', 'Researcher') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Patients table
DROP TABLE IF EXISTS Patients;
CREATE TABLE Patients (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    sex ENUM('Male', 'Female', 'Other') NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    country VARCHAR(50),
    age INT,
    cancer_type VARCHAR(100),
    treatment TEXT,
    mutational_profile TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample oncologists into Users table
INSERT INTO Users (first_name, last_name, username, password_hash, role) VALUES
('John', 'Doe', 'jdoe_oncologist', SHA2('password123', 256), 'Oncologist'),
('Jane', 'Smith', 'jsmith_oncologist', SHA2('securepass', 256), 'Oncologist');

-- Insert sample researchers into Users table
INSERT INTO Users (first_name, last_name, username, password_hash, role) VALUES
('Emily', 'Brown', 'ebrown_researcher', SHA2('researcherpass', 256), 'Researcher'),
('Michael', 'Clark', 'mclark_researcher', SHA2('data4life', 256), 'Researcher');

-- Insert sample patients into Patients table
INSERT INTO Patients (first_name, last_name, sex, email, phone, country, age, cancer_type, treatment, mutational_profile) VALUES
('Alice', 'Johnson', 'Female', 'alice.johnson@example.com', '1234567890', 'USA', 35, 'Lung Cancer', 'Chemotherapy', 'TP53:MUT; KRAS:MUT; EGFR:WT'),
('Bob', 'Williams', 'Male', 'bob.williams@example.com', '0987654321', 'UK', 50, 'Breast Cancer', 'Radiation Therapy', 'BRCA1:MUT; BRCA2:WT; HER2:MUT'),
('Catherine', 'Taylor', 'Female', 'catherine.taylor@example.com', '5678901234', 'Australia', 42, 'Colon Cancer', 'Surgery and Targeted Therapy', 'APC:MUT; KRAS:MUT; TP53:WT'),
('David', 'Anderson', 'Male', 'david.anderson@example.com', '8765432109', 'India', 60, 'Prostate Cancer', 'Hormonal Therapy', 'AR:MUT; PTEN:WT; TP53:MUT');

-- Verify initial data
SELECT * FROM Users;
SELECT * FROM Patients;
