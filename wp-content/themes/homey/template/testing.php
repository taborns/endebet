<?php
/**
 * Template Name: Unit Testing
 */
get_header();

/*$start = "11:00";
$end = "13:30";

$tStart = strtotime($start);
$tEnd = strtotime($end);
$tNow = $tStart;

while($tNow <= $tEnd){
  echo date("H:i",$tNow)."<br/>";
  $tNow = strtotime('+30 minutes',$tNow);
}*/

/*$start=strtotime('9:00');
$end=strtotime('17:00');
for ($halfhour=$start;$halfhour<=$end;$halfhour=$halfhour+30*60) {
    printf('<option value="%s">%s</option>',date('H:i',$halfhour),date('g:i a',$halfhour));
}*/

//print date_default_timezone_get();
$dt = new DateTime();
print $dt->getTimeZone()->getName();


    function homey_get_booking_pending_days_test($listing_id) {
        $now = time();
        //$daysAgo = $now-3*24*60*60;
		$daysAgo = $now-1*24*60*60;

        $args = array(
            'post_type'        => 'homey_reservation',
            'post_status'      => 'any',
            'posts_per_page'   => -1,
            'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key'       => 'reservation_listing_id',
                        'value'     => $listing_id,
                        'type'      => 'NUMERIC',
                        'compare'   => '='
                    ),
                    array(
                        'key'       => 'reservation_status',
                        'value'     => 'declined',
                        'type'      => 'CHAR',
                        'compare'   => '!='
                    )
                )
            );
        
        $pending_dates_array = get_post_meta($listing_id, 'reservation_pending_dates', true );
        
        if( !is_array($pending_dates_array) || empty($pending_dates_array) ) {
            $pending_dates_array  = array();
        }
        
        $wpQry = new WP_Query($args);
        
        if ($wpQry->have_posts()) {    

            while ($wpQry->have_posts()): $wpQry->the_post();

                $resID = get_the_ID();
                
                $check_in_date  = get_post_meta( $resID, 'reservation_checkin_date', true );
                $check_out_date = get_post_meta( $resID, 'reservation_checkout_date', true );

                $unix_time_start = strtotime ($check_in_date);
                //$unix_time_end   = strtotime ($check_out_date);
                
                if ($unix_time_start > $daysAgo) {

                	//$pending_dates_array[$unix_time_start]=$unix_time_end;

                	$check_in       =   new DateTime($check_in_date);
                    $check_in_unix  =   $check_in->getTimestamp();
                    $check_out      =   new DateTime($check_out_date);
                    $check_out_unix =   $check_out->getTimestamp();

                    
                    $pending_dates_array[$check_in_unix] = $resID;

                    $check_in_unix =   $check_in->getTimestamp();

                    while ($check_in_unix < $check_out_unix){
                    
                        $pending_dates_array[$check_in_unix] = $resID;

                        //$check_in->modify('+1 hour');
                        $check_in->modify('+30 minutes');
                        $check_in_unix =   $check_in->getTimestamp();
                    }

                    /*$check_in       =   new DateTime($check_in_date);
                    $check_in_unix  =   $check_in->getTimestamp();
                    $check_out      =   new DateTime($check_out_date);
                    $check_out_unix =   $check_out->getTimestamp();

                    
                    $pending_dates_array[$check_in_unix] = $resID;

                    $check_in_unix =   $check_in->getTimestamp();

                    while ($check_in_unix < $check_out_unix){
                    
                        $pending_dates_array[$check_in_unix] = $resID;

                        $check_in->modify('tomorrow');
                        $check_in_unix =   $check_in->getTimestamp();
                    } */         
                }
            endwhile;
            wp_reset_postdata();
        }        
      
        return $pending_dates_array;
        
    }


	function homey_add_reservation_test() {
        global $current_user;
        $current_user = wp_get_current_user();
        $userID       = $current_user->ID;
        $local = homey_get_localization();
        $allowded_html = array();
        $reservation_meta = array();

        $listing_id = 338;//intval($_POST['listing_id']);
        $listing_owner_id  =  get_post_field( 'post_author', $listing_id );
        $check_in_date     =  '2019-03-16 15:00';//wp_kses ( $_POST['check_in_date'], $allowded_html );
        $check_out_date    =  '2019-03-16 18:00';//wp_kses ( $_POST['check_out_date'], $allowded_html );
        $guests   =  '2';//intval($_POST['guests']);
        $title = $local['reservation_text'];

        $owner = homey_usermeta($listing_owner_id);
        $owner_email = $owner['email'];
        
        if ( !is_user_logged_in() || $userID === 0 ) {   
            echo json_encode( 
                array( 
                    'success' => false, 
                    'message' => $local['login_for_reservation'] 
                ) 
             );
             wp_die();
        }

        if($userID == $listing_owner_id) {
            echo json_encode( 
                array( 
                    'success' => false, 
                    'message' => $local['own_listing_error'] 
                ) 
             );
             wp_die();
        }
        
        if(!homey_is_renter()) {
            echo json_encode( 
                array( 
                    'success' => false, 
                    'message' => $local['host_user_cannot_book'] 
                ) 
             );
             wp_die();
        }

        //check security
        /*$nonce = $_REQUEST['security'];
        if ( ! wp_verify_nonce( $nonce, 'reservation-security-nonce' ) ) {

             echo json_encode( 
                array( 
                    'success' => false, 
                    'message' => $local['security_check_text'] 
                ) 
             );
             wp_die();
        }*/

        $check_availability = check_booking_availability_test($check_in_date, $check_out_date, $listing_id, $guests);
        $is_available = $check_availability['success'];
        $check_message = $check_availability['message'];

        if($is_available) {
            $prices_array = homey_get_prices($check_in_date, $check_out_date, $listing_id, $guests);
            
            $reservation_meta['no_of_days'] = $prices_array['days_count'];
            $reservation_meta['additional_guests'] = $prices_array['additional_guests'];

            $upfront_payment = $prices_array['upfront_payment'];
            $balance = $prices_array['balance'];
            $total_price = $prices_array['total_price'];

            $reservation_meta['check_in_date'] = $check_in_date;
            $reservation_meta['check_out_date'] = $check_out_date;
            $reservation_meta['guests'] = $guests;
            $reservation_meta['listing_id'] = $listing_id;
            $reservation_meta['upfront'] = $upfront_payment;
            $reservation_meta['balance'] = $balance;
            $reservation_meta['total'] = $total_price;

            $reservation = array(
                'post_title'    => $title,
                'post_status'   => 'publish', 
                'post_type'     => 'homey_reservation' ,
                'post_author'   => $userID
            );
            $reservation_id =  wp_insert_post($reservation );  
            
            $reservation_update = array(
                'ID'         => $reservation_id,
                'post_title' => $title.' '.$reservation_id
            );
            wp_update_post( $reservation_update );

            update_post_meta($reservation_id, 'reservation_listing_id', $listing_id);
            update_post_meta($reservation_id, 'listing_owner', $listing_owner_id);
            update_post_meta($reservation_id, 'listing_renter', $userID);
            update_post_meta($reservation_id, 'reservation_checkin_date', $check_in_date);
            update_post_meta($reservation_id, 'reservation_checkout_date', $check_out_date);
            update_post_meta($reservation_id, 'reservation_guests', $guests);
            update_post_meta($reservation_id, 'reservation_meta', $reservation_meta);
            update_post_meta($reservation_id, 'reservation_status', 'under_review');

            update_post_meta($reservation_id, 'reservation_upfront', $upfront_payment);
            update_post_meta($reservation_id, 'reservation_balance', $balance);
            update_post_meta($reservation_id, 'reservation_total', $total_price);

            $pending_dates_array = homey_get_booking_pending_days_test($listing_id);      
            update_post_meta($listing_id, 'reservation_pending_dates', $pending_dates_array); 

            echo json_encode( 
                array( 
                    'success' => true, 
                    'message' => $local['request_sent']
                ) 
            );
            
            $email_args = array('reservation_detail_url' => reservation_detail_link($reservation_id) );
            homey_email_composer( $owner_email, 'new_reservation', $email_args );

            wp_die();

         } else { // end $check_availability
            echo json_encode( 
                array( 
                    'success' => false, 
                    'message' => $check_message
                ) 
             );
             wp_die();
         }

    } 

    //2019-04-8 16:00
    //2019-04-8 17:00

    //homey_add_reservation_test();


    function check_booking_availability_test() {
        $return_array = array();
        $local = homey_get_localization();
        $booking_proceed = true;
      

        $listing_id = 338;//intval($_POST['listing_id']);
        $listing_owner_id  =  get_post_field( 'post_author', $listing_id );
        $check_in_date     =  '2019-03-16 17:30';//wp_kses ( $_POST['check_in_date'], $allowded_html );
        $check_out_date    =  '2019-03-16 18:30';//wp_kses ( $_POST['check_out_date'], $allowded_html );
        $guests   =  '2';//intval($_POST['guests']);

        $min_book_days = get_post_meta($listing_id, 'homey_min_book_days', true);
        $max_book_days = get_post_meta($listing_id, 'homey_max_book_days', true);

        $homey_allow_additional_guests = get_post_meta($listing_id, 'homey_allow_additional_guests', true);
        $allowed_guests = get_post_meta($listing_id, 'homey_guests', true);

        if( ($homey_allow_additional_guests != 'yes') && ($guests > $allowed_guests) ) {
            $return_array['success'] = false;
            $return_array['message'] = $local['guest_allowed'].' '.$allowed_guests;
            return $return_array;
        } 

        if(strtotime($check_out_date) <= strtotime($check_in_date)) {
            $booking_proceed = false;
        }

        if(empty($check_in_date) && empty($check_out_date) && empty($guests)) {
            $return_array['success'] = false;
            $return_array['message'] = $local['fill_all_fields'];
            return $return_array;

        } 

        if(empty($check_in_date)) {
            $return_array['success'] = false;
            $return_array['message'] = $local['choose_checkin'];
            return $return_array;

        }

        if(empty($check_out_date)) {
            $return_array['success'] = false;
            $return_array['message'] = $local['choose_checkout'];
            return $return_array;

        }


        $time_difference = abs( strtotime($check_in_date) - strtotime($check_out_date) );
        $days_count      = $time_difference/86400;
        $days_count      = intval($days_count); 

        if($days_count < $min_book_days) {
            $return_array['success'] = false;
            $return_array['message'] = $local['min_book_days_error'].' '.$min_book_days;
            return $return_array;
        }

        if(($days_count > $max_book_days) && !empty($max_book_days)) {
            $return_array['success'] = false;
            $return_array['message'] = $local['max_book_days_error'].' '.$max_book_days;
            return $return_array;
        }

        if(empty($guests)) {
            $return_array['success'] = false;
            $return_array['message'] = $local['choose_guests'];
            return $return_array;

        }

        if(!$booking_proceed) {
            $return_array['success'] = false;
            $return_array['message'] = $local['ins_book_proceed'];
            return $return_array;
        }


        $reservation_booked_array = get_post_meta($listing_id, 'reservation_dates', true);
        if(empty($reservation_booked_array)) {
            $reservation_booked_array = homey_get_booked_days($listing_id);
        }

        $reservation_pending_array = get_post_meta($listing_id, 'reservation_pending_dates', true);
        if(empty($reservation_pending_array)) {
            $reservation_pending_array = homey_get_booking_pending_days_test($listing_id);
        }

        print_r($reservation_pending_array);
        echo '<br/>';

        $check_in      = new DateTime($check_in_date);
        $check_in_unix = $check_in->getTimestamp();

        $check_out     = new DateTime($check_out_date);
        //$check_out->modify('yesterday');
        //$check_out->modify('-1 hour');
        $check_out->modify('-30 minutes');
        $check_out_unix = $check_out->getTimestamp();

        while ($check_in_unix <= $check_out_unix) {
        	echo $check_in_unix.' ===== <br/>';
            if(array_key_exists($check_in_unix, $reservation_pending_array) ) {
                
                $return_array['success'] = false;
                $return_array['message'] = $local['dates_not_available'];
                if(homey_is_instance_page()) {
                    $return_array['message'] = $local['ins_unavailable'];
                }
                return $return_array; //dates are not available

            }

            //$check_in->modify('tomorrow');
            //$check_in->modify('+1 hour');
            $check_in->modify('+30 minutes');
            $check_in_unix =   $check_in->getTimestamp();
        }

        //dates are available
        $return_array['success'] = true;
        $return_array['message'] = $local['dates_available'];
        return $return_array;
        
    }

    $test = check_booking_availability_test();
    print_r($test);


get_footer(); 
?>