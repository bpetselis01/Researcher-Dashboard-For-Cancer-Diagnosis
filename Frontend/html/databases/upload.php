<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = __DIR__ . '/uploads/';

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Save uploaded files
    foreach ($_FILES['dataFolder']['name'] as $index => $fileName) {
        $tempPath = $_FILES['dataFolder']['tmp_name'][$index];
        $destinationPath = $uploadDir . $fileName;
        move_uploaded_file($tempPath, $destinationPath);
    }

    // Collect parameters from the form
    $dataset = htmlspecialchars($_POST['dataset']); // BRCA or ROSMAP
    $trainSamples = intval($_POST['trainSamples']); // Number of training samples
    $testSamples = intval($_POST['testSamples']); // Number of testing samples
    $viewList = isset($_POST['viewList']) ? implode(",", $_POST['viewList']) : "1,2,3"; // Default to all views if none selected

    // Build the command for the Python script
    $command = escapeshellcmd("python3 main_mogonet.py $uploadDir \"$viewList\" $trainSamples $testSamples $dataset");

    // Run the Python script and capture its output
    $output = shell_exec($command);

    // Display results
    echo nl2br($output); // Convert newlines in output to <br> tags for HTML display
}
?>