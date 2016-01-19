<?php
if(class_exists('ET_AdminMenuItem')) {

	Class CE_AdLert_Menu extends ET_AdminMenuItem{
		const CE_ALERT_SLUG = 'ce-alert';
		private $alert_option;
		function __construct($args = array()){

			 parent::__construct(self::CE_ALERT_SLUG,  array(
	            'menu_title'    => __('CE Alert', ET_DOMAIN),
	            'page_title'    => __('CE ALERT', ET_DOMAIN),
	            'callback'      => array($this, 'menu_view'),
	            'slug'          => self::CE_ALERT_SLUG,
	            'page_subtitle' => __('CE Adlert', ET_DOMAIN),
	            'pos'           => 59,
	            'icon_class'    => 'icon-gear'
	        ));

			$this->alert_option	=	CE_Alert_Option::get_instance();
			$this->add_action('wp_ajax_save-alert-option','save_alert_option');
		}

		function save_alert_option () {
			$request= $_POST;
			$this->alert_option->set_option($request);
			$alert = new CE_Alert();
			$alert->activation();
			wp_send_json(array('success' => true,'msg' => __('Save Alert Option Success',ET_DOMAIN),'data'=>$request) );
		}

		/**
		 * view in backend this plugin.
		 */

		function menu_view($args){
			$option = CE_Alert_Option::get_option();
			extract($option);

			$ce_option 		= new CE_Options;
			$website_logo 	= $ce_option->get_website_logo();

			?>
			<div class="et-main-header">

				<div class="title font-quicksand"><?php _e('CE Alert',ET_DOMAIN);?></div>
				<div class="desc"><?php _e('Set how frequent your subscribers will receive new ad\'s alert in their inbox.',ET_DOMAIN);?></div>
			</div>

			<div class="et-main-content" id="ce_alert">
				<div class="et-main-left">
					<ul class="et-menu-content inner-menu">
						<li>
							<a href="#alert_settings" menu-data="general" class="section-link active">
							<span class="icon" data-icon="y"></span><?php _e('Settings',ET_DOMAIN);?></a></li>
						<li>
							<a href="#alert_subscribers" menu-data="update" class="section-link ">
							<span class="icon" data-icon="~"></span> <?php _e('Subscribers',ET_DOMAIN);?></a>
						</li>
					</ul>
				</div>
				<div class="alert-content">
					<div class="et-main-main clearfix inner-content" id="setting-general">
						<div class="right-alert">
						<div id="alert_settings_tab" class="tab-alert <?php if(isset($_GET['paged'])) echo 'hide';?>">
							<div class="title font-quicksand"><?php _e('Mailing settings',ET_DOMAIN);?></div>
							<div class="desc">
								<form id="ad_alert" method="post" action="#">
			        				<div class="form-item">
			        					<label><?php _e('Recurrence',ET_DOMAIN);?><br><span><?php _e('Choose frequency',ET_DOMAIN);?></span></label>
			        					<div class="alignright">
				        					<div class="select-category select-style et-button-select">
				        						<select name="schedule" style="z-index: 10; opacity: 0;">
				        							<option <?php if($schedule =='daily') echo 'selected ="selected"';?> value="daily"><?php  _e('Daily',ET_DOMAIN);?></option>
				        							<option <?php if($schedule =='weekly') echo 'selected ="selected"';?>value="weekly"><?php _e('Weekly',ET_DOMAIN);?></option>
				        						</select>
				        					</div>
			        					</div>
			        				</div>
			        				<div class="form-item">
			        					<label><?php _e('Ad Limit',ET_DOMAIN);?> <br><span><?php _e('Number of ads per email',ET_DOMAIN);?></span></label>
			        					<input type="text" class="required number" value="<?php echo $number_ads;?>" name="number_ads" >
			        					<span class="span-end"><?php _e('Ads',ET_DOMAIN);?></span>
			        				</div>
			        				<div class="form-item">
			        					<label><?php _e('Batch Email',ET_DOMAIN);?> <br><span><?php _e('Number of emails to send per batch',ET_DOMAIN);?></span></label>
			        					<input type="text" class="required number" value="<?php echo $number_emails;?>" name="number_emails" >
			        					<span class="span-end"><?php _e('Emails',ET_DOMAIN);?></span>
			        				</div>
			        				<div class="form-item  form">
							        		<p>Alert emails are sent in batches (Batch Email) per your selected frequency (Recurrence).
							        			For each batch, you can send a maximum of 100 emails. If the number of emails to be sent
							        			at a given time exceeds the batch size you set, the remaining emails will be included in the next batch.
							        			</p>
							        			<p>
							        			Each email contains the number of ad defined in the "Ad Limit" and only "published" ads that match
							        			the subscriber's alert criteria are included.
							        		</p>
							        		<p>
												The "Batch Email" is limited to 100 but some web hosts allow fewer than this value.
												To avoid being blacklisted, you should contact your web host to know the right limit.
							        		</p>

			        				</div>
			        				<input type="hidden" value="save-alert-option" name="action">
			        				<div class="form-item">
			        					<button type="submit" class="et-button btn-button"><?php _e('Save your setting',ET_DOMAIN);?></button>
			        				</div>
			        			</form>

							</div>
						</div> <!-- end #alert_settings !-->
						<div id="alert_subscribers_tab" class="tab-alert <?php if(!isset($_GET['paged'])) echo 'hide';?>">
							<div class="title font-quicksand"><?php _e('Listing Subscribers',ET_DOMAIN);?></div>
							<div class="desc">
								<?php
								global $wp_query;
								$paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
								//$paged = max( 1, get_query_var('paged') );
								$args 			= array('post_type'=>'subscriber','post_status' => 'private','paged' => $paged);
								$alerts 		= new WP_Query($args);

								if($alerts->have_posts()){
									echo '<table width="100%"><th align="left" valign="top" width="15%"> Email</th> <th align="left" width="40%" > Categories</th> <th align="center"  width="40%" align="left"> Location </th><th  width="5%">Delete</th>';
									while($alerts->have_posts()): $alerts->the_post();
										echo '<tr><td valign="top" >';
										$email = get_post_meta(get_the_ID(),'et_subscriber_email',true);
										the_title();

										$status = get_post_meta(get_the_ID(),'ce_have_new_ad',true);

										echo '</td><td >';

										$cats = wp_get_post_terms(get_the_ID(), 'ad_category', array("fields" => "names"));
										if($cats){
											foreach ($cats as $key => $cat) {
												echo $cat.', ';
											}
										}
										echo '</td><td >';

										$locals = wp_get_post_terms(get_the_ID(), 'ad_location', array("fields" => "names"));
										if($locals){
											foreach ($locals as $key => $name) {
												echo $name.', ';
											}
										}

										echo '</td><td align="center">';
										echo '<span class="del-subscriber" id="'.get_the_ID().'" title="Remove '.get_the_title().'"> X </span>';
										echo '</td></tr>';
									endwhile;
									echo '</table>';
									echo '<div class="row row-paging">';
									$url  = admin_url( 'admin.php?page=ce-alert' );
									 	ce_alert_pagination ($alerts,$paged,$url);
									echo '</div>';
								} else {
									_e(' List subscriber is empty!',ET_DOMAIN);
								}
								?>

							</div>

						</div><!-- end //#alert_subscribers !-->
						</div> <!--end .right-alert !-->
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
			wp_enqueue_style('admin-alert',CE_ALERT_URL.'/css/admin.css');
		}
		/**
		 *  add script for backend this page.
		 */

		function on_add_scripts(){
			//wp_enqueue_script('jquery');
			wp_enqueue_script('backbone');
			wp_enqueue_script('ce');
			wp_enqueue_script('admin-alert',CE_ALERT_URL.'/js/admin.js',array('backbone','underscore','ce'));
		}

		/**
		 * get page id which show config ad roll in front-end.
		 */

	}
}

?>