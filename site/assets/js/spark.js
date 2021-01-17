"use strict";

/**
 * Dashboard
 *
 * Holds methods for backend usage
 *
 * @type {Object}
 */
 var $spark = {
  strtpl: function (text, replacements) {
    text = text.replace(/\%(\s*?[\w.]+\s*?)%/mg, function(match, contents, offset, input_string) {
      if (contents in replacements) {
        return replacements[contents];
      }

      console.log('No replacement found for ' + match);
      return '';
    });
    return text;
  },

  /**
   * Ensures absolute URLs
   *
   * @param  String url
   * @return String
   */
  absURL: function (url) {
    // Absolute URL
    if (url.indexOf('http://') === 0 || url.indexOf('https://') === 0) {
        return url;
    }

    // Non URL
    if (url.startsWith('#') || url.startsWith('javascript:')) {
        return url;
    }

    return base_uri + url.replace(/^\/+/g, '');
  },

    /**
     * Create Slug From String
     *
     * @param  {String} text  The string
     * @return {String}
     */
     slugify: function slugify(text) {
      text = text.toString().toLowerCase()
      .replace(/\s+/g, '-');

      if (!/[^\u0000-\u007f]/.test(text)) {
        text = text.replace(/[^\w\-]+/g, '-');
      } else {
        text = text.replace(/[\/@.#!\\|?]/g, '-');
      }

      text = text.replace(/\-\-+/g, '-')
      .replace(/^-+/, '')
      .replace(/-+$/, '');

      return text;
    },

    /**
     * Add or Update query params to a URL string
     *
     * @param  {String} uri      The URL
     * @param  {String} paramKey Parameter key
     * @param  {String} paramVal Parameter key value
     * @return
     */
     updateUrlParam: function (uri, paramKey, paramVal) {
      var re = new RegExp("([?&])" + paramKey + "=[^&#]*", "i");
      if (re.test(uri)) {
        uri = uri.replace(re, '$1' + paramKey + "=" + paramVal);
      } else {
        var separator = /\?/.test(uri) ? "&" : "?";
        uri = uri + separator + paramKey + "=" + paramVal;
      }
      return uri;
    },

    /**
     * Perform a redirect
     *
     * @param  {String}  url          The URL
     * @param  {Boolean} cache        To avoid cache set this to false and a _nocahe param will be added
     * @param  {Boolean} httpRedirect Toggle window.location.replace() defaults to false
     * @return
     */
     redirect: function (url, cache, httpRedirect) {
      if (cache === false) {
        var time = new Date().getTime();
        url = $spark.updateUrlParam(url, '_', time);
      }

      if (httpRedirect) {
        window.location.replace(url);
      } else {
        window.location.href = url;
      }
      return true;
    },

    /**
     * Perform a HTTP redirect
     *
     * Alias of this.redirect();
     *
     * @param  {String}  url          The URL
     * @param  {Boolean} cache        To avoid redirect caching set this to false and a _nocahe param will be added
     * @return
     */
     httpRedirect: function (url, cache) {
      return $spark.redirect(url, cache, true);
    },

    selfReload: function (httpRedirect, cache)
    {
      httpRedirect = httpRedirect || false;
      cache = cache || true;

      return $spark.redirect(window.location.href, cache, httpRedirect);
    },

    /**
     * Checks if a var is object or not
     *
     * @param  mixed  value
     * @return {Boolean}
     */
     isObject: function (value) {
      return value && typeof value === 'object' && value.constructor === Object;
    },

    /**
     * Checks if a var is string or not
     *
     * @param  mixed  value
     * @return {Boolean}
     */
     isString: function (value) {
      return typeof value === 'string' || value instanceof String;
    },

    /**
     * Show backend Ajax Error alert
     *
     * @return
     */
     ajaxError: function () {
      lnv.alert({
        title: spark_i18n.ajax_err_title,
        content: spark_i18n.ajax_err_desc,
        alertBtnText: spark_i18n.okay
      });

    },

    /**
     * Show or hide the ajax loader
     *
     * @param  {String} state
     * @return
     */
     ajaxLoader: function (state) {
      var ajaxLoader = $('#global-ajax-overlay');
      if (state == 'hide') {
        $('body').removeClass('ajax-loader-active');
        ajaxLoader.hide();
      } else {
        $('body').addClass('ajax-loader-active');
        ajaxLoader.fadeIn(30);
      }
    },

    formatAjaxResponse: function (response) {
      if ($spark.isString(response)) {
        return response;
      }

      if (!response.message) {
        return false;
      }

      var dismissable = false;
      if (response.dismissable) {
        dismissable = true;
      }

      return $spark.buildAlert(response.message, response.type, dismissable);
    },

    buildAlert: function (text, type, dismissable) {
      type = type || 'success';
      var html = '<div class="alert alert-' + type + '">';

      if (dismissable) {
        html += '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>';
      }

      html += text + '</div>';

      return html;
    },

    buildQueryString: function (obj) {
      var str = [];
      for (var p in obj)
        if (obj.hasOwnProperty(p)) {
          str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
        return str.join("&");
      },

    /**
     * Scroll to specific ID
     *
     * @param  {String} id
     * @return
     */
     scrollToID: function (id) {
      var position = $(id).offset().top - 70;
      $('html, body').animate({
        scrollTop: (position)
      }, 500);
    },

    /**
     * Wrapper for Quick Ajax POST Requests
     *
     * @param  {String}   url
     * @param  {Object}   data
     * @param  {Function} doneCallback
     * @return
     */
     ajaxPost: function (url, data, doneCallback, alwaysCallback, options) {
      $spark.ajaxLoader('show');

      data = data || {};
      doneCallback = doneCallback || function () {};
      alwaysCallback = alwaysCallback || function () {};

      var hasFile = false;

      if (data instanceof FormData) {
        data.append(csrf_key, csrf_token);
        hasFile = true;
      } else if ($spark.isString(data)) {
        data = data + csrf_token_amp;
      } else if ($spark.isObject(data)) {
        data.csrf_token = csrf_token;
      }

      var options = {
        cache   :   false,
        type    :   'POST',
        url     :   url,
        data    :   data,
      };

      if (hasFile) {
        options.contentType = false;
        options.processData = false;
      }

      $.ajax(options).done(function (response) {
        $spark.ajaxLoader('hide');
        doneCallback(response);
      }).fail(function (response) {
        $spark.ajaxLoader('hide');
        $spark.ajaxError('show');
      }).always(function (res) {
        alwaysCallback(res);
      });
    },

  };

  var parsleyOptions = {
   errorClass: 'is-invalid text-danger',
   successClass: 'is-valid',
   errorsWrapper: '<span class="form-text text-danger"></span>',
   errorTemplate: '<span></span>',
   trigger: 'focusout',
   focusInvalid: true,
 };


 $(function() {
  // Generic ajax form
  $(document).on('submit', 'form[data-spark-ajax]', function (e) {
    e.preventDefault();

    // The form
    var form = $(this);

    // IE can suck dick
    var data = new FormData(form[0]);

    // Form Action
    var action = form.prop('action');

    // Custom target for the response
    var response_target = form.data('response-target');

    var success_callback = form.data('success-callback');


    // The default redirect location
    var redirect_to = form.data('data-redirect');

    var submit = form.find(':submit');

    submit.attr('disabled', true);

    $spark.ajaxPost(action, data, function (response) {

      if (success_callback) {
        window[success_callback](response);
      }

      // If there's a response target
      if (response_target) {
        var responseDiv = $(response_target);
        responseDiv.hide();

        var responseText = $spark.formatAjaxResponse(response);

        // If there's a response text set it to the response div
        if (responseText) {
          responseDiv.html(responseText);
          // boom! you looking for this?
          responseDiv.fadeIn();
          // scroll to that element
          //$spark.scrollToID(response_target);
        }
      }

      if ($spark.isObject(response) && response.redirect) {
        $spark.httpRedirect(response.redirect);
      }

    }, function (res) {
      submit.attr('disabled', false);
    });
  });

  $('select[data-url-select]').on('change', function () {
    var url = $(this).val();
    if (url) {
      $spark.redirect(url);
    }
    return false;
  });

  $(document).on('click', '[data-copy-to-clipboard]', function(e) {
    e.preventDefault();

    var el = $(this);

    // If it's the text type use the already existing input
    if (el.is('input:text') || el.is('textarea')) {
      el.select();
      return document.execCommand('copy');
    }

    // Try to fetch via data copy value
    var text_to_copy = el.attr('data-copy-value');

    // No point then
    if (!text_to_copy) {
      return false;
    }

    var textarea = document.createElement('textarea');
    textarea.value  = text_to_copy;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'absolute';
    textarea.style.left = '-9999px';

    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);

    return true;
  });


    // Manage sidebar
    $("#sidebarToggle").on("click", function () {
      $(".row-offcanvas").toggleClass("active");
      $("#overlay").toggleClass("overlay-active");
      $("#topnavbar").toggleClass("sidebar-active");
    });

    // Manage overlay
    $("#overlay").on("click", function () {
      $(".row-offcanvas").removeClass("active");
      $("#overlay").removeClass("overlay-active");
      $("#topnavbar").removeClass("sidebar-active");
    });

    // Add file name to bootstrap's custom file input fields
    $('.custom-file-input').change(function() {
      var $el = $(this),
      files = $el[0].files,
      label = files[0].name;
      if (files.length > 1) {
        label = label + " and " + String(files.length - 1) + " more files"
      }
      $el.next('.custom-file-label').html(label);
    });

    $('.has-children').hover(function () {
      $(this).children('.collapse').collapse('show');
    }, function () {
      // If its expanded by default don't close it
      if ($(this).hasClass('children-expanded-true')) {
        return false;
      }
      $(this).children('.collapse').collapse('hide');
    });


    $(document).on('change', '[data-image-preview]', function(e) {
        var el = $(this);
        var target = $(el.data('target'));
        var value = $spark.absURL(el.val());
        target.attr('src', value);
    });

  });
