-- Drop and recreate Users table (for oncologists and researchers)
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

-- Drop and recreate Patients table
DROP TABLE IF EXISTS Patients;
CREATE TABLE Patients (
    patient_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    sex ENUM('Male', 'Female', 'Other') NOT NULL,
    age INT,
    mutation_type VARCHAR(255),
    cancer_type VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Import data from mutationalData.csv file
LOAD DATA INFILE 'C:\Users\ayush\biom9450\main\BIOM9450\databases\mutationalData.csv' 
INTO TABLE Patients
FIELDS TERMINATED BY ',' 
LINES TERMINATED BY '\n' 
IGNORE 1 LINES
(
    patient_id, first_name, last_name, sex, age, mutation_type, cancer_type
);

-- Insert data into Patients table directly (from CSV structure)
INSERT INTO Patients (patient_id, first_name, last_name, sex, age, mutation_type, cancer_type) VALUES
('SP112909', 'Jake', 'Diggle', 'Male', 63, 'single base substitution', 'Brain'),
('SP192770', 'Wiggle', 'Niggle', 'Female', 29, 'single base substitution', 'Breast'),
('SP112909', 'Shiv', 'Mishra', 'Other', 50, 'insertion of <=200bp', 'Brain'),
('SP195936', 'Shashank', 'Mishra', 'Male', 70, 'single base substitution', 'Brain'),
('SP195938', 'Aviral', 'Vij', 'Male', 20, 'single base substitution', 'Breast'),
('SP195941', 'Asmita', 'Singhai', 'Female', 47, 'single base substitution', 'Blood'),
('SP195947', 'John', 'Baked', 'Male', 30, 'single base substitution', 'Prostate'),
('SP195961', 'Jenna', 'Ortega', 'Female', 28, 'single base substitution', 'Liver'),
('SP195965', 'Raki', 'Inder', 'Female', 48, 'single base substitution', 'Breast');

-- Insert sample oncologists into Users table
INSERT INTO Users (first_name, last_name, username, password_hash, role) VALUES
('John', 'Doe', 'jdoe_oncologist', SHA2('password123', 256), 'Oncologist'),
('Jane', 'Smith', 'jsmith_oncologist', SHA2('securepass', 256), 'Oncologist');

-- Insert sample researchers into Users table
INSERT INTO Users (first_name, last_name, username, password_hash, role) VALUES
('Emily', 'Brown', 'ebrown_researcher', SHA2('researcherpass', 256), 'Researcher'),
('Michael', 'Clark', 'mclark_researcher', SHA2('data4life', 256), 'Researcher');

-- Verify initial data
SELECT * FROM Users;
SELECT * FROM Patients;
