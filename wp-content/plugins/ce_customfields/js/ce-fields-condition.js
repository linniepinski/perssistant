(function ($) {

    //Custom field on add new ad
    CE.fields_condition = Backbone.View.extend({
        el: 'div#customfield',
        template: $('#custom_field_template'),
        addedField : {}, //key : field_id, value : array of catId, which field belong to.
        initialize: function () {
            pubsub.on('ce:ad:addCat', this.addField, this);
            pubsub.on('ce:ad:removeCat', this.removeField, this);
            pubsub.on('ce:ad:afterSetupFields', this.loadField, this);
        },
        //Load all field on edit form loaded. When all cat provided.
        'loadField': function (model) {
            var catList = model.get("category");
            var that = this;
            
            if(catList.length >= 1){
                _.each(catList, function (cat) {
                    that.addField( { catId : String(cat.term_id),model : model });
                });
            }
        },
        // Add field when category added
        'addField': function (params) {
            var catId = params.catId;
            var model = params.model;
            var $fieldEl = this.getCatField(catId);
            var that = this;
            if ($fieldEl.length == 0) {
                var template = _.template(this.template.html(), {catId: catId, model: model}); //have to give model object, b/c we don't know what field key to get. Only template know.
                this.$el.append(template);
            }

            $fieldEl = this.getCatField(catId);
            $fieldEl.each(function(){
                var fieldID = $(this).attr("id");
                that.addedField[fieldID] = that.addedField[fieldID] || [];
                that.addedField[fieldID].push(catId);
            });
        },
        // Remove field when category removed
        'removeField': function (params) {

            var catId = params.catId;
            var model = params.model;
            var that = this;
            var $fieldEl = this.getCatField(catId);
            if ($fieldEl.length > 0) {
                $.each(that.addedField, function (key, value) {
                    var index = that.addedField[key].indexOf(catId);
                    if (index > -1) {
                        that.addedField[key].splice(index, 1); // remove the category which it belong to
                    }
                    if (that.addedField[key].length == 0) { //if it not belong to any category, remove it
                        $("#" + key).remove();
                    }
                });
            }
        },
        //Get all field element which have category data contain catId
        'getCatField': function (catId) {
            return this.$el.find('div[data-cats*=\'' + catId + '\']');
        }
    });
    //init
    $(document).ready(function () {
        new CE.fields_condition();
    });

})(jQuery);