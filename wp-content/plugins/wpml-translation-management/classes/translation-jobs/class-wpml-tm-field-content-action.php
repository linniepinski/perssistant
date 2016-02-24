<?php

class WPML_TM_Field_Content_Action extends WPML_TM_Job_Factory_User {

	/** @var  int $job_id */
	protected $job_id;

	/**
	 * WPML_TM_Field_Content_Action constructor.
	 *
	 * @param WPML_Translation_Job_Factory $job_factory
	 * @param int                          $job_id
	 */
	public function __construct( &$job_factory, $job_id ) {
		parent::__construct( $job_factory );
		if ( ! ( is_int( $job_id ) && $job_id > 0 ) ) {
			throw new InvalidArgumentException( 'Invalid job id provided, received: ' . serialize( $job_id ) );
		}
		$this->job_id = $job_id;
	}

	/**
	 * Returns an array containing job fields
	 *
	 * @return array
	 */
	public function run() {
		try {
			$job = $this->job_factory->get_translation_job( $this->job_id );
			if ( ! $job ) {
				throw new Exception( 'No job found for id: ' . $this->job_id );
			}

			return $this->content_from_elements( $job->elements );
		} catch ( Exception $e ) {
			throw new RuntimeException(
				'Could not retrieve field contents for job_id: ' . $this->job_id,
				0, $e
			);
		}
	}

	/**
	 * Extracts the to be retrieved content from given job elements
	 *
	 * @param array $elements
	 *
	 * @return array
	 */
	private function content_from_elements( array $elements ) {
		$data = array();
		foreach ( $elements as $element ) {
			$data[] = array(
				'field_type'            => sanitize_title( $element->field_type ),
				'field_data'            => $this->sanitize_field_content( $element->field_data ),
				'field_data_translated' => $this->sanitize_field_content( $element->field_data_translated )
			);
		}

		return $data;
	}

	/**
	 * @param string $content base64-encoded translation job field content
	 *
	 * @return string base64-decoded field content, with linebreaks turned into
	 * paragraph html tags
	 */
	private function sanitize_field_content( $content ) {
		$decoded = base64_decode( $content );

		return strpos( $decoded,
			"\n" ) !== false ? wpautop( $decoded )
			: $decoded;
	}
}