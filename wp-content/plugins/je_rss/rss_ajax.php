<?php 
class JE_RSS_Ajax extends JE_RSS_Import {
	function __construct() {
		parent::__construct();
		add_action('wp_ajax_rss-import-job', array($this, 'rss_import_job'));
		add_action('wp_ajax_rss-save-imported-jobs', array($this, 'save_imported_jobs'));
		add_action('wp_ajax_rss-delete-jobs', array ($this, 'delete_jobs'));
		add_action ('wp_ajax_rss-change-page', array($this, 'rss_change_page'));
		add_action ('wp_ajax_update-rss-import-schedule', array($this, 'update_rss_import_schedule'));
		add_action ('wp_ajax_rss-update-recurrent-time', array($this, 'update_recurrent_time'));
		add_action ('wp_ajax_rss-detele-schedule', array($this, 'delete_schedule'));
		add_action ('wp_ajax_rss-off-schedule', array($this, 'on_off_schedule'));
		add_action ('wp_ajax_rss-update-job-limit-date', array($this, 'update_job_limit_date'));
		add_action( 'wp_ajax_rss-delete-old-jobs', array( $this, 'delete_old_jobs' ));
	}
	/**
	 * manual delete old job from date
	*/
	function delete_old_jobs () {
		
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if(!current_user_can ('manage_options')) {
			return array (
						'success'	=> false,
						'msg'		=> __('Permission Denied', ET_DOMAIN)
					);
		}
		$count	=	$this->delete_job_from_date ();
		echo json_encode(array('success' => true, 'jobs_deteled' => $count ));
		exit;
	}

	/**
	 * update an limit date option to auto delete jobs
	*/
	function update_job_limit_date () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		$response	= array('success' => true);
		if(!current_user_can ('manage_options')) {
			$response['success']	=	 false;
			$response['msg']		=	__("Permission Denied!", ET_DOMAIN);
		}

		if( !update_option( 'je_rss_delete_days', intval($_POST['days']) )) {
			$response['success']	= false;
		}

