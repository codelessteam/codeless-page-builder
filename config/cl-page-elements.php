<?php

/* Row */
	
cl_builder_map(array(
    'type'        => 'clelement',
    'label'       => esc_attr__( 'Row', 'codeless-builder' ),
    'section'     => 'cl_codeless_page_builder',
    'tooltip' => 'Manage all options of the selected Row',
    //'priority'    => 10,
    'icon'		  => 'icon-software-layout',
    'transport'   => 'postMessage',
    'paddingPositions' => array('top', 'bottom'),
    'settings'    => 'cl_row',
    'is_container' => true,
    'is_root'	  => true,
    'fields' => array(
        
        
        'element_tabs' => array(
            'type' => 'show_tabs',
            'default' => 'general',
            'tabs' => array(
                'general' => array( 'General', 'cl-icon-settings' ),
                'design' => array( 'Design', 'cl-icon-tune' ),
                'animation' => array( 'Animation', 'cl-icon-animation' ),
                'responsive' => array( 'Responsive', 'cl-icon-responsive' )
            )
        ),

            'general_tab_start' => array(
                'type' => 'tab_start',
                'label' => 'General',
                'tabid' => 'general'
            ),
            
                /* ----------------------------------------------- */
                
                'row_layout_start' => array(
                    'type' => 'group_start',
                    'label' => 'Layout',
                    'groupid' => 'layout'
                ),
                    
                    
            
                    'row_type' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Type', 'codeless-builder' ),
                        'default'     => 'container',
                        'choices' => array(
                            'container' => 'Into Container',
                            'container-fluid' => 'Stretch Content'
                        ),
                        'selector' => '.cl-row > .container, .cl-row > .container-fluid',
                        'selectClass' => ' '
                    ),

                    'fullheight' => array(
                        'type'        => 'switch',
                        'label'       => esc_html__( 'Full Height Row', 'codeless-builder' ),
                        'default'     => 0,
                        'priority'    => 10,
                        'choices'     => array(
                            'on'  => esc_attr__( 'On', 'codeless-builder' ),
                            'off' => esc_attr__( 'Off', 'codeless-builder' ),
                        ),
                        'selector' => '.cl-row > div > .row',
                        'addClass' => 'cl_row-fullheight cl_row-flex'
                    ),
                    
                    
                    
                    'content_pos' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Content Position (Fullheight)', 'codeless-builder' ),
                        'tooltip' => esc_attr__( 'Change position of columns and elements into the fullheight Row. Works on Fullheight', 'codeless-builder' ),
                        'default'     => 'middle',
                        'choices' => array(
                            'middle' => 'Middle',
                            'top' => 'Top',
                            'bottom' => 'Bottom',
                            'stretch' => 'Stretch'
                        ),
                        'selector' => '.cl-row > div > .row',
                        'selectClass' => 'cl_row-cp-',
                        
                        
                        
                    ),

                    'custom_width_bool' => array(
                        'type'        => 'switch',
                        'label'       => esc_html__( 'Custom Container Width', 'codeless-builder' ),
                        'tooltip' => 'Switch on to add a custom width for container only for this row. Switch Off to leave the default container width.',
                        'default'     => 0,
                        'priority'    => 10,
                        'choices'     => array(
                            'on'  => esc_attr__( 'On', 'codeless-builder' ),
                            'off' => esc_attr__( 'Off', 'codeless-builder' ),
                        ),

                        'selector' => '.cl-row',
                        'addClass' => 'cl-row--custom-width'

                        
                    ),

                    'custom_width' => array(
                        'type'     => 'slider',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Custom Container Width', 'codeless-builder' ),
                        'tooltip' => esc_attr__( 'Is applied only for media screen (min-width: 1200px)', 'codeless-builder' ),
                        'default'     => get_theme_mod('layout_container_width', 1100),
                        'choices'     => array(
                            'min'  => '0',
                            'max'  => '1600',
                            'step' => '10',
                        ),
                        'suffix' => 'px',
                        'selector' => '.cl-row.cl-row--custom-width > .container-content',
                        'css_property' => 'width',
                        'media_query' => '(min-width: 1200px)',
                        'cl_required'    => array(
                            array(
                                'setting'  => 'custom_width_bool',
                                'operator' => '==',
                                'value'    => 1,
                            ),
                        ),
                    ),

                'row_layout_end' => array(
                    'type' => 'group_end',
                    'label' => 'Row Layout',
                    'groupid' => 'layout'
                ),

                /* ----------------------------------------------------- */
            
                'columns_start' => array(
                    'type' => 'group_start',
                    'label' => 'Columns',
                    'groupid' => 'columns'
                ),
                    
                    
                    'columns_gap' => array(
                        'type'     => 'slider',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Columns Gap', 'codeless-builder' ),
                        'default'     => '15',
                        'choices'     => array(
                            'min'  => '0',
                            'max'  => '35',
                            'step' => '1',
                        ),
                        'suffix' => 'px',
                        'selector' => '.row > .cl_cl_column > .cl_column, .row > .cl_column',
                        'css_property' => array('padding-left', 'padding-right')
                    ),
                    
                    
                    'equal_height' => array(
                        'type'        => 'switch',
                        'label'       => esc_html__( 'Equal Columns Height', 'codeless-builder' ),
                        'default'     => '0',
                        'priority'    => 10,
                        'choices'     => array(
                            'on'  => esc_attr__( 'On', 'codeless-builder' ),
                            'off' => esc_attr__( 'Off', 'codeless-builder' ),
                        ),
                        'selector' => '.cl-row > div > .row',
                        'addClass' => 'cl_row-equal_height cl_row-flex'
                    ), 

                    'col_responsive' => array(
                        'type'        => 'inline_select',
                        'label'       => esc_html__( 'Responsive Columns', 'codeless-builder' ),
                        'tooltip' => 'This option will change the width of columns on tablets sizes from (768px to 992px). Important option to build responsive perfect layouts.',
                        'default'     => 'none',
                        'priority'    => 10,
                        'choices'     => array(
                            'none' => 'None',
                            'full'  => esc_attr__( 'Fullwidth Columns', 'codeless-builder' ),
                            'half' => esc_attr__( 'Half Width Columns', 'codeless-builder' ),
                            'one_third' => esc_attr__( 'One / Third Width Columns', 'codeless-builder' ),
                        ),
                        'selector' => '.cl-row > div > .row',
                        'selectClass' => 'cl-col-tablet-'
                    ),
                    
                'columns_end' => array(
                    'type' => 'group_end',
                    'label' => 'Columns',
                    'groupid' => 'columns'
                ),
                
                /* --------------------------------------------- */

                'row_info_start' => array(
                    'type' => 'group_start',
                    'label' => 'Attributes',
                    'groupid' => 'attr'
                ),
                
                    'row_disabled' => array(
                        'type'        => 'switch',
                        'label'       => esc_html__( 'Disable Row', 'codeless-builder' ),
                        'default'     => '0',
                        'priority'    => 10,
                        'choices'     => array(
                            'on'  => esc_attr__( 'On', 'codeless-builder' ),
                            'off' => esc_attr__( 'Off', 'codeless-builder' ),
                        ),
                        'selector' => '.cl-row',
                        'addClass' => 'disabled_row'
                    ),
                    
                    'row_id' => array(
                        
                        'type'     => 'text',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Row Id', 'codeless-builder' ),
                        'tooltip' => esc_attr__( 'This is useful when you want to add unique identifier to row.', 'codeless-builder' ),
                        'default'     => '',
                    ),
                    
                    'extra_class' => array(
                        'type'     => 'text',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Extra Class', 'codeless-builder' ),
                        'tooltip' => esc_attr__( 'Add extra class identifiers to this row, that can be used for various custom styles.', 'codeless-builder' ),
                        'default'     => '',
                    ),

                    'anchor_label' => array(
                        'type'     => 'text',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Anchor Label', 'codeless-builder' ),
                        'tooltip' => esc_attr__( 'Used on Vertical Codeless Slider', 'codeless-builder' ),
                        'default'     => '',
                        'selector' => '.cl-row',
                        'htmldata' => 'anchor'
                    ),
                    
        
                'row_info_end' => array(
                    'type' => 'group_end',
                    'label' => 'Attributes',
                    'groupid' => 'attr'
                ),
                
                /* --------------------------------------------- */

                /* ---------------------------------------------- */
        
            
                'video_start' => array(
                    'type' => 'group_start',
                    'label' => 'Video',
                    'groupid' => 'video'
                ),
                
                
                    'video' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Video Background', 'codeless-builder' ),
                        
                        'default'     => 'none',
                        'choices' => array(
                            'none'	=> 'None',
                            'self' =>	'Self-Hosted',
                            'youtube' =>	'Youtube',
                            'vimeo' => 'Vimeo'
                        ),
                        'customJS' => 'inlineEdit_videoSection'
                    ),
                    
                    'video_mp4' => array(
                        
                        'type'     => 'text',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Video Mp4', 'codeless-builder' ),
                        
                        'default'     => '',
                        'cl_required'    => array(
                            array(
                                'setting'  => 'video',
                                'operator' => '==',
                                'value'    => 'self',
                            ),
                        ),
                        'customJS' => 'inlineEdit_videoSection'
                    ),
                    'video_webm' => array(
                        
                        'type'     => 'text',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Video Webm', 'codeless-builder' ),
                        
                        'default'     => '',
                        'cl_required'    => array(
                            array(
                                'setting'  => 'video',
                                'operator' => '==',
                                'value'    => 'self',
                            ),
                        ),
                        'customJS' => 'inlineEdit_videoSection'
                    ),
                    'video_ogv' => array(
                        
                        'type'     => 'text',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Video Ogv', 'codeless-builder' ),
                        
                        'default'     => '',
                        'cl_required'    => array(
                            array(
                                'setting'  => 'video',
                                'operator' => '==',
                                'value'    => 'self',
                            ),
                        ),
                        'customJS' => 'inlineEdit_videoSection'
                    ),

                    
                    'video_youtube' => array(
                        
                        'type'     => 'text',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Youtube ID', 'codeless-builder' ),
                        
                        'default'     => '',
                        'cl_required'    => array(
                            array(
                                'setting'  => 'video',
                                'operator' => '==',
                                'value'    => 'youtube',
                            ),
                        
                        ),
                        'customJS' => 'inlineEdit_videoSection'
                    ),
                    
                    'video_vimeo' => array(
                        
                        'type'     => 'text',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Vimeo ID', 'codeless-builder' ),
                        
                        'default'     => '',
                        'cl_required'    => array(
                            array(
                                'setting'  => 'video',
                                'operator' => '==',
                                'value'    => 'vimeo',
                            ),
                        
                        ),
                        'customJS' => 'inlineEdit_videoSection'
                    ),
                    
                    'row_video_loop' => array(
                        'type'        => 'switch',
                        'label'       => esc_html__( 'Video Loop', 'codeless-builder' ),
                        'default'     => 0,
                        'priority'    => 10,
                        'choices'     => array(
                            'on'  => esc_attr__( 'On', 'codeless-builder' ),
                            'off' => esc_attr__( 'Off', 'codeless-builder' ),
                        ),
                        'cl_required'    => array(
                            array(
                                'setting'  => 'video',
                                'operator' => '!=',
                                'value'    => 'none',
                            ),
                        ),
                        'customJS' => 'inlineEdit_videoSection'
                    ),

                'video_end' => array(
                    'type' => 'group_end',
                    'label' => 'Video',
                    'groupid' => 'video'
                ),

            'general_tab_end' => array(
                'type' => 'tab_end',
                'label' => '',
                'tabid' => 'general'
            ),

            /*-------------------------------------------------------*/
        
        
            'design_tab_start' => array(
                'type' => 'tab_start',
                'label' => 'Design',
                'tabid' => 'design'
            ),
                
                /* ------------------------------------------ */
                
                'panel' => array(
                    'type' => 'group_start',
                    'label' => 'Box',
                    'groupid' => 'design_panel'
                ),
            
                    'css_style' => array(
                        'type' => 'css_tool',
                        'label' => 'Tool',
                        'selector' => '.cl-row',
                        'css_property' => '',
                        'default' => array('padding-top' => '45px', 'padding-bottom' => '45px')
                    ),
                    
                    'text_color' => array(
                        'type' => 'inline_select',
                        'label' => 'Text Color',
                        'default' => 'dark-text',
                        'choices' => array(
                            'dark-text' => 'Dark',
                            'light-text' => 'Light'
                        ),
                        'selector' => '.cl-row',
                        'selectClass' => ''
                    ),
                
                    
                'design_panel_end' => array(
                    'type' => 'group_end',
                    'label' => 'Animation',
                    'groupid' => 'design_panel'
                ),
                
                /* ------------------------------------------ */
            
                'background_color_group' => array(
                    'type' => 'group_start',
                    'label' => 'Background Color',
                    'groupid' => 'background_color_group'
                ),
                
                    'background_color' => array(
                        'type' => 'color',
                        'label' => 'Background Color',
                        'default' => '',
                        'selector' => '.cl-row > .bg-layer',
                        
                        'css_property' => 'background-color',
                        'alpha' => true
                    ),
                
                'background_color_group_end' => array(
                    'type' => 'group_end',
                    'label' => 'Background Color',
                    'groupid' => 'background_color_group'
                ),
                
                /* ------------------------------------------- */
                
                'background_image_group' => array(
                    'type' => 'group_start',
                    'label' => 'Background Image',
                    'groupid' => 'background_image_group'
                ),
                
                    'background_image' => array(
                        'type'        => 'image',
                        'label'       => '',
                        'default'     => '',
                        'priority'    => 10,
                        'selector' => '.cl-row > .bg-layer',
                        'css_property' => 'background-image',
                        'choices' => array(),
                    ),
                    
                    'background_position' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Background Position', 'codeless-builder' ),
                        
                        'default'     => 'left-top',
                        'choices' => array(
                            'left top' => 'left top',
                            'left center' => 'left center',
                            'left bottom' => 'left bottom',
                            'right top' => 'right top',
                            'right center' => 'right center',
                            'right bottom' => 'right bottom',
                            'center top' => 'center top',
                            'center center' => 'center center',
                            'center bottom' => 'center bottom',
                        ),
                        'selector' => '.cl-row > .bg-layer',
                        'css_property' => 'background-position',
                        
                    ),

                    'background_size' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Background Size', 'codeless-builder' ),
                        
                        'default'     => 'cover',
                        'choices' => array(
                            'cover' => 'Cover',
                            'auto' => 'auto',
                        ),
                        'selector' => '.cl-row > .bg-layer',
                        'css_property' => 'background-size',
                        
                    ),
                    
                    'background_repeat' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Background Repeat', 'codeless-builder' ),
                        
                        'default'     => 'no-repeat',
                        'choices' => array(
                            'repeat' => 'repeat',
                            'repeat-x' => 'repeat-x',
                            'repeat-y' => 'repeat-y',
                            'no-repeat' => 'no-repeat'
                        ),
                        'selector' => '.cl-row > .bg-layer',
                        'css_property' => array('background-repeat'),
                        
                    ),
                    
                    'background_attachment' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Bg. Attachment', 'codeless-builder' ),
                        
                        'default'     => 'scroll',
                        'choices' => array(
                            'scroll' => 'scroll',
                            'fixed' => 'fixed',
                        ),
                        'selector' => '.cl-row > .bg-layer',
                        'css_property' => 'background-attachment',
                        
                    ),
                    
                    'background_blend' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Bg. Blend', 'codeless-builder' ),
                        
                        'default'     => 'normal',
                        'choices' => array(
                            'normal' => 'normal',
                            'multiply' => 'multiply',
                            'screen' => 'screen',
                            'overlay' => 'overlay',
                            'darken' => 'darken',
                            'lighten' => 'lighten',
                            'color-dodge' => 'color-dodg',
                            'color-burn' => 'color-burn',
                            'hard-light' => 'hard-light',
                            'soft-light' => 'soft-light',
                            'difference' => 'difference',
                            'exclusion' => 'exclusion',
                            'hue' => 'hue',
                            'saturation' => 'saturation',
                            'color' => 'color',
                            'luminosity' => 'luminosity',
                        ),
                        'selector' => '.cl-row > .bg-layer',
                        'css_property' => 'background-blend-mode',
                        
                    ),
                    
                    'parallax' => array(
                        'type'        => 'switch',
                        'label'       => esc_html__( 'Parallax', 'codeless-builder' ),
                        'description'       => esc_html__( 'Works with smoothscroll active only.', 'codeless-builder' ),
                        'default'     => 0,
                        'priority'    => 10,
                        'choices'     => array(
                            'on'  => esc_attr__( 'On', 'codeless-builder' ),
                            'off' => esc_attr__( 'Off', 'codeless-builder' ),
                        ),
                        'selector' => '.cl-row',
                        'addClass' => 'cl-parallax',
                        
                    ),

                
                'background_image_group_end' => array(
                    'type' => 'group_end',
                    'label' => 'Background Image',
                    'groupid' => 'background_image_group'
                ),
            
                /* ---------------------------------------------------- */
                
                'overlay_group' => array(
                    'type' => 'group_start',
                    'label' => 'Overlay',
                    'groupid' => 'overlay'
                ),
            
                    'overlay' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Overlay Backgrund', 'codeless-builder' ),
                        
                        'default'     => 'none',
                        'choices' => array(
                            'none' => 'None',
                            'color' => 'Color',
                            'gradient' => 'Gradient'
                        )
                        
                    ),
                    
                    'overlay_color' => array(
                        'type' => 'color',
                        'label' => 'Overlay Color',
                        'default' => '',
                        'selector' => '.cl-row > .overlay',
                        'css_property' => 'background-color',
                        'cl_required'    => array(
                            array(
                                'setting'  => 'overlay',
                                'operator' => '==',
                                'value'    => 'color',
                            ),
                        ),
                        'alpha' => false
                    ),
                    
                    'overlay_gradient' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Overlay Gradient', 'codeless-builder' ),
                        
                        'default'     => 'none',
                        'choices' => array(
                            'none'	=> 'None',
                            'azure_pop' =>	'Azure Pop',
                            'love_couple' => 'Love Couple',
                            'disco' => 'Disco',
                            'limeade' => 'Limeade',
                            'dania' => 'Dania',
                            'shades_of_grey' =>	'Shades of Grey',
                            'dusk' => 'dusk',
                            'delhi' => 'delhi',
                            'sun_horizon' => 'Sun Horizon',
                            'blood_red' => 'Blood Red',
                            'sherbert' => 'Sherbert',
                            'firewatch' => 'Firewatch',
                            'frost' => 'Frost',
                            'mauve' => 'Mauve',
                            'deep_sea' => 'Deep Sea',
                            'solid_vault' => 'Solid Vault',
                            'deep_space' =>	'Deep Space',
                            'suzy' => 'Suzy'
                            
                            
                        ),
                        'selector' => '.cl-row > .overlay',
                        'selectClass' => 'cl-gradient-',
                        'cl_required'    => array(
                            array(
                                'setting'  => 'overlay',
                                'operator' => '==',
                                'value'    => 'gradient',
                            ),
                        ),
                    ),
                    
                    'overlay_opacity' => array(
                        'type' => 'slider',
                        'label' => 'Overlay Opacity',
                        'default' => '0.8',
                        'selector' => '.cl-row > .overlay',
                        'css_property' => 'opacity',
                        'choices'     => array(
                            'min'  => '0',
                            'max'  => '1',
                            'step' => '0.05',
                        ),
                        'cl_required'    => array(
                            array(
                                'setting'  => 'overlay',
                                'operator' => '!=',
                                'value'    => 'none',
                            ),
                        ),
                    ),
            
                'overlay_group_end' => array(
                    'type' => 'group_end',
                    'label' => 'Overlay',
                    'groupid' => 'overlay'
                ),
            
                /* ------------------------------------------ */
                
                
                'border_style_start' => array(
                    'type' => 'group_start',
                    'label' => 'Border Style',
                    'groupid' => 'border'
                ),
                
                    'border_style' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Border Style', 'codeless-builder' ),
                        
                        'default'     => 'solid',
                        'choices' => array(
                            'solid'	=> 'solid',
                            'dotted' =>	'dotted',
                            'dashed' =>	'dashed',
                            'double' => 'double',
                            'groove' => 'groove',
                            'ridge' => 'ridge',	
                            'inset' => 'inset',	
                            'outset' => 'outset',
                        ),
                        'selector' => '.cl-row',
                        'css_property' => 'border-style'
                    ),
                    
                    'border_color' => array(
                        'type' => 'color',
                        'label' => 'Border Color',
                        'default' => '',
                        'selector' => '.cl-row',
                        'css_property' => 'border-color',
                        'alpha' => true
                    ),
                
                'border_style_end' => array(
                    'type' => 'group_end',
                    'label' => 'Border Style',
                    'groupid' => 'border'
                ),
                
                /* --------------------------------------------------- */

            'design_tab_end' => array(
                'type' => 'tab_end',
                'label' => '',
                'tabid' => 'design'
            ),

            'animation_tab_start' => array(
                'type' => 'tab_start',
                'label' => 'Animation',
                'tabid' => 'animation'
            ),

            'animation' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Animation Effect', 'codeless-builder' ),
                        'default'     => 'none',
                        'choices' => array(
                            'none'	=> 'None',
                            'top-t-bottom' =>	'Top-Bottom',
                            'bottom-t-top' =>	'Bottom-Top',
                            'right-t-left' => 'Right-Left',
                            'left-t-right' => 'Left-Right',
                            'alpha-anim' => 'Fade-In',	
                            'zoom-in' => 'Zoom-In',	
                            'zoom-out' => 'Zoom-Out',
                            'zoom-reverse' => 'Zoom-Reverse',
                        ),
                        'selector' => '.cl-row > .bg-layer',
                        'group_vc' => 'Animations'
                    ),
                    
                    'animation_delay' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Animation Delay', 'codeless-builder' ),
                        'default'     => 'none',
                        'choices' => array(
                            'none'	=> 'None',
                            '100' =>	'ms 100',
                            '200' =>	'ms 200',
                            '300' =>	'ms 300',
                            '400' =>	'ms 400',
                            '500' =>	'ms 500',
                            '600' =>	'ms 600',
                            '700' =>	'ms 700',
                            '800' =>	'ms 800',
                            '900' =>	'ms 900',
                            '1000' =>	'ms 1000',
                            '1100' =>	'ms 1100',
                            '1200' =>	'ms 1200',
                            '1300' =>	'ms 1300',
                            '1400' =>	'ms 1400',
                            '1500' =>	'ms 1500',
                            '1600' =>	'ms 1600',
                            '1700' =>	'ms 1700',
                            '1800' =>	'ms 1800',
                            '1900' =>	'ms 1900',
                            '2000' =>	'ms 2000',
                        
                        ),
                        'selector' => '.cl-row > .bg-layer',
                        'htmldata' => 'delay',
                        
                        'group_vc' => 'Animations'
                    ),
                    
                    'animation_speed' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Animation Speed', 'codeless-builder' ),
                        'default'     => '400',
                        'choices' => array(
                            'none'	=> 'None',
                            '100' =>	'ms 100',
                            '200' =>	'ms 200',
                            '300' =>	'ms 300',
                            '400' =>	'ms 400',
                            '500' =>	'ms 500',
                            '600' =>	'ms 600',
                            '700' =>	'ms 700',
                            '800' =>	'ms 800',
                            '900' =>	'ms 900',
                            '1000' =>	'ms 1000'
                            
                        
                        ),
                        'selector' => '.cl-row > .bg-layer',
                        'htmldata' => 'speed',
                        
                        'group_vc' => 'Animations'
                    ),

            'animation_tab_end' => array(
                'type' => 'tab_end',
                'label' => 'Animation',
                'tabid' => 'animation'
            ),




            'responsive_tab_start' => array(
                'type' => 'tab_start',
                'label' => 'Responsive',
                'tabid' => 'responsive'
            ),
                
                
                    'device_visibility' => array(
                        'type'     => 'multicheck',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Devices Visibility', 'codeless-builder' ),
                        'default'     => '',
                        'choices' => array(
                            'hidden-xs' => esc_attr__( 'Hide on Phones (smaller-than-768px)', 'codeless-builder' ),
                            'hidden-sm' => esc_attr__('Hide on Tables (larger-then-768px)', 'codeless-builder' ),
                            'hidden-md' => esc_attr__('Hide on Medium Desktops (larger-then-992px) ', 'codeless-builder' ),
                            'hidden-lg' => esc_attr__('Hide on Large Desktops (larger-then1200px)', 'codeless-builder' ),
                        ),
                        'selector' => '.cl-row',
                        'selectClass' => '',
                        'group_vc' => 'Responsive',

                    ),

                    'col_responsive' => array(
                        'type'        => 'inline_select',
                        'label'       => esc_html__( 'Responsive Columns', 'codeless-builder' ),
                        'tooltip' => 'This option will change the width of columns on tablets sizes from (768px to 992px). Important option to build responsive perfect layouts.',
                        'default'     => 'none',
                        'priority'    => 10,
                        'choices'     => array(
                            'none' => 'None',
                            'full'  => esc_attr__( 'Fullwidth Columns', 'codeless-builder' ),
                            'half' => esc_attr__( 'Half Width Columns', 'codeless-builder' ),
                            'one_third' => esc_attr__( 'One / Third Width Columns', 'codeless-builder' ),
                        ),
                        'selector' => '.cl-row > div > .row',
                        'selectClass' => 'cl-col-tablet-',
                        'group_vc' => 'Responsive'
                    ),

                    'css_style_991_row_bool' => array(
                        'type'        => 'switch',
                        'label'       => esc_html__( 'Custom Box Design on max-width:991px (Tablet & Mobile)', 'codeless-builder' ),
                        'tooltip'       => esc_html__( 'Add custom box design (padding etc) on screen sizes max-width:991px', 'codeless-builder' ),
                        'default'     => 0,
                        'priority'    => 10,
                        'choices'     => array(
                            'on'  => esc_attr__( 'On', 'codeless-builder' ),
                            'off' => esc_attr__( 'Off', 'codeless-builder' ),
                        ),
                        'group_vc' => 'Responsive'
                    ),

                    'css_style_991' => array(
                        'type' => 'css_tool',
                        'label' => 'Tool',
                        'selector' => '.cl-row',
                        'css_property' => '',
                        'default' => array('padding-top' => '', 'padding-bottom' => ''),
                        'media_query' => '(max-width: 991px)',
                        'cl_required'    => array(
                            array(
                                'setting'  => 'css_style_991_row_bool',
                                'operator' => '==',
                                'value'    => true,
                            ),
                        ),
                        'group_vc' => 'Responsive'
                    ),



                    'css_style_767_row_bool' => array(
                        'type'        => 'switch',
                        'label'       => esc_html__( 'Custom Box Design on max-width:767px (Only Mobile)', 'codeless-builder' ),
                        'tooltip'       => esc_html__( 'Add custom box design (padding etc) on screen sizes max-width:767px', 'codeless-builder' ),
                        'default'     => 0,
                        'priority'    => 10,
                        'choices'     => array(
                            'on'  => esc_attr__( 'On', 'codeless-builder' ),
                            'off' => esc_attr__( 'Off', 'codeless-builder' ),
                        ),
                        'group_vc' => 'Responsive'
                        
                    ),

                    'css_style_767' => array(
                        'type' => 'css_tool',
                        'label' => 'Tool',
                        'selector' => '.cl-row',
                        'css_property' => '',
                        'default' => array('padding-top' => '', 'padding-bottom' => ''),
                        'media_query' => '(max-width: 767px)',
                        'cl_required'    => array(
                            array(
                                'setting'  => 'css_style_767_row_bool',
                                'operator' => '==',
                                'value'    => true,
                            ),
                        ),
                        'group_vc' => 'Responsive'
                    ),
                

            'responsive_tab_end' => array(
                'type' => 'tab_end',
                'label' => 'Responsive',
                'tabid' => 'responsive'
            ),
    )
));

