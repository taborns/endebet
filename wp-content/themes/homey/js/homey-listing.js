jQuery(document).ready( function($) {
    "use strict";

    if ( typeof Homey_Listing !== "undefined" ) {
        
        var dtGlobals = {}; // Global storage
        dtGlobals.isMobile	= (/(Android|BlackBerry|iPhone|iPad|Palm|Symbian|Opera Mini|IEMobile|webOS)/.test(navigator.userAgent));
        dtGlobals.isAndroid	= (/(Android)/.test(navigator.userAgent));
        dtGlobals.isiOS		= (/(iPhone|iPod|iPad)/.test(navigator.userAgent));
        dtGlobals.isiPhone	= (/(iPhone|iPod)/.test(navigator.userAgent));
        dtGlobals.isiPad	= (/(iPad|iPod)/.test(navigator.userAgent));

        var ajaxurl = Homey_Listing.ajaxURL;
        
        var are_you_sure_text = Homey_Listing.are_you_sure_text;
        var delete_btn_text = Homey_Listing.delete_btn_text;
        var cancel_btn_text = Homey_Listing.cancel_btn_text;
        var process_loader_refresh = Homey_Listing.process_loader_refresh;
        var process_loader_spinner = Homey_Listing.process_loader_spinner;
        var process_loader_circle = Homey_Listing.process_loader_circle;
        var process_loader_cog = Homey_Listing.process_loader_cog;
        var success_icon = Homey_Listing.success_icon;
        var verify_nonce = Homey_Listing.verify_nonce;
        var verify_file_type = Homey_Listing.verify_file_type;
        var add_listing_msg = Homey_Listing.add_listing_msg;
        var processing_text = Homey_Listing.processing_text;
        var acc_bedroom_name = Homey_Listing.acc_bedroom_name;
        var acc_bedroom_name_plac = Homey_Listing.acc_bedroom_name_plac;
        var acc_guests = Homey_Listing.acc_guests;
        var acc_guests_plac = Homey_Listing.acc_guests_plac;
        var acc_no_of_beds = Homey_Listing.acc_no_of_beds;
        var acc_no_of_beds_plac = Homey_Listing.acc_no_of_beds_plac;
        var acc_bedroom_type = Homey_Listing.acc_bedroom_type;
        var acc_bedroom_type_plac = Homey_Listing.acc_bedroom_type_plac;
        var acc_btn_remove_room = Homey_Listing.acc_btn_remove_room;

        var service_name = Homey_Listing.service_name;
        var service_name_plac = Homey_Listing.service_name_plac;
        var service_price = Homey_Listing.service_price;
        var service_price_plac = Homey_Listing.service_price_plac;
        var service_des = Homey_Listing.service_des;
        var service_des_plac = Homey_Listing.service_des_plac;
        var btn_remove_service = Homey_Listing.btn_remove_service;
        var pricing_link = Homey_Listing.pricing_link;
        var calendar_link = Homey_Listing.calendar_link;
        var geo_coding_msg = Homey_Listing.geo_coding;

        /* ------------------------------------------------------------------------ */
        /*  parseInt Radix 10
        /* ------------------------------------------------------------------------ */
        function parseInt10(val) {
            return parseInt(val, 10);
        }

        /*--------------------------------------------------------------------------
        * Add/Edit listing for autocomplete
        *---------------------------------------------------------------------------*/
        var componentForm_listing = {
            locality: 'long_name',
            administrative_area_level_1: 'long_name',
            country: 'long_name',
            postal_code: 'short_name',
            neighborhood: 'long_name',
            sublocality_level_1: 'long_name',
            political: 'long_name'
        };

        if (document.getElementById('listing_address')) {
            var inputField, defaultBounds, autocomplete;
            inputField = (document.getElementById('listing_address'));
            defaultBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(-90, -180),
                new google.maps.LatLng(90, 180)
            );
            var options = {
                bounds: defaultBounds,
                types: ['geocode'],
            };

            var mapDiv = $('#map');
            var maplat = mapDiv.data('add-lat');
            var maplong = mapDiv.data('add-long');

            var map = new google.maps.Map(document.getElementById('map'), {
              center: {lat: maplat, lng: maplong},
            });

            
            if (document.getElementById('homey_edit_map')) {
                var latlng = {lat: parseFloat(maplat), lng: parseFloat(maplong)};
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map
                });
                map.setZoom(16);
            } else {
                var marker = new google.maps.Marker({
                  map: map,
                  anchorPoint: new google.maps.Point(0, -29)
                });
                map.setZoom(13); 
            }

            autocomplete = new google.maps.places.Autocomplete(inputField, options);

            autocomplete.bindTo('bounds', map);

            var geocoder = new google.maps.Geocoder();

            document.getElementById('find').addEventListener('click', function() {
              marker.setVisible(false);
              homey_geocodeAddress(geocoder, map, marker);
            });


            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();  
                fillInAddress_for_form(place);

                marker.setVisible(false);
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }

                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);  // Why 17? Because it looks good.
                }
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);

                console.log(place);
            
            });
        }

        function homey_geocodeAddress(geocoder, resultsMap, marker) {
            var lat = document.getElementById('lat').value;
            var lng = document.getElementById('lng').value;
            var latlng = {lat: parseFloat(lat), lng: parseFloat(lng)};

            geocoder.geocode({'location': latlng}, function(results, status) {
              if (status === 'OK') {
                var i, has_city, addressType, val;
        
                has_city = 0;

                $('#city').val('');
                $('#countyState').val('');
                $('#zip').val('');
                $('#area').val('');
                $('#homey_country').val('');

                document.getElementById('lat').value = results[0].geometry.location.lat();
                document.getElementById('lng').value = results[0].geometry.location.lng();
                document.getElementById('listing_address').value = results[0].formatted_address;

                // Get each component of the address from the result details
                // and fill the corresponding field on the form.
                for (i = 0; i < results[0].address_components.length; i++) {
                    addressType = results[0].address_components[i].types[0];
                    val = results[0].address_components[i][componentForm_listing[addressType]];
                     
                    if (addressType === 'neighborhood') {
                        $('#area').val(val);

                    } else if (addressType === 'political' || addressType === 'locality' || addressType === 'sublocality_level_1') {
                
                        $('#city').val(val);
                        if(val !== '') {
                            has_city = 1;
                        }
                    } else if(addressType === 'country') {
                        $('#homey_country').val(val);

                    } else if(addressType === 'postal_code') {
                        $('#zip').val(val);

                    } else if(addressType === 'administrative_area_level_1') {
                        $('#countyState').val(val);
                    }
                }

                if(has_city === 0) {
                    get_new_city_2('city', results[0].adr_address);
                }

                // If the place has a geometry, then present it on a map.
                if (results[0].geometry.viewport) {
                    resultsMap.fitBounds(results[0].geometry.viewport);
                } else {
                    resultsMap.setCenter(results[0].geometry.location);
                    resultsMap.setZoom(17);  // Why 17? Because it looks good.
                }
                marker.setPosition(results[0].geometry.location);
                marker.setVisible(true);
                console.log(results);

              } else {
                alert(geo_coding_msg +': '+ status);
              }
            });
        }


        function fillInAddress_for_form(place) {
            var i, has_city, addressType, val;
        
            has_city = 0;
        
            $('#city').val('');
            $('#countyState').val('');
            $('#zip').val('');
            $('#area').val('');
            $('#homey_country').val('');

            document.getElementById('lat').value = place.geometry.location.lat();
            document.getElementById('lng').value = place.geometry.location.lng();
            
            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (i = 0; i < place.address_components.length; i++) {
                addressType = place.address_components[i].types[0];
                val = place.address_components[i][componentForm_listing[addressType]];

                 
                if (addressType === 'neighborhood') {
                    $('#area').val(val);

                } else if (addressType === 'locality') {
            
                    $('#city').val(val);
                    if(val !== '') {
                        has_city = 1;
                    }
                } else if(addressType === 'country') {
                    $('#homey_country').val(val);

                } else if(addressType === 'postal_code') {
                    $('#zip').val(val);

                } else if(addressType === 'administrative_area_level_1') {
                    $('#countyState').val(val);
                }
            }

            $('#address-place').html(place.adr_address);
            
            if(has_city === 0) {
                get_new_city_2('city', place.adr_address);
            }
        }

        function get_new_city_2(stringplace, adr_address) {
            var new_city;
            new_city = $(adr_address).filter('span.locality').html() ;
            $('#'+stringplace).val(new_city);
        }

        /* ------------------------------------------------------------------------ */
        /*  Custom Period Prices 
        /* ------------------------------------------------------------------------ */
        $('#cus_btn_save').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var cus_start_date = $('#cus_start_date').val();
            var cus_end_date = $('#cus_end_date').val();
            var cus_night_price = $('#cus_night_price').val();
            var cus_additional_guest_price = $('#cus_additional_guest_price').val();
            var cus_weekend_price = $('#cus_weekend_price').val();
            var listing_id = $('#listing_id_for_custom').val();

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_add_custom_period',
                    'start_date': cus_start_date,
                    'end_date': cus_end_date,
                    'night_price': cus_night_price,
                    'additional_guest_price': cus_additional_guest_price,
                    'weekend_price': cus_weekend_price,
                    'listing_id': listing_id
                },
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(data) {
                    if( data.success ) {
                        window.location.href = pricing_link;
                    } else {
                       alert(data.message);   
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
        /*  Delete Custom Period Prices 
        /* ------------------------------------------------------------------------ */
        $('.homey_delete_period').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var startdate = $this.data('startdate');
            var enddate = $this.data('enddate');
            var listing_id = $this.data('listingid');

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'homey_delete_custom_period',
                    'start_date': startdate,
                    'end_date': enddate,
                    'listing_id': listing_id
                },
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(data) {
                    if( data.success ) {
                        $this.parents('tr').remove();
                    } else {
                       alert(data.message);   
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

        var homey_validation = function( field_required ) {
            if( field_required != 0 ) {
                return true;
            }
            return false;
        };

        /* ------------------------------------------------------------------------ */
        /*  START CREATE LISTING FORM STEPS AND VALIDATION
        /* ------------------------------------------------------------------------ */
        $("[data-hide]").on("click", function() {
            $(this).closest("." + $(this).attr("data-hide")).hide();
        });

        var current = 1;

        var form = $("#submit_listing_form");
        var formStep = $(".form-step");
        var formStepGal = $(".form-step-gal");
        var btnnext = $(".btn-step-next");
        var btnback = $(".btn-step-back");
        var btnsubmitBlock = $(".btn-step-submit");
        var btnsubmit = btnsubmitBlock.find("button[type='submit']");
        var total_steps = $('#total-steps');
        var steps_counter = $('#step-counter');
        var nav_item = $('.steps-breadcrumb li');


        var errorBlock = $(".validate-errors");
        var errorBlockGal = $(".validate-errors-gal");
        var galThumbs = $(".upload-gallery-thumb");

        total_steps.html(formStep.length);
        steps_counter.html(current);

        // Init buttons and UI
        formStep.not(':eq(0)').hide();
        nav_item.eq(0).addClass('active');
        hideButtons(current);

        $('ul#form_tabs li, .btn-save-listing').on('click', function() {
            
            var currentTab = $('#form_tabs li.active').index();

            if (form.valid()) {
                errorBlock.hide();
            } else {

                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                setTimeout(function() { 
                    
                    $('#form_tabs li, .tab-content div').removeClass('active in');
                    $('#form_tabs li a').attr('aria-expanded', 'false');
                    $('#form_tabs li').eq(currentTab).addClass('active');
                    $('#form_tabs li a').attr('aria-expanded', 'true');
                    $('.tab-content .tab-pane').eq(currentTab).addClass('active in');

                }, 200);
                
                errorBlock.show();
            }
        });

        // Next button click action
        btnnext.on('click', function() {
            $("html, body").animate({
                scrollTop: 0
            }, "slow");

            if (current < formStep.length) {
                // Check validation
                if ($(formStepGal).is(':visible')) {
                    if (!$(galThumbs).length > 0) {
                        errorBlockGal.show();
                        return
                    } else {
                        errorBlockGal.hide();
                    }
                }
                if (form.valid()) {
                    formStep.show();
                    formStep.not(':eq(' + (current++) + ')').hide();
                    nav_item.eq(current - 1).addClass('active');
                    errorBlock.hide();
                } else {
                    errorBlock.show();
                }
            }
            hideButtons(current);
            steps_counter.html(current);
        });

        // Back button click action
        btnback.on('click', function() {
            $("html, body").animate({
                scrollTop: 0
            }, "slow");
            if (current > 1) {
                current = current - 2;
                if (current < formStep.length) {
                    formStep.show();
                    formStep.not(':eq(' + (current++) + ')').hide();
                    nav_item.eq(current).removeClass('active');
                }
            }
            hideButtons(current);
            steps_counter.html(current);
        });

        // Submit button click
        btnsubmit.on('click', function(event) {
            event.preventDefault();
            // Check validation
            if ($(formStepGal).is(':visible')) {
                if (!$(galThumbs).length > 0) {
                    errorBlockGal.show();
                    return
                } else {
                    errorBlockGal.hide();
                }
            }
            if (form.valid()) {
                errorBlock.hide();
                btnsubmit.attr('disabled', true);
            } else {
                errorBlock.show();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");
            }
        });

        if (form.length > 0) {
            form.validate({ // initialize plugin
                ignore: ":hidden:not(.selectpicker)",
                errorPlacement: function(error, element) {
                    return false;
                },
                rules: {
                    night_price: {
                        number: true,
                    }

                }
            });
        }

        // Hide buttons according to the current step
        function hideButtons(current) {
            var limit = parseInt10(formStep.length);

            $(".action").hide();

            if (current < limit) btnnext.show();
            if (current > 1) btnback.show();
            if (current === limit) {
                btnnext.hide();
                btnsubmitBlock.show();
            }
        }
        
        /* ------------------------------------------------------------------------ */
        /*  Print Invoice
        /* ------------------------------------------------------------------------ */
        if( $('#invoice-print-button').length > 0 ) {

            $('#invoice-print-button').on('click', function (e) {
                e.preventDefault();
                var invoiceID, printWindow;
                invoiceID = $(this).attr('data-id');

                printWindow = window.open('', 'Print Me', 'width=700 ,height=842');
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        'action': 'homey_create_invoice_print',
                        'invoice_id': invoiceID,
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

        /*--------------------------------------------------------------------------
         *  Invoice Filter
         * -------------------------------------------------------------------------*/
        $('#invoice_status, #invoice_type').on('change', function() {
            homey_invoices_filter();
        });

        $('#startDate, #endDate').on('focusout', function() {
            homey_invoices_filter();
        })

        var homey_invoices_filter = function() {
            var inv_status = $('#invoice_status').val(),
                inv_type   = $('#invoice_type').val(),
                startDate  = $('#startDate').val(),
                endDate  = $('#endDate').val();

            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                type: 'POST',
                data: {
                    'action': 'homey_invoices_ajax_search',
                    'invoice_status': inv_status,
                    'invoice_type'  : inv_type,
                    'startDate'     : startDate,
                    'endDate'       : endDate
                },
                success: function(res) {
                    if(res.success) {
                        $('#invoices_content').empty().append( res.result );
                        $( '#invoices_total_price').empty().append( res.total_price );
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        /* ------------------------------------------------------------------------ */
        /*  Print Reservation
        /* ------------------------------------------------------------------------ */
        if( $('#printReservation').length > 0 ) {

            $('#printReservation').on('click', function (e) {
                e.preventDefault();
                var reservationID, printWindow;
                reservationID = $(this).attr('data-resvID');

                printWindow = window.open('', 'Print Me', 'width=700 ,height=842');
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        'action': 'homey_create_reservation_print',
                        'reservation_id': reservationID,
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
        /*  START LISTING VIEW
        /* ------------------------------------------------------------------------ */
        var get_title = $("#listing_title");
        var view_title = $("#property-title-fill");
        var selected = null;

        function keyup_fill(ele, ele_place) {
            $(ele).on("keyup", function(event) {
                if ($(ele).attr("name") === "night_price") {
                    if (!$.isNumeric($(ele).val())) {
                        return
                    }
                }

                if ($(ele).attr("name") === "listing_bedrooms" || $(ele).attr("name") === "guests" || $(ele).attr("name") === "baths") {
                    if (!$.isNumeric($(ele).val())) {
                        return
                    }
                }



                var newText = event.target.value;
                $(ele_place).html(newText); 
            });
        }

        keyup_fill("#listing_title", "#title-place");
        keyup_fill("#listing_address", "#address-place");
        keyup_fill("#night_price", "#price-place");
        keyup_fill("#listing_bedrooms", "#total-beds");
        keyup_fill("#guests", "#total-guests");
        keyup_fill("#baths", "#total-baths");

        function amenities_selector(ele, view_ele, is_text) {
            $(ele).on('change', function() {
                if(is_text == 'yes') {
                    var selected = $(this).find("option:selected").text();
                } else {
                    var selected = $(this).find("option:selected").val();
                }
                $(view_ele).html(selected);
            });
        }
        amenities_selector("#listing_type", "#listing-type-view", 'yes');


        /*--------------------------------------------------------------------------
         *  Delete property
         * -------------------------------------------------------------------------*/
        $( '.delete-listing' ).on('click', function () {

            var $this = $( this );
            var listing_id = $this.data('id');
            var nonce = $this.data('nonce');

            bootbox.confirm({
                message: "<p><strong>"+are_you_sure_text+"</strong></p>",
                buttons: {
                    confirm: {
                        label: delete_btn_text,
                        className: 'btn btn-primary btn-half-width'
                    },
                    cancel: {
                        label: cancel_btn_text,
                        className: 'btn btn-grey-outlined btn-half-width'
                    }
                },
                callback: function (result) {
         
                    if(result==true) {

                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: ajaxurl,
                            data: {
                                'action': 'homey_delete_listing',
                                'listing_id': listing_id,
                                'security': nonce
                            },
                            beforeSend: function( ) {
                                $this.find('i').removeClass('fa-trash');
                                $this.find('i').addClass('fa-spin fa-spinner');
                            },
                            success: function(data) {
                                if ( data.success == true ) {
                                    window.location.reload();
                                } else {
                                    jQuery('#homey_modal').modal('hide');
                                    alert( data.reason );
                                }
                            },
                            error: function(errorThrown) {

                            }
                        }); // $.ajax
                    } // result
                } // Callback
            });

            return false;

        });

        /*---------------------------------------------------------------------------
         *
         * Messaging system
         * -------------------------------------------------------------------------*/

        /*
         * Message Thread Form
         * -----------------------------*/
        $('.start_thread_form').on('click', function(e) {
            e.preventDefault();

            var $this = $(this);
            var $form = $this.parents( 'form' );
            var $result = $('.messages-notification');
            
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
                        $result.empty().append(response.msg);
                        $form.find('input').val('');
                        $form.find('textarea').val('');
                        window.location.replace( response.redirect_link );
                    } else {
                        $result.empty().append(response.msg);
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


        /*
         * Property Message Notifications
         * -----------------------------*/
        var houzez_message_notifications = function () {

            $.ajax({
                url: ajaxurl,
                data: {
                    action : 'houzez_chcek_messages_notifications'
                },
                method: "POST",
                dataType: "JSON",

                beforeSend: function( ) {
                    // code here...
                },
                success: function(response) {
                    if( response.success ) {
                        if ( response.notification ) {
                            $( '.user-alert' ).show();
                            $( '.msg-alert' ).show();
                        } else {
                            $( '.user-alert' ).hide();
                            $( '.msg-alert' ).hide();
                        }
                    }
                }
            });

        };


        /*
         * Property Thread Message Form
         * -----------------------------*/
        $('.start_thread_message_form').on('click', function(e) {

            e.preventDefault();

            var $this = $(this);
            var $form = $this.parents( 'form' );
            var $result = $('.messages-notification');

            $.ajax({
                url: ajaxurl,
                data: $form.serialize(),
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function( response ) {
                    if( response.success ) {
                        window.location.replace( response.url );
                    } else {
                        $result.empty().append(response.msg);
                    }
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });

        });

        
        $('.homey_delete_msg_thread').on('click', function(e) {
            e.preventDefault();

            var $this = $( this );
            var thread_id = $this.data('thread-id');
            var sender_id = $this.data('sender-id');
            var receiver_id = $this.data('receiver-id');

            bootbox.confirm({
                message: "<p><strong>"+are_you_sure_text+"</strong></p>",
                buttons: {
                    confirm: {
                        label: delete_btn_text,
                        className: 'btn btn-primary btn-half-width'
                    },
                    cancel: {
                        label: cancel_btn_text,
                        className: 'btn btn-grey-outlined btn-half-width'
                    }
                },
                callback: function (result) {
         
                    if(result==true) {

                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: ajaxurl,
                            data: {
                                'action': 'homey_delete_message_thread',
                                'thread_id': thread_id,
                                'sender_id': sender_id,
                                'receiver_id': receiver_id
                            },
                            beforeSend: function( ) {
                                $this.find('i').removeClass('fa-trash');
                                $this.find('i').addClass('fa-spin fa-spinner');
                            },
                            success: function(data) {
                                if ( data.success == true ) {
                                    window.location.reload();
                                } else {
                                    jQuery('#homey_modal').modal('hide');
                                }
                            },
                            error: function(errorThrown) {

                            }
                        }); // $.ajax
                    } // result
                } // Callback
            });

        });

        $('.homey_delete_message').on('click', function(e) {
            e.preventDefault();

            var $this = $( this );
            var message_id = $this.data('message-id');
            var created_by = $this.data('created-by');

            bootbox.confirm({
                message: "<p><strong>"+are_you_sure_text+"</strong></p>",
                buttons: {
                    confirm: {
                        label: delete_btn_text,
                        className: 'btn btn-primary btn-half-width'
                    },
                    cancel: {
                        label: cancel_btn_text,
                        className: 'btn btn-grey-outlined btn-half-width'
                    }
                },
                callback: function (result) {
         
                    if(result==true) {

                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: ajaxurl,
                            data: {
                                'action': 'homey_delete_message',
                                'message_id': message_id,
                                'created_by': created_by
                            },
                            beforeSend: function( ) {
                                $this.find('i').removeClass('fa-trash');
                                $this.find('i').addClass('fa-spin fa-spinner');
                            },
                            success: function(data) {
                                if ( data.success == true ) {
                                    window.location.reload();
                                } else {
                                    jQuery('#homey_modal').modal('hide');
                                }
                            },
                            error: function(errorThrown) {

                            }
                        }); // $.ajax
                    } // result
                } // Callback
            });

        });


        var homey_processing_modal = function ( msg ) {
            var process_modal ='<div class="modal fade" id="homey_modal" tabindex="-1" role="dialog" aria-labelledby="faveModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body homey_messages_modal">'+msg+'</div></div></div></div></div>';
            jQuery('body').append(process_modal);
            jQuery('#homey_modal').modal();
        };

        var homey_processing_modal_close = function ( ) {
            jQuery('#homey_modal').modal('hide');
        };

        /* ------------------------------------------------------------------------ */
        /*  Listing Thumbnails actions ( make features & delete )
         /* ------------------------------------------------------------------------ */
        var lisitng_thumbnail_event = function() {

            // Set Featured Image
            $('.icon-featured').on('click', function(e){
                e.preventDefault();

                var $this = jQuery(this);
                var thumb_id = $this.data('attachment-id');
                var thumb = $this.data('thumb');
                var icon = $this.find( 'i');

                $('.upload-view-media .media-image img').attr('src',thumb);
                $('.upload-gallery-thumb-buttons .featured_image_id').remove();
                $('.upload-gallery-thumb-buttons .icon-featured i').removeClass('fa-star').addClass('fa-star-o');

                $this.closest('.upload-gallery-thumb-buttons').append('<input type="hidden" class="featured_image_id" name="featured_image_id" value="'+thumb_id+'">');
                icon.removeClass('fa-star-o').addClass('fa-star');
            });

            //Remove Image
            $('.icon-delete').on('click', function(e){
                e.preventDefault();

                var $this = $(this);
                var thumbnail = $this.closest('.listing-thumb');
                var loader = $this.siblings('.icon-loader');
                var listing_id = $this.data('listing-id');
                var thumb_id = $this.data('attachment-id');

                loader.show();

                var ajax_request = $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        'action': 'homey_remove_listing_thumbnail',
                        'listing_id': listing_id,
                        'thumb_id': thumb_id,
                        'removeNonce': verify_nonce
                    }
                });

                ajax_request.done(function( response ) {
                    if ( response.remove_attachment ) {
                        thumbnail.remove();
                    } else {

                    }
                });

                ajax_request.fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                });

            });

        }

        lisitng_thumbnail_event();


        /*--------------------------------------------------------------------------
         *  Uplaod listing gallery
         * -------------------------------------------------------------------------*/
        var listing_gallery_images = function() {

            $( "#homey_gallery_container" ).sortable({
                placeholder: "sortable-placeholder"
            });

            var plup_uploader = new plupload.Uploader({
                browse_button: 'select_gallery_images',
                file_data_name: 'listing_upload_file',
                container: 'homey_gallery_dragDrop',
                drop_element: 'homey_gallery_dragDrop',
                url: ajaxurl + "?action=homey_listing_gallery_upload&verify_nonce=" + verify_nonce,
                filters: {
                    mime_types : [
                        { title : verify_file_type, extensions : "jpg,jpeg,gif,png" }
                    ],
                    max_file_size: '10m',//image_max_file_size,
                    prevent_duplicates: false
                }
            });
            plup_uploader.init();

            plup_uploader.bind('FilesAdded', function(up, files) {
                var homey_thumbs = "";
                var maxfiles = '50';//max_prop_images;
                if(up.files.length > maxfiles ) {
                    up.splice(maxfiles);
                    alert('no more than '+maxfiles + ' file(s)');
                    return;
                }
                plupload.each(files, function(file) {
                    homey_thumbs += '<div id="thumb-holder-' + file.id + '" class="col-sm-2 col-xs-4 listing-thumb">' + '' + '</div>';
                });
                document.getElementById('homey_gallery_container').innerHTML += homey_thumbs;
                up.refresh();
                plup_uploader.start();
            });


            plup_uploader.bind('UploadProgress', function(up, file) {
                document.getElementById( "thumb-holder-" + file.id ).innerHTML = '<span>' + file.percent + "%</span>";
            });

            plup_uploader.bind('Error', function( up, err ) {
                document.getElementById('homey_errors').innerHTML += "<br/>" + "Error #" + err.code + ": " + err.message;
            });

            plup_uploader.bind('FileUploaded', function ( up, file, ajax_response ) {
                var response = $.parseJSON( ajax_response.response );
               

                if ( response.success ) {

                    var gallery_thumbnail = '<figure class="upload-gallery-thumb">' +
                                        '<img src="' + response.url + '" alt="thumb">' +
                                    '</figure>' +
                                    '<div class="upload-gallery-thumb-buttons">' +
                                        '<button class="icon-featured" data-thumb="' + response.thumb + '" data-listing-id="' + 0 + '"  data-attachment-id="' + response.attachment_id + '"><i class="fa fa-star-o"></i></button>' +
                                        '<button class="icon-delete" data-listing-id="' + 0 + '"  data-attachment-id="' + response.attachment_id + '"><i class="fa fa-trash-o"></i></button>' +
                                        '<input type="hidden" class="listing-image-id" name="listing_image_ids[]" value="' + response.attachment_id + '"/>' +
                                    '</div>'+
                                    '<span style="display: none;" class="icon icon-loader"><i class="fa fa-spinner fa-spin"></i></span>';    

                    document.getElementById( "thumb-holder-" + file.id ).innerHTML = gallery_thumbnail;

                    lisitng_thumbnail_event();

                } else {
                    console.log ( response );
                }
            });

        }
        listing_gallery_images();

        /* ------------------------------------------------------------------------ */
        /*  Bedrooms
         /* ------------------------------------------------------------------------ */

        $( '#add_more_bedrooms' ).on('click', function( e ){
            e.preventDefault();

            var numVal = $(this).data("increment") + 1;
            $(this).data('increment', numVal);
            $(this).attr({
                "data-increment" : numVal
            });

            var newBedroom = '' +
                '<div class="more_rooms_wrap">'+
                '<div class="row">'+
                '<div class="col-sm-6 col-xs-12">'+
                    '<div class="form-group">'+
                        '<label for="acc_bedroom_name">'+acc_bedroom_name+'</label>'+
                        '<input type="text" name="homey_accomodation['+numVal+'][acc_bedroom_name]" class="form-control" placeholder="'+acc_bedroom_name_plac+'">'+
                    '</div>'+
                '</div>'+
                '<div class="col-sm-6 col-xs-12">'+
                    '<div class="form-group">'+
                        '<label for="acc_guests">'+acc_guests+'</label>'+
                        '<input type="text" name="homey_accomodation['+numVal+'][acc_guests]" class="form-control" placeholder="'+acc_guests_plac+'">'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<div class="row">'+
                '<div class="col-sm-6 col-xs-12">'+
                    '<div class="form-group">'+
                        '<label for="acc_no_of_beds">'+acc_no_of_beds+'</label>'+
                        '<input type="text" name="homey_accomodation['+numVal+'][acc_no_of_beds]" class="form-control" placeholder="'+acc_no_of_beds_plac+'">'+
                    '</div>'+
                '</div>'+
                '<div class="col-sm-6 col-xs-12">'+
                    '<div class="form-group">'+
                        '<label for="acc_bedroom_type">'+acc_bedroom_type+'</label>'+
                        '<input type="text" name="homey_accomodation['+numVal+'][acc_bedroom_type]" class="form-control" placeholder="'+acc_bedroom_type_plac+'">'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<div class="row">'+
                '<div class="col-sm-12 col-xs-12">'+
                    '<button type="button" data-remove="'+numVal+'" class="btn btn-primary remove-beds">'+acc_btn_remove_room+'</button>'+
               ' </div>'+
            '</div>'+
            '<hr>';
            '</div>';

            $( '#more_bedrooms_main').append( newBedroom );
            removeBedroom();
        });

        var removeBedroom = function (){

            $( '.remove-beds').on('click', function( event ){
                event.preventDefault();
                var $this = $( this );
                $this.closest( '.more_rooms_wrap' ).remove();
            });
        }
        removeBedroom();

        /* ------------------------------------------------------------------------ */
        /*  Services
         /* ------------------------------------------------------------------------ */

        $( '#add_more_service' ).on('click', function( e ){
            e.preventDefault();

            var numVal = $(this).data("increment") + 1;
            $(this).data('increment', numVal);
            $(this).attr({
                "data-increment" : numVal
            });

            var newService = '' +
                '<div class="more_services_wrap">'+
                '<div class="row">'+
                '<div class="col-sm-6 col-xs-12">'+
                    '<div class="form-group">'+
                        '<label for="service_name">'+service_name+'</label>'+
                        '<input type="text" name="homey_services['+numVal+'][service_name]" class="form-control" placeholder="'+service_name_plac+'">'+
                    '</div>'+
                '</div>'+
                '<div class="col-sm-6 col-xs-12">'+
                    '<div class="form-group">'+
                        '<label for="service_price">'+service_price+'</label>'+
                        '<input type="text" name="homey_services['+numVal+'][service_price]" class="form-control" placeholder="'+service_price_plac+'">'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<div class="row">'+
                '<div class="col-sm-12 col-xs-12">'+
                    '<div class="form-group">'+
                        '<label for="service_des">'+service_des+'</label>'+
                        '<textarea placeholder="'+service_des_plac+'" rows="3" name="homey_services['+numVal+'][service_des]" class="form-control"></textarea>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<div class="row">'+
                '<div class="col-sm-12 col-xs-12">'+
                    '<button type="button" data-remove="'+numVal+'" class="btn btn-primary remove-service">'+btn_remove_service+'</button>'+
               ' </div>'+
            '</div>'+
            '<hr>';
            '</div>';

            $( '#more_services_main').append( newService );
            removeService();
        });

        var removeService = function (){

            $( '.remove-service').on('click', function( event ){
                event.preventDefault();
                var $this = $( this );
                $this.closest( '.more_services_wrap' ).remove();
            });
        }
        removeService();

        /*--------------------------------------------------------------------------
         *  Thread Message Attachment
         * -------------------------------------------------------------------------*/
        var thread_message_attachment = function() {

            /* initialize uploader */
            var uploader = new plupload.Uploader({
                browse_button: 'thread-message-attachment',
                file_data_name: 'messages_upload_file',
                container: 'listing-thumbs-container',
                multi_selection: true,
                url: ajaxurl + "?action=homey_message_attacment_upload&verify_nonce=" + verify_nonce,
                filters: {

                    max_file_size: '20m',
                    prevent_duplicates: true
                }
            });
            uploader.init();

            uploader.bind('FilesAdded', function(up, files) {
                var html = '';
                var listingThumb = "";
                var maxfiles = '10';
                if(up.files.length > maxfiles ) {
                    up.splice(maxfiles);
                    alert('no more than '+maxfiles + ' file(s)');
                    return;
                }
                plupload.each(files, function(file) {
                    listingThumb += '<li id="thumb-holder-' + file.id + '" class="listing-thumb">' + '' + '</li>';
                });
                document.getElementById('listing-thumbs-container').innerHTML += listingThumb;
                up.refresh();
                uploader.start();
            });


            uploader.bind('UploadProgress', function(up, file) {
                document.getElementById( "thumb-holder-" + file.id ).innerHTML = '<li><lable>' + file.name + '<span>' + file.percent + "%</span></lable></li>";
            });

            uploader.bind('Error', function( up, err ) {
                document.getElementById('errors-log').innerHTML += "<br/>" + "Error #" + err.code + ": " + err.message;
            });

            uploader.bind('FileUploaded', function ( up, file, ajax_response ) {
                var response = $.parseJSON( ajax_response.response );

                if ( response.success ) {

                    console.log( ajax_response );

                    var message_html = 
                        '<div class="attach-icon delete-attachment">' +
                        '<i class="fa fa-trash remove-message-attachment" data-attachment-id="' + response.attachment_id + '"></i>' +
                        '</div>' +
                        '<span class="attach-text">' + response.file_name + '</span>' +
                        '<input type="hidden" class="listing-image-id" name="listing_image_ids[]" value="' + response.attachment_id + '"/>' ;

                    document.getElementById( "thumb-holder-" + file.id ).innerHTML = message_html;

                    messageAttachment();
                    thread_message_attachment();

                } else {
                    console.log ( response );
                    alert('error');
                }
            });

            uploader.refresh();

        }
        thread_message_attachment();

        var messageAttachment = function() {

            $( '.remove-message-attachment' ).on('click', function () {

                var $this = $(this);
                var thumbnail = $this.closest('li');
                var thumb_id = $this.data('attachment-id');
                $this.removeClass( 'fa-trash' );
                $this.addClass( 'fa-spinner' );

                var ajax_request = $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        'action': 'homey_remove_message_attachment',
                        'thumbnail_id': thumb_id,
                    }
                });

                ajax_request.done(function( response ) {
                    if ( response.attachment_remove ) {
                        thumbnail.remove();
                    } else {

                    }
                    thread_message_attachment();
                });

                ajax_request.fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                });

            });

        }


    } // End Type Of

});