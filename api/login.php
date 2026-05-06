<?php
// 1. SILENCE HTML ERRORS (Crucial for JSON APIs)
error_reporting(0); 
ini_set('display_errors', 0);

// 2. Set Header
header('Content-Type: application/json');

include 'db.php';
session_start();

// 3. Check Request Method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 4. Use Null Coalescing (??) to prevent "Undefined Index" errors
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 5. validation
    if(empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Please fill in all fields"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // 6. Verify Password
        if (password_verify($password, $row['password'])) {
            // 👇 UPDATE THESE LINES 👇
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id']; // We save the User ID now!
            // 👆 END UPDATE 👆
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid Request Method"]);
}
?>