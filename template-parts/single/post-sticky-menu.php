<?php
$current_post_id = get_the_id();
$styles = array(
	'--wcr_post_sticky_menu_top: 122px',
	'--wcr_post_sticky_menu_right: 260px',
	'--wcr_post_sticky_menu_opacity: 0',
);
$styles_str = implode( ';', $styles );
$wcr_video_url = get_field( 'wcr_video_url', $current_post_id );
$wcr_audio_url = get_field( 'wcr_audio_url', $current_post_id );
?>
<div class="post-sticky-menu-wrap" style="<?php echo esc_attr( $styles_str ); ?>">
	<div class="post-sticky-menu">
		<button class="post-sticky-menu-item material-icons mdc-icon-button mdc-ripple-surface psmi-more-vertical">
			<span class="material-symbols-outlined">more_vert</span>
			<span class="post-sticky-menu-item-tooltip" data-tooltip_title="<?php echo esc_attr( 'Article interactions', 'material-design-child' ); ?>"></span>
		</button>
		<button class="post-sticky-menu-item material-icons mdc-icon-button mdc-ripple-surface psmi-tldr">
			<span class="material-symbols-outlined">overview</span>
			<span class="post-sticky-menu-item-tooltip" data-tooltip_title="<?php echo esc_attr( 'To long to read', 'material-design-child' ); ?>"></span>
		</button>
		<button class="post-sticky-menu-item material-icons mdc-icon-button mdc-ripple-surface psmi-play-video">
			<span class="material-symbols-outlined">video_library</span>
			<span class="post-sticky-menu-item-tooltip" data-tooltip_title="<?php echo esc_attr( 'Play article videos', 'material-design-child' ); ?>"></span>
		</button>
		<button class="post-sticky-menu-item material-icons mdc-icon-button mdc-ripple-surface psmi-play-audio unselected" data-audio="<?php echo esc_attr( $wcr_audio_url ? $wcr_audio_url : '' ); ?>">
			<?php /* ?>
			<span class="material-symbols-outlined"></span>
			<?php */ ?>
			<span class="material-symbols-outlined">brand_awareness</span>
			<span class="post-sticky-menu-item-tooltip" data-tooltip_title="<?php echo esc_attr( 'Listen to article', 'material-design-child' ); ?>"></span>
		</button>
		<button class="post-sticky-menu-item material-icons mdc-icon-button mdc-ripple-surface psmi-skip-ahead">
			<span class="material-symbols-outlined">move_down</span>
			<span class="post-sticky-menu-item-tooltip" data-tooltip_title="<?php echo esc_attr( 'Read related articles', 'material-design-child' ); ?>"></span>
		</button>
		<?php /* ?>
		<button class="post-sticky-menu-item material-icons mdc-icon-button mdc-ripple-surface" aria-haspopup="menu" aria-expanded="false" aria-controls="dots-menu-<?php the_ID(); ?>">more_vert</button>
		<?php */ ?>
	</div>
	<?php
	if ( $wcr_video_url ) {
		?>
		<div class="psmi-video">
			<button class="psmi-video-close-btn"><span class="material-icons">close</span></button>
			<?php echo do_shortcode( '[embedpress width="100%" height="100%"]' . $wcr_video_url . '[/embedpress]' );?>
		</div>
		<?php
	}
	?>
</div>
