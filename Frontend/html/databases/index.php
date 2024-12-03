<?php
// Define the database file
define('DB_FILE', __DIR__ . '/../databases/cancer_scope.db');

// Connect to the SQLite database
function getDbConnection() {
    if (!file_exists(DB_FILE)) {
        die(json_encode(['error' => 'Database file not found: ' . DB_FILE]));
    }
    try {
        return new SQLite3(DB_FILE);
    } catch (Exception $e) {
        die(json_encode(['error' => 'Failed to connect to the database: ' . $e->getMessage()]));
    }
}

// Get HTTP request method
$method = $_SERVER['REQUEST_METHOD'] ?? null;

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'register':
            handleRegistration();
            break;
        case 'login':
            handleLogin();
            break;
        case 'fetch_patients':
            fetchPatients();
            break;
        case 'fetch_patient_details':
            fetchPatientDetails();
            break;
        case 'fetch_gene_mutations':
            fetchGeneMutations();
            break;
        case 'add_patient':
            addPatient();
            break;
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

// Handle user registration
function handleRegistration() {
    $conn = getDbConnection();

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $role = trim($_POST['role'] ?? 'other'); // Default to 'other' if not provided
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Input validation
    if (!$firstName || !$lastName || !$email || !$dob || !$gender || !$password || !$confirmPassword) {
        echo json_encode(['error' => 'All fields are required']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Invalid email format']);
        return;
    }

    if (!in_array($gender, ['male', 'female', 'other'])) {
        echo json_encode(['error' => 'Invalid gender']);
        return;
    }

    if (!in_array($role, ['oncologist', 'researcher', 'other'])) {
        echo json_encode(['error' => 'Invalid role']);
        return;
    }

    if ($password !== $confirmPassword) {
        echo json_encode(['error' => 'Passwords do not match']);
        return;
    }

    $passwordHash = hash('sha256', $password);

    // Insert user into the database
    $stmt = $conn->prepare("
        INSERT INTO Users (first_name, last_name, email, dob, gender, password_hash, role)
        VALUES (:firstName, :lastName, :email, :dob, :gender, :passwordHash, :role)
    ");
    $stmt->bindValue(':firstName', $firstName, SQLITE3_TEXT);
    $stmt->bindValue(':lastName', $lastName, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':dob', $dob, SQLITE3_TEXT);
    $stmt->bindValue(':gender', $gender, SQLITE3_TEXT);
    $stmt->bindValue(':passwordHash', $passwordHash, SQLITE3_TEXT);
    $stmt->bindValue(':role', $role, SQLITE3_TEXT);

    if ($stmt->execute()) {
        // Redirect based on role
        if ($role === 'oncologist') {
            header('Location: ../onco.html');
        } elseif ($role === 'researcher') {
            header('Location: ../rese.html');
        } else {
            header('Location: ../dashboard.html');
        }
        exit;
    } else {
        echo json_encode(['error' => 'Failed to register user']);
    }
}

// Handle user login
function handleLogin() {
    $conn = getDbConnection();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode(['error' => 'Email and password are required']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Invalid email format']);
        return;
    }

    // Fetch user from the database
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = :email");
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && hash('sha256', $password) === $user['password_hash']) {
        // Redirect based on role
        $role = $user['role'];
        if ($role === 'oncologist') {
            header('Location: ../onco.html');
        } elseif ($role === 'researcher') {
            header('Location: ../rese.html');
        } else {
            header('Location: ../dashboard.html');
        }
        exit;
    } else {
        echo json_encode(['error' => 'Invalid email or password']);
    }
}

// Fetch all patients (for Oncologist Dashboard)
function fetchPatients() {
    $conn = getDbConnection();

    $query = $_POST['query'] ?? ''; // Optional filter by chromosome, gene, etc.
    $stmt = $conn->prepare("
        SELECT * FROM Patients 
        WHERE chromosome LIKE :query 
           OR gene_affected LIKE :query
           OR cancer_type LIKE :query
    ");
    $stmt->bindValue(':query', '%' . $query . '%', SQLITE3_TEXT);
    $result = $stmt->execute();

    $patients = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $patients[] = $row;
    }
    echo json_encode($patients);
}

// Fetch detailed patient information (for Oncologist Dashboard)
function fetchPatientDetails() {
    $conn = getDbConnection();
    $patientId = trim($_POST['patient_id'] ?? '');

    if (!$patientId) {
        echo json_encode(['error' => 'Patient ID is required']);
        return;
    }

    $stmt = $conn->prepare("SELECT * FROM Patients WHERE patient_id = :patient_id");
    $stmt->bindValue(':patient_id', $patientId, SQLITE3_TEXT);
    $result = $stmt->execute();

    $patient = $result->fetchArray(SQLITE3_ASSOC);
    if ($patient) {
        echo json_encode($patient);
    } else {
        echo json_encode(['error' => 'Patient not found']);
    }
}

// Fetch gene mutation counts (for Researcher Dashboard)
function fetchGeneMutations() {
    $conn = getDbConnection();

    $stmt = $conn->prepare("
        SELECT gene_affected AS gene, COUNT(*) AS mutation_count
        FROM Patients
        GROUP BY gene_affected
        ORDER BY mutation_count DESC
    ");
    $result = $stmt->execute();

    $geneData = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $geneData[] = $row;
    }
    echo json_encode($geneData);
}

// Add a new patient (for Oncologist Dashboard)
function addPatient() {
    $conn = getDbConnection();

    // Capture and sanitize inputs
    $patientId = trim($_POST['patient_id'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $sex = trim($_POST['sex'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $mutationType = trim($_POST['mutation_type'] ?? '');
    $geneAffected = trim($_POST['gene_affected'] ?? '');
    $chromosome = trim($_POST['chromosome'] ?? '');
    $cancerType = trim($_POST['cancer_type'] ?? '');

    // Input validation
    if (!$patientId || !$firstName || !$lastName || !$sex || !$cancerType) {
        echo json_encode(['error' => 'Required fields are missing']);
        return;
    }

    if (!in_array($sex, ['Male', 'Female', 'Other'])) {
        echo json_encode(['error' => 'Invalid sex']);
        return;
    }

    // Prepare and execute insert query
    $stmt = $conn->prepare("
        INSERT INTO Patients (patient_id, first_name, last_name, sex, age, mutation_type, gene_affected, chromosome, cancer_type)
        VALUES (:patient_id, :first_name, :last_name, :sex, :age, :mutation_type, :gene_affected, :chromosome, :cancer_type)
    ");
    $stmt->bindValue(':patient_id', $patientId, SQLITE3_TEXT);
    $stmt->bindValue(':first_name', $firstName, SQLITE3_TEXT);
    $stmt->bindValue(':last_name', $lastName, SQLITE3_TEXT);
    $stmt->bindValue(':sex', $sex, SQLITE3_TEXT);
    $stmt->bindValue(':age', $age, SQLITE3_INTEGER);
    $stmt->bindValue(':mutation_type', $mutationType, SQLITE3_TEXT);
    $stmt->bindValue(':gene_affected', $geneAffected, SQLITE3_TEXT);
    $stmt->bindValue(':chromosome', $chromosome, SQLITE3_TEXT);
    $stmt->bindValue(':cancer_type', $cancerType, SQLITE3_TEXT);

    try {
        $stmt->execute();
        echo json_encode(['success' => 'Patient added successfully']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to add patient: ' . $e->getMessage()]);
    }
}
?>