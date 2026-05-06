const devContainer = document.getElementById('devContainer');

// Fetch developers on page load
window.addEventListener('DOMContentLoaded', () => {
    fetch('../api/get_developers.php')
    .then(response => response.json())
    .then(data => {
        devContainer.innerHTML = ""; // Clear loading text
        
        data.forEach(dev => {
            const card = `
                <div class="dev-card">
                    <div class="img-box">
                        <img src="${dev.image_path}" alt="${dev.name}">
                    </div>
                    <h3>${dev.name}</h3>
                    <p class="role">${dev.role}</p>
                    <p class="desc">${dev.bio}</p>
                    <div class="socials">
                        ${dev.github_link ? `<a href="${dev.github_link}" target="_blank"><i class="fa-brands fa-github"></i></a>` : ''}
                        ${dev.linkedin_link ? `<a href="${dev.linkedin_link}" target="_blank"><i class="fa-brands fa-linkedin"></i></a>` : ''}
                    </div>
                </div>
            `;
            devContainer.innerHTML += card;
        });
    })
    .catch(err => {
        devContainer.innerHTML = "<p>Failed to load team data.</p>";
        console.error(err);
    });
});