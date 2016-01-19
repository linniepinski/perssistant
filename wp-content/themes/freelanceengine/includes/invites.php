<?php
/**
 * create a comment with type fre_invite
 * @param int $user_id
 * @param int $project
 * @return int $invite_id
 * @since 1.3.1
 * @author Dakachi
 */
function fre_create_invite($user_id, $project_id){
	global $user_ID, $current_user;
	$invite_id = wp_insert_comment(array(
        'comment_post_ID' => $project_id,
        'comment_author' => $current_user->data->user_login,
        'comment_author_email' => $current_user->data->user_email,
        'comment_content' => sprintf(__("Invite %s to bid project", ET_DOMAIN), get_the_author_meta( 'display_name', $user_id )) ,
        'comment_type' => 'fre_invite',
        'user_id' => $user_ID,
        'comment_approved' => 1
    ));
    update_comment_meta( $invite_id, 'invite', $user_id);
    return $invite_id;
}
/**
 * check user is invited on project or not 
 * @param int $user_id
 * @param int $project
 * @return bool $project
 * @since 1.3.1
 * @author Dakachi
 */
function fre_check_invited($user_id, $project_id){
	$invites = get_comments( array(
			'post_id' => $project_id,
			'meta_value' => $user_id,
			'meta_key' => 'invite',
			'type' => 'fre_invite',
			'number' => 1, 
			'approved' => 1
		) );
	if(empty($invites)) return false;
	return true;
}
