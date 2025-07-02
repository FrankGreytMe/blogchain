document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('language-modal-backdrop');
  const dropdown = document.getElementById('language-dropdown');
  const trigger = dropdown.querySelector('.custom-select-trigger');
  const options = dropdown.querySelector('.custom-options');
  const selectedSpan = dropdown.querySelector('#selected-language');
  const saveBtn = document.getElementById('save-language-modal');
  let selectedURL = '';
  let originalLanguage = selectedSpan.textContent;

  // Show modal on clicking English label
  const openTrigger = document.getElementById('open-language-modal');
  openTrigger.addEventListener('click', () => {
    modal.classList.remove('hidden');
  });

   modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.classList.add('hidden');
    }
  });

  // Toggle dropdown inside modal
    trigger.addEventListener('click', () => {
    const isVisible = options.classList.contains('visible');

    if (isVisible) {
        options.classList.remove('visible');
        setTimeout(() => {
        options.classList.add('hidden');
        }, 200); // delay to match CSS transition
    } else {
        options.classList.remove('hidden');
        requestAnimationFrame(() => {
        options.classList.add('visible');
        });
    }
    });


  // Close dropdown on outside click
  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target) && !trigger.contains(e.target)) {
      options.classList.add('hidden');
    }
  });

  // Select language option
  dropdown.querySelectorAll('.mat-mdc-menu-item').forEach(item => {
    item.addEventListener('click', () => {
      // Remove 'selected-language' class from all
    dropdown.querySelectorAll('.mat-mdc-menu-item').forEach(i => i.classList.remove('selected-language'));

    // Add 'selected-language' to clicked item
    item.classList.add('selected-language');

    const langName = item.getAttribute('data-name');
    const langUrl = item.getAttribute('data-url');
    selectedSpan.textContent = langName;
    selectedURL = langUrl;

    // saveBtn.disabled = (langName === originalLanguage);
    options.classList.add('hidden');
    });
  });

  // Cancel closes modal
  document.getElementById('cancel-language-modal').addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  // Close icon closes modal
  document.getElementById('close-language-modal').addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  // Save button navigates and closes modal
    saveBtn.addEventListener('click', () => {
    modal.classList.add('hidden');
    if (selectedURL && selectedURL !== window.location.pathname) {
        window.location.href = selectedURL;
    }
    });


  // Set default selected language based on current URL
  const currentItem = dropdown.querySelector('.mat-mdc-menu-item[data-url*="' + window.location.pathname + '"]');
  if (currentItem) {
    currentItem.classList.add('selected-language');
    const lang = currentItem.getAttribute('data-name');
    selectedSpan.textContent = lang;
    originalLanguage = lang;
  }
});
