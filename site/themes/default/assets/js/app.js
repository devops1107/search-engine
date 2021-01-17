"use strict";

function _createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

var $loader = $('#ajax-loader');
var $loaderInfinite = $('#ajax-loader-infinite');
var xhr;
var notyfOpts = {
  types: [{
    type: 'success',
    backgroundColor: 'auto',
    className: 'notyf-success',
    icon: false
  }, {
    type: 'danger',
    backgroundColor: 'auto',
    className: 'notyf-danger',
    icon: false
  }, {
    type: 'error',
    backgroundColor: 'auto',
    className: 'notyf-danger',
    icon: false
  }, {
    type: 'info',
    className: 'notyf-info',
    icon: false
  }, {
    type: 'warning',
    className: 'notyf-warning',
    icon: false
  }, {
    type: 'snack',
    className: 'notyf-snack',
    icon: false
  }],
  position: {
    x: 'center',
    y: 'bottom'
  },
  duration: 2500,
  ripple: true
};
window.notyf = new Notyf(notyfOpts);
var datePickerOpts = {
  selectMonths: false,
  formatSubmit: 'dd/mmmm/yyyy'
};
var recaptchaLoaded = false;

if (typeof grecaptcha !== 'undefined') {
  recaptchaLoaded = true;
}

var recaptchaWidgets = {};

if (window.Parsley) {
  window.Parsley.addValidator('maxFileSize', {
    validateString: function validateString(_value, maxSize, parsleyInstance) {
      var files = parsleyInstance.$element[0].files;
      return files.length != 1 || files[0].size <= maxSize * 1024;
    },
    requirementType: 'integer',
    messages: {
      custom: window.locale["validation-max-file-size"]
    }
  });
}
/**
 * Callback for recaptcha
 *
 * @param   token
 */


function recaptchaSuccessCallback(token) {
  $('.captcha-error').each(function (index, el) {
    $(this).hide();
  });
  $('.recaptcha-error').each(function (index, el) {
    $(this).removeClass('recaptcha-error');
  });
}
/**
 * The theme object
 *
 * @type {Object}
 */


var $theme = {};
/**
 * Options for parsley
 *
 * @type {Object}
 */

$theme.parsleyOptions = {
  errorClass: 'is-invalid',
  successClass: 'is-valid',
  errorsWrapper: '<span class="parsley-text form-text text-danger"></span>',
  errorTemplate: '<span></span>',
  trigger: 'focusout',
  focusInvalid: true,
  errorsContainer: function errorsContainer(el) {
    return el.$element.closest('.form-group');
  }
};
/**
 * Get svg icon for an ID
 *
 * @param  {String} id
 * @param  {String} classes
 * @param  {Object} attrs
 * @return {String}
 */

$theme.svgIcon = function (id, classes, attrs) {
  attrs = attrs || {};
  classes = classes || '';
  classes = 'svg-icon ' + classes;
  var attrText = '';

  for (prop in attrs) {
    if (prop == 'class') {
      continue;
    }

    attrText += '' + prop + '="' + attrs[prop] + '"';
  }

  return '<svg class="' + classes + '" ' + attrText + '><use xlink:href="#' + id + '"/></svg>';
};
/**
 * Get svg icon for alert type
 *
 * @param  {String} type
 * @return {String}
 */


$theme.svgIconForAlert = function (type) {
  var svgID = 'notifications';

  switch (type) {
    case 'danger':
    case 'warning':
      svgID = 'warning';
      break;

    case 'success':
      svgID = 'checkmark';
      break;

    default:
      svgID = 'notifications';
      break;
  }

  return $theme.svgIcon(svgID);
};
/**
* Builds a bootstrap alert
*
* @param  String text
* @param  String type
* @param  Boolean dismissable
* @return String
*/


