<?php
// Include the connection file
include('db.php');

header('Content-Type: text/html; charset=utf-8');

// Query to select the most recent 11 records
$sql = "SELECT TOP 9 * FROM TimeChecker ORDER BY CheckTime DESC";
$stmt = odbc_exec($connection, $sql);

// Check for query execution error
if (!$stmt) {
    die("Query failed: " . odbc_errormsg($connection));
}

// Fetch all records
$result = [];
while ($row = odbc_fetch_array($stmt)) {
    $result[] = $row;
}

// Function to separate date and time components and convert time to 12-hour format without seconds
function separateDateTime($dateTime)
{
    $date = date('Y-m-d', strtotime($dateTime));
    $time = date('h:i A', strtotime($dateTime)); // Changed 'h:i:s A' to 'h:i A' to remove seconds
    return ['date' => $date, 'time' => $time];
}

// Ensure only 11 records are displayed
$result = array_slice($result, 0, 11);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoodTaste</title>
    <!-- CDN for Tailwind CSS design -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <!-- Navbar -->
    <meta http-equiv="refresh" content="1">
    <nav class="flex items-center justify-between bg-red-500 border-gray-200 dark:bg-gray-900 p-2.5">
        <div id="current-time" class="text-white text-4xl font-bold serif"></div>
        <h1 class="text-white text-4xl font-bold serif text-center">Daily Time Monitoring</h1>
        <img src="1.jpg" alt="" class="h-12 w-18">
    </nav>

    <table width="100%" border="1" style="border-collapse:collapse;">
        <thead class=" text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Name
                </th>
                <th scope="col" class="px-6 py-3">
                    Date
                </th>
                <th scope="col" class="px-6 py-3">
                    Time
                </th>
                <th scope="col" class="px-6 py-3">
                    Status
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row) { ?>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 font-bold">
                    <td class="text-center text-3xl px-6 py-4">
                    <?php echo htmlspecialchars($row['Name'] = utf8_encode($row['Name']));
 ?> 
                    </td>
                    <td class="text-center text-3xl px-6 py-4">
                        <?php $dateTime = separateDateTime($row['CheckTime']);
                        echo htmlspecialchars($dateTime['date']); ?>
                    </td>
                    <td class="text-center text-3xl px-6 py-4">
                        <?php echo htmlspecialchars($dateTime['time']); ?>
                    </td>
                    <td class="text-center text-3xl px-6 py-4">
                        <?php echo htmlspecialchars($row['StatusText']); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <script>
        // JavaScript to display current time
        function updateTime() {
            var currentTime = new Date();
            var hours = currentTime.getHours();
            var minutes = currentTime.getMinutes();

            // Add leading zeros if necessary
            minutes = (minutes < 10 ? "0" : "") + minutes;

            // Determine AM/PM
            var meridiem = (hours < 12 ? "AM" : "PM");

            // Convert to 12-hour format
            hours = (hours > 12) ? hours - 12 : hours;
            hours = (hours === 0) ? 12 : hours;

            // Construct the time string
            var timeString = hours + ":" + minutes + " " + meridiem;

            // Update the time in the HTML element
            document.getElementById('current-time').textContent = timeString;
        }

        // Call updateTime every second to keep the time updated
        setInterval(updateTime, 1000);

        // Call updateTime once initially to display the time immediately
        updateTime();
    </script>
</body>

</html>
