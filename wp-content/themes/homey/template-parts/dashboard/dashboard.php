<?php
global $wpdb, $current_user, $userID, $homey_local, $homey_threads;

wp_get_current_user();
$userID = $current_user->ID;

$reservation_page = homey_get_template_link_dash('template/dashboard-reservations.php');
$messages_page = homey_get_template_link_dash('template/dashboard-messages.php');
$author = homey_get_author_by_id('100', '100', 'img-circle', $userID);
$user_post_count = count_user_posts( $userID , 'listing' );

$tabel = $wpdb->prefix . 'homey_threads';
$message_query = $wpdb->prepare( 
    "
    SELECT * 
    FROM $tabel 
    WHERE sender_id = %d OR receiver_id = %d
    ORDER BY seen ASC LIMIT 5
    ", 
    $userID,
    $userID
);

$homey_threads = $wpdb->get_results( $message_query );

$is_renter = false;
if(homey_is_renter()) {
    $is_renter = true;
}
?>
<div class="user-dashboard-right dashboard-without-sidebar">
    <div class="dashboard-content-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-area">
        
                        <div class="block">

                            <div class="block-head text-center">
                                <h2 class="title">
                                    <?php echo esc_attr($homey_local['welcome_back_text']); ?> <?php echo esc_attr($author['name']); ?>        
                                </h2>
                            </div>

                            <?php if(!homey_is_renter()) { ?>
                            <div class="block-verify">
                                <div class="block-col block-col-50">
                                    <div class="block-icon text-secondary">
                                        <i class="fa fa-home"></i>
                                    </div>
                                    <p>
                                        <?php echo esc_attr($homey_local['pr_listing_label']); ?> <strong>
                                        <?php echo esc_attr($author['listing_count']); ?></strong>
                                    </p>

                                </div>
                                
                                <div class="block-col block-col-50">
                                    <div class="block-icon text-secondary">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <p>
                                        <?php echo esc_attr($homey_local['pr_resv_label']); ?> 
                                        <strong><?php echo homey_reservation_count($userID); ?></strong>
                                    </p>

                                </div>
                            </div>
                            <?php } ?>

                        </div><!-- .block -->
                        

                        <div class="block">
                            <div class="block-title">
                                <div class="block-left">
                                    <h2 class="title">
                                        <?php 
                                        if($is_renter) {
                                            echo esc_attr($homey_local['my_resv']);
                                        } else {
                                            echo esc_attr($homey_local['upcoming_resv']); 
                                        }
                                        ?>
                                    </h2>
                                </div>

                                <?php if(!empty($reservation_page)) { ?>
                                <div class="block-right">
                                    <a href="<?php echo esc_url($reservation_page); ?>" class="block-link pull-right">
                                        <?php echo esc_attr($homey_local['view_all_label']); ?> 
                                        <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <?php } ?>

                            </div>
                            
                            <?php 
                            $args = array(
                                'post_type'        =>  'homey_reservation',
                                'posts_per_page'   => 5,
                            );

                            if( $is_renter ) {
                                $meta_query[] = array(
                                    'key' => 'listing_renter',
                                    'value' => $userID,
                                    'compare' => '='
                                );
                                $args['meta_query'] = $meta_query;

                            } else {
                                $meta_query[] = array(
                                    'key' => 'listing_owner',
                                    'value' => $userID,
                                    'compare' => '='
                                );

                                $meta_query[] = array(
                                    'key' => 'reservation_status',
                                    'value' => 'under_review',
                                    'compare' => '='
                                );

                                $meta_count = count($meta_query);
                                if( $meta_count > 1 ) {
                                    $meta_query['relation'] = 'AND';
                                }
                                
                                $args['meta_query'] = $meta_query;
                            }

                            $res_query = new WP_Query($args);
                            
                            
                            if( $res_query->have_posts() ): ?>
                            <div class="table-block dashboard-reservation-table dashboard-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th><?php echo esc_attr($homey_local['id_label']); ?></th>
                                            <th><?php echo esc_attr($homey_local['status_label']); ?></th>
                                            <th><?php echo esc_attr($homey_local['date_label']); ?></th>
                                            <th><?php echo esc_attr($homey_local['address']); ?></th>
                                            <th><?php echo esc_attr($homey_local['check_in']); ?></th>
                                            <th><?php echo esc_attr($homey_local['check_out']); ?></th>
                                            <th><?php echo homey_option('glc_guests_label');?></th>
                                            <th><?php echo esc_attr($homey_local['pets_label']);?></th>
                                            <th><?php echo esc_attr($homey_local['subtotal_label']); ?></th>
                                            <th><?php echo esc_attr($homey_local['actions_label']); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($res_query->have_posts()): $res_query->the_post(); 
                                            
                                            get_template_part('template-parts/dashboard/reservation/item');

                                        endwhile;
                                        wp_reset_postdata();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: 
                                    echo '<div class="block-body">';
                                    echo esc_attr($homey_local['upcoming_reservation_not_found']);  
                                    echo '</div>'; 
                            endif;  
                            ?>
                        </div><!-- .block -->

                        <div class="block">
                            <div class="block-title">
                                <div class="block-left">
                                    <h2 class="title"><?php echo esc_attr($homey_local['recent_msg']); ?></h2>
                                </div>
                                <div class="block-right">
                                    <a href="<?php echo esc_url($messages_page); ?>" class="block-link pull-right"><?php echo esc_attr($homey_local['view_all_label']); ?> <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            <div class="table-block dashboard-message-table">
                                <?php get_template_part('template-parts/dashboard/messages/messages'); ?>
                            </div>
                        </div><!-- .block -->

                    </div><!-- .dashboard-area -->
                </div><!-- col-lg-12 col-md-12 col-sm-12 -->
            </div> <!-- .row -->
        </div><!-- .container-fluid -->
    </div><!-- .dashboard-content-area -->    
</div><!-- .user-dashboard-right -->