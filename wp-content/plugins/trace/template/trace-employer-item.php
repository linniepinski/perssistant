<?php
$user_info = get_userdata($item['author_id']);
?>
<div class="row">
    <div class="col-xs-12">
        <h4><?php echo $item['title']; ?> </h4>
    </div>
    <div class="col-xs-8">
        <div>Hired <a class="trace-link-author" href="/author/<?php echo $user_info->nickname; ?>"><?php echo $item['bid_author']; ?></a></div>

        <a class="project-details" href="<?php echo $item['guid']; ?>">Project details</a>
        <a class="send-message" href="/chat-room?chat_contact=<?php echo $item['author_id']; ?>">Send message...</a>
    </div>
    <div class="col-xs-4">
        <div class="trace-time-of-week">
            <?php echo get_tracker_time_of_week($item['author_id'], $item['post_parent']) . ' this week'; ?>
        </div>
        <div class="trace-link-work-diary">
            <a class="work-diary" href="/diary?project_id=<?php echo $item['post_parent'] . '&freelancer_id=' . $item['author_id']; ?>">View
                work diary </a>
        </div>
    </div>
</div>