<?php
global $userID, $user_email;
?>
<div class="block">
    <div class="block-title">
        <div class="block-left">
            <h2 class="title"> <?php esc_html_e('Verify Your Information', 'homey'); ?> </h2>
        </div>
    </div>
    <div class="block-body">
        <div class="row">
            <div class="col-sm-9 col-xs-12">
                <label for="useremail"> <?php esc_html_e('Email Address', 'homey'); ?> </label>
                <div class="form-group">
                    <input class="form-control" name="useremail" id="useremail" value="<?php echo esc_attr($user_email); ?>" placeholder="your@email.com" disabled>
                </div>
            </div>
            <div class="col-sm-3 col-xs-12">
                <div class="verified">
                    <span class="btn btn-full-width" href="#"><i class="fa fa-check-circle-o" aria-hidden="true"></i> <?php esc_html_e('Verified', 'homey'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div><!-- block -->