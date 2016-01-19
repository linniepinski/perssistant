(function($){
	CE.fields_edit = Backbone.View.extend({
		el : '#modal_edit_ad',

		initialize : function () {
			pubsub.on('ce:ad:afterSetupFields',this.fieldsEdit, this );
			pubsub.on('ce:post:submitAdMore',this.submitAdmore,this);
		},
		'fieldsEdit' : function (model){
			$("form.edit-ad-form").find('input[type=radio]').each(function(){
				var name = $(this).attr('name');
				var value = model.get(name);
				$("input[value="+value+"]").prop("checked",true);
			});
		},
		'submitAdmore' : function(model){
			if(typeof  model.get('ID') == 'undefined')
				return;
			var t 		= ce_fields.types || [];
			var list = t.split(",");
			var temp = new Array();
			for(var i =0; i < list.length; i++){
				model.unset(key);
				var key = list[i];
 					temp[key] = new Array();
 				$("form.edit-ad-form").find('input[name='+ list[i] +']:checked').each(function(){

					var name 	= $(this).attr('name');
					var value 	= $(this).val();
					temp[key].push(value);
					temp.push(value);
				});

				model.set(key,temp[key]);
			}

			return false;
			$("form.edit-ad-form").find('input[type=radio]').each(function(){
				if($(this).prop("checked")){
					var checked = $(this).val();
					var name = $(this ).attr('name');
						model.set(name,checked);
				}
			});
		}
	});

	CE.fields_add = Backbone.View.extend({
		el : 'div#post-classified',
		initialize : function () {
			pubsub.on('ce:post:submitAdMore',this.setAdMeta,this);
		},
		'setAdMeta' : function(model){
			if(typeof  model.get('ID') !== 'undefined')
				return;

			var t 		= ce_fields.types || [];
			var list = t.split(",");
			var temp = new Array();
			for(var i =0; i < list.length; i++){
				var key = list[i];
 					temp[key] = new Array();
 				$("form#ad_form").find('input[name='+ list[i] +']:checked').each(function(){
					var name 	= $(this).attr('name');
					var value 	= $(this).val();
					temp[key].push(value);
				});
				model.set(key,temp[key]);
			}

			$("form#ad_form").find('input[type=radio]').each(function(){
				if($(this).prop("checked")){
					var checked = $(this).val();
					var name = $(this ).attr('name');
						model.set(name,checked);
				}
			});
		}
	});
	$(document).ready(function(){
		new CE.fields_edit;
		new CE.fields_add;
	});
})(jQuery);