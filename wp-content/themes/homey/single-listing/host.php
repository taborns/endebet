<?php
global $post, $homey_prefix, $homey_local, $listing_author;

$reviews = homey_get_host_reviews(get_the_author_meta( 'ID' ));
?>
<div id="host-section" class="host-section">
    <div class="block">
        <div class="block-head">
            <div class="media">
                <div class="media-left">
                    <?php echo ''.$listing_author['photo']; ?>
                </div>
                <div class="media-body">
                    <h2 class="title"><?php echo esc_attr(homey_option('sn_hosted_by')); ?> <span><?php echo esc_attr($listing_author['name']); ?></span></h2>
                    <address><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_attr($listing_author['country']); ?></address>
                </div>
            </div>
        </div><!-- block-head -->
        <div class="block-body">
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <dl>
                        <dt><?php echo esc_attr(homey_option('sn_pr_lang')); ?></dt>
                        <dd><?php echo esc_attr($listing_author['languages']); ?></dd>
                    </dl>    
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <dl>
                        <dt><?php echo esc_attr(homey_option('sn_pr_profile_status')); ?></dt>
                        <dd class="text-success"><i class="fa fa-check-circle-o"></i> <?php echo esc_attr(homey_option('sn_pr_verified')); ?></dd>
                    </dl>    
                </div>

                <?php if($reviews['is_host_have_reviews']) { ?>
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <dl>
                        <dt><?php echo esc_attr(homey_option('sn_pr_h_rating')); ?></dt>
                        <dd>
                            <div class="rating">
                                <?php echo ''.$reviews['host_rating']; ?>
                            </div>
                        </dd>
                    </dl>    
                </div>
                <?php } ?>
                
            </div>
            <div class="host-section-buttons">
                <a href="#" data-toggle="modal" data-target="#modal-contact-host" class="btn btn-grey-outlined btn-half-width"><?php echo esc_attr(homey_option('sn_pr_cont_host')); ?></a>
                <a href="<?php echo esc_url($listing_author['link']); ?>" class="btn btn-grey-outlined btn-half-width">
                    <?php echo esc_attr(homey_option('sn_view_profile')); ?>        
                </a>
            </div><!-- block-body -->
        </div><!-- block-body -->

    </div><!-- block -->
</div><!-- host-section -->