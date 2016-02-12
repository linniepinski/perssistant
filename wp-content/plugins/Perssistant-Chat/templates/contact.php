<?php
$contact_user = $_POST['contact_with_user'];
$user = get_userdata( $contact_user );
?><hr class="visible-xs">
<div class="item_contact" id_contact_now="<?php echo $user->ID?>">
    <p><?php echo get_avatar($user->ID, 45); ?>
    <?php echo $user->display_name ?></p>
</div>