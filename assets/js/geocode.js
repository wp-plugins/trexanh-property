window.txl_map = {};

/**
 * show address on map based on pre-provided address coordinates
 * no need to send request outside to ask for coordinates
 */
window.txl_map.show_map_at_coordinates = function( target_element_id, latitude, longitude, marker_icon, map_styles ) {
    var myLatlng = new google.maps.LatLng( latitude, longitude );
    var map_options = {
            zoom: 12,
            center: myLatlng
    };
    if ( map_styles ) {
        map_options.styles = map_styles;
    }
    var target = document.getElementById(target_element_id);
    if ( ! parseInt( window.getComputedStyle( target, null ).getPropertyValue( "height" ) ) ) {
        target.style.minHeight = "300px";
    }
    var map = new google.maps.Map( target, map_options );

    var marker = new google.maps.Marker( {
            position: myLatlng,
            map: map,
            icon: marker_icon
    } );
};

/**
 * take address information as params then send request to google to ask for address coordinates
 * then show address on map
 */
window.txl_map.ajax_gen_map = function( target_element_id, location, success_callback, error_callback, marker_icon, map_styles ) {
    var loc_string = location.street_number + ' ' + location.street + ', ' + location.city + ', ' + location.state + ', ' + location.country;
    //replace multiple spaces with single one
    loc_string = loc_string.replace( / +/g, " " );
    var geocoder = new google.maps.Geocoder();

    geocoder.geocode( { 'address': loc_string }, function( results, status ) {
        if (status == google.maps.GeocoderStatus.OK) {
            var latitude = results[0].geometry.location.lat();
            var longitude = results[0].geometry.location.lng();
            if ( success_callback ) {
                success_callback( latitude, longitude );
            }
            window.txl_map.show_map_at_coordinates( target_element_id, latitude, longitude, marker_icon, map_styles );
        } else {
            if ( error_callback ) {
                error_callback();
            }
        }
    });
};
jQuery( document ).ready( function( $ ) {
    $( '.geocoder' ).on( 'click', function() {
        var prefix = 'txp_property_';
        var street_number = $('[name=' + prefix + 'address_street_number]').val(),
            street = $('[name=' + prefix + 'address_street]').val(),
            city = $('[name=' + prefix + 'address_city]').val(),
            state = $('[name=' + prefix + 'address_state]').val(),
            country = $('[name=' + prefix + 'address_country').val();
        var location = {
            street_number : street_number,
            street : street,
            city : city,
            state : state,
            country : country
        };
        window.txl_map.ajax_gen_map( 'map-canvas', location, function(latitude, longitude) {
            $('[name=' + prefix + 'address_coordinates]').val(latitude + ',' + longitude);
            $('.map td').html('<div id="map-canvas" style="height:450px;width:100%;"></div>');
            $(".map").show();
        }, function() {
            $('.map td').html('<div id="map-canvas">Location not found.</div>');
            $(".map").show();
        } );
        return false;
    } );
} );