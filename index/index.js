// ==========================================
// 1. GLOBAL VARIABLES & ELEMENTS
// ==========================================
let songs = [];
let currentIndex = 0;
let isPlaying = false;
let isSongLoaded = false;
let isShuffle = false;
let isRepeat = false;       // "Repeat One" mode
let isFavFilterActive = false; // "Show Favorites Only" mode

const audioPlayer = new Audio();

// DOM Elements
const songList = document.getElementById("songlist");
const albumCover = document.getElementById("albumCover");
const titleEl = document.getElementById("currentSongTitle"); 
const playIcon = document.getElementById("playIcon");
const progressBar = document.getElementById("progressBar");
const currentTimeEl = document.getElementById("currentTime");
const durationEl = document.getElementById("duration");
const searchInput = document.getElementById('searchInput');

// Upload Modal Elements
const uploadModal = document.getElementById('uploadModal');

// ==========================================
// 2. INITIALIZATION
// ==========================================
function initPlayer() {
    fetchSongs();
    loadUserName();
    albumCover.style.backgroundImage = "url('../assets/images/covers/default.png')";
}

// Load User Name from API
function loadUserName() {
    fetch('../api/me.php')
    .then(res => res.json())
    .then(data => {
        if(data.status === "success") {
            const name = data.username.charAt(0).toUpperCase() + data.username.slice(1);
            titleEl.innerHTML = `Welcome, <span style="color: #1DB954;">${name}</span>`;
        }
    })
    .catch(err => console.error("Failed to load user info"));
}

// Call init immediately
initPlayer();

// ==========================================
// 3. DATA LOGIC (Fetch & Delete)
// ==========================================

// Fetch Songs from Database
function fetchSongs() {
    fetch('../api/get_songs.php')
    .then(res => res.json())
    .then(data => {
        songs = data;
        renderSongList();
    });
}

// Render the List of Songs (HTML)
function renderSongList() {
    songList.innerHTML = "";
    songs.forEach((song, index) => {
        const li = document.createElement("li");
        
        if (isSongLoaded && index === currentIndex) {
            li.classList.add("active");
        }

        li.innerHTML = `
            <div class="song-info" onclick="playSong(${index})">
                <span>${index + 1}. ${song.name}</span>
            </div>
            <button class="delete-btn" onclick="deleteSong(event, ${song.id})">
                <i class="fa-solid fa-trash"></i>
            </button>
        `;
        songList.appendChild(li);
    });
    
    // Re-apply filters (if search or fav filter was active)
    applyFilters();
}

// Delete Logic
function deleteSong(event, id) {
    event.stopPropagation();
    if (!confirm("Permanently delete this song?")) return;

    const formData = new FormData();
    formData.append("id", id);

    fetch('../api/delete_song.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status === "success") {
            const currentSongId = songs[currentIndex]?.id;
            if (currentSongId == id && isSongLoaded) {
                audioPlayer.pause();
                isPlaying = false;
                isSongLoaded = false;
                playIcon.classList.replace("fa-pause", "fa-play");
                loadUserName(); // Reset title
            }
            fetchSongs();
        }
    });
}

// ==========================================
// 4. NEW UPLOAD MODAL LOGIC (With Cover Art)
// ==========================================

function triggerUpload() {
    // Open the modal
    uploadModal.style.display = 'flex';
}

function closeModal() {
    // Close the modal and clear inputs
    uploadModal.style.display = 'none';
    document.getElementById('uploadName').value = "";
    document.getElementById('modalAudioFile').value = "";
    document.getElementById('modalCoverFile').value = "";
}

function submitUpload() {
    const nameInput = document.getElementById('uploadName').value;
    const audioInput = document.getElementById('modalAudioFile').files[0];
    const coverInput = document.getElementById('modalCoverFile').files[0];
    const confirmBtn = document.querySelector('.confirm-btn');

    // Validation
    if (!audioInput) {
        alert("Please select an audio file.");
        return;
    }

    // UX: Show loading
    const originalText = confirmBtn.innerText;
    confirmBtn.innerText = "Uploading...";
    confirmBtn.disabled = true;

    // Prepare Data
    const formData = new FormData();
    formData.append("audioFile", audioInput);
    
    // If user typed a name, use it. Otherwise use filename.
    const finalName = nameInput ? nameInput : audioInput.name.replace(/\.[^/.]+$/, "");
    formData.append("name", finalName);

    // If user picked a cover, add it
    if (coverInput) {
        formData.append("coverFile", coverInput);
    }

    // Send to Server
    fetch('../api/add_song.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === "success") {
            fetchSongs(); // Refresh list
            closeModal(); // Close popup
        } else {
            alert("Upload Failed: " + data.message);
        }
        
        // Reset Button
        confirmBtn.innerText = originalText;
        confirmBtn.disabled = false;
    })
    .catch(err => {
        console.error(err);
        alert("Server Error");
        confirmBtn.innerText = originalText;
        confirmBtn.disabled = false;
    });
}

