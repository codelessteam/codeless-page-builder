<?php

if ( ! function_exists( 'cl_builder' ) ) {
	
	function cl_builder() {
		return Cl_Builder_Manager::getInstance();
	}
}

if ( ! function_exists( 'cl_asset_url' ) ) {

	function cl_asset_url( $file ) {
		return cl_builder()->assetUrl( $file );
	}
}

if ( ! function_exists( 'cl_path_dir' ) ) {

	function cl_path_dir( $name, $file = '' ) {
		return cl_builder()->path( $name, $file );
	}
}

if ( ! function_exists( 'cl_header_builder' ) ) {

	function cl_header_builder() {
		return cl_builder()->header_builder();
	}
}

if ( ! function_exists( 'cl_page_builder' ) ) {

	function cl_page_builder() {
		return cl_builder()->page_builder();
	}
}



if ( ! function_exists( 'cl_output_header' ) ) {

	function cl_output_header() {
		cl_header_builder()->output();
	}
}


if ( ! function_exists( 'cl_builder_map' ) ) {

	function cl_builder_map($attrs) {
		if ( ! isset( $attrs['settings'] ) ) {
			trigger_error( esc_html__( 'Wrong cl_map object. Base attribute is required', 'codeless-builder' ), E_USER_ERROR );
			die();
		}

		Cl_Builder_Mapper::map($attrs['settings'], $attrs);
	}
}

if ( ! function_exists( 'cl_include_template' ) ) {
	
	function cl_include_template( $template, $variables = array(), $once = false ) {
		is_array( $variables ) && extract( $variables );
		if ( $once ) {
			return require_once cl_path_dir('TEMPLATES_DIR', $template );
		} else {
			return require cl_path_dir('TEMPLATES_DIR', $template );
		}
	}
}

if ( ! function_exists( 'cl_do_shortcode' ) ) {
	
	function cl_do_shortcode( $atts, $content = null, $tag = null ) {
		return Cl_Shortcode_Manager::getInstance()->getElementClass( $tag )
		                                 ->output( $atts, $content );
	}

}

function codeless_get_loadedUrl(){
	//$post_type = get_post_type( get_the_ID() );

	$url = get_permalink( get_the_ID() );

	return $url;
}


function cl_get_header_elements(){
	$header = get_theme_mod('cl_header_builder');
	
	$elements = array();
	if(!empty($header) && is_array($header)){
		foreach($header as $row_id => $row){
			foreach($row as $col_id => $col){
				foreach($col as $el){
					$el['row'] = $row_id;
					$el['col'] = $col_id;
					$elements[] = $el;
				}
			}
		}
	}
	
	return $elements;
}


function cl_map_get_defaults( $tag ) {
	$shortcode = Cl_Builder_Mapper::getShortCode( $tag );
	$params = array();
	if ( is_array( $shortcode ) && isset( $shortcode['fields'] ) && ! empty( $shortcode['fields'] ) ) {
		$params = cl_map_get_params_defaults( $shortcode['fields'] );
	}

	return $params;
}


function cl_map_get_params_defaults( $params ) {
	$resultParams = array();
	foreach ( $params as $param_id => $param ) {
		if ( isset( $param_id ) && 'content' !== $param_id && 'tab_start' !== $param['type'] && 'show_tabs' !== $param['type'] && 'group_start' !== $param['type'] && 'group_end' !== $param['type'] && 'tab_end' !== $param['type'] ) {
			$value = '';
			if ( isset( $param['default'] ) ) {
				$value = $param['default'];
			} 
			
			$resultParams[ $param_id ] = $value;
		}
	}

	return $resultParams;
}


function cl_get_attributes( $tag, $atts = array() ) {
	return shortcode_atts( cl_map_get_defaults( $tag ), $atts, $tag );
	$final = array();	
}

function cl_atts_to_array($value){

	if( is_array($value) )
		return $value;

	if( strpos($value, '_-_json') !== false ){
		$value =  str_replace("'", '"', str_replace('_-_json', '', $value) );
		$value = json_decode($value, true);
	} else if( strpos($value, '__array__') !== false && strpos($value, '__array__end__') !== false){
		$value = str_replace("__array__", '[', str_replace('__array__end__', ']', $value) );
		$value = json_decode($value, true);
	}
	return $value;
}

if( !function_exists('codeless_js_object_to_array') ){
	function codeless_js_object_to_array($value){
		if( is_array($value) )
			return $value;

		if( strpos( $value, '_-_json' ) !== false ) {
			$value = str_replace( "'", '"', str_replace( '_-_json', '', $value ) );
			$value = json_decode( $value, true );
			return $value;
		}else if( strpos($value, '__array__') !== false && strpos($value, '__array__end__') !== false){
			$value = str_replace("__array__", '[', str_replace('__array__end__', ']', $value) );
			$value = json_decode($value, true);
			return $value;
		}else{
			if( strpos( $value, '|' ) === false && strpos( $value, ':' ) !== false ){
				$value = explode(':', $value);
				return array( $value[0] => $value[1] );
			}else if( strpos( $value, '|' ) !== false ){
				$n_v = explode( '|', $value );
				$final_vals = array();
				foreach( $n_v as $key => $val ){
					$val = explode(":", $val);
					$final_vals[$val[0]] = $val[1];
				}
				return $final_vals;
			}
				
		}
	}
}

function cl_remove_wpautop( $content, $autop = false ) {

	if ( $autop ) {
		$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
	}

	return do_shortcode( shortcode_unautop( $content ) );
}

function cl_translateColumnWidthToSpan( $width ) {
	preg_match( '/(\d+)\/(\d+)/', $width, $matches );

	if ( ! empty( $matches ) ) {
		$part_x = (int) $matches[1];
		$part_y = (int) $matches[2];
		if ( $part_x > 0 && $part_y > 0 ) {
			$value = ceil( $part_x / $part_y * 12 );
			if ( $value > 0 && $value <= 12 ) {
				$width = 'col-sm-' . esc_attr( $value );
			}
		}
	}

	return $width;
}

function cl_is_customize_posts_active(){
	if( class_exists('Customize_Posts_Plugin') )
		return true;
	return false;
}



function codeless_shortcode_add( $tag, $func ){
	add_shortcode($tag, $func);
}


/*
 * Inserts a new key/value before the key in the array.
 *
 * @param $key
 *   The key to insert before.
 * @param $array
 *   An array to insert in to.
 * @param $new_key
 *   The key to insert.
 * @param $new_value
 *   An value to insert.
 *
 * @return
 *   The new array if the key exists, FALSE otherwise.
 *
 * @see array_insert_after()
 */
function array_insert_before($key, array &$array, $new_key, $new_value) {
  if (array_key_exists($key, $array)) {
    $new = array();
    foreach ($array as $k => $value) {
      if ($k === $key) {
        $new[$new_key] = $new_value;
      }
      $new[$k] = $value;
    }
    return $new;
  }
  return FALSE;
}




function cl_get_ajax_handlerUrl(){
	return home_url('/?cl_ajax_handler');
}

function cl_get_ajax_nonce(){
	return wp_create_nonce('cl_ajax_handler');
}


function cl_remove_empty_p( $content ){
	
	if( is_customize_preview() )
		return $content;

	if( substr_count($content, '<p') == 1 && substr_count($content, '<p>') == 1 )
		$content = str_replace( array('<p>', '</p>'), array('', ''), $content );
	else
		$content = str_replace( array('<p', '/p>'), array('<span', '/span>'), $content );

	$content = str_replace( array('<div', '/div>'), array('<span', '/span>'), $content );

	return $content;
}


