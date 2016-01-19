<?php 
class JEP_Fields_Base{

	const FILTER_SCRIPT 	= 'et_enqueue_script';
	const FILTER_STYLE 		= 'et_enqueue_style';

	const ACTION_INIT 		= 'init';
	const ACTION_ADMIN_INIT = 'admin_init';

	public function __construct(){

	}
	
	protected function add_filter($filter, $hook, $priority = 10, $args = 1){
		add_filter($filter, array($this, $hook), $priority, $args);
	}
	protected function add_action($filter, $hook, $priority = 10, $args = 1){
		add_action($filter, array($this, $hook), $priority, $args);
	}
	protected function add_script($handle, $src, $deps = array(), $ver = false, $in_footer = false){
		$scripts = apply_filters( self::FILTER_SCRIPT, array(
			'handle' 	=> $handle,
			'src' 		=> $src,
			'deps' 		=> $deps,
			'ver' 		=> $ver,
			'in_footer' 	=> $in_footer
		));
		wp_register_script( $scripts['handle'], $scripts['src'], $scripts['deps'], $scripts['in_footer']);
		wp_enqueue_script( $scripts['handle'] );
	}

	protected function register_script($handle, $src, $deps = array(), $ver = false, $in_footer = false){
		$scripts = apply_filters( self::FILTER_SCRIPT, array(
			'handle' 	=> $handle,
			'src' 		=> $src,
			'deps' 		=> $deps,
			'ver' 		=> $ver,
			'in_footer' 	=> $in_footer
		));
		wp_register_script( $scripts['handle'], $scripts['src'], $scripts['deps'], $scripts['ver'], $scripts['in_footer']);
	}

	protected function add_existed_script($handle){
		wp_enqueue_script( $handle );
	}

	protected function add_style($handle, $src = false, $deps = array(), $ver = false, $media = 'all'){
		$style = apply_filters( self::FILTER_STYLE, array(
			'handle' 	=> $handle,
			'src' 		=> $src,
			'deps' 		=> $deps,
			'ver' 		=> $ver,
			'media' 	=> $media
		));
		wp_register_style( $style['handle'], $style['src'], $style['deps'],$style['ver'] ,  $style['media'] );
		wp_enqueue_style( $style['handle'] );
	}


	protected function register_style($handle, $src = false, $deps = array(), $ver = false, $media = 'all'){
		$style = apply_filters( self::FILTER_STYLE, array(
			'handle' 	=> $handle,
			'src' 		=> $src,
			'deps' 		=> $deps,
			'ver' 		=> $ver,
			'media' 	=> $media
		));
		wp_register_style( $style['handle'], $style['src'], $style['deps'], $style['media'] );
		wp_enqueue_style( $style['handle'] );
	}

	protected function add_existed_style($handle){
		wp_enqueue_style( $handle );
	}
}