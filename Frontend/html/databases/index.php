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

    
    if ($role === 'oncologist') {
        header('Location: ../oncologist_dashboard.html');
    } elseif ($role === 'researcher') {
        header('Location: ../researcher_dashboard.html');
    } elseif ($role === 'other') {
        header('Location: ../dashboard.html');
    } else {
        echo json_encode(['error' => 'Failed to register user']);
    }
    exit;
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
            header('Location: ../oncologist_dashboard.html');
        } elseif ($role === 'researcher') {
            header('Location: ../researcher_dashboard.html');
        } else {
            header('Location: ../dashboard.html');
        }
        exit;
    } else {
        echo json_encode(['error' => 'Invalid email or password']);
    }
}

// Fetch patients
function fetchPatients() {
    $conn = getDbConnection();
    $query = trim($_POST['query'] ?? '');
    $stmt = $conn->prepare("
        SELECT * FROM Patients 
        WHERE chromosome LIKE :query OR gene LIKE :query
    ");
    $stmt->bindValue(':query', '%' . $query . '%', SQLITE3_TEXT);
    $result = $stmt->execute();

    $patients = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $patients[] = $row;
    }
    echo json_encode($patients);
}

// Fetch patient details
function fetchPatientDetails() {
    $conn = getDbConnection();
    $patientId = intval($_POST['patient_id'] ?? 0);

    $stmt = $conn->prepare("SELECT * FROM Patients WHERE patient_id = :id");
    $stmt->bindValue(':id', $patientId, SQLITE3_INTEGER);
    $result = $stmt->execute();

    $patient = $result->fetchArray(SQLITE3_ASSOC);
    echo json_encode($patient);
}

// Fetch gene mutation counts
function fetchGeneMutations() {
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        SELECT gene, COUNT(*) as mutation_count 
        FROM Mutations 
        GROUP BY gene
    ");
    $result = $stmt->execute();

    $geneData = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $geneData['genes'][] = $row['gene'];
        $geneData['mutationCounts'][] = $row['mutation_count'];
    }
    echo json_encode($geneData);
}
?>