cl_builder_map(array(
'type'        => 'clelement',
'label'       => esc_attr__( 'Row', 'codeless-builder' ),
'section'     => 'cl_codeless_page_builder',
'tooltip' => 'Manage all options of the selected Row',
//'priority'    => 10,

'transport'   => 'postMessage',
'settings'    => 'cl_row_inner',
'is_container' => true,
'marginPositions' => array('top'),
'fields' => array(
    'inner_columns_gap' => array(
                'type'     => 'slider',
                'priority' => 10,
                'label'       => esc_attr__( 'Inner Columns Gap', 'codeless-builder' ),
                'default'     => '15',
                'choices'     => array(
                    'min'  => '0',
                    'max'  => '35',
                    'step' => '1',
                ),
                'suffix' => 'px',
                'selector' => '.row > .cl_cl_column_inner',
                'css_property' => array('padding-left', 'padding-right'),
                'customJS' => 'inlineEdit_InnerColumns'
            ),
    'css_style' => array(
                'type' => 'css_tool',
                'label' => 'Tool',
                'selector' => '.cl-row_inner',
                'css_property' => '',
                'default' => array('margin-top' => '35px'),
    ),
)
));

cl_builder_map(array(
'type'        => 'clelement',
'label'       => esc_attr__( 'Column', 'codeless-builder' ),
'section'     => 'cl_codeless_page_builder',
//'priority'    => 10,

'transport'   => 'postMessage',
'settings'    => 'cl_column',
'paddingPositions' => array('top', 'bottom', 'left', 'right'),
'is_container' => true,
'fields' => array(
    'width' => array(
        'type'     => 'select',
        'priority' => 10,
        'label'       => esc_attr__( 'Link Text', 'codeless-builder' ),
        'tooltip' => esc_attr__( 'This will be the label for your link', 'codeless-builder' ),
        'default'     => '1/1',
        'show' => false,
        'choices'     => array(
            '1/12' => '1 Column',
            '1/6' => '2 Columns',
            '1/4' => '3 Columns',
            '1/3' => '4 Columns',
            '5/12' => '5 Columns',
            '1/2' => '6 Columns',
            '7/12' => '7 Columns',
            '2/3' => '8 Columns',
            '3/4' => '9 Columns',
            '5/6' => '10 Columns',
            '11/12' => '11 Columns',
            '1/1' => '12 Columns',
        ),
    ),
    
    'element_tabs' => array(
        'type' => 'show_tabs',
        'default' => 'general',
        'tabs' => array(
            'general' => array( 'General', 'cl-icon-settings' ),
            'design' => array( 'Design', 'cl-icon-tune' ),
            'animation' => array( 'Animation', 'cl-icon-animation' ),
            'responsive' => array( 'Responsive', 'cl-icon-responsive' )
        )
    ),
    
    'general_tab_start' => array(
        'type' => 'tab_start',
        'label' => 'General',
        'tabid' => 'general'
    ),
    
        'column_info_start' => array(
            'type' => 'group_start',
            'label' => 'Attributes',
            'groupid' => 'attr'
        ),
                

            'horizontal_align' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Horizontal Align', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Horizontal Alignment of elements into this column(container)', 'codeless-builder' ),
                'default'     => 'left',
                'choices' => array(
                    'left' => 'Left',
                    'middle' => 'Middle',
                    'right' => 'Right'
                ),
                'selector' => '.cl_column',
                'selectClass' => 'align-h-',
                
            ),

            'vertical_align' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Vertical Align', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Vertical Alignment of elements into this column(container)', 'codeless-builder' ),
                'default'     => 'top',
                'choices' => array(
                    'top' => 'Top',
                    'middle' => 'Middle',
                    'bottom' => 'Bottom'
                ),
                'selector' => '.cl_column',
                'selectClass' => 'align-v-',
                
            ),

            'col_sticky' => array(
                'type'        => 'switch',
                'label'       => esc_html__( 'Sticky Column', 'codeless-builder' ),
                'tooltip' => 'Make this Column sticky on this page',
                'default'     => '0',
                'priority'    => 10,
                'choices'     => array(
                    'on'  => esc_attr__( 'On', 'codeless-builder' ),
                    'off' => esc_attr__( 'Off', 'codeless-builder' ),
                ),
                'selector' => '.cl_column',
                'addClass' => 'cl-sticky'
            ),

            'col_disabled' => array(
                'type'        => 'switch',
                'label'       => esc_html__( 'Disable Column', 'codeless-builder' ),
                'tooltip' => 'Make this Column invisible in this Page',
                'default'     => '0',
                'priority'    => 10,
                'choices'     => array(
                    'on'  => esc_attr__( 'On', 'codeless-builder' ),
                    'off' => esc_attr__( 'Off', 'codeless-builder' ),
                ),
                'selector' => '.cl_column',
                'addClass' => 'disabled_col'
            ),

            'col_id' => array(
                
                'type'     => 'text',
                'priority' => 10,
                'label'       => esc_attr__( 'Column Id', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'This is useful when you want to add unique identifier to columns.', 'codeless-builder' ),
                'default'     => '',
            ),
            
            'extra_class' => array(
                'type'     => 'text',
                'priority' => 10,
                'label'       => esc_attr__( 'Extra Class', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Add extra class identifiers to this column, that can be used for various custom styles.', 'codeless-builder' ),
                'default'     => '',
            ),

            'custom_link' => array(

                'type'     => 'text',
                'priority' => 10,
                'label'       => esc_attr__( 'Column Link', 'codeless-builder' ),
                'default'     => '#',
                'reloadTemplate' => false
            ),

            'target' => array(

                'type' => 'inline_select',
                'priority' => 10,
                'label' => esc_attr__('Specify where to open the custom link ', 'codeless-builder'),
                'default' => '_self',
                'choices' => array(
                    '_self' => 'Open in the Same Window',
                    '_blank' => 'Open link in a new tab',
                    '_parent' => 'Open link in the parent frame',
                    '_top' => 'Open the link in the full body of the window'

                )

            ),

            'column_effect' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Effect on hover', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none' => 'None',
                    'image_zoom' => 'Image Zoom'
                ),
                'selector' => '.cl_column',
                'selectClass' => 'effect-',
                
            ),
            

        'column_info_end' => array(
            'type' => 'group_end',
            'label' => 'Attributes',
            'groupid' => 'attr'
        ),

        
    'general_tab_end' => array(
        'type' => 'tab_end',
        'label' => 'General',
        'tabid' => 'general'
    ),
        
        
    'design_tab_start' => array(
        'type' => 'tab_start',
        'label' => 'Design',
        'tabid' => 'design'
    ),
        
        /* ------------------------------------------ */
        
        'panel' => array(
            'type' => 'group_start',
            'label' => 'Box',
            'groupid' => 'design_panel'
        ),
    
            'css_style' => array(
                'type' => 'css_tool',
                'label' => 'Tool',
                'selector' => '.cl_column > .cl_col_wrapper',
                'css_property' => '',
                'default' => array('padding-top' => '20px', 'padding-bottom' => '20px'),
            ),
            
            'text_color' => array(
                'type' => 'inline_select',
                'label' => 'Text Color',
                'default' => 'dark-text',
                'choices' => array(
                    'dark-text' => 'Dark',
                    'light-text' => 'Light'
                ),
                'selector' => '.cl_column',
                'selectClass' => ''
            ),
        
            
        'design_panel_end' => array(
            'type' => 'group_end',
            'label' => 'Animation',
            'groupid' => 'design_panel'
        ),
        
        /* ------------------------------------------ */
    
        'background_color_group' => array(
            'type' => 'group_start',
            'label' => 'Background Color',
            'groupid' => 'background_color_group'
        ),
        
            'background_color' => array(
                'type' => 'color',
                'label' => 'Background Color',
                'default' => '',
                'selector' => '.cl_column > .cl_col_wrapper > .bg-layer',
                'css_property' => 'background-color',
                'alpha' => true
            ),
        
        'background_color_group_end' => array(
            'type' => 'group_end',
            'label' => 'Background Color',
            'groupid' => 'background_color_group'
        ),
        
        /* ------------------------------------------- */
        
        'background_image_group' => array(
            'type' => 'group_start',
            'label' => 'Background Image',
            'groupid' => 'background_image_group'
        ),
        
            'background_image' => array(
                'type'        => 'image',
                'label'       => '',
                'default'     => '',
                'priority'    => 10,
                'selector' => '.cl_column > .cl_col_wrapper > .bg-layer',
                'css_property' => 'background-image',
                'choices' => array(),
            ),
            
            'background_position' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Background Position', 'codeless-builder' ),
                
                'default'     => 'left top',
                'choices' => array(
                    'left top' => 'left top',
                    'left center' => 'left center',
                    'left bottom' => 'left bottom',
                    'right top' => 'right top',
                    'right center' => 'right center',
                    'right bottom' => 'right bottom',
                    'center top' => 'center top',
                    'center center' => 'center center',
                    'center bottom' => 'center bottom',
                ),
                'selector' => '.cl_column > .cl_col_wrapper > .bg-layer',
                'css_property' => 'background-position',
                
                
            ),
            
            'background_repeat' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Background Repeat', 'codeless-builder' ),
                
                'default'     => 'no-repeat',
                'choices' => array(
                    'repeat' => 'repeat',
                    'repeat-x' => 'repeat-x',
                    'repeat-y' => 'repeat-y',
                    'no-repeat' => 'no-repeat'
                ),
                'selector' => '.cl_column > .cl_col_wrapper > .bg-layer',
                'css_property' => array('background-repeat', array('background-size', array('no-repeat' => 'cover', 'other' => 'auto') ) ),
                
            ),
            
            'background_attachment' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Bg. Attachment', 'codeless-builder' ),
                
                'default'     => 'scroll',
                'choices' => array(
                    'scroll' => 'scroll',
                    'fixed' => 'fixed',
                ),
                'selector' => '.cl_column > .cl_col_wrapper > .bg-layer',
                'css_property' => 'background-attachment',
                
            ),
            
            'background_blend' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Bg. Blend', 'codeless-builder' ),
                
                'default'     => 'normal',
                'choices' => array(
                    'normal' => 'normal',
                    'multiply' => 'multiply',
                    'screen' => 'screen',
                    'overlay' => 'overlay',
                    'darken' => 'darken',
                    'lighten' => 'lighten',
                    'color-dodge' => 'color-dodg',
                    'color-burn' => 'color-burn',
                    'hard-light' => 'hard-light',
                    'soft-light' => 'soft-light',
                    'difference' => 'difference',
                    'exclusion' => 'exclusion',
                    'hue' => 'hue',
                    'saturation' => 'saturation',
                    'color' => 'color',
                    'luminosity' => 'luminosity',
                ),
                'selector' => '.cl_column > .cl_col_wrapper > .bg-layer',
                'css_property' => 'background-blend-mode',
                
            ),
            
            
        
        'background_image_group_end' => array(
            'type' => 'group_end',
            'label' => 'Background Image',
            'groupid' => 'background_image_group'
        ),
    
        /* ---------------------------------------------------- */
        
        'overlay_group' => array(
            'type' => 'group_start',
            'label' => 'Overlay',
            'groupid' => 'overlay'
        ),
    
            'overlay' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Overlay Backgrund', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none' => 'None',
                    'color' => 'Color',
                    'gradient' => 'Gradient'
                )
                
            ),
            
            'overlay_color' => array(
                'type' => 'color',
                'label' => 'Overlay Color',
                'default' => '',
                'selector' => '.cl_column > .cl_col_wrapper > .overlay',
                'css_property' => 'background-color',
                'cl_required'    => array(
                    array(
                        'setting'  => 'overlay',
                        'operator' => '==',
                        'value'    => 'color',
                    ),
                ),
                'alpha' => false
            ),
            
            'overlay_gradient' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Overlay Gradient', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none'	=> 'None',
                    'azure_pop' =>	'Azure Pop',
                    'love_couple' => 'Love Couple',
                    'disco' => 'Disco',
                    'limeade' => 'Limeade',
                    'dania' => 'Dania',
                    'shades_of_grey' =>	'Shades of Grey',
                    'dusk' => 'dusk',
                    'delhi' => 'delhi',
                    'sun_horizon' => 'Sun Horizon',
                    'blood_red' => 'Blood Red',
                    'sherbert' => 'Sherbert',
                    'firewatch' => 'Firewatch',
                    'frost' => 'Frost',
                    'mauve' => 'Mauve',
                    'deep_sea' => 'Deep Sea',
                    'solid_vault' => 'Solid Vault',
                    'deep_space' =>	'Deep Space',
                    'suzy' => 'Suzy'
                    
                    
                ),
                'selector' => '.cl_column > .cl_col_wrapper > .overlay',
                'selectClass' => 'cl-gradient-',
                'cl_required'    => array(
                    array(
                        'setting'  => 'overlay',
                        'operator' => '==',
                        'value'    => 'gradient',
                    ),
                ),
            ),
            
            'overlay_opacity' => array(
                'type' => 'slider',
                'label' => 'Overlay Opacity',
                'default' => '0.8',
                'selector' => '.cl_column > .cl_col_wrapper > .overlay',
                'css_property' => 'opacity',
                'choices'     => array(
                    'min'  => '0',
                    'max'  => '1',
                    'step' => '0.05',
                ),
                'cl_required'    => array(
                    array(
                        'setting'  => 'overlay',
                        'operator' => '!=',
                        'value'    => 'none',
                    ),
                ),
            ),
    
        'overlay_group_end' => array(
            'type' => 'group_end',
            'label' => 'Overlay',
            'groupid' => 'overlay'
        ),
    
        /* ------------------------------------------ */
        
        
        'border_style_start' => array(
            'type' => 'group_start',
            'label' => 'Border Style',
            'groupid' => 'border'
        ),
        
            'border_style' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Border Style', 'codeless-builder' ),
                
                'default'     => 'solid',
                'choices' => array(
                    'solid'	=> 'solid',
                    'dotted' =>	'dotted',
                    'dashed' =>	'dashed',
                    'double' => 'double',
                    'groove' => 'groove',
                    'ridge' => 'ridge',	
                    'inset' => 'inset',	
                    'outset' => 'outset',
                ),
                'selector' => '.cl_column > .cl_col_wrapper',
                'css_property' => 'border-style'
            ),
            
            'border_color' => array(
                'type' => 'color',
                'label' => 'Border Color',
                'default' => '',
                'selector' => '.cl_column > .cl_col_wrapper',
                'css_property' => 'border-color',
                'alpha' => true
            ),

            'border_rounded' => array(
                'type'        => 'switch',
                'label'       => esc_html__( 'Border Rounded', 'codeless-builder' ),
                
                'default'     => 0,
                'priority'    => 10,
                'choices'     => array(
                    'on'  => esc_attr__( 'On', 'codeless-builder' ),
                    'off' => esc_attr__( 'Off', 'codeless-builder' ),
                ),
                'selector' => '.cl_column',
                'addClass' => 'cl-border-rounded'
            ),
        
        'border_style_end' => array(
            'type' => 'group_end',
            'label' => 'Border Style',
            'groupid' => 'border'
        ),
        
        /* --------------------------------------------------- */

    'design_tab_end' => array(
        'type' => 'tab_end',
        'label' => '',
        'tabid' => 'design'
    ),
    

    'animation_tab_start' => array(
        'type' => 'tab_start',
        'label' => 'Animation',
        'tabid' => 'animation'
    ),

        'animation' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Effect', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none'	=> 'None',
                    'top-t-bottom' =>	'Top-Bottom',
                    'bottom-t-top' =>	'Bottom-Top',
                    'right-t-left' => 'Right-Left',
                    'left-t-right' => 'Left-Right',
                    'alpha-anim' => 'Fade-In',	
                    'zoom-in' => 'Zoom-In',	
                    'zoom-out' => 'Zoom-Out',
                    'zoom-reverse' => 'Zoom-Reverse',
                    'flip-in' => 'Flip In',
                    'reveal-right' => 'Reveal Right',
                    'reveal-left' => 'Reveal Left',
                    'reveal-top' => 'Reveal Top',
                    'reveal-bottom' => 'Reveal Bottom',
                ),
                'selector' => '.cl_column',
                'group_vc' => 'Animations'
            ),
            
            'animation_delay' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Delay', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none'	=> 'None',
                    '100' =>	'ms 100',
                    '200' =>	'ms 200',
                    '300' =>	'ms 300',
                    '400' =>	'ms 400',
                    '500' =>	'ms 500',
                    '600' =>	'ms 600',
                    '700' =>	'ms 700',
                    '800' =>	'ms 800',
                    '900' =>	'ms 900',
                    '1000' =>	'ms 1000',
                    '1100' =>	'ms 1100',
                    '1200' =>	'ms 1200',
                    '1300' =>	'ms 1300',
                    '1400' =>	'ms 1400',
                    '1500' =>	'ms 1500',
                    '1600' =>	'ms 1600',
                    '1700' =>	'ms 1700',
                    '1800' =>	'ms 1800',
                    '1900' =>	'ms 1900',
                    '2000' =>	'ms 2000',
                
                ),
                'selector' => '.cl_column',
                'htmldata' => 'delay',
                
                'group_vc' => 'Animations'
            ),
            
            'animation_speed' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Speed', 'codeless-builder' ),
                
                'default'     => '400',
                'choices' => array(
                    'none'	=> 'None',
                    '100' =>	'ms 100',
                    '200' =>	'ms 200',
                    '300' =>	'ms 300',
                    '400' =>	'ms 400',
                    '500' =>	'ms 500',
                    '600' =>	'ms 600',
                    '700' =>	'ms 700',
                    '800' =>	'ms 800',
                    '900' =>	'ms 900',
                    '1000' =>	'ms 1000'
                    
                
                ),
                'selector' => '.cl_column',
                'htmldata' => 'speed',
                
                'group_vc' => 'Animations'
            ),
    'animation_tab_end' => array(
        'type' => 'tab_end',
        'label' => 'Animation',
        'tabid' => 'animation'
    ),





    'responsive_tab_start' => array(
        'type' => 'tab_start',
        'label' => 'Responsive',
        'tabid' => 'responsive'
    ),

        'device_visibility' => array(
                'type'     => 'multicheck',
                'priority' => 10,
                'label'       => esc_attr__( 'Devices Visibility', 'codeless-builder' ),
                'default'     => '',
                'choices' => array(
                    'hidden-xs' => esc_attr__( 'Hide on Phones (smaller-than-768px)', 'codeless-builder' ),
                    'hidden-sm' => esc_attr__('Hide on Tables (larger-then-768px)', 'codeless-builder' ),
                    'hidden-md' => esc_attr__('Hide on Medium Desktops (larger-then-992px) ', 'codeless-builder' ),
                    'hidden-lg' => esc_attr__('Hide on Large Desktops (larger-then1200px)', 'codeless-builder' ),
                ),
                'selector' => '.cl_column',
                'selectClass' => '',
                'group_vc' => 'Responsive'
            ),

        'css_style_991_col_bool' => array(
                'type'        => 'switch',
                'label'       => esc_html__( 'Custom Box Design on max-width:991px (Tablet & Mobile)', 'codeless-builder' ),
                'tooltip'       => esc_html__( 'Add custom box design (padding etc) on screen sizes max-width:991px', 'codeless-builder' ),
                'default'     => 0,
                'priority'    => 10,
                'choices'     => array(
                    'on'  => esc_attr__( 'On', 'codeless-builder' ),
                    'off' => esc_attr__( 'Off', 'codeless-builder' ),
                ),
                'group_vc' => 'Responsive'
            ),

            'css_style_991' => array(
                'type' => 'css_tool',
                'label' => 'Tool',
                'selector' => '.cl_column > .cl_col_wrapper',
                'css_property' => '',
                'default' => array('padding-top' => '', 'padding-bottom' => ''),
                'media_query' => '(max-width: 991px)',
                'cl_required'    => array(
                    array(
                        'setting'  => 'css_style_991_col_bool',
                        'operator' => '==',
                        'value'    => true,
                    ),
                ),
                'group_vc' => 'Responsive'
            ),



            'css_style_767_col_bool' => array(
                'type'        => 'switch',
                'label'       => esc_html__( 'Custom Box Design on max-width:767px ( Only Mobile )', 'codeless-builder' ),
                'tooltip'       => esc_html__( 'Add custom box design (padding etc) on screen sizes max-width:767px', 'codeless-builder' ),
                'default'     => 0,
                'priority'    => 10,
                'choices'     => array(
                    'on'  => esc_attr__( 'On', 'codeless-builder' ),
                    'off' => esc_attr__( 'Off', 'codeless-builder' ),
                ),
                'group_vc' => 'Responsive'
                
            ),

            'css_style_767' => array(
                'type' => 'css_tool',
                'label' => 'Tool',
                'selector' => '.cl_column > .cl_col_wrapper',
                'css_property' => '',
                'default' => array('padding-top' => '', 'padding-bottom' => ''),
                'media_query' => '(max-width: 767px)',
                'cl_required'    => array(
                    array(
                        'setting'  => 'css_style_767_col_bool',
                        'operator' => '==',
                        'value'    => true,
                    ),
                ),
                'group_vc' => 'Responsive'
            ),

    'responsive_tab_end' => array(
        'type' => 'tab_end',
        'label' => 'Responsive',
        'tabid' => 'responsive'
    ),
        
),

) );

