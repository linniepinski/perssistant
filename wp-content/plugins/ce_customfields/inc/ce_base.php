<?php
class CE_Fields extends ET_AdminMenuItem {

	const CE_FIELDS_SLUG 	= 'ce-fields';
	const CE_FIELD_META 	= 'ce_fields';
	const CE_FIELD_SELLER 	= 'ce_field_seller';
	const CE_FIELD_TAX 		= 'ce_tax';

	public function __construct($args = array() ){
		parent::__construct(self::CE_FIELDS_SLUG,  array(
			'menu_title'	=> __('CE Fields', ET_DOMAIN),
			'page_title' 	=> __('CE FIELDS', ET_DOMAIN),
			'callback'   	=> array($this, 'menu_view'),
			'slug'			=> self::CE_FIELDS_SLUG,
			'page_subtitle'	=> __('CE Fields Overview', ET_DOMAIN),
			'pos' 			=> 50,
			'icon_class'	=> 'icon-menu-overview'
		));
		$this->add_action('init','ce_fields_init', 20);
	}
	public function on_add_scripts(){

		wp_enqueue_script('jquery');
		wp_enqueue_script('underscore');
		wp_enqueue_script('backbone');
		wp_enqueue_script('json');
		wp_enqueue_script( 'ce' );
		wp_enqueue_script( 'jquery.validator' );

		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script('ce-fields-admin',CE_FIELDS_URL . '/js/ce-fields-admin.js',array('jquery','jquery-ui-sortable', 'underscore', 'backbone', 'ce') );
		wp_localize_script( 'ce-fields-admin', 'ce_fields', array(
			'text_no_space' => __('This field don\'t have a spacy.',ET_DOMAIN),
			));
	}
	public function on_add_styles(){
		wp_enqueue_style( 'admin.css' );
		wp_enqueue_style('ce-fields-style',CE_FIELDS_URL.'/css/ce-fields-admin.css');
	}
	public function ce_fields_init(){

		$taxs = self::get_taxs();
		if(is_array($taxs) && !empty($taxs) ){
		// Add new taxonomy, make it hierarchical (like categories)
			foreach($taxs as $key=>$tax){
				if(isset($tax['tax_status']) && $tax['tax_status'])
					$this->ce_fields_register_taxonomy($tax);
			}
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}

	}

	public static function get_taxs(){
		$taxs 	= get_option(self::CE_FIELD_TAX,array());
		return $taxs;
	}
	public static function set_taxs($taxs){
		update_option(self::CE_FIELD_TAX,$taxs);
	}
	public static function get_fields(){
		$fields 	= get_option(self::CE_FIELD_META,array());
		return $fields;
	}

	public static function set_fields($fields){
		$result = update_option(self::CE_FIELD_META,$fields);
		return $result;
	}
	public static function get_seller_fields(){
		$fields 	= get_option(self::CE_FIELD_SELLER,array());
		return $fields;
	}

	public static function set_seller_fields($fields){
		$result = update_option(self::CE_FIELD_SELLER,$fields);
		return $result;
	}

	public function menu_view($args){
		?>
		<div class="et-main-header">
			<div class="title font-quicksand"><?php _e('CE Fields',ET_DOMAIN);?></div>
			<div class="desc"><?php _e('Create new field and taxonomies for your ads.',ET_DOMAIN);?></div>
		</div>
		<div class="et-main-content" id="ce_fields_ext">
			<div class="et-main-main clearfix inner-content" style="margin-left : 0;">
				<div class="head-tabs et-main-left">
					<ul class="et-menu-content inner-menu">
						<li> <a class="section-link fields_tab_div active" href="#fields_tab_div" rel="#fields_tab"><span data-icon="l" class="icon"></span><?php _e('Fields',ET_DOMAIN);?> </a></li>
						<li> <a class="section-link taxs_tab_div" href="#taxs_tab_div" rel="#taxs_tab"><span data-icon="l" class="icon"></span><?php _e('Taxonomies',ET_DOMAIN);?></a></li>
						<li><a class="section-link seller_tab_div" href="#seller_tab_div" rel="#seller_tab"><span data-icon="l" class="engine-menu-icon icon-sellers"></span> Seller Fields </a></li>
					</ul>
				</div>
				<div class="ce-field-list settings-content">
					<div id="setting-general" class="et-main-main clearfix inner-content">
						<?php require_once("template/fields_and_form.php"); ?>
						<?php require_once("template/taxonomies_and_form.php"); ?>
						<?php require_once("template/seller_and_form.php"); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	function ce_fields_register_taxonomy($tax){

		$labels = array(
				'name'              => _x( $tax['tax_label'], 'taxonomy general name' ),
				'singular_name'     => _x( $tax['tax_label'], 'taxonomy singular name' ),
				'search_items'      => __( 'Search '.$tax['tax_label'] ),
				'all_items'         => __( 'All '.$tax['tax_label'] ),
				'parent_item'       => __( 'Parent '.$tax['tax_label'] ),
				'parent_item_colon' => __( 'Parent '.$tax['tax_label'].':' ),
				'edit_item'         => __( 'Edit '.$tax['tax_label'] ),
				'update_item'       => __( 'Update '.$tax['tax_label'] ),
				'add_new_item'      => __( 'Add New '.$tax['tax_label'] ),
				'new_item_name'     => __( 'New '.$tax['tax_label'].' Name' ),
				'menu_name'         => __($tax['tax_label'] ),
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => false,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $tax['tax_slug'],'with_front'=>true ),
			);

			register_taxonomy( $tax['tax_name'], array( CE_AD_POSTTYPE ), $args );

	}

}
?>