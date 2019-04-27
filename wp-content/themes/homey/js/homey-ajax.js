jQuery(document).ready(function ($) {
    "use strict";

    if ( typeof HOMEY_ajax_vars !== "undefined" ) {
        
        var ajaxurl = HOMEY_ajax_vars.admin_url+ 'admin-ajax.php';
        var is_singular_listing = HOMEY_ajax_vars.is_singular_listing;
        var paypal_connecting = HOMEY_ajax_vars.paypal_connecting;
        var login_sending = HOMEY_ajax_vars.login_loading;
        var process_loader_spinner = HOMEY_ajax_vars.process_loader_spinner;
        var currency_updating_msg = HOMEY_ajax_vars.currency_updating_msg;
        var userID = HOMEY_ajax_vars.user_id;
        var homey_reCaptcha = HOMEY_ajax_vars.homey_reCaptcha;

        var is_tansparent = HOMEY_ajax_vars.homey_tansparent;
        var retina_logo = HOMEY_ajax_vars.retina_logo;
        var retina_logo_splash = HOMEY_ajax_vars.retina_logo_splash;
        var retina_logo_mobile = HOMEY_ajax_vars.retina_logo_mobile;
        var retina_logo_mobile_splash = HOMEY_ajax_vars.retina_logo_mobile_splash;
        var no_more_listings = HOMEY_ajax_vars.no_more_listings;
        var allow_additional_guests = HOMEY_ajax_vars.allow_additional_guests;
        var allowed_guests_num = HOMEY_ajax_vars.allowed_guests_num;
        var agree_term_text = HOMEY_ajax_vars.agree_term_text;
        var choose_gateway_text = HOMEY_ajax_vars.choose_gateway_text;
        var success_icon = HOMEY_ajax_vars.success_icon;
        var calendar_link = HOMEY_ajax_vars.calendar_link;
        var focusedInput_2 = null;

        var compare_url = HOMEY_ajax_vars.compare_url;
        var add_compare = HOMEY_ajax_vars.add_compare;
        var remove_compare = HOMEY_ajax_vars.remove_compare;
        var compare_limit = HOMEY_ajax_vars.compare_limit;

         var homey_timeStamp_2 = function(str) {
          return new Date(str.replace(/^(\d{2}\-)(\d{2}\-)(\d{4})$/,
            '$2$1$3')).getTime();
        };

        /*--------------------------------------------------------------------------
         *   Retina Logo
         * -------------------------------------------------------------------------*/
        if (window.devicePixelRatio == 2) {

            if(is_tansparent) {
                if(retina_logo_splash != '') {
                    $(".transparent-header .homey_logo img").attr("src", retina_logo_splash);
                }

                if(retina_logo_mobile_splash != '') {
                    $(".mobile-logo img").attr("src", retina_logo_mobile_splash);
                }

            } else {
                if(retina_logo != '') {
                    $(".homey_logo img").attr("src", retina_logo);
                }

                if(retina_logo_mobile != '') {
                    $(".mobile-logo img").attr("src", retina_logo_mobile);
                }
            }
        }

        /*--------------------------------------------------------------------------
         *  Currency Switcher
         * -------------------------------------------------------------------------*/
        var currencySwitcherList = $('#homey-currency-switcher-list');
        if( currencySwitcherList.length > 0 ) {

            $('#homey-currency-switcher-list > li').on('click', function(e) {
                e.stopPropagation();
                currencySwitcherList.slideUp( 200 );

                var selectedCurrencyCode = $(this).data( 'currency-code' );

                if ( selectedCurrencyCode ) {

                    $('.homey-selected-currency span').html( selectedCurrencyCode );
                    homey_processing_modal('<i class="'+process_loader_spinner+'"></i> '+currency_updating_msg);

                    $.ajax({
                        url: ajaxurl,
                        dataType: 'JSON',
                        method: 'POST',
                        data: {
                            'action' : 'homey_currency_converter',
                            'currency_to_converter' : selectedCurrencyCode,
                        },
                        success: function (res) {
                            if( res.success ) {
                                window.location.reload();
                            } else {
                                console.log( res );
                            }
                        },
                        error: function (xhr, status, error) {
                            var err = eval("(" + xhr.responseText + ")");
                            console.log(err.Message);
                        }
                    });

                }

            });
        }

        /*--------------------------------------------------------------------------
         *  Module Ajax Pagination
         * -------------------------------------------------------------------------*/
        var listings_module_section = $('#listings_module_section');
        if( listings_module_section.length > 0 ) {

            $("body").on('click', '.homey-loadmore a', function(e) {
                e.preventDefault();
                var $this = $(this);
                var $wrap = $this.closest('#listings_module_section').find('#module_listings');

                var limit = $this.data('limit');
                var paged = $this.data('paged');
                var style = $this.data('style');
                var type = $this.data('type');
                var roomtype = $this.data('roomtype');
                var country = $this.data('country');
                var state = $this.data('state');
                var city = $this.data('city');
                var area = $this.data('area');
                var featured = $this.data('featured');
                var offset = $this.data('offset');
                var sortby = $this.data('sortby');
                var author = $this.data('author');
                var authorid = $this.data('authorid');

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        'action': 'homey_loadmore_listings',
                        'limit': limit,
                        'paged': paged,
                        'style': style,
                        'type': type,
                        'roomtype': roomtype,
                        'country': country,
                        'state': state,
                        'city': city,
                        'area': area,
                        'featured': featured,
                        'sort_by': sortby,
                        'offset': offset,
                        'author': author,
                        'authorid': authorid,
                    },
                    beforeSend: function( ) {
                        $this.find('i').css('display', 'inline-block');
                    },
                    success: function (data) {
                        if(data == 'no_result') {
                             $this.closest('#listings_module_section').find('.homey-loadmore').text(no_more_listings);
                             return;
                        }
                        $wrap.append(data);
                        $this.data("paged", paged+1);

                        homey_init_add_favorite(ajaxurl, userID, is_singular_listing);
                        homey_init_remove_favorite(ajaxurl, userID, is_singular_listing);
                        compare_for_ajax();

                    },
                    complete: function(){
                        $this.find('i').css('display', 'none');
                    },
                    error: function (xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }

                });

            }); 
        }

        /*--------------------------------------------------------------------------
         *   Add or remove favorites
         * -------------------------------------------------------------------------*/
        homey_init_add_favorite(ajaxurl, userID, is_singular_listing);
        homey_init_remove_favorite(ajaxurl, userID, is_singular_listing);

        /*--------------------------------------------------------------------------
         *   Compare for ajax
         * -------------------------------------------------------------------------*/
        var compare_for_ajax = function() {
            var listings_compare = homeyGetCookie('homey_compare_listings');
            var limit_item_compare = 4;
            add_to_compare(compare_url, add_compare, remove_compare, compare_limit, listings_compare, limit_item_compare );
            remove_from_compare(listings_compare, add_compare, remove_compare);
        }

        /* ------------------------------------------------------------------------ */
        /*  Paypal single listing payment
         /* ------------------------------------------------------------------------ */
        $('#homey_complete_order').on('click', function(e) {
            e.preventDefault();
            var hform, payment_gateway, listing_id, is_upgrade;

            payment_gateway = $("input[name='homey_payment_type']:checked").val();
            is_upgrade = $("input[name='is_upgrade']").val();

            listing_id = $('#listing_id').val();

            if( payment_gateway == 'paypal' ) {
                homey_processing_modal( paypal_connecting );
                homey_paypal_payment( listing_id, is_upgrade);

            } else if ( payment_gateway == 'stripe' ) {
                var hform = $(this).parents('.dashboard-area');
                hform.find('.homey_stripe_simple button').trigger("click");
            }
            return;

        });

        var homey_processing_modal = function ( msg ) {
            var process_modal ='<div class="modal fade" id="homey_modal" tabindex="-1" role="dialog" aria-labelledby="homeyModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body homey_messages_modal">'+msg+'</div></div></div></div></div>';
            jQuery('body').append(process_modal);
            jQuery('#homey_modal').modal();
        }

        var homey_processing_modal_close = function ( ) {
            jQuery('#homey_modal').modal('hide');
        }


        /* ------------------------------------------------------------------------ */
        /*  Paypal payment function
         /* ------------------------------------------------------------------------ */
        var homey_paypal_payment = function( listing_id, is_upgrade ) {

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    'action': 'homey_listing_paypal_payment',
                    'listing_id': listing_id,
                    'is_upgrade': is_upgrade,
                },
                success: function( response ) {
                    window.location.href = response;
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }


        if($('#add_review').length > 0) {
            $('#add_review').on('click', function(e){
                e.preventDefault();

                var $this = $(this);
                    var rating = $('#rating').val();
                    var review_action = $('#review_action').val();
                    var review_content = $('#review_content').val();
                    var review_reservation_id = $('#review_reservation_id').val();
                    var security = $('#review-security').val();
                    var parentDIV = $this.parents('.user-dashboard-right');

                    $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        'action': 'homey_add_review',
                        'rating': rating,
                        'review_action': review_action,
                        'review_content': review_content,
                        'review_reservation_id': review_reservation_id,
                        'security': security
                    },
                    beforeSend: function( ) {
                        $this.children('i').remove();
                        $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                    },
                    success: function(data) {

                        parentDIV.find('.alert').remove();
                        if(data.success) {
                            $this.attr("disabled", true);
                            window.location.reload();
                        } else {
                            parentDIV.find('.dashboard-area').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-hide="alert" aria-label="Close"><i class="fa fa-close"></i></button>'+data.message+'</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    },
                    complete: function(){
                        $this.children('i').removeClass(process_loader_spinner);
                    }

                });
                            
            });
        }

        var listing_review_ajax = function(sortby, listing_id, paged) {
            var review_container = $('#homey_reviews');
            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    'action': 'homey_ajax_review',
                    'sortby': sortby,
                    'listing_id': listing_id,
                    'paged': paged
                },
                beforeSend: function( ) {
                
                },
                success: function(data) {
                    review_container.empty();
                    review_container.html(data);
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    
                }

            });
        }

        if($('#sort_review').length > 0) {
            $('#sort_review').on('change', function() {
                var sortby = $(this).val();
                var listing_id = $('#review_listing_id').val();
                var paged = $('#review_paged').val();
                listing_review_ajax(sortby, listing_id, paged);
                return;
            }); 
        }

        if($('#review_next').length > 0) {
            $('#review_next').on('click', function(e) {
                e.preventDefault();
                $('#review_prev').removeAttr('disabled');
                var sortby = $('#page_sort').val();
                var total_pages = $('#total_pages').val();
                var listing_id = $('#review_listing_id').val();
                var paged = $('#review_paged').val();
                paged = Number(paged)+1;
                $('#review_paged').val(paged);

                if(paged == total_pages) {
                    $(this).attr('disabled', true);
                }
                listing_review_ajax(sortby, listing_id, paged);
                return;
            }); 
        }

        if($('#review_prev').length > 0) {
            $('#review_prev').on('click', function(e) {
                e.preventDefault();
                $('#review_next').removeAttr('disabled');
                var sortby = $('#page_sort').val();
                var listing_id = $('#review_listing_id').val();
                var paged = $('#review_paged').val();
                paged = Number(paged)-1;
                $('#review_paged').val(paged);
                if(paged <= 1) {
                    $(this).attr('disabled', true);
                }
                listing_review_ajax(sortby, listing_id, paged);
                return;
            }); 
        }


        var homey_calculate_booking_cost = function(check_in_date, check_out_date, guests, listing_id, security) {
            var $this = $(this);
            var notify = $('.homey_notification');
            notify.find('.notify').remove();

            if(check_in_date === '' || check_out_date === '') {
                $('#homey_booking_cost').empty();
                return;
            }

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    'action': 'homey_calculate_booking_cost',
                    'check_in_date': check_in_date,
                    'check_out_date': check_out_date,
                    'guests': guests,
                    'listing_id': listing_id,
                    'security': security
                },
                beforeSend: function( ) {
                    $('#homey_booking_cost').empty();
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                    notify.find('.homey_preloader').show();
                },
                success: function(data) {
                    $('#homey_booking_cost').empty().html(data);
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    notify.find('.homey_preloader').hide();
                }

            });
        }

        var check_booking_availability_on_date_change = function(check_in_date, check_out_date, listing_id, security) {
            var $this = $(this);

            var notify = $('.homey_notification');
            notify.find('.notify').remove();
        
            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'check_booking_availability_on_date_change',
                    'check_in_date': check_in_date,
                    'check_out_date': check_out_date,
                    'listing_id': listing_id,
                    'security': security
                },
                beforeSend: function( ) {
                    $('#homey_booking_cost').empty();
                    notify.find('.homey_preloader').show();
                },
                success: function(data) {
                    if( data.success ) {
                        $('#request_for_reservation').removeAttr("disabled");
                        $('#instance_reservation').removeAttr("disabled");
                        notify.prepend('<div class="notify text-success text-center btn-success-outlined btn btn-full-width">'+data.message+'</div>');
                    } else {
                        notify.prepend('<div class="notify text-danger text-center btn-danger-outlined btn btn-full-width">'+data.message+'</div>');
                        $('#request_for_reservation').attr("disabled", true);
                        $('#instance_reservation').attr("disabled", true);
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    notify.find('.homey_preloader').hide();
                }

            });
        }

        // Single listing booking form
        $("#single-listing-date-range input").on('focus', function() {
            $('.single-listing-booking-calendar-js').css("display", "block");
            $('.single-listing-booking-calendar-js').addClass("arrive_active");
            $('.single-form-guests-js').css("display", "none");
            focusedInput_2 = $(this).attr('name');
            $('.single-listing-booking-calendar-js').removeClass('arrive_active depart_active').addClass(focusedInput_2+'_active');
        });

        $(".single-guests-js input").on('focus', function() {
            $(this).prev("label").css("display", "block");
            $(this).addClass("on-focus");
            $('.single-form-guests-js').css("display", "block");
        });

        var numClicks = 0;
        var fromTimestamp_2, toTimestamp_2 = 0; // init start and end timestamps

        var homey_booking_dates = function() {
            
            $('.single-listing-booking-calendar-js ul li').on('click', function() {
                var $this = $(this);

                if($this.hasClass('past-day')) {
                    return false;
                }

                numClicks += 1;
                var vl = $this.data('formatted-date');
                var timestamp = $this.data('timestamp');

                // if modify days after selecting once
                if (focusedInput_2 == 'depart' && timestamp > fromTimestamp_2) {

                    $('.single-listing-calendar-wrap ul').find('li.to-day').removeClass('selected')
                        .siblings().removeClass('to-day in-between');

                    numClicks = 2;
                }

                if( numClicks == 1 ) {
                    fromTimestamp_2 = timestamp;

                    //day nodes
                    $('.single-listing-calendar-wrap ul li').removeClass('to-day from-day selected in-between');
                    $this.addClass('from-day selected');
                    // move caret
                    $('.single-listing-booking-calendar-js').removeClass('arrive_active').addClass('depart_active');

                    $('#check_in_date').val(vl);
                    $('#check_out_date').val('');
                    homey_calculate_price_checkin();
                    
                } else if(numClicks == 2) {

                    toTimestamp_2 = timestamp;
                    //day end node
                    $this.addClass('to-day selected');
                    $('.single-listing-booking-calendar-js').removeClass('depart_active').addClass('arrive_active');

                    var check_in_date = $('#check_in_date').val();
                    check_in_date = homey_timeStamp_2(check_in_date);
                    var check_out_date = homey_timeStamp_2(vl);

                    if(check_in_date >= check_out_date) {
                        fromTimestamp_2 = timestamp;
                        toTimestamp_2 = 0;
                        //day nodes
                        $('.single-listing-calendar-wrap ul li').removeClass('to-day from-day selected in-between');
                        $this.addClass('from-day selected');

                        // move caret
                        $('.single-listing-booking-calendar-js').removeClass('arrive_active').addClass('depart_active');

                        $('#check_in_date').val(vl);
                        numClicks = 1;
                    } else {
                        setInBetween_2(fromTimestamp_2, toTimestamp_2);
                        $('#check_out_date').val(vl);
                        $('#single-booking-search-calendar').hide();
                        homey_calculate_price_checkout();
                    }
                }
                if(numClicks == 2) { 
                    numClicks = 0; 
                }

            });
        }
        homey_booking_dates();

        $('.single-listing-calendar-wrap ul li').on('hover', function () {

            var ts = $(this).data('timestamp');
            if (numClicks == 1) {
                setInBetween_2(fromTimestamp_2, ts);
            }
        });
        /*
        * method to send in-between days
        * */
        var setInBetween_2 = function(fromTime, toTime) {
            $('.single-listing-calendar-wrap ul li').removeClass('in-between')
                .filter(function () {
                    var currentTs = $(this).data('timestamp');
                    return currentTs > fromTime && currentTs < toTime;
                }).addClass('in-between');
        }

    
        var homey_calculate_price_checkin = function() {
            var check_in_date = $('#check_in_date').val();
            var check_out_date = $('#check_out_date').val();
            var guests = $('#guests').val();
            var listing_id = $('#listing_id').val();
            var security = $('#reservation-security').val();
            homey_calculate_booking_cost(check_in_date, check_out_date, guests, listing_id, security);
        }

        var homey_calculate_price_checkout = function() {
            var check_in_date = $('#check_in_date').val();
            var check_out_date = $('#check_out_date').val();
            var guests = $('#guests').val();
            var listing_id = $('#listing_id').val();
            var security = $('#reservation-security').val();
            homey_calculate_booking_cost(check_in_date, check_out_date, guests, listing_id, security);
            check_booking_availability_on_date_change(check_in_date, check_out_date, listing_id, security);
        }
        
        $('#apply_guests').on('click', function () {
            var check_in_date = $('#check_in_date').val();
            var check_out_date = $('#check_out_date').val();
            var guests = $('#guests').val();
            var listing_id = $('#listing_id').val();
            var security = $('#reservation-security').val();
            homey_calculate_booking_cost(check_in_date, check_out_date, guests, listing_id, security);
            check_booking_availability_on_date_change(check_in_date, check_out_date, listing_id, security);
        });

        /* ------------------------------------------------------------------------ */
        /*  Guests count
        /* ------------------------------------------------------------------------ */

        var single_listing_guests = function() {
            $('.adult_plus').on('click', function(e) {
                e.preventDefault();
                var guests = parseInt($('#guests').val()) || 0;
                var adult_guest = parseInt($('#adult_guest').val());
                var child_guest = parseInt($('#child_guest').val());

                adult_guest++;
                $('.homey_adult').text(adult_guest);
                $('#adult_guest').val(adult_guest);

                var total_guests = adult_guest + child_guest;

                if( (allow_additional_guests != 'yes') && (total_guests == allowed_guests_num)) {
                    $('.adult_plus').attr("disabled", true);
                    $('.child_plus').attr("disabled", true);
                }

                $('#guests').val(total_guests);
            });

            $('.adult_minus').on('click', function(e) {
                e.preventDefault();
                var guests = parseInt($('#guests').val()) || 0;
                var adult_guest = parseInt($('#adult_guest').val());
                var child_guest = parseInt($('#child_guest').val());
                
                if (adult_guest == 0) return;
                adult_guest--;
                $('.homey_adult').text(adult_guest);
                $('#adult_guest').val(adult_guest);

                var total_guests = adult_guest + child_guest;
                $('#guests').val(total_guests);

                $('.adult_plus').removeAttr("disabled");
                $('.child_plus').removeAttr("disabled");
            });

            $('.child_plus').on('click', function(e) {
                e.preventDefault();
                var guests = parseInt($('#guests').val());
                var child_guest = parseInt($('#child_guest').val());
                var adult_guest = parseInt($('#adult_guest').val());

                child_guest++;
                $('.homey_child').text(child_guest);
                $('#child_guest').val(child_guest);

                var total_guests = child_guest + adult_guest;

                if( (allow_additional_guests != 'yes') && (total_guests == allowed_guests_num)) {
                    $('.adult_plus').attr("disabled", true);
                    $('.child_plus').attr("disabled", true);
                }

                $('#guests').val(total_guests);

            });

            $('.child_minus').on('click', function(e) {
                e.preventDefault();
                var guests = parseInt($('#guests').val());
                var child_guest = parseInt($('#child_guest').val());
                var adult_guest = parseInt($('#adult_guest').val());

                if (child_guest == 0) return;
                child_guest--;
                $('.homey_child').text(child_guest);
                $('#child_guest').val(child_guest);

                var total_guests = child_guest + adult_guest;

                $('#guests').val(total_guests);

                $('.adult_plus').removeAttr("disabled");
                $('.child_plus').removeAttr("disabled");

            });
        }
        single_listing_guests();

        /* ------------------------------------------------------------------------ */
        /*  Reservation Request
         /* ------------------------------------------------------------------------ */
         $('#request_for_reservation').on('click', function(e){
            e.preventDefault();

            var $this = $(this);
            var check_in_date = $('#check_in_date').val();
            var check_out_date = $('#check_out_date').val();
            var guests = $('#guests').val();
            var listing_id = $('#listing_id').val();
            var security = $('#reservation-security').val();
            var notify = $this.parents('.homey_notification');
            notify.find('.notify').remove();
            
            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_add_reservation',
                    'check_in_date': check_in_date,
                    'check_out_date': check_out_date,
                    'guests': guests,
                    'listing_id': listing_id,
                    'security': security
                },
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(data) {
                    if( data.success ) {
                        $('#check_in_date, #check_out_date').val('');
                        notify.prepend('<div class="notify text-success text-center btn-success-outlined btn btn-full-width">'+data.message+'</div>');
                        
                    } else {
                        notify.prepend('<div class="notify text-danger text-center btn-danger-outlined btn btn-full-width">'+data.message+'</div>');
                        
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                }

            });

         });

         /* ------------------------------------------------------------------------ */
        /*  Reserve a period host
         /* ------------------------------------------------------------------------ */
         $('#reserve_period_host').on('click', function(e){
            e.preventDefault();

            var $this = $(this);
            var check_in_date = $('#period_start_date').val();
            var check_out_date = $('#period_end_date').val();
            var listing_id = $('#period_listing_id').val();
            var period_note = $('#period_note').val();
            var security = $('#period-security').val();
            var notify = $('.homey_notification');
            notify.find('.notify').remove();
            
            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_reserve_period_host',
                    'check_in_date': check_in_date,
                    'check_out_date': check_out_date,
                    'period_note': period_note,
                    'listing_id': listing_id,
                    'security': security
                },
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(data) {
                    if( data.success ) {
                        notify.prepend('<div class="notify text-success text-center btn-success-outlined btn btn-full-width">'+data.message+'</div>');
                        window.location.href = calendar_link;
                    } else {
                        notify.prepend('<div class="notify text-danger text-center btn-danger-outlined btn btn-full-width">'+data.message+'</div>');
                        
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                }

            });

         });

         /* ------------------------------------------------------------------------ */
        /*  Instace Booking
         /* ------------------------------------------------------------------------ */
         $('#instance_reservation').on('click', function(e){
            e.preventDefault();

            var $this = $(this);
            var check_in_date = $('#check_in_date').val();
            var check_out_date = $('#check_out_date').val();
            var guests = $('#guests').val();
            var listing_id = $('#listing_id').val();
            var security = $('#reservation-security').val();
            var notify = $this.parents('.homey_notification');
            notify.find('.notify').remove();

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_instance_booking',
                    'check_in_date': check_in_date,
                    'check_out_date': check_out_date,
                    'guests': guests,
                    'listing_id': listing_id,
                    'security': security
                },
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function (data) {

                    if( data.success ) {
                        $('#check_in_date, #check_out_date').val('');
                        notify.prepend('<div class="notify text-success text-center btn-success-outlined btn btn-full-width">'+data.message+'</div>');
                        window.location.href = data.instance_url;
                    } else {
                        notify.prepend('<div class="notify text-danger text-center btn-danger-outlined btn btn-full-width">'+data.message+'</div>');
                        
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                }
            });

            

         });

         /* ------------------------------------------------------------------------ */
        /*  Confirm Reservation
         /* ------------------------------------------------------------------------ */
         $('.confirm-reservation').on('click', function(e){
            e.preventDefault();

            var $this = $(this);
            var reservation_id = $this.data('reservation_id');
            var parentDIV = $this.parents('.user-dashboard-right');

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_confirm_reservation',
                    'reservation_id': reservation_id
                },
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(data) {

                    parentDIV.find('.alert').remove();
                    if( data.success ) {
                        parentDIV.find('.dashboard-area').prepend(data.message);
                        $this.remove();
                    } else {
                        parentDIV.find('.dashboard-area').prepend(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                }

            });

         });

         /* ------------------------------------------------------------------------ */
        /*  Decline Reservation
         /* ------------------------------------------------------------------------ */
         $('#decline').on('click', function(e){
            e.preventDefault();

            var $this = $(this);
            var reservation_id = $('#reservationID').val();
            var reason = $('#reason').val();
            var parentDIV = $this.parents('.user-dashboard-right');

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_decline_reservation',
                    'reservation_id': reservation_id,
                    'reason': reason
                },
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(data) {

                    parentDIV.find('.alert').remove();
                    if( data.success ) { 
                        $this.attr("disabled", true);
                        window.location.reload();
                    } else {
                        parentDIV.find('.dashboard-area').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-hide="alert" aria-label="Close"><i class="fa fa-close"></i></button>'+data.message+'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                }

            });

         });

         /* ------------------------------------------------------------------------ */
        /*  Decline Reservation
         /* ------------------------------------------------------------------------ */
         $('#cancelled').on('click', function(e){
            e.preventDefault();

            var $this = $(this);
            var reservation_id = $('#reservationID').val();
            var reason = $('#reason').val();
            var parentDIV = $this.parents('.user-dashboard-right');

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_cancelled_reservation',
                    'reservation_id': reservation_id,
                    'reason': reason
                },
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(data) {

                    parentDIV.find('.alert').remove();
                    if( data.success ) { 
                        $this.attr("disabled", true);
                        window.location.reload();
                    } else {
                        parentDIV.find('.dashboard-area').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-hide="alert" aria-label="Close"><i class="fa fa-close"></i></button>'+data.message+'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                }

            });

         });

        var homey_booking_paypal_payment = function($this, reservation_id, security) {
            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_booking_paypal_payment',
                    'reservation_id': reservation_id,
                    'security': security,
                },
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                    $('#homey_notify').html('<div class="alert alert-success alert-dismissible" role="alert">'+paypal_connecting+'</div>');
                },
                success: function( data ) {
                    if(data.success) {
                        window.location.href = data.payment_execute_url;
                    } else {
                        $('#homey_notify').html('<div class="alert alert-danger alert-dismissible" role="alert">'+data.message+'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        $('#make_booking_payment').on('click', function(e){
            e.preventDefault();

            var $this = $(this);
            var reservation_id = $('#reservation_id').val();
            var security = $('#checkout-security').val();

            var payment_gateway = $("input[name='payment_gateway']:checked").val();
            if(payment_gateway == undefined ) {
                $('#homey_notify').html('<div class="alert alert-danger alert-dismissible" role="alert">'+choose_gateway_text+'</div>');
            }
            
            if(payment_gateway === 'paypal') {
                homey_booking_paypal_payment($this, reservation_id, security);

            } else if(payment_gateway === 'stripe') {
                var hform = $(this).parents('.dashboard-area');
                hform.find('.homey_stripe_simple button').trigger("click");
                $('#homey_notify').html('');
            }
            return;

        });

        var homey_instance_booking_paypal_payment = function($this, check_in, check_out, guests, listing_id, renter_message, security) {
            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_instance_booking_paypal_payment',
                    'check_in': check_in,
                    'check_out': check_out,
                    'guests': guests,
                    'listing_id': listing_id,
                    'renter_message': renter_message,
                    'security': security,
                },
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                    $('#instance_noti').html('<div class="alert alert-success alert-dismissible" role="alert">'+paypal_connecting+'</div>');
                },
                success: function( data ) {
                    if(data.success) {
                        window.location.href = data.payment_execute_url;
                    } else {
                        $('#instance_noti').html('<div class="alert alert-danger alert-dismissible" role="alert">'+data.message+'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        $('#make_instance_booking_payment').on('click', function(e){
            e.preventDefault();

            var $this = $(this);
            var check_in   = $('#check_in_date').val();
            var check_out  = $('#check_out_date').val();
            var guests     = $('#guests').val();
            var listing_id = $('#listing_id').val();
            var renter_message = $('#renter_message').val();
            var security   = $('#checkout-security').val();

            $('#instance_noti').empty();

            var payment_gateway = $("input[name='payment_gateway']:checked").val();
            if(payment_gateway == undefined ) {
                $('#instance_noti').html('<div class="alert alert-danger alert-dismissible" role="alert">'+choose_gateway_text+'</div>');
            }
            
            if(payment_gateway === 'paypal') {
                homey_instance_booking_paypal_payment($this, check_in, check_out, guests, listing_id, renter_message, security);

            } else if(payment_gateway === 'stripe') {
                var hform = $(this).parents('form');
                hform.find('.homey_stripe_simple button').trigger("click");

            }
            return;

        });

        $('button.homey-booking-step-1').on('click', function(e){
            e.preventDefault();
            var $this = $(this);

            var first_name = $('#first-name').val();
            var last_name = $('#last-name').val();
            var phone = $('#phone').val();

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_instance_step_1',
                    'first_name': first_name,
                    'last_name': last_name,
                    'phone': phone,
                },
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $('.homey-booking-block-body-1 .continue-block-button p.error').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(data) {
                    if( data.success ) {
                        $('.homey-booking-block-title-2').removeClass('inactive mb-0');
                        $('.homey-booking-block-body-2').slideDown('slow');

                        $('.homey-booking-block-title-1').addClass('mb-0');
                        $('.homey-booking-block-body-1').slideUp('slow');
                        $('.homey-booking-block-title-1 .text-success, .homey-booking-block-title-1 .edit-booking-form').removeClass('hidden');
                        $('.homey-booking-block-title-1 .text-success, .homey-booking-block-title-1 .edit-booking-form').show();
                    } else {
                        $('.homey-booking-block-body-1 .continue-block-button').prepend('<p class="error text-danger"><i class="fa fa-close"></i> '+ data.message +'</p>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                }

            });

        });




        $('button.homey-booking-step-2').on('click', function(e){
            e.preventDefault();

            var agreement = $("input[name='agreement']:checked").val();

            $('.homey-booking-block-body-2 .continue-block-button p.error').remove();

            if(agreement != undefined) {

                $('.homey-booking-block-title-3').removeClass('inactive mb-0');
                $('.homey-booking-block-body-3').slideDown('slow');

                $('.homey-booking-block-title-2').addClass('mb-0');
                $('.homey-booking-block-body-2').slideUp('slow');
                $('.homey-booking-block-title-2 .text-success, .homey-booking-block-title-2 .edit-booking-form').removeClass('hidden');
                $('.homey-booking-block-title-2 .text-success, .homey-booking-block-title-2 .edit-booking-form').show();
            } else {
                $('.homey-booking-block-body-2 .continue-block-button').prepend('<p class="error text-danger"><i class="fa fa-close"></i> '+ agree_term_text +'</p>');
            }

        });

        $('.homey-booking-block-title-1 .edit-booking-form').on('click', function(e){
            e.preventDefault();

            $('.homey-booking-block-title-2, .homey-booking-block-title-3').addClass('mb-0');
            $('.homey-booking-block-body-2, .homey-booking-block-body-3').slideUp('slow');

            $('.homey-booking-block-title-1').removeClass('mb-0');
            $('.homey-booking-block-body-1').slideDown('slow');

        });

        $('.homey-booking-block-title-2 .edit-booking-form').on('click', function(e){
            e.preventDefault();

            $('.homey-booking-block-title-1, .homey-booking-block-title-3').addClass('mb-0');
            $('.homey-booking-block-body-1, .homey-booking-block-body-3').slideUp('slow');

            $('.homey-booking-block-title-2').removeClass('mb-0');
            $('.homey-booking-block-body-2').slideDown('slow');

        });


        /*--------------------------------------------------------------------------
         *  Contact listing host
         * -------------------------------------------------------------------------*/
        $( '.contact_listing_host').on('click', function(e) {
            e.preventDefault();

            var $this = $(this);
            var $host_contact_wrap = $this.parents( '.host-contact-wrap' );
            var $form = $this.parents( 'form' );
            var $messages = $host_contact_wrap.find('.homey_contact_messages');

            $.ajax({
                url: ajaxurl,
                data: $form.serialize(),
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.empty().append(response.msg);
                        $form.find('input').val('');
                        $form.find('textarea').val('');
                    } else {
                        $messages.empty().append(response.msg);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });
        });

        /*--------------------------------------------------------------------------
         *   Contact host form on host detail page
         * -------------------------------------------------------------------------*/
        $('#host_detail_contact').on('click', function(e) {
            e.preventDefault();
            var current_element = $(this);
            var $this = $(this);
            var $form = $this.parents( 'form' );

            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                data: $form.serialize(),
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    current_element.children('i').remove();
                    current_element.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function( res ) {
                    current_element.children('i').removeClass(process_loader_spinner);
                    if( res.success ) {
                        $('#form_messages').empty().append(res.msg);
                        current_element.children('i').addClass(success_icon);
                    } else {
                        $('#form_messages').empty().append(res.msg);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }

            });
        });

        
         /*--------------------------------------------------------------------------
         *   Print Property
         * -------------------------------------------------------------------------*/
        if( $('#homey-print').length > 0 ) {
            $('#homey-print').on('click', function (e) {
                e.preventDefault();
                var listingID, printWindow;

                listingID = $(this).attr('data-listing-id');

                printWindow = window.open('', 'Print Me', 'width=850 ,height=842');
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        'action': 'homey_create_print',
                        'listing_id': listingID,
                    },
                    success: function (data) {
                        printWindow.document.write(data);
                        printWindow.document.close();
                        printWindow.focus();
                    },
                    error: function (xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }

                });
            });
        }

        /* ------------------------------------------------------------------------ */
        /*  Homey login and regsiter
         /* ------------------------------------------------------------------------ */
        $('.homey_login_button').on('click', function(e){
            e.preventDefault();
            var current = $(this);
            homey_login( current );
        });

        $('.homey-register-button').on('click', function(e){
            e.preventDefault();
            var current = $(this);
            homey_register( current );
        });

        var homey_login = function( current ) {
            var $form = current.parents('form');
            var $messages = $('.homey_login_messages');

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: $form.serialize(),
                beforeSend: function () {
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function( response ) {
                    if( response.success ) {
                        $messages.empty().append('<p class="success text-success"><i class="fa fa-check"></i> '+ response.msg +'</p>');
                        window.location.reload();

                    } else {
                        $messages.empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ response.msg +'</p>');
                    }

                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
        
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            })

        } // end homey_login

        var homey_register = function ( currnt ) {

            var $form = currnt.parents('form');
            var $messages = $('.homey_register_messages');

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: $form.serialize(),
                beforeSend: function () {
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function( response ) {
                    if( response.success ) {
                        $messages.empty().append('<p class="success text-success"><i class="fa fa-check"></i> '+ response.msg +'</p>');
                    } else {
                        $messages.empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ response.msg +'</p>');
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        /* ------------------------------------------------------------------------ */
        /*  Reset Password
         /* ------------------------------------------------------------------------ */
        $( '#homey_forgetpass').on('click', function(e){
            e.preventDefault();
            var user_login = $('#user_login_forgot').val(),
                security    = $('#homey_resetpassword_security').val();

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_reset_password',
                    'user_login': user_login,
                    'security': security
                },
                beforeSend: function () {
                    $('#homey_msg_reset').empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function( response ) {
                    if( response.success ) {
                        $('#homey_msg_reset').empty().append('<p class="success text-success"><i class="fa fa-check"></i> '+ response.msg +'</p>');
                    } else {
                        $('#homey_msg_reset').empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ response.msg +'</p>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });

        });


        if( $('#homey_reset_password').length > 0 ) {
            $('#homey_reset_password').on('click', function(e) {
                e.preventDefault();

                var $this = $(this);
                var rg_login = $('input[name="rp_login"]').val();
                var rp_key = $('input[name="rp_key"]').val();
                var pass1 = $('input[name="pass1"]').val();
                var pass2 = $('input[name="pass2"]').val();
                var security = $('input[name="homey_resetpassword_security"]').val();

                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        'action': 'homey_reset_password_2',
                        'rq_login': rg_login,
                        'password': pass1,
                        'confirm_pass': pass2,
                        'rp_key': rp_key,
                        'security': security
                    },
                    beforeSend: function( ) {
                        $this.children('i').remove();
                        $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                    },
                    success: function(data) {
                        if( data.success ) {
                            jQuery('#password_reset_msgs').empty().append('<p class="success text-success"><i class="fa fa-check"></i> '+ data.msg +'</p>');
                            jQuery('#oldpass, #newpass, #confirmpass').val('');
                        } else {
                            jQuery('#password_reset_msgs').empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ data.msg +'</p>');
                        }
                    },
                    error: function(errorThrown) {

                    },
                    complete: function(){
                        $this.children('i').removeClass(process_loader_spinner);
                    }

                });

            } );
        }

        /*--------------------------------------------------------------------------
         *   Facebook login
         * -------------------------------------------------------------------------*/
        $('.homey-facebook-login').on('click', function() {
            var current = $(this);
            homey_login_via_facebook( current );
        });

        var homey_login_via_facebook = function ( current ) {
            var $form = current.parents('form');
            var $messages = $('.homey_login_messages');

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action' : 'homey_facebook_login_oauth'
                },
                beforeSend: function () {
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function (data) { 
                    window.location.href = data;
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        /*--------------------------------------------------------------------------
         *  Social Logins
         * -------------------------------------------------------------------------*/
        $('.homey-yahoo-login').on('click', function () {
            var current = $(this);
            homey_login_via_yahoo( current );
        });

        var homey_login_via_yahoo = function ( current ) {
            var $form = current.parents('form');
            var $messages = $('.homey_login_messages');

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action' : 'homey_yahoo_login'
                },
                beforeSend: function () {
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function (data) {
                    window.location.href = data;
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        $('.homey-google-login').on('click', function () {
            var current = $(this);
            homey_login_via_google( current );
        });

        var homey_login_via_google = function ( current ) {
            var $form = current.parents('form');
            var $messages = $('.homey_login_messages');

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action' : 'homey_google_login_oauth'
                },
                beforeSend: function () {
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function (data) { 
                    window.location.href = data;
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }



    }// typeof HOMEY_ajax_vars

}); // end document ready