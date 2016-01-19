<?php

if (!class_exists('ET_Plugin_Updater')){
/**
 * Handle updating plugin for engine themes
 */
abstract class ET_Plugin_Updater{

	public $product_slug;
	public $current_version;
	public $slug;

	public function __construct(){
		// define the alternative API for updating checking  
		add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
		add_filter('plugins_api', array(&$this, 'check_info'), 10, 3);

		list ($t1, $t2) = explode('/', $this->product_slug);  
        $this->slug = str_replace('.php', '', $t2);

        if (empty($this->update_path)){
        	$this->update_path = add_query_arg( array(
        		'product' 	=> $this->slug,
        		'type' 		=> 'plugin',
        		'do' 		=> 'product-update'
        		), 'http://www.enginethemes.com/');
        }
	}

	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 * @param $transient
	 * @return object $ transient
	 */
	public function check_update($update_info)
	{
		global $wp_version;
		
		if ( empty($update_info->checked) )
			return $update_info;

		// get remote version
		$remote_version = $this->get_remote_version();
		
		// if a new version is alvaiable, add the update
		if ( version_compare( $this->current_version, $remote_version, '<')){
			$obj 				= new stdClass();
			$obj->slug 			= $this->slug;
			$obj->new_version 	= $remote_version;
			$obj->url 			= $this->update_path;
			$obj->package 		= add_query_arg( array('key' => $this->license_key) ,$this->update_path);
			$update_info->response[$this->product_slug] = $obj;
		}
		return $update_info;
	}

	/** 
     * Add our self-hosted description to the filter 
     * 
     * @param boolean $false 
     * @param array $action 
     * @param object $arg 
     * @return bool|object 
     */  
	public function check_info($false, $action, $arg){
		if ($arg->slug == $this->slug) {  
            $information = $this->get_remote_infomation();
            return $information;  
        }  
        return false;  
	}
	
	/**
	 * Return the remote information
	 * @return string $remote_version
	 */
	protected function get_remote_infomation()
	{
		$request = wp_remote_post($this->update_path, array('body' => array(
			'action' 	=> 'plugin_info', 
			'product' 	=> $this->slug,
			'key' 		=> $this->license_key)));
		
		if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
			return unserialize($request['body']);
		}  
		return false;
	}

	/**
	 * Return the remote version 
	 * @return string $remote_version
	 */
	protected function get_remote_version()
	{
		// send version request
		$request = wp_remote_post($this->update_path, array(
			'body' => array(
				'action' 		=> 'plugin_version',
				'product' 		=> $this->slug,
				'key' 			=> $this->license_key
			)));
		// check request if it is valid
		if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {  
			return $request['body'];
		}  
		return false;
	}
}

}
?>