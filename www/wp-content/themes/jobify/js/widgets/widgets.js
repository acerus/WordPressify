(function() {
  jQuery(function($) {
    var companies, testimonials;
    companies = function() {
      var ref, ref1;
      return $('.company-slider .testimonials-list').slick({
        autoplay: (ref = jobifySettings.widgets) != null ? (ref1 = ref.jobify_widget_companies) != null ? ref1.autoPlay : void 0 : void 0,
        autoplaySpeed: 3000,
        centerMode: true,
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 1,
        adaptiveHeight: true,
        responsive: [
          {
            breakpoint: 1200,
            settings: {
              slidesToShow: 3
            }
          }, {
            breakpoint: 992,
            settings: {
              slidesToShow: 2
            }
          }, {
            breakpoint: 768,
            settings: {
              slidesToShow: 1
            }
          }
        ]
      });
    };
    testimonials = function() {
      var ref, ref1;
      $('.testimonial-slider .testimonials-list').slick({
        infinite: false,
        slidesToShow: (ref = jobifySettings.widgets) != null ? (ref1 = ref.jobify_widget_testimonials) != null ? ref1.slidesToShow : void 0 : void 0,
        slidesToScroll: 1,
        adaptiveHeight: true,
        responsive: [
          {
            breakpoint: 992,
            settings: {
              slidesToShow: 2
            }
          }, {
            breakpoint: 768,
            settings: {
              slidesToShow: 1
            }
          }
        ]
      });
      return $('.testimonial-slider .testimonials-list').slick('setPosition');
    };
    companies();
    return testimonials();
  });

}).call(this);

//# sourceMappingURL=widgets.js.map