cl_builder_map(array(
    'type'        => 'clelement',
    'label'       => esc_attr__( 'Column', 'codeless-builder' ),
    'section'     => 'cl_codeless_page_builder',
    //'priority'    => 10,
    
    'transport'   => 'postMessage',
    'settings'    => 'cl_column_inner',
    'paddingPositions' => array('top', 'bottom', 'left', 'right'),
    'is_container' => true,
    'fields' => array(



        'width' => array(
            'type'     => 'select',
            'priority' => 10,
            'label'       => esc_attr__( 'Link Text', 'codeless-builder' ),
            'tooltip' => esc_attr__( 'This will be the label for your link', 'codeless-builder' ),
            'default'     => '1/1',
            'show' => false,
            'choices'     => array(
                '1/12' => '1 Column',
                '1/6' => '2 Columns',
                '1/4' => '3 Columns',
                '1/3' => '4 Columns',
                '5/12' => '5 Columns',
                '1/2' => '6 Columns',
                '7/12' => '7 Columns',
                '2/3' => '8 Columns',
                '3/4' => '9 Columns',
                '5/6' => '10 Columns',
                '11/12' => '11 Columns',
                '1/1' => '12 Columns',
            ),
        ),

        'element_tabs' => array(
            'type' => 'show_tabs',
            'default' => 'general',
            'tabs' => array(
                'general' => array( 'General', 'cl-icon-settings' ),
                'design' => array( 'Design', 'cl-icon-tune' ),
                'responsive' => array( 'Responsive', 'cl-icon-responsive' )
            )
        ),
        
        'gen_tab_start' => array(
                'type' => 'tab_start',
                'label' => 'General',
                'tabid' => 'general'
            ),

        'inline_elements' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Inline Elements', 'codeless-builder' ),
                    'tooltip' => 'By activating this, elements of this column will be shown inline.',
                    'default'     => '0',
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),
                    'selector' => '.cl_column_inner',
                    'addClass' => 'cl-inline-column'
            ),

        'animation_start' => array(
                'type' => 'group_start',
                'label' => 'Animation',
                'groupid' => 'animation'
            ),
            
                'animation' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Animation Effect', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'choices' => array(
                        'none'	=> 'None',
                        'top-t-bottom' =>	'Top-Bottom',
                        'bottom-t-top' =>	'Bottom-Top',
                        'right-t-left' => 'Right-Left',
                        'left-t-right' => 'Left-Right',
                        'alpha-anim' => 'Fade-In',	
                        'zoom-in' => 'Zoom-In',	
                        'zoom-out' => 'Zoom-Out',
                        'zoom-reverse' => 'Zoom-Reverse',
                    ),
                    'selector' => '.cl_column_inner',
                    'customJS' => array('front' => 'codeless_builder_animations')
                ),
                
                'animation_delay' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Animation Delay', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'choices' => array(
                        'none'	=> 'None',
                        '100' =>	'ms 100',
                        '200' =>	'ms 200',
                        '300' =>	'ms 300',
                        '400' =>	'ms 400',
                        '500' =>	'ms 500',
                        '600' =>	'ms 600',
                        '700' =>	'ms 700',
                        '800' =>	'ms 800',
                        '900' =>	'ms 900',
                        '1000' =>	'ms 1000',
                        '1100' =>	'ms 1100',
                        '1200' =>	'ms 1200',
                        '1300' =>	'ms 1300',
                        '1400' =>	'ms 1400',
                        '1500' =>	'ms 1500',
                        '1600' =>	'ms 1600',
                        '1700' =>	'ms 1700',
                        '1800' =>	'ms 1800',
                        '1900' =>	'ms 1900',
                        '2000' =>	'ms 2000',
                    
                    ),
                    'selector' => '.cl_column_inner',
                    'htmldata' => 'delay',
                    
                    'customJS' => array('front' => 'codeless_builder_animations')
                ),
                
                'animation_speed' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Animation Speed', 'codeless-builder' ),
                    
                    'default'     => '400',
                    'choices' => array(
                        'none'	=> 'None',
                        '100' =>	'ms 100',
                        '200' =>	'ms 200',
                        '300' =>	'ms 300',
                        '400' =>	'ms 400',
                        '500' =>	'ms 500',
                        '600' =>	'ms 600',
                        '700' =>	'ms 700',
                        '800' =>	'ms 800',
                        '900' =>	'ms 900',
                        '1000' =>	'ms 1000'
                        
                    
                    ),
                    'selector' => '.cl_column_inner',
                    'htmldata' => 'speed',
                    
                    'customJS' => array('front' => 'codeless_builder_animations')
                ),
            
            'animation_end' => array(
                'type' => 'group_end',
                'label' => 'Animation',
                'groupid' => 'animation'
            ),

            'gen_tab_end' => array(
                'type' => 'tab_end',
                'label' => 'General',
                'tabid' => 'general'
            ),

            'design_tab_start' => array(
                'type' => 'tab_start',
                'label' => 'Design',
                'tabid' => 'design'
            ),
        
            /* ------------------------------------------ */
            
            'panel' => array(
                'type' => 'group_start',
                'label' => 'Box',
                'groupid' => 'design_panel'
            ),
        
                'css_style' => array(
                    'type' => 'css_tool',
                    'label' => 'Tool',
                    'selector' => '.cl_column_inner > .wrapper',
                    'css_property' => '',
                    'default' => array('padding-top' => '10px', 'padding-bottom' => '10px'),
                ),
                
                'text_color' => array(
                    'type' => 'inline_select',
                    'label' => 'Text Color',
                    'default' => 'dark-text',
                    'choices' => array(
                        'dark-text' => 'Dark',
                        'light-text' => 'Light'
                    ),
                    'selector' => '.cl_column_inner',
                    'selectClass' => ''
                ),
            
                
            'design_panel_end' => array(
                'type' => 'group_end',
                'label' => 'Animation',
                'groupid' => 'design_panel'
            ),
            
            /* ------------------------------------------ */
        
            'background_color_group' => array(
                'type' => 'group_start',
                'label' => 'Background Color',
                'groupid' => 'background_color_group'
            ),
            
                'background_color' => array(
                    'type' => 'color',
                    'label' => 'Background Color',
                    'default' => '',
                    'selector' => '.cl_column_inner > .wrapper > .bg-layer',
                    'css_property' => 'background-color',
                    'alpha' => true
                ),
            
            'background_color_group_end' => array(
                'type' => 'group_end',
                'label' => 'Background Color',
                'groupid' => 'background_color_group'
            ),
            
            /* ------------------------------------------- */
            
            'background_image_group' => array(
                'type' => 'group_start',
                'label' => 'Background Image',
                'groupid' => 'background_image_group'
            ),
            
                'background_image' => array(
                    'type'        => 'image',
                    'label'       => '',
                    'default'     => '',
                    'priority'    => 10,
                    'selector' => '.cl_column_inner > .wrapper > .bg-layer',
                    'css_property' => 'background-image',
                    'choices' => array(),
                ),
                
                'background_position' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Background Position', 'codeless-builder' ),
                    
                    'default'     => 'left top',
                    'choices' => array(
                        'left top' => 'left top',
                        'left center' => 'left center',
                        'left bottom' => 'left bottom',
                        'right top' => 'right top',
                        'right center' => 'right center',
                        'right bottom' => 'right bottom',
                        'center top' => 'center top',
                        'center center' => 'center center',
                        'center bottom' => 'center bottom',
                    ),
                    'selector' => '.cl_column_inner > .wrapper > .bg-layer',
                    'css_property' => 'background-position',
                    
                ),
                
                'background_repeat' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Background Repeat', 'codeless-builder' ),
                    
                    'default'     => 'no-repeat',
                    'choices' => array(
                        'repeat' => 'repeat',
                        'repeat-x' => 'repeat-x',
                        'repeat-y' => 'repeat-y',
                        'no-repeat' => 'no-repeat'
                    ),
                    'selector' => '.cl_column_inner > .wrapper > .bg-layer',
                    'css_property' => array('background-repeat', array('background-size', array('no-repeat' => 'cover', 'other' => 'auto') ) ),
                    
                ),
                
                'background_attachment' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Bg. Attachment', 'codeless-builder' ),
                    
                    'default'     => 'scroll',
                    'choices' => array(
                        'scroll' => 'scroll',
                        'fixed' => 'fixed',
                    ),
                    'selector' => '.cl_column_inner > .wrapper > .bg-layer',
                    'css_property' => 'background-attachment',
                    
                ),
                
                'background_blend' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Bg. Blend', 'codeless-builder' ),
                    
                    'default'     => 'normal',
                    'choices' => array(
                        'normal' => 'normal',
                        'multiply' => 'multiply',
                        'screen' => 'screen',
                        'overlay' => 'overlay',
                        'darken' => 'darken',
                        'lighten' => 'lighten',
                        'color-dodge' => 'color-dodg',
                        'color-burn' => 'color-burn',
                        'hard-light' => 'hard-light',
                        'soft-light' => 'soft-light',
                        'difference' => 'difference',
                        'exclusion' => 'exclusion',
                        'hue' => 'hue',
                        'saturation' => 'saturation',
                        'color' => 'color',
                        'luminosity' => 'luminosity',
                    ),
                    'selector' => '.cl_column_inner > .wrapper > .bg-layer',
                    'css_property' => 'background-blend-mode',
                    
                ),
                
                
            
            'background_image_group_end' => array(
                'type' => 'group_end',
                'label' => 'Background Image',
                'groupid' => 'background_image_group'
            ),
        
            /* ---------------------------------------------------- */
            
            'overlay_group' => array(
                'type' => 'group_start',
                'label' => 'Overlay',
                'groupid' => 'overlay'
            ),
        
                'overlay' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Overlay Backgrund', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'choices' => array(
                        'none' => 'None',
                        'color' => 'Color',
                        'gradient' => 'Gradient'
                    )
                    
                ),
                
                'overlay_color' => array(
                    'type' => 'color',
                    'label' => 'Overlay Color',
                    'default' => '',
                    'selector' => '.cl_column_inner > .wrapper > .overlay',
                    'css_property' => 'background-color',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'overlay',
                            'operator' => '==',
                            'value'    => 'color',
                        ),
                    ),
                    'alpha' => false
                ),
                
                'overlay_gradient' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Overlay Gradient', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'choices' => array(
                        'none'	=> 'None',
                        'azure_pop' =>	'Azure Pop',
                        'love_couple' => 'Love Couple',
                        'disco' => 'Disco',
                        'limeade' => 'Limeade',
                        'dania' => 'Dania',
                        'shades_of_grey' =>	'Shades of Grey',
                        'dusk' => 'dusk',
                        'delhi' => 'delhi',
                        'sun_horizon' => 'Sun Horizon',
                        'blood_red' => 'Blood Red',
                        'sherbert' => 'Sherbert',
                        'firewatch' => 'Firewatch',
                        'frost' => 'Frost',
                        'mauve' => 'Mauve',
                        'deep_sea' => 'Deep Sea',
                        'solid_vault' => 'Solid Vault',
                        'deep_space' =>	'Deep Space',
                        'suzy' => 'Suzy'
                        
                        
                    ),
                    'selector' => '.cl_column_inner > .wrapper > .overlay',
                    'selectClass' => 'cl-gradient-',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'overlay',
                            'operator' => '==',
                            'value'    => 'gradient',
                        ),
                    ),
                ),
                
                'overlay_opacity' => array(
                    'type' => 'slider',
                    'label' => 'Overlay Opacity',
                    'default' => '0.8',
                    'selector' => '.cl_column_inner > .wrapper > .overlay',
                    'css_property' => 'opacity',
                    'choices'     => array(
                        'min'  => '0',
                        'max'  => '1',
                        'step' => '0.05',
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'overlay',
                            'operator' => '!=',
                            'value'    => 'none',
                        ),
                    ),
                ),
        
            'overlay_group_end' => array(
                'type' => 'group_end',
                'label' => 'Overlay',
                'groupid' => 'overlay'
            ),
        
            /* ------------------------------------------ */
            
            
            'border_style_start' => array(
                'type' => 'group_start',
                'label' => 'Border Style',
                'groupid' => 'border'
            ),
            
                'border_style' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Border Style', 'codeless-builder' ),
                    
                    'default'     => 'solid',
                    'choices' => array(
                        'solid'	=> 'solid',
                        'dotted' =>	'dotted',
                        'dashed' =>	'dashed',
                        'double' => 'double',
                        'groove' => 'groove',
                        'ridge' => 'ridge',	
                        'inset' => 'inset',	
                        'outset' => 'outset',
                    ),
                    'selector' => '.cl_column_inner > .wrapper',
                    'css_property' => 'border-style'
                ),
                
                'border_color' => array(
                    'type' => 'color',
                    'label' => 'Border Color',
                    'default' => '',
                    'selector' => '.cl_column_inner > .wrapper',
                    'css_property' => 'border-color',
                    'alpha' => true
                ),
            
            'border_style_end' => array(
                'type' => 'group_end',
                'label' => 'Border Style',
                'groupid' => 'border'
            ),
            
            /* --------------------------------------------------- */

        'design_tab_end' => array(
            'type' => 'tab_end',
            'label' => '',
            'tabid' => 'design'
        ),


        'responsive_tab_start' => array(
            'type' => 'tab_start',
            'label' => 'Responsive',
            'tabid' => 'responsive'
        ),

            'css_style_991_colinner_bool' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Custom Box Design on max-width:991px (Table & Mobile)', 'codeless-builder' ),
                    'tooltip'       => esc_html__( 'Add custom box design (padding etc) on screen sizes max-width:991px', 'codeless-builder' ),
                    'default'     => 0,
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),
                ),

                'css_style_991' => array(
                    'type' => 'css_tool',
                    'label' => 'Tool',
                    'selector' => '.cl_column_inner > .wrapper',
                    'css_property' => '',
                    'default' => array('margin-top' => 0),
                    'media_query' => '(max-width: 991px)',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'css_style_991_colinner_bool',
                            'operator' => '==',
                            'value'    => true,
                        ),
                    ),
                ),



                'css_style_767_colinner_bool' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Custom Box Design on max-width:767px ( Only Mobile )', 'codeless-builder' ),
                    'tooltip'       => esc_html__( 'Add custom box design (padding etc) on screen sizes max-width:767px', 'codeless-builder' ),
                    'default'     => 0,
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),
                    
                ),

                'css_style_767' => array(
                    'type' => 'css_tool',
                    'label' => 'Tool',
                    'selector' => '.cl_column_inner > .wrapper',
                    'css_property' => '',
                    'default' => array('margin-top' => 0),
                    'media_query' => '(max-width: 767px)',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'css_style_767_colinner_bool',
                            'operator' => '==',
                            'value'    => true,
                        ),
                    ),
                ),

        'responsive_tab_end' => array(
            'type' => 'tab_end',
            'label' => 'Responsive',
            'tabid' => 'responsive'
        ),
        
        
    ),
    
) );


