<?php
session_start(); // Start Session

// Security Check
if (!isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please login."]);
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $subject = $_POST['subject']; // Corresponds to Song Name/Artist input
    $message = $_POST['message'];

    // Prevent SQL Injection using Prepared Statements
    $stmt = $conn->prepare("INSERT INTO messages (name, subject, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $subject, $message);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    
    $stmt->close();
}
?>