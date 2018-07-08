jQuery ($) ->

  class MapView extends Backbone.View
    bounds: new google.maps.LatLngBounds()

    infobubble: new InfoBubble(
      backgroundClassName: 'map-marker-info'
      borderRadius: 4
      padding: 15 
      borderColor: '#ffffff'
      shadowStyle: 0
      minHeight: 70
      minWidth: 225
      maxWidth: 275
      hideCloseButton: true
      flat: true
    )	

    loaded: false

    initialize: (settings = {}) ->
      @settings = _.defaults settings, jobifyMapSettings

      google.maps.event.addDomListener window, 'load', @canvas

      @markersCollectionView = new MarkersCollectionView
        map: @
        collection: new MarkersCollection()

      # these should both call one event
      @settings.target.on 'updated_results', (event, results) =>
        @loaded = true
        @markersCollectionView.load event

    canvas: =>
      def = $.Deferred()
      @el = document.getElementById( @settings.canvas );

      if ! @el then return def.reject()

      @mapOptions = @settings.mapOptions

      @opts =
        gestureHandling: 'cooperative'
        zoom: parseInt @mapOptions.zoom
        maxZoom: parseInt @mapOptions.maxZoom
        minZoom: parseInt @mapOptions.maxZoomOut
        scrollwheel: @mapOptions.scrollwheel
        zoomControlOptions:
          position: google.maps.ControlPosition.RIGHT_TOP
        streetViewControl: true
        streetViewControlOptions:
          position: google.maps.ControlPosition.RIGHT_TOP

      if @mapOptions.center
        @defaultCenter = new google.maps.LatLng @mapOptions.center[0], @mapOptions.center[1]
      else
        @defaultCenter = new google.maps.LatLng 41.850033, -87.6500523

      @opts.center = @defaultCenter

      @obj = new google.maps.Map( @el, @opts )

      @createClusterer()

      google.maps.event.addListener @obj, 'click', @hideBubble 
      google.maps.event.addListener @obj, 'zoom_changed', @hideBubble

      # Ugh
      map = @obj

      google.maps.event.addListener @obj, 'dragend', ->
        google.maps.event.trigger map, 'resize'

      google.maps.event.addListenerOnce @obj, 'idle', ->
        @loaded = true
        def.resolve(@obj) 

      $(window).on 'resize', @resize
      @mapHeight()

      def.promise()

    mapHeight: =>

    resize: =>
      @mapHeight()
      google.maps.event.trigger @obj, 'resize'
      @fitbounds()

    createClusterer: =>
      @clusterer = new MarkerClusterer null, [], {
        ignoreHidden: true,
      }

      @clusterer.setMap @obj
      @clusterer.setMaxZoom @opts.maxZoom
      @clusterer.setGridSize parseInt @mapOptions.gridSize

      google.maps.event.addListener @clusterer, 'click', @clusterOverlay

    clusterOverlay: (c) =>
      markers = c.getMarkers()
      zoom = @obj.getZoom()

      if zoom < @opts.maxZoom then return

      content = _.map markers, (marker) ->
        template = wp.template 'infoBubble'
        template marker.meta

      title = @settings.overlayTitle.replace( '%d', markers.length )

      $.magnificPopup.open(
        items:
          src: '<div class="modal"><h2 class="modal-title">' + title + '</h2><ul class="cluster-items"><li class="map-marker-info">' +
              content.join( '</li><li class="map-marker-info">' ) +
            '</li></ul></div>',
          type: 'inline'
      )

    fitbounds: =>
      @obj.fitBounds @bounds

    hideBubble: =>
      @infobubble.close()

    showDefault: =>
      if _.isUndefined @obj then return true

      @obj.setCenter @opts.center
      @obj.setZoom @opts.zoom


  class MarkersCollectionView extends Backbone.View
    initialize: (options={}) ->
      @map = options.map

      google.maps.event.addDomListener window, 'load', @listen

    listen: =>
      if _.isUndefined @map.obj then return @

      @listenTo(@collection, 'add', @render)
      @listenTo(@collection, 'reset', @removeOld)

      if @map.settings.useClusters == '1'
        @listenTo(@collection, 'markers-reset', @clearClusterer)
        @listenTo(@collection, 'markers-added', @setClusterer)

      @listenTo(@collection, 'markers-reset', @clearBounds)
      @listenTo(@collection, 'markers-added', @fitBounds)
      @listenTo(@collection, 'markers-added', @resize)

    load: (event) =>
      data = @parseResults event;
      @collection.reset()

      if _.isEmpty data
        @map.showDefault()
      else 
        @collection.set data
        @collection.trigger 'markers-added'

    parseResults: (event) =>
      if ! _.isUndefined event && ! _.isUndefined event.target
        html = $( event.target ).find( @map.settings.list ).first().find( 'li' )
      else
        html = $( @map.settings.list ).first().find( 'li' )

      data = _.map html, (i) ->
        $(i).data()

      data = _.filter data, (i) ->
        _.has( i, 'latitude' ) && '' != i.latitude

    render: (marker) =>
      markerview = new MarkerView
        model: marker
        map: @map

      @map.bounds.extend marker.position()

      markerview.add()

    removeOld: (collection, opts) =>
      _.each opts.previousModels, (model) ->
        model.trigger( 'hide', model )

      @collection.trigger 'markers-reset'

    fitBounds: =>
      autofit = parseInt @map.settings.autoFit

      if autofit == 1 && @map.loaded == true 
        @map.fitbounds()

    clearBounds: =>
      @map.bounds = new google.maps.LatLngBounds()

    clearClusterer: =>
      @map.clusterer.clearMarkers()

    setClusterer: =>
      markers = @collection.map (model) ->
        model.get 'obj'

      @map.clusterer.addMarkers markers
      @map.obj.setZoom( @map.obj.getZoom() + 1 );

    resize: =>
      google.maps.event.trigger @map.obj, 'resize'

  class MarkerView extends Backbone.View
    template: wp.template 'infoBubble'

    initialize: (options = {}) ->
      @map = options.map

      @defaults = {
        flat: true
        draggable: false
        position: @model.position()
        meta: @model.toJSON()
      }

      @marker = new google.maps.Marker @defaults;
      @model.set 'obj', @marker 

      @listenTo( @model, 'hide', @remove )

      trigger = @map.settings.trigger

      if $(window).outerWidth() <= 992 then trigger = 'click'

      google.maps.event.addListener(@model.get( 'obj' ), trigger, @renderInfoBubble)

    renderInfoBubble: =>
      if @map.infobubble.isOpen_ && @map.infobubble.anchor == @model.get( 'obj' )
        return

      @map.infobubble.open( @map.obj, @model.get( 'obj' ) )
      @map.infobubble.setContent( @template( @model.toJSON() ) )

    add: =>
      @model.get( 'obj' ).setMap @map.obj

    remove: =>
      @model.get( 'obj' ).setMap null

  class Marker extends Backbone.Model
    default:
      id: ''
      obj: ''
      lat: ''
      lng: ''
      title: ''

    position: =>
      new google.maps.LatLng(
        @get( 'latitude' ),
        @get( 'longitude' ) 
      )

  class MarkersCollection extends Backbone.Collection
    model: Marker

  InfoBubble.prototype.getAnchorHeight_ = ->
   48 

  jobs = new MapView
    canvas: 'job_listing-map-canvas'
    target: $( 'div.job_listings' )
    form: $( '.job_filters' )
    list: 'ul.job_listings'
    submit: $( '.job_filters' ).find( '.update_results' )

  resumes = new MapView
    canvas: 'resume-map-canvas'
    target: $( 'div.resumes' )
    form: $( '.resume_filters' )
    list: 'ul.resumes' 
    submit: $( '.resume_filters' ).find( '.update_results' )
