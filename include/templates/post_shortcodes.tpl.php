<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
global $cl_row_layouts;

?>


<script type="text/javascript">
	<?php if( property_exists( $cl_page_builder, 'post_shortcodes' ) ): ?>
	cl.post_shortcodes = JSON.parse( decodeURIComponent( ("<?php echo rawurlencode( json_encode( $cl_page_builder->post_shortcodes ) ); ?>" + '') ) );
	<?php endif; ?>

	<?php if( property_exists( $cl_page_builder, 'content_blocks' ) ): ?>
	cl.content_blocks = <?php echo json_encode( $cl_page_builder->content_blocks ); ?>;
	<?php endif; ?>

	cl.map = <?php echo json_encode( Cl_Builder_Mapper::getShortCodes() ); ?>;

	cl.headerMap = <?php echo json_encode( Cl_Builder_Mapper::getHeaderElements() ); ?>;

	cl.header_elements_var = <?php echo json_encode(cl_get_header_elements() ); ?>;

	cl.pageID = <?php echo get_the_ID(); ?>;

	cl.postType = '<?php echo get_post_type( get_the_ID() ); ?>';

	cl.loadedUrl = '<?php echo codeless_get_loadedUrl(); ?>';

	cl.ajaxHandler = '<?php echo cl_get_ajax_handlerUrl(); ?>';

	cl.ajaxNonce = '<?php echo cl_get_ajax_nonce(); ?>';

</script>

<script type="text/html" id="cl_controls-default">
	
	<div class="cl_controls-default cl_controls">
		<div class="cl_controls-out">
		    <a class="cl_control-btn cl_element-name cl_element-move" title="Move">{{ name }}</a>
		 </div>
	</div>
	
</script>

<script type="text/html" id="cl_controls-cl_row">
	
	<div class="cl_controls-cl_row cl_controls">
		<div class="cl_controls-out">
		    <a class="cl_control-btn cl_element-name cl-move-row">{{ name }}</a>
		    
		    <a class="cl_control-btn cl_control-btn-layout"  title="Layout" href="#"><i class="cl-builder-icon-columns2" ></i></a>
		    <a class="cl_control-btn cl_control-btn-clone" title="Clone" href="#"><i class="cl-builder-icon-clone" ></i></a>
		    <a class="cl_control-btn cl_control-btn-color-text" title="Content Color Light / Dark" href="#">T</a>
		    <a class="cl_control-btn cl_control-btn-delete" title="Delete" href="#"><i class="cl-builder-icon-trash2" ></i></a>
		    <a class="cl_control-btn cl_control-btn-save" title="Save" href="#">Save</a>
		 </div>
		 <div class="cl_control-columns">
			<?php foreach ( $cl_row_layouts as $layout ) :  ?>
							<a href="#" class="cl_col-btn predefined-col <?php echo esc_attr( $layout['icon_class'] )
							                                   . '" data-cells="' . esc_attr( $layout['cells'] )
							                                   . '" data-cells-mask="' . esc_attr( $layout['mask'] )
							                                   . '" title="' . esc_attr( $layout['title'] ) ?>"><span
									class="icon"></span></a>
						<?php endforeach ?>
						<a href="#" class="cl_col-btn custom_size"></a>
		 </div>
	</div>
</script>
 

<script type="text/html" id="cl_controls-cl_page_header">
	
	<div class="cl_controls-default cl_controls">
		<div class="cl_controls-out">
		    <a class="cl_control-btn cl_element-name cl-move-row" title="Move">{{ name }}</a>
		    <a class="cl_control-btn cl_control-btn-clone" title="Clone" href="#"><i class="cl-builder-icon-clone" ></i></a>
		    <a class="cl_control-btn cl_control-btn-delete" title="Delete" href="#"><i class="cl-builder-icon-trash2" ></i></a>
		 </div>
	</div>
	
</script>

<script type="text/html" id="cl_controls-cl_slider">
	
	<div class="cl_controls-default cl_controls">
		<div class="cl_controls-out">
		    <a class="cl_control-btn cl_element-name cl-move-row" title="Move">{{ name }}</a>
		    <a class="cl_control-btn cl_control-btn-clone" title="Clone" href="#"><i class="cl-builder-icon-clone" ></i></a>
		    <a class="cl_control-btn cl_control-btn-delete" title="Delete" href="#"><i class="cl-builder-icon-trash2" ></i></a>

		 </div>

	</div>
	<div class="cl_control-slides">
		<a class="cl_control-btn cl_element-name">Slides</a>
		<a class="cl_control-btn cl_control-btn-add-slide" title="Add Slide" href="#"><i class="cl-builder-icon-plus2" ></i></a>
		<div class="cl-slides-container"></div>
	</div>
