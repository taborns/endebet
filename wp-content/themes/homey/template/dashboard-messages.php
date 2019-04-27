<?php
/**
 * Template Name: Dashboard Messages
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url('/') );
}

get_header();
global $current_user, $wpdb, $userID, $homey_threads;

wp_get_current_user();
$userID = $current_user->ID;

$tabel = $wpdb->prefix . 'homey_threads';

$message_query = $wpdb->prepare( 
    "
    SELECT * 
    FROM $tabel 
    WHERE sender_id = %d OR receiver_id = %d
    ORDER BY seen ASC
    ", 
    $userID,
    $userID
);

$homey_threads = $wpdb->get_results( $message_query );
?>


<section id="body-area">

    <div class="dashboard-page-title">
        <h1><?php the_title(); ?></h1>
    </div><!-- .dashboard-page-title -->

    <?php get_template_part('template-parts/dashboard/side-menu'); ?>

    <div class="user-dashboard-right dashboard-without-sidebar">
        <div class="dashboard-content-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="dashboard-area">

                            <?php 
                            if(isset($_GET['message']) && $_GET['message'] == 'new') { 
                                get_template_part('template-parts/dashboard/messages/new');
                            } else { ?>

                                <?php if ( isset( $_REQUEST['thread_id'] ) && !empty( $_REQUEST['thread_id'] ) ) { 
                                    get_template_part('template-parts/dashboard/messages/detail');

                                } else {
                                ?>        
                                    <div class="block">
                                        <div class="block-title">
                                            <div class="block-left">
                                                <h2 class="title"><?php echo esc_html__('From', 'homey'); ?></h2>
                                            </div>
                                        </div>

                                        <?php if ( sizeof( $homey_threads ) != 0 ) { ?>
                                        <div class="table-block dashboard-message-table">
                                            <?php get_template_part('template-parts/dashboard/messages/messages');  ?>
                                        </div><!-- .table-block -->
                                        <?php } else { ?>
                                        <div class="block-body">
                                            <?php esc_html_e('You don’t have any message at this moment.', 'homey'); ?>
                                        </div>
                                        <?php } ?>
                                    </div><!-- .block -->
                                <?php    
                                }
                            }
                            ?>
                        </div><!-- .dashboard-area -->
                    </div><!-- col-lg-12 col-md-12 col-sm-12 -->
                </div>
            </div><!-- .container-fluid -->
        </div><!-- .dashboard-content-area -->    
    </div><!-- .user-dashboard-right -->

</section><!-- #body-area -->


<?php get_footer();?>