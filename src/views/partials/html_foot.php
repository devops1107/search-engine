
    <script type="text/javascript">
    <?php if (sp_is_enqueued('parsley')) : ?>
          $("form").parsley(parsleyOptions);
    <?php endif; ?>

    $(function () {
        if(!('ontouchstart' in window)) {
            $('[data-toggle="tooltip"]').tooltip({boundary: 'window'});
        }
        // Enable Popovers
        $('[data-toggle="popover"]').popover();
    });
    </script>
