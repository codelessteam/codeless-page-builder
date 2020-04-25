<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


class Cl_Page_Builder{
    
    public $post_shortcodes = array();
    private $post_content = '';
    private $page_header = '';
    public $content_blocks = array();
    private $tag_index = 0;
    public function init(){
		global $cl_builder;
		
        if(is_customize_preview()){
			
			if( $cl_builder->is_page_builder_active() ){
				add_action( 'the_post', array(&$this, 'parseEditableContent' ) ); // after all the_post actions ended
				add_filter('codeless_builder_page_header_content', array(&$this, 'codeless_builder_page_header_content'));

	    		add_action( 'wp_footer', array( &$this, 'printPostShortcodes', ) );
			}
        	
	    	add_action( 'customize_register', array(&$this, 'register_content_setting') );
			if( $cl_builder->is_page_builder_active() ){
				add_action( 'wp_head', array(&$this, 'add_content_ui') );
			}

			$this->buildContentBlocks();
        
        }
        add_action( 'wp_head', array(&$this, 'add_content_wrapper') );
        
    	add_action( 'cl_ajax_handler_cl_load_shortcode', array(&$this, 'load_shortcode_ajax'), 9999);
		add_action( 'wp_ajax_cl_save_page_content', array(&$this, 'cl_save_page_content'));
		add_action( 'wp_ajax_cl_save_template', array(&$this, 'cl_save_template'));
    }


    public function cl_save_template(){

		check_ajax_referer( 'codeless_builder', 'nonce' );

		$content = isset( $_POST['data'] ) && isset( $_POST['data']['content'] ) ? wp_kses_post( $_POST['data']['content'] ) : '';
		$title = isset( $_POST['data'] ) && isset( $_POST['data']['name'] ) ? sanitize_title( $_POST['data']['name'] ) : '';

    	$my_post = array(
		      //'post_title'   => 'Updated',
		      'post_content' => $content,
		      'post_title' => $title,
		      'post_type' => 'content_block',
		      'post_status' => 'publish'
		);
		
		wp_insert_post($my_post);	
    	
    	wp_send_json_success();
    }

    public function add_content_ui(){
	
		// Content -> Begin
		add_filter( 'the_content', array(&$this, 'prepend_element_html' ) );
		// Content -> End
		add_filter( 'the_content', array(&$this, 'append_element_html' ) );

	}
	
	public function add_content_wrapper(){
	
		// Content -> Begin
		add_filter( 'the_content', array(&$this, 'prepend_content_wrapper' ) );
		// Content -> End
		add_filter( 'the_content', array(&$this, 'append_content_wrapper' ) );

	}
	

    
    public function force_empty_post_dates( $data ) {
		$empty_date = '0000-00-00 00:00:00';
		$date_fields = array(
			'post_date',
			'post_date_gmt',
			'post_modified',
			'post_modified_gmt',
		);
		$data = array_merge(
			$data,
			wp_slash( array_fill_keys( $date_fields, $empty_date ) )
		);
		return $data;
	}
    
    public function cl_save_page_content(){

		check_ajax_referer( 'codeless_builder', 'nonce' );

    	$data = isset( $_POST['data'] ) ? codeless_wp_kses_post_array( $_POST['data'] ) : array();
    	
    	foreach($data as $post_id => $content){
    		
    		if( $post_id == 'changeset' )
    			continue;

    		$my_post = array(
		      'ID'           => sanitize_key( $post_id ),
		      //'post_title'   => 'Updated',
		      'post_content' => $content,
			);
			$post = wp_update_post($my_post);
    	}
    	
    	
    	wp_send_json_success();
    }
    
    
    public function register_content_setting($wp_customize){
    	
		$wp_customize->add_setting( 'cl_content_settings_updated' , array(
		    'default' => '',
		    'transport' => 'postMessage'
		) );
		
		$wp_customize->add_setting( 'cl_page_content' , array(
		    'default' => '',
		    'transport' => 'postMessage'
		) );

		$wp_customize->add_setting( 'cl-style-clipboard' , array(
		    'default' => '',
		    'transport' => 'postMessage'
		) );

		$wp_customize->add_setting( 'cl-element-clipboard' , array(
		    'default' => '',
		    'transport' => 'postMessage'
		) );
		
			
		//return $wp_customize;
    }
    
