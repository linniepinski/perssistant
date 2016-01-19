(function($, Models, Collections, Views) {
	$(document).ready(function() { 
		/**
		 * model project
		 */
		Models.Project = Backbone.Model.extend({
			action: 'ae-project-sync',
			initialize: function() {}
		});
		/**
		 * project collections
		 */
		Collections.Projects = Backbone.Collection.extend({
			model: Models.Project,
			action: 'ae-fetch-projects',
			initialize: function() {
				this.paged = 1;
			}
		});
		/**
		 * define project item view
		 */
		ProjectItem = Views.PostItem.extend({
			tagName: 'li',
			className: 'project-item',
			template: _.template($('#ae-project-loop').html()),
			onItemBeforeRender: function() {
				// before render view
			},
			onItemRendered: function() {
				// after render view
			}
		});
		/**
		 * list view control project list
		 */
		ListProjects = Views.ListPost.extend({
			tagName: 'ul',
			itemView: ProjectItem,
			itemClass: 'project-item'
		});

	});
})(jQuery, AE.Models, AE.Collections, AE.Views);