$theme.buildAlert = function (text, type, dismissable, withIcon) {
  type = type || 'success';
  var icon_class = ' ';

  if (withIcon) {
    icon_class = ' alert-icon ';
  }

  var html = '<div class="alert' + icon_class + 'alert-' + type + '">';

  if (withIcon) {
    html += $theme.svgIconForAlert(type, 'mr-2');
  }

  if (dismissable) {
    html += '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>';
  }

  html += text + '</div>';
  return html;
};
/**
 * Localize numbers
 *
 * @param  {String} text
 * @return {String}
 */


$theme.localizeNumbers = function (text) {
  text = text.toString();

  for (var counter = 0; counter < 10; counter++) {
    var key = "num_" + counter;
    text = text.replace(new RegExp(counter, 'g'), window.locale[key]);
  }

  return text;
};
/**
 * Function to update a URL parameter
 *
 * @param  {String} uri
 * @param  {String} paramKey
 * @param  {String} paramVal
 * @return {String}
 */


$theme.updateUrlParam = function (uri, paramKey, paramVal) {
  var url = new URL(uri);
  var search_params = url.searchParams;
  search_params.set(paramKey, paramVal);
  url.search = search_params.toString();
  return url.toString();
};
/**
 * String templating
 *
 * @param  {String} text
 * @param  {Object} replacements
 * @return {String}
 */


$theme.strtpl = function (text, replacements) {
  text = text.replace(/\%(\s*?[\w.]+\s*?)%/mg, function (match, contents, offset, input_string) {
    if (contents in replacements) {
      return replacements[contents];
    }

    console.log('No replacement found for ' + match);
    return '';
  });
  return text;
};
/**
 * Build query string from object
 *
 * @param  {Object} obj
 * @return {String}
 */


