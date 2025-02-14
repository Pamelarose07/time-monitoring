<?php
// MySQLi connection setup
$host = 'localhost'; // Your MySQL host
$username = 'root'; // Your MySQL username
$password = ''; // Your MySQL password (empty for default)
$dbname = 'time_monitoring'; // Your database name

// Create connection
$connection = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($connection->connect_error) {
    die('Database connection failed: ' . $connection->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input to prevent SQL injection
    $userid = htmlspecialchars($_POST['userid']);
    $name = htmlspecialchars($_POST['Name']);
    $checkTime = htmlspecialchars($_POST['CheckTime']);
    $statusText = htmlspecialchars($_POST['StatusText']);
    $sensorid = htmlspecialchars($_POST['Sensorid']);

    // SQL query to insert data
    $sql = "INSERT INTO V_Record (Userid, Name, CheckTime, StatusText, Sensorid) 
            VALUES (?, ?, ?, ?, ?)";

    // Prepare the statement using MySQLi
    if ($stmt = $connection->prepare($sql)) {
        // Bind the parameters
        $stmt->bind_param("sssss", $userid, $name, $checkTime, $statusText, $sensorid);

        // Execute the query
        if ($stmt->execute()) {
            $message = "Record added successfully.";
            $message_type = "success";  // Added message type for success
        } else {
            $message = "Error adding record: " . $stmt->error;
            $message_type = "error"; // Added message type for error
        }

        // Close the statement
        $stmt->close();
    } else {
        $message = "Error preparing query: " . $connection->error;
        $message_type = "error"; // Added message type for error
    }
}

// Close the connection
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }

        input {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4cae4c;
        }

        .form-container p {
            text-align: center;
            font-size: 14px;
            color: red;
        }

        .message {
            text-align: center;
            font-size: 16px;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            transition: opacity 1s ease-out;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .hidden {
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Add New Record</h1>
        <form action="" method="post">
            <!-- User ID -->
            <label for="userid">User ID:</label>
            <input type="text" name="userid" id="userid" required><br>

            <!-- Name -->
            <label for="Name">Name:</label>
            <input type="text" name="Name" id="Name" required><br>

            <!-- CheckTime -->
            <label for="CheckTime">Check Time:</label>
            <input type="datetime-local" name="CheckTime" id="CheckTime" required><br>

            <!-- StatusText -->
            <label for="StatusText">Status:</label>
            <input type="text" name="StatusText" id="StatusText" required><br>

            <!-- Sensor ID -->
            <label for="Sensorid">Device #:</label>
            <input type="text" name="Sensorid" id="Sensorid" required><br>

            <button type="submit">Add Record</button>
        </form>

        <?php if (isset($message)) : ?>
            <div class="message <?php echo $message_type; ?>" id="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>

    <script>
        // Fading out the message after 3 seconds
        window.onload = function() {
            const messageElement = document.getElementById('message');
            if (messageElement) {
                setTimeout(function() {
                    messageElement.classList.add('hidden');
                }, 3000); // Message fades after 3 seconds
            }
        };
    </script>
</body>

</html>