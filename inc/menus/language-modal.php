<div id="language-modal-backdrop" class="modal-backdrop hidden">
    <div class="modal-select">
    <button class="modal-select_close-icon_btn" id="close-language-modal">
        <svg viewBox="0 0 49 48" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
        <path d="M36.5 12L12.5 36M12.5 12L36.5 36" stroke-linecap="round" stroke-linejoin="round" stroke="#1E1E1E" stroke-width="2.5" />
        </svg>
    </button>

    <h4 class="modal-select_title"><?php echo pll__('Switch to preferred Language'); ?></h4>
    <span class="modal-select_label"><?php echo pll__('Language'); ?></span>

    <div class="custom-select-wrapper" id="language-dropdown">
        <div class="custom-select-trigger">
        <span id="selected-language"><?php echo pll__('Select Language'); ?></span>
        <svg viewBox="0 0 24 24" width="24" height="24">
            <path d="M7 10l5 5 5-5z"></path>
        </svg>
        </div>
        <div class="custom-options mat-mdc-menu-panel hidden">
            <?php
            $languages = pll_the_languages(['raw' => 1]);
            foreach ($languages as $lang) {
                echo '<div class="mat-mdc-menu-item" data-url="' . esc_url($lang['url']) . '" data-name="' . esc_attr($lang['name']) . '">' .
                        '<span class="mat-mdc-menu-item-text dropdown-item-text">' . esc_html($lang['name']) . '</span>' .
                    '</div>';
            }
            ?>
        </div>
    </div>

    <div class="modal-select_action">
        <button id="cancel-language-modal" class="btn-neutral"><?php echo pll__('Cancel'); ?></button>
        <button id="save-language-modal" class="btn-primary"><?php echo pll__('Save'); ?></button>
    </div>
    </div>
</div>