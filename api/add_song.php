<?php
session_start();
include 'db.php';

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['audioFile'])) {
    
    // --- AUDIO HANDLING ---
    $audio_name = $_FILES['audioFile']['name'];
    $audio_tmp = $_FILES['audioFile']['tmp_name'];
    $audio_ext = strtolower(pathinfo($audio_name, PATHINFO_EXTENSION));
    
    // Allowed Audio Types
    $allowed_audio = ['mp3', 'wav', 'ogg', 'm4a'];
    
    if (!in_array($audio_ext, $allowed_audio)) {
        echo json_encode(["status" => "error", "message" => "Invalid audio format"]);
        exit();
    }

    // Create unique name for audio
    $new_audio_name = uniqid("song_", true) . '.' . $audio_ext;
    $upload_dir_audio = "../assets/music/";
    $audio_dest = $upload_dir_audio . $new_audio_name;

    // --- COVER IMAGE HANDLING (NEW) ---
    $cover_path = '../assets/images/covers/default.png'; // Default fallback

    if (isset($_FILES['coverFile']) && $_FILES['coverFile']['error'] === 0) {
        $cover_name = $_FILES['coverFile']['name'];
        $cover_tmp = $_FILES['coverFile']['tmp_name'];
        $cover_ext = strtolower(pathinfo($cover_name, PATHINFO_EXTENSION));
        
        $allowed_img = ['jpg', 'jpeg', 'jfif', 'png', 'gif', 'webp'];
        
        if (in_array($cover_ext, $allowed_img)) {
            $new_cover_name = uniqid("cover_", true) . '.' . $cover_ext;
            $upload_dir_cover = "../assets/images/covers/";
            
            // Ensure folder exists
            if (!file_exists($upload_dir_cover)) mkdir($upload_dir_cover, 0777, true);
            
            if (move_uploaded_file($cover_tmp, $upload_dir_cover . $new_cover_name)) {
                $cover_path = $upload_dir_cover . $new_cover_name;
            }
        }
    }

    // --- SAVE TO DB ---
    if (move_uploaded_file($audio_tmp, $audio_dest)) {
        // Use the name provided by JS, or fallback to filename
        $name = isset($_POST['name']) ? $_POST['name'] : pathinfo($audio_name, PATHINFO_FILENAME);
        
        $stmt = $conn->prepare("INSERT INTO songs (name, file_path, cover_path, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $audio_dest, $cover_path, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database Error"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to move audio file"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "No audio file uploaded"]);
}
?>