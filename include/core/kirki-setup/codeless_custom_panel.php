<?php
/**
 * Customize API: WP_Customize_Codeless_Panel class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */


class WP_Customize_Codeless_Panel extends WP_Customize_Panel {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var string
	 */
	public $type = 'codeless';
	
	
	protected function content_template() {
		?>
		<li class="panel-meta customize-info accordion-section <# if ( ! data.description ) { #> cannot-expand<# } #>">

			<a class="customize-panel-back cl-panel-back"><?php echo _('Back') ?></a>
			
			<h3 class="panel-meta-title">{{data.title}}</h3>
			
		</li>
		<?php
	}
}
