# Deprecated

This repository is deprecated. Its functionality will be integrated into the [mapbender core repository](https://github.com/mapbender/mapbender/) in version 4.0 and the code has already been ported to the [develop branch](https://github.com/mapbender/mapbender/blob/develop/src/Mapbender/CoreBundle/Element/CoordinatesUtility.php). Propose any changes there.

---

## Old description: Mapbender coordinates utility module
Retrieve coordinates from the map, Zoom to a given map coordinate

## Features
* Get coordinates by clicking on the map
* Transform coordinates to different SRS
* Navigate to selected coordinates on the map

## Element configuration
Coordinates utility element supports the following configuration values:

| Name | Type | Default | Explanation |
|---|---|---|---|
|addMapSrsList|boolean|true|Offer all SRS configured on the map element for coordinate transformation|
|srsList|list of strings|empty array|Additional SRS choices to offer for coordinate transformation. Must follow "EPSG:\<digits\>" form|
|zoomlevel|integer|6|Zoom level to use when centering map (lower = zoomed further out)|

```yaml
<...>
  sidepane:
    class: Mapbender\CoordinatesUtilityBundle\Element\CoordinatesUtility
    addMapSrsList: true      # =default; offer all SRS configured on the Map element
    srsList: ['EPSG:25834']  # Add an additional SRS for transformation
    zoomlevel: 3             # zoom in closer than default on "Center map"
```

### Customizing SRS titles
It is possible to change the displayed title of SRS choices by using an object form or a pipe separator.
For the object form, use objects with "name" and "title" keys. The pipe separator form is "\<srs name\>|\<custom title\>".

```yaml
<...>
    srsList: 
      # Rename using object form
      - {name: 'EPSG:25832', title: 'Renamed UTM32N'}
      # Rename using pipe separator form
      - 'EPSG:25834|Renamed UTM34N'
      # Use standard name
      - 'EPSG:25831'
```

NOTE: the backend form input only supports using pipe separator for SRS renaming. The explicit object
format is legal in Yaml Application definitions only.

## Installation
The backend form srs suggestions will only work after
registering the controller routes with the Symfony router.

Mapbender Starter already does this.

To do it manually, add the following to your app/config/routing.yml:
```yaml
mapbender_coordinatesutilitybundle:
    resource: "@MapbenderCoordinatesUtilityBundle/Controller/"
    type: annotation
```

Note that the top key "mapbender_coordinatesutilitybundle" is technically irrelevant, but must be unique in your routing
configuration.
