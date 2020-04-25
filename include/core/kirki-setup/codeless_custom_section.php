<?php

class WP_Customize_Codeless_Section extends WP_Customize_Section {

		/**
		 * The section type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'codeless';

		/**
		 * An Underscore (JS) template for rendering this section.
		 *
		 * Class variables for this section class are available in the `data` JS object;
		 * export custom variables by overriding WP_Customize_Section::json().
		 *
		 * @access protected
		 */
		protected function render_template() {
			//$l10n = Kirki_l10n::get_strings();
			?>
			<li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }}">
				<h3 class="accordion-section-title" tabindex="0">
					{{ data.title }}
					<span class="screen-reader-text"><?php echo esc_html( 'Press return or enter to open this section', 'codeless-builder' ); ?></span>
				</h3>
				<ul class="accordion-section-content">
					<li class="customize-section-description-container">
						<div class="panel-meta">
							<a class="customize-section-back cl-panel-back"><?php echo esc_attr__('Back', 'codeless-builder') ?> {{{ data.customizeAction }}}</a>
			
			                <h3 class="panel-meta-title">
								{{ data.title }}
								<a href="#" class="kirki-reset-section" data-reset-section-id="{{ data.id }}">
									<span class="dashicons dashicons-image-rotate"></span>
								</a>
							</h3>
							<# if ( data.description ) { #>
    							<div class="description">
    								{{{ data.description }}}
    							</div>
    						<# } #>
						</div>
						
					</li>
				</ul>
			</li>
			<?php
		}
}
