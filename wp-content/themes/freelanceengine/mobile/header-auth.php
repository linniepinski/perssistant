<?php
    /**
     * Header for page auth 
     */
?>
<!DOCTYPE html>
<!--[if IE 7]>
	<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
	<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <?php 
        // echo "<script src='".get_template_directory_uri()."/js/modernizr.min.js'></script>";
        ae_favicon();
        wp_head(); 
        if(function_exists('et_mobile_render_less_style')) {
            et_mobile_render_less_style();    
        }        
    ?>
</head>
<body <?php body_class( 'class-img-body cbp-spmenu-push' );  ?>>
<?php
    if($user_ID) {
        echo '<script type="data/json"  id="user_id">'. json_encode(array('id' => $user_ID, 'ID'=> $user_ID) ) .'</script>';  
    } 
?>    