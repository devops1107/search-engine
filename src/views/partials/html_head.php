
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge,chrome=1">
    <meta name="theme-color" content="#007BFF">

    <?php sp_head(); ?>
    <?php section('html_head'); ?>

    <script type="text/javascript">
        var base_uri = "<?php echo base_uri()?>";
        var current_route_uri = "<?php echo js_string(get_current_route_uri()) ?>";
        var csrf_token = "<?php echo $t['csrf_token'] ?>";
        var csrf_key = "<?php echo $t['csrf_key'] ?>";
        var csrf_token_amp = "&<?php echo $t['csrf_key']?>=<?php echo $t['csrf_token'] ?>";
        var spark_i18n = {
            ajax_err_title: "<?php echo js_string(__('Ajax Error')); ?>",
            ajax_err_desc: "<?php echo js_string(__('Failed to communicate to server via AJAX. Please check your internet connection or reload this page and try again.')); ?>",
            okay: "<?php echo js_string(__('Okay')); ?>",
            cancel: "<?php echo js_string(__('Cancel')); ?>",
            confirm: "<?php echo js_string(__('Confirm')); ?>",
        };
    </script>
    <?php echo breadcrumb_render_json(); ?>
