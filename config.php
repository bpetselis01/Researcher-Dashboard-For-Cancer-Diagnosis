<?php
$host = 'localhost';
$port = 5500;

// Set the document root
$docRoot = __DIR__ . '/Frontend/html';
$def = "$docRoot/index.html";

// Debug output to verify paths
echo "Document Root: $docRoot\n";
echo "Default Page: $def\n";

// Check if the document root exists
if (!is_dir($docRoot)) {
    die("Error: Document root directory '$docRoot' not found.\n");
}

// Check if the default page exists
if (!file_exists($def)) {
    die("Error: Default page '$def' not found.\n");
}

// Database setup
$dbDir = __DIR__ . '/Frontend/html/databases'; // Database directory
$dbFile = $dbDir . '/cancer_scope.db'; // Database file
$sqlFile = $dbDir . '/data.sql'; // SQL script file
initializeDatabase($dbFile, $sqlFile);

// Start the server
echo "Starting PHP server at http://$host:$port/index.html\n";
$command = "php -S $host:$port -t $docRoot";
system($command);

/**
 * Initialize the database.
 * Creates the database and populates it using an SQL script.
 */
function initializeDatabase($dbFile, $sqlFile) {
    $dbDir = dirname($dbFile);

    // Ensure the database directory exists
    if (!is_dir($dbDir)) {
        echo "Database directory '$dbDir' not found. Creating it...\n";
        mkdir($dbDir, 0777, true);
    }

    if (!file_exists($dbFile)) {
        echo "Database file not found. Creating a new database...\n";

        try {
            $db = new SQLite3($dbFile);

            // Read the SQL file
            if (!file_exists($sqlFile)) {
                die("SQL script file not found: $sqlFile\n");
            }
            $sql = file_get_contents($sqlFile);

            // Execute the SQL commands
            $db->exec($sql);

            echo "Database initialized successfully using $sqlFile.\n";
        } catch (Exception $e) {
            die("Failed to initialize database: " . $e->getMessage() . "\n");
        }
    } else {
        echo "Database file already exists. Skipping creation.\n";
    }
}
?>