(function($){

	'use strict';

	$.fn.obSlider = function (opts) {

		var $self = $(this),
			defaults = {
				active: 0
			},
			options = $.extend(defaults, $self.data(), opts);

		var ObSlider = {

			$el        : $self,
			options    : options,
			$items     : [],
			$control   : $('<div class="ob-controls"><div class="next-item"></div><div class="prev-item"></div></div>'),
			$wrapSlider: $('<div class="ob-wrap-items"></div>'),

			init: function () {

				var _this = this;

				_this.createHTML();
				_this.events();

				_this.$el.data({
					'ObSlider'       : _this,
					'ObSliderOptions': _this.options
				});
			},

			createHTML: function () {

				var _this = this;

				$('> *', _this.$el).each(function (index, item) {

					_this.$items.push($(this));
					$(this).addClass('ob-item');
					_this.$wrapSlider.append(this);

				});

				_this.$el.append(_this.$wrapSlider).append(_this.$control);
				_this.activeItem(_this.options.active);

			},

			activeItem: function (index) {

				var _this = this,
					$item = _this.$items[index];

				if (_this.$currentActive) {
					_this.$currentActive.removeClass('ob-active');
				}
				$item.addClass('ob-active');
				_this.currentActive = index;
				_this.$currentActive = $item;

			},

			nextItem: function () {


				var _this = this,
					index = _this.currentActive + 1;

				if (index >= _this.totalItems) {
					index = 0;
				}

				_this.activeItem(index);
			},

			prevItem: function () {

				var _this = this,
					index = _this.currentActive - 1;

				if (index <= -1) {
					index = _this.totalItems - 1;
				}

				_this.activeItem(index);


			},

			events: function () {

				var _this = this;

				_this.$btnNext = $('.next-item', _this.$control);
				_this.$btnPrev = $('.prev-item', _this.$control);
				_this.totalItems = _this.$items.length;


				_this.$btnNext.on('click', function () {
					_this.nextItem();
				});

				_this.$btnPrev.on('click', function () {
					_this.prevItem();
				});

				$(window).resize(function () {

				});
			}

		};

		ObSlider.init();

	};

	$(document).ready( function () {
		$('#ob-advertisment').obSlider();
	});
})(jQuery);