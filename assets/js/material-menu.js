document.addEventListener('DOMContentLoaded', function() {
    // Initialize MDC menu components
    const menuInstances = [];
    
    // Initialize all dropdown menus
    document.querySelectorAll('.mdc-menu-surface--anchor').forEach(anchor => {
        const button = anchor.querySelector('.mdc-icon-button');
        const menu = anchor.querySelector('.mdc-menu');
        
        if (button && menu) {
            try {
                // Create MDC menu instance
                const mdcMenu = new mdc.menu.MDCMenu(menu);
                mdcMenu.setAnchorCorner(mdc.menu.Corner.BOTTOM_END);
                menuInstances.push(mdcMenu);
                
                // Set initial ARIA states
                button.setAttribute('aria-expanded', 'false');
                menu.setAttribute('aria-hidden', 'true');
                
                // Toggle menu on button click
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const wasOpen = mdcMenu.open;
                    
                    // Close all other open menus first
                    menuInstances.forEach(instance => {
                        if (instance !== mdcMenu) {
                            instance.open = false;
                        }
                    });
                    
                    // Toggle this menu
                    setTimeout(() => {
                        mdcMenu.open = !wasOpen;
                    }, 10);
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!anchor.contains(e.target) || e.target === button || button.contains(e.target)) {
                        return;
                    }
                    menuInstances.forEach(instance => {
                        instance.open = false;
                    });
                });
                
                // Update ARIA attributes when menu opens/closes
                mdcMenu.listen('MDCMenu:opened', function() {
                    button.setAttribute('aria-expanded', 'true');
                    menu.removeAttribute('aria-hidden');
                    
                    // Focus first menu item
                    const firstItem = menu.querySelector('[role="menuitem"]');
                    if (firstItem) firstItem.focus();
                });
                
                mdcMenu.listen('MDCMenu:closed', function() {
                    button.setAttribute('aria-expanded', 'false');
                    menu.setAttribute('aria-hidden', 'true');
                    button.focus();
                });
                
                // Handle menu item clicks
                menu.querySelectorAll('[role="menuitem"]').forEach(item => {
                    const link = item.querySelector('a');
                    
                    if (link) {
                        // For links (like comment link), just close the menu
                        link.addEventListener('click', function() {
                            mdcMenu.open = false;
                        });
                    } else {
                        // For regular menu items, prevent default and handle action
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            mdcMenu.open = false;
                            
                            // Handle menu actions here
                            const action = this.textContent.trim().toLowerCase();
                            console.log('Menu action:', action);
                        });
                    }
                    
                    // Add keyboard support
                    item.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            this.click();
                        }
                    });
                });
                
            } catch (error) {
                console.error('Error initializing menu:', error);
            }
        }
    });
});