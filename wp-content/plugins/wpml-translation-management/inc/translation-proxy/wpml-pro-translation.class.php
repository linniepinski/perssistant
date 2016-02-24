<?php
/**
 * @package wpml-core
 * @package wpml-core-pro-translation
 */

/**
 * Class WPML_Pro_Translation
 */
class WPML_Pro_Translation extends WPML_TM_Job_Factory_User {

	public $errors = array();
	/** @var TranslationManagement $tmg */
	private $tmg;

	/** @var  WPML_TM_CMS_ID $cms_id_helper */
	private $cms_id_helper;

	/** @var WPML_TM_Xliff_Reader_Factory $xliff_reader_factory */
	private $xliff_reader_factory;
	/**
	 * WPML_Pro_Translation constructor.
	 *
	 * @param WPML_Translation_Job_Factory $job_factory
	 */
	function __construct( &$job_factory ) {
		parent::__construct( $job_factory );
		global $iclTranslationManagement, $wpdb;
		$this->tmg                  =& $iclTranslationManagement;
		$this->xliff_reader_factory = new WPML_TM_Xliff_Reader_Factory( $this->job_factory );
		$wpml_tm_records            = new WPML_TM_Records( $wpdb );
		$this->cms_id_helper        = new WPML_TM_CMS_ID( $wpml_tm_records, $job_factory );;
		add_filter( 'xmlrpc_methods', array( $this, 'custom_xmlrpc_methods' ) );
		add_action( 'post_submitbox_start', array(
			$this,
			'post_submitbox_start'
		) );
		add_action( 'icl_ajx_custom_call', array(
			$this,
			'ajax_calls'
		), 10, 2 );
	}

	/**
	 * @param string $call
	 * @param array  $data
	 */
	function ajax_calls( $call, $data ) {
		global $sitepress;

		switch ( $call ) {
			case 'set_pickup_mode':
				$method                                   = intval( $data['icl_translation_pickup_method'] );
				$iclsettings['translation_pickup_method'] = $method;
				$sitepress->save_settings( $iclsettings );

				try {
					$project = TranslationProxy::get_current_project();
					if ( $project ) {
						$project->set_delivery_method( ICL_PRO_TRANSLATION_PICKUP_XMLRPC == $method ? 'xmlrpc' : 'polling' );
					}
				} catch ( Exception $e ) {
					echo wp_json_encode( array( 'error' => __( 'Could not update the translation pickup mode.', 'sitepress' ) ) );
				}

				echo json_encode( array( 'message' => 'OK' ) );
				break;
		}
	}

	/**
	 * @param WP_Post|WPML_Package $post
	 * @param                      $target_languages
	 * @param int                  $translator_id
	 * @param                      $job_id
	 *
	 * @return bool|int
	 */
	function send_post( $post, $target_languages, $translator_id, $job_id ) {
		global $sitepress, $iclTranslationManagement;

		$this->maybe_init_translation_management( $iclTranslationManagement );

		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}
		if ( ! $post ) {
			return false;
		}

		$post_id             = $post->ID;
		$post_type           = $post->post_type;
		$element_type_prefix = $iclTranslationManagement->get_element_type_prefix_from_job_id( $job_id );
		$element_type        = $element_type_prefix . '_' . $post_type;

		$note = get_post_meta( $post_id, '_icl_translator_note', true );
		if ( ! $note ) {
			$note = null;
		}
		$err             = false;
		$res             = false;
		$source_language = $sitepress->get_language_for_element( $post_id, $element_type );
		$target_language = is_array( $target_languages ) ? end( $target_languages ) : $target_languages;
		if ( empty( $target_language ) || $target_language === $source_language ) {
			return false;
		}
		$translation = $this->tmg->get_element_translation( $post_id, $target_language, $element_type );
		if ( ! $translation ) { // translated the first time
			$err = true;
		}
		if ( ! $err && ( $translation->needs_update || $translation->status == ICL_TM_NOT_TRANSLATED || $translation->status == ICL_TM_WAITING_FOR_TRANSLATOR ) ) {
			$project       = TranslationProxy::get_current_project();

			if ( $iclTranslationManagement->is_external_type( $element_type_prefix ) ) {
				$job_object = new WPML_External_Translation_Job( $job_id );
			} else {
				$job_object = new WPML_Post_Translation_Job( $job_id );
				$job_object->load_terms_from_post_into_job();
			}

			list( $err, $project, $res ) = $job_object->send_to_tp( $project, $translator_id, $this->cms_id_helper, $this->tmg, $note );
			if ( $err ) {
				$this->enqueue_project_errors( $project );
			}

		}

