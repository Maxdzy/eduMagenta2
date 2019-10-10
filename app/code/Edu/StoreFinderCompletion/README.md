# 7. Custom module functionality with new DB table (Store locator)

Only for those who have access to the repository packages.indvp.com.
This module completion module store finder to Magento Scandiweb. 

## Features

FE

 *    Display location of stores in Leaflet maps with pins and when clicking on a pin the work hours of the specific store should be displayed on the map

 *   List of stores next to the map. When clicking on an item in the list, the map should zoom to the selected store

 *   Input field with autosuggestions. When entering an address and submitting the map should zoom in to the closest store- 

 *   Design for FE - Store locator

## Getting Started

These instructions will guide you how you can use updated migration script approach. 

### Prerequisites

* Magento version >=2.2.x
* PHP 7.2

### Installing

#### Composer

Using packages.indvp.com. 

Place credentials under auth.json

```
{
    "http-basic": {
        "packages.indvp.com": {
            "username": "xxxx",
            "password": "xxxx"
        }
    }
}
```
Add packages.indvp.com to prepositories

```
"repositories": {
        "0": {
            "type": "composer",
            "url": "https://packages.indvp.com"
        },
```


Require module through composer command

```
composer require scandiweb/module-store-finder
```
Run composer update

```
composer update scandiweb/module-store-finder
```

## Usage

### Stores import from CSV file via magneto console command

1. `php bin/magento scandiweb:storefinder:store:import --source-file=<path-to-csv-from-magento-root>`

## Notes

Module uses Google Maps with Places and MarkerClusterer libraries
Marker Clusterer: https://github.com/googlemaps/v3-utility-library/tree/master/markerclusterer

## Contributing

Contributing are welcomed. We are opened for improvements and new features

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the tag on this repository. 

## Authors

* **Andris Breimanis** - *Initial work*

* **Maris Mols** - *Stores import from CLI*

* **Maxim Dzyuba** - *FE upgrade*

## Acknowledgments

* Not yet

