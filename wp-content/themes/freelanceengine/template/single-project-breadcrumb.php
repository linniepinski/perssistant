<section class="breadcrumb-wrapper">
        <div class="breadcrumb-single-site">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <ol class="breadcrumb">
                            <li><a href="<?php echo home_url(); ?>"><?php _e('Home',ET_DOMAIN); ?></a></li>
                            <?php 
                            $first_tax = array();
                            $terms     = get_the_terms( get_the_ID(), 'project_category' );
                            if( !empty($terms) && !is_wp_error( $terms ) ){
                                foreach ($terms as $key => $term) {
                                    if($term->parent == 0){
                                        $first_tax = $term;                         
                                        continue;
                                    }
                                }
                            }
                            //<a href="'.get_term_link($first_tax->term_id,'project_category').'"> '
                            if( !empty($first_tax) ){
                                echo '<li>';
                                echo '<a href="'.get_term_link( $first_tax->term_id, 'project_category' ).'">';
                                echo $first_tax->name;
                                echo '</a>';
                                echo '</li>';
                            }
                            
                            echo '<li class="active">';
                            the_title();
                            echo '</li>';

                            ?>
                        </ol>
                    </div>
                    <?php /* 
                    <div class="col-md-6 col-xs-4 prj-next-link">
                        <?php next_post_link( '%link', __('Next project <i class="fa fa-angle-double-right"></i>',ET_DOMAIN) ) ; ?>                        
                    </div>
                    */ ?>
                </div>
            </div>
        </div>
    </section>