<script type="text/html" id="cl_row-video_template">
	
	<# if(video != 'none'){ #>
	
	<div class="video-section <# if(video != 'self'){ #> social-video" data-stream="{{ video }}" style="opacity:0; <# } #>">
	    
	    <# if(video == 'self'){ #>
	    
	    <video poster="" muted="muted" preload="auto" <# if(video_loop){ #> loop <# } #> autoplay="true">
	        <source type="video/mp4" src="{{ video_mp4 }}" />
	        <source type="video/webm" src="{{ video_webm }}" />
	        <source type="video/ogv" src="{{ video_ogv }}" />
	    </video>
	    
	    <# }else{ #>
	    
	    <div class="cl-video-centered">
	        
	        <# if(video == 'youtube'){ #>
	            <iframe src="//www.youtube.com/embed/{{ video_youtube }}?rel=0&amp;wmode=transparent&amp;enablejsapi=1&amp;controls=0&amp;showinfo=0&amp;loop={{video_loop}}&amp;playlist={{video_youtube}}"></iframe>
	        <# } #>
	        
	        <# if(video == 'vimeo'){ #>
	            <iframe src="//player.vimeo.com/video/{{ video_vimeo }}?badge=0;api=1;background=1;autoplay=1;loop={{ video_loop }}" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
	        <# } #>
	        
	    </div>
	    
	    <# } #>
	    
	 </div>
	 
	 <# } #>
	 
</script>