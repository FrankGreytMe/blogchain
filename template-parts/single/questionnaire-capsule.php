<?php
$top_title            = get_field( 'wcr_qcap_top_title', 'option' );
$top_content          = get_field( 'wcr_qcap_top_content', 'option' );
$bottom_title         = get_field( 'wcr_qcap_bottom_title', 'option' );
$bottom_subtitle      = get_field( 'wcr_qcap_bottom_subtitle', 'option' );
$bottom_content_label = get_field( 'wcr_qcap_bottom_content_label', 'option' );
$bottom_content       = get_field( 'wcr_qcap_bottom_content', 'option' );
$button_label         = get_field( 'wcr_qcap_button_label', 'option' );
?>
<div class="qstcap-wrapper">
	<div class="qstcap-content mdc-card">
		<div class="qstcap-top">
			<h4 class="qstcap-top-title"><?php echo wp_kses_post( $top_title ); ?></h4>
			<div class="qstcap-top-content"><?php echo wp_kses_post( $top_content ); ?></div>
		</div>
		<div class="qstcap-bottom">
			<div class="qstcap-bottom-header">
				<div class="qstcap-bottom-header-icon">
				</div>
				<div class="qstcap-bottom-header">
					<h5 class="qstcap-bottom-subtitle"><?php echo wp_kses_post( $bottom_subtitle ); ?></h5>
					<h4 class="qstcap-bottom-title"><?php echo wp_kses_post( $bottom_title ); ?></h4>
				</div>
				<div class="qstcap-bottom-content">
					<p class="qstcap-bottom-content-label"><?php echo wp_kses_post( $bottom_content_label ); ?></p>
					<p><?php echo wp_kses_post( $bottom_content ); ?></p>
					<a class="qstcap-btn" href="https://getme.global/help"><?php echo esc_html( $button_label ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
