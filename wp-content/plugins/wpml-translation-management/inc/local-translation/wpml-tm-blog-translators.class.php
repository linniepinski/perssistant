<?php

class WPML_TM_Blog_Translators extends WPML_SP_User {

	/** @var WPML_TM_Records $tm_records */
	private $tm_records;

	/**
	 * @param SitePress       $sitepress
	 * @param WPML_TM_Records $tm_records
	 */
	public function __construct( &$sitepress, &$tm_records ) {
		parent::__construct( $sitepress );
		$this->tm_records = &$tm_records;
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	function get_blog_translators( $args = array() ) {
		$translators = TranslationManagement::get_blog_translators( $args );
		foreach ( $translators as $key => $user ) {
			$translators[ $key ] = isset( $user->data ) ? $user->data : $user;
		}

		return $translators;
	}

	/**
	 * @param int   $user_id
	 * @param array $args
	 *
	 * @return bool
	 */
	function is_translator( $user_id, $args = array() ) {
		$admin_override = true;
		extract( $args, EXTR_OVERWRITE );
		$is_translator = $this->sitepress->get_wp_api()
		                                 ->user_can( $user_id, 'translate' );
		// check if user is administrator and return true if he is
		if ( $admin_override && $this->sitepress->get_wp_api()
		                                        ->user_can( $user_id, 'activate_plugins' )
		) {
			$is_translator = true;
		} else {
			if ( isset( $lang_from ) && isset( $lang_to ) ) {
				$um            = $this->get_language_pairs( $user_id );
				$is_translator = $is_translator && isset( $um[ $lang_from ] )
				                 && isset( $um[ $lang_from ][ $lang_to ] )
				                 && $um[ $lang_from ][ $lang_to ];
			}
			if ( isset( $job_id ) ) {
				$job_record    = $this->tm_records->icl_translate_job_by_job_id( $job_id );
				$translator_id = in_array( $job_record->service(), array(
					'local',
					0
				) ) ? $job_record->translator_id() : - 1;
				$is_translator = $translator_id == $user_id
				                 || ( $is_translator && empty( $translator_id ) );
			}
		}

		return apply_filters( 'wpml_override_is_translator', $is_translator, $user_id, $args );
	}

	/**
	 * @param int $user_id
	 *
	 * @return array
	 */
	public function get_language_pairs( $user_id ) {

		return $this->sitepress->get_wp_api()
		                       ->get_user_meta(
			                       $user_id,
			                       $this->sitepress->wpdb()->prefix . 'language_pairs',
			                       true );
	}
}