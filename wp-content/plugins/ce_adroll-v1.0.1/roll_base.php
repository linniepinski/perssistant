<?php
Class CE_AddRoll{
	const CE_ROLL_PAGE_NAME 	= 	'adroll';
	const CE_ROLL_OPTION_NAME 	= 	'ce_roll_page_template';

	// option save id page show adroll	
	function __construct(){
		add_action('widgets_init',array($this,'ce_roll_widget'));		
		add_action( 'wp_ajax_save-page-adroll', array($this, 'save_page_adroll') );

		//add_filter('template_include',array($this,'ce_roll_template'));
		if(!is_admin()){
			add_filter('the_content',array($this,'ce_roll_template'));
			add_filter('page_template', array($this, 'ce_roll_show'));	
		}		
		
	}

	function ce_roll_widget(){
		register_widget( 'AdRoll_Widget' );
	}
	function ce_roll_show($template){
		if(isset($_REQUEST['adroll_request']))
			$template =  CE_ROLL_PATH.'/ad_roll_show.php';
		return $template;
	}

	function ce_roll_template($content){
		$id = self::get_page_adroll();
		if( is_page($id) ){			
			wp_enqueue_script( 'ce.iris', CE_ROLL_URL.'/js/colorpicker.js');
			wp_enqueue_script('js-roll',CE_ROLL_URL.'/js/roll-backend.js', array('ce','jquery','backbone','underscore','ce.iris') );
			$url = get_permalink($id);
			$url = add_query_arg(array('adroll_request' => 1), $url);
			wp_localize_script('js-roll','ce_adroll',array('link' => $url));
			wp_enqueue_style('front-roll',CE_ROLL_URL.'/css/roll-front.css');
			return $this->roll_template_front();
			
		}

		return $content;
			
	}
	
	function save_page_adroll(){		

		if(isset($_REQUEST['id'])){
			$this->set_page_adroll($_REQUEST['id']);
		}

		$id = self::get_page_adroll();
		$_REQUEST['height']	=	$_REQUEST['number'] * 45 + 50;
		
		extract($_REQUEST);

		$width					=	$width ;		
		$url 					= get_page_link($id);
		$_REQUEST['url_front'] 	= $url;

		$url 	= add_query_arg(array('adroll_request'=>1,'number'=>$number, 'title' => $title, 'bgcolor'=> $bgcolor ), $url);
		
		if(!empty($ad_cat))
			$url = add_query_arg(array('ad_cat'=> $ad_cat), $url);
		if(!empty($ad_location))
			$url = add_query_arg(array('ad_location'=> $ad_location), $url);
		
		$_REQUEST['url'] 		= $url;		
		wp_send_json(array('success'=>true,'msg' => __('Save successfull',ET_DOMAIN), 'data' => $_REQUEST ));		

		
	}


	function roll_template_front(){
		$html ='<div id="ce_adroll" class="roll-view"><form class="form form-roll-admin" id="frm_roll">
							<div class="form-item">
								<div class="title font-quicksand">
									Content	
								</div>
							</div>							
							<div class="form-item">
								<div class="half alignleft">
									<label>'.__('Select Category',ET_DOMAIN).'</label>
									<div class="select-style et-button-select">
					        			<select style="z-index: 10; opacity: 0;"  class="change" name="ad_cat">
					        			<option value="">'.__('All Categories',ET_DOMAIN).'</option>';

					        			$cats 		= ET_AdCatergory::get_category_list();
						        			if($cats){
						        				foreach ($cats as $key => $cat) {
						        					$html.='<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
						        				}
						        			}
					        				
						        								        					
						        	$html.='</select>
					        		</div>
					        	</div>
								<div class="half alignright">
									<label>'.__('Select locations',ET_DOMAIN).'</label>
									<div class="select-style et-button-select">
					        			<select class="change" style="z-index: 10; opacity: 0;" name="ad_location">
						        			<option value="">'.__('All Location',ET_DOMAIN).'</option>';

						        			$locations 	= ET_AdLocation::get_location_list();
						        			if($locations){
						        				foreach ($locations as $key => $local) {

						        					$html.='<option value="'.$local->term_id.'">'.$local->name.'</option>';		
						        				}
						        			}
					        									        								        					
						        	$html.=	'</select>		
					        		</div>
								</div>
							</div>
							<div class="form-item">
								<div class="title font-quicksand">
									Display								</div>
							</div>
							<div class="form-item">
								<div class="half alignleft">
								 	<label> Number ads </label>
								 	<input type="text" name="number" class="number" value="5">
								</div>
								<div class="half alignright">
								 	<label> '.__('Width',ET_DOMAIN).'</label>
								 	<input type="text"  id="width" value="240">
								</div>
							</div>
							<div class="form-item">
								<div class="half alignleft">
								 	<label> Background Color </label>
								 	<input type="text" name="bgcolor" id="colorpicker" class="bgcolor" value="f5f5f5">
								 	
								</div>
								<div class="half alignright">
								 	<label> Title  </label>
								 	<input type="text" name="title" value="'.__("Ads from Website",ET_DOMAIN).'">
								</div>
							</div>';
							$id  	= self::get_page_adroll();
							$link  	= get_permalink($id);
							$link 	= add_query_arg(array('adroll_request'=>1), $link);

							$frame = '<iframe scrolling="no" id="frame_preview" style="border:0; overflow:hidden;" src="'.$link.'" width="240px" frameborder="0" height="478px" allowtransparency="true" marginheight="0" marginwidth="0"></iframe>';
							$html .='<div class="form-item">
								<label> Copy this code and past into your site:</label>
								<textarea class="code-content" cols="10" rows="16">'.$frame.'</textarea>
							</div>


						</form>';
					
						$html .='<div class="quick_view alignright">
							<iframe  scrolling="no" id="frame_preview" style="border:0; overflow:hidden;" src="'.$link.'" frameborder="0" width="240px" height="478px" allowtransparency="true" marginheight="0" marginwidth="0"></iframe>
						</div>
						</div>';
		return $html;

	}

	/**
	 * get page id which show config ad roll in front-end.
	 */	
	static function get_page_adroll(){

		$default  = get_posts(array(
				'name' 			=> self::CE_ROLL_PAGE_NAME,
				'post_type' 	=> 'page',
				'numberposts' 	=> 1
			));

		$id_default = isset($default[0]->ID) ? $default[0]->ID : -1;
		
		return get_option(self::CE_ROLL_OPTION_NAME,$id_default);
	}

	/**
	 * save id page, which page show config ad roll for user in front-end.
	 */

	function set_page_adroll($id){
		update_option(self::CE_ROLL_OPTION_NAME,$id);
	}	
}

new CE_AddRoll();
	
?>