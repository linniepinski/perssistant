<?php

class WPML_TM_Translation_Status_Display extends WPML_Full_PT_API {

	private $statuses = array();

	/** @var  WPML_Post_Status $status_helper */
	private $status_helper;

	/** @var WPML_Translation_Job_Factory $job_factory */
	private $job_factory;

	/** @var WPML_TM_API $tm_api */
	private $tm_api;

	/**
	 * WPML_TM_Translation_Status_Display constructor.
	 *
	 * @param wpdb                         $wpdb
	 * @param SitePress                    $sitepress
	 * @param WPML_Post_Status             $status_helper
	 * @param WPML_Translation_Job_Factory $job_factory
	 * @param WPML_TM_API                  $tm_api
	 */
	public function __construct(
		&$wpdb,
		&$sitepress,
		&$status_helper,
		&$job_factory,
		&$tm_api
	) {
		$post_translation = $sitepress->post_translations();
		parent::__construct( $wpdb, $sitepress, $post_translation );
		$this->status_helper = &$status_helper;
		$this->job_factory   = &$job_factory;
		$this->tm_api        = &$tm_api;
	}

	public function init() {
		add_action( 'wpml_cache_clear', array( $this, 'init' ), 11, 0 );
		add_filter( 'wpml_icon_to_translation', array(
			$this,
			'filter_status_icon'
		), 10, 4 );
		add_filter( 'wpml_link_to_translation', array(
			$this,
			'filter_status_link'
		), 10, 4 );
		add_filter( 'wpml_text_to_translation', array(
			$this,
			'filter_status_text'
		), 10, 4 );
		$this->statuses = array();
	}

	public function filter_status_icon( $icon, $post_id, $lang, $trid ) {
		$this->maybe_load_stats( $trid );

		if ( ( $this->is_remote( $trid, $lang )
		       || $this->is_wrong_translator( $trid, $lang ) )
		     && $this->is_in_progress( $trid, $lang )
		) {
			$icon = 'in-progress.png';
		} elseif ( $this->is_in_basket( $trid, $lang )
		           || ( ! $this->is_lang_pair_allowed( $lang ) && $this->post_translations->get_element_id( $lang, $trid ) )
		) {
			$icon = 'edit_translation_disabled.png';
		} elseif ( ! $this->is_lang_pair_allowed( $lang ) && ! $this->post_translations->get_element_id( $lang, $trid ) ) {
			$icon = 'add_translation_disabled.png';
		}

		return $icon;
	}

	public function filter_status_text( $text, $original_post_id, $lang, $trid ) {
		$this->maybe_load_stats( $trid );
		if ( $this->is_remote( $trid, $lang ) ) {
			$language = $this->sitepress->get_language_details( $lang );
			$text     = sprintf(
				__(
					"You can't edit this translation, because this translation to %s is already in progress.",
					'sitepress'
				),
				$language['display_name']
			);

		} elseif ( $this->is_in_basket( $trid, $lang ) ) {
			$text = __(
				'Cannot edit this item, because it is currently in the translation basket.',
				'sitepress'
			);
		}

		return $text;
	}

	/**
	 * @param string $link
	 * @param int    $post_id
	 * @param string $lang
	 * @param int    $trid
	 *
	 * @return string
	 */
	public function filter_status_link( $link, $post_id, $lang, $trid ) {
		$translated_element_id = $this->post_translations->get_element_id( $lang,
			$trid );
		if ( (bool) $translated_element_id
		     && (bool) $this->post_translations->get_source_lang_code( $translated_element_id ) === false
		) {
			return $link;
		}
		$this->maybe_load_stats( $trid );
		$is_remote               = $this->is_remote( $trid, $lang );
		$is_in_progress          = $this->is_in_progress( $trid, $lang );
		$tm_editor_link_base_url = 'admin.php?page=' . WPML_TM_FOLDER . '/menu/translations-queue.php';
		$use_tm_editor           = $this->sitepress->get_setting( 'doc_translation_method' );
		if ( ( $is_remote && $is_in_progress ) || $this->is_in_basket( $trid,
				$lang ) || ! $this->is_lang_pair_allowed( $lang )
		) {
			$link = '###';
		} elseif (
			( $source_lang_code = $this->post_translations->get_source_lang_code( $translated_element_id ) )
			&& $source_lang_code !== $lang
		) {
			if ( ( $is_in_progress && ! $is_remote ) || ( $use_tm_editor
			                                              && $translated_element_id )
			) {
				$link = $tm_editor_link_base_url . '&job_id=' . $this->job_factory->job_id_by_trid_and_lang( $trid,
						$lang );
			} elseif ( $use_tm_editor && ! $translated_element_id ) {
				$link = $tm_editor_link_base_url . '&trid=' . $trid . '&language_code=' . $lang . '&source_language_code=' . $source_lang_code;
			}
		}

		return $link;
	}

	/**
	 * @param string $lang
	 *
	 * @return bool
	 */
	private function is_lang_pair_allowed( $lang ) {

		return $this->tm_api->is_translator_filter(
			false, $this->sitepress->get_wp_api()->get_current_user_id(),
			array(
				'lang_from'      => $this->sitepress->get_current_language(),
				'lang_to'        => $lang,
				'admin_override' => $this->is_current_user_admin(),
			) );
	}

	private function is_current_user_admin() {

		return $this->sitepress->get_wp_api()
		                       ->current_user_can( 'manage_options' );
	}

	/**
	 * @todo make this into a proper active record user
	 *
	 * @param int $trid
	 */
	private function maybe_load_stats( $trid ) {
		if ( ! isset( $this->statuses[ $trid ] ) ) {
			$stats                   = $this->wpdb->get_results(
				$this->wpdb->prepare(
					"SELECT st.status, l.code, st.translator_id, st.translation_service
								FROM {$this->wpdb->prefix}icl_languages l
								LEFT JOIN {$this->wpdb->prefix}icl_translations i
									ON l.code = i.language_code
								JOIN {$this->wpdb->prefix}icl_translation_status st
									ON i.translation_id = st.translation_id
								WHERE l.active = 1
									AND i.trid = %d
									OR i.trid IS NULL",
					$trid
				),
				ARRAY_A
			);
			$this->statuses[ $trid ] = array();
			foreach ( $stats as $element ) {
				$this->statuses[ $trid ][ $element['code'] ] = $element;
			}
		}
	}

	private function is_remote( $trid, $lang ) {

		return isset( $this->statuses[ $trid ][ $lang ]['translation_service'] )
		       && (bool) $this->statuses[ $trid ][ $lang ]['translation_service'] !== false
		       && $this->statuses[ $trid ][ $lang ]['translation_service'] !== 'local';
	}

	private function is_in_progress( $trid, $lang ) {

		return isset( $this->statuses[ $trid ][ $lang ]['status'] )
		       && ( $this->statuses[ $trid ][ $lang ]['status'] == ICL_TM_IN_PROGRESS
		            || $this->statuses[ $trid ][ $lang ]['status'] == ICL_TM_WAITING_FOR_TRANSLATOR );
	}

	private function is_wrong_translator( $trid, $lang ) {

		return isset( $this->statuses[ $trid ][ $lang ]['translator_id'] )
		       && $this->statuses[ $trid ][ $lang ]['translator_id']
		          != $this->sitepress->get_wp_api()->get_current_user_id()
		       && ! $this->is_current_user_admin();
	}

	private function is_in_basket( $trid, $lang ) {

		return $this->status_helper
			       ->get_status( false, $trid, $lang ) === ICL_TM_IN_BASKET;
	}
}