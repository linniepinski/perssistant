(function($, Models, Collections, Views) {
    $(document).ready(function() {
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
         * // end blog list control
         */
        // projects list control
        $('.section-archive-project, .tab-project-home').each(function() {
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
            //post-type-archive-project
            //old block-projects
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: collection,
                skills: skills,
                el: $(this)
            });
        });
        if ($('.info-project-items').length > 0) {
            if ($('.info-project-items').find('.postdata').length > 0) {
                var postdata = JSON.parse($('.info-project-items').find('.postdata').html()),
                    collection = new Collections.Bids(postdata);
            } else {
                collection = new Collections.Bids();
            }
            /**
             * init list blog view
             */
            new User_ListBids({
                itemView: User_BidItem,
                collection: collection,
                el: $('.info-project-items').find('.bid-list-container')
            });
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: collection,
                el: $('.info-project-items')
            });
        }
        /**
         * // end project list control
         */
        //profile list control
        $('.section-archive-profile, .tab-profile-home').each(function() {
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
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: collection,
                skills: skills,
                el: $(this)
            });
        });
        /**
         * // end profile list control
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
                el: $container.find('.list-item-portfolio')
            });
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: collection,
                el: $container
            });
        }
        /**
         * // end porfolio list control
         */
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
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);