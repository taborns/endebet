<?php
get_header();

global $wp_query, $homey_local, $homey_prefix;
$current_author = $wp_query->get_queried_object();
$author_id = $current_author->ID;
$author_meta = get_user_meta( $author_id );

$author = homey_get_author_by_id('70', '70', 'img-circle media-object avatar', $author_id);
$facebook = $author['facebook'];
$twitter = $author['twitter'];
$linkedin = $author['linkedin'];
$pinterest = $author['pinterest'];
$instagram = $author['instagram'];
$googleplus = $author['googleplus'];
$youtube = $author['youtube'];
$vimeo = $author['vimeo'];

$reviews = homey_get_host_reviews($author_id);

$host_email = is_email( $author['email'] );

$enable_forms_gdpr = homey_option('enable_forms_gdpr');
$forms_gdpr_text = homey_option('forms_gdpr_text');
?>

<section class="main-content-area user-profile host-profile">
    <div class="container">
        <div class="host-section clearfix">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                    <div class="block">
                        <div class="block-head">
                            <div class="media">
                                <div class="media-left">
                                   <?php echo ''.$author['photo']; ?>
                                </div>
                                <div class="media-body">
                                    <h2 class="title"><span><?php echo esc_attr($homey_local['pr_iam']); ?></span> <?php echo esc_attr($author['name']); ?></h2>
                                    
                                    <?php if(!empty($author['country'])) { ?>
                                        <address><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_attr($author['country']); ?></address>
                                    <?php } ?>
                                
                                </div>
                            </div>
                        </div><!-- block-head -->
                        <div class="block-body">

                            <p><?php echo esc_attr($author['bio']); ?></p>

                            <div class="profile-social-icons">
                                <?php echo esc_attr($homey_local['pr_followme']); ?>: 
                                <?php if(!empty($facebook)) { ?>
                                    <a class="btn-facebook" href="<?php echo esc_url($facebook); ?>"><i class="fa fa-facebook"></i></a>
                                    <?php } ?>

                                    <?php if(!empty($twitter)) { ?>
                                    <a class="btn-twitter" href="<?php echo esc_url($twitter); ?>"><i class="fa fa-twitter"></i></a>
                                    <?php } ?>

                                    <?php if(!empty($googleplus)) { ?>
                                    <a class="btn-google-plus" href="<?php echo esc_url($googleplus); ?>"><i class="fa fa-google-plus"></i></a>
                                    <?php } ?>

                                    <?php if(!empty($instagram)) { ?>
                                    <a class="btn-instagram" href="<?php echo esc_url($instagram); ?>"><i class="fa fa-instagram"></i></a>
                                    <?php } ?>

                                    <?php if(!empty($pinterest)) { ?>
                                    <a class="btn-pinterest" href="<?php echo esc_url($pinterest); ?>"><i class="fa fa-pinterest"></i></a>
                                    <?php } ?>

                                    <?php if(!empty($linkedin)) { ?>
                                    <a class="btn-linkedin" href="<?php echo esc_url($linkedin); ?>"><i class="fa fa-linkedin"></i></a>
                                    <?php } ?>

                                    <?php if(!empty($youtube)) { ?>
                                    <a class="btn-youtube" href="<?php echo esc_url($youtube); ?>"><i class="fa fa-youtube"></i></a>
                                    <?php } ?>

                                    <?php if(!empty($vimeo)) { ?>
                                    <a class="btn-vimeo" href="<?php echo esc_url($vimeo); ?>"><i class="fa fa-vimeo"></i></a>
                                    <?php } ?>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                    <dl>
                                        <dt><?php echo esc_attr($homey_local['pr_lang']); ?></dt>
                                        <dd><?php echo esc_attr($author['languages']);?></dd>
                                    </dl>    
                                </div>
                                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                    <dl>
                                        <dt><?php echo esc_attr($homey_local['pr_profile_status']); ?> </dt>
                                        <dd class="text-success">
                                            <i class="fa fa-check-circle-o"></i> 
                                            <?php echo esc_attr($homey_local['pr_verified']); ?>
                                        </dd>
                                    </dl>    
                                </div>
                                <?php if($reviews['is_host_have_reviews']) { ?>
                                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                    <dl>
                                        <dt><?php echo esc_attr($homey_local['pr_h_rating']); ?></dt>
                                        <dd>
                                            <div class="rating">
                                                <?php echo ''.$reviews['host_rating']; ?>
                                            </div>
                                        </dd>
                                    </dl>    
                                </div>
                                <?php } ?>
                            </div>
                        </div><!-- block-body -->
                    </div><!-- block -->

                </div><!-- col-xs-12 col-sm-12 col-md-8 col-lg-8 -->

                <?php if($host_email) { ?>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <div class="host-contact-form">
                        <div class="block">
                            <div class="block-body">
                                <h3 class="title mb-20"><?php echo esc_html__('Contact me', 'homey'); ?></h3>
                                <div class="review-form-block">
                                    <form class="form-msg">
                                        <input type="hidden" id="target_email" name="target_email" value="<?php echo antispambot($host_email); ?>">
                                        <input type="hidden" name="host_detail_ajax_nonce" id="host_detail_ajax_nonce" value="<?php echo wp_create_nonce('host-contact-nonce'); ?>"/>
                                        <input type="hidden" name="action" value="homey_contact_host" />

                                        <div class="form-group">
                                            
                                            <input type="text" name="name" class="form-control" placeholder="<?php echo esc_attr($homey_local['fname_plac']); ?>">
                                        </div>
                                        <div class="form-group">
                                            
                                            <input type="email" name="email" class="form-control" placeholder="<?php echo esc_attr($homey_local['email_plac']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="phone" class="form-control" placeholder="<?php echo esc_attr($homey_local['con_phone']); ?>">
                                        </div>

                                        <div class="form-group">
                                            
                                            <textarea class="form-control" name="message" placeholder="<?php echo esc_attr($homey_local['message_plac']); ?>" rows="5"></textarea>
                                        </div>

                                        <?php if($enable_forms_gdpr != 0) { ?>
                                        <div class="form-group checkbox">
                                            <label>
                                                <input name="privacy_policy" type="checkbox">
                                                <?php echo wp_kses($forms_gdpr_text, homey_allowed_html()); ?>
                                            </label>
                                        </div>
                                        <?php } ?>

                                        <?php get_template_part('template-parts/google', 'reCaptcha'); ?>

                                        <button id="host_detail_contact" class="btn btn-primary btn-full-width"><?php echo esc_html__('Send Message', 'homey'); ?></button>
                                    </form>
                                </div>
                                <div id="form_messages"></div>
                            </div><!-- block-body -->
                        </div>
                    </div>
                </div><!-- col-xs-12 col-sm-12 col-md-4 col-lg-4 -->
                <?php } ?>

            </div>
        </div><!-- host-section -->

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">    
                <div class="host-profile-tabs">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#listings" aria-controls="listings" role="tab" data-toggle="tab"><?php echo esc_attr($homey_local['pr_listing_label']); ?></a></li>
                        <li role="presentation"><a href="#reviews" aria-controls="reviews" role="tab" data-toggle="tab"><?php echo esc_attr($homey_local['rating_reviews_label']); ?></a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade in active" id="listings">
                            <div class="host-property-section">
                                <?php
                                $author_args = array(
                                    'post_type' => 'listing',
                                    'posts_per_page' => '7',
                                    'author' => $author_id
                                );

                                $wp_query = new WP_Query( $author_args );

                                if ( $wp_query->have_posts() ) :
                                    $listing_founds = $wp_query->found_posts;
                                ?>
                                <div id="listings_module_section" class="listing-wrap host-listing-wrap">
                                    <div id="module_listings" class="item-row item-list-view">
                                        <?php
                                        while ( $wp_query->have_posts() ) : $wp_query->the_post();

                                            get_template_part('template-parts/listing/listing-item');

                                        endwhile;
                                        ?>
                                    </div>

                                    <?php if($listing_founds > 7) { ?>
                                    <div class="homey-loadmore loadmore text-center">
                                        <a
                                        data-paged="2" 
                                        data-limit="7" 
                                        data-style="list"  
                                        data-author="yes" 
                                        data-authorid="<?php echo esc_attr($author_id); ?>"
                                        data-country=""  
                                        data-state="" 
                                        data-city="" 
                                        data-area="" 
                                        data-featured="" 
                                        data-offset=""
                                        data-sortby=""
                                        href="#" 
                                        class="btn btn-primary btn-long">
                                            <i id="spinner-icon" class="fa fa-spinner fa-pulse fa-spin fa-fw" style="display: none;"></i>
                                            <?php echo esc_attr($homey_local['loadmore_btn']); ?>
                                        </a>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php
                                wp_reset_postdata();
                                else:
                                            
                                endif;
                                ?>
                            </div><!-- host-property-section -->
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="reviews">
                            <div class="host-rating-section">
                                <div class="block">
                                    <div class="block-body">
                                        <div class="reviews-section">
                                            <ul class="list-unstyled">
                                                <?php echo ''.$reviews['reviews_data']; ?>
                                            </ul>
                                        </div><!-- reviews-section -->
                                    </div><!-- block-body -->
                                </div><!-- block -->
                            </div><!-- host-rating-section -->
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="posts">
                            <div class="block">
                                <div class="block-body">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- host-profile-tabs -->
            </div><!-- col-xs-12 col-sm-12 col-md-12 col-lg-12 -->
        </div>
    </div>
</section><!-- main-content-area -->

<?php get_footer(); ?>
