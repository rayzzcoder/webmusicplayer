<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

include 'db.php';

// Check Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch ONLY songs belonging to this user
// Since we copied the defaults to this user ID on signup, they will show up here!
$stmt = $conn->prepare("SELECT * FROM songs WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$songs = array();
while($row = $result->fetch_assoc()) {
    $songs[] = $row;
}

echo json_encode($songs);
?>