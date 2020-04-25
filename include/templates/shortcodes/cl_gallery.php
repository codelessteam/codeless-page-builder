<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$output = '';

$atts = cl_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

if( ! isset( $gallery_id ) || empty( $gallery_id ) )
	$gallery_id = 'cl_gallery_' . str_replace( ".", '-', uniqid("", true) );

?>

<div class="cl_gallery cl-element <?php echo esc_attr( $this->generateClasses('.cl_gallery') ) ?>" <?php $this->generateStyle('.cl_gallery', '', true) ?>>
<?php $images = codeless_js_object_to_array($images); if( !empty($images) ): foreach($images as $img_id): ?>
	<div class="gallery-item" style="padding:<?php echo esc_attr($distance) ?>;">
		<div class="inner-wrapper">
			<?php echo wp_get_attachment_image( $img_id, $image_size ); ?>
		</div>
	</div>
<?php endforeach; endif; ?>
</div>