<?php
add_action( 'customize_register', function( $wp_customize ) {
	/**
	 * Group Title
	 */
	class Codeless_Controls_Grouptitle_Control extends WP_Customize_Control {
		public $type = 'grouptitle';
		public function content_template() { ?>
			<h3>{{{ data.label }}}</h3>
			<?php
		}
	}


	class Codeless_Controls_Groupdivider_Control extends WP_Customize_Control {
		public $type = 'groupdivider';
		public function content_template() { 
			 ?>
			
			<div class="divider">{{ data.tooltip }}</div>
			<?php
		}
	}

	class Codeless_Controls_Cssbox_Control extends Kirki_Control_Base {
		public $type = 'css_tool';

		public function enqueue() {
			//wp_enqueue_script( 'codeless-cssbox', cl_asset_url( 'js/kirki-setup.js' ), array( 'kirki-script' ) );
			
		}



		public function content_template() {
			?>
			<# if ( data.tooltip ) { #>
				<a href="#" class="tooltip hint--left" data-hint="{{ data.tooltip }}"><span class='dashicons dashicons-info'></span></a>
			<# } #>
			<label class="customizer-text">
				<# if ( data.label ) { #>
					<span class="customize-control-title">{{{ data.label }}}</span>
				<# } #>
				<# if ( data.description ) { #>
					<span class="description customize-control-description">{{{ data.description }}}</span>
				<# } #>
				
				<# data.value = (_.isObject(data.value) ? data.value : JSON.parse(data.value) ) #>
				<div class="customize-control-css_tool cl_css-tool">
				    <div class="cl_margin">
						<label>margin</label>
						<input type="text" name="margin_top" data-name="margin-top" class="cl_top" placeholder="-" data-attribute="margin" data-field="{{ data.id }}" value="{{ data.value[ 'margin-top' ] }}">
						<input type="text" name="margin_right" data-name="margin-right" class="cl_right" placeholder="-" data-attribute="margin" data-field="{{ data.id }}" value="{{ data.value[ 'margin-right' ] }}">
						<input type="text" name="margin_bottom" data-name="margin-bottom" class="cl_bottom" placeholder="-" data-attribute="margin" data-field="{{ data.id }}" value="{{ data.value[ 'margin-bottom' ] }}">
						<input type="text" name="margin_left" data-name="margin-left" class="cl_left" placeholder="-" data-attribute="margin" data-field="{{ data.id }}" value="{{ data.value[ 'margin-left' ] }}">      
						<div class="cl_border">
							<label>border</label>
							<input type="text" name="border_top_width" data-name="border-top-width" class="cl_top" placeholder="-" data-attribute="border" data-field="{{ data.id }}" value="{{ data.value[ 'border-top-width' ] }}">
							<input type="text" name="border_right_width" data-name="border-right-width" class="cl_right" placeholder="-" data-attribute="border" data-field="{{ data.id }}" value="{{ data.value[ 'border-right-width' ] }}">
							<input type="text" name="border_bottom_width" data-name="border-bottom-width" class="cl_bottom" placeholder="-" data-attribute="border" data-field="{{ data.id }}" value="{{ data.value[ 'border-bottom-width' ] }}">
							<input type="text" name="border_left_width" data-name="border-left-width" class="cl_left" placeholder="-" data-attribute="border" data-field="{{ data.id }}" value="{{ data.value[ 'border-left-width' ] }}">          
							<div class="cl_padding">
							    <label>padding</label>
								<input type="text" name="padding_top" data-name="padding-top" class="cl_top" placeholder="-" data-attribute="padding" data-field="{{ data.id }}" value="{{ data.value[ 'padding-top' ] }}">
								<input type="text" name="padding_right" data-name="padding-right" class="cl_right" placeholder="-" data-attribute="padding" data-field="{{ data.id }}" value="{{ data.value[ 'padding-right' ] }}">
								<input type="text" name="padding_bottom" data-name="padding-bottom" class="cl_bottom" placeholder="-" data-attribute="padding" data-field="{{ data.id }}" value="{{ data.value[ 'padding-bottom' ] }}">
								<input type="text" name="padding_left" data-name="padding-left" class="cl_left" placeholder="-" data-attribute="padding" data-field="{{ data.id }}" value="{{ data.value[ 'padding-left' ] }}">              
								<div class="cl_content"></div>          
							</div>      
						</div>    
					</div>
									
				</div>
				
			</label>
			<?php
		}
	}


	class Codeless_Controls_Clelement_Control extends Kirki_Control_Base {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'clelement';

		/**
		 * The fields that each container row will contain.
		 *
		 * @access public
		 * @var array
		 */
		public $fields = array();

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @access public
		 */
		public function to_json() {
			parent::to_json();

			$fields = $this->fields;

			$this->json['fields'] = $fields;

		}

		/**
		 * Render the control's content.
		 * Allows the content to be overriden without having to rewrite the wrapper in $this->render().
		 *
		 * @access protected
		 */
		protected function render_content() {
			?>
			<?php //$l10n = Kirki_l10n::get_strings(); ?>
			<?php if ( isset($this->tooltip) && '' !== $this->tooltip ) : ?>
				<a href="#" class="tooltip hint--left" data-hint="<?php echo esc_html( $this->tooltip ); ?>"><span class='dashicons dashicons-info'></span></a>
			<?php endif; ?>
			<label class="element-meta">
				<?php if ( ! empty( $this->label ) ) : ?>
					<h2 class="customize-control-title"><?php echo esc_html( $this->label ); ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>
				<input type="hidden" <?php $this->input_attrs(); ?> value="" <?php echo wp_kses_post( $this->get_link() ); ?> />
			</label>

			<ul class="fields"></ul>

			<?php

		}


	}


	class Codeless_Controls_ShowTabs_Control extends WP_Customize_Control {
		public $type = 'show_tabs';
		public function content_template() { 
			 ?>
			<div class="tab_sections">
								
				<# _.each( data.tabs, function( label, id ) { #>
					<a href="#" class="<# if(data.default == id){ #> active <# } #>" data-tab="{{{ id }}}"><i class="{{ label[1] }}"></i><span>{{ label[0] }}</span></a> 
				<# }) #>
									
			</div>
			<?php
		}
	}

	class Codeless_Controls_ImageGallery_Control extends WP_Customize_Control{
		public $type = 'image_gallery';
		public function content_template(){
			?>
			<div class="customize-control-kirki-image_gallery customize-control-upload">
				<# if ( data.tooltip ) { #>
					<a href="#" class="tooltip hint--left" data-hint="{{ field.tooltip }}"><span class='dashicons dashicons-info'></span></a>
				<# } #>
				<label>
					<# if ( data.label ) { #>
						<span class="customize-control-title">{{{ data.label }}}</span>
					<# } #>
											
					<div class="image-gallery-attachments" data-field="{{ data.id }}">
						<# _.each( data.attachments, function( attachment ) { #>
							<div class="image-gallery-thumbnail-wrapper" data-post-id="{{ attachment.id }}">
								<img class="attachment-thumb" src="{{ attachment.url }}" draggable="false" alt="control" />
							</div>
						<#	})#>
					</div>

					<button type="button" class="button image-upload-button" id="image-gallery-modify-gallery">Modify Gallery</button>
				</label>
			</div>
			<?php
		}
	}

	class Codeless_Controls_TabStart_Control extends WP_Customize_Control {
		public $type = 'tab_start';
		public function content_template() { 
			 ?>
			
			
			<?php
		}
	}

	class Codeless_Controls_TabEnd_Control extends WP_Customize_Control {
		public $type = 'tab_end';
		public function content_template() { 
			 ?>
			
			<?php
		}
	}


	class Codeless_Controls_GroupStart_Control extends WP_Customize_Control {
		public $type = 'group_start';
		public function content_template() { 
			 ?>
			
			<div class="group" id="group-{{{ data.groupid }}}">
				<h3>{{ data.label }}</h3> 
			</div>
			<?php
		}
	}



	class Codeless_Controls_GroupEnd_Control extends WP_Customize_Control {
		public $type = 'group_end';
		public function content_template() { 
			 ?>
			<div class="group-end"></div>
			<?php
		}
	}

	class Codeless_Controls_SelectIcon_Control extends WP_Customize_Control {
		public $type = 'select_icon';
		
		public function content_template(){
			?>
			<div class="customize-control-kirki-select_icon">
			
				<label>
					<# if ( data.label ) { #>
						<span class="customize-control-title">Icon</span>
					<# } #>
											
					<div>
						<i>Select Icon by clicking the Icon on website</i>
					</div>

					
				</label>
			</div>
			<?php
		}
		
	}


	class Codeless_Controls_ClText_Control extends WP_Customize_Control {
		public $type = 'cltext';

		public function content_template(){
			?>
			<label>
				<# if ( data.label ) { #>
					<span class="customize-control-title">{{ data.label }}</span>
				<# } #>

				<input type="{{data.type}}" name="" value="{{{ data.value }}}" data-field="{{{ data.id }}}">
			</label>
			<?php
		}
	}



	// Register our custom control with Kirki
	add_filter( 'kirki/control_types', function( $controls ) {
		$controls['grouptitle'] = 'Codeless_Controls_Grouptitle_Control';
		$controls['groupdivider'] = 'Codeless_Controls_Groupdivider_Control';
		$controls['css_tool'] = 'Codeless_Controls_Cssbox_Control';
		$controls['clelement'] = 'Codeless_Controls_Clelement_Control';
		$controls['show_tabs'] = 'Codeless_Controls_ShowTabs_Control';
		$controls['tab_start'] = 'Codeless_Controls_TabStart_Control';
		$controls['tab_end'] = 'Codeless_Controls_TabEnd_Control';
		$controls['group_start'] = 'Codeless_Controls_GroupStart_Control';
		$controls['group_end'] = 'Codeless_Controls_GroupEnd_Control';
		$controls['cltext'] = 'Codeless_Controls_ClText_Control';
		$controls['image_gallery'] = 'Codeless_Controls_ImageGallery_Control';
		$controls['select_icon'] = 'Codeless_Controls_SelectIcon_Control';
		return $controls;
	} );

	

} );

add_action( 'customize_register', function( $wp_customize ) {
	$wp_customize->register_control_type( 'Codeless_Controls_Cssbox_Control' );
	$wp_customize->register_control_type( 'Codeless_Controls_Grouptitle_Control' );
	$wp_customize->register_control_type( 'Codeless_Controls_Groupdivider_Control' );
	$wp_customize->register_control_type( 'Codeless_Controls_ShowTabs_Control' );
	$wp_customize->register_control_type( 'Codeless_Controls_TabStart_Control' );
	$wp_customize->register_control_type( 'Codeless_Controls_TabEnd_Control' );
	$wp_customize->register_control_type( 'Codeless_Controls_SelectIcon_Control' );
	$wp_customize->register_control_type( 'Codeless_Controls_GroupStart_Control' );
	$wp_customize->register_control_type( 'Codeless_Controls_GroupEnd_Control' );

	$wp_customize->register_control_type( 'Codeless_Controls_ClText_Control' );
	$wp_customize->register_control_type( 'Codeless_Controls_ImageGallery_Control' );
});
?>