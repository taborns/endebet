<?php
/**
 * Template Name: Dashboard
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url('/') );
}

get_header();

global $homey_local, $homey_prefix;

$upgrade_featured = false;
$featured_success = false;

if(isset($_GET['page']) && $_GET['page'] == 'upgrade_featured') {
    $upgrade_featured = true;

}elseif(isset($_GET['page']) && $_GET['page'] == 'featured_success') {
    $featured_success = true;
}

?>


<section id="body-area">

    <div class="dashboard-page-title">
        <h1>
        <?php 
        if($upgrade_featured) {
            echo esc_html__('Upgrade to featured', 'homey'); 
        } elseif($featured_success) {
            echo esc_html__('Payment Received', 'homey');
        } else {
            the_title();
        } 
        ?>
        </h1>
    </div><!-- .dashboard-page-title -->

    <?php get_template_part('template-parts/dashboard/side-menu'); ?>

    <?php
    if($upgrade_featured) {
        get_template_part('template-parts/dashboard/upgrade-featured');

    } elseif($featured_success) {
        get_template_part('template-parts/dashboard/featured-success');

    } else {
        get_template_part('template-parts/dashboard/dashboard'); 
    }
    ?>

</section><!-- #body-area -->


<?php get_footer();?>