<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file was uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Retrieve the file name and temporary file path
        $fileName = $_FILES['file']['name'];
        $tempPath = $_FILES['file']['tmp_name'];

        // Define the target directory and file path
        $targetDir = "/";
        $targetFile = $targetDir . basename($fileName);

        // Ensure the uploads directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($tempPath, $targetFile)) {
            echo "File uploaded successfully: " . htmlspecialchars($fileName);
        } else {
            echo "Failed to move the uploaded file.";
        }
    } else {
        echo "No file uploaded or an error occurred.";
    }
} else {
    echo "Invalid request method. Please use POST.";
}
?>