</script>


<script type="text/html" id="cl_controls-cl_slide">
	
	<div class="cl_controls-default cl_controls">
		<div class="cl_controls-out">
			<a class="cl_control-btn cl_element-name">Current Slide</a>
		    <a class="cl_control-btn cl_control-btn-clone" title="Clone" href="#"><i class="cl-builder-icon-clone" ></i></a>
		    <a class="cl_control-btn cl_control-btn-delete" title="Delete" href="#"><i class="cl-builder-icon-trash2" ></i></a>
		 </div>

	</div>
</script>

<script type="text/html" id="cl_controls-cl_row_inner">
	
	<div class="cl_controls-cl_row cl_row_inner cl_controls">
		<div class="cl_controls-out">
		    <a class="cl_control-btn cl_element-name cl_element-move">Inner Row</a>
		    
			<a class="cl_control-btn cl_control-btn-layout" title="Layout" href="#"><i class="cl-builder-icon-columns2" ></i></a>
			    
			<a class="cl_control-btn cl_control-btn-clone" title="Clone" href="#"><i class="cl-builder-icon-clone" ></i></a>
			<a class="cl_control-btn cl_control-btn-delete" title="Delete" href="#"><i class="cl-builder-icon-trash2" ></i></a>
		   
		 </div>
		 <div class="cl_control-columns">
			<?php foreach ( $cl_row_layouts as $layout ) :  ?>
							<a href="#" class="cl_col-btn <?php echo esc_attr( $layout['icon_class'] )
							                                   . '" data-cells="' . esc_attr( $layout['cells'] )
							                                   . '" data-cells-mask="' . esc_attr( $layout['mask'] )
							                                   . '" title="' . esc_attr( $layout['title'] ) ?>"><span
									class="icon"></span></a>
						<?php endforeach ?>
		 </div>
	</div>
</script>

<script type="text/html" id="cl_controls-cl_column">
	
	<div class="cl_controls-cl_column cl_controls">
		<div class="cl_controls-out">
		    <a class="cl_control-btn cl_element-name cl-move-cl_column"><i class="cl-builder-icon-columns2" ></i></a>
		     <a class="cl_control-btn cl_control-btn-color-text" title="Content Color Light / Dark" href="#">T</a>
		    <a class="cl_control-btn cl_control-btn-paste-element" title="Paste Element" data-title="Paste Element" href="#"><i class="cl-builder-icon-clipboard" ></i></a>
		    <a class="cl_control-btn cl_control-btn-save" title="Save" href="#">Save</a>
		 </div>
	</div>
	
	<div class="add-element-prepend"></div>
	<div class="add-element-append"></div>
</script>

<script type="text/html" id="cl_controls-cl_column_inner">
	
	<div class="cl_controls-cl_column cl_controls">
		<div class="cl_controls-out">
		    <a class="cl_control-btn cl_element-name cl-move-cl_column"><i class="cl-builder-icon-columns2" ></i></a>
		    <a class="cl_control-btn cl_control-btn-paste-element" title="Paste Element" data-title="Paste Element" href="#"><i class="cl-builder-icon-clipboard" ></i></a>
		 </div>
	</div>
	
	<div class="add-element-prepend"></div>
	<div class="add-element-append"></div>
</script>

<script type="text/html" id="cl_controls-cl_toggles">
	
	<div class="cl_controls-cl_toggles cl_controls">
		<div class="cl_controls-out">
			<a class="cl_control-btn cl_element-name cl_element-move">{{ name }}</a>
			<a class="cl_control-btn cl_control-btn-clone" title="Clone" href="#"><i class="cl-builder-icon-clone" ></i></a>
			<a class="cl_control-btn cl_control-btn-delete" title="Delete" href="#"><i class="cl-builder-icon-trash2" ></i></a>
		    <a class="cl_control-btn cl_control-btn-add-toggle" title="Add Toggle" href="#"><i class="cl-builder-icon-plus2" ></i></a>

		 </div>
	</div>
</script>

