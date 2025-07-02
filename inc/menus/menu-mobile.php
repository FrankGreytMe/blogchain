<div class="mdc-menu mdc-menu-surface" id="mobile-drawer" tabindex="-1">
    <ul class="mdc-list" role="menu" aria-hidden="true" aria-orientation="vertical">
        <?php if ($menu_links) : ?>
            <?php foreach ($menu_links as $link) :
                $current_user = wp_get_current_user();
                $allowed = empty($link['roles']) || array_intersect($link['roles'], $current_user->roles);
                
                $label = trim($link['label']);
                $label_normalized = trim(strip_tags($label));
                $label_upper = mb_strtoupper($label_normalized); // Unicode safe

                // Check for both English and Russian versions
                $is_login = (
                    strpos($label_upper, 'LOGIN') !== false ||
                    mb_stripos($label_normalized, 'Вход') !== false ||
                    mb_stripos($label_normalized, 'Увійти') !== false
                );
                
                $is_sign_up = (
                    (strpos($label_upper, 'SIGN') !== false && strpos($label_upper, 'UP') !== false) ||
                    mb_stripos($label_normalized, 'ЗАРЕЄСТР') !== false ||
                    mb_stripos($label_normalized, 'УВАТИСЯ') !== false ||
                    mb_stripos($label_normalized, 'ЗАРЕГИСТР') !== false ||
                    mb_stripos($label_normalized, 'ИРОВАТЬСЯ') !== false ||
                    mb_stripos($label_normalized, 'Зареєструватися') !== false
                );

                // Only show items that are not login/signup in the dropdown
                if ($allowed && !$is_login && !$is_sign_up) :

                    $label_raw = $link['label_mobile'];
                    $label_text = trim(strip_tags($label_raw));
                    $is_english = stripos($label_text, 'english') !== false;
            ?>
                    <?php if ($is_english) : ?>
                        <!-- Language Switcher Trigger -->
                        <li class="mdc-list-item" role="menuitem">
                            <a id="open-language-modal" class="mdc-list-item__text">
                                <?php if (!empty($link['icon_image_mobile'])) :
                                    $icon = $link['icon_image_mobile']; ?>
                                    <img src="<?php echo esc_url($icon['url']); ?>" 
                                        alt="<?php echo esc_attr($icon['alt']); ?>"
                                        class="mdc-list-item__graphic"
                                        width="16" height="16">
                                <?php endif; ?>
                                <p>
                                <?php
                                    $current_lang = pll_current_language();
                                    $languages = pll_the_languages(['raw' => 1]);
                                    echo esc_html($languages[$current_lang]['name']);
                                ?>
                                </p>
                            </a>
                            <?php if (!empty($link['description_mobile_item'])) : ?>
                                <span class="field_link_description"><?php echo wp_kses_post($link['description_mobile_item']); ?></span>
                            <?php endif; ?>
                        </li>

                    <?php else : ?>
                        <!-- Normal Link -->
                        <li class="mdc-list-item" role="menuitem">
                            <a href="<?php echo esc_url($link['url_mobile']); ?>" class="mdc-list-item__text">
                                <?php if (!empty($link['icon_image_mobile'])) :
                                    $icon = $link['icon_image_mobile']; ?>
                                    <img src="<?php echo esc_url($icon['url']); ?>" 
                                        alt="<?php echo esc_attr($icon['alt']); ?>"
                                        class="mdc-list-item__graphic"
                                        width="16" height="16">
                                <?php endif; ?>
                                <?php echo wp_kses_post($link['label_mobile']); ?>
                            </a>
                            <?php if (!empty($link['description_mobile_item'])) : ?>
                                <span class="field_link_description"><?php echo wp_kses_post($link['description_mobile_item']); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endif;
            endforeach; ?>
        <?php endif; ?>
    </ul>
</div>