<?php
// Start the session if it's not already started
if (!session_id()) {
    session_start();
}

// Include the database connection file
require_once 'db.php';

// SQL query to fetch data from the timechecker table
$sql = "SELECT userid, Name, CheckTime, StatusText FROM timechecker ORDER BY CheckTime DESC";

try {
    $stmt = $db->query($sql);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timechecker Records</title>
</head>

<body>
    <h1>Timechecker Records</h1>

    <!-- Add Record Button -->
    <form action="add_record.php" method="get">
        <button type="submit">Add Record</button>
    </form>

    <!-- Records table -->
    <table border="1">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>CheckTime</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($records)) : ?>
                <?php foreach ($records as $record) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['userid']); ?></td>
                        <td><?php echo htmlspecialchars($record['Name']); ?></td>
                        <td><?php echo htmlspecialchars($record['CheckTime']); ?></td>
                        <td><?php echo htmlspecialchars($record['StatusText']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>