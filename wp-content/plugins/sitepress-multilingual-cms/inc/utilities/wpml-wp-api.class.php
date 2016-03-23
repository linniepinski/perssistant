<?php

class WPML_WP_API {
	public function get_file_mime_type( $filename ) {

		$mime_type = 'application/octet-stream';
		if ( file_exists( $filename ) ) {
			if ( function_exists( 'finfo_open' ) ) {
				$finfo     = finfo_open( FILEINFO_MIME_TYPE ); // return mime type ala mimetype extension
				$mime_type = finfo_file( $finfo, $filename );
				finfo_close( $finfo );
			} else {
				$mime_type = mime_content_type( $filename );
			}
		}

		return $mime_type;
	}

	/**
	 * Wrapper for \get_option
	 *
	 * @param string     $option
	 * @param bool|false $default
	 *
	 * @return mixed|void
	 */
	public function get_option( $option, $default = false ) {

		return get_option( $option, $default );
	}

	/**
	 * Wrapper for \get_term_link
	 *
	 * @param  object|int|string $term
	 * @param string             $taxonomy
	 *
	 * @return string|WP_Error
	 */
	public function get_term_link( $term, $taxonomy = '' ) {

		return get_term_link( $term, $taxonomy );
	}

	/**
	 * Wrapper for \add_submenu_page
	 *
	 * @param              $parent_slug
	 * @param              $page_title
	 * @param              $menu_title
	 * @param              $capability
	 * @param              $menu_slug
	 * @param array|string $function
	 *
	 * @return false|string
	 */
	public function add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {

		return add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
	}

