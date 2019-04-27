<?php
/*-----------------------------------------------------------------------------------*/
/*   Upload picture for user profile using ajax
/*-----------------------------------------------------------------------------------*/
if( !function_exists( 'homey_user_picture_upload' ) ) {
    function homey_user_picture_upload( ) {

        // Verify if Nonce is valid
        $user_id = $_REQUEST['user_id'];
        $verify_nonce = $_REQUEST['verify_nonce'];
        if ( ! wp_verify_nonce( $verify_nonce, 'homey_upload_nonce' ) ) {
            echo json_encode( array( 'success' => false , 'reason' => 'Invalid request' ) );
            die;
        }

        $homey_user_image = $_FILES['homey_file_data_name'];
        $homey_wp_handle_upload = wp_handle_upload( $homey_user_image, array( 'test_form' => false ) );

        if ( isset( $homey_wp_handle_upload['file'] ) ) {
            $file_name  = basename( $homey_user_image['name'] );
            $file_type  = wp_check_filetype( $homey_wp_handle_upload['file'] );

            $uploaded_image_details = array(
                'guid'           => $homey_wp_handle_upload['url'],
                'post_mime_type' => $file_type['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $profile_attach_id      =   wp_insert_attachment( $uploaded_image_details, $homey_wp_handle_upload['file'] );
            $profile_attach_data    =   wp_generate_attachment_metadata( $profile_attach_id, $homey_wp_handle_upload['file'] );
            wp_update_attachment_metadata( $profile_attach_id, $profile_attach_data );

            $thumbnail_url = wp_get_attachment_image_src( $profile_attach_id, 'thumbnail' );
            homey_save_user_photo($user_id, $profile_attach_id);

            echo json_encode( array(
                'success'   => true,
                'url' => $thumbnail_url[0],
                'attachment_id'    => $profile_attach_id
            ));
            die;

        } else {
            echo json_encode( array( 'success' => false, 'reason' => 'Profile Photo upload failed!' ) );
            die;
        }

    }
}
add_action( 'wp_ajax_homey_user_picture_upload', 'homey_user_picture_upload' );

if( !function_exists('homey_save_user_photo')) {
    function homey_save_user_photo($user_id, $pic_id) {
        
        update_user_meta( $user_id, 'homey_author_picture_id', $pic_id );

    }
}


/*-----------------------------------------------------------------------------------*/
// Remove user photo
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_homey_delete_user_photo', 'homey_delete_user_photo' );
if( !function_exists('homey_delete_user_photo') ) {
    function homey_delete_user_photo() {

        $remove_attachment = false;

        $verify_nonce = $_REQUEST['verify_nonce'];
        if ( ! wp_verify_nonce( $verify_nonce, 'homey_upload_nonce' ) ) {
            echo json_encode( array( 'success' => false , 'reason' => 'Invalid request' ) );
            die;
        }

        if (isset($_POST['attach_id']) && isset($_POST['user_id'])) {
            $thumb_id = intval($_POST['attach_id']);
            $user_id = intval($_POST['user_id']);

            if ( $thumb_id > 0 && $user_id > 0 ) {
                delete_user_meta($user_id, 'homey_author_picture_id', $thumb_id);
                $remove_attachment = wp_delete_attachment($thumb_id);
            } elseif ($thumb_id > 0) {
                if( false == wp_delete_attachment( $thumb_id )) {
                    $remove_attachment = false;
                } else {
                    $remove_attachment = true;
                }
            }
        }

        echo json_encode(array(
            'success' => $remove_attachment,
        ));
        wp_die();

    }
}


/* ------------------------------------------------------------------------------
* Save user profile data
/------------------------------------------------------------------------------ */
add_action( 'wp_ajax_nopriv_homey_save_profile', 'homey_save_profile' );
add_action( 'wp_ajax_homey_save_profile', 'homey_save_profile' );

if( !function_exists('homey_save_profile') ):

    function homey_save_profile(){
        global $current_user;
        wp_get_current_user();
        $userID  = $current_user->ID;

        $prefix = 'homey_';

        $verify_nonce = $_REQUEST['security'];
        if ( ! wp_verify_nonce( $verify_nonce, 'homey_profile_nonce' ) ) {
            echo json_encode( array( 'success' => false , 'msg' => 'Invalid request' ) );
            die;
        }

        // Update GDPR
        if ( !empty( $_POST['gdpr_agreement'] ) ) {
            $gdpr_agreement = sanitize_text_field( $_POST['gdpr_agreement'] );
            update_user_meta( $userID, 'gdpr_agreement', $gdpr_agreement );
        } else {
            delete_user_meta( $userID, 'gdpr_agreement' );
        }

        if ( !empty( $_POST['firstname'] ) ) {
            $firstname = sanitize_text_field( $_POST['firstname'] );
            update_user_meta( $userID, 'first_name', $firstname );
        } else {
            delete_user_meta( $userID, 'first_name' );
        }

        if ( !empty( $_POST['lastname'] ) ) {
            $lastname = sanitize_text_field( $_POST['lastname'] );
            update_user_meta( $userID, 'last_name', $lastname );
        } else {
            delete_user_meta( $userID, 'last_name' );
        }

        if ( !empty( $_POST['bio'] ) ) {
            $bio = sanitize_text_field( $_POST['bio'] );
            update_user_meta( $userID, 'description', $bio );
        } else {
            delete_user_meta( $userID, 'description' );
        }

        if ( !empty( $_POST['native_language'] ) ) {
            $native_language = sanitize_text_field( $_POST['native_language'] );
            update_user_meta( $userID, $prefix.'native_language', $native_language );
        } else {
            delete_user_meta( $userID, $prefix.'native_language' );
        }

        if ( !empty( $_POST['other_language'] ) ) {
            $other_language = sanitize_text_field( $_POST['other_language'] );
            update_user_meta( $userID, $prefix.'other_language', $other_language );
        } else {
            delete_user_meta( $userID, $prefix.'other_language' );
        }

        if ( !empty( $_POST['street_address'] ) ) {
            $street_address = sanitize_text_field( $_POST['street_address'] );
            update_user_meta( $userID, $prefix.'street_address', $street_address );
        } else {
            delete_user_meta( $userID, $prefix.'street_address' );
        }

        if ( !empty( $_POST['apt_suit'] ) ) {
            $apt_suit = sanitize_text_field( $_POST['apt_suit'] );
            update_user_meta( $userID, $prefix.'apt_suit', $apt_suit );
        } else {
            delete_user_meta( $userID, $prefix.'apt_suit' );
        }

        if ( !empty( $_POST['zipcode'] ) ) {
            $zipcode = sanitize_text_field( $_POST['zipcode'] );
            update_user_meta( $userID, $prefix.'zipcode', $zipcode );
        } else {
            delete_user_meta( $userID, $prefix.'zipcode' );
        }

        if ( !empty( $_POST['country'] ) ) {
            $country = sanitize_text_field( $_POST['country'] );
            update_user_meta( $userID, $prefix.'country', $country );
        } else {
            delete_user_meta( $userID, $prefix.'country' );
        }

        if ( !empty( $_POST['state'] ) ) {
            $state = sanitize_text_field( $_POST['state'] );
            update_user_meta( $userID, $prefix.'state', $state );
        } else {
            delete_user_meta( $userID, $prefix.'state' );
        }

        if ( !empty( $_POST['city'] ) ) {
            $city = sanitize_text_field( $_POST['city'] );
            update_user_meta( $userID, $prefix.'city', $city );
        } else {
            delete_user_meta( $userID, $prefix.'city' );
        }

        if ( !empty( $_POST['neighborhood'] ) ) {
            $neighborhood = sanitize_text_field( $_POST['neighborhood'] );
            update_user_meta( $userID, $prefix.'neighborhood', $neighborhood );
        } else {
            delete_user_meta( $userID, $prefix.'neighborhood' );
        }

        if ( !empty( $_POST['em_contact_name'] ) ) {
            $em_contact_name = sanitize_text_field( $_POST['em_contact_name'] );
            update_user_meta( $userID, $prefix.'em_contact_name', $em_contact_name );
        } else {
            delete_user_meta( $userID, $prefix.'em_contact_name' );
        }

        if ( !empty( $_POST['em_relationship'] ) ) {
            $em_relationship = sanitize_text_field( $_POST['em_relationship'] );
            update_user_meta( $userID, $prefix.'em_relationship', $em_relationship );
        } else {
            delete_user_meta( $userID, $prefix.'em_relationship' );
        }

        if ( !empty( $_POST['em_email'] ) ) {
            $em_email = sanitize_text_field( $_POST['em_email'] );
            update_user_meta( $userID, $prefix.'em_email', $em_email );
        } else {
            delete_user_meta( $userID, $prefix.'em_email' );
        }

        if ( !empty( $_POST['em_phone'] ) ) {
            $em_phone = sanitize_text_field( $_POST['em_phone'] );
            update_user_meta( $userID, $prefix.'em_phone', $em_phone );
        } else {
            delete_user_meta( $userID, $prefix.'em_phone' );
        }

        // Update facebook
        if ( !empty( $_POST['facebook'] ) ) {
            $facebook = sanitize_text_field( $_POST['facebook'] );
            update_user_meta( $userID, $prefix.'author_facebook', $facebook );
        } else {
            delete_user_meta( $userID, $prefix.'author_facebook' );
        }

        // Update twitter
        if ( !empty( $_POST['twitter'] ) ) {
            $twitter = sanitize_text_field( $_POST['twitter'] );
            update_user_meta( $userID, $prefix.'author_twitter', $twitter );
        } else {
            delete_user_meta( $userID, $prefix.'author_twitter' );
        }

        // Update linkedin
        if ( !empty( $_POST['linkedin'] ) ) {
            $linkedin = sanitize_text_field( $_POST['linkedin'] );
            update_user_meta( $userID, $prefix.'author_linkedin', $linkedin );
        } else {
            delete_user_meta( $userID, $prefix.'author_linkedin' );
        }

        // Update instagram
        if ( !empty( $_POST['instagram'] ) ) {
            $instagram = sanitize_text_field( $_POST['instagram'] );
            update_user_meta( $userID, $prefix.'author_instagram', $instagram );
        } else {
            delete_user_meta( $userID, $prefix.'author_instagram' );
        }

        // Update pinterest
        if ( !empty( $_POST['pinterest'] ) ) {
            $pinterest = sanitize_text_field( $_POST['pinterest'] );
            update_user_meta( $userID, $prefix.'author_pinterest', $pinterest );
        } else {
            delete_user_meta( $userID, $prefix.'author_pinterest' );
        }

        // Update youtube
        if ( !empty( $_POST['youtube'] ) ) {
            $youtube = sanitize_text_field( $_POST['youtube'] );
            update_user_meta( $userID, $prefix.'author_youtube', $youtube );
        } else {
            delete_user_meta( $userID, $prefix.'author_youtube' );
        }

        // Update vimeo
        if ( !empty( $_POST['vimeo'] ) ) {
            $vimeo = sanitize_text_field( $_POST['vimeo'] );
            update_user_meta( $userID, $prefix.'author_vimeo', $vimeo );
        } else {
            delete_user_meta( $userID, $prefix.'author_vimeo' );
        }

        // Update Googleplus
        if ( !empty( $_POST['googleplus'] ) ) {
            $googleplus = sanitize_text_field( $_POST['googleplus'] );
            update_user_meta( $userID, $prefix.'author_googleplus', $googleplus );
        } else {
            delete_user_meta( $userID, $prefix.'author_googleplus' );
        }
        

        // Update email
        if( !empty( $_POST['useremail'] ) ) {
            $useremail = sanitize_email( $_POST['useremail'] );
            $useremail = is_email( $useremail );
            if( !$useremail ) {
                echo json_encode( array( 'success' => false, 'msg' => esc_html__('The Email you entered is not valid. Please try again.', 'homey') ) );
                wp_die();
            } else {
                $email_exists = email_exists( $useremail );
                if( $email_exists ) {
                    if( $email_exists != $userID ) {
                        echo json_encode( array( 'success' => false, 'msg' => esc_html__('This Email is already used by another user. Please try a different one.', 'homey') ) );
                        wp_die();
                    }
                } else {
                    $return = wp_update_user( array ('ID' => $userID, 'user_email' => $useremail, 'display_name' => $display_name ) );
                    if ( is_wp_error( $return ) ) {
                        $error = $return->get_error_message();
                        echo esc_attr( $error );
                        wp_die();
                    }
                }
            }
        }
        wp_update_user( array ('ID' => $userID, 'display_name' => $_POST['display_name'] ) );
        
        echo json_encode( array( 'success' => true, 'msg' => esc_html__('Profile updated', 'homey') ) );
        die();
    }
endif;


/* ------------------------------------------------------------------------------
* Ajax Reset Password function
/------------------------------------------------------------------------------ */
add_action( 'wp_ajax_nopriv_homey_ajax_password_reset', 'homey_ajax_password_reset' );
add_action( 'wp_ajax_homey_ajax_password_reset', 'homey_ajax_password_reset' );

if( !function_exists('homey_ajax_password_reset') ):
    function homey_ajax_password_reset () {
        global $current_user;
        wp_get_current_user();
        $userID         = $current_user->ID;
        $allowed_html   = array();

        $newpass        = wp_kses( $_POST['newpass'], $allowed_html );
        $confirmpass    = wp_kses( $_POST['confirmpass'], $allowed_html );

        if( $newpass == '' || $confirmpass == '' ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('New password or confirm password is blank', 'homey') ) );
            die();
        }
        if( $newpass != $confirmpass ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Passwords do not match', 'homey') ) );
            die();
        }

        check_ajax_referer( 'homey_pass_ajax_nonce', 'homey-security-pass' );

        $user = get_user_by( 'id', $userID );
        if( $user ) {
            wp_set_password( $newpass, $userID );
            echo json_encode( array( 'success' => true, 'msg' => esc_html__('Password Updated', 'homey') ) );
        } else {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Something went wrong', 'homey') ) );
        }
        die();
    }
