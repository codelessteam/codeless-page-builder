<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


class Cl_Builder_Mapper {
    
    protected static $sc = array();
    protected static $he = array();
    
    protected static $is_init = false;
    
    public static function setInit(){
        self::$is_init = true;
    }
    
    public static function map( $tag, $attributes ) {
		if ( ! self::$is_init ) {
			if ( empty( $attributes['label'] ) ) {
				trigger_error( sprintf( esc_html__( 'Wrong label for shortcode:%s. Name required', 'codeless-builder' ), $tag ) );
			} elseif ( empty( $attributes['settings'] ) ) {
				trigger_error( sprintf( esc_html__( 'Wrong settings anme for shortcode:%s.', 'codeless-builder' ), $tag ) );
			} else {
				cl_page_builder()->addToMapper(array(
					'tag' => $tag,
					'attributes' => $attributes,
				) );

				return true;
			}

			return false;
		}
		if ( empty( $attributes['label'] ) ) {
			trigger_error( sprintf( esc_html__( 'Wrong label for shortcode:%s. Name required', 'codeless-builder' ), $tag ) );
		} elseif ( empty( $attributes['settings'] ) ) {
			trigger_error( sprintf( esc_html__( 'Wrong settings anme for shortcode:%s.', 'codeless-builder' ), $tag ) );
		} else {
			

			if($attributes['section'] == 'cl_codeless_page_builder')
				self::$sc[ $tag ] = $attributes;
				
			if($attributes['section'] == 'cl_codeless_header_builder' || ( isset( $attributes['use_on_header'] ) && $attributes['use_on_header'] ) )
				self::$he[ $tag ] = $attributes; 
            
            $attributes['type'] = 'clelement';
            $attributes['priority'] = 10;
            $attributes['use_on_simple'] = true; 

            //$attributes['fields'] = array();

            //Kirki::add_field( 'cl_folie', $attributes);
            
			return true;
		}

		return false;
	}
	

	public static function getParam( $tag, $param_name ) {
		if ( ! isset( self::$sc[ $tag ] ) ) {
			return trigger_error( sprintf( esc_html__( 'Wrong name for shortcode:%s. Name required', 'codeless-builder' ), $tag ) );
		}


		if ( ! isset( self::$sc[ $tag ]['fields'] ) ) {
			return false;
		}

		foreach ( self::$sc[ $tag ]['fields'] as $index => $param ) {
			if ( $index == $param_name ) {
				return self::$sc[ $tag ]['fields'][ $index ];
			}
		}

		return false;
	}

	public static function editPredefined( $tag, $pre_id, $content ){
		if ( isset( self::$sc[ $tag ] ) && isset( self::$sc[ $tag ]['predefined'] ) && isset( self::$sc[ $tag ]['predefined'][$pre_id] ) ) {
			self::$sc[ $tag ]['predefined'][$pre_id]['content'] = $content;
			return true;
		}
		return false;
	}
	
	
	
	public static function getShortcodes(){
	    return self::$sc;
	}
	
	public static function getHeaderElements(){
	    return self::$he;
	}
	
	
	
	public static function addShortcodes() {
		
		foreach ( self::$sc as $tag => $attrs ) {
			if ( ! shortcode_exists( $tag ) ) {
				add_shortcode( $tag, 'cl_do_shortcode' );
			}
		}
	}
	
	public static function getShortCode( $tag ) {
		if ( isset( self::$sc[ $tag ] ) && is_array( self::$sc[ $tag ] ) ) {
			$shortcode = self::$sc[ $tag ];
		} else {
			$shortcode = null;
		}

		return $shortcode;
	}
	
	public static function getHeaderElement( $type ) {
		if ( isset( self::$he[ $type ] ) && is_array( self::$he[ $type ] ) ) {
			$element = self::$he[ $type ];
		} else if( isset( self::$sc[ $type ] ) && is_array( self::$sc[ $type ] ) ) {
			$element = self::$sc[ $type ];
		}else{
			$element = null;
		}

		return $element;
	}

	
	
    
}
