<?php
session_start();

// Include the connection file
include('db.php');

// Initialize variables
$nameFilter = $fromDateFilter = $toDateFilter = "";
$result = [];
$errorMsg = ""; // Variable to store error messages

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if the 'reset' button was clicked
    if (isset($_POST['reset'])) {
        $nameFilter = $fromDateFilter = $toDateFilter = "";  // Clear the filters
        header("Location: " . $_SERVER['PHP_SELF']);  // Redirect to refresh the page
        exit;  // Exit to stop further processing
    }

    // Get the Userid from the session
    $userid = isset($_SESSION['Userid']) ? $_SESSION['Userid'] : null;

    $nameFilter = isset($_POST['name']) ? $_POST['name'] : '';
    $fromDateFilter = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $toDateFilter = isset($_POST['to_date']) ? $_POST['to_date'] : '';

    // Validate the date format (if needed)
    if (!empty($fromDateFilter) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDateFilter)) {
        $errorMsg = "Invalid From Date format. Please use YYYY-MM-DD.";
    }
    if (!empty($toDateFilter) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDateFilter)) {
        $errorMsg = "Invalid To Date format. Please use YYYY-MM-DD.";
    }

    // If there's an error in date format, stop processing
    if (!empty($errorMsg)) {
        echo $errorMsg; // Output error message
        exit;  // Exit script to prevent further processing with invalid date
    }

    // Prepare the base SQL query
    $sql = "SELECT * FROM v_record WHERE 1=1";

    // Add filters if provided
    if (!empty($nameFilter)) {
        $sql .= " AND Name LIKE ?";
    }
    if (!empty($fromDateFilter)) {
        // Use DATE() to compare only the date part of the DATETIME field
        $sql .= " AND DATE(CheckTime) >= ?";
    }
    if (!empty($toDateFilter)) {
        // Use DATE() to compare only the date part of the DATETIME field
        $sql .= " AND DATE(CheckTime) <= ?";
    }
    if (!empty($userid)) {
        $sql .= " AND Userid = ?";
    }

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Check if parameters are being added
        if (!empty($nameFilter) || !empty($fromDateFilter) || !empty($toDateFilter) || !empty($userid)) {
            // Adjust binding based on the filters
            $paramTypes = '';
            $params = [];

            if (!empty($nameFilter)) {
                $paramTypes .= 's';  // 's' for string
                $params[] = '%' . $nameFilter . '%';
            }
            if (!empty($fromDateFilter)) {
                $paramTypes .= 's';  // 's' for string (date is treated as string)
                $params[] = $fromDateFilter;
            }
            if (!empty($toDateFilter)) {
                $paramTypes .= 's';  // 's' for string (date is treated as string)
                $params[] = $toDateFilter;
            }
            if (!empty($userid)) {
                $paramTypes .= 's';  // 's' for string
                $params[] = $userid;
            }

            // Bind parameters if any filter is applied
            if (!empty($paramTypes)) {
                $stmt->bind_param($paramTypes, ...$params);
            }
        }

        // Execute the query
        if ($stmt->execute()) {
            // Get the result
            $result = $stmt->get_result();
            $records = [];
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
            $stmt->close();
        } else {
            // Log any errors that occur during execution
            echo "Error executing query: " . $stmt->error;
        }
    } else {
        // Log error if statement preparation fails
        echo "Error preparing query: " . $conn->error;
    }
}

