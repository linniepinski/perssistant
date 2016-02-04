<?php

/**
 * Template Name: Interview page

 */

global $user_ID, $ae_post_factory;
if (ae_user_role($user_ID) != FREELANCER || get_user_meta($user_ID, 'interview_status', true) == 'confirmed') {

    wp_redirect(home_url());

}
$interview_object = $ae_post_factory->get('interview');
// user already login redirect to home page
//var_dump($interview_object);

$args = array(
    'author' => $user_ID,
    'post_type' => 'interview'
);
$interview_post = new WP_Query($args);

$last_post = array_shift($interview_post->posts);

$interview_meta = get_post_meta($last_post->ID);

$post_id = $last_post->ID;
if ($post_id)





get_header();



?>


    <!--    <script type="text/javascript" src="/bower_components/jquery/jquery.min.js"></script>-->
    <script type="text/javascript" src="/wp-content/themes/freelanceengine/js/moment.js"></script>
    <!--    <script type="text/javascript" src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>-->
    <script type="text/javascript" src="/wp-content/themes/freelanceengine/js/bootstrap-datetimepicker.min.js"></script>
    <!--    <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css" />-->
    <link rel="stylesheet" href="/wp-content/themes/freelanceengine/css/bootstrap-datetimepicker.min.css"/>


    <section class="blog-header-container">

        <div class="container">

            <!-- blog header -->

            <div class="row">

                <div class="col-md-12 blog-classic-top">

                    <h2><?php the_title(); ?></h2>
                    <div class="alert alert-warning" role="alert">If your plans or contact information changed, you can always refresh this page and update needed fields! Thank you!</div>
                </div>

            </div>

            <!--// blog header  -->

        </div>

    </section>


    <div class="container page-container">

        <!-- block control  -->

        <div class="row block-posts block-page">

            <div class="col-md-12 col-sm-12 col-xs-12 posts-container" id="left_content">

                <div class="blog-content">

                    <form id="interview_form" class="interview_form">
                        <input type="hidden" id="post_id" name="post_id" value="<?php echo $post_id;?>">

                        <label>Please enter 3 possible dates and times to talk to us on Skype or call:</label>

                        <div class="form-group">

                        </div>

                        <div id="picked-dates" class="row">
                            <div class='col-sm-6'>
                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input id="date_interview_1" value="<?php if ($post_id) echo date('m/d/Y g:i A',$interview_meta['date_interview_1'][0]); ?>" name="date-interview" type='text' class="form-control"/>
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker2'>
                                        <input id="date_interview_2" value="<?php if ($post_id) echo date('m/d/Y g:i A',$interview_meta['date_interview_2'][0]) ; ?>" name="date-interview" type='text' class="form-control"/>
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker3'>
                                        <input id="date_interview_3" value="<?php if ($post_id) echo date('m/d/Y g:i A',$interview_meta['date_interview_3'][0]); ?>" name="date-interview" type='text' class="form-control"/>
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                                    </div>
                                </div>

                            </div>
                            <div class='col-sm-6'>

                                <div class="form-group">
                                    <label for="interview_skype">Please fill in your skype ID :</label>
                                    <input type="text" value="<?php echo $interview_meta['skype_id'][0]; ?>" id="interview_skype" name="interview_skype" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="interview_tel">or telephone nr :</label>
                                    <input type="tel" value="<?php echo $interview_meta['tel'][0]; ?>" id="interview_tel" name="interview_tel" class="form-control">
                                </div>
                            </div>

                        </div>


                        <script type="text/javascript">
                            jQuery(function () {
                                var icons_option = {
                                    time: "fa fa-clock-o",
                                    date: "fa fa-calendar",
                                    up: "fa fa-arrow-up",
                                    down: "fa fa-arrow-down",
                                    previous: "fa fa-arrow-left",
                                    next: "fa fa-arrow-right"
                                };
                                jQuery('#datetimepicker1').datetimepicker({
                                    icons: icons_option,
                                    minDate: '<?php echo date('m/d/Y',time())?>'
                                });
                                jQuery('#datetimepicker2').datetimepicker({
                                    icons: icons_option,
                                    minDate: '<?php echo date('m/d/Y',time())?>'

                                });
                                jQuery('#datetimepicker3').datetimepicker({
                                    icons: icons_option,
                                    minDate: '<?php echo date('m/d/Y',time())?>'

                                });
                            });
                        </script>
                        <br>


                        <div class="clearfix"></div>

                        <button type="submit" class="btn-submit btn-sumary btn-sub-create">

                            <?php _e('Update possible dates', ET_DOMAIN) ?>

                        </button>

                        <?php



                        ?>
                    </form>

                    <div class="clearfix"></div>

                </div>
                <!-- end page content -->

            </div>
        </div>

        <!--// block control  -->

    </div>

<?php

get_footer();

?>