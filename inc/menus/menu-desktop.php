 <div class="mdc-top-app-bar__action-items desktop-menu-items">

    <?php foreach ($menu_links as $link) :
        $current_user = wp_get_current_user();
        $allowed = empty($link['roles']) || array_intersect($link['roles'], $current_user->roles);
        if (!$allowed) continue;

        $label = trim($link['label']);
        $label_normalized = trim(strip_tags($label));
        $label_upper = mb_strtoupper($label_normalized); // Unicode safe

        $is_sign_up =
            (strpos($label_upper, 'SIGN') !== false && strpos($label_upper, 'UP') !== false) ||
            mb_stripos($label_normalized, 'Зареєстр') !== false ||
            mb_stripos($label_normalized, 'уватися') !== false ||
            mb_stripos($label_normalized, 'Зарегистр') !== false ||
            mb_stripos($label_normalized, 'ироваться') !== false;


        $label_raw = $link['label'];
        $label_text = trim(strip_tags($label_raw));
        $is_english = stripos($label_text, 'english') !== false;


        // If the label is "English", show dropdown inside the link
        if ($is_english) : ?>

            <div class="language-switcher-container mdc-top-app-bar__action-item">
                <a href="#" id="language-toggle" class="<?php echo sanitize_title($label) . '-button'; ?> mdc-top-app-bar__action-item mdc-button mdc-button--icon-leading">
                    <?php if (!empty($link['icon_image'])) :
                        $icon = $link['icon_image']; ?>
                        <img src="<?php echo esc_url($icon['url']); ?>" 
                            alt="<?php echo esc_attr($icon['alt']); ?>" 
                            class="mdc-top-app-bar__icon"
                            width="24" height="24">
                    <?php endif; ?>
                            <span class="mdc-button__label" id="language-label">
                        <?php
                        $current_lang = pll_current_language();
                        $languages = pll_the_languages(['raw' => 1]);
                        echo esc_html($languages[$current_lang]['name']);
                        ?>
                    </span>
                </a>

                <div class="cdk-overlay-pane">
                    <div tabindex="-1" role="menu" class="mat-mdc-menu-panel mat-mdc-elevation-specific mat-menu-below mat-elevation-z8" style="transform-origin: left top;">
                        <div class="mat-mdc-menu-content">
                            <?php
                            $languages = pll_the_languages(['raw' => 1]);
                            foreach ($languages as $lang) : ?>
                                <button class="mat-mdc-menu-item mat-mdc-focus-indicator" role="menuitem" tabindex="0" aria-disabled="false" onclick="location.href='<?php echo esc_url($lang['url']); ?>'">
                                    <span class="mat-mdc-menu-item-text"><?php echo esc_html($lang['name']); ?></span>
                                    <div class="mat-ripple mat-mdc-menu-ripple"></div>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    </div>
            </div>

        <?php else : ?>

            <a href="<?php echo esc_url($link['url']); ?>" 
            class="<?php echo sanitize_title($label) . '-button'; ?> mdc-top-app-bar__action-item mdc-button mdc-button--icon-leading <?php echo $is_sign_up ? 'sign-up-class' : ''; ?>">

                <?php if (!empty($link['icon_image'])) :
                    $icon = $link['icon_image']; ?>
                    <img src="<?php echo esc_url($icon['url']); ?>" 
                        alt="<?php echo esc_attr($icon['alt']); ?>" 
                        class="mdc-top-app-bar__icon"
                        width="24" height="24">
                <?php endif; ?>

                <span class="<?php echo $is_sign_up ? 'sign-up-label' : 'mdc-button__label'; ?>">
                    <?php
                    if ($is_sign_up) {
                        echo wp_kses_post(str_replace(' ', '<br class="desktop-break">', $label));
                    } else {
                        echo wp_kses_post($label);
                    }
                    ?>
                </span>
            </a>

        <?php endif;
    endforeach; ?>
</div>