<div class="row">
    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
    <input type="hidden" name="current_user" value="<?php echo $freelancer_id; ?>">

    <div class="col-xs-12">
        <a href="/trace">Back to projects</a>
    </div>
</div>
<div class="row">
    <div class="col-xs-8">
        <h4><?php echo $current_project_post->post_title ?></h4>

        <p><?php echo get_tracker_time_of_week($freelancer_id, $project_id) ?> time this week</p>
    </div>
    <div class="col-xs-2">
        <p>Selected : <span class="select-count-diary">00:00</span> min</p>
    </div>
    <div class="col-xs-2">
        <!--                    <button type="button" class="btn btn-primary" id="delete_items">Delete</button>-->
        <button type="button" class="btn btn-warning" id="select_all" data-toogle="1">Select all</button>
    </div>
</div>