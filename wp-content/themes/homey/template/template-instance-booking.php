<?php
/**
 * Template Name: Instance Booking
 */ 
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url('/') );
}
get_header();

global $post, $current_user, $homey_prefix, $homey_local;
$current_user = wp_get_current_user();
$userID       = $current_user->ID;

$owner_name = $owner_pic_escaped = $owner_languages = '';

$listing_id = isset($_GET['listing_id']) ? $_GET['listing_id'] : '';
$guests = isset($_GET['guest']) ? $_GET['guest'] : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';

if(!empty($listing_id)) {
    $listing_owner_id  =  get_post_field( 'post_author', $listing_id );
    
    $listing_owner = homey_get_author_by_id($w = '70', $h = '70', $classes = 'img-responsive img-circle', $listing_owner_id);

    $owner_pic_escaped = $listing_owner['photo'];
    $owner_name = $listing_owner['name'];
    $owner_languages = $listing_owner['languages'];
}

$check_availability = check_booking_availability($check_in, $check_out, $listing_id, $guests);
$is_available = $check_availability['success'];
$check_message = $check_availability['message'];


$smoke     = get_post_meta($listing_id, $homey_prefix.'smoke', true);
$pets   = get_post_meta($listing_id, $homey_prefix.'pets', true);
$party       = get_post_meta($listing_id, $homey_prefix.'party', true);
$children      = get_post_meta($listing_id, $homey_prefix.'children', true);
$additional_rules       = get_post_meta($listing_id, $homey_prefix.'additional_rules', true);
$cancellation_policy       = get_post_meta($listing_id, $homey_prefix.'cancellation_policy', true);

if($smoke != 1) {
    $smoke_allow = 'fa fa-times'; 
    $smoke_text = $homey_local['text_no'];
} else {
    $smoke_allow = 'fa fa-check';
    $smoke_text = $homey_local['text_yes'];
}

if($pets != 1) {
    $pets_allow = 'fa fa-times';
    $pets_text = $homey_local['text_no'];
} else {
    $pets_allow = 'fa fa-check';
    $pets_text = $homey_local['text_yes'];
}

if($party != 1) {
    $party_allow = 'fa fa-times'; 
    $party_text = $homey_local['text_no'];
} else {
    $party_allow = 'fa fa-check';
    $party_text = $homey_local['text_yes'];
}

if($children != 1) {
    $children_allow = 'fa fa-times';
    $children_text = $homey_local['text_no'];
} else {
    $children_allow = 'fa fa-check';
    $children_text = $homey_local['text_yes'];
}

$booking_proceed = true;
if($check_out <= $check_in) {
    $booking_proceed = false;
}

$terms_conditions = homey_option('payment_terms_condition');
$privacy_policy = homey_option('payment_privacy_policy');
$ins_learnmore = homey_option('ins_learnmore');

$enable_paypal = homey_option('enable_paypal');
$enable_stripe = homey_option('enable_stripe');
$stripe_processor_link = homey_get_template_link('template/template-stripe-charge.php');
?>

