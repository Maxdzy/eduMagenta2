/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */
define([
    'storefinderAsync!https://maps.googleapis.com/maps/api/js?libraries=places&key=' + window.googleMapsApiKey,
    'Edu_StoreFinderCompletion/js/lib/markerclusterer'
], function () {
    return google.maps;
});