    public function addToMapper($attrs){
        Cl_Builder_Mapper::map($attrs['tag'], $attrs);
    }
    
    
    public function parseEditableContent( $post ) {
    	if($post->ID != get_the_ID() )
	    	$post = get_post( get_the_ID() );
	    
		
    	if( $post->post_type == 'post' && codeless_get_post_style() != 'custom' )
    		return false;
			
		if ( (int) $post->ID > 0 && ! defined( 'CL_LOADING_EDITABLE_CONTENT' )) {
			
			
			define( 'CL_LOADING_EDITABLE_CONTENT', 1 );

			remove_filter( 'the_content', 'wpautop' );
		
			ob_start();

			$content = $post->post_content;
			
			
			$this->getPageShortcodesByContent( $content );
			

			$post->post_content = ob_get_clean();
			$this->post_content = $post->post_content;


			ob_start();
			

			if(is_customize_preview()){
				cl_include_template( 'post_shortcodes.tpl.php', array( 'cl_page_builder' => $this ) );
				cl_include_template( 'dialog.tpl.php', array( 'cl_page_builder' => $this ) );
				cl_include_template( 'cl_row-video.tpl.php', array( 'cl_page_builder' => $this ) );
			}
			$post_shortcodes = ob_get_clean();

			wp_reset_postdata();
			wp_reset_query();
			$GLOBALS['cl_post_content'] =  $post_shortcodes;
			
		}
	}
    
    public function pageHeaderContent(){
    	return $this->page_header;
    }

	public function buildPredefinedList(){
		foreach ( Cl_Builder_Mapper::getShortcodes() as $tag => $attrs ) {
			if ( isset( $attrs['predefined']) ) {

				foreach($attrs['predefined'] as $pre_id => $pre){
					$content = $pre['content'];
					preg_match_all( '/' . self::shortcodesRegexp() . '/', trim( $content ), $found );
					
					if ( count( $found[2] ) === 0 ) {
						return $is_container && strlen( $content ) > 0 ? $this->parseShortcodesString( '[cl_text]' . $content . '[/cl_text]', false, false ) : $content;
					}
					foreach ( $found[2] as $index => $s ) {
						$id = md5( time() . '-' . $this->tag_index ++ );
						$content = $found[5][ $index ];
						$shortcode = array(
							'tag' => $s,
							'attrs_query' => $found[3][ $index ],
							'attrs' => shortcode_parse_atts( $found[3][ $index ] ),
							'id' => $id,
							'parent_id' => false,
						);
						if ( false !== Cl_Builder_Mapper::getParam( $s, 'content' ) ) {
							if( is_array( $shortcode['attrs'] ) )
								$shortcode['attrs']['content'] = $content;
							else
								$shortcode['attrs'] = array( 'content' => $content );
						}

						Cl_Builder_Mapper::editPredefined( $tag, $pre_id, rawurlencode( json_encode( $shortcode ) ) );
						
					}
				}
			}
		}
	}

	public function buildContentBlocks(){
		$content_blocks = get_posts( array('post_type' => 'content_block', 'posts_per_page' => -1, 'order' => 'asc') );
		if ( ! empty( $content_blocks ) && ! is_wp_error( $content_blocks ) ){
			foreach($content_blocks as $block){
				$this->content_blocks[$block->ID] = array( 'id' => $block->ID, 'name' => $block->post_title, 'content' => array() );

				$this->buildContentBlock($block->ID, $block->post_content);
			}
		}
	}

