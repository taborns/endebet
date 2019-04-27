<?php
global $homey_local, $hide_fields;
$checkin_after_before = homey_option('checkin_after_before');
$checkin_after_before_array = explode( ',', $checkin_after_before );

$checkinout_hours = '';
?>
<div class="form-step">
    <!--step information-->
    <div class="block">
        <div class="block-title">
            <div class="block-left">
                <h2 class="title"><?php echo esc_html(homey_option('ad_terms_rules')); ?></h2>
            </div><!-- block-left -->
        </div>
        <div class="block-body">
            
            <?php if($hide_fields['cancel_policy'] != 1) { ?>
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="cancel"><?php echo esc_attr(homey_option('ad_cancel_policy')).homey_req('cancellation_policy'); ?></label>
                        <input type="text" name="cancellation_policy" class="form-control" <?php homey_required('cancellation_policy'); ?> placeholder="<?php echo esc_attr(homey_option('ad_cancel_policy_plac')); ?>">
                    </div>
                </div>
            </div>
            <?php } ?>

            <div class="row">

                <?php if($hide_fields['min_book_days'] != 1) { ?>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="min_book_days"><?php echo esc_attr(homey_option('ad_min_days_booking')).homey_req('min_book_days'); ?></label>
                        <input type="text" name="min_book_days" class="form-control" <?php homey_required('min_book_days'); ?> id="min_book_days" placeholder="<?php echo esc_attr(homey_option('ad_min_days_booking_plac')); ?>">
                    </div>
                </div>
                <?php } ?>

                <?php if($hide_fields['max_book_days'] != 1) { ?>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="max_book_days"><?php echo esc_attr(homey_option('ad_max_days_booking')).homey_req('max_book_days'); ?></label>
                        <input type="text" name="max_book_days" class="form-control" <?php homey_required('max_book_days'); ?> id="max_book_days" placeholder="<?php echo esc_attr(homey_option('ad_max_days_booking_plac')); ?>">
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
                                <option value=""><?php echo esc_attr(homey_option('ad_text_select')); ?></option>
                                <?php 
                                    foreach ($checkin_after_before_array as $hour) {
                                        echo '<option value="'.trim($hour).'">'.trim($hour).'</option>';
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
                            <option value=""><?php echo esc_attr(homey_option('ad_text_select')); ?></option>
                            <?php 
                            foreach ($checkin_after_before_array as $hour2) {
                                echo '<option value="'.trim($hour2).'">'.trim($hour2).'</option>';
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
                                    <input name="smoke" value="1" type="radio">
                                    <span class="control-text"><?php echo esc_attr(homey_option('ad_text_yes')); ?></span>
                                    <span class="control__indicator"></span>
                                    <span class="radio-tab-inner"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label class="control control--radio radio-tab">
                                    <input name="smoke" value="0" checked="checked" type="radio">
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
                                    <input name="pets" value="1" checked="checked" type="radio">
                                    <span class="control-text"><?php echo esc_attr(homey_option('ad_text_yes')); ?></span>
                                    <span class="control__indicator"></span>
                                    <span class="radio-tab-inner"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label class="control control--radio radio-tab">
                                    <input name="pets" value="0" type="radio">
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
                                    <input name="party" value="1" type="radio">
                                    <span class="control-text"><?php echo esc_attr(homey_option('ad_text_yes')); ?></span>
                                    <span class="control__indicator"></span>
                                    <span class="radio-tab-inner"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label class="control control--radio radio-tab">
                                    <input name="party" value="0" checked="checked" type="radio">
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
                                    <input name="children" value="1" checked="checked" type="radio">
                                    <span class="control-text"><?php echo esc_attr(homey_option('ad_text_yes')); ?></span>
                                    <span class="control__indicator"></span>
                                    <span class="radio-tab-inner"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label class="control control--radio radio-tab">
                                    <input name="children" value="0" type="radio">
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
                        <textarea name="additional_rules" class="form-control" id="rules" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>
</div>