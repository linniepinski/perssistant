<div class="et-main-main" id="setting-mails" style="display: none">
	<div class="title font-quicksand"><?php _e('Mail template', ET_DOMAIN) ?></div>
	<div class="desc">
		<?php _e('Email templates for alert jobs. You can use placeholders to include some specific content.',ET_DOMAIN); ?> 
		<a class="icon btn-template-help" data-icon="?" href="#" title="View more details"></a>
		<div class="mail-control-btn">
			<div>
				(*)[list_jobs] : <?php _e("List of jobs", ET_DOMAIN); ?><br/>
				(*)[blogname]: <?php _e("Your page name", ET_DOMAIN) ?><br/>
				(*)[admin_email]: <?php _e("Admin email", ET_DOMAIN) ?> <br/>
				(*)[site_url]: <?php _e("Link site", ET_DOMAIN) ?><br/>
				(*)[site_logo]: <?php _e("Logo image", ET_DOMAIN) ?><br/>
				(*)[blogdescription]: <?php _e("Blog description", ET_DOMAIN) ?><br/>
				(*)[unsubscribe_link]: <?php _e("Unsubcriber link", ET_DOMAIN) ?>
			</div>
			
		</div>

		<?php
			$mail_alert_message =get_option('et_mail_alert_message', $this->get_alert_message_template());
		?>
		<div class="inner email-template">

			<div class="item">
				<div class="payment">
					<?php _e("Mail Alert Message",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting" style="dispaly:block;">
					<div class="form-item clearfix">
						<div class="mail-template">
							<div class="mail-input" data-name="et_mail_alert_message">
							<?php
 								$custom_setting=je_editor_settings ( array('heigth' => 500 ));

							 ?>
								<?php wp_editor( $mail_alert_message ,'et_mail_alert_message', $custom_setting ); ?>
							</div>
							<a href="#" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | 
							<a href="#" data="register" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>

		</div>
	</div>
</div>
