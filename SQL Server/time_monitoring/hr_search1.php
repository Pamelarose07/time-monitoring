<?php
session_start();
// Include the connection file
include('db.php');

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchFilter = isset($_POST['search']) ? $_POST['search'] : '';
    $fromDateFilter = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';

    // Prepare the SQL query
    $sql = "SELECT * FROM TimeChecker WHERE 1=1";

    // Add filters if provided
    if (!empty($searchFilter)) {
        $sql .= " AND (Name LIKE ? OR Userid = ?)";
    }
    if (!empty($fromDateFilter)) {
        $sql .= " AND CheckTime >= ?";
    }
    if (!empty($to_date)) {
        // Adjust the condition for Microsoft Access SQL
        // Use DateAdd function to add 1 day to the to_date
        $sql .= " AND CheckTime < DATEADD(day, 1, ?)";
    }

    // Prepare the statement using odbc_prepare
    $stmt = odbc_prepare($connection, $sql);

    // Bind parameters if filters are provided
    $params = [];
    if (!empty($searchFilter)) {
        $inputSearch = '%' . $searchFilter . '%'; // Wildcard for Name
        $params[] = $inputSearch; // For Name LIKE
        $params[] = $searchFilter; // For Userid
    }
    if (!empty($fromDateFilter)) {
        $formattedFromDate = date('Y-m-d', strtotime($fromDateFilter));
        $params[] = $formattedFromDate;
    }
    if (!empty($to_date)) {
        $formattedToDate = date('Y-m-d', strtotime($to_date));
        $params[] = $formattedToDate;
    }

    // Execute the query
    if (odbc_execute($stmt, $params)) {
        // Fetch all results
        $result = [];
        while ($row = odbc_fetch_array($stmt)) {
            $result[] = $row;
        }
    } else {
        echo "Query failed: " . odbc_errormsg($connection);
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
    <!-- CDN for Tailwind CSS design -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
    .icon-color {
        fill: black;
        /* Change this to the color you desire */
    }
</style>

<body>
    <div>

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

    </div>

    <!-- Search Input Fields -->
    <form class="ml-12 p-5 mt-3" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <label for="search">Name or Employee ID:</label>
<input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/6 p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" id="search" name="search" value="<?php echo isset($searchFilter) ? htmlspecialchars($searchFilter) : ''; ?>" autocomplete="off">

        <div class="flex flex-col">
            <label for="from_date">From:</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/6  p-1.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="date" id="from_date" name="from_date" value="<?php echo isset($fromDateFilter) ? htmlspecialchars($fromDateFilter) : ''; ?>">
        </div>
        <div class="flex flex-col">
            <label for="to_date">To:</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/6  p-1.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>">
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
        <!-- Add Clear button -->
        <button type="button" class="mt-3 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" id="clearButton">Clear</button>
    </form>

    <script>
        document.getElementById('clearButton').addEventListener('click', function() {
            // Clear all input fields
            document.getElementById('name').value = '';
            document.getElementById('from_date').value = '';
            document.getElementById('to_date').value = '';

            // Remove table contents
            const tableBody = document.querySelector('tbody');
            tableBody.innerHTML = '';
        });

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
                    autocompleteList.innerHTML = '';
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
                autocompleteList.innerHTML = '';
                suggestions.forEach(suggestion => {
                    const option = document.createElement('option');
                    option.value = suggestion;
                    autocompleteList.appendChild(option);
                });
            }
        });
    </script>

    <!-- Table List -->
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($result)) : ?>
        <table width="100%" border="1" style="border-collapse:collapse;">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400 grid-cols-2">
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
                    <tr class="bg-white border-b  dark:border-gray-700 font-semibold">
                        <td class="text-center px-6 py-4">
                            <?php echo $row['Name']; ?>
                        </td>
                        <td class="text-center px-6 py-4">
                            <?php $dateTime = separateDateTime($row['CheckTime']);
                            echo $dateTime['date']; ?>
                        </td>
                        <td class="text-center px-6 py-4">
                            <?php echo $dateTime['time']; ?>
                        </td>
                        <td class="text-center px-6 py-4">
                            <?php echo $row['StatusText']; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>

</html>
