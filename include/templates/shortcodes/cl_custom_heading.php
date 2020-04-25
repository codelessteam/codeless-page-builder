<?php

$output = '';

$atts = cl_get_attributes( $this->getShortcode(), $atts );
extract( $atts );
$heading_id = 'cl_custom_heading_' . str_replace( ".", '-', uniqid("", true) );


// Load Custom Font
if( $text_font_family != 'theme_default' && $typography == 'custom_font' ){
	$custom_font_link = add_query_arg( array(
		'family' => str_replace( '%2B', '+', urlencode( implode( '|', array( $text_font_family ) ) ) )
	), 'https://fonts.googleapis.com/css' );

	wp_enqueue_style( 'cl_google_font_'.$text_font_family, $custom_font_link );
}

$output = '';

?>

<<?php echo esc_attr( $tag ) ?> id="<?php echo esc_attr( $heading_id ) ?>" class="cl-custom-heading cl-element <?php echo esc_attr( $this->generateClasses('.cl-custom-heading') ) ?>" <?php $this->generateStyle('.cl-custom-heading', '', true) ?>>

<?php echo cl_remove_empty_p( cl_remove_wpautop($content, true) ) ?>
</<?php echo esc_attr( $tag ) ?>>

<?php
/* Add Custom Typography for responsive */

if( ($custom_responsive_992_bool_ch || $custom_responsive_768_bool_ch) ): ?>

	<style type="text/css">

		<?php if( $custom_responsive_992_bool_ch ): ?>
			@media (max-width:992px){ #<?php echo esc_attr( $heading_id ) ?>{ font-size:<?php echo wp_kses_post( $custom_responsive_992_size ) ?> !important; line-height:<?php echo wp_kses_post( $custom_responsive_992_line_height ) ?> !important; } }
		<?php endif; ?>

		<?php if( $custom_responsive_768_bool_ch ): ?>
			@media (max-width:767px){ #<?php echo esc_attr( $heading_id ) ?>{ font-size:<?php echo wp_kses_post($custom_responsive_768_size) ?> !important; line-height:<?php echo esc_attr( $custom_responsive_768_line_height ) ?> !important; } }
		<?php endif; ?>
	</style>

<?php endif; ?>