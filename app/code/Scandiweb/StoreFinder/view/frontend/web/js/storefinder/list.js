/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

define([
    'storefinderStoreList',
    'jquery',
    'ko',
    'uiRegistry',
    'mage/translate',
    'storefinderMap',
    'storefinderMapStores',
    'koChosen'
], function (Component, $, ko, registry, $t, StoreFinderMap, MapStores) {
    'use strict';

    return Component.extend({
        gmapSelector: null,
        placeSearchSelector: null,
        distanceSelector: null,
        storesNearYouPopup: null,
        infoWindowTemplate: null,
        mapMarkerUrl: null,
        searchFromCustomer: 1000,
        zoomScale: [21282, 16355, 10064, 5540, 2909, 1485, 752, 378, 190, 95, 48, 24, 12, 6, 3, 1.48, 0.74, 0.37, 0.2],

        /**
         * params contains:
         *   lat - Current Latitude
         *   lon - Current Longitude
         *   distance - Maximum distance
         *   page - Page of stores
         *   limit - Page size
         */
        params: {},

        storeFinderMap: null,

        /** @inheritdoc */
        initialize: function () {
            this._super();

            this.activate();
            this.initListeners();
            this.initStoresNearYouPopup();
        },

        initStoresNearYouPopup: function () {
            var popup = registry.get(this.storesNearYouPopup);

            popup.activateGeoLocation();
        },

        /**
         * Returns ko observableArray
         * @returns {object}
         */
        getStores: function () {
            return MapStores.stores;
        },

        /**
         * @returns {boolean}
         */
        hasStores: function () {
            return MapStores.stores().length > 0;
        },

        /**
         * @param response
         */
        onAjaxSuccess: function (response) {
            if (typeof(response.stores) === 'undefined') {
                this.onAjaxError($t('Something went wrong with request'));
            } else {
                if (typeof(response.params) !== 'object' || response.params === null) {
                    this.onAjaxError($t('Something went wrong with request'));
                }
                $.extend(this.params, response.params);
                if (this.params.loadMore === "true") {
                    $.each(response.stores, function (key, value) {
                        MapStores.addStore(value);
                    });
                    if (MapStores.getStores().length === 0) {
                        this.errorMessage($t('No stores found at this location.'));
                    }
                } else {
                    MapStores.setStores(response.stores);
                    if (response.stores.length === 0) {
                        this.errorMessage($t('No stores found at this location.'));
                    }
                }
                if (this.storeFinderMap === null) {
                    this.initMap();
                }
            }
        },

        /**
         * StoreFinderMap object
         * @param storefinderMap
         */
        updateMapSearch: function (storefinderMap) {
            var map = storefinderMap.map,
                minDimension = Math.min($(this.gmapSelector).height(), $(this.gmapSelector).width()),
                distance = $(this.distanceSelector).val();
            storefinderMap.setZoom(this.getSuggestedZoom(minDimension, distance));
            this.params.bounds = null;
            this.params.lat = map.center.lat();
            this.params.lon = map.center.lng();
            var bounds = map.getBounds();
            this.params.bounds = {
                lat_max: bounds.getNorthEast().lat(),
                lng_max: bounds.getNorthEast().lng(),
                lat_min: bounds.getSouthWest().lat(),
                lng_min: bounds.getSouthWest().lng()
            };
            this.params.loadMore = 'false';

            this.updateAjax(this.params);
        },

        /**
         * StoreFinderMap object
         * @param storefinderMap
         */
        updateMapGeoLocation: function (storefinderMap) {
            var map = storefinderMap.map,
                minDimension = Math.min($(this.gmapSelector).height(), $(this.gmapSelector).width()),
                distance = this.searchFromCustomer;
            storefinderMap.setZoom(this.getSuggestedZoom(minDimension, distance));
            this.params.bounds = null;
            this.params.lat = map.center.lat();
            this.params.lon = map.center.lng();
            var bounds = map.getBounds();
            this.params.bounds = {
                lat_max: bounds.getNorthEast().lat(),
                lng_max: bounds.getNorthEast().lng(),
                lat_min: bounds.getSouthWest().lat(),
                lng_min: bounds.getSouthWest().lng()
            };
            this.params.loadMore = 'false';

            this.updateAjax(this.params);
        },

        /**
         * StoreFinderMap object
         * @param storefinderMap
         */
        updateMapBounds: function (storefinderMap) {
            var map = storefinderMap.map;
            this.params.lat = map.center.lat();
            this.params.lon = map.center.lng();
            var bounds = map.getBounds();
            this.params.bounds = {
                lat_max: bounds.getNorthEast().lat(),
                lng_max: bounds.getNorthEast().lng(),
                lat_min: bounds.getSouthWest().lat(),
                lng_min: bounds.getSouthWest().lng()
            };
            this.params.loadMore = 'true';

            this.updateAjax(this.params);
        },

        /**
         * @param minDimension (px)
         * @param radius (m)
         * @return {integer}
         */
        getSuggestedZoom: function (minDimension, radius) {
            var ratio = (radius * 2) / minDimension,
                counter = 0;
            while (counter <= 19 && ratio < this.zoomScale[counter]) {
                counter++;
            }

            return counter;
        },

        initMap: function () {
            var self = this,
                params = {
                    mapParams: {
                        zoomControl: true,
                        mapTypeControl: true,
                        scaleControl: true
                    },
                    selector: self.gmapSelector,
                    placeSearchSelector: self.placeSearchSelector,
                    stores: self.stores,
                    startLat: parseFloat(self.params.lat),
                    startLng: parseFloat(self.params.lon),
                    updateMapSearch: function () {
                        clearTimeout(this.boundUpdateTimeout);
                        self.updateMapSearch(this);
                    },
                    updateMapGeoLocation: function () {
                        clearTimeout(this.boundUpdateTimeout);
                        self.updateMapGeoLocation(this);
                    },
                    updateMapBounds: function () {
                        clearTimeout(this.boundUpdateTimeout);
                        var self2 = this;
                        this.boundUpdateTimeout = setTimeout(function () {
                            self.updateMapBounds(self2);
                        }, 400);
                    }
                };
            if (this.infoWindowTemplate !== null) {
                params.infoWindowTemplate = this.infoWindowTemplate;
            }
            this.storeFinderMap = StoreFinderMap(params);
        },

        /**
         * Store item
         * @param item
         * @returns {integer}
         */
        getIndex: function (item) {
            return item.order_index;
        },

        /**
         * Store item
         * @param item
         */
        goToMarker: function (item) {
            MapStores.goToMarker(item);
        },

        goToPoint: function (point) {
            this.storeFinderMap.goToPoint(point);
        },

        /**
         * Activates on Submit button or Enter keypress
         */
        submitPlaceSearch: function () {
            this.storeFinderMap.submitPlaceSearch(this);
        },

        /**
         * Store item
         * @param item
         * @return {boolean}
         */
        isShowMoreDetailsUrl: function (item) {
            return (typeof(item.more_details_url) === 'string')
                && item.more_details_url.length > 0;
        }
    });
});
