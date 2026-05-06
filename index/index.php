<?php
session_start();

// 1. Check Login
if (!isset($_SESSION['username'])) {
    header("Location: ../login/login.html"); // Make sure this path is correct for your folder structure
    exit();
}

// 2. DISABLE BROWSER CACHING
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vibe Player</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="index.css"> 
</head>
<body>
    
    <div class="background-circle circle-1"></div>
    <div class="background-circle circle-2"></div>

    <nav class="navbar">
        <div class="logo">
            <i class="fa-solid fa-music"></i> VibePlayer
        </div>
        
        <ul class="nav-links">
            <li><a href="#" class="active">Home</a></li>
            <li><a href="../about/about.php">About Devs</a></li>
            <li><a href="../contact/contact.php">Request Song</a></li>
            <li><a href="../api/logout.php" class="logout-btn">Logout</a></li>
        </ul>
    
        <div class="menu-toggle">
            <i class="fa-solid fa-bars"></i>
        </div>
    </nav>

    <main>
        <div class="player-container">
            <div class="playlist-box">
                <div class="playlist-header">
                    <h3>Your Playlist</h3>
                    <input type="file" id="audioUpload" accept="audio/*" style="display: none;">
                    <button class="add-btn" onclick="triggerUpload()">
                        <i class="fa-solid fa-plus"></i> Add Song
                    </button>
                </div>
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" placeholder="Search songs...">
                    <button id="favFilterBtn" onclick="toggleFavFilter()" title="Show Favorites Only" style="background:none; border:none; cursor:pointer; padding: 0 10px;">
                    <i class="fa-regular fa-heart" style="color: #aaa; font-size: 1.1rem; transition: 0.2s;"></i>
                    </button>
                </div>
                <ul class="song-list" id="songlist"></ul>
            </div>

            <div class="player-box">
                <div class="album-art-wrapper">
                    <div class="album-cover" id="albumCover"></div>
                    <div class="glow-effect"></div>
                </div>
                
                <div class="song-header">
                    <h2 class="song-title" id="currentSongTitle">Welcome</h2>
                    <i class="fa-regular fa-heart" id="likeBtn" onclick="toggleLike()"></i>
                </div>
                
                <div class="progress-area">
                    <div class="time-info">
                        <span id="currentTime">0:00</span>
                        <span id="duration">0:00</span>
                    </div>
                    <input type="range" id="progressBar" min="0" value="0">
                </div>

                <div class="controls-main">
                    <button class="ctrl-btn" id="shuffleBtn" onclick="toggleShuffle()" title="Shuffle">
                    <i class="fa-solid fa-shuffle"></i>
                    </button>

                    <button class="ctrl-btn" onclick="prevSong()"><i class="fa-solid fa-backward-step"></i></button>
    
                    <button class="play-btn-main" id="playPauseBtn" onclick="togglePlay()">
                    <i class="fa-solid fa-play" id="playIcon"></i>
                    </button>
    
                    <button class="ctrl-btn" onclick="nextSong()"><i class="fa-solid fa-forward-step"></i></button>
    
                    <button class="ctrl-btn" id="repeatBtn" onclick="toggleRepeat()" title="Repeat One">
                    <i class="fa-solid fa-repeat"></i>
                    </button>
                </div>

                <div class="volume-area">
                    <button id="muteBtn" class="vol-btn"><i class="fa-solid fa-volume-high"></i></button>
                    <input type="range" id="volumeBar" min="0" max="1" step="0.01" value="1">
                </div>
            </div>
        </div>
        <div id="uploadModal" class="modal">
            <div class="modal-content">
                <h3>Upload New Song</h3>
        
                <input type="text" id="uploadName" placeholder="Song Name (Optional)">

                <label>Audio File:</label>
                <input type="file" id="modalAudioFile" accept="audio/*">
        
                <label>Cover Image:</label>
                <input type="file" id="modalCoverFile" accept="image/*">
        
                <div class="modal-buttons">
                    <button onclick="closeModal()" class="cancel-btn">Cancel</button>
                    <button onclick="submitUpload()" class="confirm-btn">Upload</button>
                </div>
            </div>
        </div>
    </main>

    <script src="index.js"></script>
</body>
</html>