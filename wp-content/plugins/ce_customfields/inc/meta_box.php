<?php
class CE_Meta_Box {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type = CE_AD_POSTTYPE ) {
         if($post_type != CE_AD_POSTTYPE)
			return;
		add_meta_box(
			'ce_box_name'
			,__( 'CE Fields Meta For Ad', 'ET_DOMAIN' )
			,array( $this, 'render_meta_box_content' )
			,$post_type
			,'advanced'
			,'high'
		);
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */
		// Check if our nonce is set.
		if ( ! isset( $_POST['myplugin_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['myplugin_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'myplugin_inner_custom_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		//$mydata = sanitize_text_field( $_POST['myplugin_new_field'] );

		// Update the meta field.
		$fields = CE_Fields::get_fields();
		if($fields){
			foreach ($fields as $key => $field) {
				$mydata = sanitize_text_field( $_POST[$field['field_name']]);
				update_post_meta( $post_id, trim($field['field_name']), $mydata );
			}
		}

	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
		if($post->post_type != CE_AD_POSTTYPE)
			return;
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'myplugin_inner_custom_box', 'myplugin_inner_custom_box_nonce' );
		// Display the form, using the current value.
		echo '<label for="myplugin_new_field">';
		_e( 'List of fields added through CE Custom Fields.', 'ET_DOMAIN' );
		echo '</label> ';
		?>
		<table class="form-table ad-info">
		<tbody>
			<?php
			$fields = CE_Fields::get_fields();
			if($fields){
				foreach($fields as $key=>$field){
					$this->show_field($field,$post);
				}
			}
			?>
		</tbody>
		</table>
		<script type="text/javascript">
			(function($) {
				$(document).ready(function(){
					if($('.ce_field_datepicker').length>0){
						$('.ce_field_datepicker').datepicker({
							dateFormat : edit_ad.dateFormat
							//defaultDate : new Date(jQuery('#et_date').val())
						});
					}


				});
			})(jQuery);
		</script>

		<?php
	}

	/**
	* show html field in wordpress admin when insert or edit a ad.
	* @param  array $field  value of this field.
	* @param  array  $post  value off post
	* @return none
	*/
	function show_field($field , $post = array()){
		$type 	= isset($field['field_type']) ? $field['field_type'] : 'text';
		$value 	= get_post_meta($post->ID,$field['field_name'],true);
		$class 	= '';
		?>
		<tr valign="top">
			<?php
			echo '<th scope="row"> <strong>'.$field['field_label'].'</strong> </th>';
			echo '<td>';
				if($type == 'textarea')
					echo '<textarea class="large-text" placeholder="'.$field['field_pholder'].'"  cols="40" name="'.$field['field_name'].'"  />'.$value.'</textarea>';
				else {
					if($type == 'date'){
						$class .= 'ce_field_datepicker';
							if(!empty($value))
								$value = date(get_option('date_format'),strtotime($value) );

					}
					echo '<input placeholder="'.$field['field_pholder'].'" type="text" class=" '.$class.' large-text " name="'.$field['field_name'].'" value = "'.$value.'"/>';	
				}
				?>
				<p class="description"><?php echo $field['field_des'];?></p>
			</td>
		</tr>
		<?php
	}


}
/**
 * call meta field has registed when edit or add new ad.
 * @return [type] [description]
 */
function call_MetaBox() {
    new CE_Meta_Box();
}
if ( is_admin() ) {
    add_action( 'load-post.php', 'call_MetaBox' );
    add_action( 'load-post-new.php', 'call_MetaBox' );
}


?>