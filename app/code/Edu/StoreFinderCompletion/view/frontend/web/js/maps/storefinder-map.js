/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */
define([
    'uiComponent',
    'jquery',
    'ko',
    'uiRegistry',
    'mage/translate',
    'gmap',
    'storefinderMapStores'
], function (uiComponent, $, ko, registry, $t, gmap, MapStores) {
    var GoogleMap = {
        selector: null,
        placeSearchSelector: null,
        storesNearYouPopup: null,
        stores: null,
        startLat: 51.5074,
        startLng: 0.1278,
        infoWindowTemplate: window.googleMapsInfoWindowTemplate,
        /**
         * Types: "search", "geolocation", null
         */
        updateType: null,
        canTriggerIdle: true,
        boundUpdateTimeout: null,

        map: null,
        mapParams: {},
        placeSearch: null,
        autocompleteService: null,
        placeService: null,

        initialize: function () {
            var params = $.extend({
                center: {
                    lat: this.startLat,
                    lng: this.startLng
                },
                zoom: 12
            }, this.mapParams);
            this.map = new gmap.Map($(this.selector).get(0), params);

            this.addMapListeners();
            this.stores = MapStores.stores;
            MapStores.infoWindowTemplate = this.infoWindowTemplate;
            MapStores.setMap(this.map);

            this.initPlaceSearch();
        },

        /**
         * Place Search is used when user clicks Submit button or presses Enter key
         * when entering location in search input
         */
        initPlaceSearch: function () {
            var input = $(this.placeSearchSelector)[0];
            if (typeof(input) === 'undefined') {
                return;
            }

            this.placeSearch = new gmap.places.Autocomplete(input);
            this.placeService = new gmap.places.PlacesService(this.map);
            this.autocompleteService = new gmap.places.AutocompleteService();

            var self = this;
            this.placeSearch.addListener('place_changed', function() {
                var place = self.placeSearch.getPlace();
                if (!place.geometry) {
                    self.submitPlaceSearch();
                    return;
                }

                self.setCenter(place.geometry.location);
                self.updateMapSearch();
            });
        },

        /**
         * Add map listeners
         * Suggested to add timeouts to functions that could be called many times
         * to make experience more streamline
         * (e.g. onCenterChange isn't called 100 times per second when map is dragged)
         */
        addMapListeners: function () {
            var self = this;
            this.map.addListener('idle', function () {
                self.updateMapBounds();
            });
        },

        submitPlaceSearch: function (storefinderList) {
            var search = $(this.placeSearchSelector).val();
            if (!search) {
                if (typeof(storefinderList) === 'object') {
                    storefinderList.errorMessage($t('Please enter City, State, or Zip Code in search field'));
                }
                return;
            }
            var self = this;
            // Nested API calls - bad, but placeService.textSearch doesn't want to return results
            // that autocomplete does, and autocomplete doesn't return latitude/longitude
            this.autocompleteService.getQueryPredictions({input: search}, function (result, status) {
                if (status === gmap.places.PlacesServiceStatus.OK && result.length > 0) {
                    self.placeService.getDetails({placeId: result[0].place_id}, function (place, status) {
                        if (status === gmap.places.PlacesServiceStatus.OK) {
                            self.setCenter(place.geometry.location);
                            self.updateMapSearch();
                        } else {
                            MapStores.setStores([]);
                        }
                    });
                } else {
                    MapStores.setStores([]);
                }
            });
        },

        setCenter: function (location) {
            this.canTriggerIdle = false;
            this.map.setCenter(location);
        },

        setZoom: function (zoom) {
            this.canTriggerIdle = false;
            this.map.setZoom(zoom);
        },

        goToPoint: function (point) {
            this.setCenter(new gmap.LatLng({
                lat: point.lat,
                lng: point.lng
            }));
            this.updateMapGeoLocation();
        },

        /**
         * To be overwritten
         */
        updateMapBounds: function () {},

        /**
         * To be overwritten
         */
        updateMapSearch: function () {},

        /**
         * To be overwritten
         */
        updateMapGeoLocation: function () {}
    };

    return function (data) {
        var map = $.extend({}, GoogleMap, data);
        map.initialize();

        return map;
    };
});
