var cl_page_settings = [];
var loadedUrl = '';
var CodelessHeaderBuilderPane = ( function( $, api ) {
	'use strict';

	var self = {
		
		header_section:'',
		activedElement: false,

		styleClipboard: false,
		elementClipboard: false
		
	};
	

	self.init = function() {
		
		self.header_section = api.section( 'cl_codeless_header_builder' );
		self.hideAll();
		
		
		// Listen for the preview sending updates for settings
		/*api.previewer.bind( 'cl_header_builder_add_el', function( data ) {
			self.hideAll();
			
			api.control(data.element).activate();
			api.control(data.element).focus();
			
			api.control('header_builder').dropped(data.options);
			
		});*/
		
		api.previewer.bind( 'cl_header_builder_update', function( data ) {
			api('cl_header_builder').set(data);
			
			
		});

		api.previewer.bind( 'cl_open_page_options', function(data){
			self.actualHeaderOptions(data);
		} );

		api.previewer.bind('cl_update_customizer', function(){
			if( scriptData.active_builder ){
				api( 'cl_content_settings_updated', function ( obj ) {
				
					var n = obj.get() || 0;
					obj.set( !n );
				} );
				
				
				api( 'cl_page_content', function ( obj ) {
					var n = obj.get();
					
					obj.set( {} );
					obj.set( n );
				} );
			}
			
            
		});
		
		api.previewer.bind('cl_show_options', self.activeElement);
		//api.previewer.bind('cl_show_section', self.activeSection);
		api.previewer.bind('cl_create_custom_post', function(data){

			if(_.isUndefined(api.Posts))
				return;


				var promise = api.Posts.insertAutoDraftPost( data.postType );

				promise.done(function(d){

					// Set initial post data.
					var postData = {};
				
					postData.post_title = 'Title';
					//postData.post_content = 'Content';
					//postData.post_excerpt = 'Excerpt';
					
					d.setting.set( _.extend(
						{},
						d.setting.get(),
						postData
					) );

					d.section.focus();
					if( !_.isUndefined(data.modelId) ){
							
						setTimeout(function(){
							api.previewer.send('cl-save');
							setTimeout(function(){
								wp.customize.previewer.refresh();
							}, 300);
							
						}, 100);
							
					
					}
				});			
			
			
		});

		api.previewer.bind('cl_active_section_by_id', function(data){
			$('.wp-full-overlay').addClass('cl-on-process');
			
			api.Posts.ensurePosts( [data.id] ).done(function(){
				api.section(data.section).activate();			
				api.section(data.section).expand();
				$('.wp-full-overlay').removeClass('cl-on-process');
			});

		});
		api.previewer.bind('cl_hide_all', self.hideAll);

		api.previewer.bind( 'cl_open_page_settings', function(data){
			$('.wp-full-overlay').addClass('cl-on-process');
		
			if( _.isUndefined(api.section(data.section) ) ){
				api.Posts.ensurePosts( [data.pageID] ).done(function(){
					api.section(data.section).activate();			
					api.section(data.section).expand();
					$('.wp-full-overlay').removeClass('cl-on-process');
				});
			}else{

				api.section(data.section).activate();			
				api.section(data.section).expand();
				$('.wp-full-overlay').removeClass('cl-on-process');
			}
		} );
		api.previewer.bind('cl_open_global_styling', function(){
			api.section('cl_styling').activate();			
			api.section('cl_styling').expand();
		});

		api.previewer.bind('cl_add_new_page', function(data){
			var promise = api.Posts.startCreatePostFlow( {
				postType: data.postType,
				restorePreviousUrl: false
			} );

			promise.done(function(data){

				setTimeout(function(){
					var postData = {};
					
					postData.post_title = 'Codeless Builder Page';
					
					data.setting.set( _.extend(
						{},
						data.setting.get(),
						postData
					) );
				}, 500);
				
			})
		});

		api.previewer.bind('cl_header_builder_close_options', function(){
			
			self.header_section.deactivate();
			$(self.header_section.container).addClass('hideSection');
		});
		
		
		
		api.previewer.bind('cl_update_silent', self.updateSilent);
		api.previewer.bind('cl-copy-style', self.copyStyle);
		api.previewer.bind('cl-copy-element', self.copyElement);
		
	};

	self.copyStyle = function( params ){

		self.styleClipboard = params;
	};

	self.copyElement = function( model ){
		if( !_.isUndefined( model.from_content ) )
			delete model.from_content;
		
		self.elementClipboard = model;
	};

	
	self.hideAll = function(){
		
		_.each( api.section( 'cl_codeless_header_builder' ).controls(), function ( control ) {
			control.active.set(false);
		});

		_.each( api.section( 'cl_codeless_page_builder' ).controls(), function ( control ) {
			control.active.set(false);
		});
	};
	
	self.activeElement = function(element){
		
		if( cl_kirki.element === false || cl_kirki.element.id !== element.id )
			cl_kirki.initialize( element );

		var section = 'cl_codeless_page_builder';
		if( element.header_element )
			section = 'cl_codeless_header_builder';
		setTimeout(function(){
			api.section(section).expand();
		}, 400);


		/*if( self.activedElement !== false ){
			api.control(self.activedElement).active.set(false);
			api.control(self.activedElement).container.removeClass('actived');
		}*/
		
	
		/*api.control(element.type).active.set(true);
		api.control(element.type).container.addClass('actived');*/
		
		/*var section = 'cl_codeless_page_builder';
		api.section( section ).collapse();

		_.each( api.section(section).controls(), function(m, i){
			console.log(m);
			console.log(i);
			var cd = api.control(m.id);
			
			api.control.remove( cd.id );
			cd.container.remove();
			api.remove( m.id );
		});
		//api.control(element.type).createEl({options:element.options, id: element.id}, true);
		

		var setting = new api.Setting( 'test1', '#000', { transport: 'postMessage', dirty: true } );
		api.add( setting );
		
		var c = new api.kirkiDynamicControl( 'test1', {
			params:{
				section: 'cl_codeless_page_builder',
				type: 'kirki-color',
				id: 'test1',
				settings:{
					'default': 'test1'
				},
				choices: {}
				
			}
		 } );
		 api.control.add('test1', c);
		 
		api('test1', function(setting) {
			setting.bind(function() {
				console.log( api('test1').get() );
			});
		});

		setTimeout(function(){
			api.section(section).expand();
		}, 400);
		

		 
		//jQuery('.wp-full-overlay').removeClass('collapsed').addClass('expanded');
		//api.state('paneVisible').set(true);
		//self.activedElement = element.type;*/
		//api.control(element.type).focus();*/
	}
	
	self.activeSection = function(type){
		
		self.hideAll();
		
		api.control(type).active.set(true);
		var section = api.control(type).section.get();
		api.section(section).activate();
		//$(api.section(section).container).removeClass('hideSection');
		
		api.section(section).expand();
	}

	self.actualHeaderOptions = function(data){
		api.section('cl_page_options').expand();
		kirkiSetSettingValue.set( 'this_header_color', data['header_color'] );
	}
	
	self.updateSilent = function(element){
		api.control(element.type).updateSilent(element);
	}
	

	api.bind( 'ready', function() {
		self.init();       

        if( typeof api( 'cl_page_content' ) !== 'undefined' && api( 'cl_page_content' ).get()['changeset'] != api.settings.changeset.uuid ){

            var obj = { changeset: api.settings.changeset.uuid };
            api('cl_page_content').set(obj);
            
		}

		api.bind('save', function(){
			api.previewer.send('cl-save');
		});
	});

	return self;
}( jQuery, wp.customize ) );