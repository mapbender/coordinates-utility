/*jslint browser: true, nomen: true*/
/*globals initDropdown, Mapbender, OpenLayers, Proj4js, _, jQuery*/

(function ($) {
    'use strict';

    $.widget("mapbender.mbCoordinatesUtility", $.mapbender.mbBaseElement, {
        options: {
            autoOpen:  true,
            target:    null
        },

        isPopupDialog: false,
        callback:       null,

        /**
         * @var {mapbender.mbMap}
         */
        mbMap:          null,

        /**
         * @var {Mapbender.Model}
         */
        model:          null,

        /**
         * @var null | {string} mapClickHandler id
         */
        mapClickHandler: null,

        /**
         * @var null | {string}
         */
        currentMapCoordinate: null,

        /**
         * @var null | {string}
         */
        transformedCoordinate: null,

        /**
         * @var undefined | {{x}, {y}}
         */
        coordinatesObject: undefined,

        DECIMAL_ANGULAR: 6,
        DECIMAL_METRIC: 2,
        STRING_SEPARATOR: ' ',
        ZOOM: 10,

        /**
         * Widget constructor
         *
         * @private
         */
        _create: function () {
            var widget = this,
                options = widget.options;

            if (!Mapbender.checkTarget("mbCoordinatesUtility", options.target)) {
                return;
            }

            Mapbender.elementRegistry.onElementReady(options.target, $.proxy(widget._setup, widget));
        },

        /**
         * Setup widget
         *
         * @private
         */
        _setup: function () {
            var options = this.options;

            this.mbMap = Mapbender.elementRegistry.listWidgets().mapbenderMbMap;
            this.model = this.mbMap.model;
            this.isPopUpDialog = options.type === "dialog";

            this._initializeMissingSrsDefinitions(this.options.srsList)
                ._setupMapClickHandler()
                ._setupButtons()
                ._setupSrsDropdown()
                ._setupEventListeners();
        },

        /**
         * Initialize srs definitions which are not set before and missing in Proj4js.defs array
         *
         * @param srsList
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _initializeMissingSrsDefinitions: function (srsList) {

            if (null === srsList || typeof srsList.length === "undefined") {
                return this;
            }

            srsList.map(function (srs) {
                if (typeof proj4.defs(srs.name) === "undefined") {
                    proj4.defs(srs.name, srs.definition);
                }
            });

            return this;
        },

        /**
         * Setup widget buttons
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _setupButtons: function () {
            var widget = this;

            $('.copyClipBoard', widget.element).on('click',  $.proxy(widget._copyToClipboard, widget));
            $('.center-map', widget.element).on('click',  $.proxy(widget._centerMap, widget));

            if (!widget.isPopUpDialog) {
                var coordinateSearchButton = $('.coordinate-search');

                coordinateSearchButton.on('click', function () {
                    var isActive = $(this).hasClass('active');

                    if (isActive) {
                        widget.deactivate();
                    } else {
                        widget.activate();
                    }

                    $(this).toggleClass('active');
                });

                coordinateSearchButton.removeClass('hidden');
            }

            return this;
        },

        /**
         * Setup map click handler
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _setupMapClickHandler: function () {
            var widget = this;

            if (!widget.mapClickHandler) {
                widget.mapClickHandler = this.model.setOnSingleClickHandler( $.proxy(this._mapClick, this));
            }

            return this;
        },

        /**
         * Create SRS dropdown
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _setupSrsDropdown: function () {
            var widget = this,
                dropdown = $('.srs', widget.element);

            if (dropdown.children().length === 0) {
                widget._createDropdownOptions(dropdown);
            }

            initDropdown.call($('.dropdown', widget.element));

            return this;
        },

        /**
         * Create options for the dropdown
         *
         * @param {DOM} dropdown
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _createDropdownOptions: function (dropdown) {
            var widget = this,
                srsArray = (null === widget.options.srsList) ? [] : widget.options.srsList;

            if (widget.options.addMapSrsList) {
                widget._addMapSrsOptionsToDropodw(srsArray);
            }

            if (srsArray.length === 0) {
                Mapbender.error(Mapbender.trans("mb.coordinatesutility.widget.error.noSrs"));
                return this;
            }

            srsArray.map(function (srs) {
                if (widget._isValidSRS(srs.name)) {
                    var title = (srs.title.length === 0)
                        ? srs.name
                        : srs.title;

                    dropdown.append($('<option></option>').val(srs.name).html(title));
                }
            });

            widget._setDefaultSelectedValue(dropdown);

            return this;
        },

        /**
         * Check if SRS is valid
         *
         * @param srs
         * @returns {boolean}
         * @private
         */
        _isValidSRS: function (srs) {
            var isValid = true;

            try {
                proj4.Proj(srs);
            } catch (error) {
                console.error("Projection " + srs + " is not valid", error);
                isValid = false;
            }

            return isValid;
        },

        /**
         * Add SRSs from the map
         *
         * @param array srsArray
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _addMapSrsOptionsToDropodw: function (srsArray) {
            var mapSrs = this.mbMap.getAllSrs();

            var srsNames = srsArray.map(function (srs) {
                return srs.name;
            });

            mapSrs.map(function (srs) {

                var srsAlreadyExists = $.inArray(srs.name, srsNames) >= 0;

                if (srsAlreadyExists === false) {
                    srsArray.push(srs);
                }
            });

            return this;
        },

        /**
         * Set selected by default value in dropdown
         *
         * @param {DOM} dropdown
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _setDefaultSelectedValue: function (dropdown) {
            var currentSrs = this.model.getCurrentProjectionCode();

            dropdown.val(currentSrs);

            return this;
        },

        /**
         * Setup event listeners
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _setupEventListeners: function () {
            var widget = this;

            $(document).on('mbmapsrschanged', $.proxy(widget._resetFields, widget));
            $(document).on('mbmapsrsadded', $.proxy(widget._resetFields, widget));

            $('select.srs', widget.element).on('change', $.proxy(widget._transformCoordinateToSelectedSrs, widget));
            $('input.input-coordinate', widget.element).on('change', $.proxy(widget._transformCoordinateToMapSrs, widget));

            return this;
        },

        /**
         * Popup HTML window
         *
         * @param html
         * @return {mapbender.mbLegend.popup}
         */
        popup: function () {
            var widget = this,
                element = widget.element;

            if (!widget.popupWindow || !widget.popupWindow.$element) {
                widget.popupWindow = new Mapbender.Popup2({
                    title:                  element.attr('title'),
                    draggable:              true,
                    resizable:              true,
                    modal:                  false,
                    closeButton:            false,
                    closeOnPopupCloseClick: true,
                    closeOnESC:             false,
                    destroyOnClose:         false,
                    detachOnClose:          false,
                    content:                this.element.removeClass('hidden'),
                    width:                  450,
                    height:                 400,
                    buttons:                {}
                });

                widget.popupWindow.$element.on('close', function () {
                    widget.close();
                });
            }

            widget.popupWindow.$element.removeClass('hidden');
        },

        /**
         * Provide default action
         *
         * @returns {action}
         */
        defaultAction: function () {
            return this.open();
        },

        /**
         * On open handler
         */
        open: function (callback) {
            this.callback = callback;

            this.popup();
            this.activate();
        },

        /**
         * On close
         */
        close: function () {
            this.popupWindow.$element.addClass('hidden');

            this.deactivate();
            this._resetFields();
        },

        /**
         * Activate coordinate search
         */
        activate: function () {
            this._setupMapClickHandler();

            this.model.setMapCursorStyle('crosshair');
        },

        /**
         * Deactivate coordinate search
         */
        deactivate: function () {
            this.model.removeEventListenerByKey(this.mapClickHandler);
            this.mapClickHandler = null;

            this.model.setMapCursorStyle('');
        },

        /**
         * On map click handler
         *
         * @param event selected pixel
         * @private
         */
        _mapClick: function (event) {
            this.coordinatesObject = this.model.getCoordinatesXYObjectFromMapClickEvent(event);
            this.currentMapCoordinate = this._formatOutputString(this.coordinatesObject, this.model.getUnitsOfCurrentProjection());

            this._transformCoordinates()
                ._updateFields()
                ._removePreviousFeature()
                ._drawNewFeature();
        },

        /**
         * Transform coordinates to selected SRS
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _transformCoordinates: function () {
            var selectedSrs = $('select.srs', this.element).val();

            if (typeof this.coordinatesObject === 'undefined' || null === selectedSrs) {
                return this;
            }

            var currentProjection = proj4.Proj(this.model.getCurrentProjectionCode()),
                projectionToTransform = proj4.Proj(selectedSrs),
                coordinatesToTransform = $.extend(this.coordinatesObject);

            var transformedCoordinatesObject = proj4.transform(currentProjection, projectionToTransform, coordinatesToTransform);

            this.transformedCoordinate = this._formatOutputString(
                transformedCoordinatesObject,
                projectionToTransform.units
            );

            return this;
        },

        /**
         * Format output coordinate string
         *
         * @param {x,y} coordinates
         * @param {string} unit
         * @returns {string}
         * @private
         */
        _formatOutputString: function (coordinates, unit) {
            var formattedOutputString = '';

            if (typeof coordinates !== 'undefined') {
                var decimal = (unit  === 'm')
                    ? this.DECIMAL_METRIC
                    : this.DECIMAL_ANGULAR;

                formattedOutputString = coordinates.x.toFixed(decimal) + this.STRING_SEPARATOR + coordinates.y.toFixed(decimal);
            }

            return formattedOutputString;
        },

        /**
         * Update coordinate input fields
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _updateFields: function () {
            $('input.map-coordinate', this.element).val(this.currentMapCoordinate);
            $('input.input-coordinate', this.element).val(this.transformedCoordinate);

            return this;
        },

        /**
         * Reset coordinate input fields
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _resetFields: function () {
            this.currentMapCoordinate = null;
            this.transformedCoordinate = null;

            this._updateFields();
            this._removePreviousFeature();

            return this;
        },

        /**
         * Show feature on the map
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _drawNewFeature: function () {
            var coordinatesArray = [this.coordinatesObject.x, this.coordinatesObject.y];
            this.vectorLayerId = this.model.setMarkerOnCoordinates(coordinatesArray, this.element.attr('id'), this.vectorLayerId);

            return this;
        },

        /**
         * Remove feature from the map
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _removePreviousFeature: function () {
            if (typeof this.vectorLayerId !== 'undefined') {
                this.model.removeAllFeaturesFromLayer(this.element.attr('id'), this.vectorLayerId);
            }

            return this;
        },

        /**
         * Copy a coordinate to the buffer
         *
         * @param e
         * @private
         */
        _copyToClipboard: function (e) {
            $(e.target).parent().find('input').select();
            document.execCommand("copy");
        },

        /**
         * Center the map accordingly to a selected coordinate
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _centerMap: function () {
            if (typeof this.coordinatesObject.x === 'undefined' || typeof this.coordinatesObject.x === 'undefined') {
                return this;
            }

            if (this._areCoordinatesValid(this.coordinatesObject)) {
                this.model.centerMapByCoordinates([this.coordinatesObject.x, this.coordinatesObject.y])
                    .zoomToZoomLevel(this.ZOOM);
            } else {
                Mapbender.error(Mapbender.trans("mb.coordinatesutility.widget.error.invalidCoordinates"));
            }

            return this;
        },

        /**
         * Check if coordinates to navigate are valid
         *
         * @returns boolean
         * @param {{x},{y}} coordinates
         * @private
         */
        _areCoordinatesValid: function (coordinates) {
            if (typeof coordinates === 'undefined'
                || !$.isNumeric(coordinates.x)
                || !$.isNumeric(coordinates.y)
            ) {
                return false;
            }

            var areValid = false,
                currentProjection = proj4.Proj(this.model.getCurrentProjectionCode()),
                theSameCoordinates = proj4.transform(currentProjection, currentProjection, coordinates);

            if (theSameCoordinates === coordinates) {
                areValid = true;
            }

            return areValid;
        },

        /**
         * Transform a coordinate to the selected SRS
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _transformCoordinateToSelectedSrs: function () {
            this._transformCoordinates();
            this._updateFields();

            return this;
        },

        /**
         * Transform coordinates from selected SRS to a map SRS
         *
         * @returns {mapbender.mbCoordinatesUtility}
         * @private
         */
        _transformCoordinateToMapSrs: function () {
            var selectedSrs = $('select.srs', this.element).val(),
                inputCoordinates = $('input.input-coordinate').val(),
                inputCoordinatesArray = inputCoordinates.split(/ \s*/);

            var currentProjection = proj4.Proj(this.model.getCurrentProjectionCode()),
                projectionToTransform = proj4.Proj(selectedSrs);

            var transformedCoordinates = proj4.transform(currentProjection, projectionToTransform, inputCoordinatesArray);

            if (this._areCoordinatesValid(transformedCoordinates)) {
                this.currentMapCoordinate = this._formatOutputString(
                    transformedCoordinates,
                    currentProjection.units
                );

                this.transformedCoordinate = inputCoordinates;

                this._updateFields();
            }

            return this;
        },

        /**
         * On map SRS added handler
         *
         * @param event
         * @param srsObj
         * @private
         */
        _onMapSrsAdded: function (event, srsObj) {
            $('.srs', this.element).append($('<option></option>').val(srsObj.name).html(srsObj.title));
        }
    });

})(jQuery);



