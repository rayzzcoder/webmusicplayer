<?php
error_reporting(0); 
ini_set('display_errors', 0);
header('Content-Type: application/json');

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect inputs
    $username = $_POST['username'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if(empty($username) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    // Check if email exists
    $check = $conn->prepare("SELECT email FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    if($check->get_result()->num_rows > 0){
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
        exit;
    }

    // Hash and Insert User
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_pass);

    if ($stmt->execute()) {
        
        // ==========================================================
        // 👇 CLONING LOGIC: GIVE NEW USER THE 10 DEFAULT SONGS 👇
        // ==========================================================
        
        // 1. Get the ID of the new user we just created
        $new_user_id = $conn->insert_id;

        // 2. Copy songs from Admin (ID 0) to New User
        // We copy Name, File Path, and Cover Path, but insert the NEW USER ID
        $copy_sql = "INSERT INTO songs (name, file_path, cover_path, user_id) 
                     SELECT name, file_path, cover_path, $new_user_id 
                     FROM songs 
                     WHERE user_id = 0";
        
        $conn->query($copy_sql);

        // ==========================================================
        // 👆 END CLONING LOGIC 👆
        // ==========================================================

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database Error"]);
    }
}
?>