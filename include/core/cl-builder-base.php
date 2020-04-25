<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Cl_Builder_Base{
    
    
    
    function init(){
        
        if( ! class_exists('Kirki') )
            return;

        add_action( 'customize_preview_init', array(&$this, 'preview_init_enqueue' ));
        add_action( 'customize_controls_enqueue_scripts', array(&$this, 'pane_init_enqueue') );
        
        add_action('wp_enqueue_scripts', array(&$this, 'register_global_styles') );
    	add_action('wp_enqueue_scripts', array( &$this,'register_global_scripts') );
        add_action('admin_enqueue_scripts', array(&$this, 'register_admin_styles') );
        add_filter( 'customize_dynamic_setting_args', array( &$this, 'filter_customize_dynamic_setting_args' ), 10, 2 );

        add_action('page_row_actions', array(&$this, 'add_link_post_edit'), 999, 2 );
        add_action('post_row_actions', array(&$this, 'add_link_post_edit'), 999, 2 );
        add_action('edit_form_after_title', array(&$this, 'add_edit_form_buttons'), 999);
        add_action('customize_controls_print_scripts', array(&$this, 'close_customize_sidebar'), 999);
        //add_action('customize_controls_print_footer_scripts', array(&$this, 'add_loading_overlay'), 1);

        //add_action( 'codeless_hook_custom_post_end', array(&$this, 'add_custom_post_button'), 1, 2 );
        //add_action( 'codeless_hook_custom_post_loop_end', array(&$this, 'add_new_custom_post_button'), 1, 2 );

        add_action( 'wp_footer', array(&$this, 'add_sticky_panel_ui') );

    	Cl_Builder_Mapper::setInit();
        $this->load_page_elements();

    	    
    	add_action( 'template_redirect', array(
    		'Cl_Builder_Mapper',
    		'addShortcodes',
    	) );
    		
    	add_filter( 'the_content', array(
    		&$this,
    		'fixPContent',
    	), 11 );
    }

    function filter_customize_dynamic_setting_args($args, $setting_id){
        if ( preg_match( '/(?<cl_element>[^\]]+)\[.*\]$/', $setting_id, $matches ) ) {
            $sc = Cl_Builder_Mapper::getShortCode($matches['cl_element']);
            $hc = Cl_Builder_Mapper::getHeaderElement($matches['cl_element']);
            if( is_null( $sc ) && is_null($hc) )
                return $args;

			if ( false === $args ) {
				$args = array();
            }
            
			$args['type'] = 'theme_mod';
            $args['transport'] = 'postMessage';
        }
        return $args;
    }

    public function load_page_elements(){
        require_once cl_path_dir( 'CONFIG_DIR', 'cl-page-elements.php' );
        if( is_file( cl_path_dir( 'THEME_CODELESS_CONFIG', 'cl-page-elements.php' ) ) )
            require_once cl_path_dir( 'THEME_CODELESS_CONFIG', 'cl-page-elements.php' );
            
    }
    
    public function add_edit_form_buttons( $post ){
        $customize_url = add_query_arg( 'clactive', 1, get_permalink($post->ID) );

        $customize_url = add_query_arg( 'url', urlencode($customize_url), wp_customize_url() );

        $customize_url = add_query_arg( 'clactive', 1, $customize_url );

        echo '<a href="'.esc_url( $customize_url ).'" class="cl-edit-button">Codeless Builder</a>';
        
    }

    
    function preview_init_enqueue(){
        global $cl_builder;
/*       
        wp_enqueue_script( 'dragula', cl_asset_url('js/dragula.min.js') );
        
        wp_enqueue_script( 'cl-shortcode', cl_asset_url('js/cl-shortcodes.js'), array('cl-helper-functions', 'backbone', 'underscore', 'shortcode', 'jquery-ui-sortable', 'jquery-ui-droppable') );
        wp_enqueue_script( 'cl-shortcodes-builder', cl_asset_url('js/cl-shortcodes-builder.js'), array('cl-shortcode') );
        
        wp_enqueue_script( 'cl-header-elements', cl_asset_url('js/cl-header-elements.js'), array('cl-shortcode') );
        wp_enqueue_script( 'cl-header-builder', cl_asset_url('js/cl-header-builder.js'), array('cl-header-elements') );
        
        wp_enqueue_script( 'cl-codeless-app', cl_asset_url('js/cl-codeless-app.js'), array('cl-shortcodes-builder', 'cl-header-builder') );
        wp_enqueue_script( 'cl-main', cl_asset_url('js/cl-main.js'), array('cl-codeless-app') );
        
        wp_enqueue_script( 'cl-helper-functions', cl_asset_url('js/cl-helper-functions.js') );

*/      
        wp_enqueue_script( 'cl-lazyload', cl_asset_url('js/cl-lazyload.js') );
        wp_enqueue_script( 'cl-builder', cl_asset_url('js/cl-builder.js'), array('backbone', 'underscore', 'customize-preview', 'shortcode', 'jquery-ui-sortable', 'jquery-ui-droppable') );

        wp_enqueue_script( 'cl-editor-exts', cl_asset_url('js/medium-editor/cl-editor-exts.js'));
        wp_enqueue_script( 'medium-editor', cl_asset_url('js/medium-editor/medium-editor.min.js'), array('cl-editor-exts') );
        wp_enqueue_script( 'a11y-dialog', cl_asset_url('js/cl-a11y-dialog.min.js') );
        
        wp_enqueue_style( 'medium-editor', cl_asset_url('css/medium-editor/medium-editor.min.css') );
        wp_enqueue_style( 'medium-editor-theme', cl_asset_url('css/medium-editor/beagle.min.css') );
        wp_enqueue_style( 'cl-builder', cl_asset_url('css/cl-builder.css') );
        wp_enqueue_style( 'cl-icons', cl_asset_url('css/icons/icons.css') );
        wp_enqueue_style( 'cl-builder-icons', cl_asset_url('css/codeless-builder-icons.css') );
        wp_localize_script(
            'cl-builder',
            'scriptData',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'ajax_nonce' => wp_create_nonce( 'codeless_builder' ),
                'active_builder' => $cl_builder->is_page_builder_active()
            )
        );
    }

    
    
    function pane_init_enqueue(){
        global $cl_builder;
        wp_enqueue_script( 'codeless-cssbox', cl_asset_url( 'js/kirki-new.js' ), array( 'kirki-script' ) );

			wp_localize_script( 'codeless-cssbox', 'codelessPalettes', codeless_builder_generate_palettes() );

			if( class_exists('Cl_Builder_Mapper') ){
				wp_localize_script( 'codeless-cssbox', 'codelessElementsMap', Cl_Builder_Mapper::getShortCodes() );
				wp_localize_script( 'codeless-cssbox', 'codelessHeaderElementsMap', Cl_Builder_Mapper::getHeaderElements() );
			}
        wp_enqueue_script( 'cl-customize-pane', cl_asset_url('js/cl-customize-pane.js'), array( 'customize-controls', 'jquery', 'codeless-cssbox' ) );
		

        wp_localize_script(
            'cl-customize-pane',
            'scriptData',
            array(
                'active_builder' => $cl_builder->is_page_builder_active()
            )
        );
    }
    
    
    public function register_global_styles(){
        wp_register_style( 'cl-elements-inline', false );
        
        if( ! apply_filters( 'codeless_builder_custom_bootstrap_css', false ) )
            wp_enqueue_style( 'codeless-builder-bootstrap', cl_asset_url('css/cl-bootstrap.css') );

        if( ! apply_filters( 'codeless_builder_custom_frontend_css', false ) )
            wp_enqueue_style( 'codeless-builder-frontend', cl_asset_url('css/cl-frontend.css') );

        if( ! apply_filters( 'codeless_builder_custom_icon_pack', false ) )
            wp_enqueue_style( 'codeless-icons', cl_asset_url('css/codeless-icons.css') );

        //wp_enqueue_style( 'cl-front-site', cl_asset_url('css/cl-front-site.css') );
    }
    
    public function register_global_scripts(){
        
        //wp_enqueue_script( 'waypoints', cl_asset_url('js/front_libraries/waypoints.min.js') );
        if( ! apply_filters( 'codeless_builder_custom_front_js', false ) ){
            wp_enqueue_script( 'cl-front-end', cl_asset_url('js/cl-front-end.js'), array( 'jquery' ) );
            wp_localize_script( 'cl-front-end', 'cl_builder_global', $this->load_global_vars() );
        }

    }
    
    public function load_global_vars(){
    	return $array = array(
    		
    		'FRONT_LIB_JS' => cl_asset_url('js/front_libraries/')
    		
    	);
    }
    
    public function fixPContent( $content = null ) {
		if ( $content ) {
			$s = array(
				'/' . preg_quote( '</div>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
				'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<div ', '/' ) . '/i',
				'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<section ', '/' ) . '/i',
				'/' . preg_quote( '</section>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
			);
			$r = array(
				'</div>',
				'<div ',
				'<section ',
				'</section>',
			);
			$content = preg_replace( $s, $r, $content );

			return $content;
		}

		return null;
	}

    public function register_admin_styles($hook){
        
        if ( 'post.php' != $hook && 'post-new.php' != $hook )
            return false;

        wp_enqueue_style( 'cl-admin-style', cl_asset_url('css/cl-admin-style.css'), false, '1.0.0' );
        wp_enqueue_style( 'cl-codeless-icons', get_template_directory_uri() . '/css/codeless-icons.css', false, '1.0.0' );
    }

    public function add_link_post_edit( $actions, $post ){
        $can_edit_post = current_user_can( 'edit_post', $post->ID );

        $customize_url = add_query_arg( 'clactive', 1, get_permalink($post->ID) );
        $customize_url = add_query_arg( 'url', urlencode($customize_url), wp_customize_url() );

        $customize_url = add_query_arg( 'clactive', 1, $customize_url );

        $actions['customize_link'] = sprintf(
            '<a href="%s" aria-label="%s">%s</a>',
            $customize_url,
            /* translators: %s: post title */
            esc_attr( sprintf( __( 'Edit Codeless Builder &#8220;%s&#8221;' ), $post->post_title ) ),
            __( 'Edit with Codeless Builder' )
        );

        return $actions;
    }

    function close_customize_sidebar(){
        ?>
        <style type="text/css">
            .cl-loading-overlay{
              position:fixed;
              left:0;
              top:0;
              z-index:999999999;
              background:#eee;
              width:100%;
              height:100%;
            }
        </style>
        <script type="text/javascript">
            ( function( $, window ) {
            
                $(document).ready(function(){

                    var sPageURL = decodeURIComponent(window.location.search.substring(1));
                    if( sPageURL.indexOf('mode=simple') !== -1  ){
                        $('.wp-full-overlay').removeClass('expanded').addClass('collapsed preview-only');
                        $('.collapse-sidebar.button').attr('aria-expanded', false);
                        $('.wp-full-overlay').addClass('cl-simple-mode');
                    }
                    

                });
              
            }( jQuery, window ));

        </script>
        <?php
    }

    function add_loading_overlay(){
        
                            
                            
        $tips = array(
            '<div class="tip show">
                <span class="title"><span class="blue">Useful Tip:</span> Keep SHIFT pressed for editing spaces.</span>
                <p>Keep SHIFT pressed on elements that you want to modify the spaces (padding, margin). Then Drag UP-DOWN or LEFT-RIGHT to modify spaces in pixels. It\'s so easy and playful.</p>
                <img src="'.cl_asset_url("images/loading2.jpg").'" />
            </div>',

            '<div class="tip show">
                <span class="title"><span class="blue">Useful Tip:</span> Click On Text direct modification</span>
                <p>Click on every text that you want to modify. A toolkit for formatting text will appear. Use it, start Inline Editing your pages now!</p>
                <img src="'.cl_asset_url("images/loading1.jpg").'" />
            </div>',

            '<div class="tip show">
                <span class="title"><span class="blue">Useful Tip:</span> Inline Icon Select, click on Icon</span>
                <p>Click on icon and a new box with all icons will be shown. Will change directly after click. In this way you can select the best one for your purpose!</p>
                <img src="'.cl_asset_url("images/loading3.jpg").'" />
            </div>'
        );

        ?>

        <div class="cl-loading-overlay">
            <div class="inner">
                <div class="center">
                    <div class="content">
                        <h2>Hey! Welcome to Codeless Builder!</h2>
                        <div class="tips">
                            
                            <?php 
                                $index = rand(0, count($tips) - 1 );
                                echo esc_html( $tips[$index] );
                            ?>
                            
                            
                        </div>

                    </div>

                    <span class="loading style-3"></span>
                </div>
            </div>
        </div>

        <?php
    }


    function add_custom_post_button($type, $id){

        if( is_customize_preview() ){

            echo '<a href="#" class="cl-custom-post-button" data-type="'.esc_attr( $type ).'" data-id="'.esc_attr( $id ).'"></a>';
        }
    }

    function add_new_custom_post_button($type){

        if( is_customize_preview() ){

            echo '<a href="#" class="cl-add-custom-post-button" data-type="'.esc_attr( $type ).'"></a>';
        }
    }


    function add_sticky_panel_ui(){
        if( is_customize_preview() ){

            echo '<div class="cl-sticky-panel">';

                echo '<a href="#" id="cl-nav-styling" data-tooltip="Global Styling" class="cl-sticky-panel-btn"><i class="cl-builder-icon-global"></i></a>';
                echo '<a href="#" id="cl-nav-preview" data-tooltip="Live Page" class="cl-sticky-panel-btn"><i class="cl-builder-icon-link3"></i></a>';
            
            echo '</div>';
        }
    }
        
   
    
}


?>