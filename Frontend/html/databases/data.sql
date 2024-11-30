-- database: cancer_scope.db
DROP TABLE IF EXISTS Users;
-- Create the Users table
-- Create Users table with role field
CREATE TABLE Users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    dob TEXT NOT NULL,
    gender TEXT CHECK(gender IN ('male', 'female', 'other')) NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT CHECK(role IN ('oncologist', 'researcher', 'other')) DEFAULT 'other' NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Drop and recreate Patients table
DROP TABLE IF EXISTS Patients;
-- Create Patients table
CREATE TABLE Patients (
    patient_id TEXT UNIQUE NOT NULL,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    sex TEXT CHECK(sex IN ('Male', 'Female', 'Other')) NOT NULL,
    age INTEGER,
    mutation_type TEXT,
    gene_affected TEXT,
    chromosome TEXT,
    cancer_type TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- feedback sql
-- Drop and recreate Feedback table
DROP TABLE IF EXISTS Feedback;
CREATE TABLE Feedback (
    feedback_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL, -- Link to the Users table
    dataset TEXT NOT NULL CHECK(dataset IN ('BRCA', 'ROSMAP', 'GENOME', 'EXOME')),
    accuracy_rating TEXT NOT NULL CHECK(accuracy_rating IN ('accurate', 'inaccurate')),
    comments TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Insert data into Patients table directly (from CSV structure)
INSERT INTO Patients (patient_id, first_name, last_name, sex, age, mutation_type, gene_affected, chromosome, cancer_type) VALUES
('SP112909', 'Jake', 'Diggle', 'Male', 63, 'single base substitution', 'ENSG00000142623', '1', 'Brain'),
('SP192770', 'Wiggle', 'Niggle', 'Female', 29, 'single base substitution', 'ENSG00000159023', '1', 'Breast'),
('SP195936', 'Shashank', 'Mishra', 'Male', 70, 'single base substitution', 'ENSG00000062485', '2', 'Brain'),
('SP195938', 'Aviral', 'Vij', 'Male', 20, 'single base substitution', 'ENSG00000145242', '2', 'Breast'),
('SP195941', 'Asmita', 'Singhai', 'Female', 47, 'single base substitution', 'ENSG00000164176', '5', 'Blood'),
('SP195947', 'John', 'Baked', 'Male', 30, 'single base substitution', 'ENSG00000248973', '4', 'Prostate'),
('SP195961', 'Jenna', 'Ortega', 'Female', 28, 'single base substitution', 'ENSG00000144331', '10', 'Liver'),
('SP195965', 'Raki', 'Inder', 'Female', 48, 'single base substitution', 'ENSG00000270163', '20', 'Breast');

INSERT INTO Users (first_name, last_name, email, dob, gender, password_hash, role) VALUES
('John', 'Doe', 'jdoe_oncologist@example.com', '1980-01-01', 'male', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd4b24458dcf8c9ab35', 'oncologist'), -- Password: password
('Jane', 'Smith', 'jsmith_researcher@example.com', '1985-02-02', 'female', '6a884898da28047151d0e56f8dc6292773603d0d6aabbdd4b24458dcf8c9ab35', 'researcher'), -- Password: securepass
('Emily', 'Brown', 'ebrown_other@example.com', '1990-03-03', 'female', '7b884898da28047151d0e56f8dc6292773603d0d6aabbdd4b24458dcf8c9ab35', 'other'); -- Password: researcherpass

-- Verify initial data
SELECT * FROM Users;
SELECT * FROM Patients;