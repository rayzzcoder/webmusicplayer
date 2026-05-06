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
    <title>About Us - VibePlayer</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="about.css">
</head>
<body>
    <div class="background-circle circle-1"></div>
    <div class="background-circle circle-2"></div>

    <div class="container">
        <a href="../index/index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Player</a>
        
        <h1 class="page-title">Meet The <span>Developers</span></h1>

        <div class="cards-wrapper" id="devContainer">
            <p style="color: #aaa;">Loading team...</p>
        </div>
    </div>

    <script src="about.js"></script>
</body>
</html>