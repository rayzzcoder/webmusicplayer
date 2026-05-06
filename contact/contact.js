document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const btn = document.querySelector('.send-btn');
    const originalText = btn.innerHTML;

    // Show loading state
    btn.innerHTML = 'Sending... <i class="fa-solid fa-spinner fa-spin"></i>';

    fetch('../api/send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === "success"){
            alert("Message sent successfully!");
            this.reset(); // Clear form
        } else {
            alert("Error sending message. Please try again.");
        }
    })
    .catch(error => {
        console.error(error);
        alert("Server error.");
    })
    .finally(() => {
        // Restore button text
        btn.innerHTML = originalText;
    });
});