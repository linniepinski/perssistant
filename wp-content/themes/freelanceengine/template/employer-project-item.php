<?php
/**
 * The template for displaying  
 * @param snippet
 * @since snippet.
 * @author Dakachi
 */
global $wp_query, $ae_post_factory, $post, $role;

$current        = $ae_post_factory->get( PROJECT );
$project        = $current->current_post;
$total_bids     = $project->total_bids ? $project->total_bids : 0;
$bid_average    = $project->bid_average ? $project->bid_average: 0;
$bid_accept_id  = $project->accepted ? $project->accepted : 0;
$status         = get_post_status($project->ID);


?>
<li <?php post_class( 'project-item' ); ?> >
    <div class="name-history">
        <span>
            <?php if($role == 'freelancer') echo get_avatar( $project->post_author, 35 ); ?>
        </span>
        <span><a href="<?php the_permalink($project->ID);?>"><?php echo $project->post_title;?> </a></span><br />
        <?php
        if($status =='complete'){
            _e('Status : Finished<br /> list review here', ET_DOMAIN);
        } else if($status == 'publish' && $bid_accept_id) {
            _e('Status:Working', ET_DOMAIN);
        } else if($status =='publish' && !$bid_accept_id ){
            _e('Status:Hire opening',ET_DOMAIN);
        }
        ?>
        <div class="clearfix"></div>
    </div>
    <ul class="info-history">
    <?php
    if($bid_accept_id){
        $bid_accept = get_post($bid_accept_id);

        if($bid_accept && $status != 'complete'){                
            $profile_id     = get_user_meta($bid_accept->post_author,'user_profile_id',true);         
            $bid_budget     = get_post_meta($bid_accept_id,'bid_budget', true);
            $bid_time       = get_post_meta($bid_accept_id,'bid_time', true);
            $type_time      = get_post_meta($bid_accept_id,'type_time', true);
        ?>    
            
            <li> <span> <?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) ; ?> ago </li>
            <li> <span> </span> </li>
            <li><span> <?php echo $project->et_budget; ?> </span>$ </li>

        <?php        
        } else if($status == 'complete'){ ?>           
          
            <li><span> <?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) ; ?> ago </li>
            <li><span> <?php echo $project->et_budget; ?> </span>$ </li>

            <?php           
        } 
    } else { ?>
        <li> <span> <?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) ; ?> ago </li>        
        <li> <span> <?php echo $project->et_budget; ?> </span>$ </li>
        <?php        
    }

    ?>
       
    </ul>
    <div class="list-review">

        <?php
            $args = array(
           // 'status' => 'approve',
           // 'number' => '',
            'type' => 'review',
            'post_id' => $project->ID, // use post_id, not post_ID
        );      

        $comments = get_comments($args);        
        if(!empty($comments)){
            foreach($comments as $comment) :
                echo $comment->comment_author . '<br />';
                echo $comment->comment_content;
            endforeach;    
        }     
        ?>
    </div>
</li>


    