/* Text */
cl_builder_map(array(
    'type'        => 'clelement',
    'label'       => esc_attr__( 'Text', 'codeless-builder' ),
    'section'     => 'cl_codeless_page_builder',
    //'priority'    => 10,
    'icon'		  => 'icon-software-font-smallcaps',
    'transport'   => 'postMessage',
    'settings'    => 'cl_text',
    'is_container' => false,
    'marginPositions' => array('top'),
    'fields' => array(
        'content' => array(
            'type'     => 'inline_text',
            'priority' => 10,
            'selector' => '.cl-text',
            'label'       => esc_attr__( 'Text', 'codeless-builder' ),
            'tooltip' => esc_attr__( 'This will be the label for your link', 'codeless-builder' ),
            'default'     => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores ',
            'holder' => 'div'
        ),

        'margin_paragraphs' => array(

                    'type' => 'slider',
                    'label' => 'Distance between paragraphs',
                    'default' => '10',
                    'selector' => '.cl-text p',
                    'css_property' => array('margin-top', 'margin-bottom'),
                    'choices'     => array(
                        'min'  => '0',
                        'max'  => '40',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                ),

        'typography_start' => array(
                'type' => 'group_start',
                'label' => 'Typography',
                'groupid' => 'typography'
            ),

            'custom_typography' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Custom Typography', 'codeless-builder' ),
                    'default'     => 0,
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),
                ),

            
            'text_font_size' => array(

                    'type' => 'slider',
                    'label' => 'Text Font Size',
                    'default' => '14',
                    'selector' => '.cl-text',
                    'css_property' => 'font-size',
                    'choices'     => array(
                        'min'  => '14',
                        'max'  => '72',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_typography',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                ),

            'text_font_weight' => array(

                    'type' => 'inline_select',
                    'label' => 'Title Font Weight',
                    'default' => '400',
                    'selector' => '.cl-text',
                    'css_property' => 'font-weight',
                    'choices'     => array(
                        '100' => '100',
                        '200' => '200',
                        '300' => '300',
                        '400' => '400',
                        '500' => '500',
                        '600' => '600',
                        '700' => '700',
                        '800' => '800'
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_typography',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                ),
                
            'text_line_height' => array(

                    'type' => 'slider',
                    'label' => 'Line Height',
                    'default' => '20',
                    'selector' => '.cl-text',
                    'css_property' => 'line-height',
                    'choices'     => array(
                        'min'  => '20',
                        'max'  => '100',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_typography',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                ),
            
            'text_transform' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Text Transform', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'selector' => '.cl-text',
                    'css_property' => 'text-transform',
                    'choices' => array(
                        'none' => 'None',
                        'uppercase' => 'Uppercase'
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_typography',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                    
                ),
                
            
                
            
            'text_color' => array(
                    'type' => 'color',
                    'label' => 'Color',
                    'default' => '',
                    'selector' => '.cl-text',
                    'css_property' => 'color',
                    'alpha' => true,
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_typography',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
            ),

        'typography_end' => array(
                'type' => 'group_end',
                'label' => 'Typography',
                'groupid' => 'typography'
        ),


        'responsive_start' => array(
            'type' => 'group_start',
            'label' => 'Responsive',
            'groupid' => 'responsive' 
        ),

            'custom_responsive_992_bool' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Custom Max-width:992px', 'codeless-builder' ),
                    'tooltip' => 'Add a custom size for this heading for screens smaller than 992px',
                    'default'     => 0,
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),
                    array(
                            'setting'  => 'typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    
                ),

                'custom_responsive_992_size' => array(
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Custom Font size Max-width:992px', 'codeless-builder' ),
                    'tooltip' => esc_attr__( 'Add a custom size for this heading for screens smaller than 992px', 'codeless-builder' ),
                    'default'     => '24px',
                    'selector' => '.cl-text',
                    'css_property' => 'font-size',
                    'media_query' => '(max-width: 992px)',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_responsive_992_bool',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                ),

                'custom_responsive_992_line_height' => array(
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Custom Line Height Max-width:992px', 'codeless-builder' ),
                    'tooltip' => esc_attr__( 'Add a custom line height for this heading for screens smaller than 992px', 'codeless-builder' ),
                    'default'     => '30px',
                    'selector' => '.cl-text',
                    'css_property' => 'line-height',
                    'media_query' => '(max-width: 992px)',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_responsive_992_bool',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                ),

            'custom_responsive_768_bool' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Custom Max-width:768px', 'codeless-builder' ),
                    'tooltip' => 'Add a custom size for this heading for screens smaller than 768px',
                    'default'     => 0,
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),
                    array(
                            'setting'  => 'typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    
                ),

                'custom_responsive_768_size' => array(
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Custom Font size Max-width:768px', 'codeless-builder' ),
                    'tooltip' => esc_attr__( 'Add a custom size for this heading for screens smaller than 768px', 'codeless-builder' ),
                    'default'     => '18px',
                    'selector' => '.cl-text',
                    'css_property' => 'font-size',
                    'media_query' => '(max-width: 768px)',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_responsive_768_bool',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                ),

                'custom_responsive_768_line_height' => array(
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Custom Line Height Max-width:768px', 'codeless-builder' ),
                    'tooltip' => esc_attr__( 'Add a custom line height for this heading for screens smaller than 768px', 'codeless-builder' ),
                    'default'     => '26px',
                    'selector' => '.cl-text',
                    'css_property' => 'line-height',
                    'media_query' => '(max-width: 768px)',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_responsive_768_bool',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                ),


        'responsive_end' => array(
            'type' => 'group_end',
            'label' => 'Responsive',
            'groupid' => 'responsive'
        ),


            'animation_start' => array(
                'type' => 'group_start',
                'label' => 'Animation',
                'groupid' => 'animation'
            ),
            
                'animation' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Animation Effect', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'choices' => array(
                        'none'	=> 'None',
                        'top-t-bottom' =>	'Top-Bottom',
                        'bottom-t-top' =>	'Bottom-Top',
                        'right-t-left' => 'Right-Left',
                        'left-t-right' => 'Left-Right',
                        'alpha-anim' => 'Fade-In',	
                        'zoom-in' => 'Zoom-In',	
                        'zoom-out' => 'Zoom-Out',
                        'zoom-reverse' => 'Zoom-Reverse',
                    ),
                    'selector' => '.cl-text'
                ),
                
                'animation_delay' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Animation Delay', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'choices' => array(
                        'none'	=> 'None',
                        '100' =>	'ms 100',
                        '200' =>	'ms 200',
                        '300' =>	'ms 300',
                        '400' =>	'ms 400',
                        '500' =>	'ms 500',
                        '600' =>	'ms 600',
                        '700' =>	'ms 700',
                        '800' =>	'ms 800',
                        '900' =>	'ms 900',
                        '1000' =>	'ms 1000',
                        '1100' =>	'ms 1100',
                        '1200' =>	'ms 1200',
                        '1300' =>	'ms 1300',
                        '1400' =>	'ms 1400',
                        '1500' =>	'ms 1500',
                        '1600' =>	'ms 1600',
                        '1700' =>	'ms 1700',
                        '1800' =>	'ms 1800',
                        '1900' =>	'ms 1900',
                        '2000' =>	'ms 2000',
                    
                    ),
                    'selector' => '.cl-text',
                    'htmldata' => 'delay',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'animation',
                            'operator' => '!=',
                            'value'    => 'none',
                        ),
                    ),
                ),
                
                'animation_speed' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Animation Speed', 'codeless-builder' ),
                    
                    'default'     => '400',
                    'choices' => array(
                        'none'	=> 'None',
                        '100' =>	'ms 100',
                        '200' =>	'ms 200',
                        '300' =>	'ms 300',
                        '400' =>	'ms 400',
                        '500' =>	'ms 500',
                        '600' =>	'ms 600',
                        '700' =>	'ms 700',
                        '800' =>	'ms 800',
                        '900' =>	'ms 900',
                        '1000' =>	'ms 1000'
                        
                    
                    ),
                    'selector' => '.cl-text',
                    'htmldata' => 'speed',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'animation',
                            'operator' => '!=',
                            'value'    => 'none',
                        ),
                    ),
                ),
            
            'animation_end' => array(
                'type' => 'group_end',
                'label' => 'Animation',
                'groupid' => 'animation'
            ),

        'css_style' => array(
            'type' => 'css_tool',
            'label' => 'Tool',
            'selector' => '.cl-text',
            'css_property' => '',
            'default' => array('margin-top' => '35px')
        ),
    ),
    
) );                 