	/**
	 * @param              $page_title
	 * @param              $menu_title
	 * @param              $capability
	 * @param              $menu_slug
	 * @param array|string $function
	 * @param string       $icon_url
	 * @param null         $position
	 *
	 * @return string
	 */
	public function add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null ) {

		return add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	}

	/**
	 * Wrapper for \get_post_type_archive_link
	 *
	 * @param string $post_type
	 *
	 * @return string
	 */
	public function get_post_type_archive_link( $post_type ) {

		return get_post_type_archive_link( $post_type );
	}

	/**
	 * Wrapper for \get_edit_post_link
	 *
	 * @param int    $id
	 * @param string $context
	 *
	 * @return null|string|void
	 */
	public function get_edit_post_link( $id = 0, $context = 'display' ) {

		return get_edit_post_link( $id, $context );
	}

	/**
	 * Wrapper for get_the_title
	 *
	 * @param int|WP_Post $post
	 *
	 * @return string
	 */
	public function get_the_title( $post ) {

		return get_the_title( $post );
	}

	/**
	 * Wrapper for \get_day_link
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 *
	 * @return string
	 */
	public function get_day_link( $year, $month, $day ) {

		return get_day_link( $year, $month, $day );
	}

	/**
	 * Wrapper for \get_month_link
	 *
	 * @param int $year
	 * @param int $month
	 *
	 * @return string
	 */
	public function get_month_link( $year, $month ) {

		return get_month_link( $year, $month );
	}

	/**
	 * Wrapper for \get_year_link
	 *
	 * @param int $year
	 *
	 * @return string
	 */
	public function get_year_link( $year ) {

		return get_year_link( $year );
	}

	/**
	 * Wrapper for \get_author_posts_url
	 *
	 * @param int    $author_id
	 * @param string $author_nicename
	 *
	 * @return string
	 */
	public function get_author_posts_url( $author_id, $author_nicename = '' ) {

		return get_author_posts_url( $author_id, $author_nicename );
	}

	/**
	 * Wrapper for \current_user_can
	 *
	 * @param string $capability
	 *
	 * @return bool
	 */
	public function current_user_can( $capability ) {

		return current_user_can( $capability );
	}

	/**
	 * @param int    $user_id
	 * @param string $key
	 * @param bool   $single
	 *
	 * @return mixed
	 */
	public function get_user_meta( $user_id, $key = '', $single = false ) {

		return get_user_meta( $user_id, $key, $single );
	}

	/**
	 * Wrapper for \get_post_type
	 *
	 * @param null|int|WP_Post $post
	 *
	 * @return false|string
	 */
	public function get_post_type( $post = null ) {

		return get_post_type( $post );
	}

	/**
	 * @param int|WP_User $user
	 * @param string      $capability
	 *
	 * @return bool
	 */
	public function user_can( $user, $capability ) {

		return user_can( $user, $capability );
	}

	public function get_tm_url( $tab = null, $hash = null ) {
		$tm_url = menu_page_url( WPML_TM_FOLDER . '/menu/main.php', false );

		$query_vars = array();
		if ( $tab ) {
			$query_vars['sm'] = $tab;
		}

		$tm_url = add_query_arg( $query_vars, $tm_url );

		if ( $hash ) {
			if ( strpos( $hash, '#' ) !== 0 ) {
				$hash = '#' . $hash;
			}
			$tm_url .= $hash;
		}

		return $tm_url;
	}

	/**
	 * Wrapper for \is_admin()
	 *
	 * @return bool
	 */
	public function is_admin() {

		return is_admin();
	}

	public function is_jobs_tab() {
		return $this->is_tm_page( 'jobs' );
	}

	public function is_tm_page( $tab = null ) {
		$result = is_admin()
		          && isset( $_GET['page'] )
		          && $_GET['page'] == WPML_TM_FOLDER . '/menu/main.php';

		if ( $tab ) {
			if ( $tab == 'dashboard' && ! isset( $_GET['sm'] ) ) {
				$result = $result && true;
			} else {
				$result = $result && isset( $_GET['sm'] ) && $_GET['sm'] == $tab;
			}
		}

		return $result;
	}

	public function is_troubleshooting_page() {
		return $this->is_core_page( 'troubleshooting.php' );
	}

	public function is_core_page( $page ) {
		$result = is_admin()
		          && isset( $_GET['page'] )
		          && $_GET['page'] == ICL_PLUGIN_FOLDER . '/menu/' . $page;

		return $result;
	}

	public function is_back_end() {
		return is_admin() && ! $this->is_ajax() && ! $this->is_cron_job();
	}

	public function is_front_end() {
		return ! is_admin() && ! $this->is_ajax() && ! $this->is_cron_job();
	}

	public function is_ajax() {

		return ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || wpml_is_ajax();
	}

	public function is_cron_job() {
		return defined( 'DOING_CRON' ) && DOING_CRON;
	}

	public function is_heartbeat() {
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );

		return $action == 'heartbeat';
	}

	/**
	 * Wrapper for \is_feed that returns false if called before the loop
	 *
	 * @param string $feeds
	 *
	 * @return bool
	 */
	public function is_feed( $feeds = '' ) {
		global $wp_query;

		return isset( $wp_query ) && is_feed( $feeds );
	}

	/**
	 * Wrapper for \wp_update_term_count
	 *
	 * @param  int[]     $terms given by their term_taxonomy_ids
	 * @param  string    $taxonomy
	 * @param bool|false $do_deferred
	 *
	 * @return bool
	 */
	function wp_update_term_count( $terms, $taxonomy, $do_deferred = false ) {

		return wp_update_term_count( $terms, $taxonomy, $do_deferred );
	}

	/**
	 * Wrapper for \get_taxonomy
	 *
	 * @param string $taxonomy
	 *
	 * @return bool|object
	 */
	function get_taxonomy( $taxonomy ) {

		return get_taxonomy( $taxonomy );
	}

	/**
	 * Wrapper for \wp_set_object_terms
	 *
	 * @param int              $object_id The object to relate to.
	 * @param array|int|string $terms A single term slug, single term id, or array of either term slugs or ids.
	 *                                    Will replace all existing related terms in this taxonomy.
	 * @param string           $taxonomy The context in which to relate the term to the object.
	 * @param bool             $append Optional. If false will delete difference of terms. Default false.
	 *
	 * @return array|WP_Error Affected Term IDs.
	 */
	function wp_set_object_terms( $object_id, $terms, $taxonomy, $append = false ) {

		return wp_set_object_terms( $object_id, $terms, $taxonomy, $append );
	}

	/**
	 * Wrapper for \get_post_types
	 *
	 * @param array  $args
	 * @param string $output
	 * @param string $operator
	 *
	 * @return array
	 */
	function get_post_types( $args = array(), $output = 'names', $operator = 'and' ) {

		return get_post_types( $args, $output, $operator );
	}

	function wp_send_json( $response ) {
		wp_send_json( $response );

		return $response;
	}

	function wp_send_json_success( $data = null ) {
		wp_send_json_success( $data );

		return $data;
	}

	function wp_send_json_error( $data = null ) {
		wp_send_json_error( $data );

		return $data;
	}

	/**
	 * Wrapper for \get_current_user_id
	 * @return int
	 */
	function get_current_user_id() {

		return get_current_user_id();
	}

	/**
	 * Wrapper for \get_post
	 *
	 * @param null|int|WP_Post $post
	 * @param string           $output
	 * @param string           $filter
	 *
	 * @return array|null|WP_Post
	 */
	function get_post( $post = null, $output = OBJECT, $filter = 'raw' ) {

		return get_post( $post, $output, $filter );
	}

	/**
	 * Wrapper for \get_post_meta
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key Optional. The meta key to retrieve. By default, returns
	 *                        data for all keys. Default empty.
	 * @param bool   $single Optional. Whether to return a single value. Default false.
	 *
	 * @return mixed Will be an array if $single is false. Will be value of meta data
	 *               field if $single is true.
	 */
	public function get_post_meta( $post_id, $key = '', $single = false ) {

		return get_post_meta( $post_id, $key, $single );
	}

	/**
	 * Wrapper for \update_post_meta
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key
	 * @param mixed  $value
	 * @param mixed  $prev_value
	 *
	 * @return int|bool
	 */
	public function update_post_meta(
		$post_id,
		$key,
		$value,
		$prev_value = ''
	) {

		return update_post_meta( $post_id, $key, $value, $prev_value );
	}

	/**
	 * Wrapper for \get_term_meta
	 *
	 * @param int    $term_id
	 * @param string $key
	 * @param bool   $single
	 *
	 * @return mixed
	 */
	function get_term_meta( $term_id, $key = '', $single = false ) {

		return get_term_meta( $term_id, $key, $single );
	}

	/**
	 * Wrapper for \get_permalink
	 *
	 * @param int        $id
	 * @param bool|false $leavename
	 *
	 * @return bool|string
	 */
	function get_permalink( $id = 0, $leavename = false ) {

		return get_permalink( $id, $leavename );
	}

	/**
	 * Wrapper for \wp_mail
	 *
	 * @param string       $to
	 * @param string       $subject
	 * @param string       $message
	 * @param string|array $headers
	 * @param array|array  $attachments
	 *
	 * @return bool
	 */
	function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {

		return wp_mail( $to, $subject, $message, $headers, $attachments );
	}

	/**
	 * Wrapper for \get_post_custom
	 *
	 * @param int $post_id
	 *
	 * @return array
	 */
	function get_post_custom( $post_id = 0 ) {

		return get_post_custom( $post_id );
	}

	function is_dashboard_tab() {
		return $this->is_tm_page( 'dashboard' );
	}

	public function wp_safe_redirect( $redir_target, $status = 302 ) {
		wp_safe_redirect( $redir_target, $status );
		exit;
	}

	/**
	 * Wrapper around PHP constant lookup
	 *
	 * @param string $constant_name
	 *
	 * @return string|int
	 */
	public function constant( $constant_name ) {

		return defined( $constant_name ) ? constant( $constant_name ) : null;
	}

	/**
	 * Wrapper for \load_textdomain
	 *
	 * @param string $domain
	 * @param string $mofile
	 *
	 * @return bool
	 */
	public function load_textdomain( $domain, $mofile ) {

		return load_textdomain( $domain, $mofile );
	}

	/**
	 * Wrapper for \get_home_url
	 *
	 * @param null|int    $blog_id
	 * @param string      $path
	 * @param null|string $scheme
	 *
	 * @return string
	 */
	public function get_home_url(
		$blog_id = null,
		$path = '',
		$scheme = null
	) {

		return get_home_url( $blog_id, $path, $scheme );
	}

	/**
	 * Wrapper for \get_site_url
	 *
	 * @param null|int    $blog_id
	 * @param string      $path
	 * @param null|string $scheme
	 *
	 * @return string
	 */
	public function get_site_url(
		$blog_id = null,
		$path = '',
		$scheme = null
	) {

		return get_site_url( $blog_id, $path, $scheme );
	}

	/**
	 * Wrapper for \is_multisite
	 *
	 * @return bool
	 */
	public function is_multisite() {

		return is_multisite();
	}

	/**
	 * Wrapper for \ms_is_switched
	 *
	 * @return bool
	 */
	public function ms_is_switched() {

		return ms_is_switched();
	}

	/**
	 * Wrapper for \get_current_blog_id
	 *
	 * @return int
	 */
	public function get_current_blog_id() {

		return get_current_blog_id();
	}

	/**
	 * Wrapper for wp_get_post_terms
	 *
	 * @param int $post_id
	 * @param string $taxonomy
	 * @param array $args
	 *
	 * @return array|WP_Error
	 */
	public function wp_get_post_terms(
		$post_id = 0,
		$taxonomy = 'post_tag',
		$args = array()
	) {

		return wp_get_post_terms( $post_id, $taxonomy, $args );
	}

	/**
	 * Wrapper for get_taxonomies
	 *
	 * @param array  $args
	 * @param string $output
	 * @param string $operator
	 *
	 * @return array
	 */
	public function get_taxonomies(
		$args = array(),
		$output = 'names',
		$operator = 'and'
	) {

		return get_taxonomies( $args, $output, $operator );
	}

	/**
	 * Wrapper for \wp_get_theme
	 *
	 * @param string $stylesheet
	 * @param string $theme_root
	 *
	 * @return WP_Theme
	 */
	public function wp_get_theme( $stylesheet = null, $theme_root = null ) {

		return wp_get_theme( $stylesheet, $theme_root );
	}

	/**
	 * Wrapper for \wp_get_theme->get('Name')
	 *
	 * @return string
	 */
	public function get_theme_name() {

		return wp_get_theme()->get( 'Name' );
	}

	/**
	 * Wrapper for \wp_get_theme->get('URI')
	 *
	 * @return string
	 */
	public function get_theme_URI() {

		return wp_get_theme()->get( 'URI' );
	}

	/**
	 * Wrapper for \wp_get_theme->get('Author')
	 *
	 * @return string
	 */
	public function get_theme_author() {

		return wp_get_theme()->get( 'Author' );
	}

	/**
	 * Wrapper for \wp_get_theme->get('AuthorURI')
	 *
	 * @return string
	 */
	public function get_theme_authorURI() {

		return wp_get_theme()->get( 'AuthorURI' );
	}

	/**
	 * Wrapper for \wp_get_theme->get('Template')
	 *
	 * @return string
	 */
	public function get_theme_template() {

		return wp_get_theme()->get( 'Template' );
	}

	/**
	 * Wrapper for \wp_get_theme->get('Version')
	 *
	 * @return string
	 */
	public function get_theme_version() {

		return wp_get_theme()->get( 'Version' );
	}

	/**
	 * Wrapper for \wp_get_theme->get('TextDomain')
	 *
	 * @return string
	 */
	public function get_theme_textdomain() {

		return wp_get_theme()->get( 'TextDomain' );
	}

	/**
	 * Wrapper for \wp_get_theme->get('DomainPath')
	 *
	 * @return string
	 */
	public function get_theme_domainpath() {

		return wp_get_theme()->get( 'DomainPath' );
	}

	/**
	 * Wrapper for \get_plugins()
	 *
	 * @return array
	 */
	public function get_plugins() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		return get_plugins();
	}

	/**
	 * Wrapper for \get_post_custom_keys
	 *
	 * @param int $post_id
	 *
	 * @return array|void
	 */
	public function get_post_custom_keys( $post_id ) {

		return get_post_custom_keys( $post_id );
	}

	/**
	 * Wrapper for \get_bloginfo
	 *
	 * @param string $show (optional)
	 * @param string $filter (optional)
	 *
	 * @return string
	 */
	function get_bloginfo( $show = '', $filter = 'raw' ) {

		return get_bloginfo( $show, $filter );
	}

	/**
	 * Wrapper for \phpversion()
	 *
	 * * @param string $extension (optional)
	 *
	 * @return string
	 */
	function phpversion( $extension = '' ) {

		return phpversion( $extension );
	}
}