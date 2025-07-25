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

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post-style'); ?>>
	<header class="entry-header section-inner">
		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta">
				<?php
				material_design_theme_posted_on();
				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>

		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title headline-large">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title headline-large"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
        ?>
	</header><!-- .entry-header -->
	<?php material_design_theme_post_thumbnail(); ?>

	<div class="entry-content">
        <?php
            if ( 'post' === get_post_type() ) :
            ?>
                <?php material_design_theme_posted_by(); ?>
		<?php endif; ?>
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'material-design-google' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);

		wp_link_pages(
			array(
				'before' => '<div class="page-links label-large">' . esc_html__( 'Pages:', 'material-design-google' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->
	<footer class="entry-meta">
		<?php get_template_part( 'template-parts/single/questionnaire-capsule' ); ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
