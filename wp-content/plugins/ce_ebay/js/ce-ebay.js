
  (function($){
      jQuery(document).ready(function($){   
      new CE_Ebay_Import;
      // $(".et-head-import a").click(function(event){
      //    var target =  $(event.currentTarget).attr('href');         
      //    $("div"+target).show();         
      //    return false;
      //  });
      $("a#delete_ebay_ad").click(function(){
        $("form.ebay-manage").submit();
        return false;
      });
      $("a.first-click").click(function(){
         $("body").find(".loading-blur").css('top','0 !important');
         $("div.ebay-box").hide();       
      })

  });

  var itemSearch = Backbone.Model.extend({
      defaults: {
        display_name: '123',
        title       : '',
        permalink   : '',
        currentPrice :'',
        ID          : '',
        count_text  : '',
        country     :'',
        the_post_thumbnail: '',
        viewItemURL : '',
        galleryURL  : '',
        pictureURLLarge : '',
        pictureURLThumbnail : '',
      }

   });
  CE.Views.EbaySearch = Backbone.View.extend({
   tagName : "tr",

   events :  {
      'click a.approve_view'     : 'approveView',
      'click a.reject_view'      : 'rejectView'
   },

   className : 'seller-item',
   
    
    template : _.template('<td align="center"><input id= "{{ itemId }}" class="select" type="checkbox" value="{{ itemId }}" name="imports[{{ stt }}][allow]" /><span class="span-wr response red"></span></td><td><img src="{{ galleryURL }}" /></td>' +
            '<td><a target ="_blank" href="{{ viewItemURL }}" class="add">{{ title }}</a>' +
            '<div class="more-info-item"> <i> {{ location }} {{ country }}</i> <br /> <i> {{ sellingStatus.currentPrice }}({{ currencyId }}) - {{ sellingStatus.convertedCurrentPrice }}({{ currencyConvert }})</i>' +
            '<br /> Seller: <i> {{ sellerInfo.sellerUserName }}</i>' + 
            '<br /> Expired: <i> {{ end_time }}</i> </div></td>' +
            '<td><div class="select-style et-button-select">'+ ebay_script.select_cat + '</div></td>' +
            '<td><div class="select-style et-button-select">'+ ebay_script.select_location + '</div></td>' +           
         
            '<input type="hidden" name="imports[{{ stt }}][viewItemURL]" value = "{{ viewItemURL }}" />  '+
            '<input type="hidden" name="imports[{{ stt }}][title]" value = "{{ title }}" />  '+
            '<input type="hidden" name="imports[{{ stt }}][endTime]" value = "{{ end_time }}" />"  '+
            '<input type="hidden" name="imports[{{ stt }}][timeLeft]" value = "{{ sellingStatus.timeLeft }}" />"  '+
            '<input type="hidden" name="imports[{{ stt }}][currentPrice]" value = "{{ sellingStatus.currentPrice }}" />  '+
            '<input type="hidden" name="imports[{{ stt }}][location]" value = "{{ location }}" />  '+
            '<input type="hidden" name="imports[{{ stt }}][galleryURL]" value = "{{ pictureURLLarge }}" />  '+
            '<input type="hidden" name="imports[{{ stt }}][galleryURLThumb]" value = "{{ galleryURL }}" />  '+
            '<input type="hidden" name="imports[{{ stt }}][end_time]" value = "{{ end_time }}" />  '+ 
            '<input type="hidden" name="imports[{{ stt }}][time_left]" value = "{{ time_left }}" />  '+           
            '<input type="hidden" name="imports[{{ stt }}][currencyId]" value = "{{ currencyId }}" />  '),
    template1: _.template('<td align="center"><input class = "select" type="checkbox" value="{{ ID }}" name="id[]" id="{{ ID }}" > <td>{{ the_post_thumbnail }}</td></td><td><a target="_blank" href="{{ guid }} ">{{ post_title }}</a> </td><td>{{ price }} </td><td>{{ et_location }}</td><td>{{ post_date }}</td>'),
    render : function(type){
      //console.log(this.template);
      if(type == 1)
         this.$el.append( this.template(this.model.toJSON()) );
      else if(type == 2)
         this.$el.append( this.template1(this.model.toJSON()) );
      //this.$el.append( this.template( this.model.toJSON() ) ).addClass(this.className).attr('data-id', this.model.get('id'));

      return this;
    }
});
  
var    CE_Ebay_Import  = Backbone.View.extend({
      el : 'div#ce_ebay_import',
      events : {
     // 'click  #save_setting'    : 'updateSettings',
      'click #test_setting'           : 'testconnect',
      'submit form.ebay-search'       : 'ebaySearchAd',
      'click a.page-action'           : 'ebaySearchAd',    
      'submit form.save-setting'      : 'ebaySaveSetting',       
      'change select[name=site]'      : 'ebayChangeSite', 
      'change select[name=category]'  : 'ebayChangeCategory', 
      'click a#ebay_manage_link'      : 'autoLoadAds',
      'click div.paging-wp a.page-numbers' : 'autoLoadAds',
      'submit form.ebay-manage'       : 'deleteEbayAds',    
      'change input#select_all'       : 'massCheck',
      'submit form#ebay-import'       : 'saveImportation',     
      'click .et-menu-content li a'   : 'changeMenu',  
    
    'change select[name=cat_all]'      : 'massChangeCat',
    'change select[name=location_all]' : 'massChangeType',
     
      // action for schedule
      'click #add-ebay-schedule'          : 'viewAddScheduleForm',
      //'click #submit_schedule'            : 'updateSchedule',
      
      'click #schedule_list .edit'        : 'editSchedule',
      'change input.number_day'           : 'updateDaySchedule',
      'click #schedule_list td a.power'   : 'toggleSchedule',
      'click #schedule_list td a.delete'  : 'deleteSchedule',

    },

    initialize : function () {
    this.styleSelector(this.$el);
    this.blockUi = new CE.Views.BlockUi();
    this.blockUI = new CE.Views.BlockUi({
            image : ebay_script.imgURL + '/loading_big.gif'
         });
   // this.schedule_list = JSON.parse($('#schedule_data').html());
   this.$('#search_author').autocomplete({
                source : JSON.parse($('#user_source').html()),
                select : function(event, ui){
                    $('#import_author').val(ui.item.id);
                }
            });
   
    },
    styleSelector : function(container){
        // apply custom look for select box
        $(container).find('.select-style select').each(function(){
            var $this = jQuery(this),
                title = $this.attr('title'),
                selectedOpt = $this.find('option:selected');
            
            if( selectedOpt.val() !== '' ){
                title = selectedOpt.text();
            }

            $this.css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
                .after('<span class="select">' + title.trim() + '</span>')
                .change(function(){
                    var val = jQuery('option:selected',this).text();
                    jQuery(this).next().text(val.trim());
                });
        });
    },

    
    ebaySearchAd :  function (event) {   
        event.preventDefault();
        var paged =1;
        var type = event.type;
        if(type =='click')
            var paged = $(event.currentTarget).attr('rel');

        var loadingButton = new CE.Views.LoadingButton({el: '#ebay_search'});

        var   keywords = $('form.ebay-search input#keywords').val(),
              user_id  = $('form.ebay-search input#user_id').val(),
              site     =  $('form.ebay-search select[name=site]').val(),
              category =  parseInt($('form.ebay-search select[name=category]').val()),
              view  = this;
        
        if(category == -1 && keywords == '' && user_id == ''){         
            $("button#ebay_search").next().html('<span class="red span-wr">Keywords or category are required.</span>');        
            return false;
        } else {
            $("button#ebay_search").next().html('');
        }
        var data = $("form.ebay-search").serialize();
        data  = data + '&paged=' + paged; 
        $.ajax({       
            url   : ebay_script.ajax_url,
            type  : 'POST',          
            data : data,          
            beforeSend : function(){
                loadingButton.loading();
                if(type =='click')
                view.blockUI.block($('div#search-results'));
            },
            success: function(resp) {
                loadingButton.finish();
                $('div#search-results').removeClass('hide');
                $('div.import-tb-container table tbody').html('');
                $('div#search-results div.pager-wrap').html('');
                if(resp.success){
                    view.blockUI.unblock();
                    var items = resp.data || [];
                    if(items.length > 0){

                       
                        _.each( items, function(item,i){
                            item['stt'] = i;                                                   
                            var view = new CE.Views.EbaySearch({ model : new itemSearch( item )});                                         
                            $('div.import-tb-container table tbody').append( view.render(1).$el );
                        });
                        view.styleSelector($('.import-tb-container > table'));

                        // append paginarot html 
                        if(resp.paginationOutput.totalPages > 1){
                            var html =  view.createPaginator(resp.paginationOutput);               
                            $('div#search-results div.pager-wrap').append(html);
                        }
                       

                    } else {
                        $('div#search-results form table tbody').html('<tr align="center"><td align="center" colspan ="5">No items found for your search.</td></tr>');  
                    }
                } else {
                    $('div#search-results form table tbody').html('<tr align="center"><td align="center" colspan ="7">No items found for your search.</td></tr>');  
           
           }
         }
      });    
   return false;
   },

   massCheck : function(e){
      var   isChecked   = $(e.currentTarget).is(':checked'),
            table       = $(e.currentTarget).closest('table');

      if ( isChecked )  table.find('input.select').attr('checked','checked');      
      else  table.find('input.select').removeAttr('checked');
    },
    updateSettings : function (event) {
        event.preventDefault();
        var data  = $('#linkedin-settings form').serialize(),
        blockUI = new CE.Views.BlockUi();
        // ajax update job alert setting
        $.ajax ({
            type : 'post',
            url : 'admin-ajax.php',
            data : data,
        beforeSend : function  () {
            blockUI.block($(event.currentTarget));
        },
        success : function () { 
            blockUI.unblock();
        }
      });
      
    },
    testconnect:function(event){
      event.preventDefault();
      var   data  = $('form.save-setting').serialize(),
            app_id = $('form.save-setting').find('input#app_id').val(),
            blockUI = new CE.Views.BlockUi();
      // ajax update job alert setting
      $.ajax ({
        type : 'post',
        url : 'admin-ajax.php',
        data : {action:'ebay_connecting',app_id : app_id},
        beforeSend : function  () {
          blockUI.block($(event.currentTarget));
        },
        success : function (res) {  
          blockUI.unblock();
          alert(res);
        }

      });
      
      
    },
   ebaySaveSetting : function (event){
    
      var   target      = $(event.currentTarget),
            app_id      = $("input#app_id").val(),
            network_id  = $("select#network_id").val();
           
        data = target.serialize();
        var blockUI = new CE.Views.BlockUi();     
        $.ajax({
            url   : ebay_script.ajax_url,
            type  : 'POST',
            data  :  data,
            beforeSend: function(){
                blockUI.block("button#save_setting");     
            },
            success : function(resp){
                blockUI.unblock();
                $("button#test_setting").attr('rel',resp.data.app_id);         
            }
      });
      return false;
   },
    ebayChangeSite : function(e){
        var target  = $(e.currentTarget);
        var blockUI = new CE.Views.BlockUi();
        var value   = target.val();
        var site    = $("option[value="+value+"]").attr('rel');

         $.ajax({
            url   : ebay_script.ajax_url,
            type  : 'post',
            data  :  {
               action  : 'ebay-get-categories',
               site : site
            },
            beforeSend: function(){
            	var loading = target.closest('.form-item').next().find('#wrap-cat');
              	blockUI.block(loading);     
            },
            success : function(resp){
               blockUI.unblock();
               target.closest('.form-item').next().html(resp.data); 
            }
         });
    
    },
    
    ebayChangeCategory : function(event){
		var target 	= $(event.currentTarget);
		var value 	= target.val();
		var opt 	= target.find('option:selected');
		var text 	= opt.text();     
		target.next().html(text);

    },
    autoLoadAds : function(event){
        $("div#ebay-manage").show();
        var target   = $(event.currentTarget),
            type     = event.type,
            view     = this,
            paged    = 1;
      
        if( target.hasClass('page-numbers') )
            var paged = parseInt(target.text());   
        $.ajax({
            url   : ebay_script.ajax_url,
            type  : 'post',
            data  :  {
                action  : 'ebay-load-ads',            
                paged : paged
            },
            beforeSend: function(){          
                view.blockUi.block($("div#iads"));
            },
            success : function(resp){
                $('form.ebay-manage table tbody').html('');
                if(resp.success){
                    $("tr.item-static").remove();
                    
                    _.each( resp.data, function(item,i){                             
                        var view = new CE.Views.EbaySearch({model : new itemSearch( item )});
                        $('form.ebay-manage table tbody').append( view.render(2).$el );
                    });
                    $("div.row-pagination").html('');
                    $("div.row-pagination").html(resp.paging);
                } else {
                    $("form.ebay-manage table tbody").html("<tr><td  colspan ='6'>" + resp.msg + "</td></tr>");
                }
                view.blockUi.unblock();
            }
      });
      return false;
   },
   deleteEbayAds : function(event){
      var   view = this,
            target = $(event.currentTarget),
            data = target.serialize();
            
            var flag = true;
            $( ".ebay-manage input.select:checked" ).each(function() {
                flag = false;
            
            });
            if(flag){
                alert('Please select an item to delete.');
                return false;
            }
            // if(data.length<23 || data == ''){
            //     alert('The items selected is empty');
            //     return false;
            // }

        $.ajax({
            url   : ebay_script.ajax_url,
            type  : 'post',
            data  :  data,
            beforeSend: function(){
                view.blockUi.block($("form.ebay-manage div.import-tb-container"));
            },
            success : function(resp){
            _.each( resp.data, function(record){
                var target = $("input#"+record).closest('tr');               
                $(target).delay(1500).fadeOut('normal', function(){ $(this).remove() });
            });
            $("a#ebay_manage_link").trigger("click");          
            }
        });
        return false;
    },

   saveImportation : function(e){
        e.preventDefault();
        var flag = true;
        $( ".ebay-import input.select:checked" ).each(function() {
            flag = false;
        
        });
        if(flag){
            alert('Please select an item to import.');
            return false;
        }


        var data = $('form#ebay-import').serialize(),
        loadingBtn = new CE.Views.LoadingButton({el : $('form#ebay-import').find('button') }),
        view = this;    

        if(data == null || data == '' || data.length == 0)
        return false;
       
        var params = {
            url : ebay_script.ajax_url,
            type : 'post',
            data: data,
            beforeSend: function(){
                loadingBtn.loading();
            },
            success : function(resp){
                loadingBtn.finish();  
                _.each( resp.data, function(record){            
                    if(typeof record.item !== "undefined"){
                        $("input#"+record.item.id).next().html('Exists!');
                        $("input#"+record.item.id).closest('tr').find('select').removeAttr('name');
                        $("input#"+record.item.id).prop( "checked", false );                
                    } else  {
                        $("input#"+record.post.id).next().html('<span class=" response green">Done!</span>');                  
                    }
             });
        }
      }
      $.ajax(params);
    },

    changeMenu : function(event){
        view = this;      
        event.preventDefault();  
        $("div.ebay-box").hide();
        var target = $(event.currentTarget),
        menu = target.attr('href');
        target.closest('ul').find('a').removeClass('active');
        target.addClass('active');
        $("div.ebay-box").addClass('hide');
        $(menu).removeClass('hide');        
        $(menu).show();
    },

    createPaginator : function(input){
        var entriesPerPage  = parseInt(input.entriesPerPage),
            pageNumber      = parseInt(input.pageNumber),
            totalEntries    = parseInt(input.totalEntries),
            totalPages      = parseInt(input.totalPages),
            tailPage        = Math.min(pageNumber + 5, totalPages),
            headPage        = Math.max(pageNumber - 5, 1);

        var prev    =  pageNumber - 1,
            next    =  pageNumber + 1,
            html    =  '';    
        if(totalPages == 1)
            return
        html += '<ul class="pagination">';
        if(pageNumber > 1)
            html += '<li><a class="page-numbers page-action" href="#" rel="' + prev + '">Prev</a></li>';
        for(var i = headPage; i < tailPage; i++){
            if(pageNumber == i)
               html +='<li><span class="page-numbers page-action current" href="#"> ' + i + '</span></li>';
            else 
               html +='<li><a class="page-numbers page-action" href="#" rel="'+ i +'"> ' + i + '</a></li>';
      }
        if(tailPage < totalPages)
            html += '<li><span>...</a></li>';
        if(pageNumber < totalPages)
            html += '<li><a class="page-numbers page-action" href="#" rel="' + next + '">Next</a></li>';
        html += '</ul>';
    return html;
    },
    // schedule function 
    viewAddScheduleForm : function (event) {
         event.preventDefault();
         var $currentTarget   =  $(event.currentTarget),
            parent          =  $currentTarget.parents('.module'),
            data    =  {
                        'keywords'    : '',                             
                        'site'        : '1', 
                        'user_id'     : '', 
                        'number'      : '',
                        'category'    : '', 
                        'schedule_id' : '',
                        'author'      : '',
                        'import_author' : '',
                        'ON'        : 1 ,
                        'ad_category' : '',
                        'product_cat' : ''                  
                     };
            this.newScheduleForm( parent, data );
      },

      newScheduleForm : function ( parent , data) {
        $('#form-schedule').remove ();

        var template   =  _.template( $('#schedule_template').html() );

        parent.append(template(data)).slideDown();    
         
        $(".ebay_site option[value="+data.site+"]").prop("selected", true);
        $(".ebay_category option[value="+data.category+"]").prop("selected", true);

        $("#ad_category option[value="+data.ad_category+"]").prop("selected", true);  
        $("#product_cat option[value="+data.product_cat+"]").prop("selected", true);      
        $("#schedule_fjt option[value="+data.fjt+"]").prop("selected", true);

        $('.job-import-review').hide();
        $('#schedule_jobtitle').focus();        
        
         jQuery(".select-schedule select").each(function(){


            var title = jQuery(this).attr('title');
         
            var arrow = "";
            if (jQuery(".select_cat select").attr('arrow') !== undefined) 
               arrow = " " + jQuery(".select-style select").attr('arrow');

            if( jQuery('option:selected', this).val() != ''  ) title = jQuery('option:selected',this).text() + arrow ;


            jQuery(this)
               .css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
               .after('<span class="select">' + title + arrow + '</span>')
               .change(function(){
                  val = jQuery('option:selected',this).text() + arrow;
                  jQuery(this).next().text(val);
               });
         });
      },
      updateSchedule : function (event) {
			// event.preventDefault();

			// var $currentTarget	=	$(event.currentTarget), 
			// 	schedule_list	=	this.schedule_list,
			// 	update			=	$('#schedule_form').find('.schedule_id').val(),
			// 	schedule	=	$('#schedule_form').serialize(),
   //              cat_name    =   $('#schedule_form select.ebay_category option:selected').text(),
   //              site_name    =   $('#schedule_form select.ebay_site option:selected').text(),
			// 	view		=	this;
   //              schedule    = schedule + '&cat_name='+ cat_name +"&site_name="+site_name;	

			// var params = {
			// 		url 	:'admin-ajax.php',
			// 		type 	: 'post',
			// 		data 	:	schedule ,

			// 		beforeSend : function(){
			// 			view.blockUi.block($('#submit_schedule'));
			// 		},

			// 		success : function(resp){
			// 			$('#schedule_error').remove();
			// 			view.blockUi.unblock();

			// 			if(!resp.success) {
			// 				var error_msg = '', i = 0;
			// 				for(i = 0 ; i < resp.msg.length ; i++)
			// 					error_msg	+=	resp.msg[i]	;
			// 				$currentTarget.parents('.form-button').append('<div id="schedule_error" class="error">'+error_msg+'</div>');

			// 			} else {
			// 				var template	=	_.template($('#et_schedule_list').html()),
			// 					data		=	resp.data;


   //                      	schedule_list[resp.data.schedule_id]	=	data;
			// 				$('#form-schedule').fadeOut(500);
			// 				if(update == '') {
			// 					var tr = document.createElement("tr");
			// 					tr.setAttribute ('id', 'schedule-'+resp.data.schedule_id);
			// 					tr.innerHTML =	template(resp.data);
			// 					$('#schedule_list').find('tbody').append(tr);
			// 				}
			// 				else 
			// 					$('#schedule_list').find('#schedule-'+resp.data.schedule_id).html(template(resp.data));
			// 			}
			// 		}
			// };
			// $.ajax (params);

		},

    editSchedule : function (event) {
        event.preventDefault();
        var currentTarget   = $(event.currentTarget),
        schedule_id         = currentTarget.parents('td').find('.schedule_id').val(),
        data                = this.schedule_list[schedule_id];        
        this.newScheduleForm(currentTarget.parents('.module'), data );
    },
    // Update number days for run schedule
    updateDaySchedule : function(event){
        var target  = $(event.currentTarget),
            view    = this,
            days    = target.val();           
        if(isNaN(days)){
            alert('Please input a number!');
            return ;
        }
        $.ajax({       
            url   : ebay_script.ajax_url,
            type  : 'POST',          
            data : {
                action      :'ebay-set-days-run',
                number_days : days 
            },          
            beforeSend : function(){
                view.blockUi.block(target);        
            },
            success: function(resp) { 
                view.blockUi.unblock(target);  
                if(resp.success){

                }
            }
        });
    },

    toggleSchedule : function(event){
        var target  = $(event.currentTarget),
            id      = target.attr('data-id'),
            view    = this;
        $.ajax({       
            url   : ebay_script.ajax_url,
            type  : 'POST',          
            data : {
                action  : 'ebay-toggle-schedule',
                id      : id 
            },          
            beforeSend : function(){
                view.blockUi.block(target);        
            },
            success: function(resp) { 
                view.blockUi.unblock(target);  
                if(resp.success){
                    target.closest('tr').toggleClass('off');
                }
            }
        });
        return false;
    },
    deleteSchedule : function (event){
        var target  = $(event.currentTarget),
            id      = target.attr('data-id'),
            view    = this;
        $.ajax({       
            url   : ebay_script.ajax_url,
            type  : 'POST',          
            data : {
                action  : 'ebay-delete-schedule',
                id      : id 
            },          
            beforeSend : function(){
                view.blockUi.block(target);        
            },
            success: function(resp) { 
                view.blockUi.unblock(target);  
                if(resp.success)                
                    $(target.closest('tr')).fadeOut(500, function() { $(this).remove();});
            }
        });
        return false;
    },
    massChangeCat : function(e){       
            var value           = $(e.currentTarget).val();
            var table           = $('.import-tb-container > table');
            var affected_rows   = _.map( table.find('tr:not(:eq(0)) input.select:checked'), function(element){
                return $(element).closest('tr').find('select.set-cat');
            });

            _.each(affected_rows, function(data, index){
                $(data).val(value);
                $(data).trigger('change');
            });
        },

    massChangeType : function(e){
        console.log('b');
        var value           = $(e.currentTarget).val();
        var table           = $('.import-tb-container > table');
        var affected_rows   = _.map( table.find('tr:not(:eq(0)) input.select:checked'), function(element){
            return $(element).closest('tr').find('select.set-local');
        });

        _.each(affected_rows, function(data, index){
            $(data).val(value);
            $(data).trigger('change');
        });
    },

  });
 
})(jQuery);
