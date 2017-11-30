(function($) {

    $.widget("mapbender.mbCoordinatesUtility", {
        options: {
            autoOpen:  true,
            target:    null
        },

        isPopupDialog: false,

        readyCallbacks: [],
        callback:       null,

        mbMap:          null,
        mapQuery:       null,
        highlightLayer: null,
        containerInfo:  null,
        feature:        null,
        mapClickHandler: null,

        currentMapCoordinate: null,
        transformedCoordinate: null,
        lon: null,
        lat: null,

        DECIMAL_ANGULAR: 6,
        DECIMAL_METRIC: 2,
        STRING_SEPARATOR: ' ',
        ZOOM: 10,

        /**
         * Widget constructor
         *
         * @private
         */
        _create: function() {
            var widget = this;
            var options = widget.options;

            if(!Mapbender.checkTarget("mbCoordinatesUtility", options.target)) {
                return;
            }

            Mapbender.elementRegistry.onElementReady(options.target, $.proxy(widget._setup, widget));
        },

        /**
         * Setup widget
         *
         * @private
         */
        _setup: function() {
            var widget = this;
            var options = widget.options;

            widget.mbMap = $("#" + widget.options.target).data("mapbenderMbMap");
            widget.mapQuery = $(widget.mbMap.element).data('mapQuery');
            widget.highlightLayer = widget.mapQuery.layers({
                type: 'vector',
                label: 'Highlight'
            });

            widget.isPopUpDialog = options.type === "dialog";

            widget._setupMapClickHandler();
            widget._setupButtons();
            widget._setupSrsDropdown();
            widget._setupEventListeners();

            widget._trigger('ready');
            widget._ready();
        },

        /**
         * Setup widget buttons
         *
         * @private
         */
        _setupButtons: function() {
            var widget = this;

            $('.copyClipBoard', widget.element).on('click',  $.proxy(widget._copyToClipboard, widget));
            $('.center-map', widget.element).on('click',  $.proxy(widget._centerMap, widget));

            if (!widget.isPopUpDialog) {
                var coordinateSearchButton = $('.coordinate-search');

                coordinateSearchButton.on('click', function() {
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
        },

        /**
         * Setup map click handler
         *
         * @private
         */
        _setupMapClickHandler: function() {
            var widget = this;

            if(!widget.mapClickHandler) {
                widget.mapClickHandler = new OpenLayers.Handler.Click(
                    widget,
                    { 'click': widget._mapClick },
                    { map: widget.mbMap.map.olMap }
                );
            }
        },

        /**
         * Create SRS dropdown
         */
        _setupSrsDropdown: function() {
            var widget = this;
            var dropdown = $('.srs', widget.element);

            if (dropdown.children().length === 0) {
                widget._createDropdownOptions(dropdown);
            }

            initDropdown.call($('.dropdown', widget.element));
        },

        /**
         * Create options for the dropdown
         *
         * @param {DOM} dropdown
         * @private
         */
        _createDropdownOptions: function(dropdown) {
            var widget = this;
            var srsArray = (null === widget.options.srsList) ? [] : widget.options.srsList;

            if (widget.options.addMapSrsList) {
                widget._addMapSrsOptionsToDropodw(srsArray);
            }

            if (srsArray.length === 0) {
                Mapbender.error(Mapbender.trans("mb.coordinatesutility.widget.error.noSrs"));
                return;
            }

            srsArray.map(function(srs){
                if (widget._isValidSRS(srs.name)) {
                    var title = (srs.title.length === 0)
                        ? srs.name
                        : srs.title;

                    dropdown.append($('<option></option>').val(srs.name).html(title));
                }
            });

            widget._setDefaultSelectedValue(dropdown);
        },

        /**
         * Check if SRS is valid
         *
         * @param srs
         * @returns {boolean}
         * @private
         */
        _isValidSRS: function(srs) {
            var projection = new OpenLayers.Projection(srs);
            var isValid = true;

            if (typeof projection.proj.defData === 'undefined') {
                isValid = false;
            }

            return isValid;
        },

        /**
         * Add SRSs from the map
         *
         * @param array srsArray
         * @private
         */
        _addMapSrsOptionsToDropodw: function(srsArray) {
            var widget = this;
            var mapSrs = widget.mbMap.getAllSrs();

            var srsNames = srsArray.map(function(srs) {
                return srs.name;
            });

            mapSrs.map(function(srs){

                var srsAlreadyExists = $.inArray(srs.name, srsNames) >= 0;

                if (srsAlreadyExists === false) {
                    srsArray.push(srs);
                }
            });
        },

        /**
         * Set selected by default value in dropdown
         *
         * @param {DOM} dropdown
         * @private
         */
        _setDefaultSelectedValue: function(dropdown) {
            var widget = this;
            var currentSrs = widget.mbMap.getModel().getCurrentProj();

            dropdown.val(currentSrs.projCode);
        },

        /**
         * Setup event listeners
         *
         * @private
         */
        _setupEventListeners: function() {
            var widget = this;

            $(document).on('mbmapsrschanged', $.proxy(widget._resetFields, widget));
            $(document).on('mbmapsrsadded', $.proxy(widget._resetFields, widget));

            $('select.srs', widget.element).on('change', $.proxy(widget._transformCoordinateToSelectedSrs, widget));

            $('input.input-coordinate', widget.element).on('change', function() {
                var lonlat = $(this).val().split(/ \s*/);

                widget.lat = lonlat.pop();
                widget.lon = lonlat.pop();
            });
        },

        /**
         * Popup HTML window
         *
         * @param html
         * @return {mapbender.mbLegend.popup}
         */
        popup: function() {
            var widget = this;
            var element = widget.element;

            if(!widget.popupWindow || !widget.popupWindow.$element) {
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

                widget.popupWindow.$element.on('close', function() {
                    widget.close();
                });
            }

            widget.popupWindow.$element.removeClass('hidden');
        },


        /**
         * On open handler
         */
        open: function(callback) {
            var widget = this;

            widget.callback = callback;

            widget.popup();
            widget.activate();
        },

        /**
         * On close
         */
        close: function() {
            var widget = this;

            widget.popupWindow.$element.addClass('hidden');

            widget.deactivate();
            widget._resetFields();
        },

        /**
         * On ready handler
         */
        ready: function(callback) {
            var widget = this;

            if(widget.readyState) {

                if(typeof(callback ) === 'function') {
                    callback();
                }

            } else {
                widget.readyCallbacks.push(callback);
            }
        },

        /**
         * On ready handler
         */
        _ready: function() {
            var widget = this;

            _.each(widget.readyCallbacks, function(readyCallback){
                if(typeof(readyCallback ) === 'function') {
                    readyCallback();
                }
            });

            // Mark as ready
            widget.readyState = true;

            // Remove handlers
            widget.readyCallbacks.splice(0, widget.readyCallbacks.length);
        },

        /**
         * Activate coordinate search
         */
        activate: function() {
            var widget = this;

            widget.mapClickHandler.activate();
            widget.mbMap.map.element.addClass('crosshair');
        },

        /**
         * Deactivate coordinate search
         */
        deactivate: function() {
            var widget = this;

            widget.mapClickHandler.deactivate();
            widget.mbMap.map.element.removeClass('crosshair');
        },

        /**
         * On map click handler
         *
         * @param e selected pixel
         * @private
         */
        _mapClick: function(e) {
            var widget = this;

            var lonlat = widget.mbMap.map.olMap.getLonLatFromPixel(e.xy);
            widget.clickPoint = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);

            widget.currentMapCoordinate = widget._formatOutputString(lonlat, e.object.units);

            widget.lon = lonlat.lon;
            widget.lat = lonlat.lat;

            widget._transformCoordinates();
            widget._updateFields();
        },

        /**
         * Transform coordinates to selected SRS
         *
         * @private
         */
        _transformCoordinates: function() {
            var widget = this;
            var selectedSrs = $('select.srs', widget.element).val();

            if (null === widget.lon || null === widget.lat || null === selectedSrs) {
                return;
            }

            var currentProjection = widget.mbMap.map.olMap.getProjectionObject();
            var projectionToTransform = new OpenLayers.Projection(selectedSrs);

            var lonlat = new OpenLayers.LonLat(widget.lon,widget.lat).transform(currentProjection, projectionToTransform);

            widget.transformedCoordinate = widget._formatOutputString(
                lonlat,
                projectionToTransform.proj.units
            );
        },

        /**
         * Format output coordinate string
         *
         * @param {OpenLayersPoint} point
         * @returns {string}
         * @private
         */
        _formatOutputString: function(point, unit) {
            var widget = this;

            var decimal = (unit  === 'm')
                ? widget.DECIMAL_METRIC
                : widget.DECIMAL_ANGULAR;

           return point.lon.toFixed(decimal) + widget.STRING_SEPARATOR + point.lat.toFixed(decimal);
        },

        /**
         * Update coordinate input fields
         *
         * @private
         */
        _updateFields: function() {
            var widget = this;

            $('input.map-coordinate', widget.element).val(widget.currentMapCoordinate);
            $('input.input-coordinate', widget.element).val(widget.transformedCoordinate);

            widget._showFeature();
        },

        /**
         * Reset coordinate input fields
         *
         * @private
         */
        _resetFields: function(){
            var widget = this;

            widget.currentMapCoordinate = null;
            widget.transformedCoordinate = null;

            widget._updateFields();
            widget._removeFeature();
        },

        /**
         * Show feature on the map
         *
         * @private
         */
        _showFeature: function(){
            var widget = this;

            widget.feature = new OpenLayers.Feature.Vector(widget.clickPoint);

            widget.highlightLayer.olLayer.removeAllFeatures();
            widget.highlightLayer.olLayer.addFeatures(widget.feature);
        },

        /**
         * Remove feature from the map
         *
         * @private
         */
        _removeFeature: function(){
            var widget = this;

            if(widget.feature) {
                widget.highlightLayer.olLayer.removeFeatures(widget.feature);
            }
        },

        /**
         * Copy a coordinate to the buffer
         *
         * @param e
         * @private
         */
        _copyToClipboard: function(e){
            $(e.target).parent().find('input').select();
            document.execCommand("copy");
        },

        /**
         * Center the map accordingly to a selected coordinate
         *
         * @private
         */
        _centerMap: function() {
            var widget = this;

            if (null === widget.lon || null === widget.lat || typeof widget.lon === 'undefined' || typeof widget.lat === 'undefined'){
                return;
            }

            if (widget._areCoordinatesValid()) {
                widget.highlightLayer.olLayer.removeAllFeatures();
                widget.highlightLayer.olLayer.addFeatures(widget.feature);

                var lonLat = new OpenLayers.LonLat(widget.lon,widget.lat);

                widget.mbMap.map.olMap.setCenter(lonLat, widget.ZOOM);
            } else {
                Mapbender.error(Mapbender.trans("mb.coordinatesutility.widget.error.invalidCoordinates"));
            }

        },

        /**
         * Check if coordinates to navigate are valid
         *
         * @returns boolean
         * @private
         */
        _areCoordinatesValid: function() {
            var widget = this;

            console.log($.isNumeric(widget.lon));
            if (!$.isNumeric(widget.lon) || !$.isNumeric(widget.lat)) {
                return false;
            }

            var Point = new Proj4js.Point(widget.lon,widget.lat);
            var currentProjection = widget.mbMap.map.olMap.getProjectionObject();

            Proj4js.transform(currentProjection, currentProjection, Point);

            var lonLat = new OpenLayers.LonLat(Point.x, Point.y);

            return widget.mbMap.map.olMap.isValidLonLat(lonLat);
        },

        /**
         * Transform a coordinate to the selected SRS
         *
         * @private
         */
        _transformCoordinateToSelectedSrs: function(){
            var widget = this;

            widget._transformCoordinates();
            widget._updateFields();
        },

        /**
         * On map SRS added handler
         *
         * @param event
         * @param srsObj
         * @private
         */
        _onMapSrsAdded: function(event, srsObj){
            var widget = this;

            $('.srs', widget.element).append($('<option></option>').val(srsObj.name).html(srsObj.title));
        }
    });

})(jQuery);



