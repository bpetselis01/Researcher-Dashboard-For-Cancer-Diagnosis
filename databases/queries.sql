-- Fetch basic details of all patients
DROP PROCEDURE IF EXISTS GetAllPatients //
CREATE PROCEDURE GetAllPatients()
BEGIN
    SELECT 
        p.patient_id AS PatientID,
        CONCAT(p.first_name, ' ', p.last_name) AS FullName,
        p.sex AS Gender,
        p.email AS Email,
        p.phone AS PhoneNumber,
        p.country AS Country,
        p.age AS Age
    FROM 
        Patients p
    ORDER BY 
        p.last_name;
END //

-- Fetch detailed information for a patient by name
DROP PROCEDURE IF EXISTS GetPatientDetailsByName //
CREATE PROCEDURE GetPatientDetailsByName(IN searchName VARCHAR(255))
BEGIN
    SELECT 
        p.patient_id AS PatientID,
        CONCAT(p.first_name, ' ', p.last_name) AS FullName,
        p.sex AS Gender,
        p.email AS Email,
        p.phone AS PhoneNumber,
        p.country AS Country,
        p.cancer_type AS CancerType,
        p.age AS Age,
        p.treatment AS TreatmentPlan,
        p.mutational_profile AS MutationalProfile
    FROM 
        Patients p
    WHERE 
        LOWER(CONCAT(p.first_name, ' ', p.last_name)) LIKE LOWER(CONCAT('%', searchName, '%'));
END //

-- Fetch mutational profiles for all patients
DROP PROCEDURE IF EXISTS GetMutationalProfiles //
CREATE PROCEDURE GetMutationalProfiles()
BEGIN
    SELECT 
        p.patient_id AS PatientID,
        CONCAT(p.first_name, ' ', p.last_name) AS FullName,
        p.mutational_profile AS MutationalProfile
    FROM 
        Patients p
    WHERE 
        p.mutational_profile IS NOT NULL;
END //

-- Insert a new oncologist
DROP PROCEDURE IF EXISTS InsertOncologist //
CREATE PROCEDURE InsertOncologist(
    IN firstName VARCHAR(50),
    IN lastName VARCHAR(50),
    IN username VARCHAR(50),
    IN passwordHash VARCHAR(255)
)
BEGIN
    INSERT INTO Users (
        first_name,
        last_name,
        username,
        password_hash,
        role
    ) VALUES (
        firstName,
        lastName,
        username,
        passwordHash,
        'Oncologist'
    );
END //

-- Insert a new researcher
DROP PROCEDURE IF EXISTS InsertResearcher //
CREATE PROCEDURE InsertResearcher(
    IN firstName VARCHAR(50),
    IN lastName VARCHAR(50),
    IN username VARCHAR(50),
    IN passwordHash VARCHAR(255)
)
BEGIN
    INSERT INTO Users (
        first_name,
        last_name,
        username,
        password_hash,
        role
    ) VALUES (
        firstName,
        lastName,
        username,
        passwordHash,
        'Researcher'
    );
END //

-- Insert a new patient
DROP PROCEDURE IF EXISTS InsertPatient //
CREATE PROCEDURE InsertPatient(
    IN firstName VARCHAR(50),
    IN lastName VARCHAR(50),
    IN sex ENUM('Male', 'Female', 'Other'),
    IN email VARCHAR(100),
    IN phone VARCHAR(15),
    IN country VARCHAR(50),
    IN age INT,
    IN cancerType VARCHAR(100),
    IN treatment TEXT,
    IN mutationalProfile TEXT
)
BEGIN
    INSERT INTO Patients (
        first_name,
        last_name,
        sex,
        email,
        phone,
        country,
        age,
        cancer_type,
        treatment,
        mutational_profile
    ) VALUES (
        firstName,
        lastName,
        sex,
        email,
        phone,
        country,
        age,
        cancerType,
        treatment,
        mutationalProfile
    );
END //

-- Fetch cancer type statistics
DROP PROCEDURE IF EXISTS GetCancerTypeStats //
CREATE PROCEDURE GetCancerTypeStats()
BEGIN
    SELECT 
        p.cancer_type AS CancerType,
        COUNT(*) AS PatientCount
    FROM 
        Patients p
    WHERE 
        p.cancer_type IS NOT NULL
    GROUP BY 
        p.cancer_type
    ORDER BY 
        PatientCount DESC;
END //

-- Fetch patients filtered by age range
DROP PROCEDURE IF EXISTS GetPatientsByAgeRange //
CREATE PROCEDURE GetPatientsByAgeRange(IN minAge INT, IN maxAge INT)
BEGIN
    SELECT 
        p.patient_id AS PatientID,
        CONCAT(p.first_name, ' ', p.last_name) AS FullName,
        p.sex AS Gender,
        p.age AS Age,
        p.cancer_type AS CancerType
    FROM 
        Patients p
    WHERE 
        p.age BETWEEN minAge AND maxAge;
END //
