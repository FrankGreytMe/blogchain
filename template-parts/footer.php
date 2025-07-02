<footer class="site-footer">
    <div class="wave-container">
        <!-- Canvas for waves -->
        <canvas 
            id="wave-canvas" 
            style="display: block; width: 100%; height: 100%; position: absolute; top: 0; left: 0; z-index: 1;"
        ></canvas>
        <div class="wave-items">
            <!-- Content overlay -->
            <div class="wave-content">
                <span class="hosted-author">Hosted by: Greyt.iT UG ( ZU Testzwecken)</span>
                <!-- Your links here -->
                <div class="links">
                    <div class="links-1">
                        <a href="https://wcr.is/imprint" class="link-item">IMPRINT</a>
                        <a href="https://wcr.is/terms" class="link-item">TERMS OF SERVICE</a>
                    </div>
                    <a href="https://wcr.is/policy" class="link-item">PRIVACY POLICY</a>
                </div>
            </div>
            
            <!-- Social media icons -->
			<div class="social-medias">
				<?php 
					// Directory path where the social media icons are stored
					$social_media_dir = get_stylesheet_directory() . '/assets/images/social-medias/';
					$social_media_url = get_stylesheet_directory_uri() . '/assets/images/social-medias/';

					// Array of social media platforms in YOUR desired order
					$social_media_order = array(
						'tiktok' => '#',
						'instagram' => '#',
						'youtube' => '#',
						'facebook' => '#',
						'linkedin' => '#',
						'media' => '#',
					);

					// Check if directory exists first
					if (is_dir($social_media_dir)) {
						// Process in defined order
						foreach ($social_media_order as $platform => $url) {
							// Look for matching files (case-insensitive)
							$pattern = $social_media_dir . $platform . '.{svg,png,jpg,jpeg}';
							$matches = glob($pattern, GLOB_BRACE | GLOB_NOSORT);
							
							if (!empty($matches)) {
								$icon_path = $matches[0];
								$icon_url = $social_media_url . basename($icon_path);
								
								echo '<a href="' . esc_url($url) . '" class="social-icon" target="_blank">';
								echo '<img src="' . esc_url($icon_url) . '" alt="' . esc_attr($platform) . '" width="40" height="40" />';
								echo '</a>';
							} else {
								// Optional: Show which icons are missing (for debugging)
								// echo '<!-- Missing icon: ' . esc_html($platform) . ' -->';
							}
						}
					} else {
						echo '<!-- Social media directory not found: ' . esc_html($social_media_dir) . ' -->';
					}
				?>
			</div>
        </div>
    </div>
</footer>