/* Custom Heading */
cl_builder_map(array(
    'type'        => 'clelement',
    'label'       => esc_attr__( 'Custom Heading', 'codeless-builder' ),
    'section'     => 'cl_codeless_page_builder',
    //'priority'    => 10,
    'icon'		  => 'icon-software-character',
    'transport'   => 'postMessage',
    'settings'    => 'cl_custom_heading',
    'marginPositions' => array('top'),
    'is_container' => false,
    'fields' => array(
        'content' => array(
            'type'     => 'inline_text',
            'priority' => 10,
            'selector' => '.cl-custom-heading',
            'label'       => esc_attr__( 'Text', 'codeless-builder' ),
            'tooltip' => esc_attr__( 'This will be the label for your link', 'codeless-builder' ),
            'default'     => 'Custom Heading',
            'holder' => 'h2',
            'group_vc' => esc_attr__('General', 'codeless-builder')
        ),

        'option_start' => array(
                'type' => 'group_start',
                'label' => 'Options',
                'groupid' => 'options'
            ),

        'tag' => array(
                    'type'        => 'inline_select',
                    'label'       => esc_html__( 'Heading Tag', 'codeless-builder' ),
                    'tooltip' => '',
                    'default'     => 'h2',
                    'priority'    => 10,
                    'selector' => '.cl-custom-heading',
                    'choices'     => array(
                        'h1'  => esc_attr__( 'H1', 'codeless-builder' ),
                        'h2' => esc_attr__( 'H2', 'codeless-builder' ),
                        'h3' => esc_attr__( 'H3', 'codeless-builder' ),
                        'h4' => esc_attr__( 'H4', 'codeless-builder' ),
                        'h5' => esc_attr__( 'H5', 'codeless-builder' ),
                        'h6' => esc_attr__( 'H6', 'codeless-builder' ),
                    ),
                    'group_vc' => esc_attr__('General', 'codeless-builder')
        ),

        'option_end' => array(
                'type' => 'group_end',
                'label' => 'Options',
                'groupid' => 'options'
            ),

        'typography_start' => array(
                'type' => 'group_start',
                'label' => 'Typography',
                'groupid' => 'typography'
            ),

        'typography' => array(
                    'type'        => 'inline_select',
                    'label'       => esc_html__( 'Title Typography', 'codeless-builder' ),
                    'tooltip' => 'Select one of the predefined title typography styles on Styling Section or select "Custom Font" if you want to edit the typography of Title. SHIFT-CLICK on Element if you want to modify one of the predefined Style',
                    'default'     => 'h2',
                    'priority'    => 10,
                    'selector' => '.cl-custom-heading',
                    'selectClass' => 'custom_font ',
                    'choices'     => array(
                        'h1'  => esc_attr__( 'H1', 'codeless-builder' ),
                        'h2' => esc_attr__( 'H2', 'codeless-builder' ),
                        'h3' => esc_attr__( 'H3', 'codeless-builder' ),
                        'h4' => esc_attr__( 'H4', 'codeless-builder' ),
                        'h5' => esc_attr__( 'H5', 'codeless-builder' ),
                        'h6' => esc_attr__( 'H6', 'codeless-builder' ),
                        'custom_font' => esc_attr__( 'Custom Font', 'codeless-builder' ),
                    ),
                    'group_vc' => esc_attr__('Typography', 'codeless-builder')
                ),

            
            'text_font_family' => array(

                    'type' => 'inline_select',
                    'label' => 'Font Family',
                    'default' => 'theme_default',
                    'selector' => '.cl-custom-heading',
                    'css_property' => 'font-family',
                    'choices'     => codeless_get_google_fonts(),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr__('Typography', 'codeless-builder')
                ),

            'text_font_size' => array(

                    'type' => 'slider',
                    'label' => 'Font Size',
                    'default' => '22',
                    'selector' => '.cl-custom-heading',
                    'css_property' => 'font-size',
                    'choices'     => array(
                        'min'  => '14',
                        'max'  => '160',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr__('Typography', 'codeless-builder')
                ),

            'text_font_weight' => array(

                    'type' => 'inline_select',
                    'label' => 'Font Weight',
                    'default' => '700',
                    'selector' => '.cl-custom-heading',
                    'css_property' => 'font-weight',
                    'choices'     => array(
                        '100' => '100',
                        '200' => '200',
                        '300' => '300',
                        '400' => '400',
                        '500' => '500',
                        '600' => '600',
                        '700' => '700',
                        '800' => '800'
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr__('Typography', 'codeless-builder')
                ),
                
            'text_line_height' => array(

                    'type' => 'slider',
                    'label' => 'Line Height',
                    'default' => '34',
                    'selector' => '.cl-custom-heading',
                    'css_property' => 'line-height',
                    'choices'     => array(
                        'min'  => '20',
                        'max'  => '200',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr__('Typography', 'codeless-builder')
                ),

            'text_letterspace' => array(

                        'type' => 'slider',
                        'label' => 'Letter-Spacing',
                        'default' => '0',
                        'selector' => '.cl-custom-heading',
                        'css_property' => 'letter-spacing',
                        'choices'     => array(
                            'min'  => '0',
                            'max'  => '4',
                            'step' => '0.05',
                            'suffix' => 'px'
                        ),
                        'suffix' => 'px',
                        'cl_required'    => array(
                            array(
                                'setting'  => 'typography',
                                'operator' => '==',
                                'value'    => 'custom_font',
                            ),
                        ),
                        'group_vc' => esc_attr__('Typography', 'codeless-builder')
                    ),
            
            'text_transform' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Text Transform', 'codeless-builder' ),
                    
                    'default'     => 'uppercase',
                    'selector' => '.cl-custom-heading',
                    'css_property' => 'text-transform',
                    'choices' => array(
                        'none' => 'None',
                        'uppercase' => 'Uppercase'
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr__('Typography', 'codeless-builder')
                    
                ),
                
            
                
            
            'text_color' => array(
                    'type' => 'color',
                    'label' => 'Color',
                    'default' => '',
                    'selector' => '.cl-custom-heading',
                    'css_property' => 'color',
                    'alpha' => true,
                    'cl_required'    => array(
                        array(
                            'setting'  => 'typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr__('Typography', 'codeless-builder')
            ),

        'typography_end' => array(
                'type' => 'group_end',
                'label' => 'Typography',
                'groupid' => 'typography'
        ),


        'responsive_start' => array(
            'type' => 'group_start',
            'label' => 'Responsive',
            'groupid' => 'responsive' 
        ),

            'custom_responsive_992_bool_ch' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Custom Max-width:992px', 'codeless-builder' ),
                    'tooltip' => 'Add a custom size for this heading for screens smaller than 992px',
                    'default'     => 0,
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),
                    array(
                            'setting'  => 'typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                        'group_vc' => esc_attr__('Responsive', 'codeless-builder')
                    
                ),

                'custom_responsive_992_size' => array(
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Custom Font size Max-width:992px', 'codeless-builder' ),
                    'tooltip' => esc_attr__( 'Add a custom size for this heading for screens smaller than 992px', 'codeless-builder' ),
                    'default'     => '24px',
                    'selector' => '.cl-custom-heading',
                    'css_property' => 'font-size',
                    'media_query' => '(max-width: 992px)',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_responsive_992_bool_ch',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                    'group_vc' => esc_attr__('Responsive', 'codeless-builder')
                ),

                'custom_responsive_992_line_height' => array(
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Custom Line Height Max-width:992px', 'codeless-builder' ),
                    'tooltip' => esc_attr__( 'Add a custom line height for this heading for screens smaller than 992px', 'codeless-builder' ),
                    'default'     => '30px',
                    'selector' => '.cl-custom-heading',
                    'css_property' => 'line-height',
                    'media_query' => '(max-width: 992px)',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_responsive_992_bool_ch',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                    'group_vc' => esc_attr__('Responsive', 'codeless-builder')
                ),

            'custom_responsive_768_bool_ch' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Custom Max-width:768px', 'codeless-builder' ),
                    'tooltip' => 'Add a custom size for this heading for screens smaller than 768px',
                    'default'     => 0,
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),
                    array(
                            'setting'  => 'typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                        'group_vc' => esc_attr__('Responsive', 'codeless-builder')
                    
                ),

                'custom_responsive_768_size' => array(
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Custom Font size Max-width:768px', 'codeless-builder' ),
                    'tooltip' => esc_attr__( 'Add a custom size for this heading for screens smaller than 768px', 'codeless-builder' ),
                    'default'     => '18px',
                    'selector' => '.cl-custom-heading',
                    'css_property' => 'font-size',
                    'media_query' => '(max-width: 768px)',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_responsive_768_bool_ch',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                    'group_vc' => esc_attr__('Responsive', 'codeless-builder')
                ),

                'custom_responsive_768_line_height' => array(
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Custom Line Height Max-width:768px', 'codeless-builder' ),
                    'tooltip' => esc_attr__( 'Add a custom line height for this heading for screens smaller than 768px', 'codeless-builder' ),
                    'default'     => '26px',
                    'selector' => '.cl-custom-heading',
                    'css_property' => 'line-height',
                    'media_query' => '(max-width: 768px)',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_responsive_768_bool_ch',
                            'operator' => '==',
                            'value'    => 1,
                        ),
                    ),
                    'group_vc' => esc_attr__('Responsive', 'codeless-builder')
                ),


        'responsive_end' => array(
            'type' => 'group_end',
            'label' => 'Responsive',
            'groupid' => 'responsive'
        ),


            'animation_start' => array(
                'type' => 'group_start',
                'label' => 'Animation',
                'groupid' => 'animation'
            ),
            
                'animation' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Animation Effect', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'choices' => array(
                        'none'	=> 'None',
                        'top-t-bottom' =>	'Top-Bottom',
                        'bottom-t-top' =>	'Bottom-Top',
                        'right-t-left' => 'Right-Left',
                        'left-t-right' => 'Left-Right',
                        'alpha-anim' => 'Fade-In',	
                        'zoom-in' => 'Zoom-In',	
                        'zoom-out' => 'Zoom-Out',
                        'zoom-reverse' => 'Zoom-Reverse',
                    ),
                    'selector' => '.cl-custom-heading',
                    'group_vc' => esc_attr__('Animation', 'codeless-builder')
                ),
                
                'animation_delay' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Animation Delay', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'choices' => array(
                        'none'	=> 'None',
                        '100' =>	'ms 100',
                        '200' =>	'ms 200',
                        '300' =>	'ms 300',
                        '400' =>	'ms 400',
                        '500' =>	'ms 500',
                        '600' =>	'ms 600',
                        '700' =>	'ms 700',
                        '800' =>	'ms 800',
                        '900' =>	'ms 900',
                        '1000' =>	'ms 1000',
                        '1100' =>	'ms 1100',
                        '1200' =>	'ms 1200',
                        '1300' =>	'ms 1300',
                        '1400' =>	'ms 1400',
                        '1500' =>	'ms 1500',
                        '1600' =>	'ms 1600',
                        '1700' =>	'ms 1700',
                        '1800' =>	'ms 1800',
                        '1900' =>	'ms 1900',
                        '2000' =>	'ms 2000',
                    
                    ),
                    'selector' => '.cl-custom-heading',
                    'htmldata' => 'delay',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'animation',
                            'operator' => '!=',
                            'value'    => 'none',
                        ),
                    ),
                    'group_vc' => esc_attr__('Animation', 'codeless-builder')
                ),
                
                'animation_speed' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Animation Speed', 'codeless-builder' ),
                    
                    'default'     => '400',
                    'choices' => array(
                        'none'	=> 'None',
                        '100' =>	'ms 100',
                        '200' =>	'ms 200',
                        '300' =>	'ms 300',
                        '400' =>	'ms 400',
                        '500' =>	'ms 500',
                        '600' =>	'ms 600',
                        '700' =>	'ms 700',
                        '800' =>	'ms 800',
                        '900' =>	'ms 900',
                        '1000' =>	'ms 1000'
                        
                    
                    ),
                    'selector' => '.cl-custom-heading',
                    'htmldata' => 'speed',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'animation',
                            'operator' => '!=',
                            'value'    => 'none',
                        ),
                    ),
                    'group_vc' => esc_attr__('Animation', 'codeless-builder')
                ),
            
            'animation_end' => array(
                'type' => 'group_end',
                'label' => 'Animation',
                'groupid' => 'animation'
            ),

        'box_start' => array(
                'type' => 'group_start',
                'label' => 'Box Design',
                'groupid' => 'box'
        ),
            'css_style' => array(
                'type' => 'css_tool',
                'label' => 'Tool',
                'selector' => '.cl-custom-heading',
                'css_property' => '',
                'default' => array('margin-top' => '35px'),
                'group_vc' => esc_attr__('Design', 'codeless-builder')
            ),

            'border_style' => array(
                        'type'     => 'inline_select',
                        'priority' => 10,
                        'label'       => esc_attr__( 'Border Style', 'codeless-builder' ),
                        
                        'default'     => 'solid',
                        'choices' => array(
                            'solid'	=> 'solid',
                            'dotted' =>	'dotted',
                            'dashed' =>	'dashed',
                            'double' => 'double',
                            'groove' => 'groove',
                            'ridge' => 'ridge',	
                            'inset' => 'inset',	
                            'outset' => 'outset',
                        ),
                        'selector' => '.cl-custom-heading',
                        'css_property' => 'border-style',
                        'group_vc' => esc_attr__('Design', 'codeless-builder')
                    ),
                    
            'border_color' => array(
                'type' => 'color',
                'label' => 'Border Color',
                'default' => '',
                'selector' => '.cl-custom-heading',
                'css_property' => 'border-color',
                'alpha' => true,
                'group_vc' => esc_attr__('Design', 'codeless-builder')
            ),
        'box_end' => array(
                'type' => 'group_end',
                'label' => 'Box Design',
                'groupid' => 'box'
        ),
    ),
    
) );

