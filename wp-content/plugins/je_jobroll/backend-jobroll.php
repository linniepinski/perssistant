<div class="main-jobroll" id="jobroll_info">
	<div class="main-center clearfix">
		<form id="jobroll_form">
		<div class="job-left f-left">

			<div class="main-title"><?php _e("SET JOBROLL PAGE",ET_DOMAIN);?></div>
			<div class="desc">
				<label><?php _e("Select a page for your jobroll tempalte", ET_DOMAIN); ?></label>
				<div class="form no-background" >
				 	<div class="backend  input-job select-style btn-background border-radius et-button-select">
						<select  id="page_jobroll" class="save-page" style="z-index: 10; opacity: 0;" title="<?php _e("Select a page", ET_DOMAIN); ?>">
							<option value=""><?php _e("Select a page", ET_DOMAIN); ?></option>
							<?php
							$id 	= self::get_page_jobroll();
							$pages 	= get_pages();

							if ( !empty($pages) ){
								foreach ($pages as $page) {
									$class = ($id == $page->ID) ? 'selected ="selected"' : '';
									$template = get_post_meta( $page->ID, '_wp_page_template', true );

									if(empty($template) || $template == 'default')
										echo '<option data-url="'. add_query_arg( array('jobroll_request'=>1),get_permalink($page->ID) ).'" '.$class.' value="' . $page->ID . '">' .$page->post_title.'</option>';
								}
							}
							?>
						</select>
					</div>
				</div>
			</div>

			<div class="main-title"><?php _e("CONTENT",ET_DOMAIN);?></div>
			<div class="item-jobroll clearfix">
				<div class="item-job">
					<label><?php _e("Job Category", ET_DOMAIN); ?></label>
					<div class="backend  input-job select-style btn-background border-radius et-button-select">
						<select  title="<?php _e("All categories", ET_DOMAIN);?>" class="auto-save" name="categories" id="categories" style="z-index: 10; opacity: 0;">
							<option value="" ><?php _e("All categories", ET_DOMAIN);?></option>
							<?php
							et_job_categories_option_list ( $parent =	0 );
							?>
						</select>
					</div>
				</div>
				<div class="item-job">
					<label><?php _e("Job Type", ET_DOMAIN); ?></label>
					<div class="backend  input-job select-style btn-background border-radius et-button-select">
						<select name="job_types" class="auto-save" id="job_types"  style="z-index: 10; opacity: 0;" title="<?php _e("All job types", ET_DOMAIN); ?>">
							<option value=""><?php _e("All job types", ET_DOMAIN); ?></option>
							<?php $types = et_get_job_types();
							if ( !empty($types) ){
								foreach ($types as $type) {
									echo '<option value="' . $type->slug . '">' . $type->name . '</option>';
								}
							}
							?>
						</select>
					</div>
				</div>
			</div>

			<!-- display -->
			<div class="main-title"><?php _e("DISPLAY",ET_DOMAIN);?></div>
			<div class="item-jobroll clearfix">
				<div class="item-job">
					<label><?php _e("Number of jobs", ET_DOMAIN); ?></label>
					<div class="input-job">
						<input type="text" class="bg-default-input"  id="number" name="number" value="5" />
					</div>
				</div>
				<div class="item-job">
					<label><?php _e("Width", ET_DOMAIN); ?></label>
					<div class="input-job">
						<input type="text" class="bg-default-input" class="width" id="width" name="width" value="250"/>
					</div>
				</div>
			</div>
			<div class="item-jobroll clearfix">
				<div class="item-job">
					<label><?php _e("Background Color", ET_DOMAIN); ?></label>
					<div class="input-job">
						<input type="text" class="bg-default-input"  id="color" name="color" value="d9d9d9" />
					</div>
				</div>
				<div class="item-job">
					<label><?php _e("Title", ET_DOMAIN); ?></label>
					<div class="input-job">
						<input type="text" class="bg-default-input"  id="title" name="title" value="<?php printf(__("Jobs from %s",ET_DOMAIN), get_option('blogname') ); ?>"/>
					</div>
				</div>
			</div>
				<?php
				$id = self::get_page_jobroll();

				?>
			<!-- view code -->
			<div class="main-title"><?php _e("CODE",ET_DOMAIN);?></div>
			<div class="code-view preview" id="preview">
				<label><?php _e("Copy this code and paste into your website", ET_DOMAIN); ?></label>
				<textarea class="code-content bg-grey-widget"><?php echo htmlentities('<iframe id="je_jobroll" style="border:0; overflow:hidden;" src="'.get_page_link($id).'?jobroll_request=1&number=5" frameborder="0" allowtransparency="true" marginheight="0" marginwidth="0"></iframe>') ?></textarea>
			</div>

		</div>
		</form>

		<!-- right content -->
		<div class="quickview-right">
			<div class="main-title"><?php _e("QUICK VIEW",ET_DOMAIN);?></div>
			<?php
			$url = get_permalink($id);
			$url = add_query_arg(array('jobroll_request' => 1,'number' =>5,'width'=>250,'color'=>'d9d9d9'),$url );
			?>
			<iframe id="frame_preview" style="border:0; overflow:hidden;" src="<?php echo $url;?>" frameborder="0" height="400px" allowtransparency="true" marginheight="0" marginwidth="0"></iframe>
		</div>
	</div>
	Note: You can also allow your users to create a jobroll in the frontend by providing this link to them: <a id="url_front" href='<?php echo get_page_link($id) ?>' target="_blank" >Jobroll</a>
</div>