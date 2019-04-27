<?php
global $homey_local, $hide_fields, $current_user, $listing_data, $listing_meta_data, $edit_listing_id;

wp_get_current_user();
$userID = $current_user->ID;

$layout_order = homey_option('listing_form_sections');
$layout_order = $layout_order['enabled'];

$edit_listing_id = intval( trim( $_GET['edit_listing'] ) );
$listing_data    = get_post( $edit_listing_id );

if ( ! empty( $listing_data ) && ( $listing_data->post_type == 'listing' ) ) {
        $listing_meta_data = get_post_custom( $listing_data->ID );

    if ( $listing_data->post_author == $current_user->ID ) {

        $address = get_post_meta( $edit_listing_id, 'homey_listing_address', true );
        $featured = get_post_meta( $edit_listing_id, 'homey_featured', true );

        $cal_class = $info_class = $pricing_class = '';
        if(isset($_GET['tab']) && $_GET['tab'] == 'calendar') {
            $cal_class = 'active';

        } elseif(isset($_GET['tab']) && $_GET['tab'] == 'pricing-tab') {
            $pricing_class = 'active';
        } else {
            $info_class = 'active';
        }

        $dashboard = homey_get_template_link('template/dashboard.php');

        $upgrade_link  = add_query_arg( array(
            'page' => 'upgrade_featured',
            'upgrade_id' => $edit_listing_id,
         ), $dashboard );
?>

        <form autocomplete="off" id="submit_listing_form" name="new_post" method="post" action="#" enctype="multipart/form-data" class="edit-frontend-property">

            <div class="block">
                <div class="block-head table-block">
                    <div class="block-left">
                        <h2 class="title"><?php echo get_the_title($edit_listing_id); ?></h2>
                        <?php if(!empty($address)) { ?>
                        <address class="title-address"><i class="fa fa-map-marker v-middle"></i> 
                            <?php echo esc_attr($address); ?>
                        </address>
                        <?php } ?>
                    </div>

                    <?php if($featured != 1) { ?>
                    <div class="block-right">
                        <a class="btn btn-secondary btn-slim upgrade-button" href="<?php echo esc_url($upgrade_link); ?>"><?php echo esc_attr($homey_local['upgrade_btn']); ?></a>
                    </div>
                    <?php } ?>
                </div>
                <div class="listing-submit-wrap">
                    <a href="<?php echo get_permalink($edit_listing_id); ?>" class="btn btn-dark-grey btn-preview-listing"><?php echo esc_attr($homey_local['view_btn']); ?></a>
                    <button class="btn btn-dark-grey btn-save-listing"><?php echo esc_attr($homey_local['update_btn']); ?></button>
                </div>
            </div>

            <div class="homy-taber-module">
                <ul id="form_tabs" class="taber-nav taber-nav-fixed" role="tablist">
                    <?php 
                    if ($layout_order) { 
                        foreach ($layout_order as $key=>$value) {

                            switch($key) { 
                                case 'information':
                                    ?>
                                    <li role="presentation" class="<?php echo esc_attr($info_class); ?>">
                                        <a href="#information-tab" aria-controls="information-tab" role="tab" data-toggle="tab"><?php echo esc_attr(homey_option('ad_section_info'));?></a>
                                    </li>
                                    <?php
                                break;

                                case 'pricing':
                                    ?>
                                    <li role="presentation" class="<?php echo esc_attr($pricing_class); ?>">
                                        <a href="#pricing-tab" aria-controls="pricing-tab" role="tab" data-toggle="tab"><?php echo esc_attr(homey_option('ad_pricing_label'));?></a>
                                    </li>
                                    <?php
                                break;

                                case 'media':
                                    ?>
                                    <li role="presentation">
                                        <a href="#media-tab" aria-controls="media-tab" role="tab" data-toggle="tab"><?php echo esc_attr(homey_option('ad_section_media')); ?></a>
                                    </li>
                                    <?php
                                break;

                                case 'features':
                                    ?>
                                    <li role="presentation">
                                        <a href="#features-tab" aria-controls="features-tab" role="tab" data-toggle="tab"><?php echo esc_attr(homey_option('ad_features')); ?></a>
                                    </li>
                                    <?php
                                break;

                                case 'location':
                                    ?>
                                    <li role="presentation">
                                        <a href="#location-tab" aria-controls="location-tab" role="tab" data-toggle="tab"><?php echo esc_attr(homey_option('ad_location')); ?></a>
                                    </li>
                                    <?php
                                break;

                                case 'bedrooms':
                                    ?>
                                    <li role="presentation">
                                        <a href="#bedrooms-tab" aria-controls="bedrooms-tab" role="tab" data-toggle="tab"><?php echo esc_attr(homey_option('ad_bedrooms_text')); ?></a>
                                    </li>
                                    <?php
                                break;

                                case 'services':
                                    ?>
                                    <li role="presentation">
                                        <a href="#services-tab" aria-controls="services-tab" role="tab" data-toggle="tab"><?php echo esc_attr(homey_option('ad_services_text')); ?></a>
                                    </li>
                                    <?php
                                break;

                                case 'term_rules':
                                    ?>
                                    <li role="presentation">
                                        <a href="#rules-tab" aria-controls="rules-tab" role="tab" data-toggle="tab"><?php echo esc_attr(homey_option('ad_terms_rules')); ?></a>
                                    </li>
                                    <?php
                                break;
                            }
                        }
                    }
                    ?>
                    <li role="presentation" class="<?php echo esc_attr($cal_class); ?>">
                        <a href="#calendar-tab" aria-controls="calendar-tab" role="tab" data-toggle="tab"><?php echo esc_attr($homey_local['cal_label']); ?></a>
                    </li>
                </ul>
            </div>
            
            <div class="block">
                <div class="tab-content">                            
                <?php 
                if ($layout_order) { 
                    foreach ($layout_order as $key=>$value) {

                        switch($key) { 
                            case 'information':
                                get_template_part('template-parts/dashboard/edit-listing/information');
                            break;

                            case 'pricing':
                                get_template_part('template-parts/dashboard/edit-listing/pricing');
                            break;

                            case 'media':
                                get_template_part('template-parts/dashboard/edit-listing/media');
                            break;

                            case 'features':
                                get_template_part('template-parts/dashboard/edit-listing/features');
                            break;

                            case 'location':
                                get_template_part('template-parts/dashboard/edit-listing/location');
                            break;

                            case 'bedrooms':
                                get_template_part('template-parts/dashboard/edit-listing/bedrooms');
                            break;

                            case 'services':
                                get_template_part('template-parts/dashboard/edit-listing/services');
                            break;

                            case 'term_rules':
                                get_template_part('template-parts/dashboard/edit-listing/terms');
                            break;
                        }
                    }
                }

                get_template_part('template-parts/dashboard/edit-listing/calendar'); ?>
                
                </div>
            </div>


            <?php wp_nonce_field('submit_listing', 'homey_add_listing_nonce'); ?>

            <input type="hidden" name="action" value="update_listing"/>
            <input type="hidden" name="listing_id" value="<?php echo intval( $listing_data->ID ); ?>"/>

        </form><!-- #add-property-form -->

<?php 

    } else {
            esc_html_e('You are not logged in or This listing does not belong to you.', 'homey');
        }

} else {
        esc_html_e('This is not a valid request', 'homey');
    }
?>