<section class="main-content-area booking-page">
    <?php
    if(!$is_available || empty($listing_id)) { ?>

        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                    <?php
                    if(!$is_available) {
                        $ins_warning = $check_message;

                    } elseif(empty($listing_id)) {
                        $ins_warning = $homey_local['ins_no_listing'];
                    }
                    ?>

                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-exclamation-circle"></i> <?php echo esc_html($ins_warning); ?>
                    </div>
                    <a href="<?php echo get_permalink($listing_id); ?>" class="btn btn-primary"><?php echo esc_attr($homey_local['continue_btn']); ?></a>
                </div><!-- col-xs-12 col-sm-12 col-md-12 col-lg-12 -->
            </div><!-- .row -->
        </div><!-- .container -->

    <?php    
    } else {
    ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="page-title text-center">
                        <div class="block-top-title">
                            <h1 class="listing-title"><?php echo homey_option('ins_page_title'); ?></h1>
                            <p><?php echo homey_option('ins_page_subtitle'); ?> 
                            
                            <?php if(!empty($ins_learnmore)) { ?>
                            <a target="_blank" href="<?php echo esc_url($ins_learnmore); ?>">
                                <?php echo esc_attr($homey_local['learnmore']); ?>        
                            </a>
                            <?php } ?>
                            </p>
                        </div><!-- block-top-title -->
                    </div><!-- page-title -->
                    <div class="alert alert-info" role="alert">
                        <i class="fa fa-clock-o"></i> <?php echo esc_attr($homey_local['ins_notic']); ?>
                    </div>
                </div><!-- col-xs-12 col-sm-12 col-md-12 col-lg-12 -->
            </div><!-- .row -->
        </div><!-- .container -->

        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 col-md-push-7 col-lg-push-7">
                    
                    <?php get_template_part('single-listing/booking/sidebar-instance-booking'); ?>
                    
                </div>

                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 col-md-pull-5 col-lg-pull-5">

                    <div class="block homey-booking-block-title-1">
                        <div class="block-head table-block">
                            <h2 class="title"><span class="circle-icon">1</span> <?php echo esc_attr($homey_local['start_booking']); ?>
                                <i class="fa fa-check text-success hidden" aria-hidden="true"></i> 
                                <a class="edit-booking-form hidden" href=""><?php echo esc_attr($homey_local['edit_btn']); ?></a>
                            </h2>
                        </div>
                        
                        <div class="block-body homey-booking-block-body-1">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="first-name"><?php echo esc_attr($homey_local['fname_label']); ?></label>
                                        <input type="text" id="first-name" class="form-control" value="<?php echo esc_attr($current_user->first_name); ?>" placeholder="<?php echo esc_attr($homey_local['fname_plac']); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="last-name"><?php echo esc_attr($homey_local['lname_label']); ?></label>
                                        <input type="text" id="last-name" class="form-control" value="<?php echo esc_attr($current_user->last_name); ?>" placeholder="<?php echo esc_attr($homey_local['lname_plac']); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="phone"><?php echo esc_attr($homey_local['phone_label']); ?></label>
                                        <input type="text" id="phone" class="form-control" value="<?php echo esc_attr($current_user->phone); ?>" placeholder="<?php echo esc_attr($homey_local['phone_plac']); ?>">
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="block-sub-title homey-booking-block-body-1">
                            <h3 class="margin-0"><?php echo esc_attr($homey_local['intro_to_host']); ?></h3>
                        </div>
                        <div class="block-body homey-booking-block-body-1">
                            <div class="msg-send-block">
                                <div class="msg-send-block-host-avatar clearfix">
                                    <?php 
                                    // This variable has been safely escaped in the following file: homey/framework/functions/profile.php  Line: 509
                                    if(!empty($owner_pic_escaped)){ echo $owner_pic_escaped; } ?>
                                    <div class="msg-send-block-host-info">
                                        <h4><?php echo esc_attr($owner_name); ?></h4>

                                        <?php if(!empty($owner_languages)) { ?>
                                        <p><?php echo esc_attr($homey_local['speaks_label']); ?>: <?php echo esc_attr($owner_languages); ?></p>
                                        <?php } ?>
                                    </div>
                                </div>
                                <form class="form-msg">
                                    <div class="msg-type-block">
                                        <textarea name="renter_message" id="renter_message" class="form-control" rows="5" placeholder="<?php echo esc_attr($homey_local['message_plac']); ?>"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="continue-block-button">
                                <button type="button" class="btn homey-booking-step-1 btn-booking btn-full-width"><?php echo esc_attr($homey_local['continue_btn']); ?></button>
                                <p class="text-center mb-0 instance-term-condition">
                                    <?php
                                    

                                    echo sprintf( wp_kses(__( 'By clicking Continue you agree to our <a href="%s">Terms & Conditions</a> and <a href="%s">Privacy Policy</a>', 'homey' ), homey_allowed_html()), get_permalink($terms_conditions), get_permalink($privacy_policy) );
                                    ?>
                                    
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="block homey-booking-block-title-2 inactive mb-0">
                        <div class="block-head table-block">
                            <h2 class="title"><span class="circle-icon">2</span> <?php echo esc_attr($homey_local['rules_policies']); ?>
                                <i class="fa fa-check text-success hidden" aria-hidden="true"></i> 
                                <a class="edit-booking-form hidden" href=""><?php echo esc_attr($homey_local['edit_btn']); ?></a>
                            </h2>
                        </div>
                        <div class="block-body homey-booking-block-body-2" style="display: none;">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?php if(!empty($cancellation_policy)) { ?>
                                        <h3><?php echo esc_attr($homey_local['cancel_policy']); ?></h3>
                                        <p><?php echo esc_html($cancellation_policy); ?></p>
                                    <?php } ?>

                                    <h3><?php echo esc_attr($homey_local['owner_rules']); ?></h3>
                                    <ul class="list-unstyled rules-options">
                                        <li>
                                            <i class="<?php echo esc_attr($smoke_allow); ?>" aria-hidden="true"></i> 
                                            <?php echo esc_attr($homey_local['smoking_allowed']); ?>:
                                            <span><?php echo esc_attr($smoke_text); ?></span>
                                        </li>                    
                                        <li>
                                            <i class="<?php echo esc_attr($pets_allow); ?>" aria-hidden="true"></i> 
                                            <?php echo esc_attr($homey_local['pets_allowed']); ?>:
                                            <span><?php echo esc_attr($pets_text); ?></span>
                                        </li>
                                        <li>
                                            <i class="<?php echo esc_attr($party_allow); ?>" aria-hidden="true"></i> 
                                            <?php echo esc_attr($homey_local['party_allowed']); ?>:
                                            <span><?php echo esc_attr($party_text); ?></span>
                                        </li>
                                        <li>
                                            <i class="<?php echo esc_attr($children_allow); ?>" aria-hidden="true"></i> 
                                            <?php echo esc_attr($homey_local['children_allowed']); ?>:
                                            <span><?php echo esc_attr($children_text); ?></span>
                                        </li>
                                    </ul>

                                    <?php if( !empty($additional_rules)) { ?>
                                    
                                        <p><?php echo esc_attr($additional_rules); ?></p>
                                    
                                    <?php } ?>
                                
                                    
                                    <div class="well">
                                        <!-- the div here below must be hidden and visible only if users doesn't check the policy agreement -->
                                        <div style="display: none;" class="mb-10"><i class="fa fa-exclamation-circle text-danger" aria-hidden="true"></i> <?php echo esc_attr($homey_local['agr_policy']); ?></div>
                                        
                                        <label class="control control--checkbox mb-0">
                                            <input type="checkbox" name="agreement">
                                            <span class="contro-text"><?php echo esc_attr($homey_local['read_agr_text']); ?></span>
                                            <span class="control__indicator"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="continue-block-button">
                                <button type="button" class="btn homey-booking-step-2 btn-booking btn-full-width"><?php echo esc_attr($homey_local['continue_btn']); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="block homey-booking-block-title-3 inactive mb-0">
                        <div class="block-head table-block">
                            <h2 class="title"><span class="circle-icon">3</span> <?php echo esc_attr($homey_local['payment_label']); ?></h2>
                        </div>
                        <div class="block-body homey-booking-block-body-3" style="display: none;">
                            <form name="homey_checkout" method="post" class="homey_payment_form" action="<?php echo esc_url($stripe_processor_link); ?>">
                                <div class="row">

                                    <div class="col-sm-12">
                                        <h3><?php echo esc_attr($homey_local['select_payment']); ?></h3>
                                        <div class="payment-method">
                                            <?php if( $enable_paypal != 0 ) { ?>
                                            <div class="payment-method-block paypal-method">
                                                <div class="form-group">
                                                    <label class="control control--radio radio-tab">
                                                        <input name="payment_gateway" value="paypal" type="radio">
                                                        <span class="control-text"><?php esc_html_e('Paypal', 'homey'); ?></span>
                                                        <span class="control__indicator"></span>
                                                        <span class="radio-tab-inner"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <?php } ?>

                                            <?php if( $enable_stripe != 0 ) { ?>
                                            <div class="payment-method-block stripe-method">
                                                <div class="form-group">
                                                    <label class="control control--radio radio-tab">
                                                        <input name="payment_gateway" value="stripe" type="radio">
                                                        <span class="control-text"><?php esc_html_e('Stripe', 'homey'); ?></span>
                                                        <span class="control__indicator"></span>
                                                        <span class="radio-tab-inner"></span>
                                                    </label>
                                                    <?php homey_stripe_payment_instance($listing_id, $check_in, $check_out, $guests); ?>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="continue-block-button">
                                    <input type="hidden" id="check_in_date" value="<?php echo esc_attr($check_in); ?>">
                                    <input type="hidden" id="check_out_date" value="<?php echo esc_attr($check_out); ?>">
                                    <input type="hidden" id="guests" value="<?php echo esc_html($guests); ?>">
                                    <input type="hidden" id="listing_id" value="<?php echo intval($listing_id); ?>">
                                    <input type="hidden" name="checkout-security" id="checkout-security" value="<?php echo wp_create_nonce('checkout-security-nonce'); ?>"/>
                                    <button id="make_instance_booking_payment" type="button" class="btn btn-booking btn-full-width"><?php echo esc_attr($homey_local['btn_process_pay']); ?></button>
                                    <div id="instance_noti"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                

            </div><!-- .row -->

        </div><!-- .container -->
    <?php } ?> <!-- end check availability if -->

</section><!-- main-content-area -->

<?php get_footer(); ?>