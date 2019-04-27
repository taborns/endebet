<?php
global $current_user, $post, $homey_local;
$current_user = wp_get_current_user();
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
<div class="user-dashboard-left white-bg">
    <div class="navi">
        <ul class="board-panel-menu">
            <?php 
            if( !empty($dashboard) ) {
                echo '<li>
                        <a href="'.esc_url($dashboard).'" data-toggle="tooltip" data-placement="right" title="'.$homey_local['m_dashboard_label'].'">
                            <i class="fa fa-home"></i>
                        </a>
                    </li>';
            }

            if( !empty($dashboard_profile) ) {
                echo '<li>
                    <a href="'.esc_url($dashboard_profile).'" data-toggle="tooltip" data-placement="right" title="'.$homey_local['m_profile_label'].'">
                        <i class="fa fa-user-o"></i>
                    </a>
                </li>';
                
            }

            if(!homey_is_renter()) {
                if( !empty($dashboard_listings) ) {
                    echo '<li>
                        <a href="'.esc_url($dashboard_listings).'" data-toggle="tooltip" data-placement="right" title="'.$homey_local['m_listings_label'].'"><i class="fa fa-th-list"></i></a>
                    </li>';
                }

                if( !empty($dashboard_add_listing) ) {
                    echo '<li>
                        <a href="'.esc_url($dashboard_add_listing).'" data-toggle="tooltip" data-placement="right" title="'.$homey_local['m_add_listing_label'].'"><i class="fa fa-plus-circle"></i></a>
                    </li>';
                }
            }

            if( !empty($dashboard_reservations) ) {
                echo '<li>
                    <a href="'.esc_url($dashboard_reservations).'" data-toggle="tooltip" data-placement="right" title="'.$homey_local['m_reservation_label'].'"><i class="fa fa-calendar"></i></a>
                </li>';
            }

            if( !empty($dashboard_messages) ) {
                echo '<li>
                    <a href="'.esc_url($dashboard_messages).'" data-toggle="tooltip" data-placement="right" title="'.$homey_local['m_messages_label'].'"><i class="fa fa-comments-o"></i></a>
                </li>';
            }

            if( !empty($dashboard_invoices) ) {
                echo '<li>
                    <a href="'.esc_url($dashboard_invoices).'" data-toggle="tooltip" data-placement="right" title="'.$homey_local['m_invoices_label'].'"><i class="fa fa-file"></i></a>
                </li>';
            }

            if( !empty($dashboard_favorites) ) {
                echo '<li>
                    <a href="'.esc_url($dashboard_favorites).'" data-toggle="tooltip" data-placement="right" title="'.$homey_local['m_favorites_label'].'"><i class="fa fa-heart-o"></i></a>
                </li>';
            }


            echo '<li>
                <a href="' . wp_logout_url(home_url('/')) . '" data-toggle="tooltip" data-placement="right" title="'.$homey_local['m_logout_label'].'"><i class="fa fa-sign-out"></i></a>
            </li>';
            ?>
            
        </ul>
    </div>
</div>