		echo json_encode ($response);
		exit;
	}
	/**
	 * update recurrent time and schedule event
	*/
	function update_recurrent_time () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		if(!current_user_can ('manage_options')) {
			echo json_encode (array (
					'success'	=> false,
					'msg'		=> array('0' => __('Permission Denied', ET_DOMAIN) )
				)
			);
			exit;
		}
		update_option('et_rss_recurrence', $_REQUEST['time']);
		$this->schedule_activation();
	}

	/**
	 * delete rss link in schedule
	*/
	function delete_schedule () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		if(!current_user_can ('manage_options')) {
			echo json_encode (array (
					'success'	=> false,
					'msg'		=> array('0' => __('Permission Denied', ET_DOMAIN) )
				)
			);
			exit;
		}
		$option	=	$this->get_schedule_option();
		if(isset($option[$_REQUEST['schedule_id']])) {
			unset($option[$_REQUEST['schedule_id']]);
		}
		$this->update_schedule_option($option);

		echo json_encode (array (
					'success'	=> true
				)
		);
		exit;
	}
	
	/**
	 * enable or disable schedule
	*/
	function on_off_schedule () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		if(!current_user_can ('manage_options')) {
			echo json_encode (array (
					'success'	=> false,
					'msg'		=> array('0' => __('Permission Denied', ET_DOMAIN) )
				)
			);
			exit;
		}
		$icon	=	'Q';
		$option	=	$this->get_schedule_option();

		if(isset($option[$_REQUEST['schedule_id']])) {
			$schedule	=	$option[$_REQUEST['schedule_id']];
			if(isset($schedule['ON']) && $schedule['ON'] == 0 ) {
				$schedule['ON']	=	1;
				$icon	=	'Q';
			} else {
				$schedule['ON']	=	0;
				$icon	=	'q';
			}
			$option[$_REQUEST['schedule_id']]	=	$schedule;
		}
		$this->update_schedule_option($option);

		echo json_encode (array (
					'success'	=> true,
					'icon'		=> $icon
				)
		);
		exit;
	}
	/**
	 * manual rss import job ajax callback
	*/
	public function rss_import_job () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: text/plain' );
		
		if(!current_user_can ('manage_options')) {
			echo json_encode (array (
					'success'	=> false,
					'msg'		=> array('0' => __('Permission Denied', ET_DOMAIN) )
				)
			);
			exit;
		}

		$rss_link	=	isset($_REQUEST['link']) ? $_REQUEST['link'] : '';
		
		if($rss_link == '') {
			echo json_encode (array (
					'success'	=> false,
					'msg'		=> array('0' => __('Your RSS feed is invalid.', ET_DOMAIN) )
				)
			);
			exit;
		}
		$data	=	$this->get_rss_data($rss_link);
		echo json_encode($data);
		exit;
	}
	/**
	 * save import job
	*/
	function save_imported_jobs( ){
		try {
			// validate user's permission
			if(!current_user_can ('manage_options')) 
				throw new Exception(__('Permission Denied', ET_DOMAIN), '402');

			$data 	= parse_str( urldecode($_POST['content']) );
			$count 	= 0;

			foreach ((array)$import as $job) {				
				//print_r($job['allow']);
				if ( isset($job['allow']) && $job['allow'] == 1 ){
					$count += $this->manual_rss_save_jobs($job, $import_author);
				}
			}

			$response = array(
				'success' 	=> true,
				'msg' 		=> __('Jobs have been imported to your site.', ET_DOMAIN),
				'code' 		=> '200',
				'data' 		=> array(
					'count' => $count
					)
				);

		} catch (Exception $e) {
			$response = array(
				'success' 	=> false,
				'msg' 		=> $e->getMessage(),
				'code' 		=> $e->getCode(),
				'data' 		=> array(
					'count' => $count
					)
				);
		}

		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		echo json_encode($response);
		exit;
	}

	public function delete_jobs () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if(!current_user_can ('manage_options')) {
			return array (
					'success'	=> false,
					'msg'		=> __('Permission Denied', ET_DOMAIN)
				);
		}

		$data		=	$_REQUEST['content'];
 		$count 		= 0;
 		foreach ($data['ids'] as $id) {
 			if (wp_delete_post($id)){
 				$count++;
 			}
 		}
 		$page_max 	= $count >= 10 ? $data['page_max'] - 1 : $data['page_max'];
 		$page 		= min($data['page'], $page_max);

 		$query = new WP_Query(array(
								'post_type' 		=> 'job',
								'meta_key' 			=> 'et_template_id',
								'meta_value' 		=> 'rss',
								'posts_per_page' 	=> 10,
								'paged' 			=> $page
							));

 		$jobs	=	array();
 		foreach ($query->posts as $job) {
 			$jobs[] = array(
 				'ID'		=> $job->ID,
 				'title' 	=> $job->post_title,
 				'url'		=> get_post_meta($job->ID, 'et_rss_url'),
 				'date' 		=> date('d-m-Y', strtotime($job->post_date)),
 				'creator'	=> get_post_meta($job->ID, 'et_rss_creator'),
 				'permalink'	=> get_permalink($job->ID), 
 			);
 		}

 		if ($count > 0)
 			$resp = array(
 				'success' 	=> true,
 				'msg' 		=> $count == 1 ? __('1 job has been deleted', ET_DOMAIN) : sprintf(__('%d jobs have been deleted', ET_DOMAIN), $count),
 				'data' 		=> array(
 					'count' => $count,
 					'page' 	=> $page,
 					'pages_max' => $query->max_num_pages,
 					'jobs' => $jobs
 				)
 			);
 		else 
 			$resp = array(
 				'success' 	=> false,
 				'msg' 		=> __('No job has been deleted', ET_DOMAIN)
 			);

		echo json_encode($resp);
		exit;
	}

	/**
	 * 
	 */
	public function rss_change_page(){
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if(!current_user_can ('manage_options')) {
			return array (
						'success'	=> false,
						'msg'		=> __('Permission Denied', ET_DOMAIN)
					);
		}

		$data	=	$_REQUEST['content'];
 		$query	= 	new WP_Query(array(
								'post_type' 		=> 'job',
								'meta_key' 			=> 'et_template_id',
								'meta_value' 		=> 'rss',
								'posts_per_page' 	=> 10,
								'paged' 			=> $data['page']
							));
 		foreach ($query->posts as $job) {
 			$jobs[] = array(
 				'ID'		=> $job->ID,
 				'title' 	=> $job->post_title,
 				'url' 		=> get_post_meta($job->ID, 'et_rss_url', true),
 				'date' 		=> date('d-m-Y', strtotime($job->post_date)),
 				'permalink'	=> get_permalink($job->ID), 
 				'creator'	=> get_post_meta($job->ID, 'et_rss_creator'),
 			);
 		}
 		if ($query->post_count > 0)
 			$resp = array(
 				'success' 	=> true,
 				'msg' 		=> '',
 				'data' 		=> array(
 					'page' => $data['page'],
 					'pages_max' => $query->max_num_pages,
 					'jobs' => $jobs
 				)
 			);
 		else 
 			$resp = array(
 				'success' 	=> false,
 				'msg' 		=> ''
 			);

		echo json_encode($resp);
		exit;
	}

	function update_rss_import_schedule () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: text/plain' );
		if(!current_user_can ('manage_options')) {
			echo json_encode (array (
					'success'	=> false,
					'msg'		=> array('0' => __('Permission Denied', ET_DOMAIN) )
				)
			);
			exit;
		}

		$option	=	$this->get_schedule_option();
		$data	=	$_REQUEST;
		$rss	=	$this->get_rss_data( $data['rss_link'], true);

		if($rss['success'] ) {
			/**
			 * generate new schedule id if add new
			*/
			if($data['schedule_id'] == '') {
				if(!empty($option))
					$i	=	max(array_keys($option)) + 1 ;
				else $i	=	1;
				$data['schedule_id']	=	$i;
			}

			$option[$data['schedule_id']]	=	$data;

			$this->update_schedule_option($option);

			$resp	=	array('success' => true, 'data' => $data , 'rss' => $rss['data']);

		} else {
			$msg	=	array();
			if(!$rss['success']) {
				$msg[]	=	$rss['msg'][0];
			}
			$resp	=	array('success' => false , 'msg'	=> $msg );
		}
		echo json_encode($resp);
		exit;

	}

}