// Close modal if clicking outside of it
window.onclick = function(event) {
    if (event.target == uploadModal) {
        closeModal();
    }
}

// ==========================================
// 5. PLAYER CONTROLS (Play, Pause, Next)
// ==========================================

function playSong(index) {
    currentIndex = index;
    const song = songs[index];
    
    audioPlayer.src = song.file_path;
    titleEl.textContent = song.name; // Set Title
    
    // Use uploaded cover or default
    const cover = song.cover_path ? song.cover_path : '../assets/images/covers/default.png';
    albumCover.style.backgroundImage = `url('${cover}')`;

    // Handle Heart Icon Visibility
    const likeBtn = document.getElementById('likeBtn');
    likeBtn.style.display = 'block'; 

    // Update Heart Color based on DB status
    if (song.is_favorite == 1) {
        likeBtn.classList.replace('fa-regular', 'fa-solid');
        likeBtn.style.color = '#1DB954';
    } else {
        likeBtn.classList.replace('fa-solid', 'fa-regular');
        likeBtn.style.color = '#fff';
    }

    isSongLoaded = true;
    renderSongList();
    audioPlayer.play();
    isPlaying = true;
    updateUI();
}

function togglePlay() {
    if (!isSongLoaded) {
        if(songs.length > 0) playSong(0);
        return;
    }
    if (isPlaying) {
        audioPlayer.pause();
        isPlaying = false;
    } else {
        audioPlayer.play();
        isPlaying = true;
    }
    updateUI();
}

function nextSong() {
    if (!isSongLoaded || songs.length === 0) return;

    if (isShuffle) {
        // Pick random index
        let newIndex = currentIndex;
        while (newIndex === currentIndex && songs.length > 1) {
            newIndex = Math.floor(Math.random() * songs.length);
        }
        currentIndex = newIndex;
    } else {
        // Sequential
        currentIndex = (currentIndex + 1) % songs.length;
    }
    playSong(currentIndex);
}

function prevSong() {
    if (!isSongLoaded || songs.length === 0) return;
    currentIndex = (currentIndex - 1 + songs.length) % songs.length;
    playSong(currentIndex);
}

function updateUI() {
    if (isPlaying) {
        playIcon.classList.replace("fa-play", "fa-pause");
        albumCover.classList.add("spinning");
    } else {
        playIcon.classList.replace("fa-pause", "fa-play");
        albumCover.classList.remove("spinning");
    }
}

// ==========================================
// 6. SHUFFLE & REPEAT
// ==========================================

function toggleShuffle() {
    isShuffle = !isShuffle;
    const btn = document.getElementById('shuffleBtn');
    isShuffle ? btn.classList.add('active-btn') : btn.classList.remove('active-btn');
}

function toggleRepeat() {
    isRepeat = !isRepeat;
    const btn = document.getElementById('repeatBtn');
    isRepeat ? btn.classList.add('active-btn') : btn.classList.remove('active-btn');
}

// ==========================================
// 7. FAVORITES & FILTER SYSTEM
// ==========================================

function toggleLike() {
    if (!isSongLoaded) return;

    const song = songs[currentIndex];
    const likeBtn = document.getElementById('likeBtn');
    
    // 1. Optimistic UI Update
    const isCurrentlyLiked = likeBtn.classList.contains('fa-solid');
    
    if (isCurrentlyLiked) {
        likeBtn.classList.replace('fa-solid', 'fa-regular');
        likeBtn.style.color = '#fff';
        song.is_favorite = 0;
    } else {
        likeBtn.classList.replace('fa-regular', 'fa-solid');
        likeBtn.style.color = '#1DB954';
        song.is_favorite = 1;
    }

    // 2. Send to API
    const formData = new FormData();
    formData.append('id', song.id);

    fetch('../api/toggle_favorite.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status !== "success") console.error("Like failed");
    });

    // 3. Refresh Filtered List instantly
    setTimeout(() => { applyFilters(); }, 50);
}

