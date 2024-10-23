<?php
$connection = mysqli_connect('phpdbproject.mysql.database.azure.com', 'manishankarkudipudi', '');
if (!$connection){
    die("Database Connection Failed" . mysqli_error($connection));
}
$select_db = mysqli_select_db($connection, 'aroma');
if (!$select_db){
    die("Database Selection Failed" . mysqli_error($connection));
}