// Function to separate date and time components and convert time to 12-hour format with AM/PM
function separateDateTime($dateTime)
{
    $date = date('Y-m-d', strtotime($dateTime));
    $time = date('h:i A', strtotime($dateTime)); // Changed to 12-hour format with AM/PM
    return ['date' => $date, 'time' => $time];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoodTaste</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .icon-color {
            fill: black;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="bg-red-500 border-gray-200 dark:bg-gray-900">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-8">
            <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg  md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0  dark:border-gray-700">
                    <li>
                        <a href="hr_home.php" title="Home" class="block py-2 px-8 text-white bg-blue-700 rounded md:bg-transparent md:text-white md:p-0 dark:text-white md:dark:text-blue-500 hover:text-white hover:scale-150">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="hrr.php" title="Search" class="block py-2 px-8 text-white bg-blue-700 rounded md:bg-transparent md:text-white md:p-0 dark:text-white md:dark:text-blue-500 hover:text-white hover:scale-150">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
            <h1 class="text-white text-4xl font-bold serif text-center">Daily Time Monitoring</h1>
            <div class="flex items-center justify-between">
                <img src="1.jpg" alt="" class="h-12 w-18">
            </div>
        </div>
    </nav>

    <!-- Search Input Fields -->
    <form class="ml-12 p-5 mt-3" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="name">Name:</label>
        <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1.2 p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" id="name" name="name" value="<?php echo isset($nameFilter) ? htmlspecialchars($nameFilter) : ''; ?>" autocomplete="off">

        <div class="flex flex-col">
            <label for="from_date">From:</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/6  p-1.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="date" id="from_date" name="from_date" value="<?php echo isset($fromDateFilter) ? htmlspecialchars($fromDateFilter) : ''; ?>">

            <label for="to_date">To:</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/6 p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="date" id="to_date" name="to_date" value="<?php echo isset($toDateFilter) ? htmlspecialchars($toDateFilter) : ''; ?>">
        </div>

        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md mt-4">Filter</button>
        <button type="button" class="bg-gray-500 text-white px-6 py-2 rounded-md mt-4 ml-4" onclick="clearFilters()">Clear</button>
    </form>
    </form>

    <!-- Table for Results -->
    <div class="overflow-x-auto w-full">
        <table class="min-w-full bg-white text-sm text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3 text-center">Userid</th>
                    <th class="px-6 py-3 text-center">Name</th>
                    <th class="px-6 py-3 text-center">Date</th>
                    <th class="px-6 py-3 text-center">Time</th>
                    <th class="px-6 py-3 text-center">Status</th>
                    <th class="px-6 py-3 text-center">Device #</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($records)) {
                    foreach ($records as $row) {
                        $dateTime = separateDateTime($row['CheckTime']);
                        echo "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 font-bold text-black'>";
                        echo "<td class='text-center px-6 py-4'>" . htmlspecialchars($row['Userid']) . "</td>";
                        echo "<td class='text-center px-6 py-4'>" . htmlspecialchars($row['Name']) . "</td>";
                        echo "<td class='text-center px-6 py-4'>" . htmlspecialchars($dateTime['date']) . "</td>";
                        echo "<td class='text-center px-6 py-4'>" . htmlspecialchars($dateTime['time']) . "</td>";
                        echo "<td class='text-center px-6 py-4'>" . htmlspecialchars($row['StatusText']) . "</td>";
                        echo "<td class='text-center px-6 py-4'>" . htmlspecialchars($row['Sensorid']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center px-6 py-4'>No results found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
<script>
    function clearFilters() {
        // Clear the filter fields
        document.getElementById('name').value = '';
        document.getElementById('from_date').value = '';
        document.getElementById('to_date').value = '';

        // Reload the page to clear the PHP filters as well
        window.location.href = window.location.href.split('?')[0];
    }

    document.addEventListener("DOMContentLoaded", function() {
        const nameInput = document.getElementById('name');
        const autocompleteList = document.createElement('datalist');
        autocompleteList.id = 'nameSuggestions';
        nameInput.setAttribute('list', autocompleteList.id);
        nameInput.insertAdjacentElement('afterend', autocompleteList);

        nameInput.addEventListener('input', function() {
            const inputValue = this.value;
            if (inputValue.length >= 2) {
                fetchSuggestions(inputValue);
            } else {
                autocompleteList.innerHTML = ''; // Clear suggestions if input is short
            }
        });

        async function fetchSuggestions(input) {
            try {
                const response = await fetch('suggested_names.php?input=' + input);
                const data = await response.json();
                populateSuggestions(data);
            } catch (error) {
                console.error('Error fetching suggestions:', error);
            }
        }

        function populateSuggestions(suggestions) {
            autocompleteList.innerHTML = ''; // Clear previous suggestions
            suggestions.forEach(suggestion => {
                const option = document.createElement('option');
                option.value = suggestion;
                autocompleteList.appendChild(option);
            });
        }
    });
</script>

</html>