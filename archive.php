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
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package MaterialDesign
 */

get_header();

$max_width  = get_theme_mod( 'archive_width', 'normal' );
$class_name = sprintf( 'material-archive__%s', $max_width );
$enable_sidebar = get_field('enable_sidebar', 'option');
?>
<div class="main-content-wrapper">
	<?php if ($enable_sidebar) : ?>
		<aside class="sidebar">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>
	<div id="primary" class="content-area <?php echo esc_attr( $class_name ); ?>">
		<main id="main" class="site-main">

		<?php
		if ( have_posts() ) :
			?>

			<header class="page-header">
				<?php
				the_archive_title( '<h1 class="page-title display-small">', '</h1>' );
				?>
			</header><!-- .page-header -->

			<div class="site-main__inner">
				<?php get_template_part( 'template-parts/archive' ); ?>
			</div>
		</div>

			<?php
			get_template_part( 'template-parts/posts-navigation' );

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php
get_footer();

