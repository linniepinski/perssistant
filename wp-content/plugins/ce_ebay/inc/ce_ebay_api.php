<?php
class CE_Ebay_API{
	const 	URL_API_SHOPPING 	= 'http://open.api.ebay.com/shopping';  // Shopping
	const  	URL_API_SEARCH 		= 'http://svcs.ebay.com/services/search/FindingService/v1?OPERATION-NAME=findItemsAdvanced&SERVICE-VERSION=1.0.0';  // Finding	
	const  	TYPE_RESPONSE 		= 'XML';   // Format of the response
	const  	API_VERSION  		= '759';//667';   // Shopping API version number
	const  	API_FINDING_VERSION = '1.7.0';   // Finding API version number	
	static $instance = null;
	
	public static function get_instance() {
		if(self::$instance == null) {
			self::$instance = new CE_Ebay_API();
		}
		return self::$instance;
	}
	/*
		get categories follow site input from ebay domain
	*/
	public static function get_categories($site = 0){
		$instance = CE_Ebay_API::get_instance();
		$options  = CE_Ebay::get_option();

		$apicall 	= $instance::URL_API_SHOPPING.'?callname=GetCategoryInfo';
		$apicall 	.= '&appid='.$options['app_id'];
		$apicall 	.= '&siteid='.$site;
		$apicall 	.= '&CategoryID=-1&version=729&IncludeSelector=ChildCategories';
		$resp 		= simplexml_load_file($apicall);
		$categories = array();
		if($resp->Ack == 'Success'){
			foreach($resp->CategoryArray[0] as $key=>$cat){
				 $catid = (float) $cat->CategoryID;
				$categories[$catid] = array($cat);
			}
		}
		return $categories;
	}

