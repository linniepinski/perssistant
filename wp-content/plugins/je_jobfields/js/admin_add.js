(function($){

$(document).ready(function(){
	new jobFieldView();
});

var jobFieldView = Backbone.View.extend({
	el 			: '#job-fields-add',
	events 		: {
		'change #fadd input[name=type]' : 'changeType',
		'keyup .form-options li input:text:last' : 'addNewOption',
		'click #lst_fields .item .act-del' : 'deleteField',
		'click .form-option .del-opt' : 'deleteOption',
		'blur #fadd input.field_label'		: 'auto_set_name',
		'change input:radio' 	: 'showLimitField'

	},

	initialize 	: function(){

		$.validator.addMethod("noSpace", function(value, element) {
			var container = $(this.currentForm);

			// don't check spacy in field of form job. only check if is field name  = name of  form resume.
			if(!container.hasClass('form-resume')){
				return true;
			}
			if(value.length < 1)
				return false;
		  	return value.indexOf(" ") < 0 && value != "";
		}, 'Do not allow blank space in this field.');

		this.fieldValidate();

	},
	showLimitField : function(event){

		var target = $(event.currentTarget);
		if(target.val() == 'file' || target.val() == 'image'){
			$("span.file-limit").show();
		} else{
			$("span.file-limit").hide();
		}
	},
	changeType : function(event){
		var val = $(event.currentTarget).val();
		if (val == 'select' || val == 'checkbox' || val == 'radio')
			$('.form-item.form-drop').show();
		else
			$('.form-item.form-drop').hide();
	},

	addNewOption: function(event){
		var target 		= $(event.currentTarget),
			container 	= $('ul.form-options'),
			isLast 		= $(event.currentTarget).closest('li').is(':last-child');

		if ( $(event.currentTarget).val() != '' && isLast){
			_.templateSettings = {
			    evaluate    : /<#([\s\S]+?)#>/g,
				interpolate : /\{\{(.+?)\}\}/g,
				escape      : /<%-([\s\S]+?)%>/g
			};

			var clone = _.template($('#tl_option').html());
			container.append( clone({ id : container.find('li').length }) );
		}
	},

	deleteField : function(event){
		event.preventDefault();
	},

	deleteOption: function(event){
		if ( !confirm(et_fields.confirm_del) ) return false;
		var container = $(event.currentTarget).closest('li');

		container.fadeOut('normal', function(){ $(this).remove() });
	},
	'auto_set_name' : function (event){

    	var target = event.currentTarget;
    	var val = $(target).val().toLowerCase();
    	val = val.trim();
    	var res = val.split(" ");
    	var tem = res[0];
    	for(var i=1; i < res.length; i++){
    		if(res[i])
    			tem = tem + "_"+res[i];
    	}
    	var namefield 	= $(target).closest("form").find('input#field_name');
    	var name 		= namefield.val();
    	if( name=='' || name == ' ' )
    		namefield.val(tem);
	},
	'fieldValidate' : function(div){

			$("form#fadd").validate({
				rules: {
					// simple rule, converted to {required:true}
					field_label : {
						required : true
					},
					field_slug :{
						required:true,
						noSpace : true
					},
					name : {
						required: true,
						noSpace : true

					},
					type : {
						required : true
					}

				}
		});
	},

});

})(jQuery)