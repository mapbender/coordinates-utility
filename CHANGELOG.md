## dev-master @ 86625b4e9c930b57f605fea5fa84e32c7d0e5cea (1.2 WIP)
* Reimplement for Symfony 4 conformance

NOTE: This version cannot be installed on Mapbender before v3.2.6.

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
