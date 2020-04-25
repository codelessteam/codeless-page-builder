var api = wp.customize;

wp.customize.controlConstructor['css_tool'] = wp.customize.kirkiDynamicControl.extend({

	// When we're finished loading continue processing.
	initKirkiControl: function() {

		'use strict';
		var control = this;
        
		// Save the value
		control.container.on( 'input keyup', 'input', function(e) {
		 
			var value = ( _.isObject( control.setting.get() ) ? control.setting.get() : JSON.parse( control.setting.get() ) ),
			    el = jQuery(this).data('name'),
			    new_value = jQuery(this).val();
			
			if ( false === kirki.util.validate.cssValue( new_value ) && new_value != '' ) {
				
				jQuery(this).addClass('invalid');
				return;
				
			}else{
				if(!_.isObject(value))
					value = {};

				value = Object.assign({},value);

				jQuery(this).removeClass('invalid')
				value[el] = new_value;
				// Update the value in the customizer.
				kirki.setting.set( control.id,  value );
				
				
			}

		});

	}

});

wp.customize.controlConstructor['image_gallery'] = wp.customize.kirkiDynamicControl.extend({
    initKirkiControl: function(){
        this.galleryReady();
    },

    galleryReady: function(id){
		

		var control = this;
		// Shortcut so that we don't have to use _.bind every time we add a callback.
            _.bindAll( control, 'galleryOpenFrame', 'gallerySelect' );

            /**
             * Set gallery data and render content.
             */
            

            // Ensure attachment data is initially set.
            control.setGalleryDataAndRenderContent();

            // Bind events.
            control.container.on( 'click keydown', '.image-upload-button', control.galleryOpenFrame );
	},


	setGalleryDataAndRenderContent: function(id) {
        var control = this;
        var value = kirki.setting.get(control.id);

        if( !_.isUndefined(value) && value.length ){
       

        	value = JSON.parse( value.replace('__array__', '[').replace('__array__end__', ']') );
        }else
        	value = [];
               
        

        control.gallerySetAttachmentsData( value ).done( function() {
            control.galleryRenderAttachments();
            control.gallerySetupSortable();
        } );
    },


	    galleryRenderAttachments: function(){
	    	var inner_container, control;
	    	control = this;

	    	inner_container = control.container.find('[data-field="'+control.id+'"]').closest('.image-gallery-attachments');
	    	inner_container.html('');
	    	_.each(control.attachments, function(attachment){
	    		inner_container.append('<div class="image-gallery-thumbnail-wrapper" data-post-id="'+attachment.id+'"><img class="attachment-thumb" src="'+attachment.url+'" draggable="false" alt="control" /></div>');
	    	} );

	    	control.gallerySetupSortable();
	    },

		/**
         * Open the media modal.
         */
        galleryOpenFrame: function( event ) {
            event.preventDefault();

            if ( ! this.frame ) {
                this.galleryInitFrame();
            }

            this.frame.open();
        },

        /**
         * Initiate the media modal select frame.
         * Save it for using later in case needed.
         */
        galleryInitFrame: function() {

            var control = this, galleryPreSelectImages;
            this.frame = wp.media({

                button: {
                    text: 'Choose Images'
                },
                states: [
                    new wp.media.controller.Library({
                        title: 'Select Gallery Images',
                        library: wp.media.query({ type: 'image' }),
                        multiple: 'add'
                    })
                ]
            });

            /**
             * Pre-select images according to saved settings.
             */
            galleryPreSelectImages = function() {
                var selection, ids, attachment, library;

                library = control.frame.state().get( 'library' );
                selection = control.frame.state().get( 'selection' );

                ids = kirki.setting.get(control.id);
                if( !_.isUndefined( ids ) && !_.isEmpty( ids ) )
                    ids = ids.replace('__array__', '').replace( '__array__end__', '' ).split(',');
                else
                	ids = [];

                // Sort the selected images to top when opening media modal.
                library.comparator = function( a, b ) {
                    var hasA = true === this.mirroring.get( a.cid ),
                        hasB = true === this.mirroring.get( b.cid );

                    if ( ! hasA && hasB ) {
                        return -1;
                    } else if ( hasA && ! hasB ) {
                        return 1;
                    } else {
                        return 0;
                    }
                };

                _.each( ids, function( id ) {
                    attachment = wp.media.attachment( id );
                    selection.add( attachment ? [ attachment ] : [] );
                    library.add( attachment ? [ attachment ] : [] );
                });
            };

            changeopt = function(){
               
                var value = kirki.setting.get(control.id);
                
                kirki.setting.set( control.id, '' );
                kirki.setting.set( control.id, value );
            };

            control.frame.on( 'open', galleryPreSelectImages );
            control.frame.on( 'select', control.gallerySelect );
            control.frame.on( 'close', changeopt );

            
        },

        

        /**
         * Callback for selecting attachments.
         */
        gallerySelect: function() {

            var control = this, attachments, attachmentIds, fieldId, newValue;

            attachments = control.frame.state().get( 'selection' ).toJSON();
            control.attachments = attachments;

            attachmentIds = control.galleryGetAttachmentIds( attachments );
            
            fieldId = control.container.find('.image-gallery-attachments').data('field');
           

            newValue = JSON.stringify(attachmentIds).replace('[', '__array__').replace(']', '__array__end__');
			kirki.setting.set( control.id, newValue );
			control.container.focus();
			control.setGalleryDataAndRenderContent(fieldId);

        },

        /**
         * Get array of attachment id-s from attachment objects.
         *
         * @param {Array} attachments Attachments.
         * @returns {Array}
         */
        galleryGetAttachmentIds: function( attachments ) {
            var ids = [], i;
            for ( i in attachments ) {
                ids.push( attachments[ i ].id );
            }
            return ids;
        },


	gallerySetAttachmentsData: function( value ) {
            var control = this,
                promises = [];

            control.attachments = {};

            _.each( value, function( id, index ) {
                var hasAttachmentData = new jQuery.Deferred();
                wp.media.attachment( id ).fetch().done( function() {
                    control.attachments[ index ] = this.attributes;
                    hasAttachmentData.resolve();
                } );
                promises.push( hasAttachmentData );
            } );

            return jQuery.when.apply( undefined, promises ).promise();
    },

    gallerySetupSortable: function() {
            var control = this,
                list = jQuery( '.image-gallery-attachments' );
            list.sortable({
                items: '.image-gallery-thumbnail-wrapper',
                tolerance: 'pointer',
                stop: function() {
                    var selectedValues = [];
                    list.find( '.image-gallery-thumbnail-wrapper' ).each( function() {
                        var id;
                        id = parseInt( jQuery( this ).data( 'postId' ), 10 );
                        selectedValues.push( id );
                    } );
                    var fieldId = control.container.find('.image-gallery-attachments').data('field');
		            var newValue = JSON.stringify(selectedValues).replace('[', '__array__').replace(']', '__array__end__');

					kirki.setting.set( control.id, newValue);
                }
            });
    },
})


