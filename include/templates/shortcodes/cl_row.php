<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$output = '';

$atts = cl_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$extracss = '';

if( ! isset( $row_id ) || empty( $row_id ) )
	$row_id = 'cl_row_' . str_replace( ".", '-', uniqid("", true) );

$video_wrapper = '';

$video_poster_image = '';


if($video == 'self'){
    
        $video_wrapper .= '<div class="video-section">';
		$video_wrapper .= '     <video poster="'.esc_attr( $video_poster_image ).'" muted="muted" preload="auto" '.((int)$row_video_loop ? "loop" : "").' autoplay="true">';

		if (!empty($video_mp4))
		    $video_wrapper .= '		    <source type="video/mp4" src="'.esc_url( $video_mp4 ).'" /> ';
		
		if (!empty($video_webm))
		    $video_wrapper .= '		    <source type="video/webm" src="'.esc_url( $video_webm ).'" /> ';
		    
		if (!empty($video_ogv))
		    $video_wrapper .= '		    <source type="video/ogv" src="'.esc_url( $video_ogv ).'" /> ';
	
		$video_wrapper .= '	    </video>';
		$video_wrapper .= '</div>';
		
}else{
        $video_wrapper .= '<div class="video-section social-video"  data-stream="'.esc_attr( $video ).'" style="opacity:0;">';
	        $video_wrapper .= '<div class="cl-video-centered">';
				
			if ($video == 'youtube')
			    $video_wrapper .= '<iframe src="//www.youtube.com/embed/'.esc_attr( $video_youtube ).'?rel=0&amp;wmode=transparent&amp;enablejsapi=1&amp;controls=0&amp;showinfo=0&amp;loop='.(int)esc_attr( $row_video_loop ).'&amp;playlist='.esc_attr( $video_youtube ).'"></iframe>';
	
	        if ($video == 'vimeo')
	            $video_wrapper .= '<iframe src="//player.vimeo.com/video/'.esc_attr( $video_vimeo ).'?badge=0;api=1;background=1;autoplay=1;loop='.(int)$row_video_loop.'" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
	
			$video_wrapper .= '</div>';
		$video_wrapper .= '</div>';
}


if( $columns_gap != '15' ){
	$this->addCustomCss( '#'.esc_attr($row_id).' .row > .cl_cl_column > .cl_column, #'.esc_attr( $row_id ).' .row > .cl_column{ padding-left: '. esc_attr( $columns_gap ) .'px; padding-right: '. esc_attr( $columns_gap ) .'px; }' );
}

if( $custom_width_bool ){
	$this->addCustomCss( '@media (min-width:1200px){ #'.esc_attr( $row_id ).' > .container-content{ width:'.esc_attr( $custom_width ).'px; } }' );
}

if( $css_style_991_row_bool ){
	$this->addCustomCss( '@media (max-width:991px){ #'.esc_attr( $row_id ).'{ ' . $this->generateCSSBox( $css_style_991 ) . ' } }' );
}

if( $css_style_767_row_bool ){
	$this->addCustomCss( '@media (max-width:767px){ #'.esc_attr( $row_id ).'{ ' . $this->generateCSSBox( $css_style_767 ) . ' } }' );
}


$output .= '<div id="'.esc_attr( $row_id ).'" class="cl-row cl-element '.$this->generateClasses('.cl-row').' '.esc_attr( $extra_class ).'" '.$this->generateStyle('.cl-row').'>';
   
    $output .= $video_wrapper;
    
    $output .= '<div class="bg-layer '.$this->generateClasses('.cl-row > .bg-layer').'" '.$this->generateStyle('.cl-row > .bg-layer').' data-parallax-config=\'{ "speed": 0.3 }\'></div>';
    $output .= '<div class="overlay '.$this->generateClasses('.cl-row > .overlay').'" '.$this->generateStyle('.cl-row > .overlay').'></div>';
    
    $output .= '<div class="'.esc_attr( $row_type ).' container-content">';
        $output .= '<div class="row cl_row-sortable '.$this->generateClasses('.cl-row > div > .row').'">';
            $output .= cl_remove_wpautop($content);
        $output .= '</div>';
    $output .= '</div>';
    
$output .= '</div>';


echo cl_remove_wpautop( $output );

