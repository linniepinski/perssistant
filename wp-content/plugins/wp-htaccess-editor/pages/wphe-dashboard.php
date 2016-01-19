<?php
if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');

if(current_user_can('activate_plugins')){
	$WPHE_backup_path = WP_CONTENT_URL.'/htaccess.backup';
	$WPHE_orig_path = ABSPATH.'.htaccess';
	?>
	<div class="wrap">
	<h2 class="wphe-title">WP Htaccess Editor</h2>
	<?php
	//============================ Uložení Htaccess souboru =======================================
	if(!empty($_POST['submit']) AND !empty($_POST['save_htaccess']) AND check_admin_referer('wphe_save', 'wphe_save')){
		$WPHE_new_content = $_POST['ht_content'];
		WPHE_DeleteBackup();
		if(WPHE_CreateBackup()){
			if(WPHE_WriteNewHtaccess($WPHE_new_content)){
				echo'<div id="message" class="updated fade"><p><strong>'.__('File has been successfully changed', 'wphe').'</strong></p></div>';
				?>
				<p><?php _e('You have made changes to the htaccess file. The original file was automatically backed up (in <code>wp-content</code> folder)', 'wphe'); ?><br />
				<a href="<?php echo get_option('home'); ?>/" target="_blank"><?php _e('Check the functionality of your site (the links to the articles or categories).', 'wphe');?></a>. <?php _e('If something is not working properly restore the original file from backup', 'wphe');?></p>
				<div class="postbox" style="float: left; width: 95%; padding: 15px;">
				<form method="post" action="admin.php?page=<?php echo $WPHE_dirname; ?>">
				<?php wp_nonce_field('wphe_delete','wphe_delete'); ?>
				<input type="hidden" name="delete_backup" value="delete" />
				<p class="submit"><?php _e('If everything works properly, you can delete the backup file:', 'wphe');?> <input type="submit" class="button button-primary" name="submit" value="<?php _e('Remove backup &raquo;', 'wphe');?>" />&nbsp;<?php echo __('or','wphe'); ?>&nbsp;<a href="admin.php?page=<?php echo $WPHE_dirname; ?>_backup"><?php _e('restore the original file from backup','wphe');?></a></p>
				</form>
				</div>
				<?php
			}else{
				echo'<div id="message" class="error fade"><p><strong>'.__('The file could not be saved!', 'wphe').'</strong></p></div>';
				echo'<div id="message" class="error fade"><p><strong>'.__('Due to server configuration can not change permissions on files or create new files','wphe').'</strong></p></div>';
			}
		}else{
			echo'<div id="message" class="error fade"><p><strong>'.__('The file could not be saved!', 'wphe').'</strong></p></div>';
			echo'<div id="message" class="error fade"><p><strong>'.__('Unable to create backup of the original file! <code>wp-content</code> folder is not writeable! Change the permissions this folder!', 'wphe').'</strong></p></div>';
		}
		unset($WPHE_new_content);
	//============================ Vytvoření nového Htaccess souboru ================================
	}elseif(!empty($_POST['submit']) AND !empty($_POST['create_htaccess']) AND check_admin_referer('wphe_create', 'wphe_create')){
		if(WPHE_WriteNewHtaccess('# Created by WP Htaccess Editor') === false)
		{
			echo'<div id="message" class="error fade"><p><strong>'.__('Htaccess file is not created.', 'wphe').'</p></div>';
			echo'<div id="message" class="error fade"><p><strong>'.__('Due to server configuration can not change permissions on files or create new files','wphe').'</strong></p></div>';
        }else{
			echo'<div id="message" class="updated fade"><p><strong>'.__('Htaccess file was successfully created.', 'wphe').'</strong></p></div>';
			echo'<div id="message" class="updated fade"><p><strong><a href="admin.php?page='.$WPHE_dirname.'">'.__('View new Htaccess file', 'wphe').'</a></strong></p></div>';
        }
	//============================ Smazání zálohy =======================================
	}elseif(!empty($_POST['submit']) AND !empty($_POST['delete_backup']) AND check_admin_referer('wphe_delete', 'wphe_delete'))
	{
        if(WPHE_DeleteBackup() === false)
		{
           echo'<div id="message" class="error fade"><p><strong>'.__('Backup file could not be removed! <code>wp-content</code> folder is not writeable! Change the permissions this folder!', 'wphe').'</p></div>';
        }else{
           echo'<div id="message" class="updated fade"><p><strong>'.__('Backup file has been successfully removed.', 'wphe').'</strong></p></div>';
        }
	//============================ Home ================================================
	}else{
		?>
		<p><?php _e('Using this editor you can easily modify your htaccess file without having to use an FTP client.', 'wphe');?></p>
		<p class="wphe-red"><?php _e('<strong>WARNING:</strong> Any error in this file may cause malfunction of your site!', 'wphe');?><br />
		<?php _e('Edit htaccess file should therefore be performed only by experienced users!', 'wphe');?><br />
		</p>
		<div class="postbox wphe-box">
		<h3 class="wphe-title"><?php _e('Information for editing htaccess file', 'wphe');?></h3>
		<p><?php _e('For more information on possible adjustments to this file, please visit', 'wphe');?> <a href="http://httpd.apache.org/docs/current/howto/htaccess.html" target="_blank">Apache Tutorial: .htaccess files</a> <?php _e('or','wphe'); ?> <a href="http://net.tutsplus.com/tutorials/other/the-ultimate-guide-to-htaccess-files/" target="_blank">The Ultimate Guide to .htaccess Files</a>. </p>
		<p><?php _e('Interesting tips and guides can also be found on ', 'wphe');?> <?php WPHE_AuthorLink(); ?>.</p>
		<p><a href="http://www.google.com/#sclient=psy&q=htaccess+how+to" target="_blank"><?php _e('Or use the Google search.','wphe');?></a></p>
		</div>
		<?php
		if(!file_exists($WPHE_orig_path))
		{
			echo'<div class="postbox wphe-box">';
			echo'<pre class="wphe-red">'.__('Htaccess file does not exists!', 'wphe').'</pre>';
			echo'</div>';
			$success = false;
		}else{
			$success = true;
			if(!is_readable($WPHE_orig_path))
			{
				echo'<div class="postbox wphe-box">';
				echo'<pre class="wphe-red">'.__('Htaccess file cannot read!', 'wphe').'</pre>';
				echo'</div>';
				$success = false;
			}
			if($success == true){
				@chmod($WPHE_orig_path, 0644);
				$WPHE_htaccess_content = @file_get_contents($WPHE_orig_path, false, NULL);
				if($WPHE_htaccess_content === false){
					echo'<div class="postbox wphe-box">';
					echo'<pre class="wphe-red">'.__('Htaccess file cannot read!', 'wphe').'</pre>';
					echo'</div>';
					$success = false;
				}else{
					$success = true;
				}
			}
		}

		if($success == true){
			?>
			<div class="postbox wphe-box">
			<form method="post" action="admin.php?page=<?php echo $WPHE_dirname; ?>">
			<input type="hidden" name="save_htaccess" value="save" />
			<?php wp_nonce_field('wphe_save','wphe_save'); ?>
			<h3 class="wphe-title"><?php _e('Content of the Htaccess file', 'wphe');?></h3>
			<textarea name="ht_content" class="wphe-textarea" wrap="off"><?php echo $WPHE_htaccess_content;?></textarea>
			<p class="submit"><input type="submit" class="button button-primary" name="submit" value="<?php _e('Save file &raquo;', 'wphe');?>" /></p>
			</form>
			</div>
			<?php
			unset($WPHE_htaccess_content);
		}else{
			?>
			<div class="postbox wphe-box" style="background: #E0FCE1;">
			<form method="post" action="admin.php?page=<?php echo $WPHE_dirname; ?>">
			<input type="hidden" name="create_htaccess" value="create" />
			<?php wp_nonce_field('wphe_create','wphe_create'); ?>
			<p class="submit"><?php _e('Create new <code>.htaccess</code> file?', 'wphe');?> <input type="submit" class="button button-primary" name="submit" value="<?php _e('Create &raquo;', 'wphe');?>" /></p>
			</form>
			</div>
			<?php
		}
		unset($success);
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
	</div>
	<?php
	unset($WPHE_orig_path);
	unset($WPHE_backup_path);
}else{
	wp_die( __('You do not have permission to view this page','wphe'));
}

