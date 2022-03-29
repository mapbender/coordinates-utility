# 1.2.3
* Fix incompatibility with current doctrine/doctrine-bundle

# 1.2.2
* Allow both "," and "." as a decimal separator in manual coordinate entry ([PR#16](https://github.com/mapbender/coordinates-utility/pull/16))

# 1.2.1
* Fix Composer autoload coverage warning for test classes
* Remove broken legacy test

# 1.2.0
* Reimplement for Symfony 4 conformance
* Update frontend design for Mapbender 3.2

NOTE: This version cannot be installed on Mapbender before v3.2.6.

## 1.1.5
* Fix initially selected SRS not applied to calculations

## 1.1.4
* Allow both "," and "." as a decimal separator in manual coordinate entry ([PR#16](https://github.com/mapbender/coordinates-utility/pull/16))

## 1.1.3
* Fix Composer autoload coverage warning for test classes

## 1.1.2
* Fix Symfony 4 incompatibility in backend autocompletion controller
* Fix broken backend srs suggestion layout when matching srs with very long title
* Document Element configuration

## 1.1.1
* Fix errors if Yaml configuration completely omits `srsList`
* Fix errors if Yaml configuration supplies a list of (scalar string) srs names in `srsList`
* Fix inconsistent backend form typography vs other Mapbender elements

## 1.1.0
* Add Openlayers 4/5/6 support

## 1.0.9
* Update for current Mapbender API conventions (no functional changes)

## 1.0.8
* Fix coordinate display rounding drift on map click
* Fix coordinate display rounding drift on coordinate input changes
* Fix error when trying to center on map after external srs change
* Fix internal server error when submitting configuration form with validation error
* Drop "type" configuration option; auto-determine popup vs sidepane operation mode from containing region

## 1.0.7.2
* Resolve Symfony 3 incompatibilities
* Fix errors when using "Coordinate search" button with multiple Coordinates Utility instances in the same application
