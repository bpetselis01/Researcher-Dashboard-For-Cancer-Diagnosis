<?php
// Define the database file
define('DB_FILE', __DIR__ . '/../databases/cancer_scope.db');

// Connect to the SQLite database
function getDbConnection() {
    if (!file_exists(DB_FILE)) {
        die('Database file not found: ' . DB_FILE);
    }
    try {
        return new SQLite3(DB_FILE);
    } catch (Exception $e) {
        die('Failed to connect to the database: ' . $e->getMessage());
    }
}

// Ensure the request is POST
$method = $_SERVER['REQUEST_METHOD'] ?? null;
if ($method !== 'POST') {
    die(json_encode(['error' => 'Invalid request method']));
}

// Retrieve and sanitize POST data
$email = trim($_POST['email'] ?? '');
$dataset = trim($_POST['dataset'] ?? '');
$accuracy_rating = trim($_POST['accuracy_rating'] ?? '');
$comments = trim($_POST['comments'] ?? '');

// Validate required inputs
if (!$email || !$dataset || !$accuracy_rating) {
    die(json_encode(['error' => 'Email, dataset, and accuracy rating are required']));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(['error' => 'Invalid email format']));
}

// Connect to the database
$conn = getDbConnection();

try {
    // Fetch the user ID based on the provided email
    $stmt = $conn->prepare("SELECT id FROM Users WHERE email = :email");
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if (!$user) {
        die(json_encode(['error' => 'User not found']));
    }

    $user_id = $user['id'];

    // Insert feedback into the Feedback table
    $stmt = $conn->prepare("
        INSERT INTO Feedback (user_id, dataset, accuracy_rating, comments) 
        VALUES (:user_id, :dataset, :accuracy_rating, :comments)
    ");
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':dataset', $dataset, SQLITE3_TEXT);
    $stmt->bindValue(':accuracy_rating', $accuracy_rating, SQLITE3_TEXT);
    $stmt->bindValue(':comments', $comments, SQLITE3_TEXT);

    if ($stmt->execute()) {
        header("Location: ../dashboard.html");
        exit;
    } else {
        die(json_encode(['error' => 'Failed to submit feedback']));
    }
} catch (Exception $e) {
    die(json_encode(['error' => 'Database error: ' . $e->getMessage()]));
}
?>
