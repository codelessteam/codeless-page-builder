<?php

/**
 * Used to configure all Customizer, load needed files and config
 * 
 * @package  Kirki Setup Codeless Buiolder
 * @subpackage Framework
 * @version 1.6
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
    exit;
}

// Start Class
if( !class_exists( 'Cl_Kirki_Setup' ) ) {
    
    class Cl_Kirki_Setup {
        
        public function __construct() {
            
            if(class_exists('Kirki_Fonts_Google'))
                Kirki_Fonts_Google::$force_load_all_variants = true;

            $this->load_custom_codeless_kirki();
            

            // Load Customizer Controls Pane Scripts
            add_action( 'customize_controls_enqueue_scripts', array(
                 &$this,
                'register_customizePane_scripts' 
            ) );


            // Register New Panel/Section Types
            add_action( 'customize_register', array(
                 &$this,
                'register_custom_types' 
            ) );
            add_filter( 'kirki/panel_types', array(
                 &$this,
                'load_custom_panel' 
            ) );
            add_filter( 'kirki/section_types', array(
                 &$this,
                'load_custom_section' 
            ) );
            
            
            add_action( 'customize_register', array(
                 &$this,
                'remove_section_on_simple' 
            ), 9999 );
            
            Kirki::add_section( 'cl_codeless_page_builder', array(
                'title'          => esc_html__( 'Page Builder', 'codeless-builder' ),
                'description'    => esc_html__( 'Options for adding an additional element on header', 'codeless-builder' ),
                'panel'          => '',
                'type'			 => '',
                'priority'       => 160,
                'capability'     => 'edit_theme_options'
            ) );

            Kirki::add_field('cl_folie', array(
                'settings' => 'footer_des',
                'label' => esc_html__('Footer Box Design', 'codeless-builder') ,
                'section' => 'cl_codeless_page_builder',
                'type' => 'clelement',
                'priority' => 1,
                'default' => '',
                'into_group' => true,
                'transport' => 'postMessage',
            ));
            
        }


        public function register_custom_types() {
            global $wp_customize;
            $wp_customize->register_panel_type( 'WP_Customize_Codeless_Panel' );
            $wp_customize->register_section_type( 'WP_Customize_Codeless_Section' );
        }
        
        
        public function load_custom_panel( $panels ) {
            require_once cl_path_dir( 'KIRKI_SETUP', 'codeless_custom_panel.php' );
            $panels['codeless'] = 'WP_Customize_Codeless_Panel';
            return $panels;
        }
        
        public function load_custom_section( $sections ) {
            require_once cl_path_dir( 'KIRKI_SETUP', 'codeless_custom_section.php' );
            $sections['codeless'] = 'WP_Customize_Codeless_Section';
            return $sections;
        }


        function load_custom_codeless_kirki(){
            require_once cl_path_dir( 'KIRKI_SETUP', 'codeless_controls.php' );
        }

        
        
        function register_customizePane_scripts() {
            
            wp_enqueue_style(
                'codeless-customizer-styles',
                cl_asset_url(  'css/kirki-styles.css' )
            );
        }

        
        public function remove_section_on_simple() {
            global $wp_customize;
            if( isset( $_GET['mode'] ) && $_GET['mode'] == 'simple' ) {
                //$wp_customize->remove_section( 'title_tagline' );
                $wp_customize->remove_section( 'colors' );
                $wp_customize->remove_section( 'header_image' );
                $wp_customize->remove_section( 'background_image' );
                $wp_customize->remove_section( 'static_front_page' );
                $wp_customize->remove_section( 'themes' );
            }
        } 
    }
}

?>