	public function buildContentBlock($content_id, $content, $is_container = false, $parent_id = false){
		
		preg_match_all( '/' . self::shortcodesRegexp() . '/', trim( $content ), $found );
					
		if ( count( $found[2] ) === 0 ) {
		return $is_container && strlen( $content ) > 0 ? $this->parseShortcodesString($content_id,  '[cl_text]' . $content . '[/cl_text]', false, $parent_id ) : $content;
					}
		$ii = 0;  
		foreach ( $found[2] as $index => $s ) {
			$ii++;
			$id = md5( time() . '-' . $this->tag_index ++ );
			$content = $found[5][ $index ];
			$shortcode = array(
							'tag' => $s,
							'attrs_query' => $found[3][ $index ],
							'attrs' => shortcode_parse_atts( $found[3][ $index ] ),
							'id' => $id,
							'parent_id' => $parent_id,
			);

			if( $ii == 1 && $parent_id == false && $s == 'cl_row' )
				$this->content_blocks[$content_id]['type'] = 'cl_row';

			if( $ii == 1 && $parent_id == false && $s == 'cl_column' )
				$this->content_blocks[$content_id]['type'] = 'cl_column';

			if ( false !== Cl_Builder_Mapper::getParam( $s, 'content' ) ) {
				if( is_array( $shortcode['attrs'] ) )
					$shortcode['attrs']['content'] = $content;
				else
					$shortcode['attrs'] = array( 'content' => $content );
			}

			$this->content_blocks[$content_id]['content'][] = rawurlencode( json_encode( $shortcode ) );
			
			$shortcode_obj = $this->getShortCode( $shortcode['tag'] );
			$is_container = $shortcode_obj->settings( 'is_container' ) || ( null !== $shortcode_obj->settings( 'as_parent' ) && false !== $shortcode_obj->settings( 'as_parent' ) );
			
			if( $is_container )
				$this->buildContentBlock( $content_id, $content, $is_container, $shortcode['id'] ) ;		
		}
	}

    
    public function codeless_builder_page_header_content( $content ) {
		$string = '';
		preg_match_all( '/' . self::shortcodesRegexp() . '/', trim( $content ), $found );
		
		Cl_Builder_Mapper::addShortcodes();


		if ( count( $found[2] ) === 0 ) {
			return $is_container && strlen( $content ) > 0 ? $this->parseShortcodesString( '[cl_text]' . $content . '[/cl_text]', false, $parent_id ) : $content;
		}
		foreach ( $found[2] as $index => $s ) {
			$id = md5( time() . '-' . $this->tag_index ++ );
			$content = $found[5][ $index ];
			$shortcode = array(
				'tag' => $s,
				'attrs_query' => $found[3][ $index ],
				'attrs' => shortcode_parse_atts( $found[3][ $index ] ),
				'id' => $id,
				'parent_id' => false,
			);
			if ( false !== Cl_Builder_Mapper::getParam( $s, 'content' ) ) {
				if( is_array( $shortcode['attrs'] ) )
					$shortcode['attrs']['content'] = $content;
				else
					$shortcode['attrs'] = array( 'content' => $content );
			}			

			$this->post_shortcodes[] = rawurlencode( json_encode( $shortcode ) );
			
			$string .= $this->toString( $shortcode, $content );
		}

		return do_shortcode( $string );
	}
    
    
    public function printPostShortcodes() {
        
		echo isset( $GLOBALS['cl_post_content'] ) ? $GLOBALS['cl_post_content'] : '';
	}
    
    
    public static function shortcodesRegexp() {
		$tagnames = array_keys( Cl_Builder_Mapper::getShortcodes() );
		return get_shortcode_regex( $tagnames );

	}
	
	
    
    function getPageShortcodesByContent( $content ) {

		
		$cl_page_content = get_theme_mod('cl_page_content');

		if( isset($_GET['customize_changeset_uuid']) && isset($cl_page_content['changeset']) && $_GET['customize_changeset_uuid'] == $cl_page_content['changeset'] && isset( $cl_page_content[ get_the_ID() ] ) )
			$content = $cl_page_content[ get_the_ID() ];

		//echo $content;
		
		$content = shortcode_unautop( trim( $content ) );
	
		$not_shortcodes = preg_split( '/' . self::shortcodesRegexp() . '/', $content );
		
		foreach ( $not_shortcodes as $string ) {
			$temp = str_replace( array(
				'<p>',
				'</p>',
			), '', $string );
			if ( strlen( trim( $temp ) ) > 0  ) {
				$content = preg_replace( '/(' . preg_quote( $string, '/' ) . '(?!\[\/))/', '[cl_row][cl_column width="1/1"][cl_text]$1[/cl_text][/cl_column][/cl_row]', $content );
			}
		}

		echo $this->parseShortcodesString( $content );
	}
	
	

	/**
	 * @param $content
	 * @param bool $is_container
	 * @param bool $parent_id
	 *
	 * @since 1.0.0
	 * @return string
	 */
	function parseShortcodesString( $content, $is_container = false, $parent_id = false) {
		$string = '';
		preg_match_all( '/' . self::shortcodesRegexp() . '/', trim( $content ), $found );
		
		Cl_Builder_Mapper::addShortcodes();


		if ( count( $found[2] ) === 0 ) {
			return $is_container && strlen( $content ) > 0 ? $this->parseShortcodesString( '[cl_text]' . $content . '[/cl_text]', false, $parent_id ) : $content;
		}
		foreach ( $found[2] as $index => $s ) {
			$id = md5( time() . '-' . $this->tag_index ++ );
			$content = $found[5][ $index ];
			$shortcode = array(
				'tag' => $s,
				'attrs_query' => $found[3][ $index ],
				'attrs' => shortcode_parse_atts( $found[3][ $index ] ),
				'id' => $id,
				'parent_id' => $parent_id,
			);
			if ( false !== Cl_Builder_Mapper::getParam( $s, 'content' ) ) {
				if( is_array( $shortcode['attrs'] ) )
					$shortcode['attrs']['content'] = $content;
				else
					$shortcode['attrs'] = array( 'content' => $content );
			}
			if( $s != 'cl_page_header' )
				$this->post_shortcodes[] = rawurlencode( json_encode( $shortcode ) );
			
			
			$string .= $this->toString( $shortcode, $content );
		}
		
		return $string;
	}
	
	
	function getShortCode($tag){
		return Cl_Shortcode_Manager::getInstance()->setTag( $tag );
	}


