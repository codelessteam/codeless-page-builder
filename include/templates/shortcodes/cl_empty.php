<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$output = '';

$atts = cl_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$empty_id = 'cl_empty_' . uniqid();
?>

<div id="<?php echo esc_attr( $empty_id ) ?>" class="cl-element cl_empty <?php echo esc_attr( $this->generateClasses('.cl_empty') ); ?>" <?php $this->generateStyle('.cl_empty', '', true); ?>>

</div>

<?php

if( $responsive ): ?>
	<style type="text/css"> 

		<?php if( $custom_767 != '' ): ?>
			@media (max-width:767px){ #<?php echo esc_attr( $empty_id ) ?>{ height:<?php echo esc_attr( $custom_767 ) ?>px; } }
		<?php endif; ?>


		<?php if( $custom_1024 != '' ): ?>
			@media (max-width:1024px){ #<?php echo esc_attr( $empty_id ) ?>{ height:<?php echo esc_attr($custom_1024) ?>px; } }
		<?php endif; ?>
	</style>

<?php endif; ?>