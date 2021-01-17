<?php block('content'); ?>
<div class="row">
  <div class="container col-login">
    <?php echo sp_alert_flashes('themes'); ?>
    <div class="py-5 px-4 text-center">
      <?php echo svg_icon('color-palette', 'text-muted', ['style' => 'height:5rem;width:5rem']); ?>
    </div>
    <form method="post" action="?" enctype="multipart/form-data" class="card">
        <?php echo $t['csrf_html']?>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label" for="theme_archive"><?php echo __('Choose Theme Package'); ?></label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" name="theme_archive" id="theme_archive" accept="application/zip" required>
            <label class="custom-file-label"><?php echo __('Choose file'); ?></label>
          </div>
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-block btn-secondary ml-auto"><?php echo __('Install')?></button>
      </div>
    </form>
  </div>
</div>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
    'title' => __('Add New Theme'),
    'body_class' => 'themes themes-create',
    'page_heading' => __('Add New Theme'),
    'page_subheading' => __('Upload a Theme.'),
    'page_heading_classes' => 'container'
    ]
);
