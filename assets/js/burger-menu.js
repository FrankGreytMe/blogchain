document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('burger-menu-button');
    const mobileMenu = document.getElementById('mobile-drawer');
    
    // Track menu state
    let isMenuOpen = false;
    
    // Toggle menu function
    function toggleMenu() {
      isMenuOpen = !isMenuOpen;
      
      if (isMenuOpen) {
        // Show menu
        mobileMenu.style.display = 'block';
        
        // Trigger animation
        setTimeout(() => {
          mobileMenu.classList.add('open');
          menuButton.classList.add('open');
        }, 10);
        
        // Add aria attributes
        mobileMenu.setAttribute('aria-hidden', 'false');
        menuButton.setAttribute('aria-expanded', 'true');
      } else {
        // Hide menu
        mobileMenu.classList.remove('open');
        menuButton.classList.remove('open');
        
        // Wait for animation to complete before hiding
        setTimeout(() => {
          mobileMenu.style.display = 'none';
        }, 200);
        
        // Update aria attributes
        mobileMenu.setAttribute('aria-hidden', 'true');
        menuButton.setAttribute('aria-expanded', 'false');
      }
    }
    
    // Toggle menu on button click
    menuButton.addEventListener('click', function(e) {
      e.stopPropagation();
      toggleMenu();
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
      if (isMenuOpen && 
          !mobileMenu.contains(event.target) && 
          !menuButton.contains(event.target)) {
        toggleMenu();
      }
    });
    
    // Close menu when item is clicked
    mobileMenu.querySelectorAll('.mdc-list-item').forEach(item => {
      item.addEventListener('click', function() {
        toggleMenu();
      });
    });
  });

// Language script
  document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('language-toggle');
    const dropdownMenu = document.querySelector('.cdk-overlay-pane');

    let isDropdownOpen = false;

    function toggleDropdown() {
      isDropdownOpen = !isDropdownOpen;

      if (isDropdownOpen) {
        dropdownMenu.style.display = 'block';
        setTimeout(() => {
          dropdownMenu.classList.add('visible');
        }, 10);
      } else {
        dropdownMenu.classList.remove('visible');
        setTimeout(() => {
          dropdownMenu.style.display = 'none';
        }, 200); // match the transition duration
      }
    }

    if (toggleButton && dropdownMenu) {
      toggleButton.addEventListener('click', function (event) {
        event.stopPropagation();
        toggleDropdown();
      });

      document.addEventListener('click', function (event) {
        if (isDropdownOpen &&
            !toggleButton.contains(event.target) &&
            !dropdownMenu.contains(event.target)) {
          toggleDropdown();
        }
      });
    }
  });