class JE_RSS_Schedule extends JE_RSS_Import {
	function __construct () {
		
		add_action('wp', array($this,'refesh_schedule_activation') );
		
		add_action('simplyhired_delete_job_event', array($this, 'delete_job_from_date'));
		add_action('rss_import_schedule_event', array($this,'schedule_start') );
		add_action('rss_import_single_schedule_event' , array($this, 'rss_import_single_schedule_event'));


	}

	/**
	 * start the import schedule
	*/
	function schedule_start () {
		$option	=	$this->get_schedule_option();
		update_option ('je_rss_for_run_schedule_list', $option );
		/**
		 * add repeatly event to run each schedule after 10 minutes
		*/
		wp_schedule_event( time() + 10 , 'custom_single_rss_schedule' , 'rss_import_single_schedule_event');
	}
	/**
	 *	check schedule exist or not
	*/
	function refesh_schedule_activation () {

		if(!wp_next_scheduled('rss_delete_job_event')) {
			$this->add_delete_job_event ();
		}

		if(!wp_next_scheduled('rss_import_schedule_event')) {
			$time_stamp	=	date('d M y 00:00:00', time() + 3600*24);
			$time_stamp	=	strtotime( $time_stamp );
			
			wp_clear_scheduled_hook('rss_import_schedule_event');
			wp_clear_scheduled_hook('rss_import_single_schedule_event');

			wp_schedule_event( $time_stamp , 'custom_rss_recurrence', 'rss_import_schedule_event');	
		}

		$schedule_list	=	get_option('je_rss_for_run_schedule_list', array());
		if(empty($schedule_list)) {
			wp_clear_scheduled_hook('rss_import_single_schedule_event');
		}
	}

