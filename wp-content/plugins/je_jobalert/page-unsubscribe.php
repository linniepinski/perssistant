<?php
/**
 * Template Name: Unsubsribe Page
 */
get_header();
?>
<div class="wrapper clearfix">
	<div class="heading">
		<div class="main-center">
			<h1 class="title job-title" id="job_title">Unsubscribe</h1>
		</div>
	</div>
	<div class="main-center">
		<div class="full-column">
			<div class="entry-blog tinymce-style">
		      	<?php _e('Are you sure you want to unsubscribe? You won\'t be able to receive new job alerts in your inbox afterward.',ET_DOMAIN); ?>
				<div class="widget-job-alert" style="padding:0">		      	
					<div class="unsubscribe" style="padding-top:20px;float:left;">
						<form id="unsubscribe_form" method="POST" action="">
							<input type="hidden" name="code" id="code" value="<?php echo $_REQUEST['code'];?>" />
							<input type="hidden" name="action" id="action" value="je-remove-subscriber" />
							<input class="bg-default-input" type="text" disabled="disabled" placeholder="Email address" name="email" id="email" value="<?php echo $_REQUEST['email'];?>"  />
						</form>
					</div>
					<a id="unsubscribe_btn" href="javascript:void(0);" class="bg-btn-action backhome-btn" style="margin-left:10px;float:left;margin-top:19px;">
						<?php _e('Unsubscribe', ET_DOMAIN);?>
					</a>	      	
			 	</div>
		 	</div>
		</div>
	</div>
</div>
<?php 
get_footer();