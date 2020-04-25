<?php

$output = '';

$atts = cl_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$output = '';
$text_id = 'cl_text_' . uniqid();

?>

<div id="<?php echo esc_attr($text_id) ?>" class="cl-text cl-element <?php echo esc_attr( $this->generateClasses('.cl-text') ) ?>" <?php $this->generateStyle('.cl-text', '', true) ?>>

	<?php echo cl_remove_wpautop($content, true); ?>

</div>

<?php if( (int) $margin_paragraphs != 10 ):
	$this->addCustomCss( '#'.$text_id.' p{ margin-top:'.$margin_paragraphs.'; margin-bottom:'.$margin_paragraphs.'; }');
endif; ?>