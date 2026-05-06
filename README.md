# 🎵 VibePlayer

VibePlayer is a full-stack, personalized web-based music streaming application. It allows users to create accounts, upload their own music library (with custom cover art), and stream audio through a modern, responsive glassmorphism interface.

## 🚀 Features
* **Secure Authentication:** User login and signup with hashed passwords and secure PHP session management.
* **Personalized Library:** Every uploaded song is tied to the user's account via a MySQL database.
* **Custom Uploads:** Upload `.mp3` and `.wav` files along with custom cover art (`.jpg`, `.png`, `.jfif`).
* **Advanced Player Controls:** Play, pause, skip, seek, volume control, Shuffle, and Repeat.
* **Favorites System:** "Heart" songs to save them, and toggle the "Favorites Only" filter to instantly sort your library.
* **Real-time Search:** Search through your library instantly (works seamlessly with the Favorites filter).
* **Keyboard Shortcuts:** Use `Space` to play/pause, `Left/Right Arrows` to skip tracks, and `Up/Down Arrows` for volume.

## 🛠️ Tech Stack
* **Frontend:** HTML5, CSS3 (Glassmorphism UI), Vanilla JavaScript
* **Backend:** PHP
* **Database:** MySQL

## ⚙️ Installation & Setup
1. Clone the repository to your local machine.
2. Ensure you have a local server running (like XAMPP, MAMP, or WAMP).
3. Create a MySQL database named `music_player` (or your chosen name).
4. Import the provided SQL structure to set up the `users` and `songs` tables.
5. Update `api/db.php` with your local database credentials.
6. Open the project in your browser via your localhost!