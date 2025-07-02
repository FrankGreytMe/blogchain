document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.mdc-top-app-bar__action-item');
    const currentUrl = window.location.href;
    
    // Set initial active state based on current URL
    menuItems.forEach(item => {
        const itemUrl = item.getAttribute('href');
        if (itemUrl && currentUrl.includes(itemUrl)) {
            item.classList.add('active');
        }
    });
    
    // Handle click events
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Remove active class from all items
            menuItems.forEach(i => i.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
        });
    });
});