	/*
		Search follow args input,
		return result
	*/
	public static function ebay_search($request){
		$options  = CE_Ebay::get_option();
		extract($request);
		$paged 	= isset($paged) ? $paged : 1;
		$number = isset($number) ? $number : 10;

		$url 	= self::URL_API_SEARCH;
		$url 	.='&GLOBAL-ID='.$site;
		$url 	.= '&SECURITY-APPNAME='.$options['app_id'];
		$url 	.= '&RESPONSE-DATA-FORMAT=XML&outputSelector[0]=SellerInfo&outputSelector[1]=PictureURLLarge';
		$url 	.= "&paginationInput.entriesPerPage=".$number;
		$url 	.= "&paginationInput.pageNumber=".$paged;

		if($options['use_affiliate']){
			$url .= '&affiliate.networkId='.$options['network_id'];
			$url .= '&affiliate.trackingId='.$options['tracking_id'];
			$url .= '&affiliate.customId='.$options['custom_id'];
		}
		if(!empty($user_id)){
			$url .= "&itemFilter(0).name=Seller";
       		$url .= "&itemFilter(0).value=$user_id";
		}
		if(!empty($keywords))
			$url .= '&keywords='.$keywords;
		if($category != '-1')
			$url .= '&categoryId='.$category;

		$items = array();
		$resp 		= simplexml_load_file($url);
		if($resp->ack == 'Success'){
			foreach($resp->searchResult->item as $item) {

				$item->currencyId 		= (string)$item->sellingStatus->currentPrice['currencyId'];
				$item->currencyConvert 	= (string)$item->sellingStatus->convertedCurrentPrice['currencyId'];
				$endTime 		 		= (string)$item->listingInfo->endTime;
				$time 					= strtotime($endTime);
				$item->end_time 		= date('Y-m-d H:i:s',$time);
				$item->title 			= str_replace('"',"''",$item->title);
				$item->time_left 		= getPrettyTimeFromEbayTime($item->sellingStatus->timeLeft);
				$items[] 				= (array)$item;
			}
			$resp = array('success'=>true,'msg'=>__('Search success'),'data' =>  $items,'paginationOutput' => $resp->paginationOutput);
		} else {
			$resp = array('success'=>false,'msg'=>__('Search ebay false'));
		}
		return $resp;
	}
	/*
		// search follow schedule setting and import results.

	*/
	public static function ebay_search_import($option){
		$options  = CE_Ebay::get_option();
		extract($option);
		$paged 	= isset($paged) ? $paged : 1;
		if(!is_numeric($number) || $number < 1)
			$number = 3;

		$url = self::URL_API_SEARCH;
		$url .='&GLOBAL-ID='.$site;
		$url .= '&SECURITY-APPNAME='.$options['app_id'];
		//$url .= '&RESPONSE-DATA-FORMAT=XML&outputSelector[0]=SellerInfo&outputSelector[1]=PictureURLLarge';
		$url .= '&RESPONSE-DATA-FORMAT=XML&outputSelector[0]=PictureURLLarge';
		$url .= "&paginationInput.entriesPerPage=".$number;
		$url .= "&paginationInput.pageNumber=".$paged;

		if($options['use_affiliate']){
			$url .= '&affiliate.networkId='.$options['network_id'];
			$url .= '&affiliate.trackingId='.$options['tracking_id'];
			$url .= '&affiliate.customId='.$options['custom_id'];
		}
		if(!empty($user_id)){
			$url .= "&itemFilter(0).name=Seller";
       		$url .= "&itemFilter(0).value=$user_id";
		}
		if(!empty($keywords))
		$url .= '&keywords='.$keywords;
		if($category != '-1'){
			$url .= '&categoryId='.$category;
		}

		$items 		= array();
		$resp 		= simplexml_load_file($url);
		if($resp->ack == 'Success'){
			foreach($resp->searchResult->item as $item) {
				$item->currencyId = $item->sellingStatus->convertedCurrentPrice['currencyId'];
				$instance = CE_Ebay_API::get_instance();
				$instance->_import($item);
			}
		}
	}
	/*
		import an item.
	*/
	public   function _import($item){

		$item_id 				= (string)$item->itemId;
		$post_title 			= (string)$item->title;
		$viewItemURL 			= (string)$item->viewItemURL;
		$currentPrice 			= (float)$item->sellingStatus->currentPrice;
		$convertedCurrentPrice	= (float)$item->sellingStatus->convertedCurrentPrice;
		$end_time 				= (string)$item->listingInfo->endTime;
		$time_left  			= (string)$item->sellingStatus->timeLeft;
		$pictureURLLarge 		= (string)$item->pictureURLLarge;
		$currency 				= (string)$item->sellingStatus->convertedCurrentPrice['currencyId'];
		$location 				= (string)$item->location;

		global $wpdb;
		$time 				=  strtotime($end_time);
		$et_expired_date 	=  date('Y-m-d H:i:s',$time);
		$record 			= $wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s",'ce_ebay_item_id', $item_id));

		if( !$record ){

			$post = array(
				'post_title' 	=> $post_title,
				'guid' 			=> $viewItemURL,
				'post_status' 	=> 'publish',
				'post_content' 	=>'',
				'post_type' 	=> CE_AD_POSTTYPE
			);

			$post_id 	= wp_insert_post($post,true);
			if(!is_wp_error($post_id) ) {
				if(isset($term_loc_id))
					wp_set_post_terms( $post_id, array($term_loc_id), 'ad_location' );
				if(isset($category))
					wp_set_post_terms( $post_id, array($category), CE_AD_CAT );

				// 	et_expired_date 	2014-01-12 04:20:29
				update_post_meta($post_id, 'ce_ebay_url', $viewItemURL );
				update_post_meta($post_id, 'ce_ebay_item_id', $item_id );
				update_post_meta($post_id, 'ce_out_thumb', $pictureURLLarge );
				update_post_meta($post_id, CE_ET_PRICE, $currentPrice );
				update_post_meta($post_id, 'et_featured', 0 );
				update_post_meta($post_id, 'et_expired_date', $et_expired_date );
				
				/**
				 * add template id to control ebay ad
				*/
				update_post_meta( $post_id, 'template_id', 'ebay' );

			}
		}
	}
}
?>