<?php
/*
 Plugin Name: Contact Form 7 Cognalys Addon Plugin
 Plugin URI: 
 Description: Allows you to verify telephone number when they submit a message using Contact Form 7
 Author: Anish Menon
 Version: 1.0
 Author URI: 
 */
define( 'CF7COGADDON_VERSION', '1.0.1' );
define( 'CF7COGADDON_PATH', dirname(__FILE__) . '/' );
define( 'CF7COGADDON_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

add_action('plugins_loaded', 'cf7_cognalys_addon_loaded');

function cf7_cognalys_addon_loaded() {
	if(!function_exists('wpcf7_install')){
		add_action('admin_notices', 'cf7_plugin_conflict_check');		
		return;
	}	
	// Add a menu for our options page
	add_action('admin_menu', 'cf7_cognalys_addon_add_page');
}


function cf7_cognalys_plugin_url( $path = '' ) {
	$url = untrailingslashit( CF7COGADDON_URL );

	if ( ! empty( $path ) && is_string( $path ) && false === strpos( $path, '..' ) )
		$url .= '/' . ltrim( $path, '/' );

	return $url;
}

function cf7_plugin_conflict_check(){
	echo '<div class="error fade"><p>Attention! You do not have the <a href="http://contactform7.com/" target="_blank">Contact Form 7 plugin</a> active! The Contact Form 7 Cognalys Addon Plugin can only work if Contact Form 7 is active.</p></div>';
}

function cf7_cognalys_addon_add_page() {
	add_options_page( 'CF7 Cognalys Addon', 'CF7 Cognalys Addon', 'manage_options', 'cf7_cognalys_addon', 'cf7_cognalys_addon_option_page' );
}

// Draw the admin page
function cf7_cognalys_addon_option_page() {
	$cognalys_enabled = 0;
	$cognalys_app_id = '';
	$cognalys_access_token = '';

	//process form submission
	if (isset($_POST['Submit'])){
		if ($_POST['cognalys-app-id'] != "") {
			$cognalys_app_id = filter_var(trim($_POST['cognalys-app-id']), FILTER_SANITIZE_STRING);
			if (!$cognalys_app_id || $cognalys_app_id == "") {
				$errors .= 'Please enter a valid app id.<br/><br/>';
			}
		} else {
			$errors .= 'Please enter app id of your OTP.<br/>';
		}
		 
		if ($_POST['cognalys-access-token'] != "") {
			$cognalys_access_token = filter_var(trim($_POST['cognalys-access-token']), FILTER_SANITIZE_STRING);
			if (!$cognalys_access_token || $cognalys_access_token == "") {
				$errors .= 'Please enter a valid access token.<br/>';
			}
		} else {
			$errors .= 'Please enter access token of your OTP.<br/>';
		}
		if (!$errors)
		{
			if (isset($_POST['enable-cognalys'])) {
				$cognalys_enabled = 1;
			} else {
				$cognalys_enabled = 0;
			}
			
			//add the data to the wp_options table
			$options = array(
				'cognalys_enabled' => $cognalys_enabled,
				'cognalys_app_id' => $cognalys_app_id,
				'cognalys_access_token' => $cognalys_access_token
			);
			update_option('cf7_cognalys_addon', $options); //store the results in WP options table
			echo '<div id="message" class="updated fade">';
			echo '<p>Settings Saved</p>';
			echo '</div>';
		}
		else
		{
			echo '<div style="color: red">' . $errors . '<br/></div>';
		}
	}
	if (get_option('cf7_cognalys_addon'))
	{
		$settings = get_option('cf7_cognalys_addon');
		$cognalys_enabled = $settings['cognalys_enabled'];
		$cognalys_app_id = $settings['cognalys_app_id'];
		$cognalys_access_token = $settings['cognalys_access_token'];
	}
	?>
<div class="wrap">
<div id="poststuff"><div id="post-body">
<h2>Contact Form 7 Cognalys Addon</h2>
<p>Enter Your Cognalys OTP Application Details</p>
<form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST" onsubmit="">
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="enable-cognalys"> Enable Verifying Cognlays: </label></th>
		<td><input type="checkbox" name="enable-cognalys"
		<?php if($cognalys_enabled) echo ' checked="checked"'; ?> /></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="cognalys_app_id"> OTP App ID:</label>
		</th>
		<td><input size="24" name="cognalys-app-id" value="<?php echo $cognalys_app_id; ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="cognalys_access_token"> OTP Access Token:</label>
		</th>
		<td><input size="40" name="cognalys-access-token"
			value="<?php echo $cognalys_access_token; ?>" /></td>
	</tr>
</table>
<input name="Submit" type="submit" value="Save Settings" class="button-primary" /></form>
<p>
Please, create a cognalys account ( <a target='_new' href="https://www.cognalys.com/">https://www.cognalys.com/</a> ).<br/>
To create new OTP Application, click DASHBORD -> OTP Applications -> Create new.<br/>
<img src="<?php echo cf7_cognalys_plugin_url( 'assets/img/new_otp_app.png' ); ?>"/><br/>
To grab APP ID and Access Token of your OTP App, click DASHBORD -> OTP Applications -> Manage.<br/>
<img src="<?php echo cf7_cognalys_plugin_url( 'assets/img/manage_otp_app.png' ); ?>"/><br/>
And click Configuration from panel of you OTP App.<br/>
<img src="<?php echo cf7_cognalys_plugin_url( 'assets/img/config_otp_app.png' ); ?>"/><br/>
</p>
</div></div>
<div style="border-bottom: 1px solid #dedede; height: 10px"></div>
		<?php
}

add_action( 'wpcf7_enqueue_scripts', 'cf7_cognalys_enqueue_scripts' );

function cf7_cognalys_enqueue_scripts() {
	$settings = get_option('cf7_cognalys_addon');

	if ($settings['cognalys_enabled'] == 1) {
		$in_footer = true;

		if ( 'header' === wpcf7_load_js() ) {
			$in_footer = false;
		}

		wp_enqueue_script( 'jquery-intl-tel-input',
			cf7_cognalys_plugin_url( 'assets/js/intlTelInput.min.js' ),
			array( 'jquery', 'jquery-form' ), CF7COGADDON_VERSION, $in_footer );
		wp_enqueue_script( 'contact-form-7-cognalys',
			cf7_cognalys_plugin_url( 'assets/js/script.js' ),
			array( 'jquery', 'jquery-form' ), CF7COGADDON_VERSION, $in_footer );
		wp_enqueue_script( 'jquery-ui-dialog' );
	}
}

add_action( 'wpcf7_enqueue_styles', 'cf7_cognalys_enqueue_styles' );

function cf7_cognalys_enqueue_styles() {
	$settings = get_option('cf7_cognalys_addon');

	if ($settings['cognalys_enabled'] == 1) {
		wp_enqueue_style( 'style-jquery-intl-tel-input', 
			cf7_cognalys_plugin_url( 'assets/css/intlTelInput.css' ) );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
	}
}

add_action( 'init', 'cf7_cognalys_control_init', 12 );

function cf7_cognalys_control_init() {
	$settings = get_option('cf7_cognalys_addon');
	$cognalys_app_id = $settings['cognalys_app_id'];
	$cognalys_access_token = $settings['cognalys_access_token'];

	if ( $settings['cognalys_enabled'] != 1 ) {
		return;
	}

	if ( ! isset( $_SERVER['REQUEST_METHOD'] ) ) {
		return;
	}

	if ( $_REQUEST['cf7_check_cognayls'] == 1 ) {
		$mobiles = $_REQUEST['mobile'];
		if ($mobiles != "") {
			$mobiles = split(',', $mobiles);
			$json = "";

			foreach($mobiles as $mobile) {
				$url = 'https://www.cognalys.com/api/v1/otp/?app_id=' . $cognalys_app_id . '&access_token=' . $cognalys_access_token . '&mobile=' . $mobile;
				if ($json != "") {
					$json .= ",";
				}
				$json .= file_get_contents($url);
			}
			$json = "[" . $json . "]";

			if ( wpcf7_is_xhr() ) {
				@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
				echo $json;
			} else {
				@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
				echo '<textarea>' . $json . '</textarea>';
			}
		}

		exit();
	}
	else if ( $_REQUEST['cf7_check_cognayls'] == 2 ) {
		$keymatchs = $_REQUEST['keymatch'];
		$otps = $_REQUEST['otp'];
		if ($keymatchs != "") {
			$keymatchs = split(',', $keymatchs);
			$otps = split(',', $otps);
			$json = "";

			for ($i = 0; $i < count($keymatchs); $i ++) {
				$keymatch = $keymatchs[$i];
				$otp = $otps[$i];
				$url = 'https://www.cognalys.com/api/v1/otp/confirm/?app_id=' . $cognalys_app_id . '&access_token=' . $cognalys_access_token . '&keymatch=' . $keymatch . '&otp=' . $otp;
				if ($json != "") {
					$json .= ",";
				}
				$json .= file_get_contents($url);
			}
			$json = "[" . $json . "]";
			
			if ( wpcf7_is_xhr() ) {
				@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
				echo $json;
			} else {
				@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
				echo '<textarea>' . $json . '</textarea>';
			}
		}

		exit();
	}
}
?>