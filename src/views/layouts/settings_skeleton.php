<?php block('content'); ?>
<div class="row">
  <div class="col-md-2  h-100 position-relative" id="setting-tabs">
    <?php echo sp_render_tabs($t['subsettings'], 'setting-tabs mb-2 tabs-vertical flex-column'); ?>
  </div>
  <div class="col-md-10 col-sm-10">
    <?php echo sp_alert_flashes('settings'); ?>
    <form action="<?php echo sp_current_form_uri(); ?>" enctype="multipart/form-data" method="post" class="card" id="settings-form" data-parsley-validate>
        <?php echo $t['csrf_html']?>
        <div class="card-header py-4">

        <h4 class="card-title">
        <?php echo $t['page_heading']; ?>
    </h4>
        </div>
      <div class="card-body">
        <div class="row">
            <div class="col-md-9">
        <?php section('form-content'); ?>
    </div>
    </div>
      </div>
      <div class="card-footer text-right">
          <button type="submit" class="btn btn-secondary ml-auto" id="form-submit"><?php echo $t->get('form_btn_label', __('Save Settings'))?></button>
      </div>
    </form>

  </div>
</div>
<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
  $(document).ready(function() {
    $(document).formToggle();

    var sidebar = $('#setting-tabs');

    var tabs = $('.setting-tabs');
    tabs.width(sidebar.width());

    $(window).on('resize', function(e) {
        tabs.width(sidebar.width());
    });
});
</script>
<?php endblock(); ?>
<?php
// Extends the base skeleton
extend('admin::layouts/skeleton.php', ['page_heading_classes' => 'container']);
