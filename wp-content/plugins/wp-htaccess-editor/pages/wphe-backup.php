<?php
if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');

if(current_user_can('activate_plugins')){
	?>
	<div class="wrap">
	<h2 class="wphe-title">WP Htaccess Editor - <?php _e('Backup', 'wphe'); ?></h2>
	<?php
	//============================ Restore Backup ===================================
	if(!empty($_POST['submit']) && !empty($_POST['restore_backup']) && check_admin_referer('wphe_restoreb', 'wphe_restoreb'))
	{

		$wphe_restore_result = WPHE_RestoreBackup();
		if($wphe_restore_result === false)
		{
			echo'<div id="message" class="error fade"><p><strong>'.__('Unable to restore backup! Probably the wrong setting write permissions to the files.', 'wphe').'</strong></p></div>';
			echo'<div class="postbox wphe-box">';
			echo'<p>'.__('The backup file is located in the <code>wp-content</code> folder.','wphe').'</p>';
			echo'</div>';
		}elseif($wphe_restore_result === true)
		{
			echo'<div id="message" class="updated fade"><p><strong>'.__('Backup was restored successfully', 'wphe').'</strong></p></div>';
			echo'<div id="message" class="updated fade"><p><strong>'.__('Old backup file was deleted successfully', 'wphe').'</strong></p></div>';
		}else
		{
			echo'<div id="message" class="error fade"><p><strong>'.__('Unable to restore backup!', 'wphe').'</strong></p></div>';
			echo'<div class="postbox wphe-box" style="background: #FFEECE;">';
			echo'<p class="wphe-red">'.__('This is contents of the original file, put it into a file manually','wphe').':</p>';
			echo'<textarea class="wphe-textarea">'.$wphe_restore_result.'</textarea>';
			echo'</div>';
		}
	//============================== Create Backup ===================================
	}elseif(!empty($_POST['submit']) && !empty($_POST['create_backup']) && check_admin_referer('wphe_createb', 'wphe_createb')){
		if(WPHE_CreateBackup())
		{
			echo'<div id="message" class="updated fade"><p><strong>'.__('Backup file was created successfully', 'wphe').'</strong></p></div>';
			echo'<div class="postbox wphe-box">';
			echo'<p>'.__('The backup file is located in the <code>wp-content</code> folder.','wphe').'</p>';
			echo'</div>';
		}else{
			echo'<div id="message" class="error fade"><p><strong>'.__('Unable to create backup! <code>wp-content</code> folder is not writeable! Change the permissions this folder manually!', 'wphe').'</strong></p></div>';
			echo'<div id="message" class="error fade"><p><strong>'.__('Due to server configuration can not change permissions on files or create new files','wphe').'</strong></p></div>';
		}
	//============================== Delete Backup ====================================
	}elseif(!empty($_POST['submit']) && !empty($_POST['delete_backup']) && check_admin_referer('wphe_deleteb', 'wphe_deleteb'))
	{
		if(WPHE_DeleteBackup())
		{
			echo'<div id="message" class="updated fade"><p><strong>'.__('Backup file was successfully removed', 'wphe').'</strong></p></div>';
		}else{
			echo'<div id="message" class="error fade"><p><strong>'.__('Backup file could not be removed! Probably the wrong setting write permissions to the files.','wphe').'</strong></p></div>';
			echo'<div id="message" class="error fade"><p><strong>'.__('Due to server configuration can not change permissions on files or create new files','wphe').'</strong></p></div>';
		}
	//============================== Home ==============================================
	}else{
		if(file_exists(ABSPATH.'wp-content/htaccess.backup'))
		{
			echo '<div class="postbox wphe-box" style="background: #FFEECE;">';
			?>
			<form method="post" action="admin.php?page=<?php echo $WPHE_dirname; ?>_backup">
			<?php wp_nonce_field('wphe_restoreb','wphe_restoreb'); ?>
			<input type="hidden" name="restore_backup" value="restore" />
			<p class="submit"><?php _e('Do you want to restore the backup file?', 'wphe'); ?> <input type="submit" class="button button-primary" name="submit" value="<?php _e('Restore backup &raquo;', 'wphe'); ?>" /></p>
			</form>
			<?php
			echo '</div>';
			echo '<div class="postbox wphe-box" style="background: #FFEECE;">';
			?>
			<form method="post" action="admin.php?page=<?php echo $WPHE_dirname; ?>_backup">
			<?php wp_nonce_field('wphe_deleteb','wphe_deleteb'); ?>
			<input type="hidden" name="delete_backup" value="delete" />
			<p class="submit"><?php _e('Do you want to delete a backup file?', 'wphe'); ?> <input type="submit" class="button button-primary" name="submit" value="<?php _e('Remove backup &raquo;', 'wphe'); ?>" /></p>
			</form>
			<?php
			echo '</div>';
		}else{
			echo '<div class="postbox wphe-box">';
			echo '<pre class="wphe-red">'.__('Backup file not found...','wphe').'</pre>';
			echo '</div>';

			echo '<div class="postbox wphe-box" style="background: #E0FCE1;">';
			?>
			<form method="post" action="admin.php?page=<?php echo $WPHE_dirname; ?>_backup">
			<?php wp_nonce_field('wphe_createb','wphe_createb'); ?>
			<input type="hidden" name="create_backup" value="create" />
			<p class="submit"><?php _e('Do you want to create a new backup file?', 'wphe'); ?> <input type="submit" class="button button-primary" name="submit" value="<?php _e('Create new &raquo;', 'wphe'); ?>" /></p>
			</form>
			<?php
			echo '</div>';
		}
	}
	?>
	<p style="clear:both;">&nbsp;</p>
		<div class="postbox wphe-box">
			<h3 class="wphe-title"><?php _e('Information about this plugin', 'wphe');?></h3>
		<div class="wphe-infobox">
		<h4><?php _e('Author','wphe'); ?></h4>
		<p style="padding-left: 5px"><em>Lukenzi</em>&nbsp;&nbsp;<?php WPHE_AuthorLink(); ?></p>
		</div>
		<div class="wphe-infobox">
		<h4><?php _e('Translators','wphe'); ?></h4>
		<table class="wphe-translators">
		<tr><td><em>Lukenzi</em></td><td><img src="<?php echo $WPHE_dirurl.'style/img/flag-cz.jpg'; ?>" border="0" alt="Czech" width="12" height="9" /></td><td><?php WPHE_AuthorLink(); ?></td></tr>
		<tr><td><em>Andi Eko</em></td><td><img src="<?php echo $WPHE_dirurl.'style/img/flag-en.jpg'; ?>" border="0" alt="English" width="12" height="9" /></td><td><a href="http://andieko.info/" target="_blank">andieko.info</a></td></tr>
		<tr><td><em>Andrew Kurtis</em></td><td><img src="<?php echo $WPHE_dirurl.'style/img/flag-es.gif'; ?>" border="0" alt="Spanish" width="12" height="9" /></td><td><a href="http://www.webhostinghub.com" target="_blank">WebHostingHub.com</a></td></tr>
		</table>
		</div>
		<div class="wphe-infobox">
		<h4><?php _e('Plugin','wphe'); ?></h4>
		<table>
		<tr><td><em><?php _e('Version:','wphe'); ?></em></td><td><span class="wphe-green"><?php echo $WPHE_version; ?></span></td></tr>
		<tr><td><em><?php _e('Plugin homepage:','wphe'); ?></em></td><td><a href="http://wordpress.org/extend/plugins/wp-htaccess-editor/" target="_blank"><?php _e('WordPress repository','wphe'); ?></a></td></tr>
		<tr><td><em><?php _e('Support:','wphe'); ?></em></td><td><a href="http://wordpress.org/support/plugin/wp-htaccess-editor"><?php _e('Forum','wphe'); ?></a></td></tr>
		<tr><td><em><?php _e('Support for the development:','wphe');?></em></td><td><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KM4GZAE9FVPSN"><?php _e('Donate','wphe'); ?></a></td></tr>
		</table>
		</div>
		<div class="wphe-infobox">
		<h4><?php _e('Other plugins','wphe'); ?></h4>
		<table>
		<tr><td><em><a href="http://wordpress.org/plugins/wedos-news/" target="_blank">WEDOS News</a></em></td></tr>
		<tr><td><em><a href="http://wp-blog.cz/wpb-file-manager-spravce-souboru-pro-wordpress/" target="_blank">WPB File Manager</a></em></td></tr>
		<tr><td><em><a href="http://wp-blog.cz/system-files-editor/" target="_blank">System Files Editor</a></em></td></tr>
		<tr><td><em><a href="http://wordpress.org/extend/plugins/ceska-podpora-wordpressu/" target="_blank"><?php _e('Czech support for WordPress','wphe'); ?></a></em></td></tr>
		</table>
		</div>
		<div class="wphe-infobox">
		<h4><?php _e('Donate','wphe'); ?></h4>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="KM4GZAE9FVPSN">
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
		</div>
		</div>
	<p style="clear:both;">&nbsp;</p>
	<p style="clear:both;">&nbsp;</p>
	<p style="clear:both;">&nbsp;</p>
	</div>
	<?php
}else{
	wp_die( __('You do not have permission to view this page','wphe'));
}
