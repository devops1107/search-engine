<script type="text/javascript">
    // Global data for the application
    window.app = <?php echo json_encode($t['window_js_app'], JSON_UNESCAPED_SLASHES); ?>;

    // Basic translation strings required by the js components
    window.locale = <?php echo json_encode($t['window_js_locale'], JSON_UNESCAPED_SLASHES); ?>;

    function localizeNumbers(text) {
        text = text.toString();
        for (var counter = 0; counter < 10; counter++) {
            var key = "num_" + counter;
            text = text.replace(new RegExp(counter, 'g'), window.locale[key]);
        }
        return text;
    };
</script>
<style type="text/css">
    .web-result .web-result-title {
        color: <?php echo e_attr(get_option('serp_link_color')); ?>;
    }
    .web-result .web-result-domain {
        color: <?php echo e_attr(get_option('serp_domain_color')); ?>;
    }
    .web-result .web-result-desc {
        color: <?php echo e_attr(get_option('serp_text_color')); ?>;
    }

    .home-logo-wrap .home-logo {
        max-width: <?php echo (int) get_option('home_logo_width'); ?>px;
    }

    .search-logo {
        max-width: <?php echo (int) get_option('search_logo_width'); ?>px;
    }

</style>
