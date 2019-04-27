<?php
global $current_user, $homey_local;
wp_get_current_user();
$userID  =  $current_user->ID;
$first_name  =  $current_user->first_name;
$last_name  =  $current_user->last_name;
$display_name = $current_user->display_name;


$homey_author = homey_get_author_by_id('36', '36', 'img-circle', $userID);
$author_photo = $homey_author['photo'];


$dashboard = homey_get_template_link_dash('template/dashboard.php');
$dashboard_profile = homey_get_template_link_dash('template/dashboard-profile.php');
$dashboard_listings = homey_get_template_link_dash('template/dashboard-listings.php');
$dashboard_add_listing = homey_get_template_link_dash('template/dashboard-submission.php');
$dashboard_favorites = homey_get_template_link_dash('template/dashboard-favorites.php');
$dashboard_search = homey_get_template_link_dash('template/dashboard-saved-searches.php');
$dashboard_reservations = homey_get_template_link_dash('template/dashboard-reservations.php');
$dashboard_messages = homey_get_template_link_dash('template/dashboard-messages.php');
$dashboard_invoices = homey_get_template_link_dash('template/dashboard-invoices.php');
$home_link = home_url('/');

?>
<div class="account-loggedin">
    <?php echo esc_html($display_name); ?>

    <span class="user-image">
        <?php echo ''.$author_photo; ?>
    </span>
    <div class="account-dropdown">
        <ul>
            <?php 
            if( !empty($dashboard) ) {
                echo '<li>
                        <a href="'.esc_url($dashboard).'">
                            <i class="fa fa-home"></i> '.$homey_local['m_dashboard_label'].'
                        </a>
                    </li>';
            }

            if( !empty($dashboard_profile) ) {
                echo '<li>
                    <a href="'.esc_url($dashboard_profile).'">
                        <i class="fa fa-user-o"></i> '.$homey_local['m_profile_label'].'
                    </a>
                </li>';
                
            }

            if(!homey_is_renter()) {
                if( !empty($dashboard_listings) ) {
                    echo '<li>
                        <a href="'.esc_url($dashboard_listings).'"><i class="fa fa-th-list"></i> '.$homey_local['m_listings_label'].'</a>
                    </li>';
                }

                if( !empty($dashboard_add_listing) ) {
                    echo '<li>
                        <a href="'.esc_url($dashboard_add_listing).'"><i class="fa fa-plus-circle"></i> '.$homey_local['m_add_listing_label'].' </a>
                    </li>';
                }
            }

            if( !empty($dashboard_reservations) ) {
                echo '<li>
                    <a href="'.esc_url($dashboard_reservations).'"><i class="fa fa-calendar"></i> '.$homey_local['m_reservation_label'].' </a>
                </li>';
            }

            if( !empty($dashboard_favorites) ) {
                echo '<li>
                    <a href="'.esc_url($dashboard_favorites).'"><i class="fa fa-heart-o"></i> '.$homey_local['m_favorites_label'].' </a>
                </li>';
            }

            if( !empty($dashboard_invoices) ) {
                echo '<li>
                    <a href="'.esc_url($dashboard_invoices).'"><i class="fa fa-file"></i> '.$homey_local['m_invoices_label'].' </a>
                </li>';
            }


            if( !empty($dashboard_messages) ) {
                echo '<li>
                    <a href="'.esc_url($dashboard_messages).'"><i class="fa fa-comments-o"></i> '.$homey_local['m_messages_label'].'</a>
                </li>';
            }

            echo '<li>
                <a href="' . wp_logout_url(home_url('/')) . '"><i class="fa fa-sign-out"></i> '.$homey_local['m_logout_label'].' </a>
            </li>';
            ?>
        </ul>
    </div>
</div>