/* Button */
cl_builder_map(array(
    'type'        => 'clelement',
    'label'       => esc_attr__( 'Button', 'codeless-builder' ),
    'section'     => 'cl_codeless_page_builder',
    //'priority'    => 10,
    'icon'		  => 'icon-basic-signs',
    'transport'   => 'postMessage',
    'settings'    => 'cl_button',
    'is_container' => false,
    'marginPositions' => array('top'),
    'fields' => array(
        'btn_title' => array(
            'type'     => 'inline_text',
            'priority' => 10,
            'selector' => '.cl-btn span',
            'label'       => esc_attr__( 'Text', 'codeless-builder' ),
            'tooltip' => esc_attr__( 'This will be the label for your link', 'codeless-builder' ),
            'default'     => 'View More',
            'only_text' => true,
            'replace_type_vc' => 'textfield',
            'holder' => 'h2'
        ),

        

        'button_style' => array(

            'type' => 'inline_select',
            'priority' => 10,
            'label' => 'Button Style',
            'default'=> 'material_square',
            'choices' => array(

                'material_square' => 'Material Square',
                'material_circular' => 'Material Circular',
                'text_effect' => 'Text Effect',
                'rounded_border' => 'Rounded Border'

            ),

            'selector' => '.cl-btn',
            'selectClass' => 'btn-style-',
            
        ),

        'button_layout' =>  array(

            'type' => 'inline_select',
            'priority' => 10,
            'label' => 'Button Layout',
            'default' => 'medium',
            'choices'=> array(

                'extra-small' => 'Extra Small',
                'small' => 'Small',
                'medium' => 'Medium',
                'large' => 'Large',
                'extra-large' => 'Extra Large',
            
            ),

            'selector'=> '.cl-btn',
            'selectClass' => 'btn-layout-',
           
        ),

        'button_font' => array(

            'type' => 'inline_select',
            'priority' => 10,
            'label' => 'Button Font',
            'default'  => 'medium',
            'choices' => array(

                'extra-small' => 'Extra Small',
                'small' => 'Small',
                'medium' => 'Medium',
                'large' => 'Large',
                'extra-large' => 'Extra Large',
                'custom' => 'Custom',

            ),


            'selector'=> '.cl-btn',
            'selectClass' => 'btn-font-',
            
        ),

        'button_bg_color'=> array(

            'type' => 'color',
            'priority'=> 10,
            'label' => 'Button Background Color',
            'default' => get_theme_mod('primary_color'),
            'selector' => '.cl-btn',
            'css_property' => 'background-color',
            'alpha' => true,
            
        ),

        'button_bg_color_hover'=> array(

            'type' => 'color',
            'priority'=> 10,
            'label' => 'Button Background Color on Hover',
            'default' => get_theme_mod('primary_color'),
            'selector' => '.cl-btn',
            'alpha' => true,
            'reloadTemplate' => true

        ),

        'button_font_color' => array(

            'type' => 'color',
            'priority'=> 10,
            'label' => 'Button Font Color', 
            'default'=> '#ffffff',
            'selector'=> '.cl-btn',
            'css_property'=> 'color',
            'alpha' => true,
    
        ),

        'button_font_color_hover' => array(

            'type' => 'color',
            'priority'=> 10,
            'label' => 'Button Font Color on Hover', 
            'default'=> '#ffffff',
            'alpha' => true,
            'reloadTemplate' => true
        ),



        'button_border_color' => array(
            
            'type'=> 'color',
            'priority'=> 10,
            'label'=> 'Button Border Color',
            'default' => 'transparent',
            'selector' => '.cl-btn-custom',
            'css_property' => 'border-color',
            'alpha' => true,
            

        ),	

        'button_border_color_hover' => array(
            
            'type'=> 'color',
            'priority'=> 10,
            'label'=> 'Button Border Color Hover',
            'default' => 'transparent',
            'alpha' => true,
            'reloadTemplate' => true

        ),	


        'link' => array(
            'type'     => 'text',
            'priority' => 10,
            'label'       => esc_attr__( 'Link', 'codeless-builder' ),
            'default'     => '#'
        ),

        'link_target' => array(

                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Link Target', 'codeless-builder' ),
                'default'     => '_self',

                'choices'     => array(
                    '_self' => esc_html__('_self', 'codeless-builder'),
                    '_blank' => esc_html__('_blank', 'codeless-builder'),				
                ),
                'reloadTemplate' => true
        ),

        'css_style' => array(
                'type' => 'css_tool',
                'label' => 'Tool',
                'selector' => '.cl-btn-div',
                'css_property' => '',
                'default' => array('margin-top' => '35px')
        ),

        'animation' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Effect', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none'	=> 'None',
                    'top-t-bottom' =>	'Top-Bottom',
                    'bottom-t-top' =>	'Bottom-Top',
                    'right-t-left' => 'Right-Left',
                    'left-t-right' => 'Left-Right',
                    'alpha-anim' => 'Fade-In',	
                    'zoom-in' => 'Zoom-In',	
                    'zoom-out' => 'Zoom-Out',
                    'zoom-reverse' => 'Zoom-Reverse',
                ),
                'selector' => '.cl-btn-div'
            ),
            
            'animation_delay' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Delay', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none'	=> 'None',
                    '100' =>	'ms 100',
                    '200' =>	'ms 200',
                    '300' =>	'ms 300',
                    '400' =>	'ms 400',
                    '500' =>	'ms 500',
                    '600' =>	'ms 600',
                    '700' =>	'ms 700',
                    '800' =>	'ms 800',
                    '900' =>	'ms 900',
                    '1000' =>	'ms 1000',
                    '1100' =>	'ms 1100',
                    '1200' =>	'ms 1200',
                    '1300' =>	'ms 1300',
                    '1400' =>	'ms 1400',
                    '1500' =>	'ms 1500',
                    '1600' =>	'ms 1600',
                    '1700' =>	'ms 1700',
                    '1800' =>	'ms 1800',
                    '1900' =>	'ms 1900',
                    '2000' =>	'ms 2000',
                
                ),
                'selector' => '.cl-btn-div',
                'htmldata' => 'delay',
                'cl_required'    => array(
                    array(
                        'setting'  => 'animation',
                        'operator' => '!=',
                        'value'    => 'none',
                    ),
                ),
            ),
            
            'animation_speed' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Speed', 'codeless-builder' ),
                
                'default'     => '400',
                'choices' => array(
                    'none'	=> 'None',
                    '100' =>	'ms 100',
                    '200' =>	'ms 200',
                    '300' =>	'ms 300',
                    '400' =>	'ms 400',
                    '500' =>	'ms 500',
                    '600' =>	'ms 600',
                    '700' =>	'ms 700',
                    '800' =>	'ms 800',
                    '900' =>	'ms 900',
                    '1000' =>	'ms 1000'
                    
                
                ),
                'selector' => '.cl-btn-div',
                'htmldata' => 'speed',
                'cl_required'    => array(
                    array(
                        'setting'  => 'animation',
                        'operator' => '!=',
                        'value'    => 'none',
                    ),
                )
            ),
    )
));

/* Divider */
cl_builder_map(array(
'type'        => 'clelement',
'label'       => esc_attr__( 'Divider', 'codeless-builder' ),
'section'     => 'cl_codeless_page_builder',
//'priority'    => 10,
'icon'		  => 'icon-arrows-minus',
'transport'   => 'postMessage',
'settings'    => 'cl_divider',
'use_on_header' => true,
'is_container' => false,
'marginPositions' => array('top'),
'fields' => array(
    'height' => array(
        'type'     => 'slider',
        'label' => 'Divider height',
        'default' => '1',
        'selector' => '.cl_divider .inner',
        'css_property' => 'border-top-width',
        'choices'     => array(
                    'min'  => '0',
                    'max'  => '30',
                    'step' => '1',
                    'suffix' => 'px'
                ),
        'suffix' => 'px',

        'label'       => esc_attr__( 'Divider Height', 'codeless-builder' ),
        'tooltip' => esc_attr__( 'Set the divider height', 'codeless-builder' )
        
    ),

    'width_full' => array(

                'type'        => 'switch',
                'label'       => esc_html__( 'Set divider fullwidth', 'codeless-builder' ),
                'default'     => 1,
                'priority'    => 10,
                'choices'     => array(
                    'on'  => esc_attr__( 'On', 'codeless-builder' ),
                    'off' => esc_attr__( 'Off', 'codeless-builder' ),
                ),
            ),
        
        
    'width' => array(

                'type' => 'slider',
                'label' => 'Set the divider width',
                'default' => '300',
                'selector' => '.cl_divider .wrapper',
                'css_property' => 'width',
                'choices'     => array(
                    'min'  => '1',
                    'max'  => '1070',
                    'step' => '1',
                    'suffix' => 'px'
                ),
                'suffix' => 'px',

                'cl_required'    => array(
                    array(
                        'setting'  => 'width_full',
                        'operator' => '==',
                        'value'    => 0,
                    ),
                ),
            ),


    'color' => array(
                'type' => 'color',
                'label' => 'Set Color',
                'default' => '#222',
                'selector' => '.cl_divider .inner',
                'css_property' => 'border-color',
                'alpha' => true
                
        ),

    'border_style' => array(
                'type' => 'inline_select',
                'label' => 'Set the border style',
                'default' => 'solid',
                'selector' => '.cl_divider .inner',
                'css_property' => 'border-style',
                'choices'     => array(
                    'solid'	=> 'solid',
                    'dotted' =>	'dotted',
                    'dashed' =>	'dashed',
                    'double' => 'double',
                    'groove' => 'groove',
                    'ridge' => 'ridge',	
                    'inset' => 'inset',	
                    'outset' => 'outset'
                
                ),
                
                
        ),

    'align' => array( 

                'type' => 'inline_select',
                'label' => 'Set the border align',
                'default' => '',
                'selector' => '.cl_divider .wrapper',
                'choices'     => array(
                    'left_divider'	=> 'left',
                    'center_divider' =>	'center',
                    'right_divider' =>	'right',
                    
                                                
                ),
                'selectClass' => '',
                'cl_required'    => array(
                    array(
                        'setting'  => 'width_full',
                        'operator' => '==',
                        'value'    => 0,
                    ),
                ),


        ),


    'divider_style' => array(

                'type' => 'inline_select',
                'label' => 'Select the style of the divider',
                'default' => 'simple',
                'selector' => '.cl_divider .wrapper',
                'choices'     => array(
                    'simple' => 'Simple',
                    'two' => 'Two Borders',
                    'icon' => 'With Centred Icon'
                
                ),
                'reloadTemplate' => true
                
            ),
            
    'icon' => array(
                'type'     => 'select_icon',
                'priority' => 10,
                'label'       => esc_attr__( 'Select Icon', 'codeless-builder' ),
                'default'     => 'cl-icon-camera2',
                'selector' => '.cl_divider i',
                'selectClass' => ' ',
                'cl_required'    => array(
                    array(
                        'setting'  => 'divider_style',
                        'operator' => '==',
                        'value'    => 'icon',
                    ),
                )
            ),

    'color_icon' => array(
                'type'     => 'color',
                'priority' => 10,
                'label'       => esc_attr__( 'Icon Color', 'codeless-builder' ),
                'default'     => '#222',
                'selector' => '.cl_divider .wrapper > i',
                'css_property' => 'color',
                'alpha' => true,
                'cl_required'    => array(
                    array(
                        'setting'  => 'divider_style',
                        'operator' => '==',
                        'value'    => 'icon',
                    ),
                ),
            ),

    'size_icon' => array(
                'type'     => 'slider',
                'priority' => 10,
                'label'       => esc_attr__( 'Icon size', 'codeless-builder' ),
                'default'     => '10',
                'selector' => '.cl_divider .wrapper > i',
                'css_property'=> 'font-size',
                'choices'     => array(
                    'min'  => '0',
                    'max'  => '30',
                    'step' => '1',
                    'suffix' => 'px'
                ),
                'suffix' => 'px',
                'cl_required'    => array(
                    array(
                        'setting'  => 'divider_style',
                        'operator' => '==',
                        'value'    => 'icon',
                    ),
                ),
            ),

    'css_style' => array(
            'type' => 'css_tool',
            'label' => 'Tool',
            'selector' => '.cl_divider',
            'css_property' => '',
            'default' => array('margin-top' => '35px')
        ),
        
        

    ),


));







/* Media */
cl_builder_map(array(
    'type'        => 'clelement',
    'label'       => esc_attr__( 'Media', 'codeless-builder' ),
    'section'     => 'cl_codeless_page_builder',
    //'priority'    => 10,
    'icon'		  => 'icon-basic-photo',
    'transport'   => 'postMessage',
    'settings'    => 'cl_media',
    'is_container' => false,
    'marginPositions' => array('top'),
    'fields' => array(
            'media_type' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Media Type', 'codeless-builder' ),
                
                'default'     => 'image',
                'choices' => array(
                    'image'	=> 'Image',
                    'video' =>	'Video'
                ),
                'selector' => '.cl_media',
                'selectClass' => 'type-', 
                'reloadTemplate' => true 
            ),

            'image' => array(
                'type'        => 'image',
                'label'       => esc_html__( 'Upload Image', 'codeless-builder' ),
                'default'     => '',
                'priority'    => 10,
                'image_get' => 'id',
                'reloadTemplate' => true,
                'choices' => array(),
                
            ),

            'video_mov' => array(
                    
                'type'     => 'text',
                'priority' => 10,
                'label'       => esc_attr__( 'Video Mov', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Add this video if you want to use it for live photo', 'codeless-builder' ),
                'default'     => '',
                'cl_required'    => array(
                    array(
                        'setting'  => 'media_type',
                        'operator' => '==',
                        'value'    => 'live',
                    ),
                ),
                'reloadTemplate' => true
            ),

            'position' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Position', 'codeless-builder' ),
                
                'default'     => 'stretch',
                'choices' => array(
                    'left'	=> 'Left',
                    'center' =>	'Center',
                    'right' => 'Right',
                    'stretch' => 'stretch' 
                ),
                'selector' => '.cl_media',
                'selectClass' => 'position_',
                
            ),


           
            'shadow' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Shadow', 'codeless-builder' ),
                    'tooltip' => 'Switch on/off shadow',
                    'default'     => 1,
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),

                    'selector' => '.cl_media',
                    'addClass' => 'add-shadow'
                ),

            'image_size' => array(
                    'type'        => 'inline_select',
                    'label'       => esc_html__( 'Image Size', 'codeless-builder' ),
                    'tooltip' => "",
                    'default'     => 'full',
                    'priority'    => 10,
                    'choices'     => codeless_get_additional_image_sizes(),
                    'reloadTemplate' => true,
                    'cl_required'    => array(
                        array(
                            'setting'  => 'media_type',
                            'operator' => '==',
                            'value'    => 'image',
                        ),
                    ),
                ),

            'custom_width_bool_media' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Custom Width?', 'codeless-builder' ),
                    'tooltip' => 'Add a custom width for this media',
                    'default'     => 0,
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),
                    'selector' => '.cl_media',
                    
                    'addClass' => 'cl-custom-width'
                ),

            'custom_width' => array(
                'type'     => 'text',
                'priority' => 10,
                'label'       => esc_attr__( 'Set Custom Width', 'codeless-builder' ),
                
                'default'     => '400px',
            
                'selector' => '.cl_media .inner',
                'css_property' => 'width',
                'cl_required'    => array(
                    array(
                        'setting'  => 'custom_width_bool_media',
                        'operator' => '==',
                        'value'    => 1,
                    ),
                ),
            ),

            'custom_link' => array(

                'type'     => 'text',
                'priority' => 10,
                'label'       => esc_attr__( 'Custom Link', 'codeless-builder' ),
                'default'     => '#',
                'reloadTemplate' => true
            ),

            'target' => array(

                'type' => 'inline_select',
                'priority' => 10,
                'label' => esc_attr__('Specify where to open the custom link ', 'codeless-builder'),
                'default' => '_self',
                'choices' => array(
                    '_self' => 'Open in the Same Window',
                    '_blank' => 'Open link in a new tab',
                    '_parent' => 'Open link in the parent frame',
                    '_top' => 'Open the link in the full body of the window'

                )

            ),

            'video_start' => array(
                'type' => 'group_start',
                'label' => 'Video',
                'groupid' => 'video'
            ),
            

            
            
                'video' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Video', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'choices' => array(
                        'none'	=> 'None',
                        'self' =>	'Self-Hosted',
                        'youtube' =>	'Youtube',
                        'vimeo' => 'Vimeo'
                    ),

                    'reloadTemplate' => true
                    //'customJS' => 'inlineEdit_videoSection'
                ),
                
                'video_mp4' => array(
                    
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Video Mp4', 'codeless-builder' ),
                    
                    'default'     => '',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'video',
                            'operator' => '==',
                            'value'    => 'self',
                        ),
                    ),
                    'reloadTemplate' => true
                ),
                'video_webm' => array(
                    
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Video Webm', 'codeless-builder' ),
                    
                    'default'     => '',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'video',
                            'operator' => '==',
                            'value'    => 'self',
                        ),
                    ),
                    'reloadTemplate' => true
                ),
                'video_ogv' => array(
                    
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Video Ogv', 'codeless-builder' ),
                    
                    'default'     => '',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'video',
                            'operator' => '==',
                            'value'    => 'self',
                        ),
                    ),
                    'reloadTemplate' => true
                ),

                
                
                'video_youtube' => array(
                    
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Youtube ID', 'codeless-builder' ),
                    
                    'default'     => '',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'video',
                            'operator' => '==',
                            'value'    => 'youtube',
                        ),
                    
                    ),
                    'reloadTemplate' => true
                ),
                
                'video_vimeo' => array(
                    
                    'type'     => 'text',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Vimeo ID', 'codeless-builder' ),
                    
                    'default'     => '',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'video',
                            'operator' => '==',
                            'value'    => 'vimeo',
                        ),
                    
                    ),
                    'reloadTemplate' => true
                ),
                
                'media_video_loop' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Video Loop', 'codeless-builder' ),
                    'tooltip' => 'Switch on/off video loop',
                    'default'     => 0,
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'video',
                            'operator' => '!=',
                            'value'    => 'none',
                        ),
                    ),
                    'reloadTemplate' => true
                ),

                'autoplay' => array(
                    'type'        => 'switch',
                    'label'       => esc_html__( 'Autoplay', 'codeless-builder' ),
                    'tooltip' => 'Switch on when video is used with Image Placeholder',
                    'default'     => 1,
                    'priority'    => 10,
                    'choices'     => array(
                        'on'  => esc_attr__( 'On', 'codeless-builder' ),
                        'off' => esc_attr__( 'Off', 'codeless-builder' ),
                    ),

                    'cl_required'    => array(
                        array(
                            'setting'  => 'video',
                            'operator' => '!=',
                            'value'    => 'none',
                        ),
                    ),

                    'reloadTemplate' => true
                ),

                'height' => array(
                    'type'     => 'slider',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Video / Embed Height', 'codeless-builder' ),
                    'tooltip' => esc_attr__( 'Use this only for embed links and for video with image placeholder.', 'codeless-builder' ),
                    'default'     => '400',
                    'choices'     => array(
                        'min'  => '0',
                        'max'  => '1000',
                        'step' => '10',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'selector' => '.cl_media iframe, .cl_media video',
                    'css_property' => 'height',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'video',
                            'operator' => '!=',
                            'value'    => 'none',
                        ),
                    ),
                    
                ),

            'video_end' => array(
                'type' => 'group_end',
                'label' => 'Video',
                'groupid' => 'video'
            ),

            'anim_start' => array(
                'type' => 'group_start',
                'label' => 'Animation',
                'groupid' => 'animation'
            ),

            'animation' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Effect', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none'	=> 'None',
                    'top-t-bottom' =>	'Top-Bottom',
                    'bottom-t-top' =>	'Bottom-Top',
                    'right-t-left' => 'Right-Left',
                    'left-t-right' => 'Left-Right',
                    'alpha-anim' => 'Fade-In',	
                    'zoom-in' => 'Zoom-In',	
                    'zoom-out' => 'Zoom-Out',
                    'zoom-reverse' => 'Zoom-Reverse',
                ),
                'selector' => '.cl_media'
            ),
            
            'animation_delay' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Delay', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none'	=> 'None',
                    '100' =>	'ms 100',
                    '200' =>	'ms 200',
                    '300' =>	'ms 300',
                    '400' =>	'ms 400',
                    '500' =>	'ms 500',
                    '600' =>	'ms 600',
                    '700' =>	'ms 700',
                    '800' =>	'ms 800',
                    '900' =>	'ms 900',
                    '1000' =>	'ms 1000',
                    '1100' =>	'ms 1100',
                    '1200' =>	'ms 1200',
                    '1300' =>	'ms 1300',
                    '1400' =>	'ms 1400',
                    '1500' =>	'ms 1500',
                    '1600' =>	'ms 1600',
                    '1700' =>	'ms 1700',
                    '1800' =>	'ms 1800',
                    '1900' =>	'ms 1900',
                    '2000' =>	'ms 2000',
                
                ),
                'selector' => '.cl_media',
                'htmldata' => 'delay',
                'cl_required'    => array(
                    array(
                        'setting'  => 'animation',
                        'operator' => '!=',
                        'value'    => 'none',
                    ),
                ),
            ),
            
            'animation_speed' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Speed', 'codeless-builder' ),
                
                'default'     => '400',
                'choices' => array(
                    'none'	=> 'None',
                    '100' =>	'ms 100',
                    '200' =>	'ms 200',
                    '300' =>	'ms 300',
                    '400' =>	'ms 400',
                    '500' =>	'ms 500',
                    '600' =>	'ms 600',
                    '700' =>	'ms 700',
                    '800' =>	'ms 800',
                    '900' =>	'ms 900',
                    '1000' =>	'ms 1000'
                    
                
                ),
                'selector' => '.cl_media',
                'htmldata' => 'speed',
                'cl_required'    => array(
                    array(
                        'setting'  => 'animation',
                        'operator' => '!=',
                        'value'    => 'none',
                    ),
                )
            ),

            'anim_end' => array(
                'type' => 'group_end',
                'label' => 'Animation',
                'groupid' => 'animation'
            ),

            'css_style' => array(
                'type' => 'css_tool',
                'label' => 'Tool',
                'selector' => '.cl_media',
                'css_property' => '',
                'default' => array('margin-top' => '15px')
            ),
    )
));

