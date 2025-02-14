<?php


// Replace 'Your_DSN_Name' with the name you chose for your DSN
$dsn = 'SQL Driver';
$username = 'gt_access';
$password = 'G00dt@st3';

$connection = odbc_connect($dsn, $username, $password);

if (!$connection) {
    die("Connection failed: " . odbc_errormsg());
}
?>
