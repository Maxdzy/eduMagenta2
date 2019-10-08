/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */
define([
    'storefinderAsync!https://maps.googleapis.com/maps/api/js?libraries=places&key=' + window.googleMapsApiKey,
    'Scandiweb_StoreFinder/js/lib/markerclusterer'
], function () {
    return google.maps;
});
