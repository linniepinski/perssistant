<?php
if(class_exists('ET_AdminMenuItem')) {

	Class CE_AdRoll_Menu extends ET_AdminMenuItem{

		const CE_ROLL_SLUG = 'ce-roll';
		

		function __construct($args = array()){

			 parent::__construct(self::CE_ROLL_SLUG,  array(
	            'menu_title'    => __('CE Roll', ET_DOMAIN),
	            'page_title'    => __('CE ROLL', ET_DOMAIN),
	            'callback'      => array($this, 'menu_view'),
	            'slug'          => self::CE_ROLL_SLUG,
	            'page_subtitle' => __('CE AdRoll', ET_DOMAIN),
	            'pos'           => 57,
	            'icon_class'    => 'icon-menu-overview'
	        ));

		}

		/**
		 * view in backend this plugin.
		 */

		function menu_view($args){ 
			?>
			<div class="et-main-header">
				<div class="title font-quicksand"><?php _e('CE Ad Roll', ET_DOMAIN);?></div>
				<div class="desc"><?php _e('Create a adroll for publisher.',ET_DOMAIN);?></div>
			</div>
			
			<div id="ce_adroll" class="et-main-content roll-view">

				<div class="settings-content et-main-main no-margin overview">					
					<div class="title font-quicksand">Install sample data</div>
					<div class="desc">
						<div class="row-item row-top">
							<label><?php _e('Select a page for your adroll tempalte:',ET_DOMAIN)?> </label>
							<div class="select-style et-button-select btn-effect">
			        			<select id="select-page-roll" class="page-roll" style="z-index: 10; opacity: 0;">

			        			<?php 
			        			
			        				$id 		=  CE_AddRoll::get_page_adroll();		        				
			        			
			        				$pages 		= get_posts(array('meta_key'=>'_wp_page_template','meta_value'=>'', 'post_type' => 'page','post_status' => 'publish'));

									if ( !empty($pages) ){
										foreach ($pages as $page) {
											$class = ($id == $page->ID) ? 'selected ="selected"' : '';
											$url = get_permalink($page->ID);
											$url = add_query_arg(array('adroll_request' => 1), $url);											
											
											echo '<option '.$class.' data-url ="'.$url.'" value="' . $page->ID . '">' .$page->post_title.'</option>';
										}
									}
								?>
				        								        					
				        		</select>		
			        		</div>

						</div>
						<div class="row-roll">
							<form id="frm_roll" class="form form-roll-admin">
								<div class="form-item">
									<div class="title font-quicksand">
										<?php _e('Content',ET_DOMAIN);?>
									</div>
								</div>
								<div class="form-item">
									<div class="half alignleft">
										<label><?php _e('Select Category',ET_DOMAIN);?></label>
										<div class="select-style et-button-select">
						        			<select name = "ad_cat" class="change" style="z-index: 10; opacity: 0;">
						        				<option value=""><?php _e('All Categories',ET_DOMAIN);?></option>';
						        				<?php 
							        			$cats 		= ET_AdCatergory::get_category_list();
							        			
							        			if($cats){
							        				foreach ($cats as $key => $cat) {
							        					echo '<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
							        				}
							        			}
							        			?>	
							        								        					
							        		</select>		
						        		</div>
						        	</div>
									<div class="half alignright">
										<label><?php _e('Select locations',ET_DOMAIN);?> </label>
										<div class="select-style et-button-select">
						        			<select name="ad_location" class="change" style="z-index: 10; opacity: 0;">
						        				<option value=""><?php _e('All Location',ET_DOMAIN);?></option>';
							        			<?php 
							        			
							        			$locations 	= get_terms( ET_AdLocation::AD_LOCATION, array('hide_empty'=>false,'hierarchical'=> false) );
							        			if($locations){
							        				foreach ($locations as $key => $local) {
							        					echo '<option value="'.$local->term_id.'">'.$local->name.'</option>';
							        				}
							        			}
							        			?>						        								        					
							        		</select>		
						        		</div>
									</div>
								</div>
								<div class="form-item">
									<div class="title font-quicksand">
										<?php _e('Display',ET_DOMAIN);?>
									</div>
								</div>
								<div class="form-item">
									<div class="half alignleft">
									 	<label> <?php _e('Number ads',ET_DOMAIN);?> </label>
									 	<input type="text" name="number" class="number" value="5" />
									</div>
									<div class="half alignright">
									 	<label><?php _e('Width',ET_DOMAIN);?> </label>
									 	<input type="text" id="width" value="240"  />
									</div>
								</div>
								<div class="form-item">
									<div class="half alignleft">
									 	<label> <?php _e('Background Color',ET_DOMAIN);?> </label>
									 	

									 	<input type="text" class="bgcolor" value="d9d9d9" name="bgcolor" />

									</div>
									<div class="half alignright">
									 	<label> <?php _e('Title',ET_DOMAIN);?>  </label>
									 	<input type="text" value="<?php _e('Ads from Website',ET_DOMAIN);?>" name="title" />
									</div>
								</div>
								<div class="form-item">
									<?php 
									$url 	= get_permalink($id);
									$link  	= add_query_arg(array('adroll_request'=>1,'number'=>5), $url);
									?>

									<label> <?php _e('Copy this code and past into your site',ET_DOMAIN);?>:</label>
									<textarea class="code-content" rows="16" cols="10"><iframe id="frame_preview" style="border:0; overflow:hidden;" src="<?php echo $link;?>" frameborder="0" height="467px" width="250px" allowtransparency="true" marginheight="0" marginwidth="0"></iframe></textarea>

								</div>
								<div class="form-item">
							 		<?php printf(__('Note: You can also allow your users to create a adroll in the frontend by providing this link to them: <a id = "url_front" target="_blank" href="%s">Adroll</a>',ET_DOMAIN),$url);?>

								</div>

							</form>
							
							<div class="quick_view alignright">
								<div class="main-title title font-quicksand"><?php _e('QUICK VIEW',ET_DOMAIN);?></div>
								<iframe title ="<?php _e(" Title Ad roll",ET_DOMAIN);?>" id="frame_preview" style="border:0; overflow:hidden;" src="<?php echo $link;?>" frameborder="0" height="467px" allowtransparency="true" marginheight="0" marginwidth="0"></iframe>
							</div>
						</div><!-- row-roll !-->
						</div>
					</div>
					
				</div>
		
			<?php 
								
		}

		/**
		 * add style for backend .
		 */

		function on_add_styles(){
			wp_enqueue_style('admin.css');
			wp_enqueue_style('roll-style',CE_ROLL_URL.'/css/roll-front.css');
		}
		/**
		 *  add script for backend this page.
		 */

		function on_add_scripts(){
			$roll 	= new CE_AddRoll();
			$id 	= $roll->get_page_adroll();
			wp_enqueue_script( 'ce.iris', CE_ROLL_URL.'/js/colorpicker.js');
			wp_enqueue_script('js-roll-backend',CE_ROLL_URL.'/js/roll-backend.js', array('ce','jquery','backbone','underscore') );
			$url = get_permalink($id);
			$url = add_query_arg(array('adroll_request'=>1), $url);			
			wp_localize_script('js-roll-backend','ce_adroll',array('link' => $url));
		}

		/**
		 * get page id which show config ad roll in front-end.
		 */	
		
	}
}
	
?>