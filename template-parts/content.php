<?php
/**
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package MaterialDesign
 */

/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package MaterialDesign
 */

$show_comments = get_theme_mod( 'archive_comments', true );
$show_author   = get_theme_mod( 'archive_author', true );
$show_excerpt  = get_theme_mod( 'archive_excerpt', true );
$show_date     = get_theme_mod( 'archive_date', true );
$classes       = get_theme_mod( 'archive_outlined', false ) ? 'mdc-card--outlined' : '';
?>

<div class="post-card__container animate-card">
	<div id="<?php the_ID(); ?>" <?php post_class( "mdc-card post-card $classes" ); ?>>
        <!-- Author section -->
         <div class="mdc-first-section">
            <?php if ( ! empty( $show_author ) ) : ?>
                <a
                class="mdc-author mdc-card__action mdc-card__action--button"
                href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"
                aria-label="
                <?php
                    printf(
                        /* translators: 1: author name. */
                        esc_attr__(
                            'Author: %s',
                            'material-design-google'
                        ),
                        esc_attr( get_the_author() )
                    );
                    ?>
                "
                >
                    <?php echo get_avatar( get_the_author_meta( 'ID' ), 18 ); ?>
                    <?php the_author(); ?>
                </a>
            <?php endif; ?>
            <!-- Dots part from Material Theme -->
            <div class="mdc-menu-surface--anchor post-card__actions-wrapper">
                <button class="material-icons mdc-icon-button mdc-ripple-surface"
                        id="dots-menu-button-<?php the_ID(); ?>"
                        aria-haspopup="menu"
                        aria-expanded="false"
                        aria-controls="dots-menu-<?php the_ID(); ?>">
                    more_vert
                </button>

                <div class="mdc-menu mdc-menu-surface"
                    id="dots-menu-<?php the_ID(); ?>"
                    aria-hidden="true"
                    aria-modal="true"
                    aria-orientation="vertical">
                        <ul class="mdc-list" role="menu">
                            <!-- Comment with icon and indicator -->
                            <li class="mdc-list-item" role="menuitem" tabindex="0">
                                <a href="<?php the_permalink(); ?>#respond" class="smooth-scroll-comment">
                                    <span class="material-icons mdc-list-item__graphic" aria-hidden="true">comment</span>
                                    <span class="mdc-list-item__text"><?php echo pll__('Write a comment'); ?></span>
                                </a>
                            </li>

                            <!-- Notification toggle -->
                            <li class="mdc-list-item" role="menuitem" tabindex="0">
                                <span class="material-icons mdc-list-item__graphic" aria-hidden="true">notifications_none</span>
                                <span class="mdc-list-item__text"><?php echo pll__('Notify on changes'); ?></span>
                            </li>
                        </ul>
                </div>
            </div>
        </div>
		<a class="mdc-card__link" href="<?php the_permalink(); ?>">
			<div class="mdc-card__primary-action post-card__primary-action">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="mdc-card__media mdc-card__media--16-9 post-card__media">
						<?php the_post_thumbnail(); ?>
					</div>
				<?php endif; ?>
				<div class="post-card__primary">
					<?php if ( is_sticky() ) : ?>
						<h2
							class="post-card__title title-large"
							aria-label="
							<?php
								printf(
									/* translators: Post title */
									esc_attr__( 'Sticky post: %s', 'material-design-google' ),
									esc_attr( get_the_title() )
								);
							?>
							"
						>
							<i class="material-icons" aria-hidden="true">push_pin</i>
							<?php the_title(); ?>
						</h2>
					<?php else : ?>
						<?php the_title( '<h2 class="post-card__title title-large">', '</h2>' ); ?>
					<?php endif; ?>

					<?php if ( ! empty( $show_date ) ) : ?>
						<time class="post-card__subtitle label-small"><?php the_time( 'F j, Y' ); ?></time>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $show_excerpt ) ) : ?>
					<div class="post-card__secondary body-medium"><?php the_excerpt(); ?></div>
				<?php endif; ?>
			</div>
		</a>
		<?php if ( ! empty( $show_comments ) ) : ?>
			<div class="mdc-card__actions">
				<div class="mdc-card__action-buttons">
					<?php if ( ! empty( $show_comments ) && ( comments_open() || ( 0 < get_comments_number() ) ) ) : ?>
                        <a href="<?php comments_link(); ?>" class="mdc-button mdc-card__action mdc-card__action--button">
                            <?php
                            $comments_number = get_comments_number();
                            $comment_text = $comments_number === 1 ? pll__('%s Comment') : pll__('%s Comments');
                            echo esc_html(sprintf($comment_text, number_format_i18n($comments_number)));
                            ?>
                        </a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<script>
    // Smooth scroll to comment section
document.querySelectorAll('.smooth-scroll-comment').forEach(link => {
    link.addEventListener('click', function(e) {
        const targetId = this.getAttribute('href').split('#')[1];
        const targetEl = document.getElementById(targetId);

        if (targetEl) {
            e.preventDefault();

            // Scroll smoothly
            targetEl.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // Optionally focus the textarea after scroll
            setTimeout(() => {
                const textarea = targetEl.querySelector('textarea');
                if (textarea) textarea.focus();
            }, 600); // delay matches the scroll animation
        }
    });
});

</script>