global $cl_row_layouts;
$cl_row_layouts = array(

	array(
		'cells' => '11',
		'mask' => '12',
		'title' => '1/1',
		'icon_class' => 'l_11',
	),
	array(
		'cells' => '12_12',
		'mask' => '26',
		'title' => '1/2 + 1/2',
		'icon_class' => 'l_12_12',
	),
	array(
		'cells' => '23_13',
		'mask' => '29',
		'title' => '2/3 + 1/3',
		'icon_class' => 'l_23_13',
	),
	array(
		'cells' => '13_13_13',
		'mask' => '312',
		'title' => '1/3 + 1/3 + 1/3',
		'icon_class' => 'l_13_13_13',
	),
	array(
		'cells' => '14_14_14_14',
		'mask' => '420',
		'title' => '1/4 + 1/4 + 1/4 + 1/4',
		'icon_class' => 'l_14_14_14_14',
	),
	array(
		'cells' => '14_34',
		'mask' => '212',
		'title' => '1/4 + 3/4',
		'icon_class' => 'l_14_34',
	),
	array(
		'cells' => '14_12_14',
		'mask' => '313',
		'title' => '1/4 + 1/2 + 1/4',
		'icon_class' => 'l_14_12_14',
	),
	array(
		'cells' => '56_16',
		'mask' => '218',
		'title' => '5/6 + 1/6',
		'icon_class' => 'l_56_16',
	),
	array(
		'cells' => '16_16_16_16_16_16',
		'mask' => '642',
		'title' => '1/6 + 1/6 + 1/6 + 1/6 + 1/6 + 1/6',
		'icon_class' => 'l_16_16_16_16_16_16',
	),
	array(
		'cells' => '16_23_16',
		'mask' => '319',
		'title' => '1/6 + 4/6 + 1/6',
		'icon_class' => 'l_16_46_16',
	),
	array(
		'cells' => '16_16_16_12',
		'mask' => '424',
		'title' => '1/6 + 1/6 + 1/6 + 1/2',
		'icon_class' => 'l_16_16_16_12',
	),
);


function codeless_dequeue_stylesandscripts() {
        wp_dequeue_style( 'select2' );
        wp_deregister_style( 'select2' );

        wp_dequeue_script( 'select2');
        wp_deregister_script('select2');

        wp_dequeue_script( 'selectWoo');
        wp_deregister_script('selectWoo');
} 

function codeless_decode_content($data){
	return base64_decode( $data );
}

function codeless_encode_content($data){
	return base64_encode( $data );
}

function codeless_builder_generic_read_file( $file ){
	$content = "";
    
    if( ! function_exists('codeless_decode_content') )
        return false;

    if ( file_exists($file) ) {
                
        $content = codeless_generic_get_content($file);

        if ($content) {

            if( ! empty( $content ) ){
                $decoded_content = codeless_decode_content($content);

                if( !empty( $decoded_content ) )
                    $unserialized_content = unserialize( $decoded_content );

                if ($unserialized_content) {
                    return $unserialized_content;
                }
            }else{
                return '';
            }
        }
        return false;
    }
}

function codeless_builder_generic_get_content($file){
	$content = '';
    if ( function_exists('realpath') )
        $filepath = realpath($file);

    if ( !$filepath || !@is_file($filepath) )
        return '';

    if( ini_get('allow_url_fopen') ) {
        $method = 'fopen';
    } else {
        $method = 'file_get_contents';
    }
    
    if ( $method == 'fopen' ) {
        $handle = fopen( $filepath, 'rw' );

        if( $handle !== false ) {
            if( filesize( $filepath ) > 0 ){
                while (!feof($handle)) {
                    $content .= fread($handle, filesize( $filepath ) );
                }
                fclose( $handle );
            }
        }
        return $content;
    } else {
        return file_get_contents($filepath);
    }
}

function codeless_builder_file_open( $filename, $mode ){
	return fopen( $filename, $mode );
}

function codeless_builder_file_close( $fp ){
	return fclose( $fp );
}

function codeless_builder_f_get_contents( $data ){
	return file_get_contents($data);
}

function codeless_http_user_agent(){
	return isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
}

function codeless_isLocalhost(){
	return ( $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === 'localhost' || $_SERVER['REMOTE_ADDR'] === '::1') ? 1 : 0;
}

function codeless_server_software(){
	return $_SERVER['SERVER_SOFTWARE'];
}


function codeless_add_submenu_page($a1, $a2, $a3, $a4, $a5, $a6){

	add_submenu_page( $a1, $a2, $a3, $a4, $a5, $a6 );
}

function codeless_add_menu_page($a1, $a2, $a3, $a4, $a5, $a6, $a7){
	add_menu_page( $a1, $a2, $a3, $a4, $a5, $a6, $a7 );
}


function vc_add_short_param( $param, $value ){
	if( function_exists( 'vc_add_shortcode_param' ) )
		vc_add_shortcode_param( $param, $value );
}


function codeless_widget_register( $widget ){
	register_widget( $widget );
}

if( ! function_exists( 'codeless_builder_get_default_header' ) ){
	function codeless_builder_get_default_header(){
		$data = array(
			'main' => array ( 
				
				'left' => array ( 
					0 => array ( 
						'id' => '8ead0c8d-2536', 
						'type' => 'cl_header_logo', 
						'order' => 0, 
						'params' => array ( ), 
						'row' => 'main', 
						'col' => 'left', 
						'from_content' => true, 
					), 
					1 => array ( 
						'id' => '688ebeea-7803', 
						'type' => 'cl_header_menu', 
						'order' => 2, 
						'params' => array ( 'hamburger' => false ), 
						'row' => 'main', 
						'col' => 'left', 
						'from_content' => true
					), 
				), 
	
				'right' => array ( 
					0 => array ( 
						'id' => '0baeceb2-c63c', 
						'type' => 'cl_header_tools', 
						'order' => 0, 
						'params' => array ( 
							'search_button' => 1, 
							'cart_button' => 1, 
							'side_nav_button' => 0, 
							'search_type' => 'simple'
						), 
						'row' => 'main', 
						'col' => 'right', 
						'from_content' => true
					), 
				), 
			)
		);
	
		return apply_filters( 'codeless_default_header', $data );
	}
}

function codeless_builder_generate_palettes(){
	return apply_filters( 'codeless_builder_color_palette', array(
        get_theme_mod( 'primary_color' ),
        get_theme_mod( 'border_accent_color' ),
        get_theme_mod( 'labels_accent_color' ),
        get_theme_mod( 'highlight_light_color' ),
        get_theme_mod( 'other_area_links' ),
        get_theme_mod( 'h1_dark_color' ),
        get_theme_mod( 'h1_light_color' )
    ));
}

function codeless_builder_get_attachment_image_src($id, $size){
	if( function_exists( 'codeless_get_attachment_image_src' ) )
		return codeless_get_attachment_image_src( $id, $size );
	else{
		if( $size === false )
			$size = 'full';
		
		$src = wp_get_attachment_image_src( $id, $size );
		return $src[0];
	}
}


