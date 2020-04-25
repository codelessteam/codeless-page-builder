function cl_guid() {
    return CLS4() + CLS4() + "-" + CLS4()
}

function CLS4() {
    return (65536 * (1 + Math.random()) | 0).toString(16).substring(1)
}

function rgbToHex(color) {
    if (color.charAt(0) === "#") {
        return color;
    }

    color = color.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
    return (color && color.length === 4) ? "#" +
      ("0" + parseInt(color[1],10).toString(16)).slice(-2) +
      ("0" + parseInt(color[2],10).toString(16)).slice(-2) +
      ("0" + parseInt(color[3],10).toString(16)).slice(-2) : '';
}

function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function loadCSS(href){
    var ss = document.styleSheets;
    for (var i = 0, max = ss.length; i < max; i++) {
        if (ss[i].href == href)
            return;
    }
    var link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = href;

    document.getElementsByTagName("head")[0].appendChild(link);
}



/* cl-shortcodes.js */
var cl = cl || {};


(function($, api) {
        
        cl.memoize = function(func, resolver) {
            var cache = {};
            return function() {
                var key = resolver ? resolver.apply(this, arguments) : arguments[0];
                return _.hasOwnProperty.call(cache, key) || (cache[key] = func.apply(this, arguments)), _.isObject(cache[key]) ? jQuery.fn.extend(!0, {}, cache[key]) : cache[key]
            };
        };
        
        cl.elementCoordinates = {};
        
        cl.getMapped = cl.memoize(function(tag) {
            return cl.map[tag] || {}
        });

        cl.objectToString = function(data){
            var new_string = '';
            _.each(data, function( value, index ){
                new_string += index + ':' + encodeURIComponent(value) + '|';
            });

            new_string = new_string.substring(0, new_string.length-1);
            return new_string;
        };

        cl.checkObjectValue = function(key, value, model){
            var field = cl.map[model.attributes.shortcode].fields[key];
            if( _.isUndefined(field) )
                return false;

            if( field['type'] == 'css_tool' || 
                 field['type'] == 'multicheck' || 
                 ( field['type'] == 'select' && !_.isUndefined( field['multiple'] ) && field['multiple'] ) ||
                 field['type'] == 'image' ||
                 value.indexOf("_-_json") !== -1 )
                 return true;

            return false; 
         }

         cl.stringToObject = function( value ){
             // Fallback old content
            if( value.indexOf("_-_json") !== -1 ){
                value = value.replace(/'/g, '"');
                value = value.replace("_-_json", '');
                value = value.replace(/\s/g, '');
                value = jQuery.parseJSON(value);
                return value;
            }else if( value.indexOf( ":" ) !== -1 ) {
                var final_values = {};
                var values = {};
                var inner_values = {};
                if( value.indexOf("|") === -1 && value.indexOf( ":" ) !== -1 ){
                    values = value.split( ':' );
                    final_values[values[0]] = decodeURIComponent(values[1]);
                    return final_values;
                }else if( value.indexOf("|") !== -1  ) {
                    
                    values = value.split( '|' );
                    _.each(values, function( sv, sk ){
                        inner_values = sv.split( ':' );
                        final_values[inner_values[0]] = decodeURIComponent(inner_values[1]);
                    });
                    return final_values;
                }
            }

            return {};
            
         }

        cl.buildNotOverwrite = cl.memoize(function(tag) {
            var return_val = [];
            var fields = _.isObject(cl.getMapped(tag).fields) ? cl.getMapped(tag).fields : [];
            _.each(fields, function(param, index){
                if( param['type'] == 'inline_text' || param['type'] == 'select_icon' )
                    return_val.push( index );
            });

            return return_val;
        });
        
        cl.getParamSettings = cl.memoize(function(tag, paramName) {
            var params, paramSettings;
            params = _.isObject(cl.getMapped(tag).fields) ? cl.getMapped(tag).fields : [];
            
            return paramSettings = _.find(params, function(settings, name) {
                
                return _.isObject(settings) && name === paramName
            }, this)
        });

        cl.getPredefinedList = function(tag, pre_id) {
            var predefined = _.isObject(cl.getMapped(tag).predefined) ? cl.getMapped(tag).predefined : [];
            
            if( _.isObject( predefined[pre_id] ) )
                return predefined[pre_id];

            return false;
        };

        cl.getCSSDependency = cl.memoize(function(tag) {
            var dependency = _.isObject(cl.getMapped(tag).css_dependency) ? cl.getMapped(tag).css_dependency : [];
            
            return dependency;
        });

        cl.getContentBlock = cl.memoize(function(id) {
            var block = _.isObject(cl.content_blocks[id]) ? cl.content_blocks[id] : false;
            
            if( _.isObject( block ) )
                return block;

            return false;
        });
        
        cl.getDefaultParams = cl.memoize(function(tag) {
            var params, default_params = {};
            params = _.isObject(cl.getMapped(tag).fields) ? cl.getMapped(tag).fields : [];
            
            _.each(params, function(param, index){
                default_params[index] = param['default'];
            });
            
            return default_params;
        });


        
        
        var Shortcode = Backbone.Model.extend({
                defaults: function() {
                    var id = cl_guid();
                    return {
                        id: id,
                        shortcode: "cl_text",
                        order: cl.shortcodes.nextOrder(),
                        params: {},
                        parent_id: !1
                    }
                },
                settings: !1,
                getParam: function(key) {
                    return _.isObject(this.get("params")) && !_.isUndefined(this.get("params")[key]) ? this.get("params")[key] : ""
                },
                sync: function() {
                    return false;
                },
                setting: function(name) {
                    return !1 === this.settings && (this.settings = cl.getMapped(this.get("shortcode")) || {}), this.settings[name]
                },
                view: !1
            }),
            Shortcodes = Backbone.Collection.extend({
                model: Shortcode,
                sync: function() {
                    return false
                },
                nextOrder: function() {
                    return this.length ? this.last().get("order") + 1 : 1
                },
                initialize: function() {
                    this.bind("remove", this.removeChildren, this);
                    this.bind("remove", this.removeEvents, this);
                    this.bind("shortcode:placed_after_id", this.placedAfterID, this);
                },

                placedAfterID: function(model){
                    var place_after_id = model.get('place_after_id'),
                        before_model = cl.shortcodes.get(place_after_id),
                        new_order = before_model.get('order') + 1

                    model.save({
                        order: new_order

                    }, {
                        silent: true
                    })
                },

                comparator: function(model) {
                    return model.get("order")
                },

                removeEvents: function(model) {
                },
                removeChildren: function(parent) {
                    var models = cl.shortcodes.where({
                        parent_id: parent.id
                    });
                    _.each(models, function(model) {
                        model.destroy()
                    }, this)
                },
                stringify: function(state) {
                    var models = _.sortBy(cl.shortcodes.where({
                        parent_id: !1
                    }), function(model) {
                        return model.get("order")
                    });
                    return this.modelsToString(models, state)
                },
                createShortcodeString: function(model, state) {
                    var mapped, data, tag, params, content, paramsForString = {}, mergedParams, isContainer;
                    tag = model.get("shortcode"); 
                    params = _.extend({}, model.get("params"));
                    defaultParams = cl.getDefaultParams(tag);
                  
                    var new_params = {};



                    
                    _.each( params, function(value, key){

                        
                        if( !_.isObject(defaultParams[key]) && value != defaultParams[key] ){
                            if( value != 'undefined' )
                                new_params[key] = value;
                            else
                                new_params[key] = defaultParams[key];
                        }
                        else if( _.isObject(defaultParams[key]) && _.isObject(value) && !_.isEmpty( value ) ) {

                            _.each( value, function(subvalue, subkey){
                                if( subvalue != defaultParams[key][subkey] ){
                                    if( _.isUndefined(new_params[key]) )
                                        new_params[key] = {};

                                    new_params[key][subkey] = subvalue;
                                }
                            } );
                        }

                    })

                    params = new_params;
                   
                    
                    _.each(params, function(value, key) {



                        if(key != 'content'){
                            if(_.isObject(value) && !_.isEmpty( value )){
                                value = cl.objectToString(value);
                                
                                paramsForString[key] = value;

                            }else if( _.isObject(value) && _.isEmpty( value ) ){
                                delete paramsForString[key]
                            }else{
                                paramsForString[key] = value;
                            }

                            
                            
                        }
                    });


                    mapped = cl.getMapped(tag);
                    isContainer = _.isObject(mapped) && (_.isBoolean(mapped.is_container) && !0 === mapped.is_container || !_.isEmpty(mapped.as_parent));
                        
                    content = this._getShortcodeContent(model);
                    
                    data = {
                        tag: tag,
                        attrs: paramsForString,
                        content: content,
                        type: _.isUndefined(cl.getParamSettings(tag, "content")) && !isContainer ? "single" : ""
                    };
                    
                        
                        //_.isUndefined(state) ? model.trigger("stringify", model, data) : model.trigger("stringify:" + state, model, data);
                    return wp.shortcode.string(data)
                    
                },
            
                
                modelsToString: function(models) {
                    var string = _.reduce(models, function(memo, model) {
                        return memo + this.createShortcodeString(model)
                    }, "", this);
                    return string
                },
                _getShortcodeContent: function(parent) {
                    var models, params;
                    return models = _.sortBy(cl.shortcodes.where({
                        parent_id: parent.get("id")
                    }), function(model) {
                        return model.get("order")
                    }), models.length ? _.reduce(models, function(memo, model) {
                        return memo + this.createShortcodeString(model)
                    }, "", this) : (params = _.extend({}, parent.get("params")), _.isUndefined(params.content) ? "" : params.content)
                },
                create: function(model, options) {
                    
                    model = Shortcodes.__super__.create.call(this, model, options);
                    cl.events.trigger('shortcodes:add', model);
                    return model
                    
                }
            });
            
            
            cl.shortcode_view = Backbone.View.extend({
                
                hold_hover_on: !1,
                events: {
                    "click > .cl_controls .cl_control-btn-delete": "destroy",
                    "click > .cl_controls .cl_control-btn-edit": "edit",

                    "click > .cl_controls .cl_control-btn-copy": "openCopyOptions",
                    "click > .cl_controls .cl_control-btn-clone": "clone",
                    "click > .cl_controls .cl_control-btn-copy-style": "copyStyle",
                    "click > .cl_controls .cl_control-btn-paste-style": "pasteStyle",
                    "click > .cl_controls .cl_control-btn-copy-element": "copyElement",


                    "click .cl-custom-post-button": 'openCustomPostSection',
                    "click .cl-add-custom-post-button": 'createCustomPost',

                    "click > .cl_service i[class]": 'changeIcon',
                    "click > .cl_service svg": 'changeIcon',
                    "click > .cl_list_item > i[class]": 'changeIcon',
                    "click > .cl_divider i[class]": 'changeIcon',
                    "click > .cl_icon i[class]": 'changeIcon',
                    "click > .wrapper-heading i[class]": 'changeIcon',

                    "click": "shiftClickOpen",
                    "click > .cl_controls .cl_element-name": "edit",
                    "contextmenu": "contextMenu"
                },
                controls_set: !1,
                $content: !1,
                move_timeout: !1,
                out_timeout: !1,
                hold_active: !0,
                builder: !1,
                default_controls_template: !1,
                initialize: function() {
                    _.bindAll(this, 'reloadTemplate', 'copyElement' );
                    this.listenTo(this.model, "destroy", this.removeView);
                    this.listenTo(this.model, "change:params", this.update);
                    this.listenTo(this.model, "update:icon", this.updateIcon);
                    this.listenTo(this.model, "update:svg", this.updateSVG);
                    this.listenTo(this.model, "change:parent_id", this.changeParentId);
                    this.listenTo(this.model, "change:order", this.changeOrder);
                    this.listenTo(this.model, "updateField", this.updateFieldEvent);
                    
                    wp.customize.preview.bind('cl_reload_template', this.reloadTemplate);
                 
                },

                reloadTemplate: function(item){
                    if(item[0] != this.model.get('id'))
                        return;
                    
                    if(!this.builder)
                        this.builder = new cl.ShortcodesBuilder;

                    this.$el.addClass('loading');
                    this.builder.create(this.model.toJSON());
                   
                    cl.activity = 'replace';
                    this.builder.render(false, 'replace');
                        //this.$el.removeClass('loading');
                    return;
                },

                contextMenu: function(e){
                    e.preventDefault();
                    var self = this;
                    superCm.settings.title = this.model.setting('label');
                    superCm.createMenu([
                        {
                          
                            label: 'Edit',
                            action: function(){ 
                                self.edit(e);  
                                
                                superCm.destroyMenu();
                                setTimeout(function(){
                                    self.$el.addClass('cl-selected-element');
                                }, 600);
                            },
                        },
                        {
                            
                            label: 'Delete',
                            action: function(){ 
                                self.destroy(e); 
                                superCm.destroyMenu();
                            },
                        },
                        {
                            
                            label: 'Clone',
                            action: function(){ 
                                self.clone(e); 
                                superCm.destroyMenu();
                            },
                        },
                        {
                            
                            label: 'Copy Element',
                            action: function(){ 
                                self.copyElement(e); 
                                superCm.destroyMenu();
                            },
                        },
                        {
                            
                            label: 'Copy Style',
                            action: function(){ 
                                self.copyStyle(e); 
                                superCm.destroyMenu();
                            },
                        },
                        {
                            label: 'Paste Style',
                            action: function(){ 
                                self.pasteStyle(e); 
                                superCm.destroyMenu();
                            },
                        },
                        {
                            separator: true,
                        },
                        {
                            label: 'Parent Column',
                            action: function(){ 
                                var parent = cl.shortcodes.get(self.model.get("parent_id"));
                                if( !_.isUndefined( parent.view ) && !_.isUndefined( parent.view.edit ) ){
                                    parent.view.edit();
                                    setTimeout(function(){
                                        parent.view.$el.addClass('cl-selected-element');
                                    }, 600);
                                }
                                superCm.destroyMenu();
                            },
                        },
                        
                    ], e);
                },

                changeIcon: function(e){
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    isSVG = false;
                    if( $(e.target).is('svg') || $(e.target).parents('svg').length > 0 )
                        isSVG = true;

                    cl.changeIconDialog.render(this.model, true, {pageX: e.pageX, pageY:e.pageY}, isSVG); 
                },
                
                openCustomPostSection: function(e){
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    var type = $(e.target).data('type');
                    var id = $(e.target).data('id');
                    wp.customize.preview.send( 'cl_active_section_by_id', {section: 'post['+type+']['+id+']', id: id} )
                },

                createCustomPost: function(e){
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    var type = $(e.target).data('type');
                    var modelID = this.model.get('id');
                    wp.customize.preview.send( 'cl_create_custom_post', {postType: type, modelId: modelID} );
                },

                changeOrder:function(){
                    cl.updateCustomizer();  
                },

                changed: function(){
                    this.$el.removeClass('loading');
                    cl.activity = false;
                },

                addingElement: function(){
                    this.$el.addClass('loading');
                },
                
                check_frontJS: function(){
                    var shortcode = this.model.get('shortcode');
                    
                    if( !_.isUndefined(window['codeless_builder_'+shortcode]) ){
                        window['codeless_builder_'+shortcode]();
                    }
                },
                
                update: function(){
                    cl.updateCustomizer();
                    
                },

                updateIcon: function(value){
                    var fields = this.model.setting('fields');
                    var field = fields['icon'];

                    var that = this;    
                    window.requestAnimationFrame(function(){that.updateField('icon', value, field) } );
                    cl.updateCustomizer();
                },

                updateSVG: function(value){
                    var href = this.$el.find('svg use')[0].getAttribute('xlink:href');
                    var newHref = href.substring(0, href.indexOf('#')) + '#';
                    this.$el.find('svg use')[0].setAttribute('xlink:href', newHref+value);
                    cl.updateCustomizer();
                },
                

                openCopyOptions: function(e){
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();

                    var toClose = this.$el.find('.cl_controls .copy-options').hasClass('open-options');

                    this.$el.closest('.cl_cl_column, .cl_cl_column_inner').find('.open-options').removeClass('open-options');

                    if( ! toClose )
                        this.$el.find('.cl_controls .copy-options').addClass( 'open-options' );
                },


                copyStyle: function(e){
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    this.openCopyOptions();

                    var api = wp.customize;
                    if( !_.isUndefined(parent.wp) )
                        api = parent.wp.customize;

                    api('cl-style-clipboard').set( { style: this.model.get('params'), tag: this.model.get('shortcode') } );
                    cl.showMessage(this.model.setting("label") + ' style copied' );
                },


                pasteStyle: function(e){
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                        
                        var api = wp.customize;
                        if( !_.isUndefined(parent.wp) )
                            api = parent.wp.customize;

                        style = api('cl-style-clipboard').get();

                        if( !_.isUndefined(style.tag) && style.tag != this.model.get('shortcode') )
                            return;

                        current = this.model.get('params');
                        new_params = style.style;

                        var not_overwrite = cl.buildNotOverwrite( style.tag );

                        _.each(not_overwrite, function(param){
                            if( ! _.isUndefined(current[param]) )
                                new_params[param] = current[param];
                        });
                        
                        
                        this.model.save({
                            params: new_params
                        }, {
                            silent: true
                        });
                        this.openCopyOptions();

                        this.reloadTemplate([this.model.get('id')]);
                        cl.showMessage(this.model.setting("label") + ' style applied with success' );
                    
                },

                copyElement: function(e){
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    this.openCopyOptions();

                    var api = wp.customize;
                    if( !_.isUndefined(parent.wp) )
                        api = parent.wp.customize;

                    api('cl-element-clipboard').set( this.model );
                    cl.showMessage(this.model.setting("label") + ' copied' );
                },


                clone: function(e){
                    
                    var new_model, builder = new cl.ShortcodesBuilder;
                    
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    
                    if(!this.builder)
                        this.builder = builder;
                    
                    new_model = cl.CloneModel(builder, this.model, this.model.get("parent_id"));
                    
                    cl.showMessage(new_model.setting("label") + ' cloned with success' );
                    void builder.render();
                    cl.updateCustomizer();
                    
                    
           
                },
                

                updateReverseField: function(field_id, field_value, field){
                    if(!_.isUndefined(field['selector']) && !_.isEmpty(field['selector']) && !_.isUndefined(field['css_property']) && !_.isEmpty(field['css_property']) ){
                        var $field_el = this.$el.find(field['selector']);
                     
                        if(_.isString(field['css_property']))
                            $field_el.css(field['css_property'], '');
                        else if(_.isObject(field['css_property']) || _.isArray(field['css_property'])){
                            _.each(field['css_property'], function(prop, index){
                            
                                if(_.isString(prop))
                                    $field_el.css(prop, '');
                            });
                        }
                        
                    }
                    
                },

                updateFieldEvent: function( data ){
                    
                    this.updateField( data[0], data[1], data[2], data[3] );
                },
                
                updateField: function(field_id, field_value, field, isRequired){
                  

                    var field_type = field['type'];

                    
                    this.builder = new cl.ShortcodesBuilder;
                   
                    if(  ! this.model.setting('is_container') && ( (!_.isUndefined(field['reloadTemplate']) && field['reloadTemplate'] ) || isRequired ) ){
                        
                        this.$el.addClass('loading');
                        this.builder.create(this.model.toJSON());
                        
                        cl.activity = 'replace';
                        this.builder.render(false, 'replace');
                        return;
                    }
                    
                    
                    /* CSS Property */
                    if(!_.isUndefined(field['selector']) && !_.isEmpty(field['selector']) && !_.isUndefined(field['css_property']) && !_.isEmpty(field['css_property']) ){
                        var $field_el = this.$el.find(field['selector']);
                        


                        if( !_.isUndefined(field['media_query']) && !_.isEmpty(field['media_query']) ){
                            var custom_css = '@media '+field['media_query'] + '{';

                                var suffix = !_.isUndefined( field['suffix'] ) ? field['suffix'] : '';
                                custom_css += '#clr_'+this.model.get('id')+' '+field['selector']+'{';
                                    custom_css += field['css_property']+': '+field_value+suffix + ' !important';
                                custom_css += '}';

                            custom_css += '}';

                            

                            if ( ! jQuery( '#codeless-custom-css-model-' + this.model.get('id') + '-' + field_id ).size() ) {
                                jQuery( 'head' ).append( '<style id="codeless-custom-css-model-' + this.model.get('id') + '-' + field_id + '"></style>' );
                            }
                            jQuery( '#codeless-custom-css-model-' + this.model.get('id') + '-' + field_id ).text( custom_css );

                        }else{
                            if(field_type == 'image'){
                               
                                if( _.isObject(field_value) && !_.isUndefined( field_value.url ) )
                                    field_value = 'url('+decodeURIComponent(field_value.url)+')';
                                else if( _.isString(field_value) )
                                    field_value = 'url('+decodeURIComponent(field_value)+')';
                            }
                             
                            if(field_type == 'slider' && !_.isUndefined(field['suffix']))
                                field_value = field_value + field['suffix']
                            
                            if( field['css_property'] == 'font-family' ){
                                if( field_value == 'theme_default' )
                                    field_value = '';
                                else{
                             
                                    WebFont.load({
                                        google: { 
                                            families: [field_value] 
                                        } 
                                    }); 
                                    field_value = field_value;
                                }

                            }
                            
                            if(_.isString(field['css_property'])){
                                if( field['css_property'] == 'font-family' ){
                                    $field_el.css({ 'font-family': field_value });
                                }
                                else
                                    $field_el.css(field['css_property'], field_value);
                            }
                            else if(_.isObject(field['css_property']) || _.isArray(field['css_property'])){
                                _.each(field['css_property'], function(prop, index){
                                    
                                    if(_.isString(prop))
                                        $field_el.css(prop, field_value);
                                    else if(_.isObject(prop) || _.isArray(prop)){
                                        var extra_css_property = prop[0];
                                        var executed = false;
                                        _.each(prop[1], function(extra_prop, key){
                                            
                                            if(key == field_value){
                                                
                                                $field_el.css(extra_css_property, extra_prop);
                                                if(extra_prop == 'cover'){
                                                    $field_el.addClass('bg_cover');
                                                }
                                                executed = true;
                                                return;
                                                
                                            }else if(key == 'other' && !executed){
                                                
                                                if(extra_css_property == 'background-size')
                                                    $field_el.removeClass('bg_cover');
                                                    
                                                $field_el.css(extra_css_property, extra_prop);
                                                
                                            }
                                            
                                        });
                                    }    
                                    
                                });
                            }
                        }

                        
                        
                    }
                    
                    /* addClass */
                    if(!_.isUndefined(field['selector']) && !_.isEmpty(field['selector']) && !_.isUndefined(field['addClass']) && !_.isEmpty(field['addClass']) ){
                        var $field_el = this.$el.find(field['selector']);
                        
                        if(field_value)
                            $field_el.addClass(field['addClass']);
                        else
                            $field_el.removeClass(field['addClass']);
                    }
                    
                    
                    /* htmldata */
                    if(!_.isUndefined(field['selector']) && !_.isEmpty(field['selector']) && !_.isUndefined(field['htmldata']) && !_.isEmpty(field['htmldata']) ){
                        var $field_el = this.$el.find(field['selector']);
                        
                        if(field_value != 'none')
                            $field_el.attr('data-'+field['htmldata'], field_value);
                        else
                            $field_el.attr('data-'+field['htmldata'], '0');
                    }
                    
                    
                    
                    /* Select Class */
                    if(!_.isUndefined(field['selector']) && !_.isEmpty(field['selector']) && !_.isUndefined(field['selectClass']) ){
                        var $field_el = this.$el.find(field['selector']);
                        
                        if( field_type == 'select_icon' ){
                            this.$el.find(field['selector']).each(function(){
                                $(this)[0].className = '';
                            });
                        }else{
                            _.each(field['choices'], function(choice, index){
                                $field_el.removeClass(field['selectClass']+index); 
                            });
                        }
                        
                        
                        if(_.isString(field_value))
                            $field_el.addClass(field['selectClass']+field_value);
                        else if(_.isObject(field_value) || _.isArray(field_value)){
                            _.each(field_value, function(value, key){
                                 $field_el.addClass(field['selectClass']+value);
                            });
                        }
                            
                    }
                    
                    /* Custom Function */
                    if(_.isFunction(this['inlineEdit_'+this.model.get('shortcode')+'_'+field_id])){
                        this['inlineEdit_'+this.model.get('shortcode')+'_'+field_id](field_id, field_value);
                    }else if(_.isFunction(this['inlineEdit_'+field_id])){
                        this['inlineEdit_'+field_id](field_id, field_value, field);
                    }
                    
                    
                    if(!_.isUndefined(field['customJS']) && !_.isEmpty(field['customJS']) && _.isString( field['customJS'] ) ){
                        this[field['customJS']](field_id, field_value);
                    }

                    if(!_.isUndefined(field['customJS']) && !_.isEmpty(field['customJS']) && !_.isUndefined(field['customJS']['front']) ){
                        if( !_.isUndefined(field['customJS']['params']) )
                            window[ field['customJS']['front'] ](field['customJS']['params'], true);
                        else
                            window[ field['customJS']['front'] ](null, true);
                    }
                    
                    
                    if(field_type == 'css_tool'){
                        var $field_el = this.$el.find(field['selector']);

                        if( _.isUndefined( field['media_query'] ) ){
                            
                            
                            if(field_value != null && _.isObject(field_value) )
                                $field_el.css(field_value);
                           
                        }else{

                            var custom_css = '@media '+field['media_query'] + '{';

                                custom_css += '#clr_'+this.model.get('id')+' '+field['selector']+'{';

                                    if( _.isObject( field_value ) ){
                                        _.each(field_value, function(subvalue, subkey){
                                            custom_css += subkey+': '+subvalue + ' !important; ';
                                        });
                                    }
                                    
                                custom_css += '}';

                            custom_css += '}';

                            

                            if ( ! jQuery( '#codeless-custom-css-model-' + this.model.get('id') + '-' + field_id ).size() ) {
                                jQuery( 'head' ).append( '<style id="codeless-custom-css-model-' + this.model.get('id') + '-' + field_id + '"></style>' );
                            }
                            jQuery( '#codeless-custom-css-model-' + this.model.get('id') + '-' + field_id ).text( custom_css );
                        }
                        
                    }

                    if( field_type == 'inline_text' ){
                        var $field_el = this.$el.find( field['selector'] );
                        $field_el.html( field_value );
                    }
                    
                   
                    /*if(!_.isUndefined(cl_required) ){
                        
                        var fields = this.model.setting('fields');
                        var params = this.model.get('params');
                        var operators = {
                           '==': function(a, b){ return a==b},
                           '!=': function(a, b){ return a!=b}
                        };

                        _.each(cl_required, function(opt, index){
                            var field_id = opt['setting'],
                                field_val = !_.isUndefined(params[opt['setting']]) ? params[opt['setting']] : fields[field_id]['default'],
                                field = fields[field_id],
                                new_cl_required = null;
                            
                            if(!_.isUndefined(cl_required[field_id]))
                                new_cl_required = cl_required[field_id];
                         
                            if( operators[opt['operator']](field_value, opt['value'] ) )   
                              this.updateField(field_id, field_val, field, new_cl_required);
                            else
                              // Reverse Update
                              this.updateReverseField(field_id, field_val, field);
                            
                        }, this);
                        
                    }*/
                },
                
                edit: function(e){
                    _.isObject(e) && e.preventDefault();
                    cl.$page.find('.cl-selected-element').removeClass('cl-selected-element');
                    this.$el.addClass('cl-selected-element');
                    this.addPaddingDrag();
                    this.addMarginDrag();
                    cl.app.showEditPanel(this.model);
                },
                
                changeParentId: function() {
                    var parent, parent_id = this.model.get("parent_id");
                    this.delegateEvents();
                    false === parent_id ? cl.app.placeElement(this.$el) : (parent = cl.shortcodes.get(parent_id), parent && parent.view && parent.view.placeElement(this.$el));
                    cl.updateCustomizer();
                },
                    
                checkCSSDependency: function(){
                    var css = cl.getCSSDependency(this.model.get('shortcode') );

                    
                    _.each(css, function(file){
                        loadCSS(file);
                    });

                },

                render: function() {

                    this.checkCSSDependency();

                    this.$el.attr("data-model-id", this.model.get("id"));
                    this.$el.attr('id', 'clr_'+this.model.get("id"));
                    var tag = this.model.get("shortcode");
                    this.$el.attr("data-tag", tag);
                    
                    this.$el.addClass("cl_" + tag);
                    this.addControls();
                    if( !_.isUndefined( window['CL_FRONT'] ) )
                        window.CL_FRONT.animations(this.$el, true);

                    if(!_.isUndefined( this.model.get('cloned') ) &&  this.model.get('cloned') == true )  
                        cl.events.trigger('shortcodes:cloned', this.model);


                    if( cl.activity == 'replace' || ! this.model.get('from_content') )
                        this.check_frontJS();

                    var is_container = this.model.setting('is_container');
                    is_container && this.$el.addClass("cl_container-block");

                    var is_root = this.model.setting('is_root');
                    is_root && this.$el.addClass('cl_root-element');
                    
                    this.inline_edit();
                    $('body [data-codeless="true"]').removeClass('loading');


                    

                    return this;
                },
                

                addMarginDrag: function(){
                    var paddingSelector = !_.isUndefined( cl.getMapped(this.model.get('shortcode'))['fields']['css_style'] ) ? cl.getMapped(this.model.get('shortcode'))['fields']['css_style']['selector'] : '';
                    var paddingPositions = !_.isUndefined( cl.getMapped(this.model.get('shortcode'))['marginPositions'] ) ? cl.getMapped(this.model.get('shortcode'))['marginPositions'] : '';
                    if( paddingSelector != '' && !_.isString(paddingPositions) ){
                        var selector = this.$el.find(paddingSelector),
                            data = {},
                            element = this.$el,
                            height = selector.outerHeight(),
                            that = this;
                        
                        _.each( paddingPositions, function(pos, index){
                            if( element.children('.cl-margin-'+pos).length == 0 )
                                element.append('<div class="cl-modify-margin cl-margin-'+pos+'"></div>');
                            
                            var padding = parseInt(selector.css('margin-'+pos), 10);
                            
                            element.children('.cl-margin-'+pos).attr('data-margin', padding+'px').attr('data-position', pos);
                            if( pos == 'top' || pos == 'bottom' )
                                element.children('.cl-margin-'+pos).css('height','1px');
                        } );

                        var isDragging = false,
                            isMouseDown = false,
                            pos = 0,
                            padd = 0,
                            newPadd = 0;
                            position = 'top',
                            mouseDownOn = null,
                            isShift = false;



                        element.children('.cl-modify-margin').mousedown(function(e) {
                            e.preventDefault();

                            if( ! element.hasClass('cl-selected-element') )
                                return false;

                            if( element.is(':first-child') )
                                return false;

                            if( e.shiftKey ){
                                mouseDownOn = $(this);
                                position = $(this).data('position');
                                $(this).css({opacity: 1});
                                $(this).addClass('change-cursor');

                                pos = (position == 'top' || position == 'bottom') ? e.pageY : e.pageX;

                                padd = parseInt(selector.css('margin-'+position), 10);
                                
                                isMouseDown = true;
                                $(document).mousemove(function(e) {
                                    if( isMouseDown ){
                                        mouseDownOn.css('opactiy', 0);
                                        var diff = ((position == 'top' || position == 'bottom') ? e.pageY : e.pageX) - pos;
                                        if( position == 'right' )
                                            diff = pos - e.pageX;
                                        newPadd = padd + (diff) > 0 ? padd + (diff) : 0;
                                        
                                        selector.css('margin-'+position, newPadd+'px');
                                        

                                        mouseDownOn.attr('data-margin', newPadd+'px');
                                    }else{
                                        if(mouseDownOn != null)
                                            mouseDownOn.css('opactiy', 0);
                                    }      
                                });
                            }
                        });
                        
                        $(document).mouseup(function(e) {  
                            e.preventDefault();        

                            if( element.is(':first-child') )
                                return false;

                            if( ! element.hasClass('cl-selected-element') )
                                return false;      

                            if( isMouseDown ){

                                var params = that.model.get('params');
                                
                                if( _.isUndefined(params['css_style']) )
                                    params['css_style'] = {};

                                params['css_style']['margin-'+position] = newPadd+'px';
                                that.model.save({params:params});
                                cl.updateCustomizer();
                                $(document).off('mousemove');
                                mouseDownOn.css({opacity: 0});
                                posY = 0;
                                padd = 0;
                                newPadd = 0;
                                mouseDownOn.removeClass('change-cursor');
                            }
                            
                            isMouseDown = false;
                            if( mouseDownOn != null )
                                mouseDownOn.removeClass('change-cursor');
                        });

                        element.mouseenter(function(e) {
                            
                            if( element.is(':first-child') )
                                return false;

                            if( ! element.hasClass('cl-selected-element') )
                                return false;

                            if( e.shiftKey )
                                element.addClass('show-all');

                            var mousemove_func = function(e){
                                if( e.shiftKey )
                                    element.addClass('show-all');
                                else
                                    element.removeClass('show-all');
                            };


                            var throttled = _.throttle(mousemove_func, 100);

                            
                            element.mousemove(throttled);

                        }).mouseleave( function(e){
                            if(element.hasClass('show-all'))
                                element.removeClass('show-all');
                            element.off('mousemove');
                            
                        });
                    }


                },


                addPaddingDrag: function(){
                    var paddingSelector = !_.isUndefined( cl.getMapped(this.model.get('shortcode'))['fields']['css_style'] ) ? cl.getMapped(this.model.get('shortcode'))['fields']['css_style']['selector'] : '';
                    var paddingPositions = !_.isUndefined( cl.getMapped(this.model.get('shortcode'))['paddingPositions'] ) ? cl.getMapped(this.model.get('shortcode'))['paddingPositions'] : '';
                    if( paddingSelector != '' && !_.isString(paddingPositions) ){
                        var selector = this.$el.find(paddingSelector),
                            data = {},
                            element = this.$el,
                            height = selector.outerHeight(),
                            that = this;
                            
                        _.each( paddingPositions, function(pos, index){
                            if( element.children('.cl-padding-'+pos).length == 0 )
                                element.append('<div class="cl-modify-padding cl-padding-'+pos+'"></div>');
                            
                            var padding = parseInt(selector.css('padding-'+pos), 10);
                            
                            element.children('.cl-padding-'+pos).attr('data-padd', padding+'px').attr('data-position', pos);
                            if( pos == 'top' || pos == 'bottom' )
                                element.children('.cl-padding-'+pos).css('height', padding+'px');
                            else
                                element.children('.cl-padding-'+pos).css('width', padding+'px');
                        } );

                        var isDragging = false,
                            isMouseDown = false,
                            pos = 0,
                            padd = 0,
                            newPadd = 0;
                            position = 'top',
                            mouseDownOn = null,
                            isShift = false;



                        element.children('.cl-modify-padding').mousedown(function(e) {
                            e.preventDefault();

                            if( ! element.hasClass('cl-selected-element') )
                                return false;

                            if( e.shiftKey ){
                                mouseDownOn = $(this);
                                position = $(this).data('position');
                                $(this).css({opacity: 1});
                                $(this).addClass('change-cursor');

                                pos = (position == 'top' || position == 'bottom') ? e.pageY : e.pageX;

                                padd = parseInt(selector.css('padding-'+position), 10);
                                
                                isMouseDown = true;
                                $(document).mousemove(function(e) {
                                    if( ! element.hasClass('cl-selected-element') )
                                        return false;
                                    if( isMouseDown ){
                                        mouseDownOn.css('opactiy', 0);
                                        var diff = ((position == 'top' || position == 'bottom') ? e.pageY : e.pageX) - pos;
                                        if( position == 'right' )
                                            diff = pos - e.pageX;
                                        newPadd = padd + (diff) > 0 ? padd + (diff) : 0;
                                        
                                        selector.css('padding-'+position, newPadd+'px');
                                        if( position == 'top' || position == 'bottom' )
                                            mouseDownOn.height(newPadd);
                                        else
                                            mouseDownOn.width(newPadd);

                                        mouseDownOn.attr('data-padd', newPadd+'px');
                                    }else{
                                        if(mouseDownOn != null)
                                            mouseDownOn.css('opactiy', 0);
                                    }      
                                });
                            }
                        });
                        
                        $(document).mouseup(function(e) {  
                            e.preventDefault();

                            if( ! element.hasClass('cl-selected-element') )
                                return false;

                            if( isMouseDown ){

                                var params = that.model.get('params');
                                
                                if( _.isUndefined(params['css_style']) )
                                    params['css_style'] = {};
                                
                                params['css_style']['padding-'+position] = newPadd+'px';
                                that.model.save({params:params});
                                cl.updateCustomizer();
                                $(document).off('mousemove');
                                mouseDownOn.css({opacity: 0});
                                posY = 0;
                                padd = 0;
                                newPadd = 0;
                                mouseDownOn.removeClass('change-cursor');
                            }
                            
                            isMouseDown = false;
                            if( mouseDownOn != null )
                                mouseDownOn.removeClass('change-cursor');
                        });

                        element.mouseenter(function(e) {

                            if( ! element.hasClass('cl-selected-element') )
                                return false;
                            
                                if( e.shiftKey )
                                    element.addClass('show-all');

                                element.mousemove(function(e){
                                    if( e.shiftKey )
                                        element.addClass('show-all');
                                    else
                                        element.removeClass('show-all');
                                });

                        }).mouseleave( function(e){
                            if(element.hasClass('show-all'))
                                element.removeClass('show-all');
                            element.off('mousemove');
                            
                        });
                    }


                },
                
                shiftClickOpen: function(e){
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    field = cl.getMapped(this.model.get('shortcode'));
                    if(!_.isUndefined(field['shiftClick']) && _.isArray(field['shiftClick']) && e.shiftKey && !e.ctrlKey){
                        var that = this;
                        _.each(field['shiftClick'], function(key, index){
                            var selector = that.$el.find(key['selector']).first();
                          
                            var is_element = $(e.target).is( selector );
                            if(is_element){
                                wp.customize.preview.send( 'focus-control-for-setting', key['option'] );
                                return false;
                            }
                            
                        });
                        return;
                    }
                    if(e.ctrlKey && e.shiftKey){
                        e.stopPropagation();
                        cl.$page.find('.cl-element-focus').removeClass('cl-element-focus');
                        cl.app.showEditPanel(this.model);
                        this.$el.parents('.cl_element').last().addClass('cl-element-focus');
                    }
                },
                
                
                
                inline_edit: function(){
                    var editor_params, fields = cl.getMapped(this.model.get('shortcode'))['fields'];
                    _.each(fields, function(value, key){
                        if(value.type == 'inline_text'){
                            var selector = value.selector || '';

                            if(selector == '')
                                selector = this.$el;
                            else{
                              
                                if( !_.isUndefined(value.select_from_document) && value.select_from_document){
                                    selector = $(selector);
                                }else
                                    selector = this.$el.find(selector);

                            }

                            if( !_.isUndefined(value.for_tab_title) && value.for_tab_title ){
                                var tab_id = selector.attr('id');
                              
                                selector = selector.parents('.cl_tabs').find('.cl-nav-tabs #'+tab_id+'-tab a span');
                            }
                            
                            if(!_.isUndefined(value.only_text) && value.only_text){
                                editor_params = {
                                    
                                    disableReturn:true,
                                    disableDoubleReturn:true,
                                    disableExtraSpaces:true,
                                    toolbar:false,
                                    anchorPreview: false
                                    
                                };
                            }else{

                                rangy.init();

                                var HighlighterButton = MediumEditor.extensions.button.extend({
                                  name: 'highlighter',

                                  tagNames: ['mark'], // nodeName which indicates the button should be 'active' when isAlreadyApplied() is called
                                  contentDefault: '<i class="cl-builder-icon-paint-brush"></i>', // default innerHTML of the button
                                  contentFA: '<i class="fa fa-paint-brush"></i>', // innerHTML of button when 'fontawesome' is being used
                                  aria: 'Highlight', // used as both aria-label and title attributes
                                  action: 'highlight', // used as the data-action attribute of the button

                                  init: function () {
                                    MediumEditor.extensions.button.prototype.init.call(this);

                                    this.classApplier = rangy.createClassApplier('highlight', {
                                      elementTagName: 'mark',
                                      normalize: true
                                    });
                                  },

                                  handleClick: function (event) {
                                    this.classApplier.toggleSelection();
                                    this.base.checkContentChanged();
                                  }
                                });


                                var DropCaps = MediumEditor.extensions.button.extend({
                                  name: 'dropcaps',

                                  tagNames: ['span'], // nodeName which indicates the button should be 'active' when isAlreadyApplied() is called
                                  contentDefault: '<i class="cl-builder-icon-font"></i>', // Default Dropcaps
                                  //contentFA: '<i class="fa fa-paint-brush"></i>', // innerHTML of button when 'fontawesome' is being used
                                  aria: 'Dropcaps', // used as both aria-label and title attributes
                                  action: 'dropcaps', // used as the data-action attribute of the button

                                  init: function () {
                                    MediumEditor.extensions.button.prototype.init.call(this);

                                    this.classApplier = rangy.createClassApplier('dropcaps', {
                                      elementTagName: 'span',
                                      normalize: true
                                    });
                                  },

                                  handleClick: function (event) {
                                    this.classApplier.toggleSelection();
                                    this.base.checkContentChanged();
                                  }
                                });

                                var BlockQuote = MediumEditor.extensions.button.extend({
                                  name: 'blockquote',

                                  tagNames: ['blockquote'], // nodeName which indicates the button should be 'active' when isAlreadyApplied() is called
                                  contentDefault: '<i class="cl-button-icon-quote-left"></i>', // Default BlockQuote
                                  aria: 'Blockquote', // used as both aria-label and title attributes
                                  action: 'blockquote', // used as the data-action attribute of the button

                                  init: function () {
                                    MediumEditor.extensions.button.prototype.init.call(this);

                                    this.classApplier = rangy.createClassApplier('blockquote', {
                                      elementTagName: 'blockquote',
                                      normalize: true
                                    });
                                  },

                                  handleClick: function (event) {
                                    this.classApplier.toggleSelection();
                                    this.base.checkContentChanged();
                                  }
                                });

                                editor_params = {
                                    toolbar: {
                                        buttons: [
                                            'bold', 
                                            'italic', 
                                            'underline', 
                                            'subscript', 
                                            'superscript', 
                                            'anchor',  
                                            'justifyLeft', 
                                            'justifyCenter', 
                                            'justifyRight', 
                                            'justifyFull', 
                                            'highlighter',
                                            'dropcaps',
                                            'blockquote',
                                            'removeFormat',
                                            
                                            ]
                                    },
                                    extensions: {
                                        'highlighter': new HighlighterButton(),
                                        'dropcaps' : new DropCaps(),
                                        'blockquote' : new BlockQuote()
                                      }
                                };
                            }




                            
                            if(_.isUndefined( selector[0]) )
                                return false;
                            
                            var editor = new MediumEditor(selector[0] , editor_params);
                            var that = this;
                            editor.subscribe('editableInput', function (event, editable) {
                                
                                var params = _.clone(that.model.get('params'));
                               
                                var cloned = $(editable).clone(true);
                                cloned.find('.cl_controls').remove();
                                
                                params[key] = cloned.html();
                                that.model.set('params', params);
                                
                            });
                            
                            editor.subscribe('focus', function(event, editable){
                                $(editable).parents('.cl_element').addClass('cl-focused-text');
                                
                                //that.edit();
                            });
                            editor.subscribe('editableBlur', function(event, editable){
                                $(editable).parents('.cl_element').removeClass('cl-focused-text');
                            });
                            
                            
                            
                            
                        }  
                    }, this);
                },
                
                removeView: function(model) {

                    this.remove();
                    //cl.builder.notifyParent(this.model.get("parent_id"));
                    
                    this.unbind();
                    this.unbind(this.model, "destroy", this.removeView);
                    this.unbind(this.model, "change:params", this.update);
                    this.unbind(this.model, "change:parent_id", this.changeParentId);
                    this.unbind(this.model, "change:order", this.changeOrder);
                    wp.customize.preview.unbind( 'cl_element_updated', this.fieldUpdated );
                    wp.customize.preview.unbind( 'cl_reload_template', this.reloadTemplate );
                },
                
                addControls: function() {
                    var shortcodeTag, $controls_el, allAccess, editAccess, template, parent, data;
                    shortcodeTag = this.model.get("shortcode");
                    $controls_el = $("#cl_controls-" + shortcodeTag);
                    
                    template = $controls_el.length ? $controls_el.html() : $("#cl_controls-default").html();
                    parent = cl.shortcodes.get(this.model.get("parent_id"));
                    
                    data = {
                        name: cl.getMapped(shortcodeTag).label,
                        tag: shortcodeTag,
                        parent_name: parent ? cl.getMapped(parent.get("shortcode")).label : "",
                        parent_tag: parent ? parent.get("shortcode") : ""
                    };
                    
                    _.templateSettings = {
                      interpolate: /\{\{(.+?)\}\}/g
                    };
                    
                    template = _.template(template);
                    this.$controls = $(template(data).trim());
                    
                    this.$controls.appendTo(this.$el);
                    this.$controls_buttons = this.$controls.find("> :first");
                },
                
                getParam: function(param_name) {
                    return _.isObject(this.model.get("params")) && !_.isUndefined(this.model.get("params")[param_name]) ? this.model.get("params")[param_name] : null
                },
                
                placeElement: function($view, activity) {
                    var model = cl.shortcodes.get($view.data("modelId"));
                    
                    
                    if(model){
                        
                        if(activity == 'replace' && cl.$page.find('[data-model-id='+model.get("id")+']').length > 0 ){
                            var toReplace = cl.$page.find('[data-model-id='+model.get("id")+']');
                            toReplace.replaceWith($view);
                           
                            setTimeout(function(){
                                cl.$page.find('[data-model-id='+model.get("id")+']').removeClass('loading');

                            },150);
                            
                            return;
                        }

                        if(model.get('place_after_id')){
                            cl.$page = cl.$page.length ? cl.$page : $('body [data-codeless="true"]');
                            
                            $view.insertAfter(cl.$page.find("[data-model-id=" + model.get("place_after_id") + "]"));
                            model.trigger( 'shortcode:placed_after_id', model );
                            model.unset("place_after_id");

                        }else if(_.isString(activity) && activity == 'prepend')
                            $view.prependTo(this.content());
                        else{
                            $view.appendTo(this.content());
                        }
                    }
                },
                
                content: function() {
                    return !1 === this.$content && (this.$content = this.$el.find("> :first")), this.$content
                },
                
                destroy: function(e) {
                   
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    var answer = confirm("Are you sure to delete this "+this.model.setting("label")+" ?");
                    return !0 !== answer ? !1 : (cl.showMessage(this.model.setting("label") + ' deleted successfully' ), void this.model.destroy(), cl.updateCustomizer() )
                    
                    
                },
                
                inlineEdit_animation: function(field_id, field_value, field){
                    if(_.isUndefined(field['selector']))
                        var $el = this.$el;
                    else 
                        var $el = this.$el.find(field['selector']);
                    
                    _.each(field['choices'], function(choice, index){
                        $el.removeClass(index); 
                    });
                    
                    if(field_value == 'none'){

                        $el.removeClass('animate_on_visible');
                        
                    }else{
           
                        $el.addClass('animate_on_visible '+field_value);
                        if( !_.isUndefined( window['CL_FRONT'] ) )
                            window.CL_FRONT.animations();
                    }
                },

                inlineEdit_loadGoogleApi: function( field_id, field_value ){
                    var api = field_value;
                    CL_FRONT.components.loadDependencies( [ 'maps.googleapis.com/maps/api/js?key=' + api + '' ], function() {
                        
                        CL_FRONT.codelessGMap();
                    
                    });
                },


                
            });
            
            cl.shortcode_container_view = cl.shortcode_view.extend({
                
                events: {
                    
                    "click > .cl_controls .cl_element-name": "edit",  
                    "click > .cl_controls .cl_control-btn-paste-element": "pasteElement", 
                    "click > .add-element-prepend": 'prependElement',
                    "click > .add-element-append": 'appendElement',
                    "click > .cl_controls .cl_control-btn-save": "save",

                    "click": "shiftClickOpen",
                    "contextmenu": "contextMenu"
                    
                },
                


                initialize: function(params){
                    //_.bindAll(this, "holdActive");
                    cl.shortcode_container_view.__super__.initialize.call(this, params);
                    var obj_parent = cl.shortcodes.get(this.model.get("parent_id"));
                    this.parent_view = _.isObject(obj_parent) ? obj_parent.view : null;
                    
                },


                
                pasteElement: function(e){
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    
                    var element = wp.customize('cl-element-clipboard').get();

                    var api = wp.customize;
                    if( !_.isUndefined(parent.wp) )
                        api = parent.wp.customize;

                    element = api('cl-element-clipboard').get()

                   
                        var new_model, builder = new cl.ShortcodesBuilder;
                        
                        if(!this.builder)
                            this.builder = builder;

                        cl.activity = false;
                        new_model = cl.CloneModel(builder, element, this.model.get("id"), false, true);
                        
                        cl.showMessage(new_model.setting("label") + ' pasted with success' );
                        void builder.render();
                        cl.updateCustomizer();
                    
                },

                contextMenu: function(e){
                    e.preventDefault();

                    var $element = $(e.target);
                    if( $element.hasClass('cl_'+this.model.setting('settings')) || $element.closest('.cl_element').hasClass('cl_'+this.model.setting('settings')) ){
                        var self = this;
                        superCm.settings.title = this.model.setting('label');
                        superCm.createMenu([
                            {
                                label: 'Edit',
                                action: function(){ 
                                    self.edit(e);  
                                    superCm.destroyMenu();
                                    setTimeout(function(){
                                        self.$el.addClass('cl-selected-element');
                                    }, 600);
                                },
                            },
                            {
                                label: 'Paste Element',
                                action: function(){ 
                                    self.pasteElement(e);  
                                    superCm.destroyMenu();
                                },
                            },

                            {
                                separator:true
                            },

                            {
                                label: 'Prepend Element',
                                action: function(){ 
                                    self.prependElement(e);  
                                    superCm.destroyMenu();
                                },
                            },

                            {
                                label: 'Append Element',
                                action: function(){ 
                                    self.appendElement(e);  
                                    superCm.destroyMenu();
                                },
                            },
                           
                        ], e);
                    }
                },

                
                content: function() {
                    !1 === this.$content && (this.$content = this.$el.find(".cl_column > .cl_col_wrapper > .col-content"));
                    return this.$content;
                },
                
                
                render: function() {
                    cl.shortcode_container_view.__super__.render.call(this);
                    this.content().addClass("cl_element-container");
                    this.$el.addClass("cl_container-block");
                    return this;
                },
                
                prependElement: function(e){
                    
                    e.preventDefault();
                    
                    cl.addElementDialog.render(this.model, true, {pageX: e.pageX, pageY:e.pageY}); 
                    
                },
                
                appendElement: function(e){
                    e.preventDefault();
                    cl.addElementDialog.render(this.model, false, {pageX: e.pageX, pageY:e.pageY}); 
                },
                
                
            });
            
            
            cl.shortcodeView_cl_row = cl.shortcode_view.extend({
                column_tag: 'cl_column',
                events: {
                      "click > .cl_controls .cl_control-btn-layout": "openLayoutTool",
                      "click > .cl-row": "closeLayoutTool",
                      "click > .cl_controls .cl_col-btn.predefined-col": "setLayout",
                      "click > .cl_controls .cl_col-btn.custom_size": "setCustomLayout",

                      "click > .cl_controls .cl_control-btn-delete": 'destroy',
                      "click > .cl_controls .cl_control-btn-edit": 'edit',
                      "click > .cl_controls .cl_control-btn-clone": "clone",
                      "click > .cl_controls .cl_control-btn-save": 'save',
                      
                      "click": "shiftClickOpen",
                      "click > .cl_controls .cl_element-name": "edit",
                      "contextmenu": "contextMenu"
                      
                },
                
                _builder: false,
                
                /*inlineEdit_cl_row_row_type: function(field_id, field_value){
                    if(field_value == 'container')
                        this.$el.children('.cl-row').children().children().removeClass('container-fluid').addClass('container');
                    else if(field_value == 'container_fluid')
                        this.$el.children('.cl-row').children().children().first().removeClass('container').addClass('container-fluid');
                
                },*/
                
                /*inlineEdit_cl_row_columns_gap: function(field_id, field_value){
                    this.$el.find('.row').children('.cl_cl_column').css('padding-left', field_value).css('padding-right', field_value);
                },*/


                save: function(e){
                    cl.saveElementTemplate.render(this.model, {pageX: e.pageX, pageY:e.pageY});
                },


                
                setCustomLayout: function(e){
                    e.preventDefault();
                    cl.customLayoutDialog.render(this.model, {pageX: e.pageX, pageY:e.pageY}); 
                },
                
                inlineEdit_cl_row_fullheight: function(field_id, field_value){
                    var $el = this.$el.find('.row');
                    if(field_value){
                        
                        _.defer(function(){window.CL_FRONT.fullHeightRow();});
                    }else{
                        _.defer(function(){window.CL_FRONT.fullHeightRow($el);});
                    }
                },

                inlineEdit_InnerColumns: function( field_id, field_value ){
                    if( field_value == '0' )
                        this.$el.find('.row').css({
                            marginLeft: '0px',
                            marginRight: '0px'  
                        });
                    else{
                        this.$el.find('.row').css({
                            marginLeft: '-'+field_value+'px',
                            marginRight: '-'+field_value+'px'  
                        });
                    }
                },
                
                inlineEdit_cl_row_parallax: function(field_id, field_value){
                    var $el = this.$el.find('.cl-row');
                    if(field_value)
                        _.defer(function(){  //window.CL_FRONT.enableParallax($el) 
                        });
                    
                },
                
                inlineEdit_cl_row_background_color: function(field_id, field_value){
                    
                    if(this.$el.find('.cl-row').css('background-image') != 'url()' && this.$el.find('.cl-row').css('background-image') != 'none'  )
                        return;
                    if( _.isNull( field_value ) )
                        return;
                    var rgb_o = hexToRgb(rgbToHex(field_value));

                    if( !_.isObject(rgb_o) || _.isUndefined(rgb_o.r) || _.isUndefined(rgb_o.g) || _.isUndefined(rgb_o.b) )
                        return;   

                },
                
                inlineEdit_cl_row_background_image: function(field, field_value){
                    var that = this;
                    
                },
                
                inlineEdit_cl_row_overlay: function(field, field_value){
                    if(field_value == 'none'){
                        this.$el.find('.cl-row > .overlay').css({display:'none'});
                        if(this.$el.find('.cl-row').css('background-color') != ''){
                            this.inlineEdit_cl_row_background_color(null, this.$el.find('.cl-row').css('background-color'));
                        }
                        
                    }else if(field_value == 'color'){
                        this.$el.find('.cl-row > .overlay').css({backgroundImage: 'none', display: 'block'});
                    }else if(field_value == 'gradient'){
                        this.$el.find('.cl-row > .overlay').css({backgroundImage: '', display: 'block'});
                    }
                },


                
                
                inlineEdit_videoSection: function(field_id, field_value){
                    
                    var fields = this.model.get('params');
                    
                    var data = {};
                    data.video = fields['video'];
                    data.video_mp4 = fields['video_mp4'];
                    data.video_webm = fields['video_webm'];
                    data.video_ogv = fields['video_ogv'];
                    data.video_loop = fields['video_loop'];
                    data.video_youtube = fields['video_youtube'];
                    data.video_vimeo = fields['video_vimeo'];
                    
                    options = {
                        evaluate: /<#([\s\S]+?)#>/g,
                        interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
                        escape: /\{\{([^\}]+?)\}\}(?!\})/g,
                        variable: ''
                    };
                    
                    template = _.template($('#cl_row-video_template').html(), null, options);
                    
                    this.$el.find('.cl-row > .video-section').remove();
                    
                    $(template(data)).insertBefore(this.$el.find('.cl-row > .overlay'));
                    window.CL_FRONT.videoSection();
                    
                },
                
                /*inlineEdit_cl_row_content_pos: function(field_id, field_value){
                    var $el = this.$el.find('.row.cl_row-fullheight');
                    $el.removeClass('cl_row-middle').removeClass('cl_row-top').removeClass('cl_row-bottom').addClass('cl_row-'+field_value);
                },*/
                
                
                
                render: function() {
                    var $content = this.content();
                    this.showControls();

                    var fields = this.model.get('params');

                    var row_text_color = !_.isUndefined( fields['text_color'] ) ? fields['text_color'] : 'dark-text';
                    

                    this.$el.find('> .cl_controls .cl_control-btn-color-text').addClass(row_text_color);


                    cl.shortcodeView_cl_row.__super__.render.call(this);

                    return this;
                },

                contextMenu: function(e){
                    e.preventDefault();

                    var $element = $(e.target);
                    if( $element.hasClass('cl_cl_row') || $element.closest('.cl_element').hasClass('cl_cl_row') ){
                        var self = this;
                        superCm.settings.title = 'Row';
                        superCm.createMenu([
                            {
                                label: 'Edit',
                                action: function(){ 
                                    self.edit(e);  
                                    superCm.destroyMenu();
                                    setTimeout(function(){
                                        self.$el.addClass('cl-selected-element');
                                    }, 600);
                                },
                            },
                            {
                                label: 'Delete',
                                action: function(){ 
                                    self.destroy(e); 
                                    superCm.destroyMenu();
                                },
                            },
                            {
                                label: 'Clone',
                                action: function(){ 
                                    self.clone(e); 
                                    superCm.destroyMenu();
                                },
                            }
                        ], e);
                    }
                },
                
                content: function() {
                    return !1 === this.$content && (this.$content = this.$el.find(".cl_row-sortable")), this.$content
                },
                
                openLayoutTool: function(e){
                    e && e.preventDefault();
                    var $el = this.$el.children('.cl_controls').find('.cl_control-columns').first();
                    
                    
                    if(!$el.hasClass('open')){
                        
                        $el.addClass('open');
                        $el.css({visibility: 'visible'});
                        
                    }else{
                        $el.removeClass('open');
                        $el.css({visibility: '' });
                        
                    }
                },
                
                closeLayoutTool: function(e){
                    e && e.preventDefault();
                    var $el = this.$el.children('.cl_controls').find('.cl_control-columns');
                    $el.removeClass('open');
                    
                    
                    $el.css('visibility', 'hidden');
                    
                },

                
                setLayout: function(e){
                    e && e.preventDefault();
                    this.$el.addClass('loading');
                    var $control = $(e.currentTarget),
                    layout = $control.attr("data-cells");
            
                    var columns = this.convertRowColumns(layout, this.func_builder() );  
                },
                
                func_builder: function() {
                    if( this._builder === false )
                        this._builder = new cl.ShortcodesBuilder;

                    return this._builder;
                },
                
                convertToWidthsArray: function(string) {
                    return _.map(string.split(/_/), function(c) {
                        var w = c.split("");
                        return w.splice(Math.floor(c.length / 2), 0, "/"), w.join("")
                    });
                },
                
                convertRowColumns: function(layout, builder) {
                    if (!layout) return !1;
                    var column_params, new_model, columns_contents = [],
                        columns = this.convertToWidthsArray(layout);
                    cl.layout_change_shortcodes = [];
                    cl.layout_old_columns = cl.shortcodes.where({
                        parent_id: this.model.get("id")
                    });

               


                    _.each(cl.layout_old_columns, function(column) {
                        column.set("deleted", !0);
                        columns_contents.push({
                            shortcodes: cl.shortcodes.where({
                                parent_id: column.get("id")
                            }),
                            params: column.get("params")
                        });
                    });
                    
                    _.each(columns, function(column) {
                        var prev_settings = columns_contents.shift();
                        _.isObject(prev_settings) ? (
                            new_model = builder.create({
                                shortcode: this.column_tag,
                                parent_id: this.model.get("id"),
                                order: cl.shortcodes.nextOrder(),
                                params: _.extend({}, prev_settings.params, {
                                    width: column
                                })
                            }).last(),
                            _.each(prev_settings.shortcodes, function(shortcode) {
                             
                                shortcode.save({
                                    parent_id: new_model.get("id"),
                                    order: cl.shortcodes.nextOrder()
                                }, {
                                    silent: !0
                                });
                                cl.layout_change_shortcodes.push(shortcode)
                            }, this)) : (
                                column_params = {
                                    width: column
                                },
                                //"undefined" != typeof cl.map[this.column_tag] && (column_params = _.extend(column_params, _.mapObject(cl.map[this.column_tag]['fields'], function(val, key){ return val['default']}))),
                                
                                new_model = builder.create({
                                    shortcode: this.column_tag,
                                    parent_id: this.model.get("id"),
                                    order: cl.shortcodes.nextOrder(),
                                    params: column_params
                                }).last()
                            )
                           
                    }, this);
                
                        _.each(columns_contents, function(column) {
                            _.each(column.shortcodes, function(shortcode) {
                                shortcode.save({
                                    parent_id: new_model.get("id"),
                                    order: cl.shortcodes.nextOrder()
                                }, {
                                    silent: !0
                                });
                                cl.layout_change_shortcodes.push(shortcode);
                                //shortcode.view.rowsColumnsConverted && shortcode.view.rowsColumnsConverted()
                            }, this)
                        }, this);

                        
                        builder.render( 
                            function(){ 
                                    _.each(cl.layout_old_columns, function(column) {
                                        column.destroy();
                                    });

                                    _.each(cl.layout_change_shortcodes, function(shortcode) {
                                        
                                        shortcode.trigger("change:parent_id");
                                      
                                    });


                                    
                                    cl.layout_old_columns = [], cl.layout_change_shortcodes = []
                            }
                        );
                      
                        return columns
                },
                
                showControls: function(e){
                    
                    
                    
                }
                
                
            });
            
            
            cl.shortcodeView_cl_row_inner = cl.shortcodeView_cl_row.extend({
                column_tag: 'cl_column_inner',
                events:{
                    //'mouseenter': 'removeParentControls',
                    //'mouseleave': 'showParentsControls',
                    "click > .cl_controls .cl_control-btn-layout": "openLayoutTool",
                    "click > .cl-row_inner": "closeLayoutTool",
                    "click > .cl_controls .cl_col-btn": "setLayout",
                    "click > .cl_controls .cl_control-btn-delete": 'destroy',
                    "click > .cl_controls .cl_control-btn-edit": 'edit',
                    "click > .cl_controls .cl_control-btn-clone": "clone",
                    "click": "shiftClickOpen",
                    "click > .cl_controls .cl_element-name": "edit",
                    "contextmenu": "contextMenu"
                },
                
                removeParentControls: function(e){
                        
                    this.$el.closest('.cl_cl_column').children('.add-element-prepend').css({opacity:0}).css({visibility:'hidden'});
                    this.$el.closest('.cl_cl_column').children('.add-element-append').css({opacity:0}).css({visibility:'hidden'});
                    this.$el.closest('.cl_cl_column').children('.cl_controls').css({opacity:0}).css({visibility:'hidden'});
                    this.$el.closest('.cl_cl_row').children('.cl_controls').css({opacity:0}).css({visibility:'hidden'});
                    
                },
                
                showParentsControls: function(e){
                    
                    this.$el.closest('.cl_cl_column').children('.add-element-prepend').css({visibility:''}).css({opacity:''});
                    this.$el.closest('.cl_cl_column').children('.add-element-append').css({visibility:''}).css({opacity:''});
                    this.$el.closest('.cl_cl_column').children('.cl_controls').css({visibility:''}).css({opacity:''});
                    this.$el.closest('.cl_cl_row').children('.cl_controls').css({visibility:''}).css({opacity:''});
                },
                
                destroy: function(e) {
                    
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    var answer = confirm("Are you sure to delete this "+this.model.setting("label")+" ?");
                    return !0 !== answer ? !1 : (cl.showMessage(this.model.setting("label") + ' deleted successfully' ), void this.model.destroy(), cl.updateCustomizer() )
                    
                    
                },

                contextMenu: function(e){
                    e.preventDefault();

                    var $element = $(e.target);
                    if( $element.hasClass('cl_cl_row_inner') || $element.closest('.cl_element').hasClass('cl_cl_row_inner') ){
                        var self = this;
                        superCm.settings.title = 'Inner Row';
                        superCm.createMenu([
                            {
                                label: 'Edit',
                                action: function(){ 
                                    self.edit(e);  
                                    superCm.destroyMenu();
                                    setTimeout(function(){
                                        self.$el.addClass('cl-selected-element');
                                    }, 600);
                                },
                            },
                            {
                                label: 'Delete',
                                action: function(){ 
                                    self.destroy(e); 
                                    superCm.destroyMenu();
                                },
                            },
                            {
                                label: 'Clone',
                                action: function(){ 
                                    self.clone(e); 
                                    superCm.destroyMenu();
                                },
                            }
                        ], e);
                    }
                },
                
            }); 
            
            
            
            cl.shortcodeView_cl_column = cl.shortcode_container_view.extend({
                
                _x: 0,
                css_width: 12,
                prepend: !1,
                exec: '',

                initialize: function(params) {
                    cl.shortcodeView_cl_column.__super__.initialize.call(this, params);
                    //_.bindAll(this, "startChangeSize", "stopChangeSize", "resize")
                    
                },
                render: function() {
                    cl.shortcodeView_cl_column.__super__.render.call(this);

                    var fields = this.model.get('params');

                    var col_text_color = !_.isUndefined( fields['text_color'] ) ? fields['text_color'] : 'dark-text';

                    this.$el.find('> .cl_controls .cl_control-btn-color-text').addClass(col_text_color);

                    this.prepend = !1;
                    this.setColumnClasses();
                    return this;
                },
                destroy: function(e) {
                    
                    var parent_id = this.model.get("parent_id");
                    cl.shortcodeView_cl_column.__super__.destroy.call(this, e), cl.shortcodes.where({
                        parent_id: parent_id
                    }).length || cl.shortcodes.get(parent_id).destroy()
                },
            
                
                setColumnClasses: function() {
                    var width = this.getParam("width") || "1/1",
                        $content = this.$el.find("> .cl_column, > .cl_column_inner");
                    this.css_class_width = this.convertSize(width).replace(/[^\d]/g, "");
                    $content.removeClass("col-sm-" + this.css_class_width);
                    this.$el.addClass("col-sm-" + this.css_class_width);
                },
                convertSize: function(width) {
                    var prefix = "col-sm-",
                        numbers = width ? width.split("/") : [1, 1],
                        range = _.range(1, 13),
                        num = !_.isUndefined(numbers[0]) && 0 <= _.indexOf(range, parseInt(numbers[0], 10)) ? parseInt(numbers[0], 10) : !1,
                        dev = !_.isUndefined(numbers[1]) && 0 <= _.indexOf(range, parseInt(numbers[1], 10)) ? parseInt(numbers[1], 10) : !1;
                    return !1 !== num && !1 !== dev ? prefix + 12 * num / dev : prefix + "12"
                },

                inlineEdit_cl_column_overlay: function(field, field_value){
                    if(field_value == 'none'){
                        this.$el.find('.cl_column > .cl_col_wrapper > .overlay').css({display:'none'});
                        
                        
                    }else if(field_value == 'color'){
                        this.$el.find('.cl_column > .cl_col_wrapper > .overlay').css({backgroundImage: 'none', display: 'block'});
                    }else if(field_value == 'gradient'){
                        this.$el.find('.cl_column > .cl_col_wrapper > .overlay').css({backgroundImage: '', display: 'block'});
                    }
                },

                save: function(e){
                    cl.saveElementTemplate.render(this.model, {pageX: e.pageX, pageY:e.pageY});
                },
                
                
                
            });
            
            
            cl.shortcodeView_cl_column_inner = cl.shortcodeView_cl_column.extend({

                content: function() {
                    !1 === this.$content && (this.$content = this.$el.find(".cl_column_inner > .wrapper > .col-content"));
                    return this.$content;
                },

                inlineEdit_cl_column_inner_overlay: function(field, field_value){
                    if(field_value == 'none'){
                        this.$el.find('.cl_column_inner > .wrapper > .overlay').css({display:'none'});
                        
                        
                    }else if(field_value == 'color'){
                        this.$el.find('.cl_column_inner > .wrapper > .overlay').css({backgroundImage: 'none', display: 'block'});
                    }else if(field_value == 'gradient'){
                        this.$el.find('.cl_column_inner > .wrapper > .overlay').css({backgroundImage: '', display: 'block'});
                    }
                },
                
                
            });


            cl.shortcodeView_cl_slide = cl.shortcode_container_view.extend({
                
                events: {

                      "click > .cl_controls .cl_control-btn-delete": 'destroy',
                      "click > .cl_controls .cl_control-btn-edit": 'edit',
                      "click > .cl_controls .cl_control-btn-clone": 'clone',
                      "click > .cl_controls .cl_element-name": "edit",
                      
                },

                content: function() {
                    !1 === this.$content && (this.$content = this.$el.find(".cl-slide"));
                    return this.$content;
                },

                render: function() {
                    cl.shortcodeView_cl_slide.__super__.render.call(this);
                    this.content().addClass("cl_element-container");
                    this.$el.addClass("cl_container-block");
                    this.$el.addClass('swiper-slide');
                    this.$el.find('.cl-slide').removeClass('swiper-slide');
                    var slider = cl.shortcodes.get(this.model.get('parent_id'));
                    slider.trigger('slide:added', this.model.get('id'));
                    return this;
                },

                

                destroy: function(){
                    cl.shortcodeView_cl_slide.__super__.destroy.call(this);
                    cl.events.trigger('slide:destroyed', this.model);

                },
                
                
            });


            cl.shortcodeView_cl_service = cl.shortcode_view.extend({
                
                
                
                
            });


            cl.shortcodeView_cl_progress_bar = cl.shortcode_view.extend({

                inlineEdit_progress_percentage: function(){
                    
                    CL_FRONT.progressBar( this.$el.find('.cl_progress_bar') );
                   
                    
                }

            });

            cl.shortcodeView_cl_map = cl.shortcode_view.extend({

                render: function(){
                    cl.shortcodeView_cl_map.__super__.render.call(this);
                    //this.inlineEdit_mapFullHeight();
                 },

                inlineEdit_mapFullHeight: function(){
                    if( this.$el.find('.cl_map').hasClass('cl-map-fullheight') )
                        this.$el.addClass('cl-map-fullheight');
                    else
                        this.$el.removeClass('cl-map-fullheight');
                }
            });






            cl.shortcodeView_cl_toggles = cl.shortcode_container_view.extend({

                events: {

                      "click > .cl_controls .cl_control-btn-delete": 'destroy',
                      "click > .cl_controls .cl_control-btn-edit": 'edit',
                      "click > .cl_controls .cl_control-btn-clone": 'clone',
                      "click > .cl_controls .cl_control-btn-add-toggle": 'addToggle',
                      "click > .cl_controls .cl_element-name": "edit",
                },

                initialize: function() {
                 
                    cl.shortcodeView_cl_toggles.__super__.initialize.call(this);
                    //this.listenTo(this.model, "toggle:added", this.slideAdded);
                    
                },

                content: function() {
                    !1 === this.$content && (this.$content = this.$el.find(".toggles_wrapper"));
                    return this.$content;
                },

                render: function() {
                    cl.shortcodeView_cl_toggles.__super__.render.call(this);
                    
                    //cl.app.setSliderSortable();
                    return this;
                },

                addToggle: function(){
                        
                    if(!this.builder)
                        this.builder = new cl.ShortcodesBuilder;

                    toggle_params = {
                            
                    };
                    

                    this.addingElement();

                    toggle_params = _.extend(toggle_params, cl.getDefaultParams('cl_toggle'));
                    this.builder.create({
                        shortcode: "cl_toggle",
                        parent_id: this.model.get('id'),
                        params: toggle_params
                    });

                    text_params = {
                            
                    };
                            
                    text_params = _.extend(text_params, cl.getDefaultParams('cl_text'));
                    text_params.content = 'Interactively underwhelm turnkey initiatives before high-payoff relationships. Holisticly restore superior interfaces before flexible technology. Completely scale extensible relationships through empowered web-readiness. Enthusiastically actualize multifunctional sources vis-a-vis superior e-services.';
                    this.builder.create({
                        shortcode: "cl_text",
                        parent_id: this.builder.lastID(),
                        params: text_params
                    });

                    this.builder.render();       
                }
            });


            cl.shortcodeView_cl_toggle = cl.shortcode_container_view.extend({
                
                events: {

                    "click > .cl_controls .cl_control-btn-delete": 'destroy',
                    "click > .add-element-prepend": 'prependElement',
                    "click > .add-element-append": 'appendElement',
                    "click > .cl_controls .cl_control-btn-clone": 'clone',
                    "click > .cl_controls .cl_element-name": "edit",
                      
                      
                },

                content: function() {
                    !1 === this.$content && (this.$content = this.$el.find(".toggle_wrapper"));
                    return this.$content;
                },

                render: function() {
                    cl.shortcodeView_cl_toggle.__super__.render.call(this);
                    this.content().addClass("cl_element-container");
                    this.$el.addClass("cl_container-block");
                    return this;
                },

                destroy: function(){
                    cl.shortcodeView_cl_toggle.__super__.destroy.call(this);

                },
                
                
            });




            cl.shortcodeView_cl_tabs = cl.shortcode_container_view.extend({

                events: {

                      "click > .cl_controls .cl_control-btn-delete": 'destroy',
                      "click > .cl_controls .cl_control-btn-edit": 'edit',
                      "click > .cl_controls .cl_control-btn-clone": 'clone',
                      "click > .cl_controls .cl_control-btn-add-tab": 'addTab',
                      "click > .cl_controls .cl_element-name": "edit"

                },

                initialize: function() {
                 
                    cl.shortcodeView_cl_tabs.__super__.initialize.call(this);
                    cl.events.on('tab:destroyed', this.tabDeleted, this);
                    CL_FRONT.codelessTabs();
                    //this.listenTo(this.model, "toggle:added", this.slideAdded);
                    
                },

                tabDeleted: function(tab) {
                    
                    var params = tab.get('params');

                    this.$el.find('.cl-nav-tabs').find('#'+params.tab_id+'-tab').remove();
                    this.$el.find('.cl-nav-tabs li:first-child a').tab('show');
                },


                content: function() {
                    !1 === this.$content && (this.$content = this.$el.find(".tab-content"));
                    return this.$content;
                },

                render: function() {
                    cl.shortcodeView_cl_tabs.__super__.render.call(this);
                     CL_FRONT.codelessTabs(); 
                    //cl.app.setSliderSortable();
                    return this;
                },

                addTab: function(){
                        
                    if(!this.builder)
                        this.builder = new cl.ShortcodesBuilder;

                    tab_params = {
                            
                    };
                    

                    this.addingElement();

                    tab_params = _.extend(tab_params, cl.getDefaultParams('cl_tab'));
                    tab_params.title = 'Tab Title';
                    tab_params.tab_id = 'tabid_' + (this.content().find('.tab-pane').length + 1);


                    this.builder.create({
                        shortcode: "cl_tab",
                        parent_id: this.model.get('id'),
                        params: tab_params
                    });

                    text_params = {
                            
                    };
                            
                    text_params = _.extend(text_params, cl.getDefaultParams('cl_text'));
                    text_params.content = 'Interactively underwhelm turnkey initiatives before high-payoff relationships. Holisticly restore superior interfaces before flexible technology. Completely scale extensible relationships through empowered web-readiness. Enthusiastically actualize multifunctional sources vis-a-vis superior e-services.';
                    this.builder.create({
                        shortcode: "cl_text",
                        parent_id: this.builder.lastID(),
                        params: text_params
                    });

                    this.builder.render();
                    this.$el.find('.cl-nav-tabs').append('<li id="'+tab_params.tab_id+'-tab"><a href="#" data-connect="'+tab_params.tab_id+'"><span>'+tab_params.title+'</span></a></li>');  
                    CL_FRONT.codelessTabs();
                    this.$el.find('.cl-nav-tabs #'+tab_params.tab_id+' a').tab('show');    

                }
            });


            cl.shortcodeView_cl_tab = cl.shortcode_container_view.extend({
                
                events: {

                    "click > .cl_controls .cl_control-btn-delete": 'destroy',
                    "click > .add-element-prepend": 'prependElement',
                    "click > .add-element-append": 'appendElement',
                    "click > .cl_controls .cl_control-btn-clone": 'clone',
                    "click > .cl_controls .cl_element-name": "edit",
                      
                      
                },

                content: function() {
                    !1 === this.$content && (this.$content = this.$el.find(".tab_panel_content"));
                    return this.$content;
                },

                render: function() {
                    cl.shortcodeView_cl_tab.__super__.render.call(this);
                    this.content().addClass("cl_element-container");
                    this.$el.addClass("cl_container-block");
                    this.inline_edit();
                    CL_FRONT.codelessTabs(); 
                    return this;
                },

                destroy: function(){
                    cl.shortcodeView_cl_tab.__super__.destroy.call(this);
                    cl.events.trigger('tab:destroyed', this.model);
                },
                
                
            });




            cl.shortcodeView_cl_list = cl.shortcode_container_view.extend({

                events: {

                      "click > .cl_controls .cl_control-btn-delete": 'destroy',
                      "click > .cl_controls .cl_control-btn-edit": 'edit',
                      "click > .cl_controls .cl_control-btn-clone": 'clone',
                      "click > .cl_controls .cl_control-btn-add-item": 'addItem',
                      "click > .cl_controls .cl_element-name": "edit",
                },

                initialize: function() {
                 
                    cl.shortcodeView_cl_list.__super__.initialize.call(this);
                    //this.listenTo(this.model, "toggle:added", this.slideAdded);
                    
                },

                content: function() {
                    !1 === this.$content && (this.$content = this.$el.find(".list-wrapper"));
                    return this.$content;
                },

                render: function() {
                    cl.shortcodeView_cl_list.__super__.render.call(this);
                    
                    //cl.app.setSliderSortable();
                    return this;
                },

                addItem: function(){
                        
                    if(!this.builder)
                        this.builder = new cl.ShortcodesBuilder;

                    list_item_params = {
                            
                    };

                    table_row_params = {
                            
                    };
                    this.addingElement();

                    if( this.getParam('type') == 'table' ){
                        table_row_params = _.extend(list_item_params, cl.getDefaultParams('cl_table_row'));
                        this.builder.create({
                            shortcode: "cl_table_row",
                            parent_id: this.model.get('id'),
                            params: table_row_params
                        });
                    }else{
                        list_item_params = _.extend(list_item_params, cl.getDefaultParams('cl_list_item'));
                        this.builder.create({
                            shortcode: "cl_list_item",
                            parent_id: this.model.get('id'),
                            params: list_item_params
                        });
                    }

                    

                    
                    

                    this.builder.render();       
                },

                inlineEdit_listType: function(field_id, field_value){
                    if( field_value != 'table' )
                        this.$el.find('ul, ol').replaceWith(function(){
                            return $('<'+field_value+' class="list-wrapper"></'+field_value+'>').append($(this).contents());
                        });
                    else
                        this.reloadTemplate();

                }
            });


            cl.shortcodeView_cl_pricelist = cl.shortcode_container_view.extend({

                events: {

                      "click > .cl_controls .cl_control-btn-delete": 'destroy',
                      "click > .cl_controls .cl_control-btn-edit": 'edit',
                      "click > .cl_controls .cl_control-btn-clone": 'clone',
                      "click > .cl_controls .cl_control-btn-add-item": 'addItem',
                      "click > .cl_controls .cl_element-name": "edit",
                },

                initialize: function() {
                 
                    cl.shortcodeView_cl_pricelist.__super__.initialize.call(this);
                    //this.listenTo(this.model, "toggle:added", this.slideAdded);
                    
                },

                content: function() {
                    !1 === this.$content && (this.$content = this.$el.find(".list-wrapper"));
                    return this.$content;
                },

                render: function() {
                    cl.shortcodeView_cl_pricelist.__super__.render.call(this);
                    
                    //cl.app.setSliderSortable();
                    return this;
                },

                addItem: function(){
                        
                    if(!this.builder)
                        this.builder = new cl.ShortcodesBuilder;

                    list_item_params = {
                            
                    };
                    

                    this.addingElement();

                    list_item_params = _.extend(list_item_params, cl.getDefaultParams('cl_list_item'));
                    this.builder.create({
                        shortcode: "cl_list_item",
                        parent_id: this.model.get('id'),
                        params: list_item_params
                    });

                    this.builder.render();       
                }
            });


            cl.shortcodeView_cl_slider = cl.shortcode_view.extend({
                slideCounter: 0,
                events: {

                      "click > .cl_controls .cl_control-btn-delete": 'destroy',
                      "click > .cl_controls .cl_control-btn-edit": 'edit',

                      "click > .cl_controls .cl_control-btn-clone": 'clone',
                      "click > .cl_control-slides .cl_control-btn-add-slide": 'addSlide',
                      "click > .cl_control-slides .cl-slide-link" : "showSlideEvent",
                      "click > .cl_controls .cl_element-name": "edit",
                },

                initialize: function() {
                 
                    cl.shortcodeView_cl_slider.__super__.initialize.call(this);
                    this.listenTo(this.model, "slide:added", this.slideAdded);
                    cl.events.on('slide:destroyed', this.slideDeleted, this);
                    
                },

                content: function() {
                    !1 === this.$content && (this.$content = this.$el.find(".swiper-wrapper"));
                    return this.$content;
                },

                render: function() {
                    cl.shortcodeView_cl_slider.__super__.render.call(this);
                    
                    cl.app.setSliderSortable();
                    var that = this;

                    _.defer(function(){
                        that.$el.find( '.cl_slider' ).addClass('loading-on-end loading-end');
                        that.$el.find( '.cl_slider .slider-overlay' ).remove();
                        that.$el.find('.cl_cl_slide:first-child').addClass('showSlide');
                    });

                    return this;
                },

                slideDeleted: function(slide){
                    this.$el.find('.cl-slides-container').find('[data-slide-id="'+slide.get('id')+'"]').remove();
                    this.showSlide(this.$el.find('.cl-slides-container > a:first-child').data('slideId'));
                },

                slideAdded: function(slide_id){
                    var model = cl.shortcodes.get(slide_id);
                    this.slideCounter++;
                    this.$el.find('.cl-slides-container').append('<a href="#" class="cl-slide-link cl_control-btn" data-slide-id="'+slide_id+'">'+this.slideCounter+'</a>');
                    this.showSlide(slide_id);
                },

                showSlideEvent: function(e){
                    var slide_id = $(e.currentTarget).attr('data-slide-id');
                    this.showSlide(slide_id);
                },

                showSlide: function(slide_id){
                    this.$el.find('.cl_cl_slide').removeClass('showSlide');
                    this.$el.find('.cl_cl_slide').find('.start_animation').removeClass('start_animation');
                    
                    this.$el.find('#clr_'+slide_id).addClass('showSlide');
                    CL_FRONT.animations( this.$el.find('#clr_'+slide_id), true );
                },

                addSlide: function(){
                        
                    if(!this.builder)
                        this.builder = new cl.ShortcodesBuilder;

                    slide_params = {
                            
                    };
                            
                    slide_params = _.extend(slide_params, cl.getDefaultParams('cl_slide'));
                    this.builder.create({
                        shortcode: "cl_slide",
                        parent_id: this.model.get('id'),
                        params: slide_params
                    });

                    row_params = {
                            
                    };
                            
                    row_params = _.extend(row_params, cl.getDefaultParams('cl_row'));
                    row_params.fullheight = 1;
                    this.builder.create({
                        shortcode: "cl_row",
                        parent_id: this.builder.lastID(),
                        params: row_params
                    });

                    column_params = {
                        width: "1/1"
                    };
                            
                    column_params = _.extend(column_params, cl.getDefaultParams('cl_column'));
                    this.builder.create({
                        shortcode: "cl_column",
                        parent_id: this.builder.lastID(),
                        params: column_params
                    });

                    this.builder.render();       
                }

            });
            
            
            cl.active_dialog = false;
            
            cl.closeActiveDialog = function(model) {
                return _.isUndefined(cl.active_dialog) ? false : cl.active_dialog.hide();
            },
            
            cl.dialogView = Backbone.View.extend({
               
               events:{
                    "click > .close_dialog" : "hide"
               },
               
               initialize: function(){
                   
               },
               
               render: function(){
                    var self= this;
                    $('body').on('click', function(e){

                        if( $( e.target ).parents('.cl_dialog').length == 0 )
                            self.hide(e);
                    });
               },
               
               show: function(){
                   if(!this.$el.hasClass('cl_active_dialog')){
                       cl.closeActiveDialog();
                       cl.active_dialog = this;
                       this.$el.css({ visibility: 'visible'});
                      
                       var that = this;
                       _.defer(function(){that.$el.addClass('cl_active_dialog');});
                       
                       this.$el.focus();
                   }
                   
               },
               
               hide: function(e){
                    _.isObject(e) && e.preventDefault();
                    $('.cl_active_dialog').removeClass('cl_active_dialog').css({ visibility:''});
               },
               
                setPosition: function(coord){
                    var win_width = window.innerWidth;
                    
                    if( coord.pageX + 800 <= win_width )
                        this.$el.css({left:coord.pageX, top:coord.pageY})
                    else{
                        var new_left = win_width - 900 < 0 ? 0 : win_width - 900;
                    }
                        this.$el.css({left: new_left, top:coord.pageY})
                },
 
            });
            
            cl.change_icon_dialog = cl.dialogView.extend({
                events:{
                    'click .icon': 'changedIcon',
                    "click > .close_dialog" : "hide",
                    "input #search" : "searchElements",
                },

                searchElements: function(e){

                    var value = $(e.target).val();
                    var selected = this.$el.find( ".icons-wrapper .icon[data-value*='"+value+"']" );

                    this.$el.find( ".icons-wrapper .icon" ).not(selected).css('display', 'none');
                    selected.css('display', 'block');

                    if( value == '' )
                        this.$el.find( ".icons-wrapper .icon" ).css('display', 'block');
                },
                
                changedIcon: function(e){
                    var new_value = '';

                    if( $( e.target ).is('i') )
                       new_value = $(e.target).parent().data('value');
                    else
                       new_value = $(e.target).data('value');

                    var params = this.model.get('params');
                  
                    params['icon'] = new_value;

                    this.model.save({params:params});
                    if(this.isSVG)
                        this.model.trigger( 'update:svg', new_value.replace('cl-icon-', 'cl-svg-') );
                    else{
                     
                        this.model.trigger( 'update:icon', new_value );
                    }
                },
                
                initialize: function(){
                    cl.change_icon_dialog.__super__.initialize.call(this);
                },
                
                render: function(model, prepend, coordinate, isSVG){
                    this.model = _.isObject(model) ? model : !1;
                    this.isSVG = isSVG;
                    cl.active_dialog = this;
                    this.setPosition(coordinate); 
                    this.show();
                    return cl.change_icon_dialog.__super__.render.call(this);
                },
            });
            
            cl.add_element_dialog = cl.dialogView.extend({
                events:{
                    "click > .wrapper .elements_ .element .from_scratch" : "createFromScratch",
                    "click > .wrapper .elements_ .element" : "createElement",
                    "click > .wrapper .elements_ .element .predefined_list" : "showList",
                    "click > .go_back" : "goBack",
                    "click > .close_dialog" : "hide",
                    "input > .wrapper #search" : "searchElements",
                    "click > .wrapper .pre_element" : "createPredefined",
                    "click > .wrapper .tabs a": 'changeTabs',
                    "click > .wrapper .content-block": "addContentBlock"
                },
                
                
                
                initialize: function(){

                    cl.add_element_dialog.__super__.initialize.call(this);
                },
                
                render: function(model, prepend, coordinate){
                    
                    _.isUndefined(cl.ShortcodesBuilder) || (this.builder = new cl.ShortcodesBuilder);
                    
                    this.prepend = _.isBoolean(prepend) ? prepend : false;
                    this.model = _.isObject(model) ? model : !1;
                    
                    cl.active_dialog = this;
                    this.setPosition(coordinate); 
                    this.$el.find('.lazy').lazyload({
                        event : "load_img"
                    });

                    if( this.model === false )
                        this.$el.removeClass('container_adding').addClass('root_adding');
                    else
                        this.$el.removeClass('root_adding').addClass('container_adding');

                    this.show();
                    return cl.add_element_dialog.__super__.render.call(this);
                },

                changeTabs: function(e){
                    _.isObject(e) && e.preventDefault();
                    var id = $(e.currentTarget).data('tab');
                    this.$el.find('.tabs a').removeClass('actived')
                    this.$el.find('.tab-content').removeClass('actived');

                    $(e.currentTarget).addClass('actived');
                    this.$el.find('#tab-'+id).addClass('actived');
                    this.contentBlockUI(this.$el.find('#tab-'+id));
                },

                contentBlockUI: function(element){
                    if( element.attr('id') != 'tab-content_blocks' )
                        return;
                    element.find('.content-block').each(function(){
                        var src = $(this).find('img').data('src');
                        $(this).find('img').attr('src', src);
                    });
                },

                goBack: function(e){
                    _.isObject(e) && e.preventDefault();
                    var actived = this.$el.find('.content-page.actived');
                    var elements_ = this.$el.find('.content-page.elements_' );
                    actived.removeClass('actived');
                    
                    setTimeout(function(){
                        actived.css('display', 'none');
                        actived.find('.predefined_container').css('display', 'none');
                        elements_.css('display', 'block');
                    }, 150);
    
                    
                    setTimeout(function(){
                        elements_.addClass('actived');
                    }, 300);
                    this.$el.find('.go_back').css('opacity', 0);
                },

                searchElements: function(e){

                    var value = $(e.target).val();
                    var selected = this.$el.find( ".elements_ .element[data-tag*='"+value+"']" );

                    this.$el.find( ".elements_ .element" ).not(selected).css('display', 'none');
                    selected.css('display', 'block');

                    if( value == '' )
                        this.$el.find( ".elements_ .element" ).css('display', 'block');
                },

                showList: function(e){
                    _.isObject(e) && e.preventDefault();
                    var linked_tag = $(e.target).data('linked-tag');
                    var actived = this.$el.find('.content-page.actived');

                    actived.removeClass('actived');
                    setTimeout(function(){
                        actived.css('display', 'none');
                    }, 150);
            
                    var predefined = this.$el.find('.content-page.predefined' );
                    var container = predefined.find('.predefined_container[data-tag="'+linked_tag+'"]');
                    container.find('img').trigger('load_img');
                    container.css('display', 'block');
                    predefined.css('display', 'block');
                    setTimeout(function(){
                        predefined.addClass('actived');
                    }, 150);

                    this.$el.find('.go_back').css('opacity', 1);
                    
                },

                createElement: function(e){
                    _.isObject(e) && e.preventDefault();
                    
                    if( $(e.target).is('.from_scratch') || $(e.target).parents('.from_scratch').length > 0 )
                        return;

                    if( ! this.model )
                        cl.$page.addClass('loading');

                    this.createFromScratch(e);
                },

                createPredefined: function(e){
                    cl.activity = '';
                    var element = $(e.currentTarget),
                        tag = element.parent('.predefined_container').data('tag'),
                        pre_id = element.attr('id'),
                        data = cl.getPredefinedList(tag, pre_id),
                        shortcode = (_.isObject(data) ) ?  JSON.parse(decodeURIComponent(data.content + "")) : {};

                    parent_id = this.model ? this.model.get("id") : false;
                    if( parent_id ){
                        parent = cl.shortcodes.get(parent_id);
                        if( parent && parent.view )
                            parent.view.addingElement();
                    }

                    params = {
                        shortcode: tag,
                        params: shortcode['attrs'],
                        parent_id: this.model ? this.model.get("id") : !1
                    };
                        
                    "cl_row" === tag ? params.params = row_params : "cl_row_inner" === tag && (params.params = row_inner_params);
                        
                    this.prepend ? (params.order = 0, shortcodeFirst = cl.shortcodes.findWhere({
                        parent_id: this.model ? this.model.get("id") : !1
                    }), shortcodeFirst && (params.order = shortcodeFirst.get("order") - 1), cl.activity = "prepend") : this.place_after_id && (params.place_after_id = this.place_after_id);
                        
                    this.builder.create(params);
                    this.hide();
                    this.model = this.builder.last();
                    cl.app.showEditPanel(this.model);
                    this.builder.render();
                    cl.updateCustomizer();
                    
                },

                addContentBlock: function(e){
                    _.isObject(e) && e.preventDefault();

                    var element = $(e.currentTarget),
                        id = element.data('id'),
                        data = cl.getContentBlock(id),
                        place_after = 0,
                        prepend_process = false,
                        replace_column = element.parent().find( '#replace_column' ).is(':checked'),
                        old_column_id = false,
                        addingType = element.closest('.content_block').hasClass('type_cl_column') ? 'cl_column' : 'cl_row',
                        shortcodes = (_.isObject(data) ) ?  data.content: {};


                    parent_id = this.model ? this.model.get("id") : false;
                    if( parent_id ){
                        parent = cl.shortcodes.get(parent_id);
                        if( parent && parent.view )
                            parent.view.addingElement();
                    }

                    row_inner_params = {};
                    row_inner_params = _.extend(row_inner_params, cl.getDefaultParams('cl_row_inner'))
                        
                    row_params = {};
                    row_params = _.extend(row_params, cl.getDefaultParams('cl_row'))


                    /*if( this.prepend ){
                        prepend_process = true;
                        _.each( cl.shortcodes.where({
                            parent_id: this.model ? this.model.get("id") : !1
                        }), function(model){

                            model.save({
                                order: model.get('order') + shortcodes.length
                            }, {
                                silent: !0
                            });

                        });    
                       
                    }*/
                    var id_changed = [];
                    _.each(shortcodes, function(data, index) {

                        var shortcode = (_.isObject(data) ) ? data : JSON.parse(decodeURIComponent(data + ""));
                 

                        var tag = shortcode['tag'];

                        if( addingType == 'cl_column' ){
                            if( this.model.get('shortcode') == 'cl_column' && tag == 'cl_column' ){
                                old_column_id = shortcode['id'];
                                if( replace_column  ){

                                    
                                    
                                    var current_col_params = this.model.get('params');
                                    var column_params = {};
                                    _.each( shortcode['attrs'], function( value, key ){
                                        if( key != 'width' )
                                            column_params[key] = value;

                                    });

                                    column_params['width'] = current_col_params['width'];

                                

                                    this.model.set({params: column_params});
                                    

                                    var inner_models = cl.shortcodes.where({
                                            parent_id: this.model ? this.model.get("id") : !1
                                    });
                                 
                                    
                                    _.each( inner_models, function( model ){
                                        model.destroy();

                                    }, this);

                                    this.model.view.reloadTemplate([this.model.get('id')]);
                                    cl.events.trigger('shortcodes:add', this.model);
                                }
                             
                                return true;
                            }
                        }
                        
                        if( this.prepend && index == 0 )
                            cl.activity = 'prepend';
                        else
                            cl.activity = false;

                        
                        if( shortcode['parent_id'] === false && index > 0 )
                            this.place_after_id = this.builder.lastID();
                        
                        var is_root = !_.isUndefined( cl.getMapped(tag)['is_root'] ) ? cl.getMapped(tag)['is_root'] : false;

                        if(false === this.model && ! is_root && index == 0 ){
                            column_params = {
                                width: "1/1"
                            };
                                
                            var row_order = 0;
                            var row_place_after_id = false;
                            column_params = _.extend(column_params, cl.getDefaultParams('cl_column'));
                            
                            this.prepend ? (row_order = 0, shortcodeFirst = cl.shortcodes.findWhere({
                                parent_id: this.model ? this.model.get("id") : !1
                            }), shortcodeFirst && (row_order = shortcodeFirst.get("order") - 1), cl.activity = "prepend") : this.place_after_id && (row_place_after_id = this.place_after_id);
                            
                            var obj_to_create = {
                                shortcode: "cl_row",
                                params: row_params,
                                place_after_id: false
                            };
                           
                            if( this.prepend )
                                obj_to_create['order'] = row_order;


                            this.builder.create(obj_to_create).create({
                                shortcode: "cl_column",
                                parent_id: this.builder.lastID(),
                                params: column_params
                            });
                                
                            this.model = this.builder.last()
                        }else if(false !== this.model && "cl_row" === tag && index == 0)
                            tag += "_inner";

                        var new_id = cl_guid();
                        id_changed.push({
                            'old_id' : shortcode['id'],
                            'new_id' : new_id
                        })
                        
                        var params = {
                                id: new_id,
                                shortcode: tag,
                                params: shortcode['attrs'],
                        };
                        

                        if( addingType == 'cl_column' && shortcode['parent_id'] == old_column_id ){
                           
                            params.parent_id = this.model.get('id');
                            if( replace_column )
                                this.prepend = false;
                        }

                        else if( shortcode['parent_id'] !== false ){
                            params.parent_id = shortcode['parent_id'];
                           
                            for( var i=0; i < id_changed.length; i++ ){
                                if( id_changed[i].old_id == shortcode['parent_id'] )
                                    params.parent_id = id_changed[i].new_id;
                            }

                            
                        }
                        
                       
                        this.prepend ? (params.order = 0, shortcodeFirst = cl.shortcodes.findWhere({
                            parent_id: this.model ? this.model.get("id") : !1
                        }), shortcodeFirst  && (params.order = shortcodeFirst.get("order") - 1), cl.activity = "prepend") : this.place_after_id && (params.place_after_id = this.place_after_id);

                        if( is_root && index == 0 )
                            params.activity = 'prepend';

                        this.builder.create(params);
                        this.place_after_id = false;
                        
                        

                    }, this);
                    var that = this;
                    setTimeout(function(){
                        that.builder.render();
                        $('body [data-codeless="true"]').addClass('loading');
                    }, 200);
                    

                    this.hide();
                    cl.updateCustomizer();
                },

                createFromScratch: function(e){
                    cl.activity = '';
                    _.isObject(e) && e.preventDefault();
                    
                    if( $(e.target).is('.predefined_list') )
                        return;

                    var showSettings, 
                        params, 
                        shortcodeFirst, 
                        newData, 
                        i, 
                        shortcode, 
                        column_params, 
                        row_params, 
                        row_inner_params, 
                        $control = $(e.currentTarget);

                    if( ! $control.is('.element') )
                        $control = $control.parents('.element').first();

                    parent_id = this.model ? this.model.get("id") : false;
                    if( parent_id ){
                        parent = cl.shortcodes.get(parent_id);
                        if( parent && parent.view )
                            parent.view.addingElement();
                    }


                    var tag = $control.data("tag");
                        
                    row_inner_params = {};
                    row_inner_params = _.extend(row_inner_params, cl.getDefaultParams('cl_row_inner'))
                        
                    row_params = {};
                    //row_params = _.extend(row_params, cl.getDefaultParams('cl_row'))
                    



                    var is_root = !_.isUndefined( cl.getMapped(tag)['is_root'] ) ? cl.getMapped(tag)['is_root'] : false;

                    if(false === this.model && ! is_root ){
                        column_params = {
                            width: "1/1"
                        };
                            
                        var row_order = 0;
                        var row_place_after_id = false;
                        //column_params = _.extend(column_params, cl.getDefaultParams('cl_column'));
                        
                        this.prepend ? (row_order = 0, shortcodeFirst = cl.shortcodes.findWhere({
                            parent_id: this.model ? this.model.get("id") : !1
                        }), shortcodeFirst && (row_order = shortcodeFirst.get("order") - 1), cl.activity = "prepend") : this.place_after_id && (row_place_after_id = this.place_after_id);
                        
                        var obj_to_create = {
                            shortcode: "cl_row",
                            params: row_params,
                            place_after_id: false
                        };

                        if( this.prepend )
                            obj_to_create['order'] = row_order;


                        this.builder.create(obj_to_create).create({
                            shortcode: "cl_column",
                            parent_id: this.builder.lastID(),
                            params: column_params
                        });
                            
                        this.model = this.builder.last()
                    }else if(false !== this.model && "cl_row" === tag)
                        tag += "_inner";
                        
                        
                    params = {
                        shortcode: tag,
                        parent_id: this.model ? this.model.get("id") : !1
                    };
                        
                    "cl_row" === tag ? params.params = row_params : "cl_row_inner" === tag && (params.params = row_inner_params);
                        
                    this.prepend ? (params.order = 0, shortcodeFirst = cl.shortcodes.findWhere({
                        parent_id: this.model ? this.model.get("id") : !1
                    }), shortcodeFirst && (params.order = shortcodeFirst.get("order") - 1), cl.activity = "prepend") : this.place_after_id && (params.place_after_id = this.place_after_id);

                    this.builder.create(params);                

                    for (i = this.builder.models.length - 1; i >= 0; i--){
                        
                        shortcode = this.builder.models[i].get("shortcode");
                        var default_params = cl.getDefaultParams(shortcode);
                        
                        if(!_.isEmpty(default_params))
                            this.builder.models[i].attributes.params = _.extend(this.builder.models[i].attributes.params, default_params);
                    } 
                    
                    if("cl_row" === tag){
                        
                        column_params = {
                            width: "1/1"
                        };
                            
                        column_params = _.extend(column_params, cl.getDefaultParams('cl_column'));
                        this.builder.create({
                            shortcode: "cl_column",
                            parent_id: this.builder.lastID(),
                            params: column_params
                        })
                    }else if("cl_row_inner" === tag){
                        column_params = {
                            width: "1/1"
                        };
                            
                        column_params = _.extend(column_params, cl.getDefaultParams('cl_column_inner'));
                        this.builder.create({
                            shortcode: "cl_column_inner",
                            parent_id: this.builder.lastID(),
                            params: column_params
                        });
                    }else if("cl_slider" === tag){
                        slide_params = {
                            
                        };
                            
                        slide_params = _.extend(slide_params, cl.getDefaultParams('cl_slide'));
                        this.builder.create({
                            shortcode: "cl_slide",
                            parent_id: this.builder.lastID(),
                            params: slide_params
                        });

                            
                        row_params = _.extend(row_params, cl.getDefaultParams('cl_row'));
                        row_params.fullheight = 1;
                        this.builder.create({
                            shortcode: "cl_row",
                            parent_id: this.builder.lastID(),
                            params: row_params
                        });

                        column_params = {
                            width: "1/1"
                        };
                            
                        column_params = _.extend(column_params, cl.getDefaultParams('cl_column'));
                        this.builder.create({
                            shortcode: "cl_column",
                            parent_id: this.builder.lastID(),
                            params: column_params
                        })
                    } 
                        
                    if(_.isString(cl.getMapped(tag).default_content) && cl.getMapped(tag).default_content.length){
                        
                        newData = this.builder.parse({}, cl.getMapped(tag).default_content, this.builder.last().toJSON());
                        _.each(newData, function(object) {
                            object.default_content = !0, this.builder.create(object)
                        }, this);
                            
                    }
                        
                    this.model = this.builder.last();
                    this.hide();
                    if(this.model.get('shortcode') != 'cl_column_inner' && this.model.get('shortcode') != 'cl_column' )
                        cl.app.showEditPanel(this.model);
                    else if( this.model.get('shortcode') == 'cl_column' ){
                        var parent_row_id = this.model.get('parent_id'),
                        parent_row_model = cl.shortcodes.get(parent_row_id);
                        cl.app.showEditPanel(parent_row_model);
                    }

                    this.builder.render();
                    cl.updateCustomizer();

                    
                }
 
                
            });
            
            cl.custom_layout_dialog = cl.dialogView.extend({
                events:{
                    'click #submit': 'change'
                },

                change: function(e){
                    e && e.preventDefault();
                    var $control = this.$el.find('#custom_layout');
                    var layout = $control.val();
                    if( layout != '' ){
                        layout = layout.replace(/\//g, '').replace(/\+/gi, '_').replace(/ /gi, '');
                   
                        columns = this.model.view.convertRowColumns(layout, this.model.view.builder);  
                        this.hide();
                    }
                    
                },
                
                initialize: function(){
                    cl.change_icon_dialog.__super__.initialize.call(this);
                },
                
                render: function(model, coordinate){
                    this.model = _.isObject(model) ? model : !1;
                    cl.active_dialog = this;
                    this.setPosition(coordinate); 
                    this.show();
                    return cl.change_icon_dialog.__super__.render.call(this);
                },
            });

            cl.save_element_template_dialog = cl.dialogView.extend({
                events:{
                    'click #submit': 'save',
                    "click > .close_dialog" : "hide",
                },

                save: function(e){
                    _.isUndefined(cl.ShortcodesBuilder) || (this.builder = new cl.ShortcodesBuilder);

                    var shortcodeString = cl.shortcodes.createShortcodeString(this.model);
                    var dataToSend = {};
                    dataToSend.content = shortcodeString;
                    dataToSend.type = this.model.get('shortcode');
                    dataToSend.key = this.$el.find( '#template_key' ).val();
                    dataToSend.name = this.$el.find( '#template_name' ).val();
                    
                    if( shortcodeString != '' )
                        this.builder.ajax({
                            action: "cl_save_template",
                            nonce: scriptData.ajax_nonce,
                            data: dataToSend
                        }, scriptData.ajax_url).done(function(html) {

                        });
    
                    window.parent.wp.customize.previewer.refresh();

                    cl.showMessage(this.model.setting("label") + ' saved successfully' );
                    this.hide();
                    
                },
                
                initialize: function(){
                    cl.save_element_template_dialog.__super__.initialize.call(this);
                },
                
                render: function(model, coordinate){
                    this.model = _.isObject(model) ? model : !1;
                    cl.active_dialog = this;
                    this.setPosition(coordinate); 
                    this.show();
                    return cl.save_element_template_dialog.__super__.render.call(this);
                },
            });


            cl.shortcodes = new Shortcodes;
            
            
            
            
}(window.jQuery, wp.customize));