function toggleFavFilter() {
    isFavFilterActive = !isFavFilterActive;
    const btnIcon = document.querySelector('#favFilterBtn i');

    if (isFavFilterActive) {
        btnIcon.classList.replace('fa-regular', 'fa-solid');
        btnIcon.style.color = '#1DB954';
    } else {
        btnIcon.classList.replace('fa-solid', 'fa-regular');
        btnIcon.style.color = '#aaa';
    }

    applyFilters();
}

function applyFilters() {
    const term = searchInput.value.toLowerCase();
    const listItems = document.querySelectorAll('.song-list li');

    songs.forEach((song, index) => {
        const li = listItems[index];
        if (!li) return;

        // Condition 1: Matches Search?
        const matchesSearch = song.name.toLowerCase().includes(term);
        
        // Condition 2: Matches Favorites? (Only if filter is active)
        const matchesFav = isFavFilterActive ? (song.is_favorite == 1) : true;

        if (matchesSearch && matchesFav) {
            li.style.display = "flex";
        } else {
            li.style.display = "none";
        }
    });
}

// Bind Search Input to the Master Filter
searchInput.addEventListener('input', applyFilters);

// ==========================================
// 8. EVENT LISTENERS (Progress, Volume, etc)
// ==========================================

// Progress Bar
audioPlayer.addEventListener("timeupdate", () => {
    if(audioPlayer.duration) {
        progressBar.max = audioPlayer.duration;
        progressBar.value = audioPlayer.currentTime;
        currentTimeEl.textContent = formatTime(audioPlayer.currentTime);
        durationEl.textContent = formatTime(audioPlayer.duration);
    }
});

progressBar.addEventListener("input", () => {
    if(isSongLoaded) audioPlayer.currentTime = progressBar.value;
});

// Song Ended Event
audioPlayer.addEventListener("ended", () => {
    if (isRepeat) {
        audioPlayer.currentTime = 0;
        audioPlayer.play();
    } else {
        nextSong();
    }
});

// Volume Control
document.getElementById("volumeBar").addEventListener("input", (e) => audioPlayer.volume = e.target.value);

// Helper: Format Time
function formatTime(s) {
    const min = Math.floor(s / 60);
    const sec = Math.floor(s % 60);
    return `${min}:${sec < 10 ? '0' : ''}${sec}`;
}

// Mobile Menu
const menuToggle = document.querySelector('.menu-toggle');
const navLinks = document.querySelector('.nav-links');

menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    const icon = menuToggle.querySelector('i');
    if (navLinks.classList.contains('active')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-xmark');
    } else {
        icon.classList.remove('fa-xmark');
        icon.classList.add('fa-bars');
    }
});

// ==========================================
// 9. KEYBOARD SHORTCUTS
// ==========================================

document.addEventListener('keydown', function(event) {
    // 1. FIX: Ignore shortcuts if user is typing in ANY input field (Search or Upload)
    // This stops Spacebar from pausing music while you type a song name.
    if (document.activeElement.tagName === "INPUT" || document.activeElement.tagName === "TEXTAREA") {
        return; 
    }

    switch(event.code) {
        case 'Space':
            event.preventDefault(); // Stop page scrolling
            togglePlay();
            break;
        
        case 'ArrowRight':
            event.preventDefault(); // 2. FIX: Stop horizontal scrolling
            nextSong();
            break;
        
        case 'ArrowLeft':
            event.preventDefault(); // 2. FIX: Stop horizontal scrolling
            prevSong();
            break;

        case 'ArrowUp':
            event.preventDefault(); // Stop vertical scrolling
            // Increase Volume
            if(audioPlayer.volume < 1) {
                audioPlayer.volume = Math.min(1, audioPlayer.volume + 0.1);
                document.getElementById("volumeBar").value = audioPlayer.volume;
            }
            break;

        case 'ArrowDown':
            event.preventDefault(); // Stop vertical scrolling
            // Decrease Volume
            if(audioPlayer.volume > 0) {
                audioPlayer.volume = Math.max(0, audioPlayer.volume - 0.1);
                document.getElementById("volumeBar").value = audioPlayer.volume;
            }
            break;
    }
});