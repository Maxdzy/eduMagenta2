/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */
define([
    'jquery',
    'ko',
    'uiRegistry',
    'gmap'
], function ($, ko, registry, gmap) {
    return {
        stores: ko.observableArray([]),
        storeMarkers: {},
        infoWindows: {},
        locationMarkers: {},
        autoIncrement: 1,
        map: null,
        infoWindowTemplate: window.googleMapsInfoWindowTemplate,
        markerClusterer: null,

        /**
         * Properties:
         *   infoWindow
         *   storeId
         */
        infoWindowHoverEvent: null,

        resetStores: function () {
            var self = this,
                storeArray = this.stores();
            $.each(storeArray, function (key, item) {
                self.removeStore(item);
            });
            this.stores([]);
            this.storeMarkers = {};
            this.autoIncrement = 1;
            if (this.map !== null) {
                this.initClusterer();
            }
        },

        /**
         * Store object from Ajax response
         * @param store
         */
        addStore: function (store) {
            var oldStore = ko.utils.arrayFirst(this.stores(), function (item) {
                return item.store_id === store.store_id;
            });

            if (!oldStore) {
                store.order_index = this.autoIncrement;
                this.autoIncrement++;

                this.stores.push(store);
                this.addMarker(store);
            }
        },

        /**
         * Store object from Ajax response
         * @param store
         */
        addMarker: function (store) {
            if (typeof(this.storeMarkers[store.store_id]) !== 'undefined') {
                this.storeMarkers[store.store_id].setMap(null);
            }
            this.storeMarkers[store.store_id] = new gmap.Marker(this.getMarkerParams(store));
            this.addMarkerInfoWindow(store);

            if (this.markerClusterer !== null) {
                this.markerClusterer.addMarker(this.storeMarkers[store.store_id]);
            }
        },

        /**
         * Store object from Ajax response
         * @param store
         */
        addMarkerInfoWindow: function (store) {
            var marker = this.storeMarkers[store.store_id];

            var infoWindow = new gmap.InfoWindow({
                content: this.generateInfoWindowContent(store)
            });
            this.infoWindows[store.store_id] = infoWindow;

            var self = this;
            marker.addListener('mouseover', function () {
                // Open InfoWindow
                if (self.infoWindowHoverEvent !== null) {
                    self.infoWindowHoverEvent.infoWindow.close();
                    self.infoWindowHoverEvent = null;
                }
                infoWindow.open(self.map, marker);
                $('.gm-style-iw').parent().addClass('infowindow-wrapper');
                self.infoWindowHoverEvent = {
                    storeId: store.store_id,
                    infoWindow: infoWindow
                };

                // Scroll to Store
                var $storeBlock = $('[data-store-id=' + store.store_id + ']');
                if (typeof($storeBlock.size()) !== 'undefined') {
                    var $listBlock = $storeBlock.closest('.list'),
                        scrollTop = $storeBlock.position().top + $listBlock.scrollTop();
                    $listBlock.animate({
                        scrollTop: scrollTop
                    });
                }
            });
            marker.addListener('click', function () {
                if (store.more_details_url) {
                    window.location = store.more_details_url;
                }
            });
            marker.addListener('mouseout', function () {
                if (self.infoWindowHoverEvent !== null) {
                    self.infoWindowHoverEvent.infoWindow.close();
                    self.infoWindowHoverEvent = null;
                }
            });
        },

        /**
         * Store object from Ajax response
         * @param store
         */
        getMarkerParams: function (store) {
            return {
                map: this.map,
                position: {
                    lat: parseFloat(store.latitude),
                    lng: parseFloat(store.longitude)
                },
                label: {
                    text: String(store.order_index),
                    color: '#ffffff'
                },
                // ie11 fix for svg icons
                optimized: false,
                icon: {
                    url: window.googleMapsMarkerIcon,
                    // ie11 fix for svg icons
                    scaledSize: new google.maps.Size(30, 38)
                },
                disableAutoPan: true
            };
        },

        /**
         * Store object from Ajax response
         * @param store
         */
        removeStore: function (store) {
            if (typeof(store) === 'object') {
                store = store.store_id;
            }

            if (typeof(this.storeMarkers[store]) === 'object') {
                if (this.storeMarkers[store] !== null) {
                    this.storeMarkers[store].setMap(null);
                }
                delete this.storeMarkers[store];
            }

            if (this.infoWindowHoverEvent !== null && this.infoWindowHoverEvent.storeId === store) {
                this.infoWindowHoverEvent = null;
            }
        },

        /**
         * @returns {array}
         */
        getStores: function () {
            return this.stores();
        },

        /**
         * Array of Store objects from Ajax response
         * @param stores
         */
        setStores: function (stores) {
            this.resetStores();

            var self = this;
            $.each(stores, function (key, value) {
                self.addStore(value);
            });
        },

        /**
         * Google Map object
         * @param map
         */
        setMap: function (map) {
            this.map = map;

            var self = this;
            $.each(this.storeMarkers, function (key, value) {
                value.setMap(self.map);
            });

            this.initClusterer();
        },

        /**
         * Store object from Ajax response
         * @param store
         */
        generateInfoWindowContent: function (store) {
            var template = this.infoWindowTemplate,
                keywords = template.match(/%[a-zA-Z_-]+%/g);
            if (keywords !== null) {
                for (var i = 0; i < keywords.length; i++) {
                    var keyword = keywords[i].replace(/%/g, '');
                    if (typeof(store[keyword]) === 'undefined') {
                        continue;
                    }

                    var value = store[keyword];
                    if (value === null) {
                        value = '';
                    }

                    template = template.replace(new RegExp('%' + keyword + '%', 'g'), value);
                }
            }

            return template;
        },

        /**
         * Store object from Ajax response
         * @param store
         */
        goToMarker: function (store) {
            if (typeof(this.storeMarkers[store.store_id]) !== 'undefined') {
                var marker = this.storeMarkers[store.store_id];
                this.map.setCenter(new gmap.LatLng(marker.position.lat(), marker.position.lng()));
                this.map.setZoom(17);
            }
        },

        initClusterer: function () {
            if (this.markerClusterer !== null) {
                this.markerClusterer.clearMarkers();
            }
            this.markerClusterer = new MarkerClusterer(
                this.map,
                this.storeMarkers,
                {
                    maxZoom: 12,
                    styles: window.googleMarkerClustererStyles
                }
            );
        }
    };
});