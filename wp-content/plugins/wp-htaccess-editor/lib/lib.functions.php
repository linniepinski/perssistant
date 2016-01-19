<?php
if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');



/***** Vytvoření zálohy htaccess souboru ******************************/
function WPHE_CreateBackup(){
	$WPHE_backup_path = ABSPATH.'wp-content/htaccess.backup';
	$WPHE_orig_path = ABSPATH.'.htaccess';
	@clearstatcache();

	WPHE_CreateSecureWPcontent();

	if(file_exists($WPHE_backup_path)){
		WPHE_DeleteBackup();

		if(file_exists(ABSPATH.'.htaccess')){
			$htaccess_content_orig = @file_get_contents($WPHE_orig_path, false, NULL);
			$htaccess_content_orig = trim($htaccess_content_orig);
			$htaccess_content_orig = str_replace('\\\\', '\\', $htaccess_content_orig);
			$htaccess_content_orig = str_replace('\"', '"', $htaccess_content_orig);
			@chmod($WPHE_backup_path, 0666);
			$WPHE_success = @file_put_contents($WPHE_backup_path, $htaccess_content_orig, LOCK_EX);
			if($WPHE_success === false)
			{
				unset($WPHE_backup_path);
				unset($WPHE_orig_path);
				unset($htaccess_content_orig);
				unset($WPHE_success);
				return false;
			}else{
				unset($WPHE_backup_path);
				unset($WPHE_orig_path);
				unset($htaccess_content_orig);
				unset($WPHE_success);
				return true;
			}
			@chmod($WPHE_backup_path, 0644);
		}else{
			unset($WPHE_backup_path);
			unset($WPHE_orig_path);
			return false;
		}
	}else{
		if(file_exists(ABSPATH.'.htaccess')){
			$htaccess_content_orig = @file_get_contents($WPHE_orig_path, false, NULL);
			$htaccess_content_orig = trim($htaccess_content_orig);
			$htaccess_content_orig = str_replace('\\\\', '\\', $htaccess_content_orig);
			$htaccess_content_orig = str_replace('\"', '"', $htaccess_content_orig);
			@chmod($WPHE_backup_path, 0666);
			$WPHE_success = @file_put_contents($WPHE_backup_path, $htaccess_content_orig, LOCK_EX);
			if($WPHE_success === false){
				unset($WPHE_backup_path);
				unset($WPHE_orig_path);
				unset($htaccess_content_orig);
				unset($WPHE_success);
				return false;
			}else{
				unset($WPHE_backup_path);
				unset($WPHE_orig_path);
				unset($htaccess_content_orig);
				unset($WPHE_success);
				return true;
			}
			@chmod($WPHE_backup_path, 0644);
		}else{
			unset($WPHE_backup_path);
			unset($WPHE_orig_path);
			return false;
		}
	}
}



/***** Vytvoření htaccess souboru ve složce wp-content ****************/
function WPHE_CreateSecureWPcontent(){
	$wphe_secure_path = ABSPATH.'wp-content/.htaccess';
	$wphe_secure_text = '
# WP Htaccess Editor - Secure backups
<files htaccess.backup>
order allow,deny
deny from all
</files>
';

	if(is_readable(ABSPATH.'wp-content/.htaccess')){
		$wphe_secure_content = @file_get_contents(ABSPATH.'wp-content/.htaccess');

		if($wphe_secure_content !== false){
			if(strpos($wphe_secure_content, 'Secure backups') === false){
				unset($wphe_secure_content);
				$wphe_create_sec = @file_put_contents(ABSPATH.'wp-content/.htaccess', $wphe_secure_text, FILE_APPEND|LOCK_EX);
				if($wphe_create_sec !== false){
					unset($wphe_secure_text);
					unset($wphe_create_sec);
					return true;
				}else{
					unset($wphe_secure_text);
					unset($wphe_create_sec);
					return false;
				}
			}else{
				unset($wphe_secure_content);
				return true;
			}
		}else{
			unset($wphe_secure_content);
			return false;
		}
	}else{
		if(file_exists(ABSPATH.'wp-content/.htaccess')){
			return false;
		}else{
			$wphe_create_sec = @file_put_contents(ABSPATH.'wp-content/.htaccess', $wphe_secure_text, LOCK_EX);
			if($wphe_create_sec !== false){
				return true;
			}else{
				return false;
			}
		}
	}
}



/***** Obnova zálohy htaccess souboru *********************************/
function WPHE_RestoreBackup(){
	$wphe_backup_path = ABSPATH.'wp-content/htaccess.backup';
	$WPHE_orig_path = ABSPATH.'.htaccess';
	@clearstatcache();

	if(!file_exists($wphe_backup_path)){
		unset($wphe_backup_path);
		unset($WPHE_orig_path);
		return false;
	}else{
		if(file_exists($WPHE_orig_path)){
			if(is_writable($WPHE_orig_path)){
				@unlink($WPHE_orig_path);
			}else{
				@chmod($WPHE_orig_path, 0666);
				@unlink($WPHE_orig_path);
			}
		}
		$wphe_htaccess_content_backup = @file_get_contents($wphe_backup_path, false, NULL);
		if(WPHE_WriteNewHtaccess($wphe_htaccess_content_backup) === false){
			unset($wphe_success);
			unset($WPHE_orig_path);
			unset($wphe_backup_path);
			return $wphe_htaccess_content_backup;
		}else{
			WPHE_DeleteBackup();
			unset($wphe_success);
			unset($wphe_htaccess_content_backup);
			unset($WPHE_orig_path);
			unset($wphe_backup_path);
			return true;
		}
	}
}



/***** Smazání záložního souboru **************************************/
function WPHE_DeleteBackup(){
	$wphe_backup_path = ABSPATH.'wp-content/htaccess.backup';
	@clearstatcache();

	if(file_exists($wphe_backup_path)){
		if(is_writable($wphe_backup_path)){
			@unlink($wphe_backup_path);
		}else{
			@chmod($wphe_backup_path, 0666);
			@unlink($wphe_backup_path);
		}

		@clearstatcache();

		if(file_exists($wphe_backup_path)){
			unset($wphe_backup_path);
			return false;
		}else{
			unset($wphe_backup_path);
			return true;
		}
	}else{
		unset($wphe_backup_path);
		return true;
	}
}



/***** Vytvoření nového htaccess souboru ******************************/
function WPHE_WriteNewHtaccess($WPHE_new_content){
	$WPHE_orig_path = ABSPATH.'.htaccess';
	@clearstatcache();

	if(file_exists($WPHE_orig_path))
	{
		if(is_writable($WPHE_orig_path))
		{
			@unlink($WPHE_orig_path);
		}else{
			@chmod($WPHE_orig_path, 0666);
			@unlink($WPHE_orig_path);
		}
	}
	$WPHE_new_content = trim($WPHE_new_content);
	$WPHE_new_content = str_replace('\\\\', '\\', $WPHE_new_content);
	$WPHE_new_content = str_replace('\"', '"', $WPHE_new_content);
	$WPHE_write_success = @file_put_contents($WPHE_orig_path, $WPHE_new_content, LOCK_EX);
	@clearstatcache();
	if(!file_exists($WPHE_orig_path) && $WPHE_write_success === false)
	{
		unset($WPHE_orig_path);
		unset($WPHE_new_content);
		unset($WPHE_write_success);
		return false;
	}else{
		unset($WPHE_orig_path);
		unset($WPHE_new_content);
		unset($WPHE_write_success);
		return true;
	}
}



/****** debug funkce **************************************************/
function WPHE_Debug($data){
	echo '<pre>';
	var_dump($data);
	echo '</pre>';
}
