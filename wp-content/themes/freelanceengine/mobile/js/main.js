(function($, Models, Collections, Views) {
    jQuery(document).ready(function($) {
        //init
        var hash = location.hash;
        if (hash.indexOf('#tab_') >= 0) {
            $('.tab-profile').hide();
            $(location.hash).show();
            $("#right_menu").trigger('click');
        }
        $('.slider-ranger').slider();
        var menuLeft = document.getElementById('cbp-spmenu-s1'),
                    menuRight = document.getElementById('cbp-spmenu-s2'),
                    showLeftPush = document.getElementById('left_menu'),
                    showRightPush = document.getElementById('right_menu'),
                    body = document.body;
        if ($('#left_menu').length > 0) {
            showLeftPush.onclick = function(e) {
                e.preventDefault();    
                classie.toggle(this, 'active');    
                classie.toggle(body, 'cbp-spmenu-push-toright');    
                classie.toggle(menuLeft, 'cbp-spmenu-open');
                classie.toggle(this, 'open');    
                disableOther('showLeftPush');
            };
        }
        if ($('#right_menu').length > 0) {
            showRightPush.onclick = function(e) {
                e.preventDefault();    
                classie.toggle(this, 'active');    
                classie.toggle(body, 'cbp-spmenu-push-toleft');    
                classie.toggle(menuRight, 'cbp-spmenu-open');    
                disableOther('showRightPush');
            };
        }
        $(window).on("hashchange", function() {
            var hash = location.hash;
            if (hash.indexOf('#tab_') >= 0) {
                $('.tab-profile').hide();
                if ($(location.hash).length > 0) {
                    $(location.hash).show();
                }
            }
        });
        $('.link-menu-nav, .mb-change-password').click(function() {
            $("#right_menu").trigger('click');
        });

        function disableOther(button) {    
            if (button !== 'showLeftPush') {        
                classie.toggle(showLeftPush, 'disabled');    
            }    
            if (button !== 'showRightPush') {        
                classie.toggle(showRightPush, 'disabled');    
            }
        }
        $('.change-link-login').click(function(e) {
            $('.section-forgot').fadeOut(300);
            $('.section-register').fadeOut(300, function(e) {
                $('.section-login').fadeIn(300);
            });
        });
        $('.change-link-forgot').click(function(e) {
            $('.section-register').fadeOut(300);
            $('.section-login').fadeOut(300, function(e) {
                $('.section-forgot').fadeIn(300);
            });
        });
        $('.change-link-register').click(function(e) {
            $('.section-forgot').fadeOut(300);
            $('.section-login').fadeOut(300, function(e) {
                $('.section-register').fadeIn(300);
            });
        });
        $('.show-search-advance').click(function(e) {
            $('#advance-search').slideDown(300);
            $(this).fadeOut(300, function(e) {
                $('.hide-search-advance').fadeIn(300);
            });
        });
        $('.hide-search-advance, .hide-advance-search').click(function(e) {
            e.preventDefault();
            $('#advance-search').slideUp(300);
            $('.hide-search-advance').fadeOut(300, function(e) {
                $('.show-search-advance').fadeIn(300);
            });
        });
        $('.btn-list-bidder').click(function(e) {
            $('.btn-tabs-wrapper ul li').removeClass('active');
            $(this).addClass('active');
            $('.comment-list-wrapper').fadeOut(300, function(e) {
                $('.list-history-bidders').fadeIn(300);
            });
        });
        $('.btn-list-cmt').click(function(e) {
            $('.btn-tabs-wrapper ul li').removeClass('active');
            $(this).addClass('active');
            $('.list-history-bidders').fadeOut(300, function(e) {
                $('.comment-list-wrapper').fadeIn(300);
            });
        });

        $(".chosen").each(function(){
            var data_chosen_width = $(this).attr('data-chosen-width'),
                data_chosen_disable_search = $(this).attr('data-chosen-disable-search');
            $(this).chosen({width: data_chosen_width, disable_search: data_chosen_disable_search });
        });

        if ($('#ae-bid-loop').length > 0) {
            /* bid item in single project*/
            SingleBidItem = Views.PostItem.extend({
                tagName: 'li',
                className: 'info-bidding',
                template: _.template($('#ae-bid-loop').html()),
                onItemBeforeRender: function() {
                    //before render item
                },
                onItemRendered: function() {
                    //after render view
                    var view = this;
                    view.$('.rate-it').raty({
                        readOnly: true,
                        half: true,
                        score: function() {
                            return view.model.get('rating_score');
                        },
                        hints: raty.hint
                    });
                }
            });
            /* bid list in single project*/
            SingleListBids = Views.ListPost.extend({
                tagName: 'ul',
                itemView: SingleBidItem,
                itemClass: 'info-bidding'
            });
        }
        // projects list control
        $('.section-archive-project').each(function() {
            if ($(this).find('.postdata').length) {
                var postdata = JSON.parse($(this).find('.postdata').html()),
                    collection = new Collections.Projects(postdata);
            } else {
                var collection = new Collections.Projects();
            }
            var skills = new Collections.Skills();
            /**
             * init list blog view
             */
            new ListProjects({
                itemView: ProjectItem,
                collection: collection,
                el: $(this).find('.project-list-container')
            });
            //project control
            SearchProjectControl  = Views.BlockControl.extend({
                onBeforeFetch : function() { 
                    this.$el.find('.no-result').remove();
                },
                onAfterFetch : function(result, res) {
                    if(!res.success) {
                        $('.list-project-wrapper').append($('#project-no-result').html()); 
                    }                           
                }
            });
            //post-type-archive-project
            //old block-projects
            /**
             * init block control list blog
             */
            new SearchProjectControl({
                collection: collection,
                skills: skills,
                el: $(this)
            });
        }); // end project list control
        //profile list control
        /**
         * Define profile item view
         */
        ProfileItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'col-md-6',
            template: _.template($('#ae-profile-loop').html()),
            onItemBeforeRender: function() {
                //before render item
            },
            onItemRendered: function() {
                //after render view
                var view = this;
                view.$('.rate-it').raty({
                    readOnly: true,
                    half: true,
                    score: function() {
                        return view.model.get('rating_score');
                    },
                    hints: raty.hint
                });
            }
        });
        
        // blog list control
        if ($('#posts_control').length > 0) {
            if ($('#posts_control').find('.postdata').length > 0) {
                var postsdata = JSON.parse($('#posts_control').find('.postdata').html()),
                    posts = new Collections.Blogs(postsdata);
            } else {
                posts = new Collections.Blogs();
            }
            /**
             * init list blog view
             */
            new ListBlogs({
                itemView: BlogItem,
                collection: posts,
                el: $('#posts_control').find('.post-list')
            });
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: posts,
                el: $('#posts_control')
            });
        }
        /**
         * List view control profiles list
         */
        ListProfiles = Views.ListPost.extend({
            tagName: 'div',
            itemView: ProfileItem,
            itemClass: 'profile-item'
        });
        /*
         * control section profile, search advance search
        */
        $('.section-archive-profile').each(function() {
            if ($(this).find('.postdata').length > 0) {
                var postdata = JSON.parse($(this).find('.postdata').html()),
                    collection = new Collections.Profiles(postdata);
            } else {
                var collection = new Collections.Profiles();
            }
            var skills = new Collections.Skills();
            /**
             * init list blog view
             */
            new ListProfiles({
                itemView: ProfileItem,
                collection: collection,
                el: $(this).find('.profile-list-container')
            });
            //project control
            SearchProfileControl  = Views.BlockControl.extend({
                onBeforeFetch : function() { 
                    this.$el.find('.no-result').remove();
                },
                onAfterFetch : function(result, res) {
                    if(!res.success) {
                        $('.list-profiles-wrapper').append($('#profile-no-result').html()); 
                    }                           
                }
            });

            /**
             * init block control list blog
             */
            new SearchProfileControl({
                collection: collection,
                skills: skills,
                el: $(this)
            });
        }); // end profile list control
        /**
         * list porfolio control
         */
        if ($('.portfolio-container').length > 0) {
            var $container = $('.portfolio-container');
            //portfolio list control
            if ($('.portfolio-container').find('.postdata').length > 0) {
                var postdata = JSON.parse($container.find('.postdata').html()),
                    collection = new Collections.Portfolios(postdata);
            } else {
                var collection = new Collections.Portfolios();
            }
            /**
             * init list blog view
             */
            new ListPortfolios({
                itemView: PortfolioItem,
                collection: collection,
                el: $container.find('.list-porfolio-author')
            });
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: collection,
                el: $container
            });
        } // end profile list control
        if ($('.bid-history').length > 0) {
            var $container = $('.bid-history');
            // $('.profile-history').each(function(){
            if ($container.find('.postdata').length > 0) {
                var postdata = JSON.parse($container.find('.postdata').html()),
                    collection = new Collections.Bids(postdata);
            } else {
                var collection = new Collections.Bids();
            }
            /**
             * init list bid view
             */
            new ListBids({
                itemView: BidItem,
                collection: collection,
                el: $container.find('.list-history-profile')
            });
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: collection,
                el: $container
            });
            // });
        }
        if ($('.project-history').length > 0) {
            var $container = $('.project-history');
            // $('.profile-history').each(function(){
            if ($container.find('.postdata').length > 0) {
                var postdata = JSON.parse($container.find('.postdata').html()),
                    collection = new Collections.Projects(postdata);
            } else {
                var collection = new Collections.Projects();
            }
            /**
             * init list bid view
             */
            new ListWorkHistory({
                itemView: WorkHistoryItem,
                collection: collection,
                el: $container.find('.list-history-profile')
            });
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: collection,
                el: $container
            });
            // });
        }
    });
})(jQuery, AE.Models, AE.Collections, AE.Views);