jQuery ($) ->

  companies = ->
    $( '.company-slider .testimonials-list' ).slick
      autoplay: jobifySettings.widgets?.jobify_widget_companies?.autoPlay
      autoplaySpeed: 3000
      centerMode: true
      infinite: true
      slidesToShow: 5
      slidesToScroll: 1
      adaptiveHeight: true
      responsive: [
        {
          breakpoint: 1200
          settings:
            slidesToShow: 3
        }
        {
          breakpoint: 992
          settings:
            slidesToShow: 2
        }
        {
          breakpoint: 768 
          settings:
            slidesToShow: 1
        }
      ]

  testimonials = ->
    $( '.testimonial-slider .testimonials-list' ).slick
      infinite: false
      slidesToShow: jobifySettings.widgets?.jobify_widget_testimonials?.slidesToShow
      slidesToScroll: 1
      adaptiveHeight: true
      responsive: [
        {
          breakpoint: 992
          settings:
            slidesToShow: 2
        }
        {
          breakpoint: 768 
          settings:
            slidesToShow: 1
        }
      ]

    $( '.testimonial-slider .testimonials-list' ).slick 'setPosition'

  companies()
  testimonials()
