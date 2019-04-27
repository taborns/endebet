<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homey_Reservation_Post_Type {
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'definition' ) );

        add_action('admin_init', array( __CLASS__, 'confirm_reservation' ));

        add_filter( 'manage_edit-homey_reservation_columns', array( __CLASS__, 'custom_columns' ) );
        add_action( 'manage_pages_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
    }

    /**
     * Custom post type definition
     *
     * @access public
     * @return void
     */
    public static function definition() {
        $labels = array(
            'name' => __( 'Reservations','homey-core'),
            'singular_name' => __( 'Reservation','homey-core' ),
            'add_new' => __('Add New','homey-core'),
            'add_new_item' => __('Add New','homey-core'),
            'edit_item' => __('Edit Reservation','homey-core'),
            'new_item' => __('New Reservation','homey-core'),
            'view_item' => __('View Reservation','homey-core'),
            'search_items' => __('Search Reservation','homey-core'),
            'not_found' =>  __('No Reservation found','homey-core'),
            'not_found_in_trash' => __('No Reservation found in Trash','homey-core'),
            'parent_item_colon' => ''
          );

        $labels = apply_filters( 'homey_reservation_post_type_labels', $labels );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'query_var' => true,
            'has_archive' => true,
            'capability_type' => 'post',
            'map_meta_cap'    => true,
            'hierarchical' => true,
            'menu_icon' => 'dashicons-location',
            'menu_position' => 21,
            'can_export' => true,
            'show_in_rest'       => true,
            'rest_base'          => 'reservations',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports' => array('title','revisions','author'),

             // The rewrite handles the URL structure.
            'rewrite' => array(
                  'slug'       => 'homey_reservation',
                  'with_front' => false,
                  'pages'      => true,
                  'feeds'      => true,
                  'ep_mask'    => EP_PERMALINK,
            ),
        );

        $args = apply_filters( 'homey_reservation_post_type_args', $args );

        register_post_type('homey_reservation',$args);
    }

    public static function confirm_reservation() {
        if (!empty($_GET['confirm_reservation']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'confirm_reservation') && current_user_can('publish_post', $_GET['confirm_reservation'])) {
            $post_id = absint($_GET['confirm_reservation']);
            

            update_post_meta($post_id, 'reservation_status', 'available');

            /*$args = array(
                'listing_title' => get_the_title($post_id),
                'listing_url' => get_permalink($post_id)
            );
            houzez_email_type( $user_email,'listing_approved', $args );*/

            wp_redirect(remove_query_arg('confirm_reservation', add_query_arg('confirm_reservation', $post_id, admin_url('edit.php?post_type=homey_reservation'))));
            exit;
        }
    }

    /**
     * Custom admin columns for post type
     *
     * @access public
     * @return array
     */
    public static function custom_columns() {

        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "pic" => __( 'Pic','homey-core' ),
            "title" => __( 'ID','homey-core' ),
            'status' => __( 'Status','homey-core' ),
            //"res_date" => __('Date','homey-core'),
            "res_address" => __('Address','homey-core'),
            "check_in" => __('Check-in','homey-core'),
            "check_out" => __( 'Check-out','homey-core' ),
            "res_guests" => __( 'Guests','homey-core' ),
            "pets" => __( 'Pets','homey-core' ),
            "subtotal" => __( 'Subtotal','homey-core' ),
            "actions" => __( 'Actions','homey-core' ),
            "date" => __('Date','homey-core'),
        );

        $columns = apply_filters( 'houzez_custom_post_listing_columns', $columns );

        return $columns;
        
    }

    /**
     * Custom admin columns implementation
     *
     * @access public
     * @param string $column
     * @return array
     */
    public static function custom_columns_manage( $column ) {
        global $post;
        $prefix = 'homey_';
        $local = homey_get_localization();
        $listing_id = get_post_meta(get_the_ID(), 'reservation_listing_id', true);
        $status = get_post_meta(get_the_ID(), 'reservation_status', true);

        switch ($column)
        {
            case 'pic':
                $listing_author = homey_get_author('40', '40', 'img-circle media-object avatar');
                if(!empty($listing_author['photo'])) { 
                    echo $listing_author['photo']; 
                } else {
                    echo '-';
                }
                break;
            case 'title':
                echo $post->ID;
                break;

            case 'status':
                homey_reservation_label($status);
                break;
            case 'res_address':
                $listing_address    = get_post_meta( $listing_id, $prefix.'listing_address', true );
                echo $listing_address;
                break;
            case 'check_in':
                $check_in = get_post_meta(get_the_ID(), 'reservation_checkin_date', true);
                esc_attr_e($check_in);
                break;
            case 'check_out':
                $check_out = get_post_meta(get_the_ID(), 'reservation_checkout_date', true);
                esc_attr_e($check_out);
                break;
            case 'res_guests':
                $guests = get_post_meta(get_the_ID(), 'reservation_guests', true);
                echo $guests; 
                break;
            case 'pets':
                $pets   = get_post_meta($listing_id, $prefix.'pets', true);
                if($pets != 1) {
                    echo $local['text_no'];
                } else {
                    echo $local['text_yes'];
                }
                break;
            case 'subtotal':
                $deposit = get_post_meta(get_the_ID(), 'reservation_upfront', true);
                echo homey_formatted_price($deposit);
                break;
            
            case 'actions':
                echo '<div class="actions">';
                $admin_actions = apply_filters( 'post_row_actions', array(), $post );

                $user = wp_get_current_user();

                if($status == 'under_review') {
    
                    /*if ( in_array( $post->post_status, array( 'publish' ) ) && !homey_is_renter() ) {
                        $admin_actions['confirmed']   = array(
                            'action'  => 'confirmed',
                            'name'    => __( 'Confirm Availability', 'homey-core' ),
                            'url'     =>  wp_nonce_url( add_query_arg( 'confirm_reservation', $post->ID ), 'confirm_reservation' )
                        );
                    }*/
                }

                if ( $post->post_status !== 'trash' ) {
                    
                    if ( current_user_can( 'edit_post', $post->ID ) ) {
                        $admin_actions['edit']   = array(
                            'action'  => 'edit',
                            'name'    => __( 'View Detail', 'homey-core' ),
                            'url'     => get_edit_post_link( $post->ID )
                        );
                    }
                    
                }


                $admin_actions = apply_filters( 'homey_listing_admin_actions', $admin_actions, $post );

                foreach ( $admin_actions as $action ) {
                    if ( is_array( $action ) ) {
                        printf( '<a class="button button-icon tips icon-%1$s" href="%2$s" data-tip="%3$s">%4$s</a>', $action['action'], esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_html( $action['name'] ) );
                    } else {
                        //echo str_replace( 'class="', 'class="button ', $action );
                    }
                }

                echo '</div>';

                break;

        }
    }

}
