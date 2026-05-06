<?php
session_start(); 

// 1. Check Login
if (!isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please login."]);
    exit();
}

include 'db.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // 2. Get User ID from Session
    $user_id = $_SESSION['user_id'];

    // 3. Select the file ONLY if it belongs to this user
    $stmt = $conn->prepare("SELECT file_path FROM songs WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        
        $file_path_db = $row['file_path'];

        // =======================================================
        // 👇 SAFE DELETE LOGIC START 👇
        // =======================================================
        
        // Count how many playlist entries are using this exact file
        $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM songs WHERE file_path = ?");
        $check_stmt->bind_param("s", $file_path_db);
        $check_stmt->execute();
        $usage_result = $check_stmt->get_result();
        $usage_count = $usage_result->fetch_assoc()['count'];

        // ONLY delete the physical file if NO ONE else is using it (Count should be 1)
        // If Count > 1, it means other users (or Admin) have this song in their list too.
        if ($usage_count <= 1) {
            
            // Convert relative path to absolute for WAMP
            $real_path = dirname(__DIR__) . "/" . str_replace("../", "", $file_path_db);
            
            if (file_exists($real_path)) {
                unlink($real_path); // Safe to delete physical file
            }
        }
        
        // =======================================================
        // 👆 SAFE DELETE LOGIC END 👆
        // =======================================================

        // 4. Always delete the specific entry from the Database
        $del_stmt = $conn->prepare("DELETE FROM songs WHERE id = ? AND user_id = ?");
        $del_stmt->bind_param("ii", $id, $user_id);
        
        if ($del_stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Delete failed"]);
        }

    } else {
        // If no row found, it means the song doesn't exist OR it doesn't belong to this user
        echo json_encode(["status" => "error", "message" => "Song not found or unauthorized"]);
    }
}
?>