<?php
// Define the string to search for in file names
$searchString = 'ok';

// Get the list of files in the current directory
$files = scandir('.');

// Loop through each file
foreach ($files as $file) {
    // Skip directories and only target files
    if (is_file($file) && strpos($file, $searchString) !== false) {
        // Delete the file
        if (unlink($file)) {
            echo "Deleted: $file\n";
        } else {
            echo "Failed to delete: $file\n";
        }
    }
}
?>