wp.customize.controlConstructor['grouptitle'] = wp.customize.kirkiDynamicControl.extend({});
wp.customize.controlConstructor['select_icon'] = wp.customize.kirkiDynamicControl.extend({});
wp.customize.controlConstructor['groupdivider'] = wp.customize.kirkiDynamicControl.extend({});



/* --------------------------------------------------------------------------------------------- */
/* --------------------------------------------------------------------------------------------- */
/* --------------------------------------------------------------------------------------------- */

var cl_kirki = {

    element: false,
    section: 'cl_codeless_page_builder',
    isRequired: {},
    tabActive: false,
    tabActiveId: false,
    map: false,

    initialize: function(element){
        var self = this;
        
        self.clearSection();

        self.element = element;
        if( self.element.header_element )
            self.section = 'cl_codeless_header_builder';
        else
            self.section = 'cl_codeless_page_builder';
        
        self.createElements();
        self.expandSection();
        
    },

    clearSection: function(){
        api.section( this.section ).collapse();

        _.each( api.section(this.section).controls(), function(control, i){
			api.control.remove( control.id );
			control.container.remove();
			api.remove( control.id );
        });
        this.element = false;
    },

    createElements: function(){
        var self = this;

        if( self.element.header_element )
            self.map = codelessHeaderElementsMap[ self.element.type ];
        else
            self.map = codelessElementsMap[ self.element.type ];

        api.section( self.section ).container.find( '.customize-section-title h3' ).html( '<span class="customize-action">Page Builder</span>' + self.map.label );

        _.each(self.map.fields, function( field, key ){
            var value = !_.isUndefined( self.element['options'][key] ) ? self.element['options'][key] : field['default'];
            
            var new_key = self.element.type + '[' + key + ']';
            

            // Custom Image Manager
            if( field['type'] == 'image' || field['type'] == 'kirki-image' ){
                
                if( _.isObject(value) && !_.isUndefined( value.id ) ){
                    value = { id: value.id, url: decodeURIComponent( value.url ) };
                    field['choices'] = {save_as: 'array'};
                }else if( _.isString( value ) && value.indexOf('http') !== -1 ) {
                    value = decodeURIComponent( value );
                    field['choices'] = {save_as: 'url'};
                }else{
                    field['choices'] = {save_as: 'array'};
                }

                if( value == '' ){
                    value = { url: '' };
                }
            }
                
            

            // Create Setting
            var setting = new api.Setting( new_key, value, { transport: 'postMessage' } );
            api.add( setting );
            
            // Create Control
            var params_ = jQuery.extend( field, {
                value: value,
                id: new_key,
                section: self.section,
                settings: {
                    'default': new_key
                },
                description: field.tooltip,
                tooltip: '',
                
                type: self.reMapType( field.type )
            });
            
            params_ = self.customParamsCheck( params_, key );
            var control = self.createControl( new_key, params_ );
           
            api.control.add(new_key, control);
            
            // Custom for multiple select
            if( params_.type == 'kirki-select' && !_.isUndefined( params_.multiple ) && params_.multiple )
                control.container.addClass('multiple-select');

            // Build Required List
            if( !_.isUndefined( field['cl_required'] ) && !_.isUndefined( field['cl_required'][0]['setting'] ) )
                self.isRequired[ field['cl_required'][0]['setting'] ] = true;

            self.manageTabs(field, control);

             api(new_key, function(setting) {
                setting.bind(function() {
                    self.onSettingChange(setting, key);
                });
            });

        });
    },


    onSettingChange: function( setting, old_key ){
        var self = this;
        var new_value = setting.get();
       
        var isRequired = ( !_.isUndefined( self.isRequired[old_key] ) && self.isRequired[old_key] ) ? true : false;
        console.log(new_value);
        setting.previewer.send('cl_element_updated', [ self.element.id, self.element.type, old_key, new_value, isRequired, self.element.header_element ] );

    },

    createControl: function(key, params_){
        if( params_.type == 'kirki-slider' )
            return new api.controlConstructor['kirki-slider']( key, {
                params:params_
            } );
        
        if( params_.type == 'kirki-switch' )
            return new api.controlConstructor['kirki-switch']( key, {
                params:params_
            } );

        if( params_.type == 'kirki-image' )
            return new api.controlConstructor['kirki-image']( key, {
                params:params_
            } );

        if( params_.type == 'kirki-multicheck' )
            return new api.controlConstructor['kirki-multicheck']( key, {
                params:params_
            } );
        
        if( params_.type == 'kirki-sortable' )
            return new api.controlConstructor['kirki-sortable']( key, {
                params:params_
            } );

        if( params_.type == 'kirki-select' )
            return new api.controlConstructor['kirki-select']( key, {
                params:params_
            } );
        
        if( params_.type == 'css_tool' )
            return new api.controlConstructor['css_tool']( key, {
                params:params_
            } );

        if( params_.type == 'image_gallery' )
            return new api.controlConstructor['image_gallery']( key, {
                params:params_
            } );

        
        return new api.kirkiDynamicControl( key, {
            params:params_
        } );
        
    },

    customParamsCheck: function( params, key ){
        if( params.type == 'kirki-color' ){
            if( _.isUndefined( params.choices ) )
                params.choices = { alpha: true }
        }

        if( key == 'content' )
            params.type = 'textarea';

        return params;
    },

    expandSection: function(){
        var self = this;
        setTimeout(function(){
            api.section(self.section).expand();
            self.activeTabs();
            self.manageRequirements();
		}, 400);
    },

    manageRequirements: function(){
        var self = this;
        _.each(self.map.fields, function( field, key ){
            var new_key = self.element.type + '[' + key + ']';
            var control = api.control(new_key);
            
            self.checkRequirement(field, control);
        });
    },

    reMapType: function( type ){
        var reMap = {
            'color': 'kirki-color',
            'switch': 'kirki-switch',
            'select': 'kirki-select',
            'slider': 'kirki-slider',
            'inline_select': 'kirki-select',
            'text' : 'text',
            'inline_text' : 'text',
            'image' : 'kirki-image', 
            'multicheck': 'kirki-multicheck',
            'sortable': 'kirki-sortable',
            'textarea': 'kirki-input-textarea'
        }

        return _.isUndefined( reMap[type] ) ? type : reMap[type];
    },

    manageTabs: function( field, control ){
        var self = this;
        if( field['type'] == 'tab_start' ){
            self.tabActive = true;
            self.tabActiveId = field['tabid'];

            return true;
        }

        if( field['type'] == 'tab_end' ){
            self.tabActive = false;
            self.tabActiveId = false;

            return true;
        }

        if( self.tabActive ){
            control.container.attr('data-tabid', self.tabActiveId);
        }
    },

    isActiveCallback: function(field){
        var self = this;
        if (_.isUndefined(field.cl_required) || field.cl_required.length == 0)
            return true;
        if (_.isUndefined(field.cl_required[0].setting) || _.isUndefined(field.cl_required[0].operator) || _.isUndefined(field.cl_required[0].value))
            return true;

        var toCheckSetting = self.element.type + '[' + field.cl_required[0].setting + ']';
        
        if (field.cl_required[0].operator == '==') {
            if (api.has(toCheckSetting) && api(toCheckSetting).get() == field.cl_required[0].value  )
                return true;
        }
        
        if (field.cl_required[0].operator == '!=') {
           
            if (api.has(toCheckSetting) && api(toCheckSetting).get() != field.cl_required[0].value)
                return true;
        }

        return false;
    },

    checkRequirement: function( field, control ){
        var self = this;
       
        if (!_.isUndefined(field.cl_required) && !_.isUndefined(field.cl_required[0].setting) && !_.isEmpty(field.cl_required[0].setting)) {
            control.container.addClass('with_requirements');
            
            if( self.isActiveCallback(field) )
                control.container.css('display', 'list-item');
            else
                control.container.css('display', 'none');
            

            var toCheckSetting = self.element.type + '[' + field.cl_required[0].setting + ']';
            
            api(toCheckSetting, function(setting) {
                setting.bind(function() {
                    if( self.isActiveCallback(field) )
                        control.container.css('display', 'list-item');
                    else
                        control.container.css('display', 'none');
                });
            });
        }
    },

    activeTabs: function(){
        var self = this;
        var $container = api.section( this.section ).container;
        if( $container.find( '.tab_sections .active' ).length == 0 )
            return;

        var $actived = $container.find( '.tab_sections .active' );
        
        var actived_id = $actived.attr('data-tab');
        
        self.manageRequirements();
        setTimeout(function(){
            $container.find( '.customize-control:not(.customize-control-show_tabs):not([data-tabid="'+actived_id+'"])' ).css( 'display', 'none' );
        }, 50);
        
        

        $container.find( '.tab_sections a' ).on('click', function(e){
            e.preventDefault();

            jQuery(this).siblings().removeClass('active');
            jQuery(this).addClass('active');

            actived_id = jQuery(this).attr('data-tab');
            $container.find( '.customize-control').css( 'display', 'list-item' );
            self.manageRequirements();
            setTimeout(function(){
                $container.find( '.customize-control:not(.customize-control-show_tabs):not([data-tabid="'+actived_id+'"])' ).css( 'display', 'none' );
            }, 50);
            
            
        });

    }

};

