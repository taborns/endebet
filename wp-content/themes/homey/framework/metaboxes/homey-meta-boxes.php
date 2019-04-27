<?php
/**
 * Registering meta boxes
 *
 * All the definitions of meta boxes are listed below with comments.
 * Please read them CAREFULLY.
 *
 * You also should read the changelog to know what has been changed before updating.
 *
 * For more information, please visit:
 * @link http://www.deluxeblogtips.com/meta-box/docs/define-meta-boxes
 */

/********************* META BOX DEFINITIONS ***********************/

add_filter( 'rwmb_meta_boxes', 'homey_register_metaboxes' );

if( !function_exists( 'homey_register_metaboxes' ) ) {
    function homey_register_metaboxes() {

        if (!class_exists('RW_Meta_Box')) {
            return;
        }

        global $meta_boxes, $wpdb;

        $prefix = 'homey_';
        $homey_local = homey_get_localization();

        $open_time_label = $homey_local['open_time_label'];
        $close_time_label = $homey_local['close_time_label'];
        $closed_label = homey_option('ad_close');

        $openning_hours_list = homey_option('openning_hours_list');
        $openning_hours_list_array = explode( ',', $openning_hours_list );
        $open_hours_array = array("" => $open_time_label);
        $close_hours_array = array("" => $close_time_label);
        if (!empty($openning_hours_list)) {
            foreach ($openning_hours_list_array as $hour) {
                $hour = trim($hour);
                $open_hours_array[$hour] = $hour;
                $close_hours_array[$hour] = $hour;
            }
        }

        $checkin_after_before_list = homey_option('checkin_after_before');
        $checkin_after_before_list = explode( ',', $checkin_after_before_list );
        $checkin_after_before_array = array("" => homey_option('ad_text_select'));
        if (!empty($checkin_after_before_list)) {
            foreach ($checkin_after_before_list as $hour) {
                $hour = trim($hour);
                $checkin_after_before_array[$hour] = $hour;
            }
        }

        $meta_boxes = array();
        $listing_city = array();

        homey_get_terms_array( 'listing_city', $listing_city );

        $dummy_array = array();

        /* ===========================================================================================
        *   Listing Custom Post Type Meta
        * ============================================================================================*/
        $meta_boxes[] = array(
            'id' => 'listing-meta-box',
            'title' => esc_html__('Listing Details', 'homey'),
            'pages' => array('listing'),
            'tabs' => array(
                'listing_details' => array(
                    'label' => homey_option('ad_section_info'),
                    'icon' => 'dashicons-admin-home',
                ),
                'listing_price' => array(
                    'label' => homey_option('ad_pricing_label'),
                    'icon' => 'dashicons-money',
                ),
                'listing_gallery' => array(
                    'label' => $homey_local['gallery_heading'],
                    'icon' => 'dashicons-format-gallery',
                ),
                'listing_video' => array(
                    'label' => homey_option('ad_video_section'),
                    'icon' => 'dashicons-format-video',
                ),
                'listing_location' => array(
                    'label' => homey_option('ad_location'),
                    'icon' => 'dashicons-location',
                ),
                'listing_bedrooms' => array(
                    'label' => homey_option('ad_bedrooms_text'),
                    'icon' => 'dashicons-layout',
                ),
                'listing_services' => array(
                    'label' => homey_option('ad_services_text'),
                    'icon' => 'dashicons-layout',
                ),
                'listing_terms_rules' => array(
                    'label' => homey_option('ad_terms_rules'),
                    'icon' => 'dashicons-admin-home',
                ),
                'home_slider' => array(
                    'label' => esc_html__('Slider', 'homey'),
                    'icon' => 'dashicons-images-alt',
                ),
                'settings' => array(
                    'label' => esc_html__('Settings', 'homey'),
                    'icon' => 'dashicons-admin-settings',
                ),

            ),
            'tab_style' => 'left',
            'fields' => array(
                array(
                    'id' => "{$prefix}listing_bedrooms",
                    'name' => homey_option('ad_no_of_bedrooms'),
                    'placeholder' => homey_option('ad_no_of_bedrooms_plac'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'listing_details',
                ),
                array(
                    'id' => "{$prefix}guests",
                    'name' => homey_option('ad_no_of_guests'),
                    'placeholder' => homey_option('ad_no_of_guests_plac'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'listing_details',
                ),
                array(
                    'id' => "{$prefix}beds",
                    'name' => homey_option('ad_no_of_beds'),
                    'placeholder' => homey_option('ad_no_of_beds_plac'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'listing_details',
                ),
                array(
                    'id' => "{$prefix}baths",
                    'name' => homey_option('ad_no_of_bathrooms'),
                    'placeholder' => homey_option('ad_no_of_bathrooms_plac'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'listing_details',
                ),
                array(
                    'id' => "{$prefix}listing_size",
                    'name' => homey_option('ad_listing_size'),
                    'placeholder' => homey_option('ad_size_placeholder'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'listing_details',
                ),
                array(
                    'id' => "{$prefix}listing_size_unit",
                    'name' => homey_option('ad_listing_size_unit'),
                    'placeholder' => homey_option('ad_listing_size_unit_plac'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'listing_details',
                ),
                array(
                    'id' => "{$prefix}listing_rooms",
                    'name' => homey_option('ad_listing_rooms'),
                    'placeholder' => homey_option('ad_listing_rooms_plac'),
                    'type' => 'text',
                    'std' => "",
                    'columns' => 6,
                    'tab' => 'listing_details',
                ),
                array(
                    'name' => homey_option('ad_is_featured_label'),
                    'id' => "{$prefix}featured",
                    'desc' => '',
                    'type' => 'radio',
                    'std' => 0,
                    'options' => array(
                        1 => homey_option('ad_text_yes'),
                        0 => homey_option('ad_text_no')
                    ),
                    'columns' => 6,
                    'tab' => 'listing_details',
                ),
                array(
                    'type' => 'heading',
                    'name' => homey_option('ad_section_openning'),
                    'columns' => 12,
                    'tab' => 'listing_details',
                ),

                array(
                    'id' => "{$prefix}mon_fri_label",
                    'name' => homey_option('ad_mon_fri'),
                    'type' => 'heading',
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),

                array(
                    'id' => "{$prefix}mon_fri_open",
                    'type' => 'select',
                    'options' => $open_hours_array,
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),
                array(
                    'id' => "{$prefix}mon_fri_close",
                    'type' => 'select',
                    'options' => $close_hours_array,
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),

                array(
                    'id' => "{$prefix}mon_fri_closed",
                    'name' => $closed_label,
                    'type' => 'checkbox',
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),

                array(
                    'id' => "{$prefix}sat_label",
                    'name' => homey_option('ad_sat'),
                    'type' => 'heading',
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),

                array(
                    'id' => "{$prefix}sat_open",
                    'type' => 'select',
                    'options' => $open_hours_array,
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),
                array(
                    'id' => "{$prefix}sat_close",
                    'type' => 'select',
                    'options' => $close_hours_array,
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),

                array(
                    'id' => "{$prefix}sat_closed",
                    'name' => $closed_label,
                    'type' => 'checkbox',
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),

                array(
                    'id' => "{$prefix}sun_label",
                    'name' => homey_option('ad_sun'),
                    'type' => 'heading',
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),

                array(
                    'id' => "{$prefix}sun_open",
                    'type' => 'select',
                    'options' => $open_hours_array,
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),
                array(
                    'id' => "{$prefix}sun_close",
                    'type' => 'select',
                    'options' => $close_hours_array,
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),

                array(
                    'id' => "{$prefix}sun_closed",
                    'name' => $closed_label,
                    'type' => 'checkbox',
                    'columns' => 3,
                    'tab' => 'listing_details',
                ),

                /*--------------------------------------------------------------------------------
                * Pricing
                **-------------------------------------------------------------------------------*/
                array(
                    'name' => homey_option('ad_ins_booking_label'),
                    'id' => "{$prefix}instant_booking",
                    'desc' => homey_option('ad_ins_booking_des'),
                    'type' => 'checkbox',
                    'std' => 0,
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),

                array(
                    'name' => homey_option('ad_nightly_label'),
                    'id' => "{$prefix}night_price",
                    'placeholder' => homey_option('ad_nightly_plac'),
                    'type' => 'text',
                    'std' => '',
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_weekends_label'),
                    'id' => "{$prefix}weekends_price",
                    'placeholder' => homey_option('ad_weekends_plac'),
                    'type' => 'text',
                    'std' => '',
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_weekend_days_label'),
                    'id' => "{$prefix}weekends_days",
                    'type' => 'select',
                    'options' => array(
                        'sat_sun' => $homey_local['sat_sun_label'],
                        'fri_sat' => $homey_local['fri_sat_label'],
                        'fri_sat_sun' => $homey_local['fri_sat_sun_label'],
                    ),
                    'std' => '',
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'type' => 'divider',
                    'columns' => 12,
                    'tab' => 'listing_price',
                ),
                array(
                    'type' => 'heading',
                    'name' => homey_option('ad_long_term_pricing'),
                    'columns' => 12,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_weekly7nights'),
                    'id' => "{$prefix}priceWeek",
                    'placeholder' => homey_option('ad_weekly7nights_plac'),
                    'type' => 'text',
                    'std' => '',
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_monthly30nights'),
                    'id' => "{$prefix}priceMonthly",
                    'placeholder' => homey_option('ad_monthly30nights_plac'),
                    'type' => 'text',
                    'std' => '',
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'type' => 'divider',
                    'columns' => 12,
                    'tab' => 'listing_price',
                ),
                array(
                    'type' => 'heading',
                    'name' => homey_option('ad_add_costs_label'),
                    'columns' => 12,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_allow_additional_guests'),
                    'id' => "{$prefix}allow_additional_guests",
                    'type' => 'radio',
                    'std' => 'no',
                    'options' => array(
                        'yes' => homey_option('ad_text_yes'),
                        'no' => homey_option('ad_text_no'),
                    ),
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_addinal_guests_label'),
                    'id' => "{$prefix}additional_guests_price",
                    'placeholder' => homey_option('ad_addinal_guests_plac'),
                    'type' => 'text',
                    'std' => '',
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_cleaning_fee'),
                    'id' => "{$prefix}cleaning_fee",
                    'placeholder' => homey_option('ad_cleaning_fee_plac'),
                    'type' => 'text',
                    'std' => '',
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_cleaning_fee_type_label'),
                    'id' => "{$prefix}cleaning_fee_type",
                    'type' => 'radio',
                    'std' => 'per_stay',
                    'options' => array(
                        'daily' => homey_option('ad_daily_text'),
                        'per_stay' => homey_option('ad_perstay_text'),
                    ),
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_city_fee'),
                    'id' => "{$prefix}city_fee",
                    'placeholder' => homey_option('ad_city_fee_plac'),
                    'type' => 'text',
                    'std' => '',
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_city_fee_type_label'),
                    'id' => "{$prefix}city_fee_type",
                    'type' => 'radio',
                    'std' => 'per_stay',
                    'options' => array(
                        'daily' => homey_option('ad_daily_text'),
                        'per_stay' => homey_option('ad_perstay_text'),
                    ),
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_security_deposit_label'),
                    'id' => "{$prefix}security_deposit",
                    'placeholder' => homey_option('ad_security_deposit_plac'),
                    'type' => 'text',
                    'std' => '',
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),
                array(
                    'name' => homey_option('ad_tax_rate_label'),
                    'id' => "{$prefix}tax_rate",
                    'placeholder' => homey_option('ad_tax_rate_plac'),
                    'type' => 'text',
                    'std' => '',
                    'columns' => 6,
                    'tab' => 'listing_price',
                ),

                /*--------------------------------------------------------------------------------
                * Media
                **-------------------------------------------------------------------------------*/
                array(
                    'name' => $homey_local['gallery_heading'],
                    'id' => "{$prefix}listing_images",
                    'desc' => $homey_local['image_size_text'],
                    'type' => 'image_advanced',
                    'max_file_uploads' => 50,
                    'columns' => 12,
                    'tab' => 'listing_gallery',
                ),

                array(
                    'id' => "{$prefix}video_url",
                    'name' => homey_option('ad_video_url'),
                    'placeholder' => homey_option('ad_video_placeholder'),
                    'type' => 'text',
                    'columns' => 12,
                    'tab' => 'listing_video',
                ),

                /*--------------------------------------------------------------------------------
                * Location
                **-------------------------------------------------------------------------------*/
                array(
                    'name' => $homey_local['listing_map_label'],
                    'id' => "{$prefix}show_map",
                    'type' => 'radio',
                    'std' => 1,
                    'options' => array(
                        1 => $homey_local['text_show'],
                        0 => $homey_local['text_hide']
                    ),
                    'columns' => 12,
                    'tab' => 'listing_location',
                ),
                array(
                    'name' => homey_option('ad_aptSuit'),
                    'id' => "{$prefix}aptSuit",
                    'type' => 'text',
                    'placeholder' => homey_option('ad_aptSuit_placeholder'),
                    'columns' => 6,
                    'tab' => 'listing_location',
                ),
                array(
                    'name' => homey_option('ad_zipcode'),
                    'id' => "{$prefix}zip",
                    'type' => 'text',
                    'placeholder' => homey_option('ad_zipcode_placeholder'),
                    'columns' => 6,
                    'tab' => 'listing_location',
                ),
                array(
                    'id' => "{$prefix}listing_address",
                    'name' => homey_option('ad_address'),
                    'placeholder' => homey_option('ad_address_placeholder'),
                    'desc' => $homey_local['address_des'],
                    'type' => 'text',
                    'std' => '',
                    'columns' => 12,
                    'tab' => 'listing_location',
                ),
                array(
                    'id' => "{$prefix}listing_location",
                    'name' => homey_option('ad_drag_pin'),
                    'desc' => $homey_local['drag_pin_des'],
                    'api_key' => homey_option('map_api_key'),
                    'type' => 'map',
                    'std' => '25.686540,-80.431345,15',
                    'style' => 'width: 100%; height: 410px',
                    'address_field' => "{$prefix}listing_address",
                    'columns' => 12,
                    'tab' => 'listing_location',
                ),
                /*array(
                    'name' => esc_html__('Google Map Street View', 'homey'),
                    'id' => "{$prefix}listing_street_view",
                    'type' => 'select',
                    'std' => 'hide',
                    'options' => array(
                        'hide' => esc_html__('Hide', 'homey'),
                        'show' => esc_html__('Show ', 'homey')
                    ),
                    'columns' => 12,
                    'tab' => 'listing_location',
                ),*/
                
                /*--------------------------------------------------------------------------------
                * Bedrooms
                **-------------------------------------------------------------------------------*/
                array(
                    'id'     => "{$prefix}accomodation",
                    // Gropu field
                    'type'   => 'group',
                    // Clone whole group?
                    'clone'  => true,
                    'sort_clone' => true,
                    'tab' => 'listing_bedrooms',
                    // Sub-fields
                    'fields' => array(
                        array(
                            'name' => homey_option('ad_acc_bedroom_name'),
                            'id'   => "acc_bedroom_name",
                            'placeholder'   => homey_option('ad_acc_bedroom_name_plac'),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                        array(
                            'name' => homey_option('ad_acc_guests'),
                            'id'   => "acc_guests",
                            'placeholder'   => homey_option('ad_acc_guests_plac'),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                        array(
                            'name' => homey_option('ad_acc_no_of_beds'),
                            'id'   => "acc_no_of_beds",
                            'placeholder'   => homey_option('ad_acc_no_of_beds_plac'),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                        array(
                            'name' => homey_option('ad_acc_bedroom_type'),
                            'id'   => "acc_bedroom_type",
                            'placeholder'   => homey_option('ad_acc_bedroom_type_plac'),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                    ),
                ),

                /*--------------------------------------------------------------------------------
                * Services
                **-------------------------------------------------------------------------------*/
                array(
                    'id'     => "{$prefix}services",
                    // Gropu field
                    'type'   => 'group',
                    // Clone whole group?
                    'clone'  => true,
                    'sort_clone' => true,
                    'tab' => 'listing_services',
                    // Sub-fields
                    'fields' => array(
                        array(
                            'name' => homey_option('ad_service_name'),
                            'id'   => "service_name",
                            'placeholder'   => homey_option('ad_service_name_plac'),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                        array(
                            'name' => homey_option('ad_service_price'),
                            'id'   => "service_price",
                            'placeholder'   => homey_option('ad_service_price_plac'),
                            'type' => 'text',
                            'columns' => 6,
                        ),
                        array(
                            'name' => homey_option('ad_service_des'),
                            'id'   => "service_des",
                            'placeholder'   => homey_option('ad_service_des_plac'),
                            'type' => 'textarea',
                            'columns' => 12,
                        )
                    ),
                ),

                /*--------------------------------------------------------------------------------
                * Terms & Rules 
                **-------------------------------------------------------------------------------*/
                array(
                    'id' => "{$prefix}cancellation_policy",
                    'name' => homey_option('ad_cancel_policy'),
                    'placeholder' => homey_option('ad_cancel_policy_plac'),
                    'type' => 'text',
                    'columns' => 12,
                    'tab' => 'listing_terms_rules',
                ),
                array(
                    'id' => "{$prefix}min_book_days",
                    'name' => homey_option('ad_min_days_booking'),
                    'placeholder' => homey_option('ad_min_days_booking_plac'),
                    'type' => 'text',
                    'columns' => 6,
                    'tab' => 'listing_terms_rules',
                ),
                array(
                    'id' => "{$prefix}max_book_days",
                    'name' => homey_option('ad_max_days_booking'),
                    'placeholder' => homey_option('ad_max_days_booking_plac'),
                    'type' => 'text',
                    'columns' => 6,
                    'tab' => 'listing_terms_rules',
                ),
                array(
                    'id' => "{$prefix}checkin_after",
                    'name' => homey_option('ad_check_in_after'),
                    'placeholder' => homey_option('ad_text_select'),
                    'type' => 'select',
                    'options' => $checkin_after_before_array,
                    'columns' => 6,
                    'tab' => 'listing_terms_rules',
                ),
                array(
                    'id' => "{$prefix}checkout_before",
                    'name' => homey_option('ad_check_out_before'),
                    'placeholder' => homey_option('ad_text_select'),
                    'type' => 'select',
                    'options' => $checkin_after_before_array,
                    'columns' => 6,
                    'tab' => 'listing_terms_rules',
                ),
                array(
                    'name' => homey_option('ad_smoking_allowed'),
                    'id' => "{$prefix}smoke",
                    'type' => 'radio',
                    'std' => 0,
                    'options' => array(
                        1 => homey_option('ad_text_yes'),
                        0 => homey_option('ad_text_no'),
                    ),
                    'columns' => 6,
                    'tab' => 'listing_terms_rules',
                ),
                array(
                    'name' => homey_option('ad_pets_allowed'),
                    'id' => "{$prefix}pets",
                    'type' => 'radio',
                    'std' => 1,
                    'options' => array(
                        1 => homey_option('ad_text_yes'),
                        0 => homey_option('ad_text_no'),
                    ),
                    'columns' => 6,
                    'tab' => 'listing_terms_rules',
                ),
                array(
                    'name' => homey_option('ad_party_allowed'),
                    'id' => "{$prefix}party",
                    'type' => 'radio',
                    'std' => 0,
                    'options' => array(
                        1 => homey_option('ad_text_yes'),
                        0 => homey_option('ad_text_no'),
                    ),
                    'columns' => 6,
                    'tab' => 'listing_terms_rules',
                ),
                array(
                    'name' => homey_option('ad_children_allowed'),
                    'id' => "{$prefix}children",
                    'type' => 'radio',
                    'std' => 1,
                    'options' => array(
                        1 => homey_option('ad_text_yes'),
                        0 => homey_option('ad_text_no'),
                    ),
                    'columns' => 6,
                    'tab' => 'listing_terms_rules',
                ),
                array(
                    'name' => homey_option('ad_add_rules_info_optional'),
                    'id' => "{$prefix}additional_rules",
                    'type' => 'textarea',
                    'placeholder' => '',
                    'columns' => 12,
                    'tab' => 'listing_terms_rules',
                ),


                /*--------------------------------------------------------------------------------
                * Homepage Slider 
                **-------------------------------------------------------------------------------*/
                array(
                    'name' => esc_html__('Do you want to display this property in the slider?', 'homey'),
                    'id' => "{$prefix}homeslider",
                    'desc' => esc_html__('Upload an image below if you selected yes.', 'homey'),
                    'type' => 'radio',
                    'std' => 'no',
                    'options' => array(
                        'yes' => esc_html__('Yes', 'homey'),
                        'no'  => esc_html__('No', 'homey'),
                    ),
                    'columns' => 12,
                    'tab' => 'home_slider',
                ),
                array(
                    'name' => esc_html__('Slider Image', 'homey'),
                    'id' => "{$prefix}slider_image",
                    'desc' => esc_html__('Suggested size 1920 x 600', 'homey'),
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1,
                    'columns' => 12,
                    'tab' => 'home_slider',
                ),

                /*--------------------------------------------------------------------------------
                * Settings 
                **-------------------------------------------------------------------------------*/
                array(
                    'name' => esc_html__('What to display in the sidebar?', 'homey'),
                    'id' => "{$prefix}booking_or_contact",
                    'desc' => esc_html__('Select what to display in the sidebar of listing detail page', 'homey'),
                    'type' => 'select',
                    'std' => '',
                    'options' => array(
                        '' => esc_html__('Default (Same settings as theme options)'),
                        'booking_form' => esc_html__('Booking Form', 'homey'),
                        'contact_form' => esc_html__('Contact Form', 'homey'),
                    ),
                    'columns' => 12,
                    'tab' => 'settings',
                ),
            )
        );

        
        /* ===========================================================================================
        *   Listing Template
        * ============================================================================================*/
        $listing_types = array();
        $room_types = array();
        $listing_amenity = array();
        $listing_facility = array();
        $listing_country = array();
        $listing_state = array();
        $listing_city = array();
        $listing_area = array();
        homey_get_terms_array( 'listing_type', $listing_types );
        homey_get_terms_array( 'room_type', $room_types );
        //homey_get_terms_array( 'listing_amenity', $listing_amenity );
        //homey_get_terms_array( 'listing_facility', $listing_facility );
        homey_get_terms_array( 'listing_country', $listing_country );
        homey_get_terms_array( 'listing_state', $listing_state );
        homey_get_terms_array( 'listing_city', $listing_city );
        homey_get_terms_array( 'listing_area', $listing_area );

        $meta_boxes[] = array(
            'id'        => 'homey_listing_template',
            'title'     => esc_html__('Listing Advanced Options', 'homey'),
            'pages'     => array( 'page' ),
            'context' => 'normal',

            'fields'    => array(
                array(
                    'name'      => esc_html__('Order By', 'homey'),
                    'id'        => $prefix . 'listings_sort',
                    'type'      => 'select',
                    'options'   => array(
                        'd_date'  => esc_html__('Date New to Old', 'homey'),
                        'a_date'  => esc_html__('Date Old to New', 'homey'),
                        'd_price' => esc_html__('Price (High to Low)', 'homey'),
                        'a_price' => esc_html__('Price (Low to High)', 'homey'),
                        'd_rating' => esc_html__('Rating', 'homey'),
                        'featured_top' => esc_html__('Show Featured on Top', 'homey'),
                    ),
                    'std'       => array( 'd_date' ),
                    'desc'      => '',
                    'columns' => 6,
                ),
                array(
                    'id' => $prefix."listings_num",
                    'name' => esc_html__('Number of listings to show', 'homey'),
                    'desc' => "",
                    'type' => 'number',
                    'std' => "9",
                    'columns' => 6
                ),
            
                array(
                    'name'      => homey_option('ad_listing_type'),
                    'id'        => $prefix . 'types',
                    'type'      => 'select',
                    'options'   => $listing_types,
                    'desc'      => '',
                    'columns' => 6,
                    'multiple' => true
                ),
                array(
                    'name'      => homey_option('ad_room_type'),
                    'id'        => $prefix . 'room_types',
                    'type'      => 'select',
                    'options'   => $room_types,
                    'desc'      => '',
                    'columns' => 6,
                    'multiple' => true
                ),
                
                array(
                    'name'      => homey_option('ad_country'),
                    'id'        => $prefix . 'countries',
                    'type'      => 'select',
                    'options'   => $listing_country,
                    'desc'      => '',
                    'columns' => 6,
                    'multiple' => true
                ),
                array(
                    'name'      => homey_option('ad_state'),
                    'id'        => $prefix . 'states',
                    'type'      => 'select',
                    'options'   => $listing_state,
                    'desc'      => '',
                    'columns' => 6,
                    'multiple' => true
                ),
                array(
                    'name'      => homey_option('ad_city'),
                    'id'        => $prefix . 'cities',
                    'type'      => 'select',
                    'options'   => $listing_city,
                    'desc'      => '',
                    'columns' => 6,
                    'multiple' => true
                ),
                array(
                    'name'      => homey_option('ad_area'),
                    'id'        => $prefix . 'areas',
                    'type'      => 'select',
                    'options'   => $listing_area,
                    'desc'      => '',
                    'columns' => 6,
                    'multiple' => true
                ),
                
            )
        );

        /* ===========================================================================================
        *   Listing Template half map
        * ============================================================================================*/

        $meta_boxes[] = array(
            'id'        => 'homey_listing_template_halfmap',
            'title'     => esc_html__('Half Map Template Options', 'homey'),
            'pages'     => array( 'page' ),
            'context' => 'normal',

            'fields'    => array(
                array(
                    'name'      => esc_html__('Order By', 'homey'),
                    'id'        => $prefix . 'listings_halfmap_sort',
                    'type'      => 'select',
                    'options'   => array(
                        'd_date'  => esc_html__('Date New to Old', 'homey'),
                        'a_date'  => esc_html__('Date Old to New', 'homey'),
                        'd_price' => esc_html__('Price (High to Low)', 'homey'),
                        'a_price' => esc_html__('Price (Low to High)', 'homey'),
                        'd_rating' => esc_html__('Rating', 'homey'),
                        'featured_top' => esc_html__('Show Featured on Top', 'homey'),
                    ),
                    'std'       => array( 'd_date' ),
                    'desc'      => '',
                    'columns' => 6,
                ),
                array(
                    'id' => $prefix."listings_halfmap_num",
                    'name' => esc_html__('Number of listings to show', 'homey'),
                    'desc' => "",
                    'type' => 'number',
                    'std' => "9",
                    'columns' => 6
                ),
            
                array(
                    'name'      => homey_option('ad_listing_type'),
                    'id'        => $prefix . 'halfmap_types',
                    'type'      => 'select',
                    'options'   => $listing_types,
                    'desc'      => '',
                    'columns' => 6,
                    'multiple' => true
                )
            )
        );


        /* ===========================================================================================
        *   Page Settings
        * ============================================================================================*/
        $meta_boxes[] = array(
            'id'        => 'homey_page_settings',
            'title'     => esc_html__('Page Header Options', 'homey' ),
            'pages'     => array( 'page' ),
            'context' => 'normal',

            'fields'    => array(
                array(
                    'name'      => esc_html__('Header Type', 'homey' ),
                    'id'        => $prefix . 'header_type',
                    'type'      => 'select',
                    'options'   => array(
                        'none' => esc_html__('None', 'homey' ),
                        'parallax' => esc_html__('Image', 'homey' ),
                        'half_search' => esc_html__('Half Search', 'homey' ),
                        'video' => esc_html__('Video', 'homey' ),
                        'slider' => esc_html__('Properties Slider', 'homey' ),
                        'rev_slider' => esc_html__('Revolution Slider', 'homey' ),
                        'map' => esc_html__('Google Map with Listings', 'homey' ),
                        
                    ),
                    'std'       => array( 'none' ),
                    'desc'      => esc_html__('Choose page header type','homey'),
                ),
                array(
                    'name'      => esc_html__('Map Data', 'homey' ),
                    'id'        => $prefix.'map_data_type',
                    'type'      => 'select',
                    'options'   => array(
                        //'lat_long' => esc_html__('Latitude & Longitude', 'homey' ),
                        'city' => esc_html__('City', 'homey' ),                    
                    ),
                    'std'       => array( 'city' ),
                    'desc'      => esc_html__('Choose where map show listings','homey'),
                    'hidden' => array( $prefix.'header_type', '!=', 'map' )
                ),
                array(
                    'name'      => esc_html__('Latitude', 'homey' ),
                    'id'        => $prefix.'map_lat',
                    'type' => 'text',
                    'std' => '',
                    'placeholder' => '25.7902778',
                    'hidden' => array( $prefix.'map_data_type', '!=', 'lat_long' )
                ),
                array(
                    'name'      => esc_html__('Longitude', 'homey' ),
                    'id'        => $prefix.'map_long',
                    'type' => 'text',
                    'std' => '',
                    'placeholder' => '-80.1302778',
                    'hidden' => array( $prefix.'map_data_type', '!=', 'lat_long' )
                ),
                array(
                    'name'      => esc_html__('Select City', 'homey'),
                    'id'        => $prefix . 'map_city',
                    'type'      => 'select',
                    'options'   => $listing_city,
                    'desc'      => esc_html__('Choose city for listings on map header, you can select multiple cities or keep all un-select to show from all cities', 'homey'),
                    'multiple' => true,
                    'hidden' => array( $prefix.'map_data_type', '!=', 'city' )
                ),
                array(
                    'name'      => esc_html__('Title', 'homey' ),
                    'id'        => $prefix . 'header_title',
                    'type' => 'text',
                    'std' => '',
                    'desc' => '',
                    'visible' => array( $prefix.'header_type', 'in', array( 'parallax', 'video', 'rev_slider', 'half_search' ) )
                ),
                array(
                    'name'      => esc_html__('Subtitle', 'homey' ),
                    'id'        => $prefix . 'header_subtitle',
                    'type' => 'text',
                    'std' => '',
                    'desc' => '',
                    'visible' => array( $prefix.'header_type', 'in', array( 'parallax', 'video', 'rev_slider', 'half_search' ) )
                ),
                array(
                    'name'      => esc_html__('Revolution Slider', 'homey' ),
                    'id'        => $prefix . 'header_revslider',
                    'type' => 'select_advanced',
                    'std' => '',
                    'options' => homey_get_revolution_slider(),
                    'multiple'    => false,
                    'placeholder' => esc_html__( 'Select an Slider', 'homey' ),
                    'desc' => '',
                    'hidden' => array( $prefix.'header_type', '!=', 'rev_slider' )
                ),
                array(
                    'name'      => esc_html__('Image', 'homey' ),
                    'id'        => $prefix . 'header_image',
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1,
                    'desc'      => '',
                    'visible' => array( $prefix.'header_type', 'in', array( 'parallax', 'half_search' ) )
                ),

                array(
                    'name' => esc_html__('MP4 File', 'homey'),
                    'id' => "{$prefix}header_bg_mp4",
                    'type' => 'file_input',
                    'hidden' => array( $prefix.'header_type', '!=', 'video' )
                ),
                array(
                    'name' => esc_html__('WEBM File', 'homey'),
                    'id' => "{$prefix}header_bg_webm",
                    'type' => 'file_input',
                    'hidden' => array( $prefix.'header_type', '!=', 'video' )
                ),
                array(
                    'name' => esc_html__('OGV File', 'homey'),
                    'id' => "{$prefix}header_bg_ogv",
                    'type' => 'file_input',
                    'hidden' => array( $prefix.'header_type', '!=', 'video' )
                ),
                array(
                    'name'      => esc_html__('Video Image', 'homey' ),
                    'id'        => $prefix . 'video_image',
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1,
                    'desc'      => '',
                    'hidden' => array( $prefix.'header_type', '!=', 'video' )
                ),
                array(
                    'name'      => esc_html__('Height', 'homey' ),
                    'id'        => $prefix . 'parallax_height',
                    'type' => 'text',
                    'std' => '',
                    'desc' => esc_html__('Default 600px', 'homey'),
                    'visible' => array( $prefix.'header_type', 'in', array( 'parallax', 'video' ) )
                ),

                array(
                    'name'      => esc_html__('Height Mobile', 'homey' ),
                    'id'        => $prefix . 'parallax_height_mobile',
                    'type' => 'text',
                    'std' => '',
                    'desc' => esc_html__('Default 300px', 'homey'),
                    'visible' => array( $prefix.'header_type', 'in', array( 'parallax', 'video' ) )
                ),
                array(
                    'name'      => esc_html__('Overlay Color Opacity', 'homey' ),
                    'id'        => $prefix . 'header_opacity',
                    'type' => 'select',
                    'options' => array(
                        '0' => '0',
                        '0.1' => '1',
                        '0.2' => '2',
                        '0.3' => '3',
                        '0.35' => '3.5',
                        '0.4' => '4',
                        '0.5' => '5',
                        '0.6' => '6',
                        '0.7' => '7',
                        '0.8' => '8',
                        '0.9' => '9',
                        '1' => '10',
                    ),
                    'std'       => array( '0.35' ),
                    'visible' => array( $prefix.'header_type', 'in', array( 'parallax', 'video' ) )
                ),
                array(
                    'name'      => esc_html__('Banner Search', 'homey' ),
                    'id'        => $prefix . 'header_search',
                    'type' => 'switch',
                    'style'     => 'rounded',
                    'on_label'  => esc_html__('Enable', 'homey' ),
                    'off_label' => esc_html__('Disable', 'homey' ),
                    'std'       => 0,
                    'desc' => '',
                    'visible' => array( $prefix.'header_type', 'in', array( 'parallax', 'video', 'rev_slider' ) )
                ),
                array(
                    'name'      => esc_html__('Banner Search Style', 'homey' ),
                    'id'        => $prefix . 'head_search_style',
                    'type' => 'select',
                    'options'   => array(
                        'horizontal' => esc_html__('Horizontal', 'homey' ),
                        'vertical' => esc_html__('Vertical', 'homey' ),                
                    ),
                    'std'       => array( 'horizontal' ),
                    'desc' => '',
                    'visible' => array( $prefix.'header_search', '!=', 0 )
                ),
                array(
                    'name'      => esc_html__('Full Screen', 'homey' ),
                    'id'        => $prefix . 'banner_full',
                    'type' => 'switch',
                    'style'     => 'rounded',
                    'on_label'  => esc_html__('Enable', 'homey' ),
                    'off_label' => esc_html__('Disable', 'homey' ),
                    'std'       => 0,
                    'desc' => '',
                    'visible' => array( $prefix.'header_type', 'in', array( 'parallax', 'video', 'map', 'slider' ) )
                ),
                array(
                    'name'      => esc_html__('Transparent Header', 'homey' ),
                    'id'        => $prefix . 'header_trans',
                    'type' => 'switch',
                    'style'     => 'rounded',
                    'on_label'  => esc_html__('Enable', 'homey' ),
                    'off_label' => esc_html__('Disable', 'homey' ),
                    'std'       => 0,
                    'desc' => esc_html__('It’s only work if the header v1 or v4 is selected', 'homey'),
                    'visible' => array( $prefix.'header_type', 'in', array( 'parallax', 'video', 'rev_slider', 'slider' ) )
                ),
            )
        );


        /* ===========================================================================================
        *   Testimonials
        * ============================================================================================*/
        $meta_boxes[] = array(
            'id'        => 'homey_testimonials',
            'title'     => esc_html__('Testimonial Details', 'homey' ),
            'pages'     => array( 'homey_testimonials' ),
            'context' => 'normal',

            'fields'    => array(
                array(
                    'name'      => esc_html__('Testimonial Text', 'homey' ),
                    'id'        => $prefix . 'testi_text',
                    'type'      => 'textarea',
                    'desc'      => esc_html__('Write a testimonial into the textarea.','homey'),
                ),
                array(
                    'name'      => esc_html__('By who?', 'homey'),
                    'id'        => $prefix . 'testi_name',
                    'type'      => 'text',
                    'desc'      => esc_html__('Name of the client who gave feedback','homey'),
                ),
                array(
                    'name'      => esc_html__('Position', 'homey'),
                    'id'        => $prefix . 'testi_position',
                    'type'      => 'text',
                    'desc'      => esc_html__('Ex: Founder & CEO.','homey'),
                ),
                array(
                    'name'      => esc_html__('Company Name', 'homey'),
                    'id'        => $prefix . 'testi_company',
                    'type'      => 'text',
                    'desc'      => '',
                ),
                array(
                    'name'      => esc_html__('Photo', 'homey'),
                    'id'        => $prefix . 'testi_photo',
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1,
                    'desc'      => '',
                )
            )
        );

        /* ===========================================================================================
        *   Partners
        * ============================================================================================*/
        $meta_boxes[] = array(
            'id'        => 'homey_partners',
            'title'     => esc_html__('Partner Details', 'homey'),
            'pages'     => array( 'homey_partner' ),
            'context' => 'normal',

            'fields'    => array(
                array(
                    'name'      => esc_html__('Partner website address', 'homey'),
                    'id'        => $prefix . 'partner_website',
                    'type'      => 'url',
                    'desc'      => esc_html__('Enter website address','homey'),
                )
            )
        );


        /* ===========================================================================================
        *   Taxonomies
        * ============================================================================================*/
        $meta_boxes[] = array(
            'id'        => 'homey_taxonomies',
            'title'     => esc_html__('Other Settings', 'homey' ),
            'taxonomies' => array( 'listing_type', 'listing_city', 'room_type', 'listing_country', 'listing_state', 'listing_area' ),
            

            'fields'    => array(
                array(
                    'name'      => esc_html__('Image', 'homey' ),
                    'id'        => $prefix . 'taxonomy_img',
                    'type'      => 'image_advanced',
                    'max_file_uploads' => 1,
                ),
                
            )
        );

        $meta_boxes[] = array(
            'id'        => 'homey_taxonomies_marker',
            'title'     => '',
            'taxonomies' => array( 'listing_type' ),
            

            'fields'    => array(
                array(
                    'name'      => esc_html__('Google Map Marker Icon', 'homey' ),
                    'id'        => $prefix . 'marker_icon',
                    'type'      => 'image_advanced',
                    'class'      => 'homey_full_width',
                    'max_file_uploads' => 1,
                ),
                array(
                    'name'      => esc_html__('Google Map Marker Retina Icon', 'homey' ),
                    'id'        => $prefix . 'marker_retina_icon',
                    'type'      => 'image_advanced',
                    'class'      => 'homey_full_width',
                    'max_file_uploads' => 1,
                )
            )
        );

        $meta_boxes = apply_filters('homey_theme_meta', $meta_boxes);

        return $meta_boxes;

    }
} // End Meta boxes

// Get revolution sliders
if( !function_exists('homey_get_revolution_slider') ) {
    function homey_get_revolution_slider()
    {
        global $wpdb;
        $catList = array();
        //Revolution Slider
        if (class_exists( 'RevSlider' )) {
            $sliders = $wpdb->get_results($q = "SELECT * FROM " . $wpdb->prefix . "revslider_sliders ORDER BY id");

            // Iterate over the sliders
            $catList = array();
            foreach ($sliders as $key => $item) {
                $catList[$item->alias] = stripslashes($item->title);
            }
        }

        return $catList;
    }
}

/*-----------------------------------------------------------------------------------*/
// Get terms array
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'homey_get_terms_array' ) ) {
    function homey_get_terms_array( $tax_name, &$terms_array ) {
        $tax_terms = get_terms( $tax_name, array(
            'hide_empty' => false,
        ) );
        homey_add_term_children( 0, $tax_terms, $terms_array );
    }
}


if ( ! function_exists( 'homey_add_term_children' ) ) :
    function homey_add_term_children( $parent_id, $tax_terms, &$terms_array, $prefix = '' ) {
        if ( ! empty( $tax_terms ) && ! is_wp_error( $tax_terms ) ) {
            foreach ( $tax_terms as $term ) {
                if ( $term->parent == $parent_id ) {
                    $terms_array[ $term->slug ] = $prefix . $term->name;
                    homey_add_term_children( $term->term_id, $tax_terms, $terms_array, $prefix . '- ' );
                }
            }
        }
    }
endif;

?>