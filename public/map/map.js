var homex = 1000.5;
var homey = 1000.5;
var LoadZoom = 8;
var imap;
var LARGE_ICON_ZOOM_LEVEL = 8;
var useLargeIcons = true;
var defaulticonsize = 30;
var MAX_NAME_LENGTH = 20;
var GRIDURL = "http://canadiangrid.ca:8002";
var MAPURL = "http://map.canadiangrid.ca";
var WEBSITE = "https://canadiangrid.ca";

function loadmap(mapdiv) {
    var mapurl = GRIDURL+"/map-{z}-{x}-{y}-objects.jpg";
    var TileLayer = L.TileLayer.extend({
        getTileUrl: function (coords) {
             var data = {
                r: L.Browser.retina ? '@2x' : '',
                s: this._getSubdomain(coords),
                x: coords.x,
                y: coords.y,
                z: this._getZoomForUrl()
            };
            if (this._map && !this._map.options.crs.infinite) {
                var invertedY = this._globalTileRange.max.y - coords.y;
                if (this.options.tms) {
                    data['y'] = invertedY;
                }
                data['-y'] = invertedY;
            }

            var regionsPerTileEdge = Math.pow(2, data['z'] - 1);
            data['x'] = data['x'] * regionsPerTileEdge;
            data['y'] = (Math.abs(data['y']) - 1) * regionsPerTileEdge;

            return L.Util.template(this._url, L.extend(data, this.options));
        }
    });
    var tiles = new TileLayer(mapurl, {
        crs: L.CRS.Simple,
        minZoom: 1,
        maxZoom: 8,
        zoomOffset: 1,
        zoomReverse: true,
        maxBounds: [[0, 0], [1048576, 1048576]],
        errorTileUrl: MAPURL+'/missingMap.png'
    });
    imap = L.map(mapdiv, {
        crs: L.CRS.Simple,
        bounds: [[0,0], [1048576,1048576]],
        minZoom: 1,
        maxZoom: 8,
        layers: [tiles]
    }).setView([homey,homex], 8);
    imap.on('zoom',function() {
        var reqSize = imap.getZoom() >= LARGE_ICON_ZOOM_LEVEL;
    });
    imap.on('click', function (event) {
        getURL(event.latlng.lng, event.latlng.lat, imap);
    });
    var overlays = {
        //"WmPS": WmpsLayerG,
    };
    L.control.layers({},overlays).addTo(imap).expand();
}
function getURL(x, y, lmap)
{
    var int_x = Math.floor(x);
    var int_y = Math.floor(y);
    var ajaxit = $.ajax({
        url: WEBSITE+"/api/siminfo",
        type: "POST",
        data: "gridx=" + (int_x * 256) + "&gridy=" + (int_y * 256),
        dataType: 'html',
        context: document.body,
        global: false,
        async:false
    }).responseText;
    L.popup().setLatLng([y, x]).setContent(ajaxit).openOn(lmap);
}
