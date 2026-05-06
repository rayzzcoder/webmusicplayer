<?php
include 'db.php';
header('Content-Type: application/json');

$sql = "SELECT * FROM developers";
$result = $conn->query($sql);

$devs = array();
while($row = $result->fetch_assoc()) {
    $devs[] = $row;
}

echo json_encode($devs);
?>