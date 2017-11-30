# Mapbender coordinates utility module
Retrieve coordinates from the map, Zoom to a given map coordinate

## Features
* Get coordinates by clicking on the map
* Transform coordinates to different SRS
* Navigate to selected coordinates on the map

## Installation 
* First you need installed mapbender3-starter https://github.com/mapbender/mapbender-starter#installation project
* Add required module to mapbender
```sh
$ cd application/
$ ../composer require "mapbender/coordinates-utility"
```
* Register routers in app/config/routing.yml
```
mapbender_coordinatesutilitybundle:
    resource: "@MapbenderCoordinatesUtilityBundle/Controller/"
    type: annotation
```

## Update 


 ```sh
$ cd application/
$ ../composer self-update
$ ../composer update
```

