<?php
/**
 * The template for displaying project report list, form in single project
 */
global $post, $user_ID;
$date_format = get_option('date_format');
$time_format = get_option('time_format');

$query_args = array('type' => 'fre_report', 'post_id' => $post->ID , 'paginate' => 'load', 'order' => 'DESC', 'orderby' => 'date' );
/**
 * count all reivews
*/
$total_args = $query_args;
$all_cmts   = get_comments( $total_args );

/**
 * get page 1 reviews
*/
$query_args['number'] = 10;//get_option('posts_per_page');
$comments = get_comments( $query_args );

$total_messages = count($all_cmts);
$comment_pages  =   ceil( $total_messages/$query_args['number'] );
$query_args['total'] = $comment_pages;
$query_args['text'] = __("Load older message", ET_DOMAIN);

$messagedata = array();
$message_object = new Fre_Report('fre_report');

?>
<div class="project-workplace-details report-details">
    <div class="row">
        <div class="col-md-8 col-xs-12 report-container">
            <div class="report-attention">
                <div class="icon-attention"><i class="fa fa-exclamation-triangle"></i> <?php _e("Attention", ET_DOMAIN); ?></div>
                <div class="attention-content">
                    <p>
                    <?php
                        $project_report_by = get_post_meta($post->ID, 'dispute_by', true);
                        if($project_report_by) {
                            $reporter = $project_report_by;
                        }else {
                            $reporter = $post->post_author;
                        }
                        $reporter_name = "<strong>".get_the_author_meta( 'display_name', $reporter ) ."</strong>";
                        if($reporter == $post->post_author) {
                            printf(__("This project has been closed by %s.", ET_DOMAIN), $reporter_name);
                        }else{
                            printf(__("This project has been quit by %s.", ET_DOMAIN), $reporter_name);
                        }
                        
                        echo '<br/>';
                        _e("We will review both sides' reports to have the right decision. Please take your time to submit the report. All proofs such as emails, contracts, files,...will be accepted.", ET_DOMAIN);
                        echo '</br/>';
                        _e("We will be back with the final result as soon as possible.  ", ET_DOMAIN);
                    ?>
                    </p>    
                </div>
            </div>
        	<div class="work-report-wrapper">
                <div class="form-report-wrapper" >
                <?php if($post->post_status == 'disputing') { ?>
                	<form class="form-group-work-place-wrapper form-report">
                    	<div class="form-group-work-place " id="report_docs_container">
                            <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'file_et_uploader' ) ?>"></span>
                            <div class="content-report-wrapper">
                            	<span class="text-your-report"><?php _e("Your report", ET_DOMAIN); ?></span>
                                <div class="form-group">
                                    <textarea name="comment_content" class="content-chat"></textarea>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="submit" value="<?php _e( "Send" , ET_DOMAIN ); ?>" class="submit-chat-content">
                                    <input type="hidden" name="comment_post_ID" value="<?php echo $post->ID; ?>" />
                                </div>
                            </div>
                            <div class="file-attachment-wrapper">
                                <div class="title-attachment"><?php _e("Attachment", ET_DOMAIN); ?></div>
                                <a href="#" class="attach-file-button" id="report_docs_browse_button">
                                    <i class="fa fa-plus-circle"></i><?php _e("Attach file", ET_DOMAIN); ?>
                                </a>
                                <ul class="file-attack-report apply_docs_file_list" id="apply_docs_file_list">
                                    <!-- report file list -->
                                </ul>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </form>                
                    <?php 
                    }

                    if(current_user_can( 'manage_options' ) && ae_get_option('use_escrow') && $post->post_status == 'disputing') { ?>
                        <form class="transfer-escrow">
                            <span class="text-transfer-escrow"><?php _e("Escrow transfer to", ET_DOMAIN); ?></span>
                            <div class="form-group">
                                <select class="transfer-select">
                                    <option value="freelancer"><?php _e("Freelancer", ET_DOMAIN); ?></option>
                                    <option value="employer"><?php _e("Employer", ET_DOMAIN); ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" value="<?php _e( "Proceed" , ET_DOMAIN ); ?>" class="btn submit-proceed-report">
                            </div>
                        </form>
                
                    <?php } ?>
                </div>
                <ul class="list-chat-work-place">
                    <?php 
                    foreach ($comments as $key => $message) { 
                        $convert = $message_object->convert($message);
                        $messagedata[] = $convert;
                        $display_name = get_the_author_meta( 'display_name', $message->user_id );
                    ?>
                	<li class="message-item" id="comment-<?php echo $message->comment_ID; ?>">
                    	<div class="form-group-work-place">
                        	<div class="info-avatar-report">
                            	<a href="#" class="avatar-employer-report">
									<?php echo $message->avatar; ?>
                                </a>
                                <div class="info-report">
                                    <span class="name-report"><?php printf(__("%s's report", ET_DOMAIN), $display_name); ?></span>
                                    <div class="date-chat-report">
                                        <?php 
                                            echo $message->message_time;
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="content-report-wrapper">
                                <div class="content-report">
                                	<?php echo $convert->comment_content; ?>
                                </div>
                                <?php 
                                    if( $convert->file_list) { ?>
                                        <div class="title-attachment"><?php _e("Attachment", ET_DOMAIN); ?></div>
                                    <?php 
                                        echo $convert->file_list; 
                                    }
                                ?>
                            </div>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
                <?php if($comment_pages > 1) { ?>
                    <div class="paginations-wrapper" >
                        <?php ae_comments_pagination( $comment_pages, $paged ,$query_args );   ?>
                    </div>
                <?php } ?>
                <?php echo '<script type="json/data" class="postdata" > ' . json_encode($messagedata) . '</script>'; ?>
            </div>
        </div>
        <?php if(!et_load_mobile()) { ?>
        <div class="col-md-4 col-xs-12 workplace-project-details">
        	<div class="content-require-project">
                <?php 
                if(fre_access_workspace($post)) {
                    $project_link = get_permalink( $post->ID );
                    echo '<a style="font-weight:600;" href="'.add_query_arg(array('workspace' => 1), $project_link).'">'
                            .__("Open workspace", ET_DOMAIN).' <i class="fa fa-arrow-right"></i>
                        </a>';
                }
                ?>
                <h4><?php _e('Project description:',ET_DOMAIN);?></h4>
                <?php the_content(); ?>

            </div>
        </div>
        <?php } ?>
    </div>
</div>
