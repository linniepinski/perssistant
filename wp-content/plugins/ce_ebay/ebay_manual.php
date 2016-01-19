
<div id="ebay-manual" class="ebay-box hide">

<?php 
	$industry= array();//$this->et_linkedin_industry();
	$search_str	=	array();//$this->get_search_string();
	if(isset($search_str['job-type']) == '')  $search_str['job-type']='';
	if(isset($search_str['industry']) == '')  $search_str['industry']='';
	if(isset($search_str['company-name']) == '')  $search_str['company-name']='';
	$industry_l =str_replace('industry,', '', $search_str['industry']);
	?>
	<form id="ebay-search" class="ebay-search">
		<div class="desc">
			<div class="title font-quicksand"><?php _e('Import eBay Products Manually') ?></div>
			<p><?php _e('Search ebay.com for products you want to import into your website',ET_DOMAIN);?></p>
			<div class="import-search-form">
				<div class="form-item no-margin no-padding no-background">
					<?php 	ce_ebay_dropdow_site();	?>
				</div>
				<div class="form-item no-margin no-padding no-background" id="select_site">
					<?php ce_ebay_dropdow_categories();?>
				</div>
				<div class="form no-margin no-padding no-background">
				   	<div class="form-item">
						<input type="text" name="keywords" id="keywords" value="" placeholder="<?php _e('Keywords',ET_DOMAIN);?>" class="bg-grey-input">
					</div>
				</div>
				<div class="form no-margin no-padding no-background">
				   	<div class="form-item">
						<input type="text" name="user_id" id="user_id" value="" placeholder="<?php _e('Seller ID',ET_DOMAIN);?>" class="bg-grey-input">
						<input type="hidden" name="action" value="ebay-search-ad" />
					</div>
				</div>
			</div>
		</div>

		<div class="clear"></div>
		<div class="f-left-all">
			<button class="engine-btn btn-button engine-btn-icon" id="ebay_search">
				<?php _e('Search', ET_DOMAIN)?>
			</button>
			<span class="span-wr red"></span>
		</div>
	</form>
	<div class="clear"></div>

	<div id="search-results" class="hide search-results">
		<form id="ebay-import" class="ebay-import">
			<div class="import-adjustment">
				<div class="padding-top10 import-results-heading font-quicksand">
					<?php _e('With Selected Items',ET_DOMAIN);?>:
				</div>
				<div class="import-results-adjust">
					<?php _e('Categories',ET_DOMAIN);?> :
					<div class="select-style et-button-select">
					<?php 
						 wp_dropdown_categories(array('id'=>CE_AD_CAT ,'hide_empty'=>false,'exclude_tree'=>true, 'hierarchical'=>3, 'taxonomy'=>CE_AD_CAT,'name'=>'cat_all'));
					?>
					</div><br>
					<?php _e('Locations',ET_DOMAIN);?> :
					<div class="select-style et-button-select">
						<?php wp_dropdown_categories(array('echo'=>1,'id'=>'ad_location','hide_empty'=>false,'exclude_tree'=>true, 'hierarchical'=>3, 'taxonomy'=>'ad_location','name'=>'location_all')); ?>
					</div>
				</div>
			</div>

			<div class="import-tb-container">
				<table>
					<thead>
						<tr class="heading">
							<th align="center"><input class="select_all" type="checkbox" name="" id="select_all" ></th>
							<th width="12%"><?php _e('Thumbnail',ET_DOMAIN);?></th>
							<th width="39%"><?php _e('Title ',ET_DOMAIN);?></th>
							<th width="20%"><?php _e('Category',ET_DOMAIN);?></th>
							<th width="20%"><?php _e('Location',ET_DOMAIN);?></th>

						</tr>
					</thead>
					<tbody>
					</tbody>
					<thead>
						<tr class="head-footer">
							<th align="center"><input class="select_all" type="checkbox" name="" id="select_all"></th>
							<th width="12%"><?php _e('Thumbnail',ET_DOMAIN);?></th>
							<th width="39%"><?php _e('Title ',ET_DOMAIN);?></th>
							<th width="20%"><?php _e('Category',ET_DOMAIN);?></th>
							<th width="20%"><?php _e('Location',ET_DOMAIN);?></th>
						</tr>
					</thead>
				</table>
			</div>
			<div class="row pager-wrap"></div>
			<div class="import-adjustment">
				<div class="padding-top10 import-results-heading font-quicksand">
					<?php _e("Apply for all Ads",ET_DOMAIN);?>:
				</div>
				<div class="import-results-adjust">
					<?php $users = get_users(array(
						'role' => 'administrator'
					));
					$user_data = array();
					foreach ($users as $user) {
						$user_data[] = array( 'id' => $user->ID, 'label' => $user->user_nicename );
					}
					?>
					<input type="hidden" name="import_author" id="import_author" value='1' />
					<input class="backend-input" placeholder="<?php _e('Author Username', ET_DOMAIN) ?>" type="text" value="" id="search_author" name="author" />
					<script type="text/data" id="user_source"><?php echo json_encode($user_data);?></script>
				</div>
			</div>
				<input type="hidden" name="action" value="ebay-save-imported-ads" />
			<div class="clear"></div>
			<button type="submit" style="margin-top: 20px" class="et-button btn-button" id="save_import"><?php _e('Import all selected items') ?></button>
			<div class="clear"></div>
		</form>

	</div>

</div>