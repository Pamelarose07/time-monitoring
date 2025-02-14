<?php
// Start the session if it's not already started
if (!session_id()) {
    session_start();
}

// Include the ODBC connection file
require_once 'db.php';

// SQL query to fetch users from the database
$sql = "SELECT userid, FName, LName FROM dbo.tbluser";

// Execute the query using ODBC
$query = odbc_exec($connection, $sql);

// Check for query errors
if (!$query) {
    die("Query failed: " . odbc_errormsg($connection));
}

// Fetch all users into an array
$users = [];
while ($row = odbc_fetch_array($query)) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
</head>

<body>
    <h1>User List</h1>

    <!-- Add User Button -->
    <form action="add_user.php" method="get">
        <button type="submit">Add User</button>
    </form>

    <!-- User table -->
    <table border="1">
        <thead>
            <tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['userid']); ?></td>
                        <td><?php echo htmlspecialchars($user['FName']); ?></td>
                        <td><?php echo htmlspecialchars($user['LName']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>