if( !function_exists( 'codeless_get_google_fonts' ) ){
	/**
	 * List Google Fonts
	 * @since 1.0.0
	 */
	function codeless_get_google_fonts(){
		$return = array('theme_default' => 'Theme Default');

		$google_fonts   = Kirki_Fonts::get_google_fonts();
		$standard_fonts = Kirki_Fonts::get_standard_fonts();

		$google_fonts = array_combine(array_keys($google_fonts), array_keys($google_fonts));
		$standard_fonts = array_combine(array_keys($standard_fonts), array_keys($standard_fonts));
		$return = array_merge($return, $google_fonts, $standard_fonts);

		return $return;
	}  
}

if( ! function_exists( 'codeless_get_additional_image_sizes' ) ){
	function codeless_get_additional_image_sizes(){
		$add = codeless_wp_get_additional_image_sizes();
		$array = array('theme_default' => 'default', 'full' => 'full');

		foreach($add as $size => $val){
			$array[$size] = $size . ' - ' . $val['width'] . 'x' . $val['height'];
		}

		return $array;
	}
}

if( ! function_exists( 'codeless_wp_get_additional_image_sizes' ) ){
	/**
	 * Check function for WP versions lower than WP 4.7
	 *
	 * @since 1.0.3
	 */
	function codeless_wp_get_additional_image_sizes(){
		if( function_exists( 'wp_get_additional_image_sizes' ) )
			return wp_get_additional_image_sizes();
		
		return array();
	}

}