	/**
	 * schedule function to run eache schedule
	*/
	function rss_import_single_schedule_event () {
		$schedule_list	=	get_option ('je_rss_for_run_schedule_list', array());
		if(!empty($schedule_list)) {
			$schedule	=	array_pop($schedule_list);
			$this->rss_schedule( $schedule );
		}
		update_option( 'je_rss_for_run_schedule_list', $schedule_list );
	}

	/**
	 * import job by schedule setting
	*/
	function rss_schedule ( $schedule ) {
		
		if(isset($schedule['ON']) && $schedule['ON'] == 0  ) return ;
		
		if($schedule['rss_link'] == '') return ;

		$rss	=	$this->get_rss_data($schedule['rss_link']);

		if(!$rss['success']) return;

		$data	=	$rss['data'];
		
		foreach ($data as $key => $v) {
			$job	=	array();
			$job['jobtitle']		=	$v['title'];
			$job['jobdesc']			=	$v['content'];
			$job['url']				=	$v['link'];
			$job['job_category']	=	$schedule['job_category'];
			$job['job_type']		=	$schedule['job_type'];
			$job['creator']			=	$v['creator'];
			$job['date']			=	$v['pubDate'];
			$job['location']		=	isset($v['job_location']) ? $v['job_location']  : '';

			$this->schedule_import_job ( $job, $schedule['import_author']);
		}
		
	}

	/**
	 * add event daily clean up simplyhired job
	*/
	public function add_delete_job_event () {
		$time_stamp	=	date('d M y 00:00:00', time() );
		$time_stamp	=	strtotime( $time_stamp );
		wp_clear_scheduled_hook('rss_delete_job_event');

		$day	=	get_option( 'je_rss_delete_days', '' );
		if($day != '')
			wp_schedule_event( $time_stamp , 'daily', 'rss_delete_job_event');	
	}


}



