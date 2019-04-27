<?php
global $homey_prefix, $hide_fields, $homey_local, $listing_data, $listing_meta_data;
$min_book_days = homey_get_field_meta('min_book_days'); 
$max_book_days = homey_get_field_meta('max_book_days'); 
$checkin_after = homey_get_field_meta('checkin_after'); 
$checkout_before = homey_get_field_meta('checkout_before'); 
$smoke = homey_get_field_meta('smoke'); 
$pets = homey_get_field_meta('pets'); 
$party = homey_get_field_meta('party'); 
$children = homey_get_field_meta('children'); 
$additional_rules = homey_get_field_meta('additional_rules'); 
$cancellation_policy = homey_get_field_meta('cancellation_policy'); 


$checkin_after_before = homey_option('checkin_after_before');
$checkin_after_before_array = explode( ',', $checkin_after_before );

?>
<div id="rules-tab" class="tab-pane fade">
    <div class="block-title visible-xs">
            <h3 class="title"><?php echo esc_attr(homey_option('ad_terms_rules')); ?></h3>
    </div>
    <div class="block-body">

        <?php if($hide_fields['cancel_policy'] != 1) { ?>
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="form-group">
                    <label for="cancel"><?php echo esc_attr(homey_option('ad_cancel_policy')).homey_req('cancellation_policy'); ?></label>
                    <input type="text" name="cancellation_policy" <?php homey_required('cancellation_policy'); ?> value="<?php echo esc_attr($cancellation_policy); ?>" class="form-control" placeholder="<?php echo esc_attr(homey_option('ad_cancel_policy_plac')); ?>">
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="row">

            <?php if($hide_fields['min_book_days'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="min_book_days"><?php echo esc_attr(homey_option('ad_min_days_booking')).homey_req('min_book_days'); ?></label>
                    <input type="text" name="min_book_days" <?php homey_required('min_book_days'); ?> value="<?php echo esc_attr($min_book_days); ?>" class="form-control" id="min_book_days" placeholder="<?php echo esc_attr(homey_option('ad_min_days_booking_plac')); ?>">
                </div>
            </div>
            <?php } ?>

            <?php if($hide_fields['max_book_days'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="max_book_days"><?php echo esc_attr(homey_option('ad_max_days_booking')).homey_req('max_book_days'); ?></label>
                    <input type="text" name="max_book_days" <?php homey_required('max_book_days'); ?> value="<?php echo esc_attr($max_book_days); ?>" class="form-control" id="max_book_days" placeholder="<?php echo esc_attr(homey_option('ad_max_days_booking_plac')); ?>">
                </div>
            </div>
            <?php } ?>

        </div>
        <div class="row">
            <?php if($hide_fields['checkin_after'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="checkin_after"><?php echo esc_attr(homey_option('ad_check_in_after')).homey_req('checkin_after'); ?></label>
                    <select name="checkin_after" class="selectpicker" <?php homey_required('checkin_after'); ?> id="checkin_after" data-live-search="false" title="<?php echo esc_attr(homey_option('ad_text_select')); ?>">
                        <?php 
                        foreach ($checkin_after_before_array as $hour) {
                            echo '<option '.selected( homey_get_field_meta('checkin_after'), trim($hour), false).' value="'.trim($hour).'">'.trim($hour).'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <?php } ?>

            <?php if($hide_fields['checkout_before'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="checkout_before"><?php echo esc_attr(homey_option('ad_check_out_before')).homey_req('checkout_before'); ?></label>
                    <select name="checkout_before" class="selectpicker" <?php homey_required('checkout_before'); ?> id="checkout_before" data-live-search="false" title="<?php echo esc_attr(homey_option('ad_text_select')); ?>">
                        <?php 
                        foreach ($checkin_after_before_array as $hour) {
                            echo '<option '.selected( homey_get_field_meta('checkout_before'), trim($hour), false).' value="'.trim($hour).'">'.trim($hour).'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="row">

            <!--Smoking-->
            <?php if($hide_fields['smoking_allowed'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <label class="label-condition"><?php echo esc_attr(homey_option('ad_smoking_allowed')); ?>?</label>
            </div>
            <div class="col-sm-6 col-xs-12">
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="control control--radio radio-tab">
                                <input <?php checked($smoke, '1'); ?> name="smoke" value="1" type="radio">
                                <span class="control-text"><?php echo esc_attr(homey_option('ad_text_yes')); ?></span>
                                <span class="control__indicator"></span>
                                <span class="radio-tab-inner"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="control control--radio radio-tab">
                                <input <?php checked($smoke, '0'); ?> name="smoke" value="0" type="radio">
                                <span class="control-text"><?php echo esc_attr(homey_option('ad_text_no')); ?></span>
                                <span class="control__indicator"></span>
                                <span class="radio-tab-inner"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <!--Pets-->
            <?php if($hide_fields['pets_allowed'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <label class="label-condition"><?php echo esc_attr(homey_option('ad_pets_allowed')); ?>?</label>
            </div>
            <div class="col-sm-6 col-xs-12">
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="control control--radio radio-tab">
                                <input name="pets" <?php checked($pets, '1'); ?> value="1" type="radio">
                                <span class="control-text"><?php echo esc_attr(homey_option('ad_text_yes')); ?></span>
                                <span class="control__indicator"></span>
                                <span class="radio-tab-inner"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="control control--radio radio-tab">
                                <input name="pets" <?php checked($pets, '0'); ?> value="0" type="radio">
                                <span class="control-text"><?php echo esc_attr(homey_option('ad_text_no')); ?></span>
                                <span class="control__indicator"></span>
                                <span class="radio-tab-inner"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <!--Party-->
            <?php if($hide_fields['party_allowed'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <label class="label-condition"><?php echo esc_attr(homey_option('ad_party_allowed')); ?>?</label>
            </div>
            <div class="col-sm-6 col-xs-12">
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="control control--radio radio-tab">
                                <input name="party" <?php checked($party, '1'); ?> value="1" type="radio">
                                <span class="control-text"><?php echo esc_attr(homey_option('ad_text_yes')); ?></span>
                                <span class="control__indicator"></span>
                                <span class="radio-tab-inner"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="control control--radio radio-tab">
                                <input name="party" <?php checked($party, '0'); ?> value="0" type="radio">
                                <span class="control-text"><?php echo esc_attr(homey_option('ad_text_no')); ?></span>
                                <span class="control__indicator"></span>
                                <span class="radio-tab-inner"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <!--Children-->
            <?php if($hide_fields['children_allowed'] != 1) { ?>
            <div class="col-sm-6 col-xs-12">
                <label class="label-condition"><?php echo esc_attr(homey_option('ad_children_allowed')); ?>?</label>
            </div>
            <div class="col-sm-6 col-xs-12">
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="control control--radio radio-tab">
                                <input name="children" <?php checked($children, '1'); ?> value="1" type="radio">
                                <span class="control-text"><?php echo esc_attr(homey_option('ad_text_yes')); ?></span>
                                <span class="control__indicator"></span>
                                <span class="radio-tab-inner"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="control control--radio radio-tab">
                                <input name="children" <?php checked($children, '0'); ?> value="0" type="radio">
                                <span class="control-text"><?php echo esc_attr(homey_option('ad_text_no')); ?></span>
                                <span class="control__indicator"></span>
                                <span class="radio-tab-inner"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php if($hide_fields['additional_rules'] != 1) { ?>
        <div class="row">
            <div class="col-sm-12 col-sm-12">
                <div class="form-group">
                    <label for="additional_rules"><?php echo esc_attr(homey_option('ad_add_rules_info_optional')); ?></label>
                    <textarea name="additional_rules" class="form-control" id="rules" rows="3"><?php echo esc_attr($additional_rules); ?></textarea>
                </div>
            </div>
        </div>
        <?php } ?>
        
    </div>
</div>