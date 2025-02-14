<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
</head>

<body>
    <h1>Add New User</h1>
    <form action="insert_user.php" method="post">
        <label for="FName">First Name:</label>
        <input type="text" name="FName" id="FName" required><br>

        <label for="LName">Last Name:</label>
        <input type="text" name="LName" id="LName" required><br>

        <button type="submit">Add User</button>
    </form>
</body>

</html>