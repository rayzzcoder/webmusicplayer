<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Login required"]);
    exit;
}

if (isset($_POST['id'])) {
    $song_id = $_POST['id'];
    $user_id = $_SESSION['user_id'];

    // 1. Check current status
    $stmt = $conn->prepare("SELECT is_favorite FROM songs WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $song_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        // 2. Flip the status (0 becomes 1, 1 becomes 0)
        $new_status = ($row['is_favorite'] == 1) ? 0 : 1;

        $update = $conn->prepare("UPDATE songs SET is_favorite = ? WHERE id = ?");
        $update->bind_param("ii", $new_status, $song_id);
        
        if ($update->execute()) {
            echo json_encode(["status" => "success", "new_state" => $new_status]);
        } else {
            echo json_encode(["status" => "error"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Song not found"]);
    }
}
?>