/**
 * show properties on map with group cluster
 * 
 */
(function() {
    // Define the overlay which will be displayed when click on marker, derived from google.maps.OverlayView
    function TrexanhPropertyOverlay(opt_options) {
        this.setValues(opt_options);

        // TrexanhPropertyOverlay specific
        var inner = this.inner_ = document.createElement('div');
        var div = this.div_ = document.createElement('div');
        div.appendChild(inner);
        div.style.cssText = 'position: absolute; display: none';
    };
    TrexanhPropertyOverlay.prototype = new google.maps.OverlayView;

    TrexanhPropertyOverlay.prototype.onAdd = function() {
        var pane = this.getPanes().overlayMouseTarget;
        pane.appendChild(this.div_);
    };

    TrexanhPropertyOverlay.prototype.onRemove = function() {
        this.div_.parentNode.removeChild(this.div_);
    };

    TrexanhPropertyOverlay.prototype.draw = function() {
        var projection = this.getProjection();
        var position = projection.fromLatLngToDivPixel( this.get( 'position' ) );

        var div = this.div_;
        div.style.display = 'block';

        this.inner_.innerHTML = this.html;
        
        // set marker_height based on marker icon
        var marker_height = this.marker.getIcon().size.height; 
        var offset = 10;
        
        //change method to get info box's height
        var box_height = jQuery(this.inner_).height() ? jQuery(this.inner_).height() : jQuery(this.div_).prop("scrollHeight");
        var box_width = jQuery(this.inner_).width();
        div.style.top = ( position.y - ( box_height + marker_height + offset ) ) + 'px';
        div.style.left = ( position.x - box_width / 2 ) + 'px';
        // move to marker to view full box with markers which is close to edge of map container
        if ( position.x < box_width / 2 || position.y < box_height ) {
            this.map.panTo( this.position );
        }
    };
    
    TrexanhPropertyOverlay.prototype.setContent = function(content) {
        this.inner_.innerHTML = content;
    };
    
    TrexanhPropertyOverlay.prototype.clear = function() {
        this.setValues( { map: null, position: null, content : "" } );
    };
    
    function refreshMap( map, data, overlay ) {
        var markers = [];
        for ( var i = 0; i < data.length; i++ ) {
            var property = data[i];
            if ( ! property.latitude && ! property.longitude ) {
                continue;
            }
            if ( property.type == 'lease' ) {
                property.type = 'rent';
            }
            var latLng = new google.maps.LatLng( property.latitude, property.longitude );
            var marker = new google.maps.Marker( {
                position: latLng,
                draggable: false,
                icon: window.map_helper_functions.get_property_marker_url( property.category )
            } );
            // Allow each marker to have an info window    
            google.maps.event.addListener(marker, 'click', ( function ( map, marker, property ) {
                return function () {
                    var info_box_template = _.template( jQuery( "#map-info-box-template" ).html() );
                    var content = info_box_template( { property : property } );
                    var position = marker.getPosition();
                    overlay.setValues({
                        map: map,
                        position: position,
                        html: content,
                        marker: marker //pass marker to Overlay.prototype.draw function to get marker's size
                    });
                };
            } )( map, marker, property ) );
            markers.push( marker );
        }
        
        // get styles for marker clusterer
        var styles = window.map_helper_functions.get_property_marker_url( 'group' );
        var markerClusterer = new MarkerClusterer( map, markers, {
            styles : styles
        } );
        // set center, zoom for map to cover all places
        var bounds = new google.maps.LatLngBounds();
        for (i = 0; i < markers.length; i++) {
            bounds.extend(markers[i].getPosition());
        }
        map.fitBounds(bounds);
    }

    function initialize( target_dom, data ) {
        var options = {
            zoom: 10,
            center: new google.maps.LatLng(39.91, 116.38),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        if ( window.TrexanhProperty && window.TrexanhProperty.map_styles ) {
            options.styles = window.TrexanhProperty.map_styles;
        }
        var map = new google.maps.Map( document.getElementById( target_dom ), options );

        var overlay = new TrexanhPropertyOverlay();
        google.maps.event.addListener( map, "click", function () {
            overlay.clear();
        } );
        google.maps.event.addListener( map, "zoom_changed", function () {
            overlay.clear();
        } );
        refreshMap( map, data, overlay );
    }
    if ( ! window.TrexanhProperty ) {
        window.TrexanhProperty = {};
    }
    window.TrexanhProperty.properties_mapping = function( target_dom, data ) {
        google.maps.event.addDomListener( window, 'load', initialize( target_dom, data ) );
    };
})();