/* Gallery */
cl_builder_map(array(
    'type'        => 'clelement',
    'label'       => esc_attr__( 'Gallery', 'codeless-builder' ),
    'section'     => 'cl_codeless_page_builder',
    //'priority'    => 10,
    'icon'		  => 'icon-basic-picture-multiple',
    'transport'   => 'postMessage',
    'settings'    => 'cl_gallery',
    'is_container' => false,
    'marginPositions' => array('top'),
    'fields' => array(

        
        'images' => array(
            'type'     => 'image_gallery',
            'priority' => 10,
            'selector' => '',
            'label'       => esc_attr__( 'Images', 'codeless-builder' ),
            
            'reloadTemplate' => true,
        ),

        'items_per_row' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Items per Row', 'codeless-builder' ),
                
                'default'     => 'all',
                'choices' => array(
                    'all'	=> 'All',
                    '2' =>	'2 items',
                    '3' =>	'3 items',
                    '4' => '4 items',
                    '5' => '5 items',
                    '6' => '6 items',
                    '7' => '7 items',
                ),
                'selector' => '.cl_gallery',
                'selectClass' => 'items_',
                
        ),

        'distance' => array(

                'type' => 'slider',
                'label' => 'Distance between items',
                'default' => '10',
                'selector' => '.cl_gallery .gallery-item',
                'css_property' => 'padding',
                'choices'     => array(
                    'min'  => '0',
                    'max'  => '60',
                    'step' => '1',
                    'suffix' => 'px'
                ),
                'suffix' => 'px',
            ),


        

            'image_size' => array(
                    'type'        => 'inline_select',
                    'label'       => esc_html__( 'Image Size', 'codeless-builder' ),
                    'tooltip' => "",
                    'default'     => 'full',
                    'priority'    => 10,
                    'choices'     => codeless_get_additional_image_sizes(),
                    'reloadTemplate' => true
                ),


        'css_style' => array(
                'type' => 'css_tool',
                'label' => 'Tool',
                'selector' => '.cl_gallery',
                'css_property' => '',
                'default' => array('margin-top' => '35px')
        ),
    )
));

