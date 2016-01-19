<?php
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get( PROFILE );
$current = $post_object->current_post;
if(!$current){
    return;
}
?>
<li class="profile-item">
    <div class="avatar-proflie">
        <a href="<?php echo $current->permalink; ?>" ><?php echo $current->et_avatar; ?></a>   
    </div>
    <div class="user-proflie">
        <a href="<?php echo $current->permalink; ?>" class="name"><?php echo $current->author_name; ?></a>
        <span class="position"><?php echo $current->et_professional_title; ?></span>
    </div>
    <div class="clearfix"></div>
    <ul class="wrapper-achivement">
        <li>
            <div class="rate-it" data-score="<?php echo $current->rating_score ; ?>"></div>
        </li>
        <li><span><?php echo $current->hourly_rate_price; ?></span></li>
        <li><span><?php echo $current->experience ?></span></li>
    </ul>
  
</li>