// cl-shortcodes-builder.js
(function($){
    
    
    cl.ShortcodesBuilder = function(models){
        this.models = models || [];
        this.is_build_complete = true;
        
        return this;
    };
    
    cl.ShortcodesBuilder.prototype = {
        
       
        create: function(attributes) {
            this.is_build_complete = false;
            this.models.push(cl.shortcodes.create(attributes));
            return this;
        },
        render: function(callback, activity) {
            var shortcodes;

            shortcodes = _.map(this.models, function(model) {
                var string = this.toString(model);
                
                return {
                    id: model.get("id"),
                    string: string,
                    tag: model.get("shortcode")
                }
            }, this);

            this.build(shortcodes, callback, activity);

            
        },
        
        notifyParent: function(parent_id) {
            var parent = cl.shortcodes.get(parent_id);
            parent && parent.view && parent.view.changed()
        },
        
        build: function(shortcodes, callback, activity) {
            
            var _ajaxPrefilter = $.ajaxPrefilter;

            $.ajaxPrefilter(function( options, originalOptions, jqXHR ) {

              if( options.dataType == 'html' && options.type == 'POST' && options.data.indexOf('action=cl_load_shortcode') !== -1 )
                options.url = cl.ajaxHandler;
            });
        
            var obj_data = {
                
                action: "cl_load_shortcode",
                nonce: cl.ajaxNonce,
                shortcodes: shortcodes,
                clactive: 1,
                wp_customize: 'on',
                customize_changeset_uuid: wp.customize.settings.changeset.uuid
            
            }

            this.ajax(obj_data, cl.loadedUrl).done(function(html) {
                
                
                $.ajaxPrefilter = _ajaxPrefilter;

                _.each($(html), function(block) {
                    this.renderBlock(block, activity);
                   
                }, this);
                
                _.isFunction(callback) && callback(html);
                cl.app.setSortable();
                
                if( cl.activity != 'replace' && cl.activity != 'prepend' )
                    cl.activity = false;
                //cl.app.setResizable();
                
                this.models = [];
                //this.showResultMessage();
                this.is_build_complete = true;

            })
        },
        
        ajax: function(data, url) {
            

            

            return this._ajax = $.ajax({
                url: url || window.parent.ajaxurl,
                type: "POST",
                dataType: "html",
                data: _.extend({

                }, data),
                
                context: this
            })
        },
        
        lastID: function() {
            return this.models.length ? _.last(this.models).get("id") : ""
        },
        
        last: function() {
            return this.models.length ? _.last(this.models) : !1
        },
        
        firstID: function() {
            return this.models.length ? _.first(this.models).get("id") : ""
        },
            
        first: function() {
            return this.models.length ? _.first(this.models) : !1
        },
        
        toString: function(model, type) {
            var paramsForString, params, content, mergedParams, tag;
            
            paramsForString = {};
            tag = model.get("shortcode");
            params = _.extend({}, model.get("params"));
            mergedParams = params;
            content = _.isString(params.content) ? params.content : "";
            _.each(mergedParams, function(value, key) {
                paramsForString[key] = this.escapeParam(value)
            }, this),
            content = _.isString(params.content) ? params.content : "";
            
            return wp.shortcode.string({
                tag: tag,
                attrs: paramsForString,
                content: content,
                type: _.isString(type) ? type : ""
            });
        },
        
        escapeParam: function(value) {
            if(_.isObject(value)){
                value = cl.objectToString(value);
                return value;
            }else
                return _.isUndefined(value) || _.isNull(value) || !value.toString ? "" : value.toString().replace(/"/g, "``").replace(/\[/g, "`{`").replace(/\]/g, "`}`")
        },
        
        renderBlock: function(block, activity) {
            
            var $html, model, $this = $(block);
            model = cl.shortcodes.get($this.data("modelId"));
            $html = $this;

            (model && model.get("shortcode") && this.renderShortcode($html, model, activity));
        },
        
        buildFromArray: function(){
            var $this = this;
            var url = window.parent.loadedUrl;
            var api = parent.wp.customize;
            var data_;
            
            /*if(!_.isUndefined(api('cl_page_content['+url+']')))
                data_ = api('cl_page_content['+url+']').get();
                
            else*/
                data_ = cl.post_shortcodes
            
   
            //var data_customizer = parent.wp.customize('cl_page_content['+window.parent.loadedUrl+']').get();
            //
            
            /*if(!_.isUndefined(data_customizer) && !_.isEmpty(data_customizer)){
                
            }*/
            
            
            _.each(data_, function(data) {
                
                var shortcode = (_.isObject(data) ) ? data : JSON.parse(decodeURIComponent(data + ""));
                var $block = $('body').find("[data-model-id=" + shortcode.id + "]");
                var params = ($block.parents("[data-model-id]"), _.isObject(shortcode.attrs)) ? shortcode.attrs : (_.isObject(shortcode.params) ? shortcode.params : {} );
                
                var model = cl.shortcodes.create({
                        
                        id: shortcode.id,
                        shortcode: shortcode.tag,
                        params: params,
                        parent_id: shortcode.parent_id,
                        from_content: true
                    }, {  silent: !0 }
                    
                );
                
                $block.attr("data-model-id", model.get("id"));
                $this.renderBlock($block);
            });
            
            cl.app.setSortable();
            //cl.app.setResizable();
        },
        
        
        
        renderShortcode: function($html, model, activity) {
            var view_name;

            view_name = this.getView(model);
   
            inner_html = $html;
            cl.last_inner = inner_html.html();
            
            ((!model.get("from_content") && !model.get("from_template")) || cl.activity == 'replace' || ( _.isString(activity) && activity == 'replace' ) ) && this.placeContainer($html, model, activity)
            
            if( cl.activity == 'replace' || ( _.isString(activity) && activity == 'replace' ) ){
                if( ! _.isUndefined( model.view ) )
                    model.view.removeView();
                $html.removeClass('loading');
            }

            model.view = new view_name({
                model: model,
                el: $html
            }).render();

            $('body [data-codeless="true"]').removeClass('loading');
            
            this.notifyParent(model.get("parent_id"));
            //model.view.rendered();
            
        },
        getView: function(model) {
            var view = model.setting("is_container") || model.setting("as_parent") ? cl.shortcode_container_view : cl.shortcode_view;
            _.isObject(cl["shortcodeView_" + model.get("shortcode")]) && (view = cl["shortcodeView_" + model.get("shortcode")]);

            return view;
            
        },
        
        last: function() {
            return this.models.length ? _.last(this.models) : !1
        },
        
        _getContainer: function(model) {
            var container, parent_model, parent_id = model.get("parent_id");
            
            if (!1 !== parent_id) {
                if (parent_model = cl.shortcodes.get(parent_id), _.isUndefined(parent_model)) return cl.app;
                container = parent_model.view
                
            } else 
                container = cl.app;
            return container
        },
        
        placeContainer: function($html, model, activity) {
            var container = this._getContainer(model);
            activity = _.isString(activity) && activity == 'replace' ? activity : cl.activity;
            if(activity == 'replace' )
                $html.addClass('loading');
            return container && container.placeElement($html, activity), container
        },
        
        getContent: function() {
            var models = _.sortBy(cl.shortcodes.where({
                parent_id: !1
            }), function(model) {

                return model.get("order")
            });
            return cl.shortcodes.modelsToString(models)
        }
        
    };
    
    cl.builder = new cl.ShortcodesBuilder();
    
    
    
}(window.jQuery));


/* cl-header-builder.js */

(function($, api) {
        
        cl.getHeaderMapped = cl.memoize(function(tag) {
            
            return cl.headerMap[tag] || {}
        });
        
        cl.getHeaderParamSettings = cl.memoize(function(tag, paramName) {
            var params, paramSettings;
            params = _.isObject(cl.getHeaderMapped(tag).fields) ? cl.getHeaderMapped(tag).fields : [];
            
            return paramSettings = _.find(params, function(settings, name) {
                
                return _.isObject(settings) && name === paramName
            }, this)
        });
        
        cl.getHeaderDefaultParams = cl.memoize(function(tag) {
            var params, default_params = {};
            params = _.isObject(cl.getHeaderMapped(tag).fields) ? cl.getHeaderMapped(tag).fields : [];
            
            _.each(params, function(param, index){
                default_params[index] = param['default'];
            });
            
            return default_params;
        });
        
        
        
        var HeaderElement = Backbone.Model.extend({
                defaults: function() {
                    var id = cl_guid();
                    return {
                        id: id,
                        type: "logo",
                        order: cl.header_elements.nextOrder('main', 'left'),
                        params: {},
                        row: 'main',
                        col: 'left'
                    }
                },
                settings: !1,
                getParam: function(key) {
                    return _.isObject(this.get("params")) && !_.isUndefined(this.get("params")[key]) ? this.get("params")[key] : ""
                },
                sync: function() {
                    return !1
                },
                setting: function(name) {
                    return !1 === this.settings && (this.settings = cl.getHeaderMapped(this.get("type")) || {}), this.settings[name]
                },
                view: !1
            }),
            HeaderElements = Backbone.Collection.extend({
                model: HeaderElement,
                sync: function() {
                    return !1
                },
                nextOrder: function(row, col) {
                    var models = cl.header_elements.where({
                        row: row,
                        col: col
                    });
                    return models.length ? _.last(models).get("order") + 1 : 1
                },
                initialize: function() {

                },
                comparator: function(model) {
                    return model.get("order")
                },
                removeEvents: function(model) {
                },
                removeChildren: function(parent) {
                    var models = cl.shortcodes.where({
                        parent_id: parent.id
                    });
                    _.each(models, function(model) {
                        model.destroy()
                    }, this)
                },
                stringify: function(state) {
                    var models = _.sortBy(cl.shortcodes.where({
                        parent_id: !1
                    }), function(model) {
                        return model.get("order")
                    });
                    return this.modelsToString(models, state)
                },
                createShortcodeString: function(model, state) {
                    var mapped, data, tag, params, content, paramsForString = {}, mergedParams, isContainer;
                    tag = model.get("shortcode"); 
                    params = _.extend({}, model.get("params"));
                    
                    _.each(params, function(value, key) {
                        if(key != 'content'){
                            if(_.isObject(value)){
                                
                                value = cl.objectToString(value);
                                
                            }    
                            
                            paramsForString[key] = value;
                            
                        }
                    });
                        
                    mapped = cl.getMapped(tag);
                    isContainer = _.isObject(mapped) && (_.isBoolean(mapped.is_container) && !0 === mapped.is_container || !_.isEmpty(mapped.as_parent));
                        
                    content = this._getShortcodeContent(model);
                    data = {
                        tag: tag,
                        attrs: paramsForString,
                        content: content,
                        type: _.isUndefined(cl.getParamSettings(tag, "content")) && !isContainer ? "single" : ""
                    };
                    
                        
                        //_.isUndefined(state) ? model.trigger("stringify", model, data) : model.trigger("stringify:" + state, model, data);
                    return wp.shortcode.string(data)
                    
                },
            
                
                modelsToString: function(models) {
                    var string = _.reduce(models, function(memo, model) {
                        return memo + this.createShortcodeString(model)
                    }, "", this);
                    return string
                },
                _getShortcodeContent: function(parent) {
                    var models, params;
                    return models = _.sortBy(cl.shortcodes.where({
                        parent_id: parent.get("id")
                    }), function(model) {
                        return model.get("order")
                    }), models.length ? _.reduce(models, function(memo, model) {
                        return memo + this.createShortcodeString(model)
                    }, "", this) : (params = _.extend({}, parent.get("params")), _.isUndefined(params.content) ? "" : params.content)
                },
                create: function(model, options) {
                    
                    model = HeaderElements.__super__.create.call(this, model, options);
                    cl.events.trigger('headerElements:add', model);        
                    return model
                    
                }
            });
            
            
            cl.header_elements = new HeaderElements;
            
            
            
            cl.headerEl_view = Backbone.View.extend({
                
                events: {
                    "click > .cl_controls .cl_control-btn-handle": 'edit',
                    "click > .cl_controls .cl_control-btn-delete": 'destroy',
                    "click > .cl-icon-text i": 'changeIcon',

                },
                
                initialize: function() {
                    _.bindAll(this, 'updateFieldEvent');
                    this.listenTo(this.model, "destroy", this.removeView);
                    this.listenTo(this.model, "change:params", this.update);
                    this.listenTo(this.model, "update:icon", this.updateIcon);
                    this.listenTo(this.collection, "add", this.update);
                  
                    
                    this.listenTo(this.model, "updateField", this.updateFieldEvent);
                    //wp.customize.preview.bind('cl_element_updated', this.fieldUpdated);
                },

                updateIcon: function(value){
                    var fields = this.model.setting('fields');
                    
                    var field = fields['icon'];

                    var that = this;    
                    window.requestAnimationFrame(function(){that.updateField('icon', value, field) } );
                    cl.updateCustomizer();
                },

                changeIcon: function(e){
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    isSVG = false;
                    if( $(e.target).is('svg') || $(e.target).parents('svg').length > 0 )
                        isSVG = true;

                    cl.changeIconDialog.render(this.model, true, {pageX: e.pageX, pageY:e.pageY}, isSVG); 
                },
                
                check_frontJS: function(){
                    var type = this.model.get('type');
                    
                    if( !_.isUndefined(CL_FRONT) && ! _.isUndefined(window['codeless_builder_'+type]) )
                        window['codeless_builder_'+type]();
                },
                
                inline_edit: function(){
                    var editor_params, fields = cl.getHeaderMapped(this.model.get('type'))['fields'];
             
                    _.each(fields, function(value, key){
                        if(value.type == 'inline_text'){
                            var selector = value.selector || '';
                            if(selector == '')
                                selector = this.$el;
                            else{

                                if( !_.isUndefined(value.select_from_document) && value.select_from_document){
                                    selector = $(selector);
                                }else
                                    selector = this.$el.find(selector);
                            }


                                
                       
                            if(!_.isUndefined(value.only_text) && value.only_text){
                                editor_params = {
                                    
                                    disableReturn:true,
                                    disableDoubleReturn:true,
                                    disableExtraSpaces:true,
                                    toolbar:false,
                                    anchorPreview: false
                                    
                                };
                            }else{

                                rangy.init();

                                var HighlighterButton = MediumEditor.extensions.button.extend({
                                  name: 'highlighter',

                                  tagNames: ['mark'], // nodeName which indicates the button should be 'active' when isAlreadyApplied() is called
                                  contentDefault: '<i class="cl-builder-icon-paint-brush"></i>', // default innerHTML of the button
                                  contentFA: '<i class="fa fa-paint-brush"></i>', // innerHTML of button when 'fontawesome' is being used
                                  aria: 'Highlight', // used as both aria-label and title attributes
                                  action: 'highlight', // used as the data-action attribute of the button

                                  init: function () {
                                    MediumEditor.extensions.button.prototype.init.call(this);

                                    this.classApplier = rangy.createClassApplier('highlight', {
                                      elementTagName: 'mark',
                                      normalize: true
                                    });
                                  },

                                  handleClick: function (event) {
                                    this.classApplier.toggleSelection();
                                    this.base.checkContentChanged();
                                  }
                                });


                                var DropCaps = MediumEditor.extensions.button.extend({
                                  name: 'dropcaps',

                                  tagNames: ['span'], // nodeName which indicates the button should be 'active' when isAlreadyApplied() is called
                                  contentDefault: '<i class="cl-builder-icon-font"></i>', // Default Dropcaps
                                  //contentFA: '<i class="fa fa-paint-brush"></i>', // innerHTML of button when 'fontawesome' is being used
                                  aria: 'Dropcaps', // used as both aria-label and title attributes
                                  action: 'dropcaps', // used as the data-action attribute of the button

                                  init: function () {
                                    MediumEditor.extensions.button.prototype.init.call(this);

                                    this.classApplier = rangy.createClassApplier('dropcaps', {
                                      elementTagName: 'span',
                                      normalize: true
                                    });
                                  },

                                  handleClick: function (event) {
                                    this.classApplier.toggleSelection();
                                    this.base.checkContentChanged();
                                  }
                                });

                                var BlockQuote = MediumEditor.extensions.button.extend({
                                  name: 'blockquote',

                                  tagNames: ['blockquote'], // nodeName which indicates the button should be 'active' when isAlreadyApplied() is called
                                  contentDefault: '<i class="cl-builder-icon-quote-left"></i>', // Default BlockQuote
                                  aria: 'Blockquote', // used as both aria-label and title attributes
                                  action: 'blockquote', // used as the data-action attribute of the button

                                  init: function () {
                                    MediumEditor.extensions.button.prototype.init.call(this);

                                    this.classApplier = rangy.createClassApplier('blockquote', {
                                      elementTagName: 'blockquote',
                                      normalize: true
                                    });
                                  },

                                  handleClick: function (event) {
                                    this.classApplier.toggleSelection();
                                    this.base.checkContentChanged();
                                  }
                                });

                                editor_params = {
                                    toolbar: {
                                        buttons: [
                                            'bold', 
                                            'italic', 
                                            'underline', 
                                            'subscript', 
                                            'superscript',
                                            'justifyLeft', 
                                            'justifyCenter', 
                                            'justifyRight', 
                                            'justifyFull', 
                                            'highlighter',
                                            'dropcaps',
                                            'blockquote',
                                            'removeFormat',
                                            'anchor'
                                            ]
                                    },
                                    extensions: {
                                        'highlighter': new HighlighterButton(),
                                        'dropcaps' : new DropCaps(),
                                        'blockquote' : new BlockQuote()
                                      },
                                    anchorPreview: false
                                };
                            }




                            
                            if(_.isUndefined( selector[0]) )
                                return false;
                            
                            var editor = new MediumEditor(selector[0] , editor_params);
                            var that = this;
                            editor.subscribe('editableInput', function (event, editable) {
                               

                                var params = _.clone(that.model.get('params'));
                               
                                var cloned = $(editable).clone(true);
                                cloned.find('.cl_controls').remove();
                                
                                params[key] = cloned.html();
                          
                                that.model.set('params', params);
                                cl.app.updateHeader();
                            });
                            
                            editor.subscribe('focus', function(event, editable){
                               
                                $(editable).parents('.cl_element').addClass('cl-focused-text');
                            });
                            editor.subscribe('editableBlur', function(event, editable){
                                $(editable).parents('.cl_element').removeClass('cl-focused-text');
                            });
                            
                            
                            
                            
                        }  
                    }, this);
                },


                /*fieldUpdated: function(item){
                    
                    var model_id = item[0], 
                        element_type = item[1], 
                        model = cl.shortcodes.get(model_id),
                        field_id = item[2],
                        field_value = item[3],
                        isRequired = item[4];

                    
                    if( _.isUndefined( model ) )
                        return;
                            
                    var params = _.clone( model.get('params'));

                    var fields = model.setting('fields');
                    var field = fields[field_id];
                    
                    if( field['type'] == 'switch' )
                        field_value = field_value ? 1 : 0;

                    if( field['type'] == 'image' && _.isObject( field_value ) )
                        field_value = { id: field_value.id, url: encodeURIComponent(field_value.url) };

                    params[field_id] = field_value;
                            
                    
                    var self = this;
                    window.requestAnimationFrame(function(){
                        
                        _.defer( function(){
                            self.updateField(field_id, field_value, field, isRequired);
                        });
                            
                        
                    } );
                        //this.updateField(field_id, field_value, field, cl_required, subKey);        
                    model.set({params: params});
                    cl.app.updateHeader()
                    
                },*/

                updateFieldEvent: function( data ){
                    this.updateField( data[0], data[1], data[2], data[3] );
                    cl.app.updateHeader()
                },
                
                updateField: function(field_id, field_value, field, isRequired){
                    
                    var field_type = field['type'];
                    if(!_.isUndefined(field['reloadTemplate']) && field['reloadTemplate']){
                        var $element = this.$el;
                        this.$el.addClass('loading');
                        var that = this;
                       
                        cl.header_builder.ajax({
                            action: 'cl_reload_template',
                            wp_customize: 'on',
                            nonce: scriptData.ajax_nonce,
                            type: that.model.get('type'),
                            params: that.model.get('params')
                        }, scriptData.ajax_url).done(function(html){
                            $element.html(html);
                            $element.removeClass('loading');
                            that.check_frontJS();
                        });
                        
                        return;
                    }
                    
                    /* CSS Property */
                    if(!_.isUndefined(field['selector']) && !_.isEmpty(field['selector']) && !_.isUndefined(field['css_property']) && !_.isEmpty(field['css_property']) ){
                        var $field_el = this.$el.find(field['selector']);
                        


                        if( !_.isUndefined(field['media_query']) && !_.isEmpty(field['media_query']) ){
                            var custom_css = '@media '+field['media_query'] + '{';

                                var suffix = !_.isUndefined( field['suffix'] ) ? field['suffix'] : '';
                                custom_css += '#clr_'+this.model.get('id')+' '+field['selector']+'{';
                                    custom_css += field['css_property']+': '+field_value+suffix + ' !important';
                                custom_css += '}';

                            custom_css += '}';

                            

                            if ( ! jQuery( '#codeless-custom-css-model-' + this.model.get('id') + '-' + field_id ).size() ) {
                                jQuery( 'head' ).append( '<style id="codeless-custom-css-model-' + this.model.get('id') + '-' + field_id + '"></style>' );
                            }
                            jQuery( '#codeless-custom-css-model-' + this.model.get('id') + '-' + field_id ).text( custom_css );

                        }else{
                            if(field_type == 'image'){
                               
                                if( _.isObject(field_value) && !_.isUndefined( field_value.url ) )
                                    field_value = 'url('+decodeURIComponent(field_value.url)+')';
                                else if( _.isString(field_value) )
                                    field_value = 'url('+decodeURIComponent(field_value)+')';
                            }
                             
                            if(field_type == 'slider' && !_.isUndefined(field['suffix']))
                                field_value = field_value + field['suffix']
                            
                            if( field['css_property'] == 'font-family' ){
                                if( field_value == 'theme_default' )
                                    field_value = '';
                                else{
                             
                                    WebFont.load({
                                        google: { 
                                            families: [field_value] 
                                        } 
                                    }); 
                                    field_value = field_value;
                                }

                            }
                            
                            if(_.isString(field['css_property'])){
                                if( field['css_property'] == 'font-family' ){
                                    $field_el.css({ 'font-family': field_value });
                                }
                                else
                                    $field_el.css(field['css_property'], field_value);
                            }
                            else if(_.isObject(field['css_property']) || _.isArray(field['css_property'])){
                                _.each(field['css_property'], function(prop, index){
                                    
                                    if(_.isString(prop))
                                        $field_el.css(prop, field_value);
                                    else if(_.isObject(prop) || _.isArray(prop)){
                                        var extra_css_property = prop[0];
                                        var executed = false;
                                        _.each(prop[1], function(extra_prop, key){
                                            
                                            if(key == field_value){
                                                
                                                $field_el.css(extra_css_property, extra_prop);
                                                if(extra_prop == 'cover'){
                                                    $field_el.addClass('bg_cover');
                                                }
                                                executed = true;
                                                return;
                                                
                                            }else if(key == 'other' && !executed){
                                                
                                                if(extra_css_property == 'background-size')
                                                    $field_el.removeClass('bg_cover');
                                                    
                                                $field_el.css(extra_css_property, extra_prop);
                                                
                                            }
                                            
                                        });
                                    }    
                                    
                                });
                            }
                        }

                        
                        
                    }
                    
                    /* addClass */
                    if(!_.isUndefined(field['selector']) && !_.isEmpty(field['selector']) && !_.isUndefined(field['addClass']) && !_.isEmpty(field['addClass']) ){
                        var $field_el = this.$el.find(field['selector']);
                        
                        if(field_value)
                            $field_el.addClass(field['addClass']);
                        else
                            $field_el.removeClass(field['addClass']);
                    }
                    
                    
                    /* htmldata */
                    if(!_.isUndefined(field['selector']) && !_.isEmpty(field['selector']) && !_.isUndefined(field['htmldata']) && !_.isEmpty(field['htmldata']) ){
                        var $field_el = this.$el.find(field['selector']);
                        
                        if(field_value != 'none')
                            $field_el.attr('data-'+field['htmldata'], field_value);
                        else
                            $field_el.attr('data-'+field['htmldata'], '0');
                    }
                    
                    
                    
                    /* Select Class */
                    if(!_.isUndefined(field['selector']) && !_.isEmpty(field['selector']) && !_.isUndefined(field['selectClass']) ){
                        var $field_el = this.$el.find(field['selector']);
                        
                        if( field_type == 'select_icon' ){
                            this.$el.find(field['selector']).each(function(){
                                $(this)[0].className = '';
                            });
                        }else{
                            _.each(field['choices'], function(choice, index){
                                $field_el.removeClass(field['selectClass']+index); 
                            });
                        }
                        
                        
                        if(_.isString(field_value))
                            $field_el.addClass(field['selectClass']+field_value);
                        else if(_.isObject(field_value) || _.isArray(field_value)){
                            _.each(field_value, function(value, key){
                                 $field_el.addClass(field['selectClass']+value);
                            });
                        }
                            
                    }
                    
                    /* Custom Function */
                    if(_.isFunction(this['inlineEdit_'+this.model.get('shortcode')+'_'+field_id])){
                        this['inlineEdit_'+this.model.get('shortcode')+'_'+field_id](field_id, field_value);
                    }else if(_.isFunction(this['inlineEdit_'+field_id])){
                        this['inlineEdit_'+field_id](field_id, field_value, field);
                    }
                    
                    
                    if(!_.isUndefined(field['customJS']) && !_.isEmpty(field['customJS']) && _.isString( field['customJS'] ) ){
                        this[field['customJS']](field_id, field_value);
                    }

                    if(!_.isUndefined(field['customJS']) && !_.isEmpty(field['customJS']) && !_.isUndefined(field['customJS']['front']) ){
                        if( !_.isUndefined(field['customJS']['params']) )
                            window[field['customJS']['front']](field['customJS']['params'], true);
                        else
                            window[field['customJS']['front']](null, true);
                    }
                    
                    
                    if(field_type == 'css_tool'){
                        var $field_el = this.$el.find(field['selector']);

                        if( _.isUndefined( field['media_query'] ) ){
                            
                            
                            if(field_value != null && _.isObject(field_value) )
                                $field_el.css(field_value);
                           
                        }else{

                            var custom_css = '@media '+field['media_query'] + '{';

                                custom_css += '#clr_'+this.model.get('id')+' '+field['selector']+'{';

                                    if( _.isObject( field_value ) ){
                                        _.each(field_value, function(subvalue, subkey){
                                            custom_css += subkey+': '+subvalue + ' !important; ';
                                        });
                                    }
                                    
                                custom_css += '}';

                            custom_css += '}';

                            

                            if ( ! jQuery( '#codeless-custom-css-model-' + this.model.get('id') + '-' + field_id ).size() ) {
                                jQuery( 'head' ).append( '<style id="codeless-custom-css-model-' + this.model.get('id') + '-' + field_id + '"></style>' );
                            }
                            jQuery( '#codeless-custom-css-model-' + this.model.get('id') + '-' + field_id ).text( custom_css );
                        }
                        
                    }

                    if( field_type == 'inline_text' ){
                        var $field_el = this.$el.find( field['selector'] );
                        $field_el.html( field_value );
                    }
                    
                   
                    /*if(!_.isUndefined(cl_required) ){
                        
                        var fields = this.model.setting('fields');
                        var params = this.model.get('params');
                        var operators = {
                           '==': function(a, b){ return a==b},
                           '!=': function(a, b){ return a!=b}
                        };

                        _.each(cl_required, function(opt, index){
                            var field_id = opt['setting'],
                                field_val = !_.isUndefined(params[opt['setting']]) ? params[opt['setting']] : fields[field_id]['default'],
                                field = fields[field_id],
                                new_cl_required = null;
                            
                            if(!_.isUndefined(cl_required[field_id]))
                                new_cl_required = cl_required[field_id];
                         
                            if( operators[opt['operator']](field_value, opt['value'] ) )   
                              this.updateField(field_id, field_val, field, new_cl_required);
                            else
                              // Reverse Update
                              this.updateReverseField(field_id, field_val, field);
                            
                        }, this);
                        
                    }*/
                },
                
                
                destroy: function(e) {
                    
                    _.isObject(e) && e.preventDefault() && e.stopPropagation();
                    var answer = confirm("Are you sure to delete this "+this.model.setting('label')+" ?");
                    return !0 !== answer ? !1 : (cl.showMessage(this.model.setting("label") + ' deleted successfully' ), void this.model.destroy(), cl.app.updateHeader() )
                    
                },
                
                edit: function(){
                    cl.app.showEditPanel(this.model);
                },
                
                
                render: function() {
                    this.$el.attr("data-model-id", this.model.get("id"));
                    this.$el.attr('id', 'h_el_'+this.model.get("id"));
                    var type = this.model.get("type");
                    this.$el.attr("data-type", type);
                    this.$el.addClass("cl_" + type);

                    //this.addControls();

                    this.inline_edit();
                    
                    return this;
                },
                
                update: function(e){
                    
                },
                
                removeView: function(model) {
                    this.remove();
                    //cl.builder.notifyParent(this.model.get("parent_id"));
                    
                },
                
            });
            
            
            
            cl.add_header_element_dialog = cl.dialogView.extend({
                events:{
                    "click .elements_ .element" : "createElement",
                    "click > .close_dialog" : "hide"
                },
                
                initialize: function(){
                    cl.add_header_element_dialog.__super__.initialize.call(this);
                },
                
                render: function(row, col, prepend, coordinate){
                    
                    _.isUndefined(cl.HeaderBuilder) || (this.builder = new cl.HeaderBuilder);
                    this.prepend = _.isBoolean(prepend) ? prepend : false;
                    this.row = !_.isEmpty(row) ? row : 'main';
                    this.col = !_.isEmpty(col) ? col : 'left';
                    
                    cl.active_dialog = this;
                    this.setPosition(coordinate); 
                    this.show();
                    return cl.add_header_element_dialog.__super__.render.call(this);
                },
                
                createElement: function(e){
                    e.preventDefault();
                    
                    var $control = $(e.currentTarget),
                        type = $control.data("type");
                    
                    var _params = cl.getHeaderDefaultParams(type);
                    var order_new = !this.prepend ? cl.header_elements.nextOrder(this.row, this.col) : 0;
                    var element = {
                        type: type,
                        order: order_new,
                        params: _params,
                        row: this.row,
                        col: this.col
                    }
                    this.prepend ? cl.activity = 'prepend' : cl.activity = false;
                    
                    this.builder.create(element);
                    
                    this.model = this.builder.last();
                    this.hide();
                    cl.app.showEditPanel(this.model);
                    this.builder.render();
                    cl.updateCustomizer();
                    
                    
                    
                }
                
            });
            
    
}(window.jQuery, wp.customize));


/* cl-header-builder.js */

(function($){
    
    
    cl.HeaderBuilder = function(models){
        this.models = models || [];
        this.is_build_complete = !0;
        
        return this;
    };
    
    cl.HeaderBuilder.prototype = {
        
        create: function(attributes) {
            this.is_build_complete = !1;
            this.models.push(cl.header_elements.create(attributes));
            return this;
        },
        render: function(callback) {
            
            var elements;
            
            elements = _.map(this.models, function(model) {
                
                
                return {
                    id: model.get("id"),
                    type: model.get('type'),
                    row: model.get('row'),
                    col: model.get('col'),
                    params: model.get('params')
                }
            }, this);
            
            this.build(elements, callback);
        },
        
        last: function() {
            return this.models.length ? _.last(this.models) : !1
        }, 
        
        build: function(elements, callback) {
            
            
            this.ajax({
                action: "cl_load_header_element",
                nonce: scriptData.ajax_nonce, 
                elements: elements,
                
                wp_customize: 'on',
            }, scriptData.ajax_url).done(function(html) {
                
                
                
                _.each($(html), function(block) {
                    this.renderBlock(block)
                }, this);
                
                _.isFunction(callback) && callback(html);
                cl.app.setHeaderSortable();
                cl.activity = false;
                //cl.app.setResizable();
                
                this.models = [];
                //this.showResultMessage();
                this.is_build_complete = !0
            })
        },
        
        ajax: function(data, url) {
            
            return this._ajax = $.ajax({
                url: url || scriptData.ajax_url,
                type: "POST",
                dataType: "html",
                data: _.extend({

                }, data),
                
                context: this
            })
        },
        
        
        renderBlock: function(block){
            var $html, model, $this = $(block);
            model = cl.header_elements.get($this.data("modelId"));
            $html = $this;
            
            if(model){
                var view_name;
                view_name = this.getView(model);
                
                if(!model.get("from_content") && !model.get("from_template")){
                    var container = $('[data-row="'+model.get('row')+'"] [data-col="'+model.get('col')+'"]');
                    if(cl.activity == 'prepend'){
                        container.prepend($html);
                    }else
                        container.append($html);
                        
                    cl.app.updateHeader();
                }
                
                model.view = new view_name({
                        model: model,
                        el: $html
                }).render();
            }
        },
        
        getView: function(model) {
            var view = cl.headerEl_view;
            _.isObject(cl["headerElView" + model.get("type")]) && (view = cl["headerElView" + model.get("type")]);

            return view;
            
        },
        
        
        buildFromArray: function(){
            var $this = this;
            
            _.each(cl.header_elements_var, function(element) {
                
                var $block = $('body .header_container').find("[data-model-id=" + element.id + "]");
                
                var params =  _.isObject(element.params) ? element.params : {};
                var model = cl.header_elements.create({
                        
                        id: element.id,
                        type: element.type,
                        params: params,
                        row: element.row,
                        col: element.col,
                        from_content: true
                    }, {  silent: !0 }
                    
                );
                
                $block.attr("data-model-id", model.get("id"));
                $this.renderBlock($block);
            });
            
            cl.app.setHeaderSortable();
            //cl.app.setResizable();
        },
        
    };
    
    cl.header_builder = new cl.HeaderBuilder();
    
    
    
}(window.jQuery));

/* cl-codeless-app.js */

(function($) {
    
    cl.CodelessApp = Backbone.View.extend({
        
        el: "body",
        mode: "view",
        events: {
            'click [data-codeless="true"] > .add-element-prepend' : 'addElementPrepend',
            'click [data-codeless="true"] > .add-element-append' : 'addElementAppend',
            'click [data-codeless="true"] > .add-first-element' : 'addElementAppend',
            'click > #viewport .header_container .add-header-element-prepend' : 'addHeaderElementPrepend',
            'click > #viewport .header_container .add-header-element-append' : 'addHeaderElementAppend',

            'click > .cl-sticky-panel #cl-nav-page-settings' : 'openPageSettings',
            'click > .cl-sticky-panel #cl-nav-styling' : 'openGlobalStyling',
            'click > .cl-sticky-panel #cl-nav-add-page' : 'addNewPage',
            'click > .cl-sticky-panel #cl-nav-preview' : 'previewPage',
            'click > .cl-sticky-panel #cl-nav-page_options' : 'openPageOptions',
        },
        initialize: function() {
            _.bindAll(this, "saveRowOrder", "saveElementOrder", "saveHeaderElementOrder", "saveColumnOrder");
            cl.events.on("shortcodes:add", this.changeObjectParams, this);
            cl.events.on('shortcodes:cloned', this.fixElementOrder, this);
            wp.customize.preview.bind('cl_element_updated', this.fieldUpdated);
            this.paddingMarginEvent();

            
        },


        fieldUpdated: function(item){
           
            var model_id = item[0], 
                element_type = item[1], 
                model = item[5] ? cl.header_elements.get(model_id) : cl.shortcodes.get(model_id),
                field_id = item[2],
                field_value = item[3],
                isRequired = item[4];

            
            if( _.isUndefined( model ) )
                return;
                       
            var params = _.clone( model.get('params'));

            if( _.isEmpty(params) )
                        params = {};

            var fields = model.setting('fields');
            var field = fields[field_id];
            
            if( field['type'] == 'switch' )
                field_value = field_value ? 1 : 0;

            if( field['type'] == 'image' && _.isObject( field_value ) )
                field_value = { id: field_value.id, url: encodeURIComponent(field_value.url) };

            params[field_id] = field_value;
                    
            
                
            model.set({params: params});

            window.requestAnimationFrame(function(){
                
                _.defer( function(){
                    
                    model.trigger('updateField', [field_id, field_value, field, isRequired] );
                });
                    
                
            } );
                  //this.updateField(field_id, field_value, field, cl_required, subKey);        
            
        },
        

        paddingMarginEvent: function(){

            var mousemove_func = function(e){
                var el = $(e.target).closest('.cl_element');
                if( ! e.shiftKey ){
                    el.removeClass('show-all');
                    return;
                }

                el.addClass('show-all');
            };

            var throttled = _.throttle(mousemove_func, 400);

            this.$el.mousemove(throttled);
        },

        changeObjectParams: function(model){
            var params = _.clone(model.get('params'));
            _.each(params, function(value, key){
               
                if( !_.isUndefined(value) && !_.isNull(value) && value.length && cl.checkObjectValue(key, value, model)  ){
                    
                    params[key] = cl.stringToObject( value );

                }
                
            });
            
            model.set('params', params);

        },
        
        showEditPanel: function(model){
            var data = {},
                elementType = _.isUndefined(model.get('shortcode')) ? 'header_el' : 'shortcode';
                type = (elementType == 'header_el') ? model.get('type') : model.get('shortcode');
            
            if(elementType == 'header_el'){
                var el = cl.getHeaderMapped(type);
                if(!_.isUndefined(el['open_section']) && !_.isEmpty(el['open_section'])){
                    wp.customize.preview.send('cl_show_section', el['open_section'] );
                    return;
                }
            }
            
            
            data['id'] = model.get('id');
            data['type'] = type;
            data['header_element'] = elementType == 'header_el' ? true : false;
            data['name'] = model.get('fields')
            data['options'] = _.clone( model.get('params') );
            wp.customize.preview.send('cl_show_options', data );  
        },
        
        render: function(){
            
            cl.$page = this.$el.find('[data-codeless="true"]');
            
            /*window.parent.wp.customize.previewer.unbind('refresh').bind('refresh', function(){
                
            })*/
            
            cl.n = '';
            
            var api = parent.wp.customize;
            
            
            wp.customize.preview.bind('cl-save', function(){
                if( typeof wp.customize('cl_page_content') !== 'undefined' && scriptData.active_builder ){
                    var content = cl.builder.getContent(),
                        cl_page_content = wp.customize('cl_page_content').get();
                    
                    if( ! _.isObject(cl_page_content) )
                        cl_page_content = {};
                    
                    //cl.lastUrl = api.previewer.previewUrl.get()

                    cl_page_content[cl.pageID] = content;
                    wp.customize('cl_page_content').set(cl_page_content);

                    cl.builder.ajax({
                        action: "cl_save_page_content",
                        data: cl_page_content,
                        nonce: scriptData.ajax_nonce,
                        clactive:1
                    }, scriptData.ajax_url).done(function(html) {
                            
                    });
                }
                
            });


            
            
            
            wp.customize.preview.bind( 'loading-initiated', function(new_url){
                
                
                    _.defer(function(){
                        var prev_content = cl.builder.getContent(),
                            cl_page_content = api('cl_page_content').get();
                    

                        wp.customize.preview.send( 'cl_hide_all' ); 
                        cl_page_content[cl.pageID] = prev_content;
                        cl_page_content['changeset'] = api.settings.changeset.uuid;
                    
                        cl.updateContent(cl_page_content);
                        cl.updateCustomizer();
                    });
            

                    
            });
            
           
            //$(window).resize(this.resizeWindow);
            _.defer(function() {
                cl.events.trigger("app.render");
                $("body").find( 'a' ).attr('href', '#');
            });


            $("body").on('click', function(e){
                if( $( e.target ).parents('.cl-selected-element').length == 0 && ! e.shiftKey )
                    cl.$page.find('.cl-selected-element').removeClass('cl-selected-element');
            });


            
            
            return this
        },
        
        addElementPrepend: function(e){
            e.preventDefault();
            cl.addElementDialog.render(this.model, true, {pageX: e.pageX, pageY:e.pageY});
        },
        
        addElementAppend: function(e){
            e.preventDefault();
            cl.addElementDialog.render(this.model, false, {pageX: e.pageX, pageY:e.pageY});
        },
        
        addHeaderElementPrepend: function(e){
            e.preventDefault();
            var row = $(e.currentTarget).closest('.header-row').data('row');
            var col = $(e.currentTarget).closest('.header-col').data('col');
            
            cl.addHeaderElementDialog.render(row, col, true, {pageX: e.pageX, pageY:e.pageY});
        },
        
        addHeaderElementAppend: function(e){
            e.preventDefault();
            var row = $(e.currentTarget).closest('.header-row').data('row');
            var col = $(e.currentTarget).closest('.header-col').data('col');
            
            cl.addHeaderElementDialog.render(row, col, false, {pageX: e.pageX, pageY:e.pageY});
        },
        
        
        
        saveRowOrder: function() {
            _.defer(function(app) {
                
                var row_params, column_params, $rows = cl.$page.find(".cl_root-element");
            
                $rows.each(function(key, value) {
                    var $this = $(this);
                    cl.shortcodes.get($this.data("modelId")).save({
                        order: key
                    }, {
                        silent: !0
                    })
                });
                
                cl.updateCustomizer();
            }, this)
        },

        fixElementOrder: function(model) {
            setTimeout(function() {

                var parent_id = model.get('parent_id'),
                    parent_model = cl.shortcodes.get(parent_id);

                var $elements = !_.isUndefined(parent_model) ? parent_model.view.content().find(' > [data-model-id]') : cl.$page.find(".cl_root-element");

                $elements.each(function(key, value) {
                    var $this = $(this);
                    cl.shortcodes.get($this.data("modelId")).save({
                        order: key
                    }, {
                        silent: !0
                    })
                });
                
                cl.updateCustomizer();
            }, 100)
        },
            
        saveElementOrder: function(event, ui) {
            _.defer(function(app, e, ui) {
                if (_.isNull(ui.sender)) {
                    var $column = ui.item.parent(),
                        $columnID = $column.closest('.cl_container-block').data('modelId');

                    

                    var $columnModel = cl.shortcodes.get($columnID),
                        $elements = $columnModel.view.content().find(' > [data-model-id]');
                  
                    $elements.each(function(key, value) {
                            
                        var model, prev_parent, current_parent, $element = $(this),
                            prepend = !1;
                            
                        model = cl.shortcodes.get($element.data("modelId"));
                        prev_parent = model.get("parent_id");
                        current_parent = $column.parents(".cl_element[data-tag]:first").data("modelId");
                            
                        model.save({
                            order: key,
                            parent_id: current_parent
                        }, {
                            silent: !0
                        });
                            
                    })
                }
                cl.updateCustomizer();
            }, this, event, ui)
        },
        
        saveHeaderElementOrder: function(event, ui) {
            _.defer(function(app, e, ui) {
                if (_.isNull(ui.sender)) {
                    var $column = ui.item.parent(),
                        $elements = $column.find("> [data-model-id]");
                        
                    $column.find("> [data-model-id]").each(function(key, value) {
                            
                        var model, prev_row, prev_col, current_row, current_col, $element = $(this),
                            prepend = !1;
                            
                        model = cl.header_elements.get($element.data("modelId"));
                        prev_row = model.get("row");
                        prev_col = model.get("col");
                        current_row = $column.parents(".header-row:first").data("row");
                        current_col = $column.data("col");
                        
                        model.save({
                            order: key,
                            row: current_row,
                            col: current_col
                        });
                        
                        
                            
                    })
                }
                
                app.updateHeader();
                
            }, this, event, ui)
        },
        
        updateHeader: function(){
            var data = [];
            data  = _.groupBy(cl.header_elements.models, function(model){
                        return model.get('row');
                    });
                    
                    _.each(data, function(key, index){
                        data[index] = _.groupBy(key, function(model){
                            return model.get('col');
                        });
                        _.each(data[index], function(col, index_col){
                            data[index][index_col] = _.sortBy(data[index][index_col], function(model){
                                return model.get('order');
                            });
                        });
                    });
                
                window.wp.customize.preview.send('cl_header_builder_update', data);
                cl.updateCustomizer();
        },
            
        saveColumnOrder: function(event, ui) {
            _.defer(function(app, e, ui) {
                var row = ui.item.parent();
                row.find("> [data-model-id]").each(function() {
                    var $element = $(this),
                        index = $element.index();
                    cl.shortcodes.get($element.data("modelId")).save({
                        order: index
                    })
                })
                cl.updateCustomizer();
            }, this, event, ui);
        },
        
        renderPlaceholder: function(event, element) {
            var tag = $(element).data("tag"),
                is_container = cl.map[tag] === Object(cl.map[tag]) && ((!0 === cl.map[tag].is_container || !1 === cl.map[tag].is_container || "[object Boolean]" === toString.call(cl.map[tag].is_container)) && !0 === cl.map[tag].is_container || null != cl.map[tag].as_parent && "[object Array]" === Object.prototype.toString.call(cl.map[tag].as_parent) && 0 != cl.map[tag].as_parent),
                $helper = $('<div class="cl_helper cl_helper-' + tag + '"><i class="cl_general cl_element-icon' + (cl.map[tag].icon ? " " + cl.map[tag].icon : "") + '"' + (is_container ? ' data-is-container="true"' : "") + "></i> " + cl.map[tag].label + "</div>").prependTo("body");
            return $helper
        },
        
        renderHeaderPlaceholder: function(event, element) {
            var tag = $(element).data("type");
            var $helper = $('<div class="cl_helper cl_helper-' + tag + '"> "' + cl.headerMap[tag].label + "</div>").prependTo("body");

            return $helper
        },
        
        placeElement: function($view, activity) {
            var model = cl.shortcodes.get($view.data("modelId"));

            if(activity == 'replace' && cl.$page.find('[data-model-id='+model.get("id")+']').length > 0 ){
                var toReplace = cl.$page.find('[data-model-id='+model.get("id")+']');
                toReplace.replaceWith($view);
                
                setTimeout(function(){
                    $view.removeClass('loading');
                },150);
                return;
            }
            

            cl.$page = this.$el.find('[data-codeless="true"]');
            var $codeless_section = cl.$page;

            if( model.get('shortcode') == 'cl_page_header' && this.$el.find( '.codeless-content-page-header[data-codeless="true"]' ).length > 0 ){
                $codeless_section = this.$el.find( '.codeless-content-page-header[data-codeless="true"]' );
            }
                
            if( activity != 'replace' ){
                model && model.get("place_after_id") ? ($view.insertAfter($codeless_section.find("[data-model-id=" + model.get("place_after_id") + "]")), model.unset("place_after_id")) : _.isString(activity) && "prepend" === activity ? $view.prependTo($codeless_section) : $view.appendTo($codeless_section);
                setTimeout(function(){
                    $codeless_section.removeClass('loading');
                },150);
            }
            
            
            cl.activity = false;
        },
        
        setSortable: function() {
            "use strict";
            var $cl_row_el = $(".cl_root-element");
            $cl_row_el.parent().sortable({
                forcePlaceholderSize: !1,
                items: ".cl_root-element",
                handle: ".cl-move-row",
                cursor: "move",
                cursorAt: {
                    top: 20,
                    left: 16
                },
                placeholder: "cl_placeholder-row",
                cancel: ".cl-non-draggable-row",
                helper: this.renderPlaceholder,
                start: function(event, ui) {
                    $('body').addClass('cl-move-start');
                    ui.placeholder.height(30)
                },
                stop: function(event, ui){
                    $('body').addClass('cl-move-stop');
                },
                tolerance: "pointer",
                update: function(event, ui) {
                    cl.app.saveRowOrder()
                }
            }); 
            
            $(".cl_element-container").sortable({
                forcePlaceholderSize: !0,
                helper: this.renderPlaceholder,
                distance: 3,
                scroll: !0,
                scrollSensitivity: 70,
                cursor: "move",
                cursorAt: {
                    top: 20,
                    left: 16
                },
                connectWith: ".cl_element-container",
                items: "> [data-model-id]",
                cancel: ".cl-non-draggable",
                handle: ".cl_element-move",
                start: function(e, ui){
                    $('body').addClass('cl-move-start'); 
                    var model = cl.shortcodes.get(ui.item.closest('.cl_cl_row').data('model-id'));
                    _.isObject(model) && model.view.closeLayoutTool();
                },
                update: this.saveElementOrder,
                change: function(event, ui) {
                    ui.placeholder.height(30), ui.placeholder.width(ui.placeholder.parent().width())
                },
                placeholder: "cl_placeholder",
                tolerance: "pointer",
                over: function(event, ui) {
                    
                    var tag = ui.item.data("tag"),
                        parent_tag = ui.placeholder.closest("[data-tag]").data("tag"),
                        allowed_container_element = "undefined" == typeof cl.map[parent_tag].allowed_container_element ? !0 : cl.map[parent_tag].allowed_container_element;
                    if (ui.placeholder.removeClass("cl_hidden-placeholder"), ui.placeholder.css({
                            maxWidth: ui.placeholder.parent().width()
                        }), tag && cl.map) {
                        if (cl.checkRestrictions(parent_tag, tag) || ui.placeholder.addClass("cl_hidden-placeholder"), ui.sender) {
                            var $sender_column = ui.sender.closest(".cl_element").removeClass("cl_sorting-over");
                            1 > $sender_column.find(".cl_element").length && $sender_column.addClass("cl_empty")
                        }
                        //ui.placeholder.closest(".cl_element.cl_cl_row").addClass("cl_sorting-over");
                        var is_container = cl.map[tag] === Object(cl.map[tag]) && ((!0 === cl.map[tag].is_container || !1 === cl.map[tag].is_container || "[object Boolean]" === toString.call(cl.map[tag].is_container)) && !0 === cl.map[tag].is_container || null != cl.map[tag].as_parent && "[object Array]" === Object.prototype.toString.call(cl.map[tag].as_parent) && 0 != cl.map[tag].as_parent);
                        is_container && !0 !== allowed_container_element && allowed_container_element !== tag.replace(/_inner$/, "") && ui.placeholder.addClass("cl_hidden-placeholder")
                    }
                },
                out: function(event, ui) {
                    ui.placeholder.removeClass("cl_hidden-placeholder");
                    var model = cl.shortcodes.get(ui.placeholder.closest('.cl_cl_row').data('model-id'));
                    _.isObject(model) && model.view.closeLayoutTool();
                    ui.placeholder.closest(".cl_element.cl_root-element").removeClass('cl_sorting-over');
                },
                stop: function(event, ui) {
                    var item_model, tag = ui.item.data("tag"),
                       
                        parent_tag = ui.item.parents("[data-tag]:first").data("tag"),
                        allowed_container_element = cl.map[parent_tag].allowed_container_element ? cl.map[parent_tag].allowed_container_element : !0,
                        trig_changed = !0;
                    $('body').removeClass('cl-move-start');
                    ui.item.closest(".cl_element.cl_root-element").removeClass("cl_sorting-over");
                    cl.checkRestrictions(parent_tag, tag) || (ui.placeholder.removeClass("cl_hidden-placeholder"), $(this).sortable("cancel"), trig_changed = !1);
                    var is_container = cl.map[tag] === Object(cl.map[tag]) && ((!0 === cl.map[tag].is_container || !1 === cl.map[tag].is_container || "[object Boolean]" === toString.call(cl.map[tag].is_container)) && !0 === cl.map[tag].is_container || null != cl.map[tag].as_parent && "[object Array]" === Object.prototype.toString.call(cl.map[tag].as_parent) && 0 != cl.map[tag].as_parent);
                    is_container && !0 !== allowed_container_element && allowed_container_element !== tag.replace(/_inner$/, "") && (ui.placeholder.removeClass("cl_hidden-placeholder"), $(this).sortable("cancel"), trig_changed = !1), trig_changed && (item_model = cl.shortcodes.get(ui.item.data("modelId")));
                }
            });
            $cl_row_el.droppable({
                over: function(e, ui){
                    $(e.target).addClass('cl_sorting-over');
                },
                
                out: function(e, ui){
                    $(e.target).removeClass('cl_sorting-over');
                }
            });
            
            $(".cl_row-sortable").sortable({
                forcePlaceholderSize: !0,
                tolerance: "pointer",
                items: "> [data-tag=cl_column], > [data-tag=cl_column_inner]",
                handle: ".cl_move-cl_column",
                start: function(event, ui) {
                    $('body').addClass('cl-move-start');
                    var id = ui.item.data("modelId"),
                        model = parent.cl.shortcodes.get(id),
                        css_class = model.view.convertSize(model.getParam("width"));
                    ui.item.appendTo(ui.item.parent().parent()), ui.placeholder.addClass(css_class), ui.placeholder.width(ui.placeholder.width() - 4)
                },
                cursor: "move",
                cursorAt: {
                    top: 20,
                    left: 16
                },
                stop: function(event, ui) {
                    $('body').removeClass('cl-move-start');
                },
                update: this.saveColumnOrder,
                placeholder: "cl_placeholder-column",
                helper: this.renderPlaceholderf
            });
        },
        
        setSliderSortable: function(){
            $(".cl-slides-container").sortable({
                forcePlaceholderSize: !0,
                tolerance: "pointer",
                items: "> a",
                start: function(event, ui) {
                  
                },
                cursor: "move",
                cursorAt: {
                    top: 20,
                    left: 16
                },
                stop: function(event, ui) {
                   
                },
                update: this.saveSliderOrder,
               
            });
        },

        saveSliderOrder: function(event, ui){
            _.defer(function(app) {
                var slider = ui.item.closest('.cl-slides-container');
                var $items = slider.find('> a');
                    
                $items.each(function(key, value) {
                    var $this = $(this);
                    cl.shortcodes.get($this.data("slideId")).save({
                        order: key
                    }, {
                        silent: !0
                    });

                    $this.html(key+1);

                });
                
                cl.updateCustomizer();
            }, this)
        },
        
        
        setHeaderSortable: function(){
            $(".header-col").sortable({
                forcePlaceholderSize: !0,
                helper: this.renderHeaderPlaceholder,
                distance: 3,
                scroll: !0,
                scrollSensitivity: 70,
                cursor: "move",
                cursorAt: {
                    top: 20,
                    left: 16
                },
                connectWith: ".header-col",
                items: "> [data-model-id]",
                cancel: ".cl-non-draggable",
                handle: ".cl_element-move",
                start: function(e, ui){
                    $('body').addClass('cl-move-start'); 
                    
                },
                update: this.saveHeaderElementOrder,
                change: function(event, ui) {
                    
                    ui.placeholder.height(60), ui.placeholder.width(100)
                },
                placeholder: "cl_placeholder",
                tolerance: "pointer",
                over: function(event, ui) {
                    
                    
                },
                out: function(event, ui) {
                    ui.placeholder.removeClass("cl_hidden-placeholder");
                    
                    //ui.placeholder.closest(".cl_element.cl_cl_row").removeClass('cl_sorting-over');
                },
                stop: function(event, ui) {
                    
                    $('body').removeClass('cl-move-start');
                    ui.placeholder.removeClass("cl_hidden-placeholder");
                }
            });
        },

        openPageSettings: function(e){
            e.preventDefault();
            wp.customize.preview.send( 'cl_open_page_settings', { pageID: cl.pageID, section: 'post['+cl.postType+']['+cl.pageID+']' } );
        },

        openGlobalStyling: function(e){
            e.preventDefault();
            wp.customize.preview.send( 'cl_open_global_styling' );
        },

        openPageOptions: function(e){
            if( !_.isUndefined(e) )
                e.preventDefault();

           
        },

        addNewPage: function(e){
            e.preventDefault();
            wp.customize.preview.send( 'cl_add_new_page', { postType: 'page' } );
        },

        previewPage: function(e){
            e.preventDefault();
            window.open(wp.customize.settings.url.self, '_blank');
        }
        
        /*setResizable: function(){
            var sibTotalWidth;
            var container;
            var handle = 'e';
            var dir;
            $('.cl_cl_column.cl_element').each(function(){
                var $el = $(this);
                if($el.next().next().length == 0)
                    handle = '';
                if($el.next().length == 0)
                    handle = 'w';
                if($el.next().length == 0 && $el.prev().length == 0)
                    handle = '';
                    
                if(handle != ''){
                    var current_model, current_width;
                    
                    $el.resizable({
                        handles: handle,
                        start: function(event, ui){
                            current_model = cl.shortcodes.get(ui.originalElement.data('model-id'));
                            current_width = current_model.getParam('width');
                            ui.originalElement.removeClass(current_model.view.convertSize(current_width).replace(/[^\d]/g, ""));
                            /*container = ui.element.closest('.cl_row-sortable');
                            sibTotalWidth = ui.originalSize.width + ui.originalElement.next().outerWidth();
                        },
                        stop: function(event, ui){     
                            var cellPercentWidth=100 * ui.originalElement.outerWidth()/ container.innerWidth();
                            ui.originalElement.css('width', cellPercentWidth + '%');  
                            var nextCell = ui.originalElement.next();
                            var nextPercentWidth=100 * nextCell.outerWidth()/container.innerWidth();
                            nextCell.css('width', nextPercentWidth + '%');
                        },
                        resize: function(event, ui){ 
                            
                            var delta_x = ui.size.width - ui.originalSize.width;
                            var delta_y = ui.size.height - ui.originalSize.height;
                            if (delta_x > 0) { 
                                //dir = 'left';
                                if(handle == 'w')
                                    dir = 'larger';
                                else
                                    dir = 'lower';
                            } else if (delta_x < 0) { 
                                //dir = 'right';
                                if(handle == 'w'){
                                    dir = 'lower'
                                }else
                                    dir = 'larger'
                            }
                            var _a = _.omit(current_model.get('params'));
                            _a.width = '1/1';
                            current_model.set('params', _a);
                            current_model.view.setColumnClasses();
                            
                            
                            
                           
                        }
                    });
                 }
            });
        }*/
    });
   

}(window.jQuery));



/* cl-main.js */

(function($, api) {
    
    "use strict";
    
    cl.events = _.extend({}, Backbone.Events);

    cl.clone_index = 1;
    
    cl.createPreLoader = function() {
        cl.$preloader = $("#cl_preloader");
    };
    
    cl.updateCustomizer = function(){
        
        wp.customize.preview.send( 'cl_update_customizer' ); 
        setTimeout(function(){
            if( $('[data-codeless="true"] .cl_element').length > 0 )
                $('.add-first-element').remove();
            else{
                if( $('[data-codeless="true"] .add-first-element').length == 0 )
                    $('[data-codeless="true"] .app-prepend').after( '<div class="add-first-element">Add Element</div>' );
            }
        }, 600);
        
    };
    
    
    cl.updateContent = function( content ){
        if( _.isUndefined(window.parent.wp) )
            return;
        
        var wasSaved = window.parent.wp.customize.state( 'saved' ).get();
        
        var setting = wp.customize( 'cl_page_content' ), wasDirty;
        if ( setting && ! _.isEqual( setting.get(), content ) ) {
            wasDirty = setting._dirty;
            setting.set( content );
            setting._dirty = true;
        }
        
        window.parent.wp.customize.state( 'saved' ).set( wasSaved );
    };

    cl.getPageContent = function( ){
        return api( 'cl_page_content' ).get();
    };
    
    cl.removePreLoader = function() {
        cl.$preloader && cl.$preloader.remove();
        
        $('.cl-loading-overlay', window.parent.document).css('opacity', 0);
            setTimeout(function(){
                $('.cl-loading-overlay', window.parent.document).remove();
        }, 400);

        if( $('.cl-simple-mode', window.parent.document).length > 0 ){
            $('.cl-sticky-panel').remove();
            $('.cl-custom-post-button').remove();
            $('.cl-add-custom-post-button').remove();
        }
    };
    
    cl.buildRestrictions = function() {
        cl.shortcode_restrictions = {};
        _.each(cl.map, function(object) {
            _.isObject(object.as_parent) && _.isString(object.as_parent.only) && (cl.shortcode_restrictions["parent_only_" + object.settings] = object.as_parent.only.replace(/\s/, "").split(","));
            _.isObject(object.as_parent) && _.isString(object.as_parent.except) && (cl.shortcode_restrictions["parent_except_" + object.settings] = object.as_parent.except.replace(/\s/, "").split(","));
            _.isObject(object.as_child) && _.isString(object.as_child.only) && (cl.shortcode_restrictions["child_only_" + object.settings] = object.as_child.only.replace(/\s/, "").split(","));
            _.isObject(object.as_child) && _.isString(object.as_child.except) && (cl.shortcode_restrictions["child_except_" + object.settings] = object.as_child.except.replace(/\s/, "").split(","));
        });
    };
    
    cl.checkRestrictions = function(tag, related_tag) {
        return _.isArray(cl.shortcode_restrictions["parent_only_" + tag]) && !_.contains(cl.shortcode_restrictions["parent_only_" + tag], related_tag) ? !1 : _.isArray(cl.shortcode_restrictions["parent_except_" + tag]) && _.contains(cl.shortcode_restrictions["parent_except_" + tag], related_tag) ? !1 : _.isArray(cl.shortcode_restrictions["child_only_" + related_tag]) && !_.contains(cl.shortcode_restrictions["child_only_" + related_tag], tag) ? !1 : _.isArray(cl.shortcode_restrictions["child_except_" + related_tag]) && _.contains(cl.shortcode_restrictions["child_except" + related_tag], tag) ? !1 : !0
    };
    
    cl.CloneModel = function(builder, model, parent_id, child_of_clone, append) {
        cl.clone_index /= 10;
        var newOrder, params, tag, data, newModel;
        
        newOrder = _.isBoolean(child_of_clone) && !0 === child_of_clone ? model.get("order") : parseFloat(model.get("order")) + cl.clone_index;
        params = _.clone( model.get('params') );

        if( !_.isUndefined( params['css_style'] ) )
                params['css_style'] = _.clone( params['css_style']);

        tag = model.get("shortcode");
        data = {
            shortcode: tag,
            parent_id: parent_id,
            order: _.isBoolean(append) && append === true ? false : newOrder,
            cloned: !0,
            cloned_from: model.toJSON(),
            params: params
        };

        cl["cloneMethod_" + tag] && (data = cl["cloneMethod_" + tag](data, model));
        if ( ! (_.isBoolean(child_of_clone) && true === child_of_clone ) && ! ( _.isBoolean(append) && append === true ) ) {
          data.place_after_id = model.get("id");
        }
        
        builder.create(data);
        newModel = builder.last();

        _.each(cl.shortcodes.where({
                parent_id: model.get("id")
        }), function(shortcode) {
                cl.CloneModel(builder, shortcode, newModel.get("id"), !0)
        }, this);

        return newModel;
    };
    
    cl.showMessage = function(message) {
        cl.message_timeout && ($(".cl_message").remove(), window.clearTimeout(cl.message_timeout));
        var $message = $('<div class="cl_message success" style="z-index: 999;">' + message + "</div>").prependTo($("body"));
        _.defer(function(){$message.addClass('show');});
        cl.message_timeout = window.setTimeout(function() {
            $message.removeClass('show');
            _.defer(function(){ $(this).remove() });
            cl.message_timeout = !1;
        }, 3000);
    };
    
    cl.createPreLoader();
    
    cl.build = function() {
       
        
        cl.addElementDialog = new cl.add_element_dialog({ el: '#cl_dialog_add_element' });
        cl.customLayoutDialog = new cl.custom_layout_dialog({ el: '#cl_dialog_custom_layout' });
        cl.addHeaderElementDialog = new cl.add_header_element_dialog({ el: '#cl_dialog_add_header_element' });
        cl.changeIconDialog = new cl.change_icon_dialog({ el: '#cl_dialog_change_icon' });
        cl.saveElementTemplate = new cl.save_element_template_dialog({ el: '#cl_save_element_template' });
        cl.app = new cl.CodelessApp;
        
        cl.buildRestrictions();
        cl.app.render();
        
        cl.builder.buildFromArray();

        cl.header_builder.buildFromArray();
        
        //cl.removePreLoader(); 

        $(window).trigger("cl_build");
    };
    
    api.bind( 'preview-ready', function(){
        if( ! _.isUndefined(window['CL_FRONT']) )
            window['CL_FRONT'].config.$isCustomizer = true;

        
        setTimeout(function(){
            cl.build();
            
        }, 100);
        

    });
    
   /*!
 * Super Context Menu
 * Created by EZ17-1, pwnedgod @ github
 */
'use strict';

window.superCm = function(msie) {

    var settings = {
        'minWidth': null,
        'maxHeight': null,
        'autoClose': false,
        'searchBar': false,
        'searchBarPlaceholder': 'Search...',
        'zIndex': 50
    };

    var cmTemplate = $('<div>').addClass('context-menu')
        .append('<span>')
        .append(
            $('<div>').addClass('context-menu-options')
        );

    var cmOptTemplate = $('<div>');

    var cmSearchTemplate = $('<div>').addClass('context-menu-search')
        .append(
            $('<input>').prop({ 'type':'text', 'placeholder': settings.searchBarPlaceholder })
        );

    var optIconTemplate = $('<i>').addClass('option-icon');
    var optTextTemplate = $('<span>').addClass('option-text');
    var optSeparatorTemplate = $('<hr>').addClass('option-separator');

    var cms = [];

    var activeOpt = null;

    function getOpts(cmIndex, actualOpts)
    {
        var cm = cms[cmIndex];
        return cm.search.result && !actualOpts ? cm.search.result : cm.opts;
    }

    function getOptContainer(cmIndex)
    {
        return cms[cmIndex].element.find('.context-menu-options');
    }

    function getOptElements(cmIndex)
    {
        return getOptContainer(cmIndex).children();
    }

    function getOptElement(cmIndex, optIndex)
    {
        return getOptContainer(cmIndex).children().eq(optIndex);
    }

    function setCurrentActiveOver(cmIndex, optIndex)
    {
        if(activeOpt == null || activeOpt.cmIndex != cmIndex || activeOpt.optIndex != optIndex) {
            if(activeOpt != null) {
                let cmOptElement = getOptElement(activeOpt.cmIndex, activeOpt.optIndex);

                if(cmOptElement.hasClass('active')) {
                    cmOptElement.removeClass('active');
                }
            }

            if(cmIndex != -1 && optIndex != -1) {
                let cmOptElement = getOptElement(cmIndex, optIndex);

                if(!cmOptElement.hasClass('active')) {
                    cmOptElement.addClass('active');
                }

                activeOpt = {
                    'cmIndex': cmIndex,
                    'optIndex': optIndex
                };
            } else {
                activeOpt = null;
            }
        }
    }

    function setActiveOptSubmenu(cmIndex, optIndex)
    {
        var activeSubmenu = cms[cmIndex].activeSubmenu;
        if(activeSubmenu != optIndex) {
            if(activeSubmenu != -1) {
                let cmOptElement = getOptElement(cmIndex, activeSubmenu);

                if(cmOptElement.hasClass('active-submenu')) {
                    cmOptElement.removeClass('active-submenu');
                }
            }

            if(optIndex != -1) {
                let cmOptElement = getOptElement(cmIndex, optIndex);

                if(!cmOptElement.hasClass('active-submenu')) {
                    cmOptElement.addClass('active-submenu');
                }
            }

            cms[cmIndex].activeSubmenu = optIndex;
        }
    }

    function destroyCm(cmIndex)
    {
        if(cmIndex === undefined) {
            cmIndex = 0;
        }

        if(activeOpt != null && cmIndex <= activeOpt.cmIndex) {
            setCurrentActiveOver(-1, -1);
        }

        for(let i = cms.length - 1; i >= cmIndex; i--) {
            cms.pop().element.remove();
        }
    }

    function updateCm(cmIndex)
    {
        var cm = cms[cmIndex];

        var opts = getOpts(cmIndex, false);

        if(opts.length == 0) {
            opts = [
                {
                    'label': '&lt; Empty &gt;',
                    'disabled': true
                }
            ];
        }

        opts.forEach(function(opt, optIndex) {
            var cmOptElement = getOptElement(cmIndex, optIndex);

            var separator = opt.separator !== undefined;
            var icon = opt.icon !== undefined && opt.icon;
            var label = opt.label !== undefined && opt.label;
            var disabled = opt.disabled !== undefined && opt.disabled;
            var action = opt.action !== undefined && opt.action;
            var submenu = opt.submenu !== undefined && opt.submenu;

            if(cmOptElement.length) {
                cmOptElement.empty();
                cmOptElement.off();
                cmOptElement.removeClass();
            } else {
                cmOptElement = cmOptTemplate.clone();
                cmOptElement.appendTo(getOptContainer(cmIndex));
            }

            if(separator) {
                if(!cmOptElement.hasClass('context-menu-separator')) {
                    cmOptElement.addClass('context-menu-separator');
                }

                cmOptElement.append(
                    optSeparatorTemplate.clone()
                );

                return;
            }

            if(icon) {
                cmOptElement.append(
                    optIconTemplate.clone().addClass(opt.icon)
                );
            }

            if(label) {
                cmOptElement.append(
                    optTextTemplate.clone().html(opt.label)
                );
            }

            if(disabled) {
                if(!cmOptElement.hasClass('context-menu-disabled')) {
                    cmOptElement.addClass('context-menu-disabled');
                }
                return;
            }

            if(action) {
                cmOptElement.click(function() {
                    if(settings.autoClose) {
                        destroyCm();
                    } else {
                        destroyCm(cmIndex + 1);
                        setActiveOptSubmenu(cmIndex, -1);
                    }

                    opt.action(opt, cmIndex, optIndex);
                });
            }

            if(submenu) {
                cmOptElement.on('submenu', function() {
                    if(cm.activeSubmenu == optIndex) {
                        return;
                    }

                    var submenuIndex = cmIndex + 1;

                    setActiveOptSubmenu(cmIndex, optIndex);
                    destroyCm(submenuIndex);

                    showCm(opt.submenu, submenuIndex, {
                        x: cm.position.x + cm.element.outerWidth(),
                        y: cm.position.y + this.offsetTop - this.parentElement.scrollTop - parseInt(getOptContainer(cmIndex).css('padding-top'))
                    });
                });

                cmOptElement.mouseenter(function() {
                    setCurrentActiveOver(cmIndex, optIndex);
                    $(this).trigger('submenu');
                });

                if(!cmOptElement.hasClass('context-menu-submenu')) {
                    cmOptElement.addClass('context-menu-submenu');
                }
            } else {
                cmOptElement.mouseenter(function() {
                    setCurrentActiveOver(cmIndex, optIndex);
                    setActiveOptSubmenu(cmIndex, -1);
                    destroyCm(cmIndex + 1);
                });

                if(!cmOptElement.hasClass('context-menu-option')) {
                    cmOptElement.addClass('context-menu-option');
                }
            }

            cmOptElement.mouseleave(function() {
                if(activeOpt.cmIndex == cmIndex && activeOpt.optIndex == optIndex) {
                    setCurrentActiveOver(-1, -1);
                }
            });
        });

        var cmElementChildren = getOptElements(cmIndex);
        for(let i = cmElementChildren.length - 1; i >= opts.length; i--) {
            cmElementChildren.eq(i).remove();
        }
    }

    function updateCmPosition(cmIndex, repositionX, repositionY)
    {
        if(repositionX === undefined) {
            repositionX = true;
        }

        if(repositionY === undefined) {
            repositionY = true;
        }

        var cm = cms[cmIndex];

        if(cmIndex > 0) {
            var parentCmIndex = cmIndex - 1;
            var parentCm = cms[parentCmIndex];
            var activeSubmenu = getOptElement(parentCmIndex, parentCm.activeSubmenu);

            cm.position = {
                'x': parentCm.position.x + parentCm.element.outerWidth(),
                'y': parentCm.position.y + activeSubmenu[0].offsetTop - activeSubmenu[0].parentElement.scrollTop - parseInt(getOptContainer(cmIndex).css('padding-top'))
            };
        }

        if(repositionX) {
            var cmElementWidth = cm.element.outerWidth();
            if(cm.position.x - $(window).scrollLeft() + cmElementWidth >= $(window).innerWidth()) {
                cm.position.x -= cmElementWidth;

                if(cmIndex > 0) {
                    cm.position.x -= parentCm.element.outerWidth();
                }

                if(cm.position.x < $(window).scrollLeft()) {
                    cm.position.x = $(window).scrollLeft();
                }
            }

            cm.element.css('left', cm.position.x);
        }

        var cmElementHeight = cm.element.outerHeight();
        if(repositionY) {
            if(cm.position.y - $(window).scrollTop() + cmElementHeight >= $(window).innerHeight()) {
                cm.position.y -= cmElementHeight;

                if(cmIndex > 0) {
                    var paddingBottom = parseInt(getOptContainer(cmIndex).css('padding-bottom'));
                    var lastOpt = getOptElements(cmIndex).last();
                    var paddingTop = parseInt(getOptContainer(cmIndex).css('padding-top'));
                    cm.position.y += paddingBottom + paddingTop + lastOpt.outerHeight();
                }

                if(cm.position.y < $(window).scrollTop()) {
                    cm.position.y = $(window).scrollTop();
                }
            }

            cm.element.css('top', cm.position.y);
        }

        if(settings.maxHeight === null) {
            var leftoverHeight = cm.position.y - $(window).scrollTop();
            if(msie) {
                let leftoverWindowHeight = $(window).innerHeight() - leftoverHeight;
                if(cmElementHeight > leftoverWindowHeight) {
                    cm.element.css('height', leftoverWindowHeight);
                }
            } else {
                cm.element.css('max-height', 'calc(100vh - ' + leftoverHeight + 'px)');
            }
        } else {
            if(msie) {
                if(cmElementHeight > settings.maxHeight) {
                    cm.element.css('height', settings.maxHeight);
                }
            } else {
                cm.element.css('max-height', settings.maxHeight);
            }
        }

        if(settings.minWidth !== null) {
            cm.element.css('min-width', settings.minWidth);
        }

        cm.element.css('z-index', settings.zIndex + cmIndex);
    }

    function populateSearchResult(result, opts, keyword)
    {
        opts.forEach(function(opt) {
            var match = false;

            if(opt.label !== undefined && opt.label) {
                var label = opt.label.toLowerCase();

                if(label && label.indexOf(keyword) != -1) {
                    result.push(opt);
                    match = true;
                }
            }

            if(!match && opt.submenu !== undefined && opt.submenu.length) {
                populateSearchResult(result, opt.submenu, keyword);
            }
        });
    }

    function updateSearch(cmIndex)
    {
        var cm = cms[cmIndex];
        if(cm.search.input === null) {
            return;
        }

        var keyword = cm.search.input.val().trim();
        if(keyword == '') {
            cm.search.result = null;
            updateCm(cmIndex);
            return;
        }

        setCurrentActiveOver(-1, -1);

        var result = [];

        populateSearchResult(result, cm.opts, keyword.toLowerCase());
        cm.search.result = result;
    }

    function showCm(opts, cmIndex, position)
    {
        var cmElement = cmTemplate.clone();

        if(settings.searchBar && cmIndex == 0) {
            var cmSearch = cmSearchTemplate.clone();
            cmSearch.prependTo(cmElement);
        }

        if( settings.title ){
            cmElement.find('> span').html(settings.title);
        }

        var cm = {
            'element': cmElement,
            'position': position,
            'opts': opts,
            'activeSubmenu': -1,
            'search': {
                'input': cmSearch ? cmSearch.find('input') : null,
                'result': null
            }
        };
        cms.push(cm);

        getOptContainer(cmIndex).scroll(function() {
            setActiveOptSubmenu(cmIndex, -1);
            destroyCm(cmIndex + 1);
        });

        setCurrentActiveOver(-1, -1);
        activeOpt = {
            'cmIndex': cmIndex,
            'optIndex': -1
        };

        cmElement.appendTo(document.body);
        updateCm(cmIndex);
        updateCmPosition(cmIndex);

        if(cmSearch) {
            cm.search.input
                .on('input', function() {
                    destroyCm(cmIndex + 1);
                    updateSearch(cmIndex);
                    updateCm(cmIndex);
                    updateCmPosition(cmIndex, true, false);
                })
                .focus();
        }
    }

    function isSelectable(cmIndex, optIndex)
    {
        var opt = getOpts(cmIndex, false)[optIndex];
        return opt.separator === undefined && (opt.disabled === undefined || !opt.disabled);
    }

    function findSuitableSelectable(cmIndex, optIndex, reverse)
    {
        var optElements = getOptElements(cmIndex);

        if(optIndex >= optElements.length) {
            optIndex = 0;
        } else if(optIndex < 0) {
            optIndex = optElements.length - 1;
        }

        var currentOptIndex = optIndex;
        while(!isSelectable(cmIndex, currentOptIndex)) {
            currentOptIndex += reverse ? -1 : 1;

            if(currentOptIndex == optIndex) {
                return -1;
            }

            if(currentOptIndex >= optElements.length) {
                currentOptIndex = 0;
            } else if(currentOptIndex < 0) {
                currentOptIndex = optElements.length - 1;
            }
        }

        return currentOptIndex;
    }

    function activeUp()
    {
        if(activeOpt == null || activeOpt.optIndex == -1) {
            var cmIndex = cms.length - 1;
            var cmOpts = getOpts(cmIndex);

            if(cmOpts.length <= 0) {
                return;
            }

            setCurrentActiveOver(cmIndex, cmOpts.length - 1);
            return;
        }

        var previousOptIndex = findSuitableSelectable(activeOpt.cmIndex, activeOpt.optIndex - 1, true);

        if(previousOptIndex != -1) {
            setCurrentActiveOver(activeOpt.cmIndex, previousOptIndex);
        }
    }

    function activeDown()
    {
        if(activeOpt == null || activeOpt.optIndex == -1) {
            var cmIndex = cms.length - 1;
            var cmOpts = getOpts(cmIndex);

            if(cmOpts.length <= 0) {
                return;
            }

            setCurrentActiveOver(cmIndex, 0);
            return;
        }

        var nextOptIndex = findSuitableSelectable(activeOpt.cmIndex, activeOpt.optIndex + 1, false);

        if(nextOptIndex != -1) {
            setCurrentActiveOver(activeOpt.cmIndex, nextOptIndex);
        }
    }

    $(document).on('mousedown.scm contextmenu.scm', '.context-menu, .opt-text, .opt-icon, .opt-separator', function(e) {
        e.stopPropagation();
    });

    $(document).on('keydown.scm', function(e) {
        if(e.key == 'Escape' || e.which == 27) {
            destroyCm();
        }

        if(cms.length > 0) {
            if(e.key == 'ArrowUp' || e.which == 38) {
                e.preventDefault();
                activeUp();
            } else if(e.key == 'ArrowDown' || e.which == 40) {
                e.preventDefault();
                activeDown();
            } else if(e.key == 'Enter' || e.which == 13) {
                e.preventDefault();
                getOptElement(activeOpt.cmIndex, activeOpt.optIndex)
                    .click();
            } else if(e.key == 'ArrowLeft' || e.which == 37) {
                if(activeOpt != null && activeOpt.cmIndex > 0) {
                    e.preventDefault();
                    var parentCmIndex = activeOpt.cmIndex - 1;
                    var parentCm = cms[parentCmIndex];

                    var parentContextActiveSubmenu = parentCm.activeSubmenu;
                    destroyCm(activeOpt.cmIndex);
                    setActiveOptSubmenu(parentCmIndex, -1);

                    setCurrentActiveOver(parentCmIndex, parentContextActiveSubmenu);
                }
            } else if(e.key == 'ArrowRight' || e.which == 39) {
                if(activeOpt != null && activeOpt.optIndex != -1) {
                    var optElement = getOptElement(activeOpt.cmIndex, activeOpt.optIndex);

                    if(optElement.hasClass('context-menu-submenu')) {
                        e.preventDefault();
                        optElement.trigger('submenu');
                    }
                }
            }
        }
    });

    $(document).on('mousedown.scm', function() {
        destroyCm();
    });

    $(window).on('scroll.scm resize.scm', function() {
        destroyCm();
    });

    return {
        settings: settings,
        createMenu: function(opts, event) {
            destroyCm();
            showCm(opts, 0, { x: event.pageX, y: event.pageY });
        },
        destroyMenu: function() {
            destroyCm();
        },
        updateMenu: function(repositionX, repositionY) {
            cms.forEach(function(cm, cmIndex) {
                updateSearch(cmIndex);
                updateCm(cmIndex);
                updateCmPosition(cmIndex, repositionX, repositionY);
            });
        },
        getMenuOptions: function(cmIndex) {
            return cms[cmIndex].opts;
        },
        addMenuOption: function(cmIndex, opt, optIndex) {
            if(optIndex !== undefined) {
                cms[cmIndex].opts.splice(optIndex, 0, opt);
            } else {
                cms[cmIndex].opts.push(opt);
            }
        },
        addMenuOptions: function(cmIndex, opts, optIndex) {
            if(optIndex !== undefined) {
                Array.prototype.splice.apply(cms[cmIndex].opts, [optIndex, 0].concat(opts));
            } else {
                cms[cmIndex].opts = cms[cmIndex].opts.concat(opts);
            }
        },
        deleteMenuOption: function(cmIndex, optIndex) {
            cms[cmIndex].opts.splice(optIndex, 1);
        },
        setMenuOption: function(cmIndex, optIndex, opt) {
            cms[cmIndex].opts[optIndex] = opt;
        },
        setMenuOptions: function(cmIndex, opts) {
            cms[cmIndex].opts = opts;
        },
		isOpen: function() {
			return $(".context-menu").length !== 0;
		},
    };

}(navigator.appName == 'Microsoft Internet Explorer' || /Trident/.test(navigator.userAgent) || /rv:11/.test(navigator.userAgent));


}(window.jQuery, wp.customize));


/*!
 * Super Context Menu
 * Created by EZ17-1, pwnedgod @ github
 */
