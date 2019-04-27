<?php 
global $post, $current_user, $homey_prefix, $homey_local;
wp_get_current_user();

$listing_id = $post->ID;
$price_per_night = get_post_meta($listing_id, $homey_prefix.'night_price', true);
$instant_booking = get_post_meta($listing_id, $homey_prefix.'instant_booking', true);

$key = '';
$userID      =   $current_user->ID;
$fav_option = 'homey_favorites-'.$userID;
$fav_option = get_option( $fav_option );
if( !empty($fav_option) ) {
    $key = array_search($post->ID, $fav_option);
}

$price_separator = homey_option('currency_separator');

if( $key != false || $key != '' ) {
    $favorite = $homey_local['remove_favorite'];
    $heart = 'fa-heart';
} else {
    $favorite = $homey_local['add_favorite'];
    $heart = 'fa-heart-o';
}
?>
<div id="homey_remove_on_mobile" class="sidebar-booking-module hidden-sm hidden-xs">
	<div class="block">
		<div class="sidebar-booking-module-header">
			<div class="block-body-sidebar">
				
					<?php 
					if(!empty($price_per_night)) { ?>

					<span class="item-price">
					<?php 	
					echo homey_formatted_price($price_per_night, true, true); ?><sub><?php echo esc_attr($price_separator); ?><?php echo esc_attr(homey_option('glc_day_night_label'));?></sub>
					</span>

					<?php } else { 
						echo '<span class="item-price free">'.esc_html__('Free', 'homey').'</span>';
					}?>
				
			</div><!-- block-body-sidebar -->
		</div><!-- sidebar-booking-module-header -->
		<div class="sidebar-booking-module-body">
			<div class="homey_notification block-body-sidebar">

				<div id="single-listing-date-range" class="search-date-range">
					<div class="search-date-range-arrive">
						<input name="arrive" readonly id="check_in_date" type="text" class="form-control" autocomplete="off" placeholder="<?php echo esc_attr(homey_option('srh_arrive_label')); ?>">
					</div>
					<div class="search-date-range-depart">
						<input name="depart" readonly id="check_out_date" type="text" class="form-control" autocomplete="off" placeholder="<?php echo esc_attr(homey_option('srh_depart_label')); ?>">
					</div>
					
					<div id="single-booking-search-calendar" class="search-calendar single-listing-booking-calendar-js clearfix" style="display: none;">
						<?php homeyAvailabilityCalendar(); ?>

						<div class="calendar-navigation custom-actions">
	                        <button class="listing-cal-prev btn btn-action pull-left disabled"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
	                        <button class="listing-cal-next btn btn-action pull-right"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
	                    </div><!-- calendar-navigation -->	                
					</div>
				</div>
				
				<?php get_template_part('single-listing/booking/guests'); ?>
				
				<div class="homey_preloader">
					<?php get_template_part('template-parts/spinner'); ?>
				</div>				
				<div id="homey_booking_cost" class="payment-list"></div>	

				<input type="hidden" name="listing_id" id="listing_id" value="<?php echo intval($listing_id); ?>">
				<input type="hidden" name="reservation-security" id="reservation-security" value="<?php echo wp_create_nonce('reservation-security-nonce'); ?>"/>
				
				<?php if($instant_booking) { ?>
					<button id="instance_reservation" type="button" class="btn btn-full-width btn-primary"><?php echo esc_html__('Instant Booking', 'homey'); ?></button>
				<?php } else { ?> 
					<button id="request_for_reservation" type="button" class="btn btn-full-width btn-primary"><?php echo esc_html__('Request to Book', 'homey'); ?></button>
					<div class="text-center text-small"><i class="fa fa-info-circle"></i> <?php echo esc_html__('You won’t be charged yet', 'homey'); ?></div>
				<?php } ?>
				
			</div><!-- block-body-sidebar -->
		</div><!-- sidebar-booking-module-body -->
		
	</div><!-- block -->
</div><!-- sidebar-booking-module -->
<div class="sidebar-booking-module-footer">
	<div class="block-body-sidebar">

		<?php if(homey_option('detail_favorite') != 0) { ?>
		<button type="button" data-listid="<?php echo intval($post->ID); ?>" class="add_fav btn btn-full-width btn-grey-outlined"><i class="fa <?php echo esc_attr($heart); ?>" aria-hidden="true"></i> <?php echo esc_attr($favorite); ?></button>
		<?php } ?>
		
		<?php if(homey_option('detail_contact_form') != 0) { ?>
		<button type="button" data-toggle="modal" data-target="#modal-contact-host" class="btn btn-full-width btn-grey-outlined"><?php echo esc_attr($homey_local['pr_cont_host']); ?></button>
		<?php } ?>
		
		<?php if(homey_option('print_button') != 0) { ?>
		<button type="button" id="homey-print" class="btn btn-full-width btn-blank" data-listing-id="<?php echo intval($listing_id);?>">
			<i class="fa fa-print" aria-hidden="true"></i> <?php echo esc_attr($homey_local['print_label']); ?>
		</button>
		<?php } ?>
	</div><!-- block-body-sidebar -->
	
	<?php 
	if(homey_option('detail_share') != 0) {
		get_template_part('single-listing/share'); 
	}
	?>
</div><!-- sidebar-booking-module-footer -->
