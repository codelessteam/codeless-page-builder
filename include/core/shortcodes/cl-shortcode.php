<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


if ( ! class_exists( 'Cl_Shortcode' ) ) {
	
	
	abstract class Cl_Shortcode{
	    
	    
	    protected $controls_list = array(
			'edit',
			'clone',
			'delete',
	    );
	    
	    protected $template;
	    
	    
	    private $atts;
	    var $custom_css = '';
	    
	    public function __construct( $settings ) {
			$this->settings = $settings;
			$this->css_property = array();
			$this->css_classes = array();
			$this->shortcode = $this->settings['settings'];
			$this->custom_css = '';
		}
	    
	    public function getControlsList(){
	        return $this->controls_list;
	    }
	    
	    protected function findShortcodeTemplate() {
		
			$user_template = cl_builder()->getShortcodesTemplateDir( $this->shortcode . '.php' );
			if ( is_file( $user_template ) ) {
				return $this->template = $user_template;
			}

			$default_dir = cl_builder()->getDefaultShortcodesTemplatesDir() . '/';
			if ( is_file( $default_dir . $this->shortcode . '.php' ) ) {
				return $this->template = $default_dir . $this->shortcode . '.php' ;
			}

			return '';
		}

	
		protected function content( $atts, $content = null ) {
			return $this->loadTemplate( $atts, $content );
		}
		
		public function output($atts, $content){
			$this->atts = $this->prepareAtts($atts);
			$this->generateStyleArray();

			return $this->content($this->atts, $content);
		}

		public function addCustomCss( $css ){

			if( strpos($this->custom_css, $css) === false )
				$this->custom_css .= ' '.$css.' ';
		}
	
		protected function loadTemplate( $atts, $content = null ) {
			$output = '';
			$this->custom_css = '';

			$this->findShortcodeTemplate();
			if ( $this->template ) {
				ob_start();
				include( $this->template );
				$output = ob_get_contents();
				ob_end_clean();
			} else {
				trigger_error( sprintf( esc_html__( 'Template file is missing for `%s` shortcode. Make sure you have `%s` file in your theme folder.', 'codeless-builder' ), $this->shortcode, '' ) );
			}

			if( !empty( $this->custom_css ) ){
				wp_enqueue_style( 'cl-elements-inline', false );
				wp_add_inline_style( 'cl-elements-inline', $this->custom_css );
			}
			
			return $output;
		}
		
		public function getShortcode(){
			return $this->shortcode;
		}
		
		public function getBackendClasses(){
			return '';
		}
		
		protected function prepareAtts( $atts ) {
			$return = array();
			if ( is_array( $atts ) ) {
				foreach ( $atts as $key => $val ) {
					$return[ $key ] = str_replace( array(
						'`{`',
						'`}`',
						'``',
					), array( '[', ']', '"' ), $val );
				}
			}



			return cl_get_attributes( $this->getShortcode(), $return );
		}
		
		
		protected function generateStyleArray(){
			$fields = $this->settings['fields'];
			
			if(!empty($fields)){
				foreach($fields as $field_id => $field){
					if( (isset($field['css_property']) || isset($field['htmldata']) ) && isset($field['selector']) && !empty($field['selector'])){
						if(!isset($this->css_property[$field['selector']]))
							$this->css_property[$field['selector']] = array();
							
						$this->css_property[$field['selector']][$field_id] = $field; 

					}
					
					if( (isset($field['selectClass']) || $field_id == 'animation' ) && isset($field['selector']) && !empty($field['selector'])){
						if(!isset($this->css_classes[$field['selector']]))
							$this->css_classes[$field['selector']] = array();
							
						$this->css_classes[$field['selector']][$field_id] = $field; 
					}
					
					if(isset($field['addClass']) && isset($field['selector']) && !empty($field['selector'])){
						if(!isset($this->css_classes[$field['selector']]))
							$this->css_classes[$field['selector']] = array();
							
						$this->css_classes[$field['selector']][$field_id] = $field; 
					}
					
				
				}
			}
		}
		
		protected function checkRequired($cl_required){
			$bool = true;
							
			foreach($cl_required as $req){
				if($req['operator'] == '=='){
					if(isset($this->atts[$req['setting']]) && $this->atts[$req['setting']] != $req['value']){
						$bool = false;
						break;
					}
										
				}else if($req['operator'] == '!='){
					if(isset($this->atts[$req['setting']]) && $this->atts[$req['setting']] == $req['value']){
						$bool =false;
						break;
					}
										
				}
								
			}
							
			return $bool;
		}
		
		public function generateStyle($selector, $extra = '', $echo = false){
			$style = '';
			$dataHtml = '';
			
			if(isset($this->css_property[$selector])){
				foreach($this->css_property[$selector] as $field_id => $field){

					if( isset($field['media_query']) )
						continue;

					if(isset($this->atts[$field_id]) && ( !empty($this->atts[$field_id]) || $field['type'] == 'css_editor' ) ){
						$value = $this->atts[$field_id];



						if(isset($field['cl_required'])){ 
							if(!$this->checkRequired($field['cl_required']))
								continue;
						}

						
						$suffix = (isset($field['suffix']) && !empty($field['suffix']) ) ? $field['suffix'] : '';
						$suffix = ( $suffix == '' && isset($field['choices']) && isset($field['choices']['suffix'])) ? $field['choices']['suffix'] : $suffix;		
						
						
						if(isset($field['htmldata']) && !empty($field['htmldata'])){
							$dataHtml .= 'data-'.esc_attr( $field['htmldata'] ).'="'.esc_attr( $value ).'" ';
							continue;
						}
						
						
						if($field['css_property'] == 'background-image'){
							
							
						
							if( strpos( $value, 'http' ) == 0 ){
								$style .= $field[ 'css_property' ] . ': url(' . urldecode( $value ) . ');';
							}else{
								
								$value = codeless_js_object_to_array( $value );
								
								if( is_array($value) ){
									if( isset($value['id']) ){
										$img = codeless_builder_get_attachment_image_src( (int) $value['id'], 'full' );
	
										$style .= $field['css_property'].': url('.$img.');';
									}
								}
							}

							continue;
						}

						if($field['css_property'] == 'font-family' ){
							if( $value == 'theme_default' )
								continue;
							else{
								$value = trim($value, '&#8221;');
								if( strpos($value, ' ') !== false )
									$value = "'".$value."'";

							}
						} 
						
						
						
						if($field['type'] == 'css_tool'){
						

							if( !is_array($value) && !empty( $value ) ){
								$value = codeless_js_object_to_array( $value );
							}

				
							
							$default = $field['default'];

							$value = array_merge( $default, $value );

							foreach($value as $prop => $val){
									$style .= $prop.': '.urldecode($val).';';
							}
							
							continue;
							
						} 
						
						if( ! is_array( $field['css_property'] ) )
							$style .= $field['css_property'].': '.$value.$suffix.';';
						else{
							$css_property1 = $field['css_property'][0];
							$style .= $css_property1.': '.$value.$suffix.';';
							$exec = false;
							if( isset($field['css_property'][1]) && is_array($field['css_property'][1]) ){
								$addition_property = $field['css_property'][1][0];
								$addition_require = $field['css_property'][1][1];
								if( is_array( $addition_require )  ){
									foreach($addition_require as $a_value => $new_value){
										if( ! $exec ){
											if( $a_value == $value )
												$style .= $addition_property.': '.$new_value.';';
											else{
												if( isset( $addition_require['other'] ) ){
													$style .= $addition_property.': '.$new_value.';';
												}
											}
											$exec = true;
										}
									}
								}
							}else{
								if( isset($field['css_property'][1]) ){
									$css_property2 = $field['css_property'][1];
									$style .= $css_property2.': '.$value.$suffix.';';
								}
								
							}
						}
					}
				}
			}
			// $dataHtml pre-filtered
			$style = 'style="'.esc_attr( $style ) .esc_attr( $extra ).'" '.$dataHtml;
			
			if( $echo )
                echo $style;
            else
                return $style;
		}

		public function generateCSSBox( $value ){
			
			$style = '';

			if( !is_array($value) )
				$value = codeless_js_object_to_array( $value );
							
			if(!empty($value)){

				foreach($value as $prop => $val){
					$style .= $prop.': '.$val.' !important;';
				}
			}
						
			return $style;
		}
		
		
		public function generateClasses($selector){
			$classes = array();
			
			if(isset($this->css_classes[$selector])){
				
				foreach($this->css_classes[$selector] as $field_id => $field){

					if(isset($this->atts[$field_id]))
						$value = $this->atts[$field_id];
					else
						$value = $field['default'];

						
					if(isset($field['cl_required'])){
						if(!$this->checkRequired($field['cl_required']))
							continue;
					}
					
					if($field_id == 'animation'){
						if($value != 'none'){
							$classes[] = 'animate_on_visible';
							$classes[] = $value;
						}
						continue;
					}
					
					if($field['type'] == 'multicheck'){
						
						$value =  codeless_js_object_to_array($value);
						
						if(!empty($value)){
							foreach($value as $prop => $val){
								$classes[] = $val;
							}
						}
						continue;
					}

					if( $field[ 'type' ] == 'select' && isset($field['multiple']) && $field['multiple'] ) {
						$value = codeless_js_object_to_array( $value );
						
						if( !empty( $value ) ) {
							foreach( $value as $prop => $val ) {
								$classes[] = $val;
							}
						}
						continue;
					}
					
						
					if(isset($field['selectClass'])){
						$classes[] = $field['selectClass'].$value;
						continue;
					}
					
					if(isset($field['addClass'])){
						
						if((int) $value || !empty($value))
							$classes[] = $field['addClass'];
						continue;
					}
				
				}
			}
			
			return implode(" ", $classes);
		
		}
		
	}
	
}
?>