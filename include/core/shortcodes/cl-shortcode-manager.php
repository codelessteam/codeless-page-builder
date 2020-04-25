<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

final class Cl_Shortcode_Manager {
    
	private $shortcode_classes = array();
	private $tag;
	
	
	private static $_instance;


	public static function getInstance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	
	public function setTag( $tag ) {
		$this->tag = $tag;

		return $this;
	}
	
	public function settings( $name ) {
		$settings = Cl_Builder_Mapper::getShortCode( $this->tag );

		return isset( $settings[ $name ] ) ? $settings[ $name ] : null;
	}
	
	public function getElementClass( $tag ) {
	    
		if ( isset( $this->shortcode_classes[ $tag ] ) ) {
			return $this->shortcode_classes[ $tag ];
		}
		$settings = Cl_Builder_Mapper::getShortCode( $tag );
		
		$cl_tag = $tag;
		
		if(false !== strstr($tag, 'cl_')){
			$cl_tag = ucfirst(str_replace("cl_","", $tag));
		}
		
		$class_name = ! empty( $settings['php_class_name'] ) ? $settings['php_class_name'] : 'Cl_Shortcode_' . $cl_tag;



		
		$file = cl_path_dir( 'SHORTCODES_DIR', str_replace( '_', '-', $tag ) . '.php' );
		
		if ( is_file( $file ) ) {
			require_once( $file );
		}
		

		if ( class_exists( $class_name ) && is_subclass_of( $class_name, 'Cl_Shortcode' ) ) {
			$shortcode_class = new $class_name( $settings );
		}else{
		    $shortcode_class = new Cl_Shortcode_Simple($settings);
		}
			
		$this->shortcode_classes[ $tag ] = $shortcode_class;

		return $shortcode_class;
		
	}
	
}

?>