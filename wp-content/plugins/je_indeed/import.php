<?php
	$support_co	=	$this->indeed->indeed_support_country();
	$settings	=	$this->indeed->get_settings();
	$display_label	=	1;

	extract($settings);
	$author	=	plugin_dir_url( __FILE__).'/default_logo.jpg';
?>
<div id="indeed-import" class="et-main-main inner-content import-container clearfix" <?php if ( isset($sub_section) && ($sub_section != '' && $sub_section != 'import')) echo 'style="display:none"' ?> >
	<form id="indeed_job">
		<div class="title font-quicksand"><?php _e('Publisher ID', ET_DOMAIN) ?></div>
		<div class="desc">
			<?php _e( 'You will be received this ID after register as an Indeed Publisher.' , ET_DOMAIN) ?>
			<div class="import-search-form">
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Publisher ID', ET_DOMAIN) ?>" type="text" value="<?php echo $publisher ?>" name="publisher" />
					</div>
				</div>
			</div>

		</div>

		<div class="title font-quicksand"><?php _e('Default query string', ET_DOMAIN) ?></div>
		<div class="desc">
			<?php _e( 'You can set up a default search criteria for users. If users choose Job category or enter a keyword when searching for a job, these values will be overwritten.' , ET_DOMAIN) ?>

			<div class="import-search-form">
				<div class="form no-margin no-padding no-background">


					<!-- <div class="select-style et-button-select"> -->
						<select name="co" class="" title="<?php _e("Set default country", ET_DOMAIN); ?>">
							<option class="empty" value=""><?php _e("Set default country", ET_DOMAIN); ?> </option>
							<?php foreach ($support_co as $key => $value) { ?>
								<option value="<?php echo $key ?>" <?php if($key == $co) echo 'selected="selected"' ; ?>>
									<?php echo $value ?>
								</option>
							<?php } ?>
						</select>
					<!-- </div> -->
				</div>

				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Location', ET_DOMAIN) ?>" type="text" value="<?php echo $l ?>" name="l" />
					</div>
				</div>

				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Query', ET_DOMAIN) ?>" type="text" value="<?php echo $q ?>" id="" name="q" />
					</div>
				</div>

				<div class="form no-margin no-padding no-background">
					<?php _e("Number of days back to search.", ET_DOMAIN); ?>
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Within (days)', ET_DOMAIN) ?>" type="text" value="<?php echo $fromage ?>" name="fromage" />
					</div>
				</div>

				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input id="display_label" class="option" type="checkbox" name="display_label" <?php checked(  1, $display_label , true ); ?> value="1" class="checkbox check-display">
						<lable for="display_label"> <?php _e('Display label "Job by indeed"', ET_DOMAIN); ?></lable>
					</div>
				</div>

			</div>
		</div>

		<div class="title font-quicksand"><?php _e('Logo', ET_DOMAIN) ?></div>
		<div class="desc">
			<?php _e( 'You can set up logo for the job list' , ET_DOMAIN) ?>
			<div class="import-search-form">
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Logo URL', ET_DOMAIN) ?>" type="text" value="<?php echo $author ?>" name="author" />
					</div>
				</div>
			</div>
		</div>

		<div class="title font-quicksand"><?php _e('Mapping job type', ET_DOMAIN) ?></div>
		<div class="desc">
			<?php _e( ' Indeed search job API only allows these values: “fulltime”, “part time”, “contract”, “internship”, “temporary”. You should map these with your site’s diversified job type.' , ET_DOMAIN) ?>
			<div class="import-search-form">
				<?php 
					$indeed_contract_type	=	$this->indeed->get_job_types();
					$jobtype				=	get_terms( 'job_type' , array('hide_empty' => false ) );
				?>
				<table class="form-table">
					<tbody>
						<tr style="border-bottom:1px dotted;">
							<th><?php _e("Indeed's Job Type", ET_DOMAIN); ?></th>
							<th><?php _e("Map with: Your site's Job Type", ET_DOMAIN); ?></th>
						</tr>
						<?php foreach ($indeed_contract_type as $key => $value) { ?>
							<tr>
								<th scope="row"><label for="<?php echo $key; ?>"><?php echo $value;   ?></label></th>
								<td>
									<select name="<?php echo $key; ?>" id="<?php echo $key; ?>">
										<?php
										foreach ($jobtype as $k => $value) { ?>
											<option <?php selected( $$key, $value->slug, true ); ?> value="<?php echo $value->slug; ?>"><?php echo $value->name; ?></option> 
										<?php } ?>
									</select>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>

			</div>

		</div>


	</form>
	<div class="clear"></div>
	<div class="f-left-all">
		<button class="engine-btn btn-button engine-btn-icon" id="save_settings">
			<?php _e('Save', ET_DOMAIN)?>
		</button>
	</div>

</div>