/* Service */
cl_builder_map(array(
'type'        => 'clelement',
'label'       => esc_attr__( 'Service', 'codeless-builder' ),
'section'     => 'cl_codeless_page_builder',
'tooltip' => 'Manage all options of the selected Row',
//'priority'    => 10,
'icon'		  => 'icon-arrows-circle-check',
'transport'   => 'postMessage',
'settings'    => 'cl_service',
'marginPositions' => array('top'),

'predefined'  => array(
    'simple_left_icon' => array(
        'photo' => get_template_directory_uri().'/img/predefined_elements/cl_service/simple_left_icon.png',
        'label' => 'Simple Left Icon',
        'content' => '[cl_service media="type_icon" title="Better Performance" icon="cl-icon-laptop2" css_style="{\'margin-top\':\'55px\'}_-_json" icon_font_size="34" wrapper_size="40" wrapper_distance="34" title_content_distance="5" animation="bottom-t-top" animation_delay="0"]A technology that renders via GPU, power saver, dependency manager, faster load. Load only scripts that needed for page.[/cl_service]'
    ),
    'simple_top_icon' => array(
        'photo' => get_template_directory_uri().'/img/predefined_elements/cl_service/simple_top_icon.png',
        'label' => 'Simple Top Icon',
        'content' => '[cl_service media="type_icon" layout_type="media_top" title="Experienced Support Team" icon="cl-icon-profile-male" css_style="{\'margin-top\':\'50px\'}_-_json" icon_font_size="42" wrapper_size="30" animation="bottom-t-top" animation_delay="100"]On the other hand, we denounce with righteous indignation and dislike men who are so beguiled[/cl_service]'
    ),
),


'is_container' => false,
'shiftClick' => array( 
        array( 'option' => 'h5_font_size', 'selector' => '.box-content h3' ) 
),
'fields' => array(

    'element_tabs' => array(
        'type' => 'show_tabs',
        'default' => 'general',
        'tabs' => array(
            'general' => array( 'General', 'cl-icon-settings' ),
            'design' => array( 'Design', 'cl-icon-tune' )
        )
    ),
    
    'general_tab_start' => array(
        'type' => 'tab_start',
        'label' => 'General',
        'tabid' => 'general'
    ),
    
        /* ----------------------------------------------- */
        
        'options_start' => array(
            'type' => 'group_start',
            'label' => 'Layout',
            'groupid' => 'layout'
        ),
            
            'media' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Media Type', 'codeless-builder' ),
                
                'default'     => 'type_text',
                'choices' => array(
                    'type_text' => 'Only Text',
                    'type_icon' => 'Icon',
                    'type_svg' => 'SVG',
                    'type_custom' => 'Custom IMG'
                ),
                'selector' => '.cl_service',
                'reloadTemplate' => true,
                'selectClass' => ''
            ),

            'icon' => array(
                'type'     => 'select_icon',
                'priority' => 10,
                'label'       => esc_attr__( 'Select Icon', 'codeless-builder' ),
                'default'     => 'cl-icon-camera2',
                'selector' => '.cl_service > .icon_wrapper i',
                'selectClass' => ' ',
                'cl_required'    => array(
                    array(
                        'setting'  => 'media',
                        'operator' => '==',
                        'value'    => 'type_icon',
                    ),
                )
            ),



            'image' => array(
                'type'        => 'image',
                'label'       => esc_html__( 'Upload Image', 'codeless-builder' ),
                'default'     => '',
                'priority'    => 10,
                'image_get' => 'id',
                'reloadTemplate' => true,
                'cl_required'    => array(
                    array(
                        'setting'  => 'media',
                        'operator' => '==',
                        'value'    => 'type_custom',
                    ),
                ),
                'choices' => array()
            ),

            'svg_source' => array(
                'type'        => 'text',
                'label'       => esc_html__( 'SVG URL', 'codeless-builder' ),
                'default'     => '',
                'priority'    => 10,
                'reloadTemplate' => true,
                'cl_required'    => array(
                    array(
                        'setting'  => 'media',
                        'operator' => '==',
                        'value'    => 'type_svg',
                    ),
                ),
                'choices' => array()
            ),

            'image_size' => array(
                    'type'        => 'inline_select',
                    'label'       => esc_html__( 'Image Size', 'codeless-builder' ),
                    'tooltip' => "You can change image sizes on Theme Panel -> <a target=\"_blank\" href=\"".admin_url('admin.php?page=codeless-panel-image-sizes')."\">Image Sizes Section</a>",
                    'default'     => 'full',
                    'priority'    => 10,
                    'choices'     => codeless_get_additional_image_sizes(),
                    'reloadTemplate' => true,
                    'cl_required'    => array(
                        array(
                            'setting'  => 'media',
                            'operator' => '==',
                            'value'    => 'type_custom',
                        ),
                    ),
            ),

            'title' => array(
                'type'     => 'inline_text',
                'priority' => 10,
                'selector' => '.cl_service .box-content > .service-title',
                'label'       => esc_attr__( 'Title', 'codeless-builder' ),
                'default'     => 'Custom Service Title',
                'replace_type_vc' => 'textfield',
                'holder' => 'h2'
            ),

            'subtitle_bool' => array(

                'type'        => 'switch',
                'label'       => esc_html__( 'Add subtitle', 'codeless-builder' ),
                'tooltip' => 'Switch On if you want a custom subtitle after Primary Title',
                'default'     => 0,
                'priority'    => 10,
                'choices'     => array(
                    'on'  => esc_attr__( 'On', 'codeless-builder' ),
                    'off' => esc_attr__( 'Off', 'codeless-builder' ),
                ),
                'reloadTemplate' => true
            ),

            'subtitle' => array(
                'type'     => 'inline_text',
                'priority' => 10,
                'selector' => '.cl_service .box-content > .subtitle',
                'label'       => esc_attr__( 'Subtitle', 'codeless-builder' ),
                'default'     => 'Custom Subtitle for service',
                'replace_type_vc' => 'textfield',
                'cl_required'    => array(
                    array(
                        'setting'  => 'subtitle_bool',
                        'operator' => '==',
                        'value'    => true,
                    ),
                ),
            ),

            'content' => array(
                'type'     => 'inline_text',
                'priority' => 10,
                'selector' => '.cl_service .box-content > .content',
                'label'       => esc_attr__( 'Content', 'codeless-builder' ),
                'default'     => 'On the other hand, we denounce with righteous indignation and dislike men who are so beguiled',
                'holder' => 'div'
            ),

            'animation_icon' => array(
                'type'        => 'switch',
                'label'       => esc_html__( 'SVG Animation', 'codeless-builder' ),
                'tooltip' => 'Switch to animate SVG on load',
                'default'     => 0,
                'priority'    => 10,
                'choices'     => array(
                    'on'  => esc_attr__( 'On', 'codeless-builder' ),
                    'off' => esc_attr__( 'Off', 'codeless-builder' ),
                ),
                'cl_required'    => array(
                    array(
                        'setting'  => 'media',
                        'operator' => '==',
                        'value'    => 'type_svg',
                    ),
                ),
            ),	
    
            'layout_type' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Layout Type', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Select layout type of service element', 'codeless-builder' ),
                'default'     => 'media_aside',
                'choices' => array(
                    'media_aside' => 'Media Aside',
                    'media_top' => 'Media Top'
                ),
                'selector' => '.cl_service',
                'selectClass' => '',
                'cl_required'    => array(
                    array(
                        'setting'  => 'media',
                        'operator' => '!=',
                        'value'    => 'type_text',
                    ),
                ),
            ),

            'layout_align' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Align Content and Icon', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Select the align of content and layout of service element', 'codeless-builder' ),
                'default'     => 'align_left',
                'choices' => array(
                    'align_left' => 'Align Left',
                    'align_center' => 'Align Center',
                    'align_right' => 'Align Right'
                ),
                'selector' => '.cl_service',
                'selectClass' => ''
            ),

            

            

            'wrapper' => array(
                
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Icon Wrapper', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Select the type of wrapper around Icon if you want one', 'codeless-builder' ),
                'default'     => 'wrapper_none',
                'choices' => array(
                    'wrapper_none' => 'None',
                    'wrapper_circle' => 'Circle',
                    'wrapper_square' => 'Square',
                    //'wrapper_hexagon' => 'Hexagon'
                ),
                'selector' => '.cl_service > .icon_wrapper',
                'selectClass' => '',
                'cl_required'    => array(
                    array(
                        'setting'  => 'media',
                        'operator' => '!=',
                        'value'    => 'type_text',
                    ),
                ),
            ),

            'hover_effect' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Hover Effect', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none' => 'None',
                    'wrapper_accent_color' => 'Wrapper Accent Color'
                ),
                'selector' => '.cl_service',
                'selectClass' => 'cl-hover-'
            ),

            'service_link' => array(
                'type'     => 'text',
                'priority' => 10,
                'selector' => '',
                'label'       => esc_attr__( 'Service Link', 'codeless-builder' ),
                'default'     => ''
            ),

        'options_end' => array(
            'type' => 'group_end',
            'label' => 'Layout',
            'groupid' => 'layout'
        ),

        

    'general_tab_end' => array(
        'type' => 'tab_end',
        'label' => 'General',
        'tabid' => 'general'
    ),
    'design_tab_begin' => array(
        'type' => 'tab_start',
        'label' => 'Design',
        'tabid' => 'design'
    ),

        'panel' => array(
            'type' => 'group_start',
            'label' => 'Box',
            'groupid' => 'design_panel'
        ),
    
            'css_style' => array(
                'type' => 'css_tool',
                'label' => 'Tool',
                'selector' => '.cl_service',
                'css_property' => '',
                'default' => array('margin-top' => '35px'),
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),

            'box_border_color' => array(
                'type' => 'color',
                'label' => 'Box Border Color',
                'default' => 'rgba(0,0,0,0.0)',
                'selector' => '.cl_service',
                'css_property' => 'border-color',
                'alpha' => true,
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),
            
            'text_color' => array(
                'type' => 'inline_select',
                'label' => 'Text Color',
                'default' => 'dark-text',
                'choices' => array(
                    'dark-text' => 'Dark',
                    'light-text' => 'Light'
                ),
                'selector' => '.cl_service',
                'selectClass' => '',
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),
        
            
        'design_panel_end' => array(
            'type' => 'group_end',
            'label' => 'Animation',
            'groupid' => 'design_panel'
        ),

        'icon_start' => array(
            'type' => 'group_start',
            'label' => 'Style and Distances',
            'groupid' => 'icon'
        ),

            'icon_font_size' => array(
                'type'     => 'slider',
                'priority' => 10,
                'label'       => esc_attr__( 'Custom Icon Size', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Change Icon size by moving the slider', 'codeless-builder' ),
                'default'     => '36',
                'choices'     => array(
                    'min'  => '14',
                    'max'  => '120',
                    'step' => '1',
                ),
                'suffix' => 'px', 
                'selector' => '.cl_service > .icon_wrapper i',
                'css_property' => 'font-size',
                'cl_required'    => array(
                    array(
                        'setting'  => 'media',
                        'operator' => '==',
                        'value'    => 'type_icon',
                    ),
                ),
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),

            'icon_color' => array(
                'type' => 'color',
                'label' => 'Icon Color',
                'default' => get_theme_mod('primary_color'),
                'selector' => '.cl_service > .icon_wrapper i',
                'css_property' => 'color',
                'alpha' => true,
                'cl_required'    => array(
                    array(
                        'setting'  => 'media',
                        'operator' => '==',
                        'value'    => 'type_icon',
                    ),
                ),
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),
            

            'wrapper_bg_color' => array(
                'type' => 'color',
                'label' => 'Wrapper BG Color',
                'default' => 'rgba(0,0,0,0)',
                'selector' => '.cl_service > .icon_wrapper .wrapper-form',
                'css_property' => 'background-color',
                'alpha' => true,
                'cl_required'    => array(
                    array(
                        'setting'  => 'wrapper',
                        'operator' => '!=',
                        'value'    => 'wrapper_none',
                    ),
                ),
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),

            'wrapper_border_color' => array(
                'type' => 'color',
                'label' => 'Wrapper Border Color',
                'default' => 'rgba(0,0,0,0.5)',
                'selector' => '.cl_service > .icon_wrapper .wrapper-form',
                'css_property' => 'border-color',
                'alpha' => true,
                'cl_required'    => array(
                    array(
                        'setting'  => 'wrapper',
                        'operator' => '!=',
                        'value'    => 'wrapper_none',
                    ),
                ),
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),

            'wrapper_box_shadow' => array(

                'type'        => 'switch',
                'label'       => esc_html__( 'Add Shadow', 'codeless-builder' ),
                'tooltip' => 'Switch On to add shadow to icon wrapper',
                'default'     => 0,
                'priority'    => 10,
                'choices'     => array(
                    'on'  => esc_attr__( 'On', 'codeless-builder' ),
                    'off' => esc_attr__( 'Off', 'codeless-builder' ),
                ),
                'selector' => '.cl_service > .icon_wrapper',
                'addClass' => 'with-shadow',
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),


            'wrapper_size' => array(
                'type'     => 'slider',
                'priority' => 10,
                'label'       => esc_attr__( 'Custom Wrapper and SVG Size', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Change Wrapper size by moving the slider. Can be used for SVG size too.', 'codeless-builder' ),
                'default'     => '72',
                'choices'     => array(
                    'min'  => '30',
                    'max'  => '240',
                    'step' => '1',
                ),
                'suffix' => 'px',
                'selector' => '.cl_service > .icon_wrapper .wrapper-form',
                'css_property' => array('width', 'height'),
                'cl_required'    => array(
                    array(
                        'setting'  => 'media',
                        'operator' => '!=',
                        'value'    => 'type_text',
                    )
                ),
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),

            'wrapper_distance' => array(
                'type'     => 'slider',
                'priority' => 10,
                'label'       => esc_attr__( 'Icon and Wrapper Distance', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Icon and Wrapper distance from content', 'codeless-builder' ),
                'default'     => '20',
                'choices'     => array(
                    'min'  => '0',
                    'max'  => '140',
                    'step' => '1',
                ),
                'suffix' => 'px',
                'selector' => '.cl_service > .icon_wrapper',
                'css_property' => array('padding-right', 'padding-bottom', 'padding-left'),
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),

            'title_distance_top' => array(
                'type'     => 'slider',
                'priority' => 10,
                'label'       => esc_attr__( 'Distance Title From Top', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Drag to change the distance of the title from top of element', 'codeless-builder' ),
                'default'     => '0',
                'choices'     => array(
                    'min'  => '0',
                    'max'  => '30',
                    'step' => '1',
                ),
                'suffix' => 'px',
                'selector' => '.cl_service .box-content > .service-title',
                'css_property' => 'margin-top',
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),

            'title_content_distance' => array(
                'type'     => 'slider',
                'priority' => 10,
                'label'       => esc_attr__( 'Distance beetween Title and Content', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Drag to change the distance of the content from Title', 'codeless-builder' ),
                'default'     => '0',
                'choices'     => array(
                    'min'  => '0',
                    'max'  => '140',
                    'step' => '1',
                ),
                'suffix' => 'px',
                'selector' => '.cl_service .box-content > .content',
                'css_property' => 'margin-top',
                'group_vc' => esc_attr('Style', 'codeless-builder')
            ),
            'title_subtitle_distance' => array(
                'type'     => 'slider',
                'priority' => 10,
                'label'       => esc_attr__( 'Distance beetween Title and Subtitle', 'codeless-builder' ),
                'tooltip' => esc_attr__( 'Drag to change the distance of the title from subtitle', 'codeless-builder' ),
                'default'     => '0',
                'choices'     => array(
                    'min'  => '0',
                    'max'  => '140',
                    'step' => '1',
                ),
                'suffix' => 'px',
                'selector' => '.cl_service .box-content > .subtitle',
                'css_property' => 'margin-top',
                'cl_required'    => array(
                    array(
                        'setting'  => 'subtitle_bool',
                        'operator' => '==',
                        'value'    => true,
                    ),
                ),
                'group_vc' => esc_attr('Style', 'codeless-builder')
                
            ),


        'icon_end' => array(
            'type' => 'group_end',
            'label' => 'Icon',
            'groupid' => 'icon'
        ),



        'typography_start' => array(
            'type' => 'group_start',
            'label' => 'Typography',
            'groupid' => 'typography',
        ),

            'title_typography' => array(
                'type'        => 'inline_select',
                'label'       => esc_html__( 'Title Typography', 'codeless-builder' ),
                'tooltip' => 'Select one of the predefined title typography styles on Styling Section or select "Custom Font" if you want to edit the typography of Title. SHIFT-CLICK on Element if you want to modify one of the predefined Style',
                'default'     => 'h5',
                'priority'    => 10,
                'selector' => '.cl_service .box-content > .service-title',
                'choices'     => array(
                    'h1'  => esc_attr__( 'H1', 'codeless-builder' ),
                    'h2' => esc_attr__( 'H2', 'codeless-builder' ),
                    'h3' => esc_attr__( 'H3', 'codeless-builder' ),
                    'h4' => esc_attr__( 'H4', 'codeless-builder' ),
                    'h5' => esc_attr__( 'H5', 'codeless-builder' ),
                    'h6' => esc_attr__( 'H6', 'codeless-builder' ),
                    'custom_font' => esc_attr__( 'Custom Font', 'codeless-builder' ),
                ),
                'reloadTemplate' => true,
                'group_vc' => esc_attr('Typography', 'codeless-builder')
            ),	


            'title_font_size' => array(

                    'type' => 'slider',
                    'label' => 'Title Font Size',
                    'default' => '16',
                    'selector' => '.cl_service .box-content >  .service-title',
                    'css_property' => 'font-size',
                    'choices'     => array(
                        'min'  => '14',
                        'max'  => '72',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'title_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                ),

            'title_font_weight' => array(

                    'type' => 'inline_select',
                    'label' => 'Title Font Weight',
                    'default' => '600',
                    'selector' => '.cl_service .box-content > .service-title',
                    'css_property' => 'font-weight',
                    'choices'     => array(
                        '100' => '100',
                        '200' => '200',
                        '300' => '300',
                        '400' => '400',
                        '500' => '500',
                        '600' => '600',
                        '700' => '700',
                        '800' => '800'
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'title_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                ),
                
            'title_line_height' => array(

                    'type' => 'slider',
                    'label' => 'Title Line Height',
                    'default' => '22',
                    'selector' => '.cl_service .box-content > .service-title',
                    'css_property' => 'line-height',
                    'choices'     => array(
                        'min'  => '20',
                        'max'  => '100',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'title_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                ),

            'title_letterspace' => array(

                    'type' => 'slider',
                    'label' => 'Title Letter-space',
                    'default' => '1',
                    'selector' => '.cl_service .box-content > .service-title',
                    'css_property' => 'letter-spacing',
                    'choices'     => array(
                        'min'  => '0',
                        'max'  => '4',
                        'step' => '0.05',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'title_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                ),
            
            'title_transform' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Title Text Transform', 'codeless-builder' ),
                    
                    'default'     => 'uppercase',
                    'selector' => '.cl_service .box-content > .service-title',
                    'css_property' => 'text-transform',
                    'choices' => array(
                        'none' => 'None',
                        'uppercase' => 'Uppercase'
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'title_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                    
                ),

            'title_color' => array(
                    'type' => 'color',
                    'label' => 'Title Color',
                    'default' => '#444444',
                    'selector' => '.cl_service .box-content > .service-title',
                    'css_property' => 'color',
                    'alpha' => true,
                    'cl_required'    => array(
                        array(
                            'setting'  => 'title_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
            ),
                
            
            
            'custom_desc_typography' => array(
                'type'        => 'switch',
                'label'       => esc_html__( 'Content Typography', 'codeless-builder' ),
                'tooltip' => 'Switch On if you want to modify default typography of content',
                'default'     => 0,
                'priority'    => 10,
                'choices'     => array(
                    'on'  => esc_attr__( 'On', 'codeless-builder' ),
                    'off' => esc_attr__( 'Off', 'codeless-builder' ),
                ),
                'group_vc' => esc_attr('Typography', 'codeless-builder')
            ),	
            
            
            
            'desc_font_size' => array(

                    'type' => 'slider',
                    'label' => 'Content Font Size',
                    'default' => '14',
                    'selector' => '.cl_service .box-content > .content',
                    'css_property' => 'font-size',
                    'choices'     => array(
                        'min'  => '14',
                        'max'  => '60',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_desc_typography',
                            'operator' => '==',
                            'value'    => true,
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                ),

            'desc_font_weight' => array(

                    'type' => 'inline_select',
                    'label' => 'Content Font Weight',
                    'default' => '400',
                    'selector' => '.cl_service .box-content > .content',
                    'css_property' => 'font-weight',
                    'choices'     => array(
                        '100' => '100',
                        '200' => '200',
                        '300' => '300',
                        '400' => '400',
                        '500' => '500',
                        '600' => '600',
                        '700' => '700',
                        '800' => '800'
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_desc_typography',
                            'operator' => '==',
                            'value'    => true,
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                ),
                
            'desc_line_height' => array(

                    'type' => 'slider',
                    'label' => 'Content Line Height',
                    'default' => '22',
                    'selector' => '.cl_service .box-content > .content',
                    'css_property' => 'line-height',
                    'choices'     => array(
                        'min'  => '20',
                        'max'  => '80',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_desc_typography',
                            'operator' => '==',
                            'value'    => true,
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                ),
                
            'desc_transform' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Content Text Transform', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'selector' => '.cl_service .box-content > .content',
                    'css_property' => 'text-transform',
                    'choices' => array(
                        'none' => 'None',
                        'uppercase' => 'Uppercase'
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_desc_typography',
                            'operator' => '==',
                            'value'    => true,
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                    
                ),
            'desc_color' => array(
                    'type' => 'color',
                    'label' => 'Content Color',
                    'default' => '#6a6a6a',
                    'selector' => '.cl_service .box-content > .content',
                    'css_property' => 'color',
                    'alpha' => true,
                    'cl_required'    => array(
                        array(
                            'setting'  => 'custom_desc_typography',
                            'operator' => '==',
                            'value'    => true,
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
            ),


            'subtitle_typography' => array(
                'type'        => 'inline_select',
                'label'       => esc_html__( 'Subtitle Typography', 'codeless-builder' ),
                'tooltip' => 'Select typography style of Subtitle',
                'default'     => 'default',
                'priority'    => 10,
                'selector' => '.cl_service .box-content > .subtitle',
                'selectClass' => '',
                'choices'     => array(
                    'default'  => esc_attr__( 'Default', 'codeless-builder' ),
                    'custom_font' => esc_attr__( 'Custom Font', 'codeless-builder' ),
                ),
                'cl_required'    => array(
                        array(
                            'setting'  => 'subtitle_bool',
                            'operator' => '==',
                            'value'    => true,
                        ),
                ),
                'group_vc' => esc_attr('Typography', 'codeless-builder')
            ),	


            'subtitle_font_size' => array(

                    'type' => 'slider',
                    'label' => 'Subtitle Font Size',
                    'default' => '14',
                    'selector' => '.cl_service .box-content > .subtitle',
                    'css_property' => 'font-size',
                    'choices'     => array(
                        'min'  => '14',
                        'max'  => '72',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'subtitle_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                ),

            'subtitle_font_weight' => array(

                    'type' => 'inline_select',
                    'label' => 'Subtitle Font Weight',
                    'default' => '400',
                    'selector' => '.cl_service .box-content > .subtitle',
                    'css_property' => 'font-weight',
                    'choices'     => array(
                        '100' => '100',
                        '200' => '200',
                        '300' => '300',
                        '400' => '400',
                        '500' => '500',
                        '600' => '600',
                        '700' => '700',
                        '800' => '800'
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'subtitle_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                ),
                
            'subtitle_line_height' => array(

                    'type' => 'slider',
                    'label' => 'Subtitle Line Height',
                    'default' => '18',
                    'selector' => '.cl_service .box-content > .subtitle',
                    'css_property' => 'line-height',
                    'choices'     => array(
                        'min'  => '20',
                        'max'  => '100',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'subtitle_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                ),

            'subtitle_letterspace' => array(

                    'type' => 'slider',
                    'label' => 'Subtitle Letter-space',
                    'default' => '0',
                    'selector' => '.cl_service .box-content > .subtitle',
                    'css_property' => 'letter-spacing',
                    'choices'     => array(
                        'min'  => '0',
                        'max'  => '4',
                        'step' => '0.05',
                        'suffix' => 'px'
                    ),
                    'suffix' => 'px',
                    'cl_required'    => array(
                        array(
                            'setting'  => 'subtitle_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                ),
            
            'subtitle_transform' => array(
                    'type'     => 'inline_select',
                    'priority' => 10,
                    'label'       => esc_attr__( 'Subtitle Text Transform', 'codeless-builder' ),
                    
                    'default'     => 'none',
                    'selector' => '.cl_service .box-content > .subtitle',
                    'css_property' => 'text-transform',
                    'choices' => array(
                        'none' => 'None',
                        'uppercase' => 'Uppercase'
                    ),
                    'cl_required'    => array(
                        array(
                            'setting'  => 'subtitle_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
                    
                ),

            'subtitle_color' => array(
                    'type' => 'color',
                    'label' => 'Subtitle Color',
                    'default' => '#a7a7a7',
                    'selector' => '.cl_service .box-content > .subtitle',
                    'css_property' => 'color',
                    'alpha' => true,
                    'cl_required'    => array(
                        array(
                            'setting'  => 'subtitle_typography',
                            'operator' => '==',
                            'value'    => 'custom_font',
                        ),
                    ),
                    'group_vc' => esc_attr('Typography', 'codeless-builder')
            ),




        'typography_end' => array(
            'type' => 'group_end',
            'label' => 'Typography',
            'groupid' => 'typography',
        ),



        'animation_start' => array(
            'type' => 'group_start',
            'label' => 'Animation',
            'groupid' => 'animation'
        ),
        
            'animation' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Effect', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none'	=> 'None',
                    'top-t-bottom' =>	'Top-Bottom',
                    'bottom-t-top' =>	'Bottom-Top',
                    'right-t-left' => 'Right-Left',
                    'left-t-right' => 'Left-Right',
                    'alpha-anim' => 'Fade-In',	
                    'zoom-in' => 'Zoom-In',	
                    'zoom-out' => 'Zoom-Out',
                    'zoom-reverse' => 'Zoom-Reverse',
                ),
                'selector' => '.cl_service',
                'group_vc' => esc_attr('Animation', 'codeless-builder')
            ),
            
            'animation_delay' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Delay', 'codeless-builder' ),
                
                'default'     => 'none',
                'choices' => array(
                    'none'	=> 'None',
                    '100' =>	'ms 100',
                    '200' =>	'ms 200',
                    '300' =>	'ms 300',
                    '400' =>	'ms 400',
                    '500' =>	'ms 500',
                    '600' =>	'ms 600',
                    '700' =>	'ms 700',
                    '800' =>	'ms 800',
                    '900' =>	'ms 900',
                    '1000' =>	'ms 1000',
                    '1100' =>	'ms 1100',
                    '1200' =>	'ms 1200',
                    '1300' =>	'ms 1300',
                    '1400' =>	'ms 1400',
                    '1500' =>	'ms 1500',
                    '1600' =>	'ms 1600',
                    '1700' =>	'ms 1700',
                    '1800' =>	'ms 1800',
                    '1900' =>	'ms 1900',
                    '2000' =>	'ms 2000',
                
                ),
                'selector' => '.cl_service',
                'htmldata' => 'delay',
                'cl_required'    => array(
                    array(
                        'setting'  => 'animation',
                        'operator' => '!=',
                        'value'    => 'none',
                    ),
                ),
                'group_vc' => esc_attr('Animation', 'codeless-builder')
            ),
            
            'animation_speed' => array(
                'type'     => 'inline_select',
                'priority' => 10,
                'label'       => esc_attr__( 'Animation Speed', 'codeless-builder' ),
                
                'default'     => '400',
                'choices' => array(
                    'none'	=> 'None',
                    '100' =>	'ms 100',
                    '200' =>	'ms 200',
                    '300' =>	'ms 300',
                    '400' =>	'ms 400',
                    '500' =>	'ms 500',
                    '600' =>	'ms 600',
                    '700' =>	'ms 700',
                    '800' =>	'ms 800',
                    '900' =>	'ms 900',
                    '1000' =>	'ms 1000'
                    
                
                ),
                'selector' => '.cl_service',
                'htmldata' => 'speed',
                'cl_required'    => array(
                    array(
                        'setting'  => 'animation',
                        'operator' => '!=',
                        'value'    => 'none',
                    ),
                ),
                'group_vc' => esc_attr('Animation', 'codeless-builder')
            ),
        
        'animation_end' => array(
            'type' => 'group_end',
            'label' => 'Animation',
            'groupid' => 'animation'
        ),


    'design_tab_end' => array(
        'type' => 'tab_end',
        'label' => 'Design',
        'tabid' => 'design'
    ),
)
));