endif; // end homey_ajax_password_reset


if(!function_exists('homey_usermeta')) {
    function homey_usermeta($user_id) {
        $user_array = array();
        $user = get_userdata($user_id);
        
        $user_array['username'] = $user->user_login;
        $user_array['email'] = $user->user_email;
        $user_array['register'] = $user->user_registered;
        $user_array['url'] = $user->user_url;
        $user_array['activation_key'] = $user->user_activation_key;
        $user_array['display_name'] = $user->display_name;

        return $user_array;
    }
}
/*-----------------------------------------------------------------------------------*/
// Get listing author
/*-----------------------------------------------------------------------------------*/
if( !function_exists('homey_get_author') ) {
    function homey_get_author($w = '36', $h = '36', $classes = 'img-responsive img-circle') {
        
        global $homey_local;
        $author = array();
        $prefix = 'homey_';
        $comma = '';
        $maximumPoints = 100;
        $point = 0;

        $ID = get_the_author_meta( 'ID' );

        $author['is_photo'] = false;
        $author['is_email'] = false;

        $author[ 'name' ] = get_the_author();
        $author[ 'email' ] = get_the_author_meta( 'email' );
        $author[ 'bio' ] = get_the_author_meta( 'description' );

        $custom_img = get_template_directory_uri().'/images/avatar.png';

        $author_picture_id = get_the_author_meta( 'homey_author_picture_id' , get_the_author_meta( 'ID' ) );

        if( !empty( $author_picture_id ) ) {
            $point+=50;

            $author_picture_id = intval( $author_picture_id );
            if ( $author_picture_id ) {

                $photo = wp_get_attachment_image( $author_picture_id, array($w, $h), "", array( "class" => $classes ) );
                
                if(!empty($photo)) {
                    $author[ 'photo' ] = $photo;
                } else {
                    $author[ 'photo' ] = '<img src="'.esc_url($custom_img).'" class="'.esc_attr($classes).'" alt="'.esc_attr($author[ 'name' ]).'" width="'.esc_attr($w).'" height="'.esc_attr($h).'">';
                }
                $author['is_photo'] = true;
            }
        } else {
            $author[ 'photo' ] = '<img src="'.esc_url($custom_img).'" class="'.esc_attr($classes).'" alt="'.esc_attr($author[ 'name' ]).'" width="'.esc_attr($w).'" height="'.esc_attr($h).'">';
        }


        
        

        $author[ 'listing_count' ] = count_user_posts( $ID , 'listing' );
        $native_language  = get_the_author_meta( $prefix.'native_language');
        $other_language  =  get_the_author_meta( $prefix.'other_language');
        if(!empty($other_language) && !empty($native_language)) {
            $comma = ', ';
        }

        $author['facebook']     =  get_the_author_meta( 'homey_author_facebook');
        $author['twitter']      =  get_the_author_meta( 'homey_author_twitter');
        $author['linkedin']     =  get_the_author_meta( 'homey_author_linkedin');
        $author['pinterest']    =  get_the_author_meta( 'homey_author_pinterest');
        $author['instagram']    =  get_the_author_meta( 'homey_author_instagram');
        $author['googleplus']   =  get_the_author_meta( 'homey_author_googleplus');
        $author['youtube']      =  get_the_author_meta( 'homey_author_youtube');
        $author['vimeo']        =  get_the_author_meta( 'homey_author_vimeo');        
        $author[ 'link' ] = get_author_posts_url( get_the_author_meta( 'ID' ) );
        $author[ 'address' ] = get_the_author_meta( $prefix.'street_address' , get_the_author_meta( 'ID' ) );
        $author[ 'country' ] = get_the_author_meta( $prefix.'country' , get_the_author_meta( 'ID' ) );
        $author[ 'state' ] = get_the_author_meta( $prefix.'state' , get_the_author_meta( 'ID' ) );
        $author[ 'city' ] = get_the_author_meta( $prefix.'city' , get_the_author_meta( 'ID' ) );
        $author[ 'area' ] = get_the_author_meta( $prefix.'area' , get_the_author_meta( 'ID' ) );
        $author[ 'languages' ] = esc_attr($native_language.$comma.$other_language);

        if(!empty($author[ 'email' ])) {
            $point+=50;
            $author['is_email'] = true;
        }

        $percentage = ($point*$maximumPoints)/100;
        $author[ 'profile_status' ] = $percentage."%";

        return $author;
    }
}


