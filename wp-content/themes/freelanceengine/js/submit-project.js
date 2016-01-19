(function($, Models, Collections, Views) {
    /*
     *
     * S U M I T  P R O J E C T  V I E W S
     *
     */
    Views.SubmitProject = Views.SubmitPost.extend({
        onAfterInit: function() { alert("AAAA");
            var view = this;
            if ($('#edit_postdata').length > 0) {
                var postdata = JSON.parse($('#edit_postdata').html());
                this.model = new Models.Project(postdata);
                this.model.set('renew', 1);
                this.setupFields();
            } else {
                this.model = new Models.Project();
            }
            view.carousels = new Views.Carousel({
                el: $('#gallery_container'),
                model: view.model,
                extensions: 'pdf,doc,docx,png,jpg,gif,zip'
            });
            console.log(this.model);
            //new skills view
            new Views.Skill_Control({
                model: this.model,
                el: view.$('.skill-control')
            });
            this.$('.multi-tax-item').chosen({
                width: '95%',
                max_selected_options: parseInt(ae_globals.max_cat),
                inherit_select_classes: true
            });
        },
        onLimitFree: function() {
            AE.pubsub.trigger('ae:notification', {
                msg: ae_globals.limit_free_msg,
                notice_type: 'error',
            });
        },
        onAfterShowNextStep: function(step) {
            $('.step-heading').find('i.fa-caret-down').removeClass('fa-caret-right fa-caret-down').addClass('fa-caret-right');
            $('.step-' + step).find('.step-heading i.fa-caret-right').removeClass('fa-caret-right').addClass('fa-caret-down');
        },
        onAfterSelectStep: function(step) {
            $('.step-heading').find('i').removeClass('fa-caret-right fa-caret-down').addClass('fa-caret-right');
            step.find('i').removeClass('fa-caret-right').addClass('fa-caret-down');
        },
        // on after Submit auth fail
        onAfterAuthFail: function(model, res) {
            AE.pubsub.trigger('ae:notification', {
                msg: res.msg,
                notice_type: 'error',
            });
        },
        onAfterPostFail: function(model, res) {
            AE.pubsub.trigger('ae:notification', {
                msg: res.msg,
                notice_type: 'error',
            });
        },
        onAfterSelectPlan: function($step, $li) {
            var label = $li.attr('data-label');
            $step.find('.text-heading-step').html(label);
        }
    });
    Views.SubmitBibPlan = Views.SubmitPost.extend({
        onAfterInit: function() {
            var view = this;
            if ($('#edit_postdata').length > 0) {
                var postdata = JSON.parse($('#edit_postdata').html());
                this.model = new Models.Bid(postdata);
                this.model.set('renew', 1);
                this.setupFields();
            } else {
                this.model = new Models.Bid();
                this.model.set('post_parent', 2858);
            }
            //new skills view
            new Views.Skill_Control({
                model: this.model,
                el: view.$('.skill-control')
            });
            this.$('.multi-tax-item').chosen({
                width: '95%',
                max_selected_options: parseInt(ae_globals.max_cat),
                inherit_select_classes: true
            });
        },
        onLimitFree: function() {
            AE.pubsub.trigger('ae:notification', {
                msg: ae_globals.limit_free_msg,
                notice_type: 'error',
            });
        },
        onAfterShowNextStep: function(step) {
            $('.step-heading').find('i.fa-caret-down').removeClass('fa-caret-right fa-caret-down').addClass('fa-caret-right');
            $('.step-' + step).find('.step-heading i.fa-caret-right').removeClass('fa-caret-right').addClass('fa-caret-down');
        },
        onAfterSelectStep: function(step) {
            $('.step-heading').find('i').removeClass('fa-caret-right fa-caret-down').addClass('fa-caret-right');
            step.find('i').removeClass('fa-caret-right').addClass('fa-caret-down');
        },
        // on after Submit auth fail
        onAfterAuthFail: function(model, res) {
            AE.pubsub.trigger('ae:notification', {
                msg: res.msg,
                notice_type: 'error',
            });
        },
        onAfterPostFail: function(model, res) {
            AE.pubsub.trigger('ae:notification', {
                msg: res.msg,
                notice_type: 'error',
            });
        }
    });
})(jQuery, AE.Views, AE.Models, AE.Collections);