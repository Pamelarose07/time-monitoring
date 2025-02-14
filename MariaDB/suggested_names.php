<?php
// Include the connection file (ODBC connection should be established in db.php)
include('db.php');

// Retrieve the 'input' parameter from the GET request
$input = isset($_GET['input']) ? $_GET['input'] : '';

// Prepare an array to store suggested names
$suggestedNames = [];

// Ensure input is not empty
if (!empty($input)) {
    try {
        // Prepare the SQL query to search for names starting with the input
        $sql = "SELECT DISTINCT Name FROM v_record WHERE Name LIKE ?";

        // Prepare the statement using ODBC
        $stmt = odbc_prepare($connection, $sql);

        // Add wildcard to the input for the LIKE operator
        $inputParam = '%' . $input . '%';

        // Execute the query with the input parameter
        odbc_execute($stmt, [$inputParam]);

        // Fetch all the matching names as an array
        while ($row = odbc_fetch_array($stmt)) {
            $suggestedNames[] = $row['Name'];
        }

        // Return the suggestions as a JSON response
        header('Content-Type: application/json');
        echo json_encode($suggestedNames);
    } catch (Exception $e) {
        // Handle errors and return the error message as a JSON response
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // If input is empty, return an empty array as JSON
    header('Content-Type: application/json');
    echo json_encode([]);
}
