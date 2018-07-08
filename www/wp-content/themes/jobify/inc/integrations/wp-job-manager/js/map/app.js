(function() {
  var bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  jQuery(function($) {
    var MapView, Marker, MarkerView, MarkersCollection, MarkersCollectionView, jobs, resumes;
    MapView = (function(superClass) {
      extend(MapView, superClass);

      function MapView() {
        this.showDefault = bind(this.showDefault, this);
        this.hideBubble = bind(this.hideBubble, this);
        this.fitbounds = bind(this.fitbounds, this);
        this.clusterOverlay = bind(this.clusterOverlay, this);
        this.createClusterer = bind(this.createClusterer, this);
        this.resize = bind(this.resize, this);
        this.mapHeight = bind(this.mapHeight, this);
        this.canvas = bind(this.canvas, this);
        return MapView.__super__.constructor.apply(this, arguments);
      }

      MapView.prototype.bounds = new google.maps.LatLngBounds();

      MapView.prototype.infobubble = new InfoBubble({
        backgroundClassName: 'map-marker-info',
        borderRadius: 4,
        padding: 15,
        borderColor: '#ffffff',
        shadowStyle: 0,
        minHeight: 70,
        minWidth: 225,
        maxWidth: 275,
        hideCloseButton: true,
        flat: true
      });

      MapView.prototype.loaded = false;

      MapView.prototype.initialize = function(settings) {
        if (settings == null) {
          settings = {};
        }
        this.settings = _.defaults(settings, jobifyMapSettings);
        google.maps.event.addDomListener(window, 'load', this.canvas);
        this.markersCollectionView = new MarkersCollectionView({
          map: this,
          collection: new MarkersCollection()
        });
        return this.settings.target.on('updated_results', (function(_this) {
          return function(event, results) {
            _this.loaded = true;
            return _this.markersCollectionView.load(event);
          };
        })(this));
      };

      MapView.prototype.canvas = function() {
        var def, map;
        def = $.Deferred();
        this.el = document.getElementById(this.settings.canvas);
        if (!this.el) {
          return def.reject();
        }
        this.mapOptions = this.settings.mapOptions;
        this.opts = {
          gestureHandling: 'cooperative',
          zoom: parseInt(this.mapOptions.zoom),
          maxZoom: parseInt(this.mapOptions.maxZoom),
          minZoom: parseInt(this.mapOptions.maxZoomOut),
          scrollwheel: this.mapOptions.scrollwheel,
          zoomControlOptions: {
            position: google.maps.ControlPosition.RIGHT_TOP
          },
          streetViewControl: true,
          streetViewControlOptions: {
            position: google.maps.ControlPosition.RIGHT_TOP
          }
        };
        if (this.mapOptions.center) {
          this.defaultCenter = new google.maps.LatLng(this.mapOptions.center[0], this.mapOptions.center[1]);
        } else {
          this.defaultCenter = new google.maps.LatLng(41.850033, -87.6500523);
        }
        this.opts.center = this.defaultCenter;
        this.obj = new google.maps.Map(this.el, this.opts);
        this.createClusterer();
        google.maps.event.addListener(this.obj, 'click', this.hideBubble);
        google.maps.event.addListener(this.obj, 'zoom_changed', this.hideBubble);
        map = this.obj;
        google.maps.event.addListener(this.obj, 'dragend', function() {
          return google.maps.event.trigger(map, 'resize');
        });
        google.maps.event.addListenerOnce(this.obj, 'idle', function() {
          this.loaded = true;
          return def.resolve(this.obj);
        });
        $(window).on('resize', this.resize);
        this.mapHeight();
        return def.promise();
      };

      MapView.prototype.mapHeight = function() {};

      MapView.prototype.resize = function() {
        this.mapHeight();
        google.maps.event.trigger(this.obj, 'resize');
        return this.fitbounds();
      };

      MapView.prototype.createClusterer = function() {
        this.clusterer = new MarkerClusterer(null, [], {
          ignoreHidden: true
        });
        this.clusterer.setMap(this.obj);
        this.clusterer.setMaxZoom(this.opts.maxZoom);
        this.clusterer.setGridSize(parseInt(this.mapOptions.gridSize));
        return google.maps.event.addListener(this.clusterer, 'click', this.clusterOverlay);
      };

      MapView.prototype.clusterOverlay = function(c) {
        var content, markers, title, zoom;
        markers = c.getMarkers();
        zoom = this.obj.getZoom();
        if (zoom < this.opts.maxZoom) {
          return;
        }
        content = _.map(markers, function(marker) {
          var template;
          template = wp.template('infoBubble');
          return template(marker.meta);
        });
        title = this.settings.overlayTitle.replace('%d', markers.length);
        return $.magnificPopup.open({
          items: {
            src: '<div class="modal"><h2 class="modal-title">' + title + '</h2><ul class="cluster-items"><li class="map-marker-info">' + content.join('</li><li class="map-marker-info">') + '</li></ul></div>',
            type: 'inline'
          }
        });
      };

      MapView.prototype.fitbounds = function() {
        return this.obj.fitBounds(this.bounds);
      };

      MapView.prototype.hideBubble = function() {
        return this.infobubble.close();
      };

      MapView.prototype.showDefault = function() {
        if (_.isUndefined(this.obj)) {
          return true;
        }
        this.obj.setCenter(this.opts.center);
        return this.obj.setZoom(this.opts.zoom);
      };

      return MapView;

    })(Backbone.View);
    MarkersCollectionView = (function(superClass) {
      extend(MarkersCollectionView, superClass);

      function MarkersCollectionView() {
        this.resize = bind(this.resize, this);
        this.setClusterer = bind(this.setClusterer, this);
        this.clearClusterer = bind(this.clearClusterer, this);
        this.clearBounds = bind(this.clearBounds, this);
        this.fitBounds = bind(this.fitBounds, this);
        this.removeOld = bind(this.removeOld, this);
        this.render = bind(this.render, this);
        this.parseResults = bind(this.parseResults, this);
        this.load = bind(this.load, this);
        this.listen = bind(this.listen, this);
        return MarkersCollectionView.__super__.constructor.apply(this, arguments);
      }

      MarkersCollectionView.prototype.initialize = function(options) {
        if (options == null) {
          options = {};
        }
        this.map = options.map;
        return google.maps.event.addDomListener(window, 'load', this.listen);
      };

      MarkersCollectionView.prototype.listen = function() {
        if (_.isUndefined(this.map.obj)) {
          return this;
        }
        this.listenTo(this.collection, 'add', this.render);
        this.listenTo(this.collection, 'reset', this.removeOld);
        if (this.map.settings.useClusters === '1') {
          this.listenTo(this.collection, 'markers-reset', this.clearClusterer);
          this.listenTo(this.collection, 'markers-added', this.setClusterer);
        }
        this.listenTo(this.collection, 'markers-reset', this.clearBounds);
        this.listenTo(this.collection, 'markers-added', this.fitBounds);
        return this.listenTo(this.collection, 'markers-added', this.resize);
      };

      MarkersCollectionView.prototype.load = function(event) {
        var data;
        data = this.parseResults(event);
        this.collection.reset();
        if (_.isEmpty(data)) {
          return this.map.showDefault();
        } else {
          this.collection.set(data);
          return this.collection.trigger('markers-added');
        }
      };

      MarkersCollectionView.prototype.parseResults = function(event) {
        var data, html;
        if (!_.isUndefined(event && !_.isUndefined(event.target))) {
          html = $(event.target).find(this.map.settings.list).first().find('li');
        } else {
          html = $(this.map.settings.list).first().find('li');
        }
        data = _.map(html, function(i) {
          return $(i).data();
        });
        return data = _.filter(data, function(i) {
          return _.has(i, 'latitude') && '' !== i.latitude;
        });
      };

      MarkersCollectionView.prototype.render = function(marker) {
        var markerview;
        markerview = new MarkerView({
          model: marker,
          map: this.map
        });
        this.map.bounds.extend(marker.position());
        return markerview.add();
      };

      MarkersCollectionView.prototype.removeOld = function(collection, opts) {
        _.each(opts.previousModels, function(model) {
          return model.trigger('hide', model);
        });
        return this.collection.trigger('markers-reset');
      };

      MarkersCollectionView.prototype.fitBounds = function() {
        var autofit;
        autofit = parseInt(this.map.settings.autoFit);
        if (autofit === 1 && this.map.loaded === true) {
          return this.map.fitbounds();
        }
      };

      MarkersCollectionView.prototype.clearBounds = function() {
        return this.map.bounds = new google.maps.LatLngBounds();
      };

      MarkersCollectionView.prototype.clearClusterer = function() {
        return this.map.clusterer.clearMarkers();
      };

      MarkersCollectionView.prototype.setClusterer = function() {
        var markers;
        markers = this.collection.map(function(model) {
          return model.get('obj');
        });
        this.map.clusterer.addMarkers(markers);
        return this.map.obj.setZoom(this.map.obj.getZoom() + 1);
      };

      MarkersCollectionView.prototype.resize = function() {
        return google.maps.event.trigger(this.map.obj, 'resize');
      };

      return MarkersCollectionView;

    })(Backbone.View);
    MarkerView = (function(superClass) {
      extend(MarkerView, superClass);

      function MarkerView() {
        this.remove = bind(this.remove, this);
        this.add = bind(this.add, this);
        this.renderInfoBubble = bind(this.renderInfoBubble, this);
        return MarkerView.__super__.constructor.apply(this, arguments);
      }

      MarkerView.prototype.template = wp.template('infoBubble');

      MarkerView.prototype.initialize = function(options) {
        var trigger;
        if (options == null) {
          options = {};
        }
        this.map = options.map;
        this.defaults = {
          flat: true,
          draggable: false,
          position: this.model.position(),
          meta: this.model.toJSON()
        };
        this.marker = new google.maps.Marker(this.defaults);
        this.model.set('obj', this.marker);
        this.listenTo(this.model, 'hide', this.remove);
        trigger = this.map.settings.trigger;
        if ($(window).outerWidth() <= 992) {
          trigger = 'click';
        }
        return google.maps.event.addListener(this.model.get('obj'), trigger, this.renderInfoBubble);
      };

      MarkerView.prototype.renderInfoBubble = function() {
        if (this.map.infobubble.isOpen_ && this.map.infobubble.anchor === this.model.get('obj')) {
          return;
        }
        this.map.infobubble.open(this.map.obj, this.model.get('obj'));
        return this.map.infobubble.setContent(this.template(this.model.toJSON()));
      };

      MarkerView.prototype.add = function() {
        return this.model.get('obj').setMap(this.map.obj);
      };

      MarkerView.prototype.remove = function() {
        return this.model.get('obj').setMap(null);
      };

      return MarkerView;

    })(Backbone.View);
    Marker = (function(superClass) {
      extend(Marker, superClass);

      function Marker() {
        this.position = bind(this.position, this);
        return Marker.__super__.constructor.apply(this, arguments);
      }

      Marker.prototype["default"] = {
        id: '',
        obj: '',
        lat: '',
        lng: '',
        title: ''
      };

      Marker.prototype.position = function() {
        return new google.maps.LatLng(this.get('latitude'), this.get('longitude'));
      };

      return Marker;

    })(Backbone.Model);
    MarkersCollection = (function(superClass) {
      extend(MarkersCollection, superClass);

      function MarkersCollection() {
        return MarkersCollection.__super__.constructor.apply(this, arguments);
      }

      MarkersCollection.prototype.model = Marker;

      return MarkersCollection;

    })(Backbone.Collection);
    InfoBubble.prototype.getAnchorHeight_ = function() {
      return 48;
    };
    jobs = new MapView({
      canvas: 'job_listing-map-canvas',
      target: $('div.job_listings'),
      form: $('.job_filters'),
      list: 'ul.job_listings',
      submit: $('.job_filters').find('.update_results')
    });
    return resumes = new MapView({
      canvas: 'resume-map-canvas',
      target: $('div.resumes'),
      form: $('.resume_filters'),
      list: 'ul.resumes',
      submit: $('.resume_filters').find('.update_results')
    });
  });

}).call(this);

//# sourceMappingURL=app.js.map