	function toString( $shortcode, $content ) {
		$shortcode_obj = $this->getShortCode( $shortcode['tag'] );
		$is_container = $shortcode_obj->settings( 'is_container' ) || ( null !== $shortcode_obj->settings( 'as_parent' ) && false !== $shortcode_obj->settings( 'as_parent' ) );
		
		
		$output = ( '<div class="cl_element '.esc_attr( $shortcode_obj->getElementClass($shortcode['tag'])->getBackendClasses() ).'" data-tag="' . esc_attr( $shortcode['tag'] ) . '" data-shortcode-controls="' . esc_attr( json_encode( $shortcode_obj->getElementClass($shortcode['tag'])
		                                                                                                                                       ->getControlsList() ) ) . '" data-model-id="' . esc_attr( $shortcode['id'] ). '">[' . $shortcode['tag'] . ' ' . $shortcode['attrs_query'] . ']' . ( $is_container ?  $this->parseShortcodesString( $content, $is_container, $shortcode['id'] ) : do_shortcode( $content ) ) . '[/' . $shortcode['tag'] . ']</div>' );
		                                                                                                                                   
		//$output = $shortcode['tag'];
		return $output;
	}
	
	function cl_test_do_shortcode( $content ){
		return;
	}

	function load_shortcode_ajax(){
		
		
		if(isset($_POST['action']) && $_POST['action'] == 'cl_load_shortcode'){
			
			Cl_Builder_Mapper::addShortcodes();
			$output = ob_start();
			$shortcodes = isset( $_POST['shortcodes'] ) ? codeless_wp_kses_post_array( $_POST['shortcodes'] ) : array();
			
			foreach ( $shortcodes as $shortcode ) {
				if ( isset( $shortcode['id'] ) && isset( $shortcode['string'] ) ) {
					if ( isset( $shortcode['tag'] ) ) {
						$shortcode_obj = $this->getShortCode( $shortcode['tag'] );
						if ( is_object( $shortcode_obj ) ) {
							
	//						$output .= '<div data-type="element" data-model-id="' . $shortcode['id'] . '">';
							$is_container = $shortcode_obj->settings( 'is_container' ) || ( null !== $shortcode_obj->settings( 'as_parent' ) && false !== $shortcode_obj->settings( 'as_parent' ) );
							
							echo '<div class="cl_element '.$shortcode_obj->getElementClass($shortcode['tag'])->getBackendClasses().'" data-shortcode-controls="' . esc_attr( json_encode( $shortcode_obj->getElementClass($shortcode['tag'])
			                                                                                                                                       ->getControlsList() ) ). '" data-model-id="' . esc_attr( $shortcode['id'] ) . '">';
			                echo	do_shortcode(  stripslashes($shortcode['string'])  ) ;
			                echo '</div>';
	//						$output .= '</div>';
						}
					}
				}
			}
			
			$output = ob_get_clean();
			die($output);
		}
		
	}
	
	function prepend_element_html($content){
		$new_content = '<div class="add-element-prepend app-prepend"></div>';
		if( $content == '' )
			$new_content .= '<div class="add-first-element">Add Element</div>';
		return $new_content.$content;

	}
	
	function append_element_html($content){
		
		if( ! is_page() && ! is_single() )
			return false;
			
		$append = '<div class="add-element-append app-append"></div>';
		return $content.$append;
	}

	function prepend_content_wrapper($content){
		$new_content = '<div class="codeless-content" data-codeless="true">';
		return $new_content.$content;

	}
	
	function append_content_wrapper($content){
		
		if( ! is_page() && ! is_single() )
			return false;
			
		$append = '</div><!-- .codeless-content -->';
		return $content.$append;
	}
	
	
}


?>