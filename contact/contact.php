<?php
session_start();
// 1. Check Login
if (!isset($_SESSION['username'])) {
    header("Location: ../login/login.html");
    exit();
}
// 2. Prevent Back Button Caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Song - VibePlayer</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="contact.css">
</head>
<body>
    <div class="background-circle circle-1"></div>
    <div class="background-circle circle-2"></div>

    <a href="../index/index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Player</a>

    <div class="contact-box">
        <h2>Request a Vibe</h2>
        <p class="subtitle">Missing a song? Let us know!</p>
        
        <form id="contactForm">
            <div class="input-group">
                <input type="text" name="name" id="contactName" required placeholder=" ">
                <label>Your Name</label>
            </div>
            
            <div class="input-group">
                <input type="text" name="subject" id="contactSubject" required placeholder=" ">
                <label>Song Name / Artist</label>
            </div>
        
            <div class="input-group">
                <textarea name="message" id="contactMessage" placeholder=" " rows="4"></textarea>
                <label>Message (Optional)</label>
            </div>
        
            <button type="submit" class="send-btn">Send Request <i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>
    <script src="contact.js"></script>
</body>
</html>