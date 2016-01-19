<?php
$user_info = get_userdata($item['author_id']);
?>
<div class="row">
    <div class="col-xs-12">
        <h4><?php echo $item['project_title']; ?> </h4>
    </div>
    <div class="col-xs-8">
        <?php
        //var_dump($item['ID']);
        $project_meta = get_post_meta($item['ID']);
        $bid_budget = get_post_meta($project_meta['accepted'][0],'bid_budget',true);
        //var_dump($bid_budget);
        //        var_dump($project_meta);
        //var_dump($project_meta['et_budget'][0]);
        //var_dump($project_meta['hours_limit'][0]);

        ?>
        <a class="trace-link-author"
           href="/author/<?php echo $user_info->nickname; ?>"><?php echo $item['bid_author']; ?></a>

        <div>Hired by <a href="/author/<?php echo $user_info->nickname; ?>"><?php echo $item['project_author']; ?></a>
        </div>
        <a class="job-details" href="<?php echo $item['guid']; ?>">Job details</a>
        <a class="send-message" href="/chat-room?chat_contact=<?php echo $item['author_id']; ?>">Send message...</a>
    </div>
    <div class="col-xs-4">
        <div class="">
            <?php
            if ($project_meta['type_budget'][0] == 'hourly_rate') {
                echo fre_price_format($bid_budget) . '/h';
            } else {
                echo fre_price_format($bid_budget);
            }

            ?>
        </div>
        <div class="trace-time-of-week">
            <?php echo get_tracker_time_of_week($current_user->ID, $item['ID']) . ' this week' ;?>
            <?php
            if ($project_meta['type_budget'][0] == 'hourly_rate') {
                echo  ' of ' . $project_meta['hours_limit'][0] . ' hours limit.';
            }
            ?>
        </div>
        <div class="trace-link-work-diary">
            <a class="work-diary" href="/diary?project_id=<?php echo $item['ID']; ?>">View work diary</a>
        </div>
    </div>
</div>