if( !function_exists('homey_get_author_by_id') ) {
    function homey_get_author_by_id($w = '36', $h = '36', $classes = 'img-responsive img-circle', $ID) {
        
        global $homey_local;
        $author = array();
        $prefix = 'homey_';
        $comma = ' ';
        $maximumPoints = 100;
        $point = 0;

        $author['is_photo'] = false;
        $author['is_email'] = false;

        $custom_img = get_template_directory_uri().'/images/avatar.png';

        $author_picture_id = get_the_author_meta( 'homey_author_picture_id' , $ID );

        $author[ 'name' ] = get_the_author_meta( 'display_name' , $ID );
        $author[ 'email' ] = get_the_author_meta( 'email', $ID );
        $author['phone'] = get_the_author_meta( 'homey_em_phone', $ID );
        $author[ 'bio' ] = get_the_author_meta( 'description' , $ID );

        if( !empty( $author_picture_id ) ) {
            $point+=50;

            $author_picture_id = intval( $author_picture_id );
            if ( $author_picture_id ) {

                $photo = wp_get_attachment_image( $author_picture_id, array($w, $h), "", array( "class" => $classes ) );
                
                if(!empty($photo)) {
                    $author[ 'photo' ] = $photo;
                } else {
                    $author[ 'photo' ] = '<img src="'.esc_url($custom_img).'" class="'.esc_attr($classes).'" alt="'.esc_attr($author[ 'name' ]).'" width="'.esc_attr($w).'" height="'.esc_attr($h).'">';
                }

                $author['is_photo'] = true;
            }
        } else {
            $author[ 'photo' ] = '<img src="'.esc_url($custom_img).'" class="'.esc_attr($classes).'" alt="'.esc_attr($author[ 'name' ]).'" width="'.esc_attr($w).'" height="'.esc_attr($h).'">';
        }

        $author[ 'listing_count' ] = count_user_posts( $ID , 'listing' );
        $native_language  = get_the_author_meta( $prefix.'native_language' , $ID );
        $other_language  =  get_the_author_meta( $prefix.'other_language' , $ID );
        if(!empty($other_language) && !empty($native_language)) {
            $comma = ', ';
        }

        $author['facebook']     =  get_the_author_meta( 'homey_author_facebook' , $ID );
        $author['twitter']      =  get_the_author_meta( 'homey_author_twitter' , $ID );
        $author['linkedin']     =  get_the_author_meta( 'homey_author_linkedin' , $ID );
        $author['pinterest']    =  get_the_author_meta( 'homey_author_pinterest' , $ID );
        $author['instagram']    =  get_the_author_meta( 'homey_author_instagram' , $ID );
        $author['googleplus']   =  get_the_author_meta( 'homey_author_googleplus' , $ID );
        $author['youtube']      =  get_the_author_meta( 'homey_author_youtube' , $ID );
        $author['vimeo']        =  get_the_author_meta( 'homey_author_vimeo' , $ID );
        $author[ 'link' ] = get_author_posts_url( $ID );
        $author[ 'address' ] = get_the_author_meta( $prefix.'street_address' , $ID );
        $author[ 'country' ] = get_the_author_meta( $prefix.'country' , $ID);
        $author[ 'state' ] = get_the_author_meta( $prefix.'state' , $ID);
        $author[ 'city' ] = get_the_author_meta( $prefix.'city' , $ID);
        $author[ 'area' ] = get_the_author_meta( $prefix.'area' , $ID);
        $author[ 'languages' ] = esc_attr($native_language.$comma.$other_language);

        if(!empty($author[ 'email' ])) {
            $point+=50;

            $author['is_email'] = true;
        }

        $percentage = ($point*$maximumPoints)/100;
        $author[ 'profile_status' ] = $percentage."%";

        return $author;
    }
}


