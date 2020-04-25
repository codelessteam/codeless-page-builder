<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

$output = '';

$atts = cl_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$image = codeless_js_object_to_array($image);



$video_wrapper = '';

if($video == 'self'){
    
        $video_wrapper .= '<div class="video-section">';
      
        	if( isset($image['id']) ){
				$im = wp_get_attachment_image_src($image['id'], 'full');
        		$video_wrapper .= '<div class="image-wrapper" style="background-image:url('.$im[0].');">';
        			$video_wrapper .= '<a href="#" class="play-button"><i class="cl-icon-play"></i></a>';
        		$video_wrapper .= '</div>';
			}
        		
		$video_wrapper .= '     <video  style="height: '.esc_attr( $height ).'px;" muted="muted" preload="auto" '.((int)$media_video_loop ? "loop" : "").' autoplay="'.esc_attr( $autoplay ).'">';
 
		if (!empty($video_mp4))
		    $video_wrapper .= '		    <source type="video/mp4" src="'.esc_url( $video_mp4 ).'" /> ';
		
		if (!empty($video_webm))
		    $video_wrapper .= '		    <source type="video/webm" src="'.esc_url( $video_webm ).'" /> ';
		    
		if (!empty($video_ogv))
		    $video_wrapper .= '		    <source type="video/ogv" src="'.esc_url( $video_ogv ).'" /> ';
	
		$video_wrapper .= '	    </video>';
		$video_wrapper .= '</div>';	
		
}else{
        $video_wrapper .= '<div class="video"  data-stream="'.esc_attr( $video ).'" style="opacity:1;">';
	        
	        if( isset($image['id']) ){
				$im = wp_get_attachment_image_src($image['id'], 'full');
        		$video_wrapper .= '<div class="image-wrapper" style="background-image:url('.esc_url( $im[0] ).');">';
        			$video_wrapper .= '<a href="#" class="play-button"><i class="cl-icon-play"></i></a>';
        		$video_wrapper .= '</div>';
        	}
				
			if ($video == 'youtube')
			    $video_wrapper .= '<iframe src="about:blank" data-src="//www.youtube.com/embed/'.esc_attr( $video_youtube ).'?rel=0&amp;wmode=transparent&amp;enablejsapi=1&amp;controls=0&amp;showinfo=0&amp;autoplay='.esc_attr( $autoplay ).';loop='.(int)esc_attr( $media_video_loop ).'&amp;playlist='.esc_attr( $video_youtube ).'" style="height: '.esc_attr( $height ).'px;"></iframe>';
	
	        if ($video == 'vimeo')
	            $video_wrapper .= '<iframe src="about:blank" data-src="//player.vimeo.com/video/'.esc_attr( $video_vimeo ).'?badge=0;api=1;background=1;autoplay='.$autoplay.';loop='.(int)$media_video_loop.'" width="500" height="281" frameborder="0"  style="height: '.esc_attr( $height ).'px;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
	
			
		$video_wrapper .= '</div>';
} 

$myimg = '';

if( isset($image['id']) )
    $myimg =urldecode(  $image['url'] );
        	
	        

?>

<div class="cl_media cl-element <?php echo esc_attr( $this->generateClasses('.cl_media') ); ?>" <?php $this->generateStyle('.cl_media', '', true); ?>>

	<?php if( $custom_link != '#' && ! empty( $custom_link ) ): ?>
	<a target="<?php echo esc_attr($target) ?>" href="<?php echo esc_url( $custom_link ) ?>" class="custom-link"></a>
	<?php endif; ?>

	<div class="inner"  <?php $this->generateStyle('.cl_media .inner', '', true); ?>>
	<?php if( $media_type == 'image' ): ?>

		<?php $img_id = isset($image['id']) ? $image['id'] : 0; ?>

		<?php echo wp_get_attachment_image($img_id, $image_size ); ?>

	<?php endif; ?>

	<?php if( $media_type == 'video' ): ?>
		<?php echo cl_remove_wpautop( $video_wrapper ) ?>
	<?php endif; ?>
	
	</div>
</div>