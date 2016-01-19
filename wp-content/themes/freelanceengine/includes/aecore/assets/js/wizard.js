/**
 * site overview render chart for post type
 */
(function(Views, $, Backbone) {
    Views.Wizard = Backbone.View.extend({
        el: '#wizard-sample',
        events:{
            'click button#install_sample_data' : 'installSampleData',
            'click button#delete_sample_data'  : 'deleteSampleData'                
        },
        initialize: function() {
            console.log('init wizard');
            this.blockUi = new Views.BlockUi();
        },
        //install sample data
        installSampleData: function(event){
            event.preventDefault();
            var button = $(event.currentTarget),
                view = this;

            if ( button.hasClass('disabled') ) return false;
            
            $.ajax ({
                url  : ae_globals.ajaxURL,
                type : 'post',
                data : { 
                    action  : 'et-insert-sample-data'
                },
                beforeSend: function(){
                    view.blockUi.block(button);
                    button.addClass('disabled');
                },
                success : function(response){
                    view.blockUi.unblock();
                    button.removeClass('disabled');
                    if( response.success  ) {
                        $(event.currentTarget).after(
                            $('<button>').text(ae_wizard.delete_sample_data).attr({
                                'id'    : 'delete_sample_data',
                                'type'  : 'button',
                                'class' : 'primary-button'
                            })
                        );
                        $(event.currentTarget).remove();
                        alert(response.msg);
                        //window.location.href = response.redirect;
                        $("#overview-listplaces").fadeIn('slow');
                    }
                    else {
                        alert(ae_wizard.insert_fail);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(textStatus);
                    window.location.reload(true);
                }
            });
        },
        //uninstall sample data
        deleteSampleData: function(event){
            event.preventDefault();
            var button = $(event.currentTarget),
                view = this;
            if ( button.hasClass('disabled') ) return false;
            
            $.ajax ({
                url  : ae_globals.ajaxURL,
                type : 'post',
                data : { 
                    action  : 'et-delete-sample-data'
                },
                beforeSend: function(){
                    view.blockUi.block(button);
                    button.addClass('disabled');
                },
                success : function(response){
                    view.blockUi.unblock();
                    button.removeClass('disabled');
                    $(event.currentTarget).after(
                        $('<button>').text(ae_wizard.insert_sample_data).attr({
                            'id'    : 'install_sample_data',
                            'type'  : 'button',
                            'class' : 'primary-button'
                        })
                    );
                    $(event.currentTarget).remove();
                    $("#overview-listplaces").fadeOut('slow');
                    alert(response.msg);
                }
            });
        },
    });

    $(document).ready(function() {
        /**
         * render wizard
         */
        new Views.Wizard();
    });

})(window.AE.Views, jQuery, Backbone);