if(!function_exists('homey_reservation_count')) {
    function homey_reservation_count($user_id) {
        $args = array(
            'post_type'        =>  'homey_reservation',
            'posts_per_page'    => -1,
        );

        if( homey_is_renter() ) {
            $meta_query[] = array(
                'key' => 'listing_renter',
                'value' => $user_id,
                'compare' => '='
            );
            $args['meta_query'] = $meta_query;
        } else {
            $meta_query[] = array(
                'key' => 'listing_owner',
                'value' => $user_id,
                'compare' => '='
            );
            $args['meta_query'] = $meta_query;
        }

        $Qry = new WP_Query($args);
        $founds = $Qry->found_posts;

        return $founds;

    }
}



/**
 * Show custom user profile fields
 * @param  obj $user The user object.
 * @return void
 */
function homey_custom_user_profile_fields($user) {
?>
    
    <h2><?php echo esc_html__('Social Info', 'homey'); ?></h2>
    <table class="form-table">
        <tbody>
            <tr class="user-homey_author_facebook-wrap">
                <th><label for="homey_author_facebook"><?php echo esc_html__('Facebook', 'homey'); ?></label></th>
                <td><input type="text" name="homey_author_facebook" id="homey_author_facebook" value="<?php echo esc_url( get_the_author_meta( 'homey_author_facebook', $user->ID ) ); ?>" class="regular-text"></td>
            </tr>
            <tr class="user-homey_author_linkedin-wrap">
                <th><label for="homey_author_linkedin"><?php echo esc_html__('LinkedIn', 'homey'); ?></label></th>
                <td><input type="text" name="homey_author_linkedin" id="homey_author_linkedin" value="<?php echo esc_url( get_the_author_meta( 'homey_author_linkedin', $user->ID ) ); ?>" class="regular-text"></td>
            </tr>
            <tr class="user-homey_author_twitter-wrap">
                <th><label for="homey_author_twitter"><?php echo esc_html__('Twitter', 'homey'); ?></label></th>
                <td><input type="text" name="homey_author_twitter" id="homey_author_twitter" value="<?php echo esc_url( get_the_author_meta( 'homey_author_twitter', $user->ID ) ); ?>" class="regular-text"></td>
            </tr>
            <tr class="user-homey_author_pinterest-wrap">
                <th><label for="homey_author_pinterest"><?php echo esc_html__('Pinterest', 'homey'); ?></label></th>
                <td><input type="text" name="homey_author_pinterest" id="homey_author_pinterest" value="<?php echo esc_url( get_the_author_meta( 'homey_author_pinterest', $user->ID ) ); ?>" class="regular-text"></td>
            </tr>
            <tr class="user-homey_author_instagram-wrap">
                <th><label for="homey_author_instagram"><?php echo esc_html__('Instagram', 'homey'); ?></label></th>
                <td><input type="text" name="homey_author_instagram" id="homey_author_instagram" value="<?php echo esc_url( get_the_author_meta( 'homey_author_instagram', $user->ID ) ); ?>" class="regular-text"></td>
            </tr>
            <tr class="user-homey_author_youtube-wrap">
                <th><label for="homey_author_youtube"><?php echo esc_html__('Youtube', 'homey'); ?></label></th>
                <td><input type="text" name="homey_author_youtube" id="homey_author_youtube" value="<?php echo esc_url( get_the_author_meta( 'homey_author_youtube', $user->ID ) ); ?>" class="regular-text"></td>
            </tr>
            <tr class="user-homey_author_vimeo-wrap">
                <th><label for="homey_author_vimeo"><?php echo esc_html__('Vimeo', 'homey'); ?></label></th>
                <td><input type="text" name="homey_author_vimeo" id="homey_author_vimeo" value="<?php echo esc_url( get_the_author_meta( 'homey_author_vimeo', $user->ID ) ); ?>" class="regular-text"></td>
            </tr>
            <tr class="user-homey_author_googleplus-wrap">
                <th><label for="homey_author_googleplus"><?php echo esc_html__('Google Plus', 'homey'); ?></label></th>
                <td><input type="text" name="homey_author_googleplus" id="homey_author_googleplus" value="<?php echo esc_url( get_the_author_meta( 'homey_author_googleplus', $user->ID ) ); ?>" class="regular-text"></td>
            </tr>
        </tbody>
    </table>

<?php
}
add_action('show_user_profile', 'homey_custom_user_profile_fields');
add_action('edit_user_profile', 'homey_custom_user_profile_fields');