if( ! class_exists( 'Codeless_Icons' ) ){
	class Codeless_Icons{

		private static $icons = array(
			'cl-icon-tablet2',
			'cl-icon-phone2',
			'cl-icon-document',
			'cl-icon-documents',
			'cl-icon-search2',
			'cl-icon-clipboard3',
			'cl-icon-newspaper',
			'cl-icon-notebook',
			'cl-icon-book-open',
			'cl-icon-browser',
			'cl-icon-calendar2',
			'cl-icon-presentation',
			'cl-icon-picture',
			'cl-icon-pictures',
			'cl-icon-video3',
			'cl-icon-gift2',
			'cl-icon-bargraph',
			'cl-icon-grid2',
			'cl-icon-expand2',
			'cl-icon-focus',
			'cl-icon-edit2',
			'cl-icon-adjustments',
			'cl-icon-ribbon2',
			'cl-icon-hourglass',
			'cl-icon-lock2',
			'cl-icon-megaphone',
			'cl-icon-shield2',
			'cl-icon-trophy2',
			'cl-icon-flag2',
			'cl-icon-map3',
			'cl-icon-gears2',
			'cl-icon-key2',
			'cl-icon-paperclip2',
			'cl-icon-attachment',
			'cl-icon-pricetags',
			'cl-icon-lightbulb',
			'cl-icon-layers2',
			'cl-icon-pencil2',
			'cl-icon-tools',
			'cl-icon-tools-2',
			'cl-icon-scissors3',
			'cl-icon-paintbrush',
			'cl-icon-magnifying-glass',
			'cl-icon-circle-compass',
			'cl-icon-linegraph',
			'cl-icon-mobile2',
			'cl-icon-laptop2',
			'cl-icon-desktop2',
			'cl-icon-camera2',
			'cl-icon-printer3',
			'cl-icon-toolbox',
			'cl-icon-briefcase2',
			'cl-icon-wallet',
			'cl-icon-puzzle',
			'cl-icon-basket',
			'cl-icon-envelope2',
			'cl-icon-streetsign',
			'cl-icon-telescope',
			'cl-icon-mic',
			'cl-icon-strategy',
			'cl-icon-beaker',
			'cl-icon-caution',
			'cl-icon-recycle2',
			'cl-icon-anchor2',
			'cl-icon-profile-male',
			'cl-icon-profile-female',
			'cl-icon-bike',
			'cl-icon-wine',
			'cl-icon-hotairballoon',
			'cl-icon-globe2',
			'cl-icon-genius',
			'cl-icon-map-pin',
			'cl-icon-dial',
			'cl-icon-chat',
			'cl-icon-heart2',
			'cl-icon-cloud2',
			'cl-icon-upload2',
			'cl-icon-download2',
			'cl-icon-target2',
			'cl-icon-hazardous',
			'cl-icon-piechart',
			'cl-icon-speedometer',
			'cl-icon-global',
			'cl-icon-compass2',
			'cl-icon-lifesaver',
			'cl-icon-clock2',
			'cl-icon-aperture',
			'cl-icon-quote',
			'cl-icon-scope',
			'cl-icon-alarmclock',
			'cl-icon-refresh2',
			'cl-icon-happy',
			'cl-icon-sad',
			'cl-icon-facebook2',
			'cl-icon-twitter2',
			'cl-icon-googleplus',
			'cl-icon-rss2',
			'cl-icon-tumblr2',
			'cl-icon-linkedin2',
			'cl-icon-dribbble2',
			'cl-icon-eye2',
			'cl-icon-paper-clip',
			'cl-icon-mail',
			'cl-icon-email',
			'cl-icon-toggle',
			'cl-icon-layout',
			'cl-icon-link3',
			'cl-icon-bell2',
			'cl-icon-lock3',
			'cl-icon-unlock2',
			'cl-icon-ribbon',
			'cl-icon-image2',
			'cl-icon-signal2',
			'cl-icon-target',
			'cl-icon-clipboard2',
			'cl-icon-clock',
			'cl-icon-watch',
			'cl-icon-air-play',
			'cl-icon-camera3',
			'cl-icon-video',
			'cl-icon-disc',
			'cl-icon-printer',
			'cl-icon-monitor',
			'cl-icon-server2',
			'cl-icon-cog2',
			'cl-icon-heart3',
			'cl-icon-paragraph2',
			'cl-icon-align-justify2',
			'cl-icon-align-left2',
			'cl-icon-align-center2',
			'cl-icon-align-right2',
			'cl-icon-book2',
			'cl-icon-layers',
			'cl-icon-stacked',
			'cl-icon-stack-2',
			'cl-icon-paper',
			'cl-icon-paper-stack',
			'cl-icon-search3',
			'cl-icon-zoom-in',
			'cl-icon-zoom-out',
			'cl-icon-reply2',
			'cl-icon-circle-plus',
			'cl-icon-circle-minus',
			'cl-icon-circle-check',
			'cl-icon-circle-cross',
			'cl-icon-square-plus',
			'cl-icon-square-minus',
			'cl-icon-square-check',
			'cl-icon-square-cross',
			'cl-icon-microphone2',
			'cl-icon-record',
			'cl-icon-skip-back',
			'cl-icon-rewind',
			'cl-icon-play2',
			'cl-icon-pause2',
			'cl-icon-stop2',
			'cl-icon-fast-forward2',
			'cl-icon-skip-forward',
			'cl-icon-shuffle',
			'cl-icon-repeat2',
			'cl-icon-folder2',
			'cl-icon-umbrella2',
			'cl-icon-moon',
			'cl-icon-thermometer',
			'cl-icon-drop',
			'cl-icon-sun',
			'cl-icon-cloud3',
			'cl-icon-cloud-upload2',
			'cl-icon-cloud-download2',
			'cl-icon-upload3',
			'cl-icon-download3',
			'cl-icon-location',
			'cl-icon-location-2',
			'cl-icon-map2',
			'cl-icon-battery',
			'cl-icon-head',
			'cl-icon-briefcase3',
			'cl-icon-speech-bubble',
			'cl-icon-anchor3',
			'cl-icon-globe3',
			'cl-icon-box',
			'cl-icon-reload',
			'cl-icon-share2',
			'cl-icon-marquee',
			'cl-icon-marquee-plus',
			'cl-icon-marquee-minus',
			'cl-icon-tag2',
			'cl-icon-power',
			'cl-icon-command',
			'cl-icon-alt',
			'cl-icon-esc',
			'cl-icon-bar-graph',
			'cl-icon-bar-graph-2',
			'cl-icon-pie-graph',
			'cl-icon-star2',
			'cl-icon-arrow-left2',
			'cl-icon-arrow-right2',
			'cl-icon-arrow-up2',
			'cl-icon-arrow-down2',
			'cl-icon-volume',
			'cl-icon-mute',
			'cl-icon-content-right',
			'cl-icon-content-left',
			'cl-icon-grid',
			'cl-icon-grid-2',
			'cl-icon-columns2',
			'cl-icon-loader',
			'cl-icon-bag',
			'cl-icon-ban2',
			'cl-icon-flag3',
			'cl-icon-trash2',
			'cl-icon-expand3',
			'cl-icon-contract',
			'cl-icon-maximize',
			'cl-icon-minimize',
			'cl-icon-plus2',
			'cl-icon-minus2',
			'cl-icon-check2',
			'cl-icon-cross',
			'cl-icon-move',
			'cl-icon-delete',
			'cl-icon-menu',
			'cl-icon-archive2',
			'cl-icon-inbox2',
			'cl-icon-outbox',
			'cl-icon-file2',
			'cl-icon-file-add',
			'cl-icon-file-subtract',
			'cl-icon-help',
			'cl-icon-open',
			'cl-icon-ellipsis',
			'cl-icon-times',
			'cl-icon-tick',
			'cl-icon-plus3',
			'cl-icon-minus3',
			'cl-icon-equals',
			'cl-icon-divide',
			'cl-icon-chevron-right2',
			'cl-icon-chevron-left2',
			'cl-icon-arrow-right-thick',
			'cl-icon-arrow-left-thick',
			'cl-icon-th-small',
			'cl-icon-th-menu',
			'cl-icon-th-list2',
			'cl-icon-th-large2',
			'cl-icon-home2',
			'cl-icon-arrow-forward',
			'cl-icon-arrow-back',
			'cl-icon-rss',
			'cl-icon-location2',
			'cl-icon-link2',
			'cl-icon-image3',
			'cl-icon-arrow-up-thick',
			'cl-icon-arrow-down-thick',
			'cl-icon-starburst',
			'cl-icon-starburst-outline',
			'cl-icon-star3',
			'cl-icon-warning',
			'cl-icon-arrow-left',
			'cl-icon-arrow-right',
			'cl-icon-view',
			'cl-icon-heart-full',
			'cl-icon-heart',
			'cl-icon-noun_150444',
			'cl-icon-search',
			'cl-icon-cart-55',
			'cl-icon-coupon',
			'cl-icon-user-account',
			'cl-icon-spk_error',
			'cl-icon-spk_check',
			'cl-icon-diamond',
			'cl-icon-load-more',
			'cl-icon-noun_185375_cc',
			'cl-icon-loader-arrow',
			'cl-icon-flow-children',
			'cl-icon-export',
			'cl-icon-delete2',
			'cl-icon-delete-outline',
			'cl-icon-cloud-storage',
			'cl-icon-wi-fi',
			'cl-icon-heart4',
			'cl-icon-flash',
			'cl-icon-cancel',
			'cl-icon-backspace',
			'cl-icon-attachment2',
			'cl-icon-arrow-move',
			'cl-icon-warning-1',
			'cl-icon-user2',
			'cl-icon-radar',
			'cl-icon-lock-open',
			'cl-icon-lock-closed',
			'cl-icon-location-arrow2',
			'cl-icon-info2',
			'cl-icon-user-delete',
			'cl-icon-user-add',
			'cl-icon-media-pause',
			'cl-icon-group2',
			'cl-icon-chart-pie',
			'cl-icon-chart-line',
			'cl-icon-chart-bar',
			'cl-icon-chart-area',
			'cl-icon-video2',
			'cl-icon-point-of-interest',
			'cl-icon-infinity',
			'cl-icon-globe4',
			'cl-icon-eye3',
			'cl-icon-cog3',
			'cl-icon-camera4',
			'cl-icon-upload4',
			'cl-icon-scissors',
			'cl-icon-refresh3',
			'cl-icon-pin',
			'cl-icon-key3',
			'cl-icon-info-large',
			'cl-icon-eject2',
			'cl-icon-download4',
			'cl-icon-zoom',
			'cl-icon-zoom-out2',
			'cl-icon-zoom-in2',
			'cl-icon-sort-numerically',
			'cl-icon-sort-alphabetically',
			'cl-icon-input-checked',
			'cl-icon-calender',
			'cl-icon-world',
			'cl-icon-notes',
			'cl-icon-code2',
			'cl-icon-arrow-sync',
			'cl-icon-arrow-shuffle',
			'cl-icon-arrow-repeat',
			'cl-icon-arrow-minimise',
			'cl-icon-arrow-maximise',
			'cl-icon-arrow-loop',
			'cl-icon-anchor4',
			'cl-icon-spanner',
			'cl-icon-puzzle2',
			'cl-icon-power2',
			'cl-icon-plane2',
			'cl-icon-pi',
			'cl-icon-phone3',
			'cl-icon-microphone3',
			'cl-icon-media-rewind',
			'cl-icon-flag4',
			'cl-icon-adjust-brightness',
			'cl-icon-waves',
			'cl-icon-social-twitter',
			'cl-icon-social-facebook',
			'cl-icon-social-dribbble',
			'cl-icon-media-stop',
			'cl-icon-media-record',
			'cl-icon-media-play',
			'cl-icon-media-fast-forward',
			'cl-icon-media-eject',
			'cl-icon-social-vimeo',
			'cl-icon-social-tumbler',
			'cl-icon-social-skype',
			'cl-icon-social-pinterest',
			'cl-icon-social-linkedin',
			'cl-icon-social-last-fm',
			'cl-icon-social-github',
			'cl-icon-social-flickr',
			'cl-icon-at2',
			'cl-icon-times-outline',
			'cl-icon-plus-outline',
			'cl-icon-minus-outline',
			'cl-icon-tick-outline',
			'cl-icon-th-large-outline',
			'cl-icon-equals-outline',
			'cl-icon-divide-outline',
			'cl-icon-chevron-right-outline',
			'cl-icon-chevron-left-outline',
			'cl-icon-arrow-right-outline',
			'cl-icon-arrow-left-outline',
			'cl-icon-th-small-outline',
			'cl-icon-th-menu-outline',
			'cl-icon-th-list-outline',
			'cl-icon-news',
			'cl-icon-home-outline',
			'cl-icon-arrow-up-outline',
			'cl-icon-arrow-forward-outline',
			'cl-icon-arrow-down-outline',
			'cl-icon-arrow-back-outline',
			'cl-icon-trash3',
			'cl-icon-rss-outline',
			'cl-icon-message',
			'cl-icon-location-outline',
			'cl-icon-link-outline',
			'cl-icon-image-outline',
			'cl-icon-export-outline',
			'cl-icon-cross2',
			'cl-icon-wi-fi-outline',
			'cl-icon-star-outline',
			'cl-icon-media-pause-outline',
			'cl-icon-mail2',
			'cl-icon-heart-outline',
			'cl-icon-flash-outline',
			'cl-icon-cancel-outline',
			'cl-icon-beaker2',
			'cl-icon-arrow-move-outline',
			'cl-icon-watch2',
			'cl-icon-warning-outline',
			'cl-icon-time',
			'cl-icon-radar-outline',
			'cl-icon-lock-open-outline',
			'cl-icon-location-arrow-outline',
			'cl-icon-info-outline',
			'cl-icon-backspace-outline',
			'cl-icon-attachment-outline',
			'cl-icon-user-outline',
			'cl-icon-user-delete-outline',
			'cl-icon-user-add-outline',
			'cl-icon-lock-closed-outline',
			'cl-icon-group-outline',
			'cl-icon-chart-pie-outline',
			'cl-icon-chart-line-outline',
			'cl-icon-chart-bar-outline',
			'cl-icon-chart-area-outline',
			'cl-icon-video-outline',
			'cl-icon-point-of-interest-outline',
			'cl-icon-map4',
			'cl-icon-key-outline',
			'cl-icon-infinity-outline',
			'cl-icon-globe-outline',
			'cl-icon-eye-outline',
			'cl-icon-cog-outline',
			'cl-icon-camera-outline',
			'cl-icon-upload-outline',
			'cl-icon-support',
			'cl-icon-scissors-outline',
			'cl-icon-refresh-outline',
			'cl-icon-info-large-outline',
			'cl-icon-eject-outline',
			'cl-icon-download-outline',
			'cl-icon-battery-mid',
			'cl-icon-battery-low',
			'cl-icon-battery-high',
			'cl-icon-zoom-outline',
			'cl-icon-zoom-out-outline',
			'cl-icon-zoom-in-outline',
			'cl-icon-tag3',
			'cl-icon-tabs-outline',
			'cl-icon-pin-outline',
			'cl-icon-message-typing',
			'cl-icon-directions',
			'cl-icon-battery-full',
			'cl-icon-battery-charge',
			'cl-icon-pipette',
			'cl-icon-pencil3',
			'cl-icon-folder3',
			'cl-icon-folder-delete',
			'cl-icon-folder-add',
			'cl-icon-edit3',
			'cl-icon-document2',
			'cl-icon-document-delete',
			'cl-icon-document-add',
			'cl-icon-brush',
			'cl-icon-thumbs-up2',
			'cl-icon-thumbs-down2',
			'cl-icon-pen',
			'cl-icon-sort-numerically-outline',
			'cl-icon-sort-alphabetically-outline',
			'cl-icon-social-last-fm-circular',
			'cl-icon-social-github-circular',
			'cl-icon-compass3',
			'cl-icon-bookmark2',
			'cl-icon-input-checked-outline',
			'cl-icon-code-outline',
			'cl-icon-calender-outline',
			'cl-icon-business-card',
			'cl-icon-arrow-up3',
			'cl-icon-arrow-sync-outline',
			'cl-icon-arrow-right3',
			'cl-icon-arrow-repeat-outline',
			'cl-icon-arrow-loop-outline',
			'cl-icon-arrow-left3',
			'cl-icon-flow-switch',
			'cl-icon-flow-parallel',
			'cl-icon-flow-merge',
			'cl-icon-document-text',
			'cl-icon-clipboard4',
			'cl-icon-calculator2',
			'cl-icon-arrow-minimise-outline',
			'cl-icon-arrow-maximise-outline',
			'cl-icon-arrow-down3',
			'cl-icon-gift3',
			'cl-icon-film2',
			'cl-icon-database2',
			'cl-icon-bell3',
			'cl-icon-anchor-outline',
			'cl-icon-adjust-contrast',
			'cl-icon-world-outline',
			'cl-icon-shopping-bag',
			'cl-icon-power-outline',
			'cl-icon-notes-outline',
			'cl-icon-device-tablet',
			'cl-icon-device-phone',
			'cl-icon-device-laptop',
			'cl-icon-device-desktop',
			'cl-icon-briefcase4',
			'cl-icon-stopwatch',
			'cl-icon-spanner-outline',
			'cl-icon-puzzle-outline',
			'cl-icon-printer2',
			'cl-icon-pi-outline',
			'cl-icon-lightbulb2',
			'cl-icon-flag-outline',
			'cl-icon-contacts',
			'cl-icon-archive3',
			'cl-icon-weather-stormy',
			'cl-icon-weather-shower',
			'cl-icon-weather-partly-sunny',
			'cl-icon-weather-downpour',
			'cl-icon-weather-cloudy',
			'cl-icon-plane-outline',
			'cl-icon-phone-outline',
			'cl-icon-microphone-outline',
			'cl-icon-weather-windy',
			'cl-icon-weather-windy-cloudy',
			'cl-icon-weather-sunny',
			'cl-icon-weather-snow',
			'cl-icon-weather-night',
			'cl-icon-media-stop-outline',
			'cl-icon-media-rewind-outline',
			'cl-icon-media-record-outline',
			'cl-icon-media-play-outline',
			'cl-icon-media-fast-forward-outline',
			'cl-icon-media-eject-outline',
			'cl-icon-wine2',
			'cl-icon-waves-outline',
			'cl-icon-ticket2',
			'cl-icon-tags2',
			'cl-icon-plug2',
			'cl-icon-headphones2',
			'cl-icon-credit-card2',
			'cl-icon-coffee2',
			'cl-icon-book3',
			'cl-icon-beer2',
			'cl-icon-volume2',
			'cl-icon-volume-up2',
			'cl-icon-volume-mute',
			'cl-icon-volume-down2',
			'cl-icon-social-vimeo-circular',
			'cl-icon-social-twitter-circular',
			'cl-icon-social-pinterest-circular',
			'cl-icon-social-linkedin-circular',
			'cl-icon-social-facebook-circular',
			'cl-icon-social-dribbble-circular',
			'cl-icon-tree2',
			'cl-icon-thermometer2',
			'cl-icon-social-tumbler-circular',
			'cl-icon-social-skype-outline',
			'cl-icon-social-flickr-circular',
			'cl-icon-social-at-circular',
			'cl-icon-shopping-cart2',
			'cl-icon-copy',
			'cl-icon-clipboard'
			/*'cl-icon-messages',
			'cl-icon-leaf2',
			'cl-icon-feather',
			'cl-icon-glass',
			'cl-icon-music',
			'cl-icon-search-1',
			'cl-icon-envelope-o',
			'cl-icon-heart-1',
			'cl-icon-star',
			'cl-icon-star-o',
			'cl-icon-user',
			'cl-icon-film',
			'cl-icon-th-large',
			'cl-icon-th',
			'cl-icon-th-list',
			'cl-icon-check',
			'cl-icon-close',
			'cl-icon-search-plus',
			'cl-icon-search-minus',
			'cl-icon-power-off',
			'cl-icon-signal',
			'cl-icon-cog',
			'cl-icon-trash-o',
			'cl-icon-home',
			'cl-icon-file-o',
			'cl-icon-clock-o',
			'cl-icon-road',
			'cl-icon-download',
			'cl-icon-arrow-circle-o-down',
			'cl-icon-arrow-circle-o-up',
			'cl-icon-inbox',
			'cl-icon-play-circle-o',
			'cl-icon-repeat',
			'cl-icon-refresh',
			'cl-icon-list-alt',
			'cl-icon-lock',
			'cl-icon-flag',
			'cl-icon-headphones',
			'cl-icon-volume-off',
			'cl-icon-volume-down',
			'cl-icon-volume-up',
			'cl-icon-qrcode',
			'cl-icon-barcode',
			'cl-icon-tag',
			'cl-icon-tags',
			'cl-icon-book',
			'cl-icon-bookmark',
			'cl-icon-print',
			'cl-icon-camera',
			'cl-icon-font',
			'cl-icon-bold',
			'cl-icon-italic',
			'cl-icon-text-height',
			'cl-icon-text-width',
			'cl-icon-align-left',
			'cl-icon-align-center',
			'cl-icon-align-right',
			'cl-icon-align-justify',
			'cl-icon-list',
			'cl-icon-dedent',
			'cl-icon-indent',
			'cl-icon-video-camera',
			'cl-icon-image',
			'cl-icon-pencil',
			'cl-icon-map-marker',
			'cl-icon-adjust',
			'cl-icon-tint',
			'cl-icon-edit',
			'cl-icon-share-square-o',
			'cl-icon-check-square-o',
			'cl-icon-arrows',
			'cl-icon-step-backward',
			'cl-icon-fast-backward',
			'cl-icon-backward',
			'cl-icon-play',
			'cl-icon-pause',
			'cl-icon-stop',
			'cl-icon-forward',
			'cl-icon-fast-forward',
			'cl-icon-step-forward',
			'cl-icon-eject',
			'cl-icon-chevron-left',
			'cl-icon-chevron-right',
			'cl-icon-plus-circle',
			'cl-icon-minus-circle',
			'cl-icon-times-circle',
			'cl-icon-check-circle',
			'cl-icon-question-circle',
			'cl-icon-info-circle',
			'cl-icon-crosshairs',
			'cl-icon-times-circle-o',
			'cl-icon-check-circle-o',
			'cl-icon-ban',
			'cl-icon-arrow-left-1',
			'cl-icon-arrow-right-1',
			'cl-icon-arrow-up',
			'cl-icon-arrow-down',
			'cl-icon-mail-forward',
			'cl-icon-expand',
			'cl-icon-compress',
			'cl-icon-plus',
			'cl-icon-minus',
			'cl-icon-asterisk',
			'cl-icon-exclamation-circle',
			'cl-icon-gift',
			'cl-icon-leaf',
			'cl-icon-fire',
			'cl-icon-eye',
			'cl-icon-eye-slash',
			'cl-icon-exclamation-triangle',
			'cl-icon-plane',
			'cl-icon-calendar',
			'cl-icon-random',
			'cl-icon-comment',
			'cl-icon-magnet',
			'cl-icon-chevron-up',
			'cl-icon-chevron-down',
			'cl-icon-retweet',
			'cl-icon-shopping-cart',
			'cl-icon-folder',
			'cl-icon-folder-open',
			'cl-icon-arrows-v',
			'cl-icon-arrows-h',
			'cl-icon-bar-chart',
			'cl-icon-twitter-square',
			'cl-icon-facebook-square',
			'cl-icon-camera-retro',
			'cl-icon-key',
			'cl-icon-cogs',
			'cl-icon-comments',
			'cl-icon-thumbs-o-up',
			'cl-icon-thumbs-o-down',
			'cl-icon-star-half',
			'cl-icon-heart-o',
			'cl-icon-sign-out',
			'cl-icon-linkedin-square',
			'cl-icon-thumb-tack',
			'cl-icon-external-link',
			'cl-icon-sign-in',
			'cl-icon-trophy',
			'cl-icon-github-square',
			'cl-icon-upload',
			'cl-icon-lemon-o',
			'cl-icon-phone',
			'cl-icon-square-o',
			'cl-icon-bookmark-o',
			'cl-icon-phone-square',
			'cl-icon-twitter',
			'cl-icon-facebook',
			'cl-icon-facebook-f',
			'cl-icon-github',
			'cl-icon-unlock',
			'cl-icon-credit-card',
			'cl-icon-feed',
			'cl-icon-hdd-o',
			'cl-icon-bullhorn',
			'cl-icon-bell-o',
			'cl-icon-certificate',
			'cl-icon-hand-o-right',
			'cl-icon-hand-o-left',
			'cl-icon-hand-o-up',
			'cl-icon-hand-o-down',
			'cl-icon-arrow-circle-left',
			'cl-icon-arrow-circle-right',
			'cl-icon-arrow-circle-up',
			'cl-icon-arrow-circle-down',
			'cl-icon-globe',
			'cl-icon-wrench',
			'cl-icon-tasks',
			'cl-icon-filter',
			'cl-icon-briefcase',
			'cl-icon-arrows-alt',
			'cl-icon-group',
			'cl-icon-chain',
			'cl-icon-cloud',
			'cl-icon-flask',
			'cl-icon-cut',
			'cl-icon-copy',
			'cl-icon-paperclip',
			'cl-icon-floppy-o',
			'cl-icon-square',
			'cl-icon-bars',
			'cl-icon-list-ul',
			'cl-icon-list-ol',
			'cl-icon-strikethrough',
			'cl-icon-underline',
			'cl-icon-table',
			'cl-icon-magic',
			'cl-icon-truck',
			'cl-icon-pinterest',
			'cl-icon-pinterest-square',
			'cl-icon-google-plus-square',
			'cl-icon-google-plus',
			'cl-icon-money',
			'cl-icon-caret-down',
			'cl-icon-caret-up',
			'cl-icon-caret-left',
			'cl-icon-caret-right',
			'cl-icon-columns',
			'cl-icon-sort',
			'cl-icon-sort-desc',
			'cl-icon-sort-asc',
			'cl-icon-envelope',
			'cl-icon-linkedin',
			'cl-icon-rotate-left',
			'cl-icon-gavel',
			'cl-icon-dashboard',
			'cl-icon-comment-o',
			'cl-icon-comments-o',
			'cl-icon-bolt',
			'cl-icon-sitemap',
			'cl-icon-umbrella',
			'cl-icon-clipboard',
			'cl-icon-lightbulb-o',
			'cl-icon-exchange',
			'cl-icon-cloud-download',
			'cl-icon-cloud-upload',
			'cl-icon-user-md',
			'cl-icon-stethoscope',
			'cl-icon-suitcase',
			'cl-icon-bell',
			'cl-icon-coffee',
			'cl-icon-cutlery',
			'cl-icon-file-text-o',
			'cl-icon-building-o',
			'cl-icon-hospital-o',
			'cl-icon-ambulance',
			'cl-icon-medkit',
			'cl-icon-fighter-jet',
			'cl-icon-beer',
			'cl-icon-h-square',
			'cl-icon-plus-square',
			'cl-icon-angle-double-left',
			'cl-icon-angle-double-right',
			'cl-icon-angle-double-up',
			'cl-icon-angle-double-down',
			'cl-icon-angle-left',
			'cl-icon-angle-right',
			'cl-icon-angle-up',
			'cl-icon-angle-down',
			'cl-icon-desktop',
			'cl-icon-laptop',
			'cl-icon-tablet',
			'cl-icon-mobile',
			'cl-icon-circle-o',
			'cl-icon-quote-left',
			'cl-icon-quote-right',
			'cl-icon-spinner',
			'cl-icon-circle',
			'cl-icon-mail-reply',
			'cl-icon-github-alt',
			'cl-icon-folder-o',
			'cl-icon-folder-open-o',
			'cl-icon-smile-o',
			'cl-icon-frown-o',
			'cl-icon-meh-o',
			'cl-icon-gamepad',
			'cl-icon-keyboard-o',
			'cl-icon-flag-o',
			'cl-icon-flag-checkered',
			'cl-icon-terminal',
			'cl-icon-code',
			'cl-icon-mail-reply-all',
			'cl-icon-star-half-empty',
			'cl-icon-location-arrow',
			'cl-icon-crop',
			'cl-icon-code-fork',
			'cl-icon-chain-broken',
			'cl-icon-question',
			'cl-icon-info',
			'cl-icon-exclamation',
			'cl-icon-superscript',
			'cl-icon-subscript',
			'cl-icon-eraser',
			'cl-icon-puzzle-piece',
			'cl-icon-microphone',
			'cl-icon-microphone-slash',
			'cl-icon-shield',
			'cl-icon-calendar-o',
			'cl-icon-fire-extinguisher',
			'cl-icon-rocket',
			'cl-icon-maxcdn',
			'cl-icon-chevron-circle-left',
			'cl-icon-chevron-circle-right',
			'cl-icon-chevron-circle-up',
			'cl-icon-chevron-circle-down',
			'cl-icon-html5',
			'cl-icon-css3',
			'cl-icon-anchor',
			'cl-icon-unlock-alt',
			'cl-icon-bullseye',
			'cl-icon-ellipsis-h',
			'cl-icon-ellipsis-v',
			'cl-icon-rss-square',
			'cl-icon-play-circle',
			'cl-icon-ticket',
			'cl-icon-minus-square',
			'cl-icon-minus-square-o',
			'cl-icon-level-up',
			'cl-icon-level-down',
			'cl-icon-check-square',
			'cl-icon-pencil-square',
			'cl-icon-external-link-square',
			'cl-icon-share-square',
			'cl-icon-compass',
			'cl-icon-caret-square-o-down',
			'cl-icon-caret-square-o-up',
			'cl-icon-caret-square-o-right',
			'cl-icon-eur',
			'cl-icon-gbp',
			'cl-icon-dollar',
			'cl-icon-inr',
			'cl-icon-cny',
			'cl-icon-rouble',
			'cl-icon-krw',
			'cl-icon-bitcoin',
			'cl-icon-file',
			'cl-icon-file-text',
			'cl-icon-sort-alpha-asc',
			'cl-icon-sort-alpha-desc',
			'cl-icon-sort-amount-asc',
			'cl-icon-sort-amount-desc',
			'cl-icon-sort-numeric-asc',
			'cl-icon-sort-numeric-desc',
			'cl-icon-thumbs-up',
			'cl-icon-thumbs-down',
			'cl-icon-youtube-square',
			'cl-icon-youtube',
			'cl-icon-xing',
			'cl-icon-xing-square',
			'cl-icon-youtube-play',
			'cl-icon-dropbox',
			'cl-icon-stack-overflow',
			'cl-icon-instagram',
			'cl-icon-flickr',
			'cl-icon-adn',
			'cl-icon-bitbucket',
			'cl-icon-bitbucket-square',
			'cl-icon-tumblr',
			'cl-icon-tumblr-square',
			'cl-icon-long-arrow-down',
			'cl-icon-long-arrow-up',
			'cl-icon-long-arrow-left',
			'cl-icon-long-arrow-right',
			'cl-icon-apple',
			'cl-icon-windows',
			'cl-icon-android',
			'cl-icon-linux',
			'cl-icon-dribbble',
			'cl-icon-skype',
			'cl-icon-foursquare',
			'cl-icon-trello',
			'cl-icon-female',
			'cl-icon-male',
			'cl-icon-gittip',
			'cl-icon-sun-o',
			'cl-icon-moon-o',
			'cl-icon-archive',
			'cl-icon-bug',
			'cl-icon-vk',
			'cl-icon-weibo',
			'cl-icon-renren',
			'cl-icon-pagelines',
			'cl-icon-stack-exchange',
			'cl-icon-arrow-circle-o-right',
			'cl-icon-arrow-circle-o-left',
			'cl-icon-caret-square-o-left',
			'cl-icon-dot-circle-o',
			'cl-icon-wheelchair',
			'cl-icon-vimeo-square',
			'cl-icon-try',
			'cl-icon-plus-square-o',
			'cl-icon-space-shuttle',
			'cl-icon-slack',
			'cl-icon-envelope-square',
			'cl-icon-wordpress',
			'cl-icon-openid',
			'cl-icon-bank',
			'cl-icon-graduation-cap',
			'cl-icon-yahoo',
			'cl-icon-google',
			'cl-icon-reddit',
			'cl-icon-reddit-square',
			'cl-icon-stumbleupon-circle',
			'cl-icon-stumbleupon',
			'cl-icon-delicious',
			'cl-icon-digg',
			'cl-icon-pied-piper-pp',
			'cl-icon-pied-piper-alt',
			'cl-icon-drupal',
			'cl-icon-joomla',
			'cl-icon-language',
			'cl-icon-fax',
			'cl-icon-building',
			'cl-icon-child',
			'cl-icon-paw',
			'cl-icon-spoon',
			'cl-icon-cube',
			'cl-icon-cubes',
			'cl-icon-behance',
			'cl-icon-behance-square',
			'cl-icon-steam',
			'cl-icon-steam-square',
			'cl-icon-recycle',
			'cl-icon-automobile',
			'cl-icon-cab',
			'cl-icon-tree',
			'cl-icon-spotify',
			'cl-icon-deviantart',
			'cl-icon-soundcloud',
			'cl-icon-database',
			'cl-icon-file-pdf-o',
			'cl-icon-file-word-o',
			'cl-icon-file-excel-o',
			'cl-icon-file-powerpoint-o',
			'cl-icon-file-image-o',
			'cl-icon-file-archive-o',
			'cl-icon-file-audio-o',
			'cl-icon-file-movie-o',
			'cl-icon-file-code-o',
			'cl-icon-vine',
			'cl-icon-codepen',
			'cl-icon-jsfiddle',
			'cl-icon-life-bouy',
			'cl-icon-circle-o-notch',
			'cl-icon-ra',
			'cl-icon-empire',
			'cl-icon-git-square',
			'cl-icon-git',
			'cl-icon-hacker-news',
			'cl-icon-tencent-weibo',
			'cl-icon-qq',
			'cl-icon-wechat',
			'cl-icon-paper-plane',
			'cl-icon-paper-plane-o',
			'cl-icon-history',
			'cl-icon-circle-thin',
			'cl-icon-header',
			'cl-icon-paragraph',
			'cl-icon-sliders',
			'cl-icon-share-alt',
			'cl-icon-share-alt-square',
			'cl-icon-bomb',
			'cl-icon-futbol-o',
			'cl-icon-tty',
			'cl-icon-binoculars',
			'cl-icon-plug',
			'cl-icon-slideshare',
			'cl-icon-twitch',
			'cl-icon-yelp',
			'cl-icon-newspaper-o',
			'cl-icon-wifi',
			'cl-icon-calculator',
			'cl-icon-paypal',
			'cl-icon-google-wallet',
			'cl-icon-cc-visa',
			'cl-icon-cc-mastercard',
			'cl-icon-cc-discover',
			'cl-icon-cc-amex',
			'cl-icon-cc-paypal',
			'cl-icon-cc-stripe',
			'cl-icon-bell-slash',
			'cl-icon-bell-slash-o',
			'cl-icon-trash',
			'cl-icon-copyright',
			'cl-icon-at',
			'cl-icon-eyedropper',
			'cl-icon-paint-brush',
			'cl-icon-birthday-cake',
			'cl-icon-area-chart',
			'cl-icon-pie-chart',
			'cl-icon-line-chart',
			'cl-icon-lastfm',
			'cl-icon-lastfm-square',
			'cl-icon-toggle-off',
			'cl-icon-toggle-on',
			'cl-icon-bicycle',
			'cl-icon-bus',
			'cl-icon-ioxhost',
			'cl-icon-angellist',
			'cl-icon-cc',
			'cl-icon-ils',
			'cl-icon-meanpath',
			'cl-icon-buysellads',
			'cl-icon-connectdevelop',
			'cl-icon-dashcube',
			'cl-icon-forumbee',
			'cl-icon-leanpub',
			'cl-icon-sellsy',
			'cl-icon-shirtsinbulk',
			'cl-icon-simplybuilt',
			'cl-icon-skyatlas',
			'cl-icon-cart-plus',
			'cl-icon-cart-arrow-down',
			'cl-icon-diamond-1',
			'cl-icon-ship',
			'cl-icon-user-secret',
			'cl-icon-motorcycle',
			'cl-icon-street-view',
			'cl-icon-heartbeat',
			'cl-icon-venus',
			'cl-icon-mars',
			'cl-icon-mercury',
			'cl-icon-intersex',
			'cl-icon-transgender-alt',
			'cl-icon-venus-double',
			'cl-icon-mars-double',
			'cl-icon-venus-mars',
			'cl-icon-mars-stroke',
			'cl-icon-mars-stroke-v',
			'cl-icon-mars-stroke-h',
			'cl-icon-neuter',
			'cl-icon-genderless',
			'cl-icon-facebook-official',
			'cl-icon-pinterest-p',
			'cl-icon-whatsapp',
			'cl-icon-server',
			'cl-icon-user-plus',
			'cl-icon-user-times',
			'cl-icon-bed',
			'cl-icon-viacoin',
			'cl-icon-train',
			'cl-icon-subway',
			'cl-icon-medium',
			'cl-icon-y-combinator',
			'cl-icon-optin-monster',
			'cl-icon-opencart',
			'cl-icon-expeditedssl',
			'cl-icon-battery-4',
			'cl-icon-battery-3',
			'cl-icon-battery-2',
			'cl-icon-battery-1',
			'cl-icon-battery-0',
			'cl-icon-mouse-pointer',
			'cl-icon-i-cursor',
			'cl-icon-object-group',
			'cl-icon-object-ungroup',
			'cl-icon-sticky-note',
			'cl-icon-sticky-note-o',
			'cl-icon-cc-jcb',
			'cl-icon-cc-diners-club',
			'cl-icon-clone',
			'cl-icon-balance-scale',
			'cl-icon-hourglass-o',
			'cl-icon-hourglass-1',
			'cl-icon-hourglass-2',
			'cl-icon-hourglass-3',
			'cl-icon-hourglass2',
			'cl-icon-hand-grab-o',
			'cl-icon-hand-paper-o',
			'cl-icon-hand-scissors-o',
			'cl-icon-hand-lizard-o',
			'cl-icon-hand-spock-o',
			'cl-icon-hand-pointer-o',
			'cl-icon-hand-peace-o',
			'cl-icon-trademark',
			'cl-icon-registered',
			'cl-icon-creative-commons',
			'cl-icon-gg',
			'cl-icon-gg-circle',
			'cl-icon-tripadvisor',
			'cl-icon-odnoklassniki',
			'cl-icon-odnoklassniki-square',
			'cl-icon-get-pocket',
			'cl-icon-wikipedia-w',
			'cl-icon-safari',
			'cl-icon-chrome',
			'cl-icon-firefox',
			'cl-icon-opera',
			'cl-icon-internet-explorer',
			'cl-icon-television',
			'cl-icon-contao',
			'cl-icon-500px',
			'cl-icon-amazon',
			'cl-icon-calendar-plus-o',
			'cl-icon-calendar-minus-o',
			'cl-icon-calendar-times-o',
			'cl-icon-calendar-check-o',
			'cl-icon-industry',
			'cl-icon-map-pin2',
			'cl-icon-map-signs',
			'cl-icon-map-o',
			'cl-icon-map',
			'cl-icon-commenting',
			'cl-icon-commenting-o',
			'cl-icon-houzz',
			'cl-icon-vimeo',
			'cl-icon-black-tie',
			'cl-icon-fonticons',
			'cl-icon-reddit-alien',
			'cl-icon-edge',
			'cl-icon-credit-card-alt',
			'cl-icon-codiepie',
			'cl-icon-modx',
			'cl-icon-fort-awesome',
			'cl-icon-usb',
			'cl-icon-product-hunt',
			'cl-icon-mixcloud',
			'cl-icon-scribd',
			'cl-icon-pause-circle',
			'cl-icon-pause-circle-o',
			'cl-icon-stop-circle',
			'cl-icon-stop-circle-o',
			'cl-icon-shopping-bag2',
			'cl-icon-shopping-basket',
			'cl-icon-hashtag',
			'cl-icon-bluetooth',
			'cl-icon-bluetooth-b',
			'cl-icon-percent',
			'cl-icon-gitlab',
			'cl-icon-wpbeginner',
			'cl-icon-wpforms',
			'cl-icon-envira',
			'cl-icon-universal-access',
			'cl-icon-wheelchair-alt',
			'cl-icon-question-circle-o',
			'cl-icon-blind',
			'cl-icon-audio-description',
			'cl-icon-volume-control-phone',
			'cl-icon-braille',
			'cl-icon-assistive-listening-systems',
			'cl-icon-american-sign-language-interpreting',
			'cl-icon-deaf',
			'cl-icon-glide',
			'cl-icon-glide-g',
			'cl-icon-sign-language',
			'cl-icon-low-vision',
			'cl-icon-viadeo',
			'cl-icon-viadeo-square',
			'cl-icon-snapchat',
			'cl-icon-snapchat-ghost',
			'cl-icon-snapchat-square',
			'cl-icon-pied-piper',
			'cl-icon-first-order',
			'cl-icon-yoast',
			'cl-icon-themeisle',
			'cl-icon-google-plus-circle',
			'cl-icon-fa',*/
		);

		public static function get_icons(){
			return self::$icons;
		}

	}
}


function codeless_sanitize_array( &$array ) {

	foreach ($array as &$value) {	

		if( !is_array($value) )	{
			// sanitize if value is not an array
			$value = sanitize_text_field( $value );
		}  else {
			codeless_sanitize_array($value);
		}
	}

	return $array;
} 


function codeless_wp_kses_post_array( &$array ) {

	foreach ($array as &$value) {	

		if( !is_array($value) )	{
			// sanitize if value is not an array
			$value = wp_kses_post( $value );
		}  else {
			codeless_wp_kses_post_array($value);
		}
	}

	return $array;
} 


?>