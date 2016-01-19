/**
 * render option view to control option settings, badge settings
 */
(function(Models, Views, Collections, $, Backbone) {
    /**
     * model option
     */
    Models.Badges = Backbone.Model.extend({
        action: 'ae-badge-sync',
        defaults: function() {
            return {
                name: "option_name",
                value: "option_value"
            };
        }
    });
    /**
     * Category list view
     */
    Views.CategoryList = Backbone.View.extend({
        //el: '#place-categories',
        events: {
            //'submit #form_new_tax' : 'onCreateCategory'
        },
        initialize: function() {
            var view = this;
            view.categories = new Collections.Categories();
            // 
            view.categoriesView = [];
            this.$('ul.list-tax li ').each(function() {
                var element = $(this),
                    id = element.attr('data-id'),
                    icon = element.attr('data-icon'),
                    tax = element.attr('data-tax'),
                    color = element.attr('data-color'),
                    name = element.find('input[name=name]').val(),
                    model = new Models.Category({
                        id: id,
                        ID: id,
                        color: color,
                        name: name,
                        icon: icon,
                        tax: tax
                    }),
                    itemView = new Views.CategoryListItem({
                        el: element,
                        model: model
                    });
                // add model to collection
                view.categories.push(model);
                // add view to list view
                view.categoriesView.push(itemView);
            });
            this.initSortable();
            // setup create category view
            var newView = new Views.NewCategoryItem({
                el: view.$('ul.add-category li'),
                model: new Models.Category({
                    color: 0,
                    icon: 'fa-map-marker',
                    tax: view.$('ul.add-category').attr('data-tax')
                }),
                disableEscape: true
            });
            newView.bind('onCreateSuccess', function(model, abc) {
                // create new view
                //console.log(model);
                var itemView = new Views.CategoryListItem({
                    model: model
                });
                // append view to list
                view.$el.find('ul.list-tax').append($(itemView.render().$el).hide().fadeIn());
            });
        },
        /* init sortable */
        initSortable: function() {
            this.$('ul.tax-sortable').nestedSortable({
                handle: '.sort-handle',
                listType: 'ul',
                items: 'li',
                maxLevels: 3,
                forcePlaceholderSize: true,
                placeholder: "placeholder",
                update: this.updateCategoriesOrder
            })
        },
        /* update categories positions */
        updateCategoriesOrder: function(event, ui) {
            var order = $(this).nestedSortable('serialize'),
                tax = $(this).attr('data-tax');
            var params = {
                url: ae_globals.ajaxURL,
                type: 'post',
                data: {
                    action: 'et_' + tax + '_sync',
                    method: 'sort',
                    content: {
                        order: order,
                        tax: tax,
                    }
                },
                beforeSend: function() {},
                success: function(resp, model) {},
                complete: function() {}
            }
            $.ajax(params);
        },
        onCreateSuccess: function(model, view) {},
        /* create new category */
        onCreateCategory: function(event) {
            event.preventDefault();
            var form = $(event.currentTarget),
                name = form.find('input[type=text]').val(),
                color = form.find('.cursor').attr('data'),
                icon = form.find('.icon').attr('data'),
                tax = form.find('input.tax-name').attr('data-tax'),
                view = this,
                model = new Models.Category({
                    name: name,
                    color: color,
                    icon: icon,
                    tax: tax
                });
            if (name == "") {
                form.find('input[type=text]').focus();
                return false;
            }
            model.save(model.attributes, {
                beforeSend: function() {
                    $(form).find('input[type=text]').val('');
                },
                success: function(model, resp) {
                    var el = $('#' + model.get('tax') + '_item_template')
                    var newCat = new Views.CategoryListItem({
                        model: model
                    });
                    view.$el.find('ul.list-tax').append($(newCat.render().$el).hide().fadeIn());
                }
            })
        }
    });
    /* Category Item Views */
    Views.NewCategoryItem = Backbone.View.extend({
        tagName: 'li',
        className: 'tax-item',
        defaults: {
            disableEscape: false
        },
        template: null,
        events: {
            // open color panel //
            'click .cursor': 'onOpenColorPanel',
            // open icon panel //
            'click .icon.trigger': 'onOpenIconsPanel',
            // change category name //
            'change input.tax-name': 'onChangeName',
            // create new sub category //
            'click button[type=submit]': 'onCreate',
            // enter to save category //
            'keyup input.tax-name': 'onKeyupInput',
            // user select an icon
            'click .icon-item ': 'selectIcon',
            // input a custom color
            'keypress .input-color' : 'inputColor', 
            // input a custom icon class
            'keypress .input-icon' : 'inputIcon'
        },
        initialize: function() {
            this.bind('onChangeColor', this.onChangeColor);
            this.bind('onChangeIcon', this.onChangeIcon);
            if (this.template == null) {
                this.template = _.template($('#' + this.model.get('tax') + '_item_form').html());
            }
        },
        onOpenColorPanel: function(event) {
            event.stopPropagation();
            $('.color-panel').fadeOut();
            // create panel
            var view = this;
            if (!view.$el.hasClass('colored')) {
                view.$el.addClass('colored').find('.color-panel').first().fadeIn();
                // cai gi day troi >.<
                view.$el.find('.color-item').click(function(event) {
                    var val = $(event.currentTarget).attr('data');
                    view.$el.find('input.tax-name').eq(0).css('color', val);
                    view.$el.find('.cursor > span.flag').eq(0).css('background-color', val);
                    view.trigger('onChangeColor', val);
                    view.$el.parent().removeClass('colored').find('.color-panel').fadeOut();
                });
                // omg :((
                view.$el.find('.color-picker').ColorPicker({
                    color: '#0000ff',
                    onSubmit: function(hsb, hex, rgb, el) {
                        view.$el.find('.color-picker').val('#'+hex);
                        var val = view.$el.find('.color-picker').val();
                        view.$el.find('input.tax-name').eq(0).css('color', val);
                        view.$el.find('.cursor > span.flag').eq(0).css('background-color', val);
                        view.trigger('onChangeColor', val);
                        view.$el.parent().removeClass('colored').find('.color-panel').fadeOut();
                        $(el).ColorPickerHide();
                    },
                    onShow: function (colpkr) {
                        $(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        var val = view.$el.find('.color-picker').val();
                        view.$el.find('input.tax-name').eq(0).css('color', val);
                        view.$el.find('.cursor > span.flag').eq(0).css('background-color', val);
                        view.trigger('onChangeColor', val);
                        view.$el.parent().removeClass('colored').find('.color-panel').fadeOut();
                        $(colpkr).fadeOut(500); 
                        return false;
                    },
                    onChange: function(hsb, hex, rgb) {
                        view.$el.find('.color-picker').val('#' + hex);
                        //$this.css('color' , '#'+hex );
                        view.$el.find('.color-picker').css('background', '#' + hex);
                        // $this.ColorPickerHide();
                    },
                    onBeforeShow: function() {
                        view.$el.find('.color-picker').ColorPickerSetColor(this.value);
                    }
                });
            }
            else{
                view.$el.removeClass('colored').find('.color-panel').fadeOut();
            }
        },
        /**
         * admin enter a custom color  for category
        */
        inputColor : function(event){
            // event.preventDefault();
            if(event.keyCode === 13) {
                var val = $(event.currentTarget).val(),
                    view = this;
                    console.log(val);
                    console.log(view.$el.html());
                    view.$el.find('input.tax-name').eq(0).css('color', val);
                    view.$el.find('.cursor > span.flag').eq(0).css('background-color', val);
                    view.trigger('onChangeColor', val);
                    view.$el.parent().removeClass('colored').find('.color-panel').fadeOut();
                    $('.colorpicker').fadeOut();
            }
        }, 
        onOpenIconsPanel: function(event) {
            // hide all panel first
            //$(".color-panel").fadeOut();          
            event.stopPropagation();
            // create panel
            var view = this,
                panel = $('<div class="color-panel icon-panel">'),
                icons = ['fa-camera', 'fa-taxi', 'fa-beer', 'fa-anchor', 'fa-cutlery', 'fa-exclamation-triangle', 'fa-road', 'fa-wheelchair', 'fa-car', 'fa-truck', 'fa-graduation-cap', 'fa-briefcase', 'fa-coffee', 'fa-book', 'fa-plane', 'fa-tachometer', 'fa-gamepad', 'fa-building', 'fa-shopping-cart', 'fa-video-camera', 'fa-tree', 'fa-rocket', 'fa-glass', 'fa-star', 'fa-map-marker'];
            /**
             * add icon panel 
            */
            if (!view.$el.hasClass('colored')) {
                for (var i = icons.length - 1; i >= 0; i--) {
                    var element = $('<div class="icon-item color-item" data="' + icons[i] + '">').append('<i class="fa ' + icons[i] + '"></i>');
                    panel.append(element);
                };
                panel.append(   '<div class="custom-icon"><lable>Get more icons at <a target="_blank" href="http://fontawesome.io/icons/" >Font Awesome</a>'+
                                '</lable><input class="input-icon" placeholder="e.g: fa-coffee" /> </div>');
                view.$el.addClass('colored').append(panel);
            } else {
                view.$el.removeClass('colored').find('.color-panel').fadeOut();
            }
        },
        /**
         * admin select an icon for category
        */
        selectIcon: function(event) {
            var val = $(event.currentTarget).attr('data'),
                view = this,
                icon = view.$el.find('div.icon i'),
                panel = $(event.currentTarget).parents('.icon-panel');
            
            panel.fadeOut('normal');
            view.$el.removeClass('colored');
            icon.attr('class', '');
            icon.attr('class', 'fa ' + val);
            // console.log(val);
            view.trigger('onChangeIcon', val, panel);
        },
        /**
         * admin enter a custom icon class for category
        */
        inputIcon : function(event){
            // event.preventDefault();
            if(event.keyCode === 13) {
                var val = $(event.currentTarget).val(),
                    view = this,
                    icon = view.$el.find('div.icon i'),
                    panel = $(event.currentTarget).parents('.icon-panel');
                panel.fadeOut('normal');
                view.$el.removeClass('colored');
                icon.attr('class', '');
                icon.attr('class', 'fa ' + val);
                // console.log(val);
                view.trigger('onChangeIcon', val, panel);
            }
        }, 

        onChangeIcon: function(val, panel) {
            // console.log(this.model);
            this.model.set('icon', val);
        },
        onChangeColor: function(val) {
            this.model.set('color', val);
        },
        onChangeName: function(event) {
            event.stopPropagation();
            this.model.set('name', $(event.currentTarget).val());
        },
        onKeyupInput: function(event) {
            if (event.which == 27 && !this.options.disableEscape) {
                this.$el.fadeOut('normal', function() {
                    $(this).remove()
                });
            }
            if (event.which == 13) {
                this.$('button[type=submit]').trigger('click');
            }
        },
        onCreate: function(event) {
            event.stopPropagation();
            var view = this,
                tax = view.$('input[type=text][name=name]').attr('data-tax'),
                value = view.$('input[type=text][name=name]').val();
            if (value == "") {
                view.$('input[type=text][name=name]').focus();
                return false;
            }
            this.model.set('name', value);
            this.model.set('tax', tax);
            this.model.save(this.model.attributes, {
                beforeSend: function() {
                    view.clearForm();
                },
                success: function(model, resp) {
                    if (resp.success) {
                        view.trigger('onCreateSuccess', view.model, view);
                        view.model = new Models.Category({
                            color: 0
                        });
                    } else {
                        alert(resp.msg);
                        view.trigger('onCreateFailed', view.model, view);
                    }
                }
            })
        },
        clearForm: function() {
            this.$('input[type=text]').val('').blur();
        },
        render: function() {
            this.$el.html(this.template());
            if (this.model.get('color') != 0) {
                this.$el.addClass('color-' + this.model.get('color'));
            }
            return this;
        }
    });
    /**
     * Category list item view
     */
    Views.CategoryListItem = Backbone.View.extend({
        tagName: 'li',
        className: 'tax-item',
        events: {
            'change input.tax-name': 'onChangeTermName',
            'click .act-open-form': 'onOpenSubForm',
            'click .cursor': 'onOpenColorPanel',
            'click .icon.trigger': 'onOpenIconsPanel',
            'click .act-del': 'onDelete', 
            // user select an icon
            'click .icon-item ': 'selectIcon', 
            // input a custom color
            'keypress .input-color' : 'inputColor',
            // input a custom icon class
            'keypress .input-icon' : 'inputIcon'
        },
        template: null,
        initialize: function() {
            this.bind('onChangeColor', this.onChangeColor);
            this.bind('onChangeIcon', this.onChangeIcon);
            this.blockUi = new Views.BlockUi();
            if (this.template == null) {
                this.template = _.template($('#' + this.model.get('tax') + '_item_template').html());
            }
        },
        onChangeTermName: function(event) {
            event.stopPropagation();
            var element = $(event.currentTarget);
            var view = this;
            this.model.save({
                name: element.val()
            }, {
                beforeSend: function() {
                    view.blockUi.block(view.$el)
                },
                success: function() {},
                complete: function() {
                    view.blockUi.unblock();
                }
            });
        },
        onOpenSubForm: function(event) {
            event.stopPropagation();
            event.preventDefault();
            // check the level
            var level = this.$el.parents('ul').length,
                limit = 3;
            if (level >= limit) {
                alert(ae_globals.texts.limit_category_level);
                return false;
            }
            // create 
            var view = this;
            var newModel = new Models.Category({
                parent: this.model.get('id'),
                color: this.model.get('color'),
                icon: this.model.get('icon'),
                tax: this.model.get('tax')
            });
            var newView = new Views.NewCategoryItem({
                model: newModel
            }),
                list = null,
                html = $('#' + this.model.get('tax') + '_item_form').html();
            if (view.$el.children('ul').length > 0) {
                list = view.$el.children('ul');
            } else {
                list = $('ul').appendTo(view.$el);
            }
            // create small view
            list.append(newView.render().$el);
            // handle of term has been created
            newView.bind('onCreateSuccess', function(model) {
                var itemView = new Views.CategoryListItem({
                    model: model
                });
                newView.remove();
                list.append(itemView.render().$el.hide().fadeIn());
            })
        },
        onOpenColorPanel: function(event) {
            event.stopPropagation();
            // create panel
            var view = this;
            $('.color-panel').fadeOut();
            if (!view.$el.hasClass('colored')) {
                view.$el.addClass('colored').find('.color-panel').first().fadeIn();
                view.$el.find('.color-item').click(function(event) {
                    var val = $(event.currentTarget).attr('data');
                    view.$el.find('input.tax-name').first().css('color', val);
                    view.$el.find('.cursor > span.flag').first().css('background-color', val);
                    view.trigger('onChangeColor', val);
                    view.$el.parent().removeClass('colored').find('.color-panel').fadeOut();
                });
                view.$el.find('.color-picker').first().ColorPicker({
                    color: '#0000ff',
                    onSubmit: function(hsb, hex, rgb, el) {
                        view.$el.find('.color-picker').val('#'+hex);
                        var val = view.$el.find('.color-picker').val();
                        view.$el.find('input.tax-name').first().css('color', val);
                        view.$el.find('.cursor > span.flag').first().css('background-color', val);
                        view.trigger('onChangeColor', val);
                        view.$el.parent().removeClass('colored').find('.color-panel').fadeOut();
                        $(el).ColorPickerHide();
                    },
                    onShow: function (colpkr) {
                        $(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        var val = view.$el.find('.color-picker').val();
                        view.$el.find('input.tax-name').eq(0).css('color', val);
                        view.$el.find('.cursor > span.flag').eq(0).css('background-color', val);
                        view.trigger('onChangeColor', val);
                        view.$el.parent().removeClass('colored').find('.color-panel').fadeOut();
                        $(colpkr).fadeOut(500); 
                        return false;
                    },
                    onChange: function(hsb, hex, rgb) {
                        view.$el.find('.color-picker').val('#' + hex);
                        //$this.css('color' , '#'+hex );
                        view.$el.find('.color-picker').css('background', '#' + hex);
                        // $this.ColorPickerHide();
                    },
                    onBeforeShow: function() {
                        view.$el.find('.color-picker').ColorPickerSetColor(this.value);
                    }
                });
            }
            else{
                view.$el.removeClass('colored').find('.color-panel').fadeOut();
            }
        },
        /**
         * admin enter a custom color  for category
        */
        inputColor : function(event){
            // event.preventDefault();
            if(event.keyCode === 13) {
                var val = $(event.currentTarget).val(),
                    view = this;
                    view.$el.find('input.tax-name').eq(0).css('color', val);
                    view.$el.find('.cursor > span.flag').eq(0).css('background-color', val);
                    view.trigger('onChangeColor', val);
                    view.$el.parent().removeClass('colored').find('.color-panel').fadeOut();
                    $('.colorpicker').fadeOut();
            }
        }, 
        onOpenIconsPanel: function(event) {
            event.stopPropagation();
            // create panel
            var view = this,
                panel = $('<div class="color-panel icon-panel">'),
                icons = ['fa-camera', 'fa-taxi', 'fa-beer', 'fa-anchor', 'fa-cutlery', 'fa-exclamation-triangle', 'fa-road', 'fa-wheelchair', 'fa-car', 'fa-truck', 'fa-graduation-cap', 'fa-briefcase', 'fa-coffee', 'fa-book', 'fa-plane', 'fa-tachometer', 'fa-gamepad', 'fa-building', 'fa-shopping-cart', 'fa-video-camera', 'fa-tree', 'fa-rocket', 'fa-glass', 'fa-star'];
            if (!view.$el.hasClass('colored')) {
                for (var i = icons.length - 1; i >= 0; i--) {
                    var element = $('<div class="icon-item color-item" data="' + icons[i] + '">').append('<i class="fa ' + icons[i] + '"></i>');
                    // set event
                    // element.bind('click', function(event) {
                    //     var val = $(event.currentTarget).attr('data'),
                    //         icon = view.$el.find('div.icon i');
                    //     panel.fadeOut('normal', function() {
                    //         $(this).remove()
                    //     });
                    //     view.$el.removeClass('colored');
                    //     icon.attr('class', '');
                    //     icon.attr('class', 'fa ' + val);
                    //     view.trigger('onChangeIcon', val, panel);
                    // });
                    panel.append(element);
                };

                panel.append(   '<div class="custom-icon"><lable>Get more icons at <a target="_blank" href="http://fontawesome.io/icons/" >Font Awesome</a>'+
                                '</lable><input class="input-icon" placeholder="e.g: fa-coffee" /> </div>');

                view.$el.addClass('colored').append(panel);
            } else {
                view.$el.removeClass('colored').find('.color-panel').remove();
            }
        },

        /**
         * admin select an icon for category
        */
        selectIcon: function(event) {
            var val = $(event.currentTarget).attr('data'),
                view = this,
                icon = view.$el.find('div.icon i').eq(0),
                panel = $(event.currentTarget).parents('.icon-panel');
            
            panel.remove();
            view.$el.removeClass('colored');
            icon.attr('class', '');
            icon.attr('class', 'fa ' + val);
            // console.log(val);
            view.trigger('onChangeIcon', val, panel);
        },
        /**
         * admin enter a custom icon class for category
        */
        inputIcon : function(event){
            // event.preventDefault();
            if(event.keyCode === 13) {
                var val = $(event.currentTarget).val(),
                    view = this,
                    icon = view.$el.find('div.icon i'),
                    panel = $(event.currentTarget).parents('.icon-panel');
                panel.fadeOut('normal');
                view.$el.removeClass('colored');
                icon.attr('class', '');
                icon.attr('class', 'fa ' + val);
                // console.log(val);
                view.trigger('onChangeIcon', val, panel);
            }
        }, 

        onChangeIcon: function(val, panel) {
            this.model.set('icon', val);
            console.log(this.model);
            if (typeof this.model.attributes.id == 'undefined') return false;
            var tempModel = new Models.Category({
                id: this.model.get('id'),
                ID: this.model.get('id'),
                icon: val,
                tax: this.model.get('tax')
            });
            var view = this;
            this.model.set('icon', val);
            tempModel.save({
                icon: val
            }, {
                beforeSend: function() {
                    view.blockUi.block(view.$el)
                },
                success: function(model, resp) {
                    if (resp.success) {} else {
                        alert(resp.msg);
                    }
                },
                complete: function() {
                    view.blockUi.unblock();
                }
            })
        },
        onChangeColor: function(val) {
            this.model.set('color', val);
            if (typeof this.model.attributes.id == 'undefined') return false;
            var tempModel = new Models.Category({
                id: this.model.get('id'),
                ID: this.model.get('id'),
                color: val,
                tax: this.model.get('tax')
            });
            var view = this;
            this.model.set('color', val);
            tempModel.save({
                color: val
            }, {
                beforeSend: function() {
                    view.blockUi.block(view.$el)
                },
                success: function(model, resp) {
                    if (resp.success) {} else {
                        alert(resp.msg);
                    }
                },
                complete: function() {
                    view.blockUi.unblock();
                }
            })
        },
        onDelete: function(event) {
            event.stopPropagation();
            event.preventDefault();
            var element = $(event.currentTarget);
            var view = this;
            this.model.deleteItem({
                beforeSend: function() {
                    view.blockUi.block(view.$el)
                },
                success: function(resp, model) {
                    if (resp.success) {
                        view.$el.fadeOut('normal', function() {
                            $(this).remove();
                        })
                    } else {
                        alert(resp.msg);
                    }
                },
                complete: function() {
                    view.blockUi.unblock();
                }
            });
        },
        render: function() {
            var html = this.template(this.model.attributes);
            var colorClass = 'color-' + this.model.get('color');
            this.$el.html(html).addClass(this.className).addClass(colorClass).attr('data-id', this.model.get('term_id')).attr('id', 'tax_' + this.model.get('term_id'));
            return this;
        }
    });
    Collections.Categories = Backbone.Collection.extend({
        model: Models.Category
    })
    Models.Category = Backbone.Model.extend({
        initialize: function() {},
        parse: function(resp) {
            if (resp.data.term) {
                var result = resp.data.term;
                result.id = result.term_id;
                return result;
            } else return {};
        },
        deleteItem: function(options) {
            this.sync('delete', this, options);
        },
        setColor: function(newColor, options) {
            this.sync('changeColor', this, options);
        },
        sync: function(method, model, options) {
            // build all params
            var params = {
                url: ae_globals.ajaxURL,
                type: 'post',
                data: {
                    action: 'et_' + model.get('tax') + '_sync',
                    method: method
                },
                beforeSend: function() {},
                success: function(resp, model) {},
                complete: function() {}
            }
            // build data
            var data = model.attributes;
            if (options.fields) {
                var data = {};
                _.each(options.fields, function(field) {
                    data[field] = model.get(fields);
                });
            }
            params.data.content = data;
            // build callback
            var beforeSend = options.beforeSend || function() {};
            var success = options.success || function(resp, model) {};
            var complete = options.complete || function() {};
            params.beforeSend = beforeSend;
            params.success = function(resp) {
                success(resp, model);
            }
            params.complete = complete;
            return $.ajax(params);
        },
    });
    /**
     * Button in backend
     *
     */
    Views.Backend_Button = Backbone.View.extend({
        el: '.backend-button-wrapper',
        events: {
            'click .backend-action-button' : 'onclickButton'
        },
        initialize: function() {
            var view = this;
            view.actions = view.$el.find('.backend-action-button').attr('data-action');
            view.blockUi = new AE.Views.BlockUi();
        },
        onclickButton: function(){
           //call a function when click to this button
        }
        
    });
    $(document).ready(function() {
        //var categories = new Views.CategoryList();
        var options = new Models.Options();
        $('form').validate();
        /**
         * settings control init
         */
        if ($('#settings').length > 0) {
            var options_view = new Views.Options({
                el: '#settings',
                model: options
            });
        }
        /**
         * badges control
         */
        if ($('#badge').length > 0) {
            var badges_view = new Views.Options({
                el: '#badge',
                model: new Models.Badges()
            });
        }
        /**
         * language list control
         */
        var lang_view = new Views.LanguageList({
            el: '#language-settings',
            model: options
        });
        /**
         * user list control
         */
        if (typeof Views.UserList !== 'undefined') {
            var user_view = new Views.UserList({
                el: $('.user-container')
            });
        }
        if ( $('.backend-action-button').length > 0){
           new Views.Backend_Button();
        }
        /**
         * init color picker
         */
        // $('.color-picker').each(function() {
        //     var $this = $(this);
        //     $this.ColorPicker({
        //         color: '#0000ff',
        //         onSubmit: function(hsb, hex, rgb, el) {
        //             $(el).val('#'+hex);
        //             $(el).ColorPickerHide();
        //         },
        //         onShow: function (colpkr) {
        //             $(colpkr).fadeIn(500);
        //             return false;
        //         },
        //         onHide: function (colpkr) {
        //             $(colpkr).fadeOut(500);
        //             return false;
        //         },
        //         onChange: function(hsb, hex, rgb) {
        //             $this.val('#' + hex);
        //             //$this.css('color' , '#'+hex );
        //             $this.css('background', '#' + hex);
        //             // $this.ColorPickerHide();
        //         },
        //         onBeforeShow: function() {
        //             $(this).ColorPickerSetColor(this.value);
        //         }
        //     });
        // });
    });
})(window.AE.Models, window.AE.Views, window.AE.Collections, jQuery, Backbone);