<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

$output = '';

$atts = cl_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

?>


<?php if($divider_style != 'icon'){ ?>

	<div class="cl_divider cl-element  <?php echo esc_attr( $this->generateClasses('.cl_divider') ) ?>" <?php $this->generateStyle('.cl_divider', '', true); ?>>
		
		<div class="wrapper <?php echo esc_attr( $this->generateClasses('.cl_divider .wrapper') ).' '.$divider_style ?> " <?php $this->generateStyle('.cl_divider .wrapper', '', true); ?>>
			
			<div class="inner  <?php echo esc_attr( $this->generateClasses('.cl_divider .inner') ).' '.$divider_style ?> " <?php $this->generateStyle('.cl_divider .inner', '', true); ?>"></div>
		
		</div>
	</div>

<?php }else{ ?>

	<div class="cl_divider cl-element <?php echo esc_attr( $this->generateClasses('.cl_divider') ) ?> " <?php $this->generateStyle('.cl_divider', '', true); ?>>

		<div class="wrapper  <?php echo esc_attr( $this->generateClasses('.cl_divider .wrapper') ).' '.$divider_style?> " <?php $this->generateStyle('.cl_divider .wrapper', '', true); ?>>

			<span class="inner left  <?php echo esc_attr( $this->generateClasses('.cl_divider .inner') ).' '.$divider_style ?> "  <?php $this->generateStyle('.cl_divider .inner', '', true);?>></span>

			<i class="<?php echo esc_attr( $this->generateClasses('.cl_divider i') ) ?> " <?php $this->generateStyle('.cl_divider i', '', true); ?> ></i>

			<span class="inner right  <?php echo esc_attr( $this->generateClasses('.cl_divider .inner') ).' '.$divider_style?> "  <?php $this->generateStyle('.cl_divider .inner', '', true);?>></span>
		
		</div>

	</div>

<?php } ?>
