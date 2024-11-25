-- Retrieve all users and their roles
SELECT user_id, first_name, last_name, username, role, created_at 
FROM Users;

-- Retrieve all patients and their information
SELECT patient_id, mutation_type, cancer_type, created_at 
FROM Patients;

-- Find all patients with a specific cancer type
CREATE PROCEDURE GetPatientsByCancerType(IN cancerType VARCHAR(100))
BEGIN
    SELECT patient_id, mutation_type, cancer_type 
    FROM Patients
    WHERE cancer_type = cancerType;
END;

-- Find all patients with a specific mutation type
CREATE PROCEDURE GetPatientsByMutationType(IN mutationType VARCHAR(255))
BEGIN
    SELECT patient_id, mutation_type, cancer_type 
    FROM Patients
    WHERE mutation_type = mutationType;
END;

-- Find patients who have both a specific mutation type and cancer type
CREATE PROCEDURE GetPatientsByMutationAndCancerType(IN mutationType VARCHAR(255), IN cancerType VARCHAR(100))
BEGIN
    SELECT patient_id, mutation_type, cancer_type 
    FROM Patients
    WHERE mutation_type = mutationType AND cancer_type = cancerType;
END;

-- Add a new user
CREATE PROCEDURE AddUser(
    IN firstName VARCHAR(50),
    IN lastName VARCHAR(50),
    IN username VARCHAR(50),
    IN password VARCHAR(255),
    IN role ENUM('Oncologist', 'Researcher')
)
BEGIN
    INSERT INTO Users (first_name, last_name, username, password_hash, role)
    VALUES (firstName, lastName, username, SHA2(password, 256), role);
END;

-- Add a new patient record
CREATE PROCEDURE AddPatient(
    IN specimenID VARCHAR(20),
    IN mutationType VARCHAR(255),
    IN cancerType VARCHAR(100)
)
BEGIN
    INSERT INTO Patients (patient_id, mutation_type, cancer_type)
    VALUES (specimenID, mutationType, cancerType);
END;

-- Get the count of patients per cancer type
CREATE PROCEDURE GetPatientCountByCancerType()
BEGIN
    SELECT cancer_type, COUNT(*) AS patient_count
    FROM Patients
    GROUP BY cancer_type
    ORDER BY patient_count DESC;
END;

-- Get the count of patients per mutation type
CREATE PROCEDURE GetPatientCountByMutationType()
BEGIN
    SELECT mutation_type, COUNT(*) AS patient_count
    FROM Patients
    GROUP BY mutation_type
    ORDER BY patient_count DESC;
END;

-- Update a patient's mutation type and cancer type
CREATE PROCEDURE UpdatePatientInfo(
    IN specimenID VARCHAR(20),
    IN newMutationType VARCHAR(255),
    IN newCancerType VARCHAR(100)
)
BEGIN
    UPDATE Patients
    SET mutation_type = newMutationType, cancer_type = newCancerType
    WHERE patient_id = specimenID;
END;

-- Delete a patient record by ID
CREATE PROCEDURE DeletePatient(IN specimenID VARCHAR(20))
BEGIN
    DELETE FROM Patients
    WHERE patient_id = specimenID;
END;

-- Verify users and patients
SELECT * FROM Users;
SELECT * FROM Patients;