$theme.buildQueryString = function (obj) {
  var str = [];

  for (var p in obj) {
    if (obj.hasOwnProperty(p)) {
      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
  }

  return str.join("&");
};
/**
 * Perform a redirect
 *
 * @param  {String} url
 * @param  {Boolean} cache
 * @param  {Boolean} httpRedirect
 */


$theme.redirect = function (url, cache, httpRedirect) {
  if (cache === false) {
    var time = new Date().getTime();
    url = $theme.updateUrlParam(url, '_', time);
  }

  if (httpRedirect) {
    window.location.replace(url);
  } else {
    window.location.href = url;
  }

  return true;
};
/**
 * Alias of $theme.redirect() for quick HTTP redirection
 *
 * @param  {String} url
 * @param  {Boolean} cache
 */


$theme.httpRedirect = function (url, cache) {
  return $theme.redirect(url, cache, true);
};
/**
 * Alias of $theme.redirect() for current page reloading
 *
 * @param  {Boolean} httpRedirect
 * @param  {Boolean} cache
 */


$theme.selfReload = function (httpRedirect, cache) {
  httpRedirect = httpRedirect || false;
  cache = cache || true;
  return $theme.redirect(window.location.href, cache, httpRedirect);
};

$theme.isObject = function (value) {
  return value && _typeof(value) === 'object' && value.constructor === Object;
};

$theme.isString = function (value) {
  return typeof value === 'string' || value instanceof String;
};

$theme.scrollToID = function (id, offset, delay) {
  offset = offset || 70;
  delay = delay || 500;

  if (!id.startsWith('#')) {
    id = '#' + id;
  }

  var position = $(id).offset().top - offset;
  $('html, body').animate({
    scrollTop: position
  }, delay);
};
/**
 * Initilazes parley with the options
 *
 */


$theme.loadParsley = function () {
  if (window.Parsley) {
    $("form").parsley($theme.parsleyOptions);
    window.Parsley.on('field:error', function (el) {
      this.$element.closest('.form-group').removeClass('input-has-success').addClass('input-has-error');
    });
    window.Parsley.on('field:success', function (el) {
      this.$element.closest('.form-group').removeClass('input-has-error').addClass('input-has-success');
    });
  }
};
/**
 * Refreshes Google Recaptcha
 *
 */


$theme.refreshRecaptcha = function () {
  if (!recaptchaLoaded) {
    return;
  }

  $('.g-recaptcha').each(function (index, el) {
    var captcha = $(this);
    var id = captcha.attr('id');
    var options = {
      sitekey: window.app.recaptchaSiteKey
    };
    var dataAttrs = ['sitekey', 'theme', 'tabindex', 'callback', 'expired-callback', 'error-callback'];
    var val = '';

    for (var i = dataAttrs.length - 1; i >= 0; i--) {
      val = dataAttrs[i];
      var value = captcha.data(val);

      if (value) {
        options[val] = value;
      }
    } // Google throws error when rendering twice


    try {
      var widgetId = grecaptcha.render(el, options);
      recaptchaWidgets[id] = widgetId;
    } catch (e) {}
  });
};
/**
 * Reload the markup after DOM changes
 *
 * @param  {String} target
 */


$theme.refreshMarkup = function (target) {
  $theme.loadParsley();
  $theme.refreshRecaptcha();
  $('.navdrawer-backdrop').remove();
  dynamicThemeEvents();
  $('.floating-label .custom-select, .floating-label .form-control').floatinglabel();
};
/**
 * Starts the ajaxLoader
 *
 * @param  {Boolean} insideForm Determines if the request is sent from a form submit or not
 */


$theme.startLoader = function (insideForm) {
  $loader.width(0).show();

  if (insideForm) {
    return;
  }

  $loader.width(30 + Math.random() * 30 + "%");
};
/**
 * Finish the ajaxLoader
 *
 * @param  {Boolean} insideForm Determines if the request is sent from a form submit or not
 */


$theme.finishLoader = function () {
  $loader.width("100%").delay(50).fadeOut(400, function () {
    $(this).width("0");
  });
};

$theme.showInfiniteLoader = function () {
  $loaderInfinite.width("100%").fadeIn('fast');
};

$theme.hideInfiniteLoader = function () {
  $loaderInfinite.width("0").fadeOut('fast');
};
/**
 * Call a function
 *
 * @param  {String} funcname
 * @param  {Array} args
 */


$theme.callFunction = function (funcname, args) {
  if (!funcname) {
    return;
  }

  if (typeof funcname === "function") {
    return funcname.apply(null, args);
  }

  return window[funcname].apply(null, args);
};
/**
 * Handles Pjax loading
 *
 * @param  {String} target
 * @param  {String} type
 * @param  {Object} params
 * @param  {Object} options
 */


$theme.ajaxContentLoad = function (target, type, params, options) {
  var formattedTarget;
  type = type || 'get';
  params = params || {};
  options = options || {};

  if (!options.el) {
    options.el = null;
  }

  if (type.toLowerCase() == 'post') {
    type = 'POST';
    params[app.csrfKey] = app.csrfToken;
  } else {
    type = 'GET';
  }

  if (options.instantFetch) {
    formattedTarget = target;
  } else {
    formattedTarget = $theme.updateUrlParam(target, 'ajax', 'true');
  }

  var finished = $theme.callFunction(options.pre_callback, [options.el]); // if explicitly returned TRUE we assume callback wants to handle the response completely on it's own

  if (finished === true) {
    return;
  }

  $theme.startLoader();
  $.ajax({
    url: formattedTarget,
    data: params,
    type: type,
    xhr: function xhr() {
      var xhr = $.ajaxSettings.xhr();

      xhr.onprogress = function (e) {
        if (e.lengthComputable) {
          $loader.width(e.loaded / e.total * 100 + '%');
        }
      };

      xhr.upload.onprogress = function (e) {
        if (e.lengthComputable) {
          $loader.width(e.loaded / e.total * 100 + '%');
        }
      };

      return xhr;
    },
    headers: {
      'x-spark-ajax': true
    }
  }).done(function (response) {
    $('body').removeClass('ajax-started');
    var finished = $theme.callFunction(options.success_callback, [response, options.el]); // if explicitly returned TRUE we assume callback wants to handle the response completely on it's own

    if (finished === true) {
      return;
    }

    if (response.redirect) {
      return $theme.redirect(response.redirect);
    }

    if (response.dash) {
      $theme.ajaxContentLoad(response.dash, 'GET', {});
      return;
    }

    if (response.title) {
      document.title = response.title;
    }

    if (response.body_class) {
      $('body').attr('class', response.body_class);
    }

    if (response.html_class) {
      $('html').attr('class', response.html_class);
    } // content that needs to be replaced


    if (response.sections) {
      for (var element in response.sections) {
        $('#' + element).html(response.sections[element]);
      }
    } // content that needs to be appended


    if (response.append) {
      for (var element in response.append) {
        $('#' + element).append(response.append[element]);
      }
    } // content that needs to be prepended


    if (response.prepend) {
      for (var element in response.prepend) {
        $('#' + element).append(response.prepend[element]);
      }
    }

    if (response.attr) {
      for (var selector in response.attr) {
        var attributes = response.attr[selector];

        if (selector == 'this') {
          options.el.attr(attributes);
          continue;
        }

        for (var attr in attributes) {
          var value = attributes[attr];
          $(selector).each(function () {
            $(this).attr(attr, value);
          });
        }
      }
    } // reload the plugins


    $theme.refreshMarkup(target); // the request type is GET

    if (type == 'GET') {
      // If we are on a different URL then add set the pushstate
      if (!options.instantFetch && target != window.location.href) {
        if (response.url) {
          window.history.pushState({
            path: response.url
          }, '', response.url);
        } else {
          window.history.pushState({
            path: target
          }, '', target);
        }
      } // Update the route URL variable if provided


      if (response.current_route_uri && window.app.currentRouteURI) {
        window.app.currentRouteURI = response.current_route_uri;
      } // Update the route name variable if provided


      if (response.current_route_name && window.app.currentRouteName) {
        window.app.currentRouteName = response.current_route_name;
      }
    } // optional scroll to top


    if (options.scrolltop) {
      $(document).scrollTop(0);
    }
  }).fail(function (response) {
    var finished = $theme.callFunction(options.error_callback, [response, options.el]); // if explicitly returned TRUE we assume callback wants to handle the response completely on it's own

    if (finished === true) {
      return;
    }

    notyf.error(response.status + ' ' + response.statusText);
  }).always(function () {
    $theme.finishLoader();
  });
  $theme.callFunction(options.post_callback, [options.el]);
};
/**
 * Handles ajax form submit
 *
 * @param  {Object} form The jQuery form element
 * @return {[type]}
 */


$theme.ajaxFormHandler = function (form) {
  try {
    xhr.abort();
  } catch (e) {
    console.log(e);
  }

  var data = new FormData(form[0]);
  var action = form.prop('action');
  var method = form.prop('method').toLowerCase();
  var parameters = '';
  var reset = false;

  if (form.data('reset') == true) {
    reset = true;
  }

  if (method == 'get') {
    method = 'GET';
    parameters = _toConsumableArray(data.entries()).map(function (e) {
      return encodeURIComponent(e[0]) + "=" + encodeURIComponent(e[1]);
    });
    parameters = parameters.join('&');

    if (parameters.length) {
      action += '?' + parameters;
    }
  } else {
    method = 'POST';
  }

  var success_callback = form.data('success-callback');
  var error_callback = form.data('error-callback');
  var redirect_to = form.data('data-redirect');
  var recaptcha_id = form.data('recaptcha-id');
  var notyf_position = form.data('toast-position');
  var recaptcha_el = $('#' + recaptcha_id); // Recaptcha validation

  if (recaptcha_el.length && recaptchaLoaded) {
    var recaptcha_widget_id = recaptchaWidgets[recaptcha_id];

    if (grecaptcha.getResponse(recaptcha_widget_id) == '') {
      recaptcha_el.addClass('recaptcha-error');
      $theme.scrollToID(recaptcha_id);
      notyf.error(locale["invalid-captcha"]);
      return;
    }
  }

  var submit = form.find(':submit');
  $theme.showInfiniteLoader(true);
  submit.attr('disabled', true);
  submit.addClass('btn-loading');
  $.ajax({
    url: action,
    type: method,
    processData: false,
    contentType: false,
    data: data,
    async: true
  }).done(function (response) {
    var finished = $theme.callFunction(success_callback, [response, form]); // if explicitly returned TRUE we assume callback wants to handle the response completely on it's own

    if (finished === true) {
      return;
    }

    if (response.type) {
      var type = response.type;
      var duration = notyfOpts.duration; // Increase the time for longer texts

      if (response.message.length > 100) {
        duration = 4000;
      }

      var toast = {
        type: type,
        message: response.message,
        duration: duration
      };

      if ($theme.isObject(notyf_position)) {
        toast.position = notyf_position;
      }

      notyf.open(toast);
    }

    if (response.redirect) {
      return $theme.httpRedirect(response.redirect);
    }

    if (response.dash) {
      $theme.ajaxContentLoad(response.dash, 'GET', {});
      return;
    }

    if (response.title) {
      document.title = response.title;
    }

    if (response.body_class) {
      $('body').attr('class', response.body_class);
    }

    if (recaptchaLoaded && recaptcha_el.length) {
      grecaptcha.reset();
    }

    if (form.parsley && response.type && response.type == 'success') {
      form.find('.input-has-success, .input-has-error').each(function (index, el) {
        $(this).removeClass('input-has-success').removeClass('input-has-error');
      });
      form.parsley().reset();
    }

    if (reset && response.type && response.type == 'success') {
      form.trigger('reset');
      form.find('.custom-file-label').text(window.locale["choose-file"]);
    }

    if (response.url && response.form_change_url) {
      window.history.pushState({
        path: response.url
      }, '', response.url);
    } // content that needs to be replaced


    if (response.sections) {
      for (var element in response.sections) {
        $('#' + element).html(response.sections[element]);
      }
    } // content that needs to be appended


    if (response.append) {
      for (var element in response.append) {
        $('#' + element).append(response.append[element]);
      }
    } // content that needs to be prepended


    if (response.prepend) {
      for (var element in response.prepend) {
        $('#' + element).append(response.prepend[element]);
      }
    }

    if (response.attr) {
      for (var selector in response.attr) {
        var element = response.attr[selector];

        for (var attr in element) {
          var value = element[attr];
          $(selector).each(function () {
            $(this).attr(attr, value);
          });
        }
      }
    }

    $theme.refreshMarkup();
  }).fail(function (response) {
    var finished = $theme.callFunction(error_callback, [response, form]); // if explicitly returned TRUE we assume callback wants to handle the response completely on it's own

    if (finished === true) {
      return;
    }

    notyf.error(response.status + ' ' + response.statusText);
  }).always(function (response) {
    $theme.hideInfiniteLoader();
    submit.removeClass('btn-loading');
    submit.attr('disabled', false);
  });
};
/**
 * Format AJAX response for form submission
 *
 * @param  {Mixed} response
 * @return {String}
 */


$theme.formatAjaxResponse = function (response) {
  if ($theme.isString(response)) {
    return response;
  }

  if (!response.message) {
    return false;
  }

  var dismissable = false;

  if (response.dismissable) {
    dismissable = true;
  }

  return $theme.buildAlert(response.message, response.type, dismissable, window.app.alertIcons);
};

var eye_svg_icon = $theme.svgIcon('eye');
var eye_svg_icon_off = $theme.svgIcon('eye-off');
/**
 * Recaptcha callback
 *
 */

function refreshRecaptcha() {
  $theme.refreshRecaptcha();
} // Load parsley because it doesn't work inside document ready


$theme.loadParsley();
$(document).ready(function () {
  // Instant Fetch
  if (window.app.enableAjaxNavigation) {
    $(document).on('click', "a.sp-link", function (e) {
      var link = $(this); // Ignore links that wants to open in a new window

      if (link.attr('target') == '_blank') {
        return;
      }

      var target = link.attr('href'); // No need for identifers or javascript urls

      if (target.startsWith('#') == true || target.startsWith('javascript:')) {
        return;
      }

      var abs_target = link.prop('href');
      var target_host = new URL(abs_target).hostname;

      if (target_host != window.location.hostname) {
        return;
      }

      var method = link.data('method') || 'get';

      if (method.toLowerCase() == 'post') {
        method = 'post';
      }

      var parameters = {};
      var params = link.data('params');

      if ($theme.isObject(params)) {
        parameters = params;
      }

      e.preventDefault();
      var scrolltop = true;

      if (link.data('scrolltop') === false) {
        scrolltop = false;
      }

      var options = {
        pre_callback: link.data('pre-callback'),
        success_callback: link.data('success-callback'),
        error_callback: link.data('error-callback'),
        post_callback: link.data('post-callback'),
        scrolltop: scrolltop,
        el: link
      };
      $('body').addClass('ajax-started');
      $theme.ajaxContentLoad(abs_target, method, parameters, options);
    });
  } // Dynamic components for custom content replacement


  $(document).on('click', "[data-instant-ajax]", function (e) {
    e.preventDefault();
    var link = $(this);
    var target = link.attr('data-instant-ajax');
    var abs_target = target;
    var method = link.data('method') || 'get';

    if (method.toLowerCase() == 'post') {
      method = 'post';
    }

    var parameters = {};
    var params = link.data('params');

    if ($theme.isObject(params)) {
      parameters = params;
    }

    var options = {
      pre_callback: link.data('pre-callback'),
      success_callback: link.data('success-callback'),
      error_callback: link.data('error-callback'),
      post_callback: link.data('post-callback'),
      scrolltop: false,
      instantFetch: true,
      el: link
    };
    $theme.ajaxContentLoad(abs_target, method, parameters, options);
  }); // Handle back button press

  if (window.app.enableAjaxNavigation) {
    $(window).on('popstate', function (e) {
      if (location.href.includes('#')) {
        return false;
      } // In case we were on a page where the content doesn't exist


      if ($('#content').length) {
        $theme.ajaxContentLoad(location.href, 'GET', {});
      } else {
        $theme.httpRedirect(location.href);
      }
    });
  }

  if (window.app.enableAjaxNavigation) {
    $(document).on('submit', 'form[data-ajax-form]', function (e) {
      var form = $(this);
      var before_callback = form.data('before-callback');
      var finished = $theme.callFunction(before_callback, [e]);

      if (finished === true) {
        return;
      }

      e.preventDefault();
      $theme.ajaxFormHandler(form);
    });
  } // Custom file inputs


  $(document).on('change', '.custom-file-input', function (e) {
    var $el = $(this),
        files = $el[0].files;
    var label_el = $el.next('.custom-file-label');

    if (files.length < 1) {
      label_el.html(window.locale["choose-file"]);
      return;
    }

    var label = files[0].name;

    if (files.length > 1) {
      label = label + " and " + String(files.length - 1) + " more files";
    }

    label_el.html(label);
  }); // Simple attribute based toggling

  $(document).on('click', '[data-toggle="class"]', function (event) {
    event.preventDefault();
    var target = $(this).data('target');
    var targetEl;

    if (!target) {
      targetEl = $(this);
    } else if (target == 'parent') {
      targetEl = $(this).parent();
    } else {
      targetEl = $(target);
    }

    var classes = $(this).data('classes');
    targetEl.toggleClass(classes);
    return false;
  });
  $(document).on('click', '[data-focus]', function (event) {
    event.preventDefault();
    var target = $($(this).data('focus'));
    target.focus();
  });
}); // Executed on DOM load and also when the dom is loaded via ajax

function dynamicThemeEvents() {
  // Start floating labels
  $('.floating-label .custom-select, .floating-label .form-control').floatinglabel();
  var body = $('body');

  if (window.app.settings.search_autocomplete) {
    $('[data-autocomplete]').autocomplete({
      autosuggest: false,
      appendMethod: 'replace',
      openOnFocus: true,
      closeOnBlur: true,
      minLength: 3,
      accents: false,
      valid: function valid(value, query) {
        return true;
      },
      source: [function (q, add) {
        if (q.toString().length < 3) {
          return;
        }

        try {
          xhr.abort();
        } catch (e) {}

        xhr = $.getJSON(window.app.suggestionEndpoint + "?q=" + encodeURIComponent(q), function (response) {
          add(response);
        });
      }]
    }).on("open.xdsoft", function () {
      $(this).closest('.searchbox-group').addClass('suggestions-active');
    }).on("close.xdsoft", function () {
      $(this).closest('.searchbox-group').removeClass('suggestions-active');
    }).on('selected.xdsoft', function (e, data) {
      $("#searchForm").submit();
    });
    $(['data-autocomplete']).autocomplete('update');
  }
}

function getParameterByName(name, url) {
  name = name.replace(/[\[\]]/g, '\\$&');
  var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
      results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function preventEmptySubmit(e) {
  if (!$('input[name=q]').val().length) {
    e.preventDefault();
    return true;
  }

  return false;
}

var accordsMarkup = '<div class="card my-4"><div class="card-body py-2"><div class="list-group bg-transparent list-group-flush" id="answers-accordion"><h3 class="card-title m-0">People also ask</h3></div></div>';
var accords = '';
var hasAccord = false;
jQuery(document).ready(function ($) {
  $('body').tooltip({
    selector: '[data-toggle="tooltip"], [data-tooltip]',
    boundary: 'window'
  });
  $(document).on('click', '.gs-spelling a', function (e) {
    var query = $(this).text();
    $('input[data-search-input]').val(query);
  });
  $(document).on('focus', '#home-search-input', function (e) {
    $('#dimmer').addClass('dimmer-active');
  });
  $(document).on('click', function (event) {
    var $trigger = $("#home-search-group");

    if ($trigger !== event.target && !$trigger.has(event.target).length) {
      $('#dimmer').removeClass('dimmer-active');
    }
  });
  $(document).on('click', '.gs-previewLink', function (e) {
    var src = $(this).find('img').attr('src');

    if (src) {
      e.preventDefault();
      window.open(src, '_blank');
    }
  });

  var resultsRenderedCallback = function resultsRenderedCallback() {
    $('#search-loader').hide();
    $('#instant-answer').show();

    if (hasAccord) {
      $('#web-result-1').before(accords);
    }
  };

  var resultsReadyCallback = function resultsReadyCallback(name, q, promos, results, resultsDiv) {
    var makeResultParts = function makeResultParts(result, id) {
      var thumb = '';

      if (result.thumbnailImage && engine.showThumbnails) {
        thumb = "<img src=\"".concat(result.thumbnailImage.url, "\" class=\"web-thumb-img\">");
      }

      var target = '';

      if (window.app.settings.new_window) {
        target = '_blank';
      }

      var resURL = getParameterByName('q', result['url']);
      var resDomain = resURL.replace(/(^\w+:|^)\/\//, '').replace(/\/$/, "");
      var anchor = "<div class=\"web-result\" id=\"web-result-".concat(id, "\">\n            <a class=\"web-result-title\" href=\"").concat(resURL, "\" target=\"").concat(target, "\" rel=\"nofollow nopener noreferer\"><h3 class=\"web-result-title-heading\">\n            ").concat(result['title'], "</h3></a>\n            <div class=\"web-result-domain\"><img src=\"https://www.google.com/s2/favicons?domain=").concat(result['visibleUrl'], "\" class=\"web-result-favicon\">").concat(resDomain, "</div>  ").concat(thumb, "\n            <p class=\"web-result-desc\">").concat(result['content'], "</p>\n\n            </div>");
      return anchor;
    };

    if (results.length > 1) {
      accords = accordsMarkup;
      var items = '';
      var i = 0;
      var answerDone = false;
      var i2 = 0;

      var _iterator = _createForOfIteratorHelper(results),
          _step;

      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var result = _step.value;

          if (result.richSnippet) {
            if (result.richSnippet.answer && result.richSnippet.answer[0] && result.richSnippet.question) {
              if (i == 5) {
                break;
              }

              i++;
              var expanded = 'false';
              var collapsed = 'collapsed';
              var collapse = 'collapse';
              var show = '';

              if (i == 1) {
                expanded = 'true';
                show = 'show';
                collapse = 'collapse show';
              }

              accords += "\n  <div class=\"expansion-panel list-group-item bg-transparent ".concat(show, "\">\n    <a aria-controls=\"collapse-").concat(i, "\" aria-expanded=\"").concat(expanded, "\" class=\"font-weight-bold expansion-panel-toggler ").concat(collapsed, "\" data-toggle=\"collapse\" href=\"#collapse-").concat(i, "\" id=\"collapse-heading-").concat(i, "\">\n    ").concat(result.richSnippet.question.name, "\n      <div class=\"expansion-panel-icon ml-3 text-black-secondary\">\n        <i class=\"collapsed-show\">").concat($theme.svgIcon('keyboard-arrow-down'), "</i>\n        <i class=\"collapsed-hide\">").concat($theme.svgIcon('keyboard-arrow-up'), "</i>\n      </div>\n    </a>\n    <div aria-labelledby=\"collapse-heading-").concat(i, "\" class=\"").concat(collapse, "\" data-parent=\"#answers-accordion\" id=\"collapse-").concat(i, "\">\n      <div class=\"expansion-panel-body\"><p class=\"card-text\">").concat(result.richSnippet.answer[0].text, " <a href=\"").concat(result.richSnippet.answer[0].url, "\" target=\"_blank\" class=\"\">Read more</a></p>\n      </div>\n    </div>\n  </div>");
            }
          }

          i2++;
          items += makeResultParts(result, i2);
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }

      accords += '</div></div>';
      $(resultsDiv).html(items);

      if (i > 3) {
        hasAccord = true;
      } else {
        hasAccord = false;
      }
    } else {
      var nothing = "<div class=\"no-results\"><p>".concat(window.locale['no-search-results'], " <strong>").concat(q, "</strong></p>\n            <p>").concat(window.locale['suggestions'], "</p>\n            <ul class=\"px-3\">\n            <li>").concat(window.locale['make-sure-spelling'], "</li>\n            <li>").concat(window.locale['try-different-keywords'], "</li>\n            <li>").concat(window.locale['try-general-keywords'], "</li>\n            <li>").concat(window.locale['try-fewer-keywords'], "</li>\n            </ul></div>");
      $(resultsDiv).html(nothing);
    }

    return true;
  };

  window.__gcse || (window.__gcse = {});
  window.__gcse.searchCallbacks = {
    web: {
      ready: resultsReadyCallback,
      rendered: resultsRenderedCallback
    },
    image: {
      rendered: resultsRenderedCallback
    }
  };
  dynamicThemeEvents();
  $(window).on('scroll load', function (event) {
    var scroll = $(window).scrollTop();

    if (scroll > 56) {
      $('body').addClass('navbar-active');
      $('#site-navbar').fadeIn(500).addClass("navbar-scrolled");
    } else {
      $('#site-navbar').fadeIn(500).removeClass("navbar-scrolled");
      $('body').removeClass('navbar-active');
    }
  });
  $(document).on('click', '[data-engine-id]', function (event) {
    event.preventDefault();
    var el = $(this);
    var id = el.data('engine-id');
    var target = el.data('target');
    var textTarget = el.data('text-target');

    if (target) {
      $(target).val(id);
    }

    if (textTarget) {
      $(textTarget).text(el.text());
    }

    $('[data-engine-active="true"]').removeClass('active').attr('data-engine-active', false);
    $('[data-engine-id="' + id + '"]').attr('data-engine-active', true).addClass('active');
  }); // Enable Popovers

  $('body').popover({
    selector: '[data-toggle="popover"]',
    html: true
  });
  $(document).on('change', '[data-file-preview]', function (event) {
    var input = this;
    var target = $($(this).data('file-preview'));

    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        target.css('background-image', 'url(' + e.target.result + ')');
      };

      reader.readAsDataURL(input.files[0]);
    }
  });
});