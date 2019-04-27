<?php
global $homey_prefix, $hide_fields, $homey_local, $listing_data, $listing_meta_data;

$instant_booking = homey_get_field_meta('instant_booking');
$night_price = homey_get_field_meta('night_price');
$weekends_price = homey_get_field_meta('weekends_price');
$weekends_days = homey_get_field_meta('weekends_days');
$priceWeek = homey_get_field_meta('priceWeek');
$priceMonthly = homey_get_field_meta('priceMonthly');
$allow_additional_guests = homey_get_field_meta('allow_additional_guests');
$additional_guests_price = homey_get_field_meta('additional_guests_price');
$cleaning_fee = homey_get_field_meta('cleaning_fee');
$cleaning_fee_type = homey_get_field_meta('cleaning_fee_type'); 
$city_fee = homey_get_field_meta('city_fee'); 
$city_fee_type = homey_get_field_meta('city_fee_type');
$security_deposit = homey_get_field_meta('security_deposit');
$tax_rate = homey_get_field_meta('tax_rate');

$class = '';
if(isset($_GET['tab']) && $_GET['tab'] == 'pricing-tab') {
    $class = 'in active';
}

?>

<div id="pricing-tab" class="tab-pane fade <?php echo esc_attr($class);?>">
    <div class="block-title visible-xs">
        <h3 class="title"><?php echo esc_attr(homey_option('ad_pricing_label'));?></h3>
    </div>
    <div class="block-body">
        <div class="row">
            
            <?php if($hide_fields['instant_booking'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label><?php echo esc_attr(homey_option('ad_ins_booking_label')); ?></label>
                    <label class="control control--checkbox radio-tab"><?php echo esc_attr(homey_option('ad_ins_booking_des')); ?>
                        <input type="checkbox" <?php checked( $instant_booking, 1 ); ?> name="instant_booking" value="1">
                        <span class="control__indicator"></span>
                        <span class="radio-tab-inner"></span>
                    </label>
                </div>
            </div>
            <?php } ?>

            <?php if($hide_fields['night_price'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="night-price"><?php echo esc_attr(homey_option('ad_nightly_label')).homey_req('night_price'); ?></label>
                    <input type="text" name="night_price" value="<?php echo esc_attr($night_price); ?>" class="form-control" <?php homey_required('night_price'); ?> id="night_price" placeholder="<?php echo esc_attr(homey_option('ad_nightly_plac')); ?>">
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="row">

            <?php if($hide_fields['weekends_price'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="weekends_price"><?php echo esc_attr(homey_option('ad_weekends_label')).homey_req('weekends_price'); ?></label>
                    <input type="text" name="weekends_price" value="<?php echo esc_attr($weekends_price); ?>" class="form-control" <?php homey_required('weekends_price'); ?> id="weekends_price" placeholder="<?php echo esc_attr(homey_option('ad_weekends_plac')); ?>">
                </div>
            </div>
            <?php } ?>

            <?php if($hide_fields['weekends_days'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="weekends_days"><?php echo esc_attr(homey_option('ad_weekend_days_label')).homey_req('weekends_days'); ?></label>
                    <select name="weekends_days" class="selectpicker" <?php homey_required('weekends_days'); ?> id="weekends_days" data-live-search="false">
                        <option <?php selected( $weekends_days, 'sat_sun' ); ?> value="sat_sun"><?php echo esc_attr($homey_local['sat_sun_label']); ?></option>
                        <option <?php selected( $weekends_days, 'fri_sat' ); ?> value="fri_sat"><?php echo esc_attr($homey_local['fri_sat_label']); ?></option>
                        <option <?php selected( $weekends_days, 'fri_sat_sun' ); ?> value="fri_sat_sun"><?php echo esc_attr($homey_local['fri_sat_sun_label']); ?></option>
                    </select>
                </div>
            </div>
            <?php } ?>
            
        </div>

        <?php if($hide_fields['priceWeek'] != 1 || $hide_fields['priceMonthly'] != 1) { ?>
        <hr class="row-separator">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <h3 class="sub-title"><?php echo esc_attr(homey_option('ad_long_term_pricing')); ?></h3>
            </div>

            <?php if($hide_fields['priceWeek'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="priceWeek"><?php echo esc_attr(homey_option('ad_weekly7nights')).homey_req('priceWeek'); ?></label>
                    <input type="text" name="priceWeek" value="<?php echo esc_attr($priceWeek); ?>" class="form-control" <?php homey_required('priceWeek'); ?> id="priceWeek" placeholder="<?php echo esc_attr(homey_option('ad_weekly7nights_plac')); ?>">
                </div>
            </div>
            <?php } ?>

            <?php if($hide_fields['priceMonthly'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="priceMonthly"><?php echo esc_attr(homey_option('ad_monthly30nights')).homey_req('priceMonthly'); ?></label>
                    <input type="text" name="priceMonthly" value="<?php echo esc_attr($priceMonthly); ?>" class="form-control" <?php homey_required('priceMonthly'); ?> id="priceMonthly" placeholder="<?php echo esc_attr(homey_option('ad_monthly30nights_plac')); ?>">
                </div>
            </div>
            <?php } ?>

        </div>
        <?php } ?>

        <hr class="row-separator">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <h3 class="sub-title"><?php echo esc_attr(homey_option('ad_add_costs_label')); ?></h3>
            </div>

            <?php if($hide_fields['allow_additional_guests'] != 1) { ?>
            <div class="col-sm-3 col-xs-6">
                <div class="form-group">
                    <label for=""><?php echo esc_attr(homey_option('ad_allow_additional_guests')); ?></label>
                    <label class="control control--radio radio-tab"> 
                        <input type="radio" <?php checked( $allow_additional_guests, 'yes' ); ?> name="allow_additional_guests" value="yes">
                        <span class="control-text"><?php echo esc_attr(homey_option('ad_text_yes')); ?></span>
                        <span class="control__indicator"></span>
                        <span class="radio-tab-inner"></span>
                    </label>
                </div>
            </div>
            <div class="col-sm-3 col-xs-6">
                <div class="form-group">
                    <label for="">&nbsp</label>
                    <label class="control control--radio radio-tab">
                        <input type="radio" name="allow_additional_guests" <?php checked( $allow_additional_guests, 'no' ); ?> value="no">
                        <span class="control-text"><?php echo esc_attr(homey_option('ad_text_no')); ?></span>
                        <span class="control__indicator"></span>
                        <span class="radio-tab-inner"></span>
                    </label>
                </div>
            </div>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="additional_guests_price"><?php echo esc_attr(homey_option('ad_addinal_guests_label')); ?></label>
                    <input type="text" name="additional_guests_price" value="<?php echo esc_attr($additional_guests_price); ?>" class="form-control" id="additional_guests_price" placeholder="<?php echo esc_attr(homey_option('ad_addinal_guests_plac')); ?>">
                </div>
            </div>
            <?php } ?>
        </div>

        <?php if($hide_fields['cleaning_fee'] != 1) { ?>
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <label><?php echo esc_attr(homey_option('ad_cleaning_fee')); ?></label>
            </div>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <input type="text" name="cleaning_fee" value="<?php echo esc_attr($cleaning_fee); ?>" class="form-control" placeholder="<?php echo esc_attr(homey_option('ad_cleaning_fee_plac')); ?>">
                </div>
            </div>
            <div class="col-sm-3 col-xs-6">
                <div class="form-group">
                    <label class="control control--radio radio-tab">
                        <input type="radio" <?php checked( $cleaning_fee_type, 'daily' ); ?> name="cleaning_fee_type" value="daily">
                        <span class="control-text"><?php echo esc_attr(homey_option('ad_daily_text')); ?></span>
                        <span class="control__indicator"></span>
                        <span class="radio-tab-inner"></span>
                    </label>
                </div>
            </div>
            <div class="col-sm-3 col-xs-6">
                <div class="form-group">
                    <label class="control control--radio radio-tab">
                        <input type="radio" <?php checked( $cleaning_fee_type, 'per_stay' ); ?> name="cleaning_fee_type" value="per_stay">
                        <span class="control-text"><?php echo esc_attr(homey_option('ad_perstay_text')); ?></span>
                        <span class="control__indicator"></span>
                        <span class="radio-tab-inner"></span>
                    </label>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($hide_fields['city_fee'] != 1) { ?>
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <label><?php echo esc_attr(homey_option('ad_city_fee')); ?></label>
            </div>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <input type="text" name="city_fee" value="<?php echo esc_attr($city_fee); ?>" class="form-control" placeholder="<?php echo esc_attr(homey_option('ad_city_fee_plac')); ?>">
                </div>
            </div>
            <div class="col-sm-3 col-xs-6">
                <div class="form-group">
                    <label class="control control--radio radio-tab">
                        <input type="radio" <?php checked( $city_fee_type, 'daily' ); ?> name="city_fee_type" value="daily">
                        <span class="control-text"><?php echo esc_attr(homey_option('ad_daily_text')); ?></span>
                        <span class="control__indicator"></span>
                        <span class="radio-tab-inner"></span>
                    </label>
                </div>
            </div>
            <div class="col-sm-3 col-xs-6">
                <div class="form-group">
                    <label class="control control--radio radio-tab">
                        <input type="radio" <?php checked( $city_fee_type, 'per_stay' ); ?> name="city_fee_type" value="per_stay">
                        <span class="control-text"><?php echo esc_attr(homey_option('ad_perstay_text')); ?></span>
                        <span class="control__indicator"></span>
                        <span class="radio-tab-inner"></span>
                    </label>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($hide_fields['security_deposit'] != 1 || $hide_fields['tax_rate'] != 1) { ?>
        <div class="row">

            <?php if($hide_fields['security_deposit'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="security_deposit"><?php echo esc_attr(homey_option('ad_security_deposit_label')); ?></label>
                    <input type="text" name="security_deposit" value="<?php echo esc_attr($security_deposit); ?>" class="form-control" id="security_deposit" placeholder="<?php echo esc_attr(homey_option('ad_security_deposit_plac')); ?>">
                </div>
            </div>
            <?php } ?>

            <?php if($hide_fields['tax_rate'] != 1 && homey_option('tax_type') == 'single_tax') { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="tax_rate"><?php echo esc_attr(homey_option('ad_tax_rate_label')); ?></label>
                    <input type="text" name="tax_rate" value="<?php echo esc_attr($tax_rate); ?>" class="form-control" id="tax_rate" placeholder="<?php echo esc_attr(homey_option('ad_tax_rate_plac')); ?>">
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } ?>

        <?php 
        if($hide_fields['section_custom_pricing'] != 1) {
            get_template_part('template-parts/dashboard/edit-listing/custom-period'); 
        }
        ?>

    </div>
</div>