		return $err ? false : $res; //last $ret
	}

	function server_languages_map( $language_name, $server2plugin = false ) {
		if ( is_array( $language_name ) ) {
			return array_map( array( $this, 'server_languages_map' ), $language_name );
		}
		$map = array(
			'Norwegian Bokmål'     => 'Norwegian',
			'Portuguese, Brazil'   => 'Portuguese',
			'Portuguese, Portugal' => 'Portugal Portuguese'
		);

		$map = $server2plugin ? array_flip( $map ) : $map;

		return isset( $map[ $language_name ] ) ? $map[ $language_name ] : $language_name;
	}

	/**
	 * @param $methods
	 *
	 * @return array
	 */
	function custom_xmlrpc_methods( $methods ) {
		$icl_methods[ 'translationproxy.test_xmlrpc' ]                = '__return_true';
		$icl_methods[ 'translationproxy.updated_job_status' ]         = array( $this, 'xmlrpc_updated_job_status_with_log' );
		$methods = array_merge( $methods, $icl_methods );
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			if ( preg_match( '#<methodName>([^<]+)</methodName>#i', $GLOBALS[ 'HTTP_RAW_POST_DATA' ], $matches ) ) {
				$method = $matches[ 1 ];
				if ( in_array( $method, array_keys( $icl_methods ) ) ) {
					set_error_handler( array( $this, "translation_error_handler" ), E_ERROR | E_USER_ERROR );
				}
			}
		}

		return $methods;
	}

	/**
	 * @param array $args
	 *
	 * @param bool  $bypass_auth
	 *
	 * @return int|string
	 */
	function xmlrpc_updated_job_status_with_log( $args, $bypass_auth = false ) {

		require_once WPML_TM_PATH . '/inc/translation-proxy/translationproxy-com-log.class.php';

		TranslationProxy_Com_Log::log_xml_rpc( array(
				'tp_job_id' => $args[0],
				'cms_id'    => $args[1],
				'status'    => $args[2],
				'signature' => 'UNDISCLOSED'
		) );
		$args[3] = $bypass_auth ? true : $args[3];
		$project = TranslationProxy::get_current_project();
		if ( $project ) {
			$update = new WPML_TM_XmlRpc_Job_Update( $project, $this );
			$ret    = $update->update_status( $args, $bypass_auth );
		} else {
			$ret = "Project does not exist";
		}

		TranslationProxy_Com_Log::log_xml_rpc( array( 'result' => $ret ) );

		return $ret;
	}

	/**
	 *
	 * Cancel translation for given cms_id
	 *
	 * @param $rid
	 * @param $cms_id
	 * @return bool
	 */
	function cancel_translation( $rid, $cms_id ) {
		/**
		 * @var WPML_String_Translation $WPML_String_Translation
		 * @var TranslationManagement $iclTranslationManagement
		 */
		global $sitepress, $wpdb, $WPML_String_Translation, $iclTranslationManagement;

		$res           = false;
		if ( empty( $cms_id ) ) { // it's a string
			if ( isset( $WPML_String_Translation ) ) {
				$res = $WPML_String_Translation->cancel_remote_translation( $rid ) ;
			}
		}
		else{
			$cms_id_parts      = $this->cms_id_helper->parse_cms_id( $cms_id );
			$post_type    = $cms_id_parts[ 0 ];
			$_element_id  = $cms_id_parts[ 1 ];
			$_target_lang = $cms_id_parts[ 3 ];
			$job_id       = isset( $cms_id_parts[ 4 ] ) ? $cms_id_parts[ 4 ] : false;

			$element_type_prefix = 'post';
			if ( $job_id ) {
				$element_type_prefix = $iclTranslationManagement->get_element_type_prefix_from_job_id( $job_id );
			}

			$element_type = $element_type_prefix . '_' . $post_type;
			if ( $_element_id && $post_type && $_target_lang ) {
				$trid = $sitepress->get_element_trid( $_element_id, $element_type );
			} else {
				$trid = null;
			}

			if ( $trid ) {
				$translation_id_query   = "SELECT i.translation_id
																FROM {$wpdb->prefix}icl_translations i
																JOIN {$wpdb->prefix}icl_translation_status s
																ON i.translation_id = s.translation_id
																WHERE i.trid=%d
																	AND i.language_code=%s
																	AND s.status IN (%d, %d)
																LIMIT 1";
				$translation_id_args    = array( $trid, $_target_lang, ICL_TM_IN_PROGRESS, ICL_TM_WAITING_FOR_TRANSLATOR );
				$translation_id_prepare = $wpdb->prepare( $translation_id_query, $translation_id_args );
				$translation_id = $wpdb->get_var( $translation_id_prepare );

				if ( $translation_id ) {
					global $iclTranslationManagement;
					$iclTranslationManagement->cancel_translation_request( $translation_id );
					$res = true;
				}
			}
		}

		return $res;
	}

	/**
	 *
	 * Downloads translation from TP and updates its document
	 *
	 * @param $translation_proxy_job_id
	 * @param $cms_id
	 *
	 * @return bool|string
	 *
	 */
	function download_and_process_translation( $translation_proxy_job_id, $cms_id ) {
		global $wpdb;

		if ( empty( $cms_id ) ) { // it's a string
			//TODO: [WPML 3.3] this should be handled as any other element type in 3.3
			$target = $wpdb->get_var( $wpdb->prepare( "SELECT target FROM {$wpdb->prefix}icl_core_status WHERE rid=%d", $translation_proxy_job_id ) );

			return $this->process_translated_string( $translation_proxy_job_id, $target );
		} else {
			$translation_id = $this->cms_id_helper->get_translation_id( $cms_id, TranslationProxy::get_current_service() );

			return ! empty ( $translation_id ) && $this->add_translated_document( $translation_id, $translation_proxy_job_id );
		}
	}

	/**
	 * @param int $translation_id
	 * @param int $translation_proxy_job_id
	 *
	 * @return bool
	 */
	function add_translated_document( $translation_id, $translation_proxy_job_id ) {
		global $wpdb, $sitepress;
		$project = TranslationProxy::get_current_project();

		$translation_info = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}icl_translations WHERE translation_id=%d", $translation_id ) );
		$translation      = $project->fetch_translation( $translation_proxy_job_id );
		if ( ! $translation ) {
			$this->errors = array_merge( $this->errors, $project->errors );
		} else {
			$translation = apply_filters( 'icl_data_from_pro_translation', $translation );
		}
		$ret = true;

		if ( ! empty( $translation ) && strpos( $translation, 'xliff' ) !== false ) {
			try {
				/** @var $job_xliff_translation WP_Error|array */
				$job_xliff_translation = $this->xliff_reader_factory
					->general_xliff_import()->import( $translation, $translation_id );
				if ( is_wp_error( $job_xliff_translation ) ) {
					$this->add_error( $job_xliff_translation->get_error_message() );

					return false;
				}
				wpml_tm_save_data( $job_xliff_translation );
				$translations = $sitepress->get_element_translations( $translation_info->trid, $translation_info->element_type, false, true, true );
				if ( isset( $translations[ $translation_info->language_code ] ) ) {
					$translation = $translations[ $translation_info->language_code ];
					if ( isset( $translation->element_id ) && $translation->element_id ) {
						$translation_post_type_prepared = $wpdb->prepare( "SELECT post_type FROM $wpdb->posts WHERE ID=%d", array( $translation->element_id ) );
						$translation_post_type          = $wpdb->get_var( $translation_post_type_prepared );
					} else {
						$translation_post_type = implode( '_', array_slice( explode( '_', $translation_info->element_type ), 1 ) );
					}
					if ( $translation_post_type == 'page' ) {
						$url = get_option( 'home' ) . '?page_id=' . $translation->element_id;
					} else {
						$url = get_option( 'home' ) . '?p=' . $translation->element_id;
					}
					$project->update_job( $translation_proxy_job_id, $url );
				} else {
					$project->update_job( $translation_proxy_job_id );
				}
			} catch ( Exception $e ) {
				$ret = false;
			}
		}

		return $ret;
	}

    function _content_get_link_paths($body) {
      
        $regexp_links = array(
                            /*"/<a.*?href\s*=\s*([\"\']??)([^\"]*)[\"\']>(.*?)<\/a>/i",*/
                            "/<a[^>]*href\s*=\s*([\"\']??)([^\"^>]+)[\"\']??([^>]*)>/i",
                            );
        
        $links = array();
        
        foreach($regexp_links as $regexp) {
            if (preg_match_all($regexp, $body, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                  $links[] = $match;
                }
            }
        }
        return $links;
    }    
    
    public static function _content_make_links_sticky($element_id, $element_type='post', $string_translation = true) {        
        if(strpos($element_type, 'post') === 0){
            // only need to do it if sticky links is not enabled.
            // create the object
            require_once ICL_PLUGIN_PATH . '/inc/absolute-links/absolute-links.class.php';        
            $icl_abs_links = new AbsoluteLinks;
            $icl_abs_links->process_post($element_id);
        }elseif($element_type=='string'){             
            require_once ICL_PLUGIN_PATH . '/inc/absolute-links/absolute-links.class.php';        
            $icl_abs_links = new AbsoluteLinks; // call just for strings
            $icl_abs_links->process_string($element_id, $string_translation);                                        
        }
    }

    function _content_fix_links_to_translated_content($element_id, $target_lang_code, $element_type='post'){
        global $wpdb, $sitepress, $wp_taxonomies;
        self::_content_make_links_sticky($element_id, $element_type);

		$post = false;
		$body = false;
        if(strpos($element_type, 'post') === 0){
            $post_prepared = $wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE ID=%d", array($element_id));
            $post = $wpdb->get_row($post_prepared);
            $body = $post->post_content;
        }elseif($element_type=='string'){
            $body_prepared = $wpdb->prepare("SELECT value FROM {$wpdb->prefix}icl_string_translations WHERE id=%d", array($element_id));
            $body = $wpdb->get_var($body_prepared);
        }
        $new_body = $body;

        $base_url_parts = parse_url(site_url());
        
        $links = $this->_content_get_link_paths($body);
        
        $all_links_fixed = 1;

        $pass_on_query_vars = array();
        $pass_on_fragments = array();

		$all_links_arr = array();

        foreach($links as $link_idx => $link) {
            $path = $link[2];
            $url_parts = parse_url($path);
            
            if(isset($url_parts['fragment'])){
                $pass_on_fragments[$link_idx] = $url_parts['fragment'];
            }
            
            if((!isset($url_parts['host']) or $base_url_parts['host'] == $url_parts['host']) and
                    (!isset($url_parts['scheme']) or $base_url_parts['scheme'] == $url_parts['scheme']) and
                    isset($url_parts['query'])) {
                $query_parts = explode('&', $url_parts['query']);
                
                foreach($query_parts as $query){
                    // find p=id or cat=id or tag=id queries
                    list($key, $value) = explode('=', $query);
                    $translations = NULL;
                    $is_tax = false;
					$kind = false;
					$taxonomy = false;
                    if($key == 'p'){
                        $kind = 'post_' . $wpdb->get_var( $wpdb->prepare("SELECT post_type
																		  FROM {$wpdb->posts}
																		  WHERE ID = %d ",
                                                                         $value));
                    } else if($key == "page_id"){
                        $kind = 'post_page';
                    } else if($key == 'cat' || $key == 'cat_ID'){
                        $kind = 'tax_category';
                        $taxonomy = 'category';
                    } else if($key == 'tag'){
                        $is_tax = true;
                        $taxonomy = 'post_tag';
                        $kind = 'tax_' . $taxonomy;                    
                        $value = $wpdb->get_var($wpdb->prepare("SELECT term_taxonomy_id
																FROM {$wpdb->terms} t
                                                                JOIN {$wpdb->term_taxonomy} x
                                                                  ON t.term_id = x.term_id
                                                                WHERE x.taxonomy = %s
                                                                  AND t.slug = %s", $taxonomy, $value ) );
                    } else {
                        $found = false;
                        foreach($wp_taxonomies as $taxonomy_name => $taxonomy_object){
                            if($taxonomy_object->query_var && $key == $taxonomy_object->query_var){
                                $found = true;
                                $is_tax = true;
                                $kind = 'tax_' . $taxonomy_name;
                                $value = $wpdb->get_var($wpdb->prepare("
                                    SELECT term_taxonomy_id
                                    FROM {$wpdb->terms} t
                                    JOIN {$wpdb->term_taxonomy} x
                                      ON t.term_id = x.term_id
                                    WHERE x.taxonomy = %s
                                      AND t.slug = %s",
                                    $taxonomy_name, $value ));
                                $taxonomy = $taxonomy_name;
                            }                        
                        }
                        if(!$found){
                            $pass_on_query_vars[$link_idx][] = $query;
                            continue;
                        } 
                    }

                    $link_id = (int)$value;  
                    
                    if (!$link_id) {
                        continue;
                    }

                    $trid = $sitepress->get_element_trid($link_id, $kind);
                    if(!$trid){
                        continue;
                    }
                    if($trid !== NULL){
                        $translations = $sitepress->get_element_translations($trid, $kind);
                    }
                    if(isset($translations[$target_lang_code]) && $translations[$target_lang_code]->element_id != null){
                        
                        // use the new translated id in the link path.
                        
                        $translated_id = $translations[$target_lang_code]->element_id;
                        
                        if($is_tax){
                            $translated_id = $wpdb->get_var($wpdb->prepare("SELECT slug
																			FROM {$wpdb->terms} t
																			JOIN {$wpdb->term_taxonomy} x
																				ON t.term_id=x.term_id
																			WHERE x.term_taxonomy_id = %d",
                                                                           $translated_id));
                        }
                        
                        // if absolute links is not on turn into WP permalinks                                                
                        if(empty($GLOBALS['WPML_Sticky_Links'])){
                            ////////
							$replace = false;
                            if(preg_match('#^post_#', $kind)){
                                $replace = get_permalink($translated_id);
                            }elseif(preg_match('#^tax_#', $kind)){
                                if(is_numeric($translated_id)) $translated_id = intval($translated_id);
                                $replace = get_term_link($translated_id, $taxonomy);                                
                            }
                            $new_link = str_replace($link[2], $replace, $link[0]);
                            
                            $replace_link_arr[$link_idx] = array('from'=> $link[2], 'to'=>$replace);
                        }else{
                            $replace = $key . '=' . $translated_id;
							$new_link = $link[0];
							if($replace) {
                            	$new_link = str_replace($query, $replace, $link[0]);
							}
                            
                            $replace_link_arr[$link_idx] = array('from'=> $query, 'to'=>$replace);
                        }
                        
                        // replace the link in the body.                        
                        // $new_body = str_replace($link[0], $new_link, $new_body);
                        $all_links_arr[$link_idx] = array('from'=> $link[0], 'to'=>$new_link);
                        // done in the next loop
                        
                    } else {
                        // translation not found for this.
                        $all_links_fixed = 0;
                    }
                }
            }
                        
        }

		if ( !empty( $replace_link_arr ) ) {
			foreach ( $replace_link_arr as $link_idx => $rep ) {
				$rep_to   = $rep[ 'to' ];
				$fragment = '';

				// if sticky links is not ON, fix query parameters and fragments
				if ( empty( $GLOBALS[ 'WPML_Sticky_Links' ] ) ) {
					if ( !empty( $pass_on_fragments[ $link_idx ] ) ) {
						$fragment = '#' . $pass_on_fragments[ $link_idx ];
					}
					if ( !empty( $pass_on_query_vars[ $link_idx ] ) ) {
						$url_glue = ( strpos( $rep[ 'to' ], '?' ) === false ) ? '?' : '&';
						$rep_to   = $rep[ 'to' ] . $url_glue . join( '&', $pass_on_query_vars[ $link_idx ] );
					}
				}

				$all_links_arr[ $link_idx ][ 'to' ] = str_replace( $rep[ 'to' ], $rep_to . $fragment, $all_links_arr[ $link_idx ][ 'to' ] );

			}
		}
        
        if(!empty($all_links_arr))
        foreach($all_links_arr as $link){
            $new_body = str_replace($link['from'], $link['to'], $new_body);
        }
        
        if ($new_body != $body){
            
            // save changes to the database.
            if(strpos($element_type, 'post') === 0){        
                $wpdb->update($wpdb->posts, array('post_content'=>$new_body), array('ID'=>$element_id));
                
                // save the all links fixed status to the database.
                $icl_element_type = 'post_' . $post->post_type;
                $translation_id = $wpdb->get_var($wpdb->prepare("SELECT translation_id
																 FROM {$wpdb->prefix}icl_translations
																 WHERE element_id=%d
																  AND element_type=%s",
                                                                $element_id,
                                                                $icl_element_type));
	            $q          = "UPDATE {$wpdb->prefix}icl_translation_status SET links_fixed=%s WHERE translation_id=%d";
	            $q_prepared = $wpdb->prepare( $q, array( $all_links_fixed, $translation_id ) );
                $wpdb->query($q_prepared);
                
            }elseif($element_type == 'string'){
                $wpdb->update($wpdb->prefix.'icl_string_translations', array('value'=>$new_body), array('id'=>$element_id));
            }
                    
        }
        
    }

	function translation_error_handler($error_number, $error_string, $error_file, $error_line){
        switch($error_number){
            case E_ERROR:
            case E_USER_ERROR:
                throw new Exception ($error_string . ' [code:e' . $error_number . '] in '. $error_file . ':' . $error_line);
            case E_WARNING:
            case E_USER_WARNING:
                return true;                
            default:
                return true;
        }
        
    }    
    
    function post_submitbox_start(){
        global $post, $iclTranslationManagement;
        if(empty($post)|| !$post->ID){
            return;
        }
        
        $translations = $iclTranslationManagement->get_element_translations($post->ID, 'post_' . $post->post_type);
        $show_box = 'display:none';
        foreach($translations as $t){
            if($t->element_id == $post->ID){
				return;
            } 
            if($t->status == ICL_TM_COMPLETE && !$t->needs_update){
                $show_box = '';
                break;
            }
        }
        
        echo '<p id="icl_minor_change_box" style="float:left;padding:0;margin:3px;'.$show_box.'">';
        echo '<label><input type="checkbox" name="icl_minor_edit" value="1" style="min-width:15px;" />&nbsp;';
        echo __('Minor edit - don\'t update translation','sitepress');        
        echo '</label>';
        echo '<br clear="all" />';
        echo '</p>';
    }

	private function process_translated_string( $translation_proxy_job_id, $language ) {
		$project     = TranslationProxy::get_current_project();
		$translation = $project->fetch_translation( $translation_proxy_job_id );
		$translation = apply_filters( 'icl_data_from_pro_translation', $translation );
		$ret         = false;
		$translation = $this->xliff_reader_factory->string_xliff_reader()->get_data( $translation );
		if ( $translation ) {
			$ret = icl_translation_add_string_translation( $translation_proxy_job_id, $translation, $language );
			if ( $ret ) {
				$project->update_job( $translation_proxy_job_id );
			}
		}

		return $ret;
	}

	private function add_error( $project_error ) {
		$this->errors[] = $project_error;
	}

	/**
	 * @param $project TranslationProxy_Project
	 */
	function enqueue_project_errors( $project ) {
		if ( isset( $project ) && isset( $project->errors ) && $project->errors ) {
			foreach ( $project->errors as $project_error ) {
				$this->add_error( $project_error );
			}
		}
	}

	/**
	 * @param TranslationManagement $iclTranslationManagement
	 */
	private function maybe_init_translation_management( $iclTranslationManagement ) {
		if ( empty( $this->tmg->settings ) ) {
			$iclTranslationManagement->init();
		}
	}
}
