<?php

class JEP_Fields_Init extends JEP_Fields_Base{
	const FILTER_REPONSE_JOB 	= 'et_jobs_ajax_response';

	public function __construct(){
		parent::__construct();
		$this->add_filter( self::FILTER_REPONSE_JOB, 'update_response_job');

		$this->add_action( 'je_filter_resume_fields', 'filter_resume_fields' );
		$this->add_action( 'je_filter_resume_taxs', 'filter_resume_taxs' );

		$this->add_filter( 'et_get_translate_string', 'add_translate_string' );

	}

	public function filter_resume_fields ($extefields) {
		$fields = JEP_Field::get_all_fields('resume');
		foreach ($fields as $key => $field) {
			if( in_array($field->type, array('text','textarea','url','file','image','date')  ) )
				 $extefields[] =  $field->name;
		}
		return $extefields;

	}

	public function filter_resume_taxs($extefields) {
		$fields = JEP_Field::get_all_fields('resume');
		foreach ($fields as $key => $field) {

			if( !in_array($field->type,array( 'text','textarea','url','file', 'image','date' ) )){
				$extefields[] =  $field->name;;
			}

		}
		return $extefields;
	}

	function add_translate_string ($entries) {
		$pot		=	new PO();
		$pot->import_from_file(dirname(__FILE__).'/default.po',true );
		return	array_merge($entries, $pot->entries);
	}

	/**
	 *
	 */
	public function update_response_job($job){
		$fields = JEP_Field::get_all_fields();
		$return = array();
		foreach ($fields as $field) {
			switch ($field->type) {
				case 'text':
				case 'select':
				default:
					$return[] = array(
						'ID' 	=> $field->ID,
						'type' 	=> $field->type,
						'value' => get_post_meta( $job['ID'], 'cfield-'. $field->ID, true )
					);
					break;

				case 'date':
					$time	=	get_post_meta( $job['ID'], 'cfield-'. $field->ID, true );
					if($time === '' || strtotime($time) === 0 )
						$time = '';
					else
						$time	=	date( get_option('date_format'), strtotime($time)) ;
					$return[] = array(
						'ID' 	=> $field->ID,
						'type' 	=> $field->type,
						'value' => $time
					);
					break;
			}
		}
		$job['fields'] = $return;
		return $job;
	}
}