<script type="text/html" id="cl_controls-cl_toggle">
	
	<div class="cl_controls-default cl_controls">
		<div class="cl_controls-out">
			<a class="cl_control-btn cl_element-name cl_element-move" title="Move">{{ name }}</a>
		    <a class="cl_control-btn cl_control-btn-clone" title="Clone" href="#"><i class="cl-builder-icon-clone" ></i></a>
		    <a class="cl_control-btn cl_control-btn-delete" title="Delete" href="#"><i class="cl-builder-icon-trash2" ></i></a>
		 </div>

	</div>
	<div class="add-element-prepend"></div>
	<div class="add-element-append"></div>
</script>


<script type="text/html" id="cl_controls-cl_tabs">
	
	<div class="cl_controls-cl_tabs cl_controls">
		<div class="cl_controls-out">
			<a class="cl_control-btn cl_element-name cl_element-move">{{ name }}</a>
			<a class="cl_control-btn cl_control-btn-clone" title="Clone" href="#"><i class="cl-builder-icon-clone" ></i></a>
			<a class="cl_control-btn cl_control-btn-delete" title="Delete" href="#"><i class="cl-builder-icon-trash2" ></i></a>
		    <a class="cl_control-btn cl_control-btn-add-tab" title="Add Tab" href="#"><i class="cl-builder-icon-plus2" ></i></a>

		 </div>
	</div>
</script>

<script type="text/html" id="cl_controls-cl_tab">
	
	<div class="cl_controls-default cl_controls">
		<div class="cl_controls-out">
			<a class="cl_control-btn cl_element-name cl_element-move" title="Move">{{ name }}</a>
		    <a class="cl_control-btn cl_control-btn-clone" title="Clone" href="#"><i class="cl-builder-icon-clone" ></i></a>
		    <a class="cl_control-btn cl_control-btn-delete" title="Delete" href="#"><i class="cl-builder-icon-trash2" ></i></a>
		 </div>

	</div>
	<div class="add-element-prepend"></div>
	<div class="add-element-append"></div>
</script>


<script type="text/html" id="cl_controls-cl_list">
	
	<div class="cl_controls-cl_list cl_controls">
		<div class="cl_controls-out">
			<a class="cl_control-btn cl_element-name cl_element-move" title="Move">{{ name }}</a>
			<a class="cl_control-btn cl_control-btn-clone" title="Clone" href="#"><i class="cl-builder-icon-clone" ></i></a>
			<a class="cl_control-btn cl_control-btn-delete" title="Delete" href="#"><i class="cl-builder-icon-trash2" ></i></a>
		    <a class="cl_control-btn cl_control-btn-add-item" title="Add Item" href="#"><i class="cl-builder-icon-plus2" ></i></a>

		 </div>
	</div>
</script>

<script type="text/html" id="cl_controls-cl_pricelist">
	
	<div class="cl_controls-cl_pricelist cl_controls">
		<div class="cl_controls-out">
			<a class="cl_control-btn cl_element-name cl_element-move" title="Move">{{ name }}</a>
			<a class="cl_control-btn cl_control-btn-clone" title="Clone" href="#"><i class="cl-builder-icon-clone" ></i></a>
			<a class="cl_control-btn cl_control-btn-delete" title="Delete" href="#"><i class="cl-builder-icon-trash2" ></i></a>
		    <a class="cl_control-btn cl_control-btn-add-item" title="Add Item" href="#"><i class="cl-builder-icon-plus2" ></i></a>

		 </div>
	</div>
</script>

<script type="text/html" id="cl_controls-cl_list_item">
	
	<div class="cl_controls-default cl_controls">
		<div class="cl_controls-out">
			<a class="cl_control-btn cl_element-name cl_element-move" title="Move">{{ name }}</a>
		    <a class="cl_control-btn cl_control-btn-clone" href="#" title="Clone"><i class="cl-builder-icon-clone" ></i></a>
		    <a class="cl_control-btn cl_control-btn-delete" href="#" title="Delete"><i class="cl-builder-icon-trash2" ></i></a>
		 </div>

	</div>
</script>


<?php /*
 <?php echo json_encode($editor->post_shortcodes); ?>
 JSON.parse(decodeURIComponent(("<?php echo urlencode(json_encode($editor->post_shortcodes)); ?>"+'').replace(/\+/g,'%20')));
 */
?>
