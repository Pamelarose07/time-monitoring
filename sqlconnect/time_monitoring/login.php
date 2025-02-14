<?php
include('db.php'); // Include the database connection file

session_start();

// Initialize error variable
$error = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input and sanitize
    $user = trim($_POST['user']);
    $pass = trim($_POST['pass']);

    // Validate user input
    if (!empty($user) && !empty($pass)) {
        // Prepare SQL statement with placeholders to prevent SQL injection
        $sql = "SELECT * FROM dbo.tbluser WHERE username = ? AND pass = ?";
        $stmt = odbc_prepare($connection, $sql);

        if ($stmt) {
            // Bind parameters and execute statement
            $result = odbc_execute($stmt, array($user, $pass));

            if ($result) {
                // Fetch results
                $userData = odbc_fetch_array($stmt);

                if ($userData) {
                    // Store user data in session
                    $_SESSION['user_id'] = $userData['userid'];
                    $_SESSION['username'] = $userData['username'];

                    // Redirect to a protected page
                    header("Location: hr_home.php");
                    exit();
                } else {
                    // Show error when no matching user found
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Failed to execute SQL statement.";
            }
        } else {
            $error = "Failed to prepare SQL statement.";
        }
    } else {
        $error = "Please enter both username and password.";
    }
}

// Close database connection
odbc_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="user">User:</label>
        <input type="text" id="user" name="user" required>
        <br>
        <label for="pass">Pass:</label>
        <input type="password" id="pass" name="pass" required>
        <br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
