<?php
/**
 * Template Name: Dashboard Listing Submitted
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url('/') );
}
get_header();

global $current_user, $homey_local;

wp_get_current_user();
$userID = $current_user->ID;

$user_email = $current_user->user_email;
$admin_email =  get_bloginfo('admin_email');
$panel_class = $calendar_link = '';
$dashboard_add_new = homey_get_template_link('template/dashboard-submission.php');

$dashboard = homey_get_template_link('template/dashboard.php');

if(isset($_GET['listing_id']) && !empty($_GET['listing_id'])) {
   $calendar_link  = add_query_arg( array(
        'edit_listing' => $_GET['listing_id'],
        'tab' => 'calendar',
    ), $dashboard_add_new );

   $pricing_link  = add_query_arg( array(
        'edit_listing' => $_GET['listing_id'],
        'tab' => 'pricing-tab',
    ), $dashboard_add_new );

   $upgrade_link  = add_query_arg( array(
        'page' => 'upgrade_featured',
        'upgrade_id' => $_GET['listing_id'],
    ), $dashboard );
} else {

}

$update_cal_title = homey_option('update_cal_title');
$update_cal_des = homey_option('update_cal_des');
$custom_prices_title = homey_option('custom_prices_title');
$custom_prices_des = homey_option('custom_prices_des');
$update_featured_title = homey_option('update_featured_title');
$update_featured_des = homey_option('update_featured_des');
?>


<section id="body-area">

    <div class="dashboard-page-title">
        <h1><?php the_title(); ?></h1>
    </div><!-- .dashboard-page-title -->

    <?php get_template_part('template-parts/dashboard/side-menu'); ?>

    <div class="user-dashboard-right dashboard-with-sidebar">
            <div class="dashboard-content-area">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="dashboard-area">
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="close" data-hide="alert" aria-label="Close"><i class="fa fa-close"></i></button>
                                    <?php echo esc_attr($homey_local['list_submit_msg']); ?>
                                </div>
                                <div class="block">
                                    <div class="block-title">
                                        <div class="block-left">
                                            <h2 class="title"><?php echo esc_attr($homey_local['complete_list_label']); ?></h2>
                                        </div>
                                        <!-- block-left -->
                                    </div>
                                    <div class="block-body">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                                <h3><?php echo esc_attr($update_cal_title); ?></h3>
                                                <p><?php echo esc_attr($update_cal_des); ?></p>

                                                <?php if(!empty($calendar_link)) { ?>
                                                <p><a class="btn btn-slim btn-primary" href="<?php echo esc_url($calendar_link); ?>"><?php esc_html_e('Update calendar', 'homey'); ?></a></p>
                                                <?php } ?>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                                <h3><?php echo esc_attr($custom_prices_title); ?></h3>
                                                <p><?php echo esc_attr($custom_prices_des); ?></p>

                                                <?php if(!empty($pricing_link)) {?>
                                                <p><a class="btn btn-slim btn-primary" href="<?php echo esc_url($pricing_link);?>"><?php esc_html_e('Setup Custom Prices', 'homey'); ?></a></p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                                <h3><?php echo esc_attr($update_featured_title); ?></h3>
                                                <p><?php echo esc_attr($update_featured_des); ?></p>

                                                <?php if(!empty($upgrade_link)) {?>
                                                <p><a class="btn btn-slim btn-primary" href="<?php echo esc_url($upgrade_link);?>"><?php esc_html_e('Upgrade to featured', 'homey'); ?></a></p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- block-body -->
                                </div>
                                <!-- block -->
                            </div>
                            <!-- .dashboard-area -->
                        </div>
                        <!-- col-lg-12 col-md-12 col-sm-12 -->
                    </div>
                </div>
                <!-- .container-fluid -->
            </div>
            <!-- .dashboard-content-area -->    
            <aside class="dashboard-sidebar">
                <?php get_template_part('template-parts/dashboard/sidebar-listing');?>
            </aside>
            <!-- .dashboard-sidebar -->
        </div>

</section><!-- #body-area -->


<?php get_footer();?>