// -------------------------------------------------
// HTML FIXER v.2.05 15/07/2010
// clean dirty html and make it better, fix open tags
// bad nesting, bad quotes, bad autoclosing tags.
//
// by Giulio Pons
// -------------------------------------------------
// -------------------------------------------------
if(!class_exists('HtmlFixer')) {
Class HtmlFixer {
	public $dirtyhtml;
	public $fixedhtml;
	public $allowed_styles;		// inline styles array of allowed css (if empty means ALL allowed)
	private $matrix;			// array used to store nodes
	public $debug;
	private $fixedhtmlDisplayCode;

	public function __construct() {
		$this->dirtyhtml = "";
		$this->fixedhtml = "";
		$this->debug = false;
		$this->fixedhtmlDisplayCode = "";
		$this->allowed_styles = array();
	}

	public function getFixedHtml($dirtyhtml) {
		$c = 0;
		$this->dirtyhtml = $dirtyhtml;
		$this->fixedhtml = "";
		$this->fixedhtmlDisplayCode = "";
		if (is_array($this->matrix)) unset($this->matrix);
		$errorsFound=0;
		while ($c<10) {
			/*
				iterations, every time it's getting better...
			*/
			if ($c>0) $this->dirtyhtml = $this->fixedxhtml;
			$errorsFound = $this->charByCharJob();
			if (!$errorsFound) $c=10;	// if no corrections made, stops iteration
			$this->fixedxhtml=str_replace('<root>','',$this->fixedxhtml);
			$this->fixedxhtml=str_replace('</root>','',$this->fixedxhtml);
			$this->fixedxhtml = $this->removeSpacesAndBadTags($this->fixedxhtml);
			$c++;
		}
		return $this->fixedxhtml;
	}

	private function fixStrToLower($m){
		/*
			$m is a part of the tag: make the first part of attr=value lowercase
		*/
		$right = strstr($m, '=');
		$left = str_replace($right,'',$m);
		return strtolower($left).$right;
	}

	private function fixQuotes($s){
		$q = "\"";// thanks to emmanuel@evobilis.com
		if (!stristr($s,"=")) return $s;
		$out = $s;
		preg_match_all("|=(.*)|",$s,$o,PREG_PATTERN_ORDER);
		for ($i = 0; $i< count ($o[1]); $i++) {
			$t = trim ( $o[1][$i] ) ;
			$lc="";
			if ($t!="") {
				if ($t[strlen($t)-1]==">") {
					$lc= ($t[strlen($t)-2].$t[strlen($t)-1])=="/>"  ?  "/>"  :  ">" ;
					$t=substr($t,0,-1);
				}
				//missing " or ' at the beginning
				if (($t[0]!="\"")&&($t[0]!="'")) $out = str_replace( $t, "\"".$t,$out); else $q=$t[0];
				//missing " or ' at the end
				if (($t[strlen($t)-1]!="\"")&&($t[strlen($t)-1]!="'")) $out = str_replace( $t.$lc, $t.$q.$lc,$out);
			}
		}
		return $out;
	}

	private function fixTag($t){
		/* remove non standard attributes and call the fix for quoted attributes */
		$t = preg_replace (
			array(
				'/borderColor=([^ >])*/i',
				'/border=([^ >])*/i'
			), 
			array(
				'',
				''
			)
			, $t);
		$ar = explode(" ",$t);
		$nt = "";
		for ($i=0;$i<count($ar);$i++) {
			$ar[$i]=$this->fixStrToLower($ar[$i]);
			if (stristr($ar[$i],"=")) $ar[$i] = $this->fixQuotes($ar[$i]);	// thanks to emmanuel@evobilis.com
			//if (stristr($ar[$i],"=") && !stristr($ar[$i],"=\"")) $ar[$i] = $this->fixQuotes($ar[$i]);
			$nt.=$ar[$i]." ";
		}
		$nt=preg_replace("/<( )*/i","<",$nt);
		$nt=preg_replace("/( )*>/i",">",$nt);
		return trim($nt);
	}

	private function extractChars($tag1,$tag2,$tutto) { /*extract a block between $tag1 and $tag2*/
		if (!stristr($tutto, $tag1)) return '';
		$s=stristr($tutto,$tag1);
		$s=substr( $s,strlen($tag1));
		if (!stristr($s,$tag2)) return '';
		$s1=stristr($s,$tag2);
		return substr($s,0,strlen($s)-strlen($s1));
	}

	private function mergeStyleAttributes($s) {
		//
		// merge many style definitions in the same tag in just one attribute style
		//

		$x = "";
		$temp = "";
		$c = 0;
		while(stristr($s,"style=\"")) {
			$temp = $this->extractChars("style=\"","\"",$s);
			if ($temp=="") {
				// missing closing quote! add missing quote.
				return preg_replace("/(\/)?>/i","\"\\1>",$s);
			}
			if ($c==0) $s = str_replace("style=\"".$temp."\"","##PUTITHERE##",$s);
				$s = str_replace("style=\"".$temp."\"","",$s);
			if (!preg_match("/;$/i",$temp)) $temp.=";";
			$x.=$temp;
			$c++;
		}

		if (count($this->allowed_styles)>0) {
			// keep only allowed styles by Martin Vool 2010-04-19
			$check=explode(';', $x);
			$x="";
			foreach($check as $chk){
				foreach($this->allowed_styles as $as)
					if(stripos($chk, $as) !== False) { $x.=$chk.';'; break; } 
			}
		}

		if ($c>0) $s = str_replace("##PUTITHERE##","style=\"".$x."\"",$s);
		return $s;


	}

	private function fixAutoclosingTags($tag,$tipo=""){
		/*
			metodo richiamato da fix() per aggiustare i tag auto chiudenti (<br/> <img ... />)
		*/
		if (in_array( $tipo, array ("img","input","br","hr")) ) {
			if (!stristr($tag,'/>')) $tag = str_replace('>','/>',$tag );
		}
		return $tag;
	}

	private function getTypeOfTag($tag) {
		$tag = trim(preg_replace("/[\>\<\/]/i","",$tag));
		$a = explode(" ",$tag);
		return $a[0];
	}


	private function checkTree() {
		// return the number of errors found
		$errorsCounter = 0;
		for ($i=1;$i<count($this->matrix);$i++) {
			$flag=false;
			if ($this->matrix[$i]["tagType"]=="div") { //div cannot stay inside a p, b, etc.
				$parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"];
				if (in_array($parentType, array("p","b","i","font","u","small","strong","em"))) $flag=true;
			}

			if (in_array( $this->matrix[$i]["tagType"], array( "b", "strong" )) ) { //b cannot stay inside b o strong.
				$parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"];
				if (in_array($parentType, array("b","strong"))) $flag=true;
			}

			if (in_array( $this->matrix[$i]["tagType"], array ( "i", "em") )) { //i cannot stay inside i or em
				$parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"];
				if (in_array($parentType, array("i","em"))) $flag=true;
			}

			if ($this->matrix[$i]["tagType"]=="p") {
				$parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"];
				if (in_array($parentType, array("p","b","i","font","u","small","strong","em"))) $flag=true;
			}

			if ($this->matrix[$i]["tagType"]=="table") {
				$parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"];
				if (in_array($parentType, array("p","b","i","font","u","small","strong","em","tr","table"))) $flag=true;
			}
			if ($flag) {
				$errorsCounter++;
				if ($this->debug) echo "<div style='color:#ff0000'>Found a <b>".$this->matrix[$i]["tagType"]."</b> tag inside a <b>".htmlspecialchars($parentType)."</b> tag at node $i: MOVED</div>";
				
				$swap = $this->matrix[$this->matrix[$i]["parentTag"]]["parentTag"];
				if ($this->debug) echo "<div style='color:#ff0000'>Every node that has parent ".$this->matrix[$i]["parentTag"]." will have parent ".$swap."</div>";
				$this->matrix[$this->matrix[$i]["parentTag"]]["tag"]="<!-- T A G \"".$this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]."\" R E M O V E D -->";
				$this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]="";
				$hoSpostato=0;
				for ($j=count($this->matrix)-1;$j>=$i;$j--) {
					if ($this->matrix[$j]["parentTag"]==$this->matrix[$i]["parentTag"]) {
						$this->matrix[$j]["parentTag"] = $swap;
						$hoSpostato=1;
					}
				}
			}

		}
		return $errorsCounter;

	}

	private function findSonsOf($parentTag) {
		// build correct html recursively
		$out= "";
		for ($i=1;$i<count($this->matrix);$i++) {
			if ($this->matrix[$i]["parentTag"]==$parentTag) {
				if ($this->matrix[$i]["tag"]!="") {
					$out.=$this->matrix[$i]["pre"];
					$out.=$this->matrix[$i]["tag"];
					$out.=$this->matrix[$i]["post"];
				} else {
					$out.=$this->matrix[$i]["pre"];
					$out.=$this->matrix[$i]["post"];
				}
				if ($this->matrix[$i]["tag"]!="") {
					$out.=$this->findSonsOf($i);
					if ($this->matrix[$i]["tagType"]!="") {
						//write the closing tag
						if (!in_array($this->matrix[$i]["tagType"], array ( "br","img","hr","input"))) 
							$out.="</". $this->matrix[$i]["tagType"].">";
					}
				}
			}
		}
		return $out;
	}

	private function findSonsOfDisplayCode($parentTag) {
		//used for debug
		$out= "";
		for ($i=1;$i<count($this->matrix);$i++) {
			if ($this->matrix[$i]["parentTag"]==$parentTag) {
				$out.= "<div style=\"padding-left:15\"><span style='float:left;background-color:#FFFF99;color:#000;'>{$i}:</span>";
				if ($this->matrix[$i]["tag"]!="") {
					if ($this->matrix[$i]["pre"]!="") $out.=htmlspecialchars($this->matrix[$i]["pre"])."<br>";
					$out.="".htmlspecialchars($this->matrix[$i]["tag"])."<span style='background-color:red; color:white'>{$i} <em>".$this->matrix[$i]["tagType"]."</em></span>";
					$out.=htmlspecialchars($this->matrix[$i]["post"]);
				} else {
					if ($this->matrix[$i]["pre"]!="") $out.=htmlspecialchars($this->matrix[$i]["pre"])."<br>";
					$out.=htmlspecialchars($this->matrix[$i]["post"]);
				}
				if ($this->matrix[$i]["tag"]!="") {
					$out.="<div>".$this->findSonsOfDisplayCode($i)."</div>\n";
					if ($this->matrix[$i]["tagType"]!="") {
						if (($this->matrix[$i]["tagType"]!="br") && ($this->matrix[$i]["tagType"]!="img") && ($this->matrix[$i]["tagType"]!="hr")&& ($this->matrix[$i]["tagType"]!="input"))
							$out.="<div style='color:red'>".htmlspecialchars("</". $this->matrix[$i]["tagType"].">")."{$i} <em>".$this->matrix[$i]["tagType"]."</em></div>";
					}
				}
				$out.="</div>\n";
			}
		}
		return $out;
	}

	private function removeSpacesAndBadTags($s) {
		$i=0;
		while ($i<10) {
			$i++;
			$s = preg_replace (
				array(
					'/[\r\n]/i',
					'/  /i',
					'/<p([^>])*>(&nbsp;)*\s*<\/p>/i',
					'/<span([^>])*>(&nbsp;)*\s*<\/span>/i',
					'/<strong([^>])*>(&nbsp;)*\s*<\/strong>/i',
					'/<em([^>])*>(&nbsp;)*\s*<\/em>/i',
					'/<font([^>])*>(&nbsp;)*\s*<\/font>/i',
					'/<small([^>])*>(&nbsp;)*\s*<\/small>/i',
					'/<\?xml:namespace([^>])*><\/\?xml:namespace>/i',
					'/<\?xml:namespace([^>])*\/>/i',
					'/class=\"MsoNormal\"/i',
					'/<o:p><\/o:p>/i',
					'/<!DOCTYPE([^>])*>/i',
					'/<!--(.|\s)*?-->/',
					'/<\?(.|\s)*?\?>/'
				), 
				array(
					' ',
					' ',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					' ',
					'',
					''
				)
				, trim($s));
		}
		return $s;
	}

	private function charByCharJob() {
		$s = $this->removeSpacesAndBadTags($this->dirtyhtml);
 		if ($s=="") return;
		$s = "<root>".$s."</root>";
		$contenuto = "";
		$ns = "";
		$i=0;
		$j=0;
		$indexparentTag=0;
		$padri=array();
		array_push($padri,"0");
		$this->matrix[$j]["tagType"]="";
		$this->matrix[$j]["tag"]="";
		$this->matrix[$j]["parentTag"]="0";
		$this->matrix[$j]["pre"]="";
		$this->matrix[$j]["post"]="";
		$tags=array();
		while($i<strlen($s)) {
			if ( $s[$i] =="<") {
				/*
					found a tag
				*/
				$contenuto = $ns;
				$ns = "";
				
				$tag="";
				while( $i<strlen($s) && $s[$i]!=">" ){
					// get chars till the end of a tag
					$tag.=$s[$i];
					$i++;
				}
				$tag.=$s[$i];
				
				if($s[$i]==">") {
					/*
						$tag contains a tag <...chars...>
						let's clean it!
					*/
					$tag = $this->fixTag($tag);
					$tagType = $this->getTypeOfTag($tag);
					$tag = $this->fixAutoclosingTags($tag,$tagType);
					$tag = $this->mergeStyleAttributes($tag);

					if (!isset($tags[$tagType])) $tags[$tagType]=0;
					$tagok=true;
					if (($tags[$tagType]==0)&&(stristr($tag,'/'.$tagType.'>'))) {
						$tagok=false;
						/* there is a close tag without any open tag, I delete it */
						if ($this->debug) echo "<div style='color:#ff0000'>Found a closing tag <b>".htmlspecialchars($tag)."</b> at char $i without open tag: REMOVED</div>";
					}
				}
				if ($tagok) {
					$j++;
					$this->matrix[$j]["pre"]="";
					$this->matrix[$j]["post"]="";
					$this->matrix[$j]["parentTag"]="";
					$this->matrix[$j]["tag"]="";
					$this->matrix[$j]["tagType"]="";
					if (stristr($tag,'/'.$tagType.'>')) {
						/*
							it's the closing tag
						*/
						$ind = array_pop($padri);
						$this->matrix[$j]["post"]=$contenuto;
						$this->matrix[$j]["parentTag"]=$ind;
						$tags[$tagType]--;
					} else {
						if (@preg_match("/".$tagType."\/>$/i",$tag)||preg_match("/\/>/i",$tag)) {
							/*
								it's a autoclosing tag
							*/
							$this->matrix[$j]["tagType"]=$tagType;
							$this->matrix[$j]["tag"]=$tag;
							$indexparentTag = array_pop($padri);
							array_push($padri,$indexparentTag);
							$this->matrix[$j]["parentTag"]=$indexparentTag;
							$this->matrix[$j]["pre"]=$contenuto;
							$this->matrix[$j]["post"]="";
						} else {
							/*
								it's a open tag
							*/
							$tags[$tagType]++;
							$this->matrix[$j]["tagType"]=$tagType;
							$this->matrix[$j]["tag"]=$tag;
							$indexparentTag = array_pop($padri);
							array_push($padri,$indexparentTag);
							array_push($padri,$j);
							$this->matrix[$j]["parentTag"]=$indexparentTag;
							$this->matrix[$j]["pre"]=$contenuto;
							$this->matrix[$j]["post"]="";
						}
					}
				}
			} else {
				/*
					content of the tag
				*/
				$ns.=$s[$i];
			}
			$i++;
		}
		/*
			remove not valid tags
		*/
		for ($eli=$j+1;$eli<count($this->matrix);$eli++) {
			$this->matrix[$eli]["pre"]="";
			$this->matrix[$eli]["post"]="";
			$this->matrix[$eli]["parentTag"]="";
			$this->matrix[$eli]["tag"]="";
			$this->matrix[$eli]["tagType"]="";
		}
		$errorsCounter = $this->checkTree();		// errorsCounter contains the number of removed tags
		$this->fixedxhtml=$this->findSonsOf(0);	// build html fixed
		if ($this->debug) {
			$this->fixedxhtmlDisplayCode=$this->findSonsOfDisplayCode(0);
			echo "<table border=1 cellspacing=0 cellpadding=0>";
			echo "<tr><th>node id</th>";
			echo "<th>pre</th>";
			echo "<th>tag</th>";
			echo "<th>post</th>";
			echo "<th>parentTag</th>";
			echo "<th>tipo</th></tr>";
			for ($k=0;$k<=$j;$k++) {
				echo "<tr><td>$k</td>";
				echo "<td>&nbsp;".htmlspecialchars($this->matrix[$k]["pre"])."</td>";
				echo "<td>&nbsp;".htmlspecialchars($this->matrix[$k]["tag"])."</td>";
				echo "<td>&nbsp;".htmlspecialchars($this->matrix[$k]["post"])."</td>";
				echo "<td>&nbsp;".$this->matrix[$k]["parentTag"]."</td>";
				echo "<td>&nbsp;<i>".$this->matrix[$k]["tagType"]."</i></td></tr>";
			}
			echo "</table>";
			echo "<hr/>{$j}<hr/>\n\n\n\n".$this->fixedxhtmlDisplayCode;
		}
		return $errorsCounter;
	}
}
}