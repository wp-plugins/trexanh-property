window.map_helper_functions = {};
window.map_helper_functions.get_property_marker_url = function ( property_category ) {
    if ( window.TrexanhProperty === undefined || window.TrexanhProperty.marker_icons === undefined ) {
        return null;
    }
    return window.TrexanhProperty.marker_icons[property_category];
};