if( !function_exists('homey_update_extra_profile_fields') ) {
    function homey_update_extra_profile_fields($user_id)
    {
        if (current_user_can('edit_user', $user_id))


        /*
         * Social Info
        --------------------------------------------------------------------------------*/
        update_user_meta($user_id, 'homey_author_facebook', sanitize_text_field($_POST['homey_author_facebook']));
        update_user_meta($user_id, 'homey_author_linkedin', sanitize_text_field($_POST['homey_author_linkedin']));
        update_user_meta($user_id, 'homey_author_twitter', sanitize_text_field($_POST['homey_author_twitter']));
        update_user_meta($user_id, 'homey_author_pinterest', sanitize_text_field($_POST['homey_author_pinterest']));
        update_user_meta($user_id, 'homey_author_instagram', sanitize_text_field($_POST['homey_author_instagram']));
        update_user_meta($user_id, 'homey_author_youtube', sanitize_text_field($_POST['homey_author_youtube']));
        update_user_meta($user_id, 'homey_author_vimeo', sanitize_text_field($_POST['homey_author_vimeo']));
        update_user_meta($user_id, 'homey_author_googleplus', sanitize_text_field($_POST['homey_author_googleplus']));

    }
}
add_action('edit_user_profile_update', 'homey_update_extra_profile_fields');
add_action('personal_options_update', 'homey_update_extra_profile_fields');





