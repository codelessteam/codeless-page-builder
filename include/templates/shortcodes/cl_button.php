<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$output = '';

$atts = cl_get_attributes( $this->getShortcode(), $atts );
extract( $atts );
$button_id = 'cl_btn_div' . str_replace( ".", '-', uniqid("", true) );
$extra_classes = '';

?>


<div class="cl-btn-div cl-element <?php echo esc_attr( $button_id ).' '.esc_attr( $this->generateClasses('.cl-btn-div') ) ?>" <?php  $this->generateStyle('.cl-btn-div', '', true) ?>>
	<a href="<?php echo esc_url( $link ) ?>" target="<?php echo esc_attr( $link_target ) ?>" class="cl-btn <?php echo esc_attr( $this->generateClasses('.cl-btn') ); ?> 
	<?php echo esc_attr( $extra_classes ); ?>" <?php $this->generateStyle('.cl-btn', '', true) ?> > <span><?php echo cl_remove_empty_p( cl_remove_wpautop($btn_title, true) ) ?></span></a>
</div>



<style type="text/css">
 .<?php echo esc_attr($button_id) ?> a:hover{ 
 	background-color: <?php echo esc_attr( $button_bg_color_hover ) ?> !important; 
	color: <?php echo esc_attr( $button_font_color_hover ) ?> !important;
	border-color: <?php echo esc_attr( $button_border_color_hover ) ?> !important;
 }
 </style>

<?php
	$extra_classes = 'cl-btn';