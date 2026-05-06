document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Create FormData from the form inputs
    const formData = new FormData(this);

    // Send to PHP Backend
    fetch('../api/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Parse JSON response from PHP
    .then(data => {
        if(data.status === "success"){
            // Redirect to main player on success
            window.location.href = "../index/index.php";
        } else {
            // Show error message from server (e.g., "User not found")
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred. Please try again.");
    });
});