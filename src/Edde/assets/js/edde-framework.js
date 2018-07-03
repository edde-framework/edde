(function ($) {
	var xhrPool = [];
	var $document = $(document);
	$document.ajaxSend(function (e, jqXHR, options) {
		xhrPool.push(jqXHR);
	});
	$document.ajaxComplete(function (e, jqXHR, options) {
		xhrPool = $.grep(xhrPool, function (x) {
			return x != jqXHR
		});
	});
	var abort = function () {
		$.each(xhrPool, function (idx, jqXHR) {
			jqXHR.abort();
		});
	};
	var onBeforeUnload = window.onbeforeunload;
	window.onbeforeunload = function () {
		var result = onBeforeUnload ? onBeforeUnload() : undefined;
		if (result == undefined) {
			abort();
		}
		return result;
	}
})(jQuery);

var Edde = {
	Event: {
		listen: function (event, handler) {
			$(document).on(event, handler);
		},
		/**
		 *
		 * @param {string} event
		 * @param {object} [parameterList]
		 * @returns {jQuery.Event}
		 */
		event: function (event, parameterList) {
			event = $.extend(true, $.Event(event), parameterList || {});
			$(document).trigger(event);
			return event;
		}
	},
	Utils: {
		redirect: function (url) {
			var event = Edde.Event.event('edde.redirect', {
				url: url
			});
			if (event.isDefaultPrevented()) {
				return;
			}
			window.location.href = url;
		},
		clientClass: function (selector, func) {
			setTimeout(function () {
				$(selector).each(function (i, element) {
					if (typeof func === 'function') {
						func.call(element, ($(element)));
						return;
					}
					$.extend(true, element, func);
				});
			}, 0);
		},
		update: function (update) {
			if (update.redirect) {
				Edde.Utils.redirect(update.redirect);
				return;
			}
			if (update.javaScript) {
				$.each(update.javaScript, function (i, src) {
					$.getScript(src);
				});
			}
			if (update.styleSheet) {
				$.each(update.styleSheet, function (i, src) {
					if ($('head link[href="' + src + '"]').length) {
						return;
					}
					$('<link/>', {rel: 'stylesheet', href: src}).appendTo('head');
				});
			}
			if (update.selector) {
				$.each(update.selector, function (selector, value) {
					switch (value.action) {
						case 'replace':
							$(selector).replaceWith(value.source);
							break;
						case 'add':
							$(selector).append(value.source);
							break;
					}
				});
			}
		},
		ajax: function (url, parameterList) {
			return $.ajax({
				url: url,
				method: 'POST',
				data: parameterList ? JSON.stringify(parameterList) : {},
				timeout: 3600 * 1000,
				contentType: 'application/json',
				dataType: 'json',
				cache: false
			});
		},
		execute: function (url, parameterList) {
			var event = Edde.Event.event('edde.on-ajax', {
				url: url,
				parameterList: parameterList
			});
			if (event.isDefaultPrevented()) {
				return;
			}
			return Edde.Utils.ajax(url, parameterList).fail(function (e) {
				Edde.Event.event('edde.on-ajax-fail', {
					error: e
				});
			}).done(function (data) {
				Edde.Event.event('edde.on-ajax-done', {
					data: data
				});
				Edde.Utils.update(data);
			}).always(function () {
				Edde.Event.event('edde.on-ajax-always');
			});
		},
		crate: function (id) {
			var crate = null;
			if (id) {
				crate = {};
				$source = $('#' + id);
				$source.find('*[data-schema]').addBack().each(function () {
					var $this = $(this);
					var dataClass = $this.data('schema');
					if (dataClass) {
						if (this.getValue === undefined) {
							console.error('Element without getValue() method!');
							console.error(this);
							return;
						}
						crate[dataClass] = crate[dataClass] ? crate[dataClass] : {};
						crate[dataClass][$this.data('property')] = this.getValue();
					}
				});
				var index = 0;
				$source.find('*[data-fill]').addBack().each(function () {
					var name = $(this).data('fill');
					if (name) {
						if (this.getValue === undefined) {
							console.error('Element without getValue() method!');
							console.error(this);
							return;
						}
						crate[''] = crate[''] || {};
						if (name.indexOf('[]') === name.length - 2) {
							name = name.substr(0, name.length - 2);
							crate[''][name] = crate[''][name] || {};
							var key = index++;
							if (this.getKey) {
								key = this.getKey();
								index--;
							}
							crate[''][name][key] = this.getValue();
							return;
						}
						crate[''][name] = this.getValue();
					}
				});
			}
			return crate;
		}
	}
};

var $document = $(document);
$document.ready(function () {
	var redirect = false;
	Edde.Event.listen('edde.redirect', function () {
		redirect = true;
	});
	$document.on('click', '.button[data-action]', function (event) {
		if (event.isDefaultPrevented()) {
			return;
		}
		var $this = $(this);
		if ($this.hasClass('disabled')) {
			return;
		}
		$this.addClass('disabled');
		Edde.Utils.execute($this.data('action'), Edde.Utils.crate($this.data('bind'))).always(function () {
			if (redirect === false) {
				$this.removeClass('disabled');
			}
		});
	});
});
