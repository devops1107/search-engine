<?php block('content'); ?>
<form method="post" action="?" class="row" data-parsley-validate>
  <div class="col-md-8">
    <div class="px-lg-4 px-0">
        <?php echo sp_alert_flashes('pages'); ?>
        <?php echo $t['csrf_html']; ?>
      <div class="">
        <div class="form-group">
          <label class="form-label" for="content_title"><?php echo __('Page Title'); ?></label>
          <input type="text" name="content_title" id="content_title" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="content_slug"><?php echo __('Slug'); ?></label>
            <div class="input-group">
              <label class="input-group-prepend m-0" for="content_slug">
                <span class="input-group-text"><?php echo e(url_for('site.page', ['identifier' => ''])); ?></span>
            </label>
            <input type="text" class="form-control px-1" name="content_slug" id="content_slug" value="<?php echo sp_post('content_slug'); ?>" maxlength="200">
        </div>

        <small class="form-text text-muted"><?php echo __('Unique URL slug. Leave empty to generate automatically
        '); ?></small>
        </div>



        <div class="form-group">
          <textarea rows="10" name="content_body" id="content_body" class="form-control" required></textarea>
        </div>



      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header"><h3 class="card-title"><?php echo __('Page Meta'); ?></h3></div>
      <div class="card-body">

        <div class="form-group">
          <label class="form-label" for="content_meta[description]"><?php echo __('Description'); ?></label>
          <textarea type="text" name="content_meta[description]" id="content_meta[description]" class="form-control" maxlength="200"></textarea>
          <small class="form-text text-muted"><?php echo __('Page description, will be used as meta description.'); ?></small>
        </div>


        <div class="image-preview text-center mb-3">
                <img src="<?php echo e_attr(ensure_abs_url($t['image_preview'])); ?>" id="image-preview" class="img-fluid shadow rounded">
        </div>

        <div class="form-group">
            <label class="form-label" for="feat_image"><?php echo __('Page Social Image'); ?></label>
          <input type="text" name="content_meta[image]" id="feat_image" class="form-control" data-image-preview="true" data-target="#image-preview">
          <small class="form-text text-muted"><?php echo __('Will be used for social media sharing.'); ?></small>

              <?php if (current_user_can('manage_gallery')) : ?>
  <span class="form-text text-muted"><?php echo __('You may provide a URL or upload via the uploader given below.'); ?></span>
  <div id="img-uploader" class="dz my-5">
    <div class="dz-message dz-small"><strong>
                    <?php echo __('Drop  here or click to upload.'); ?></strong>
      </div>
    </div>
              <?php endif; ?>

      </div>


      </div>

      <div class="card-footer text-right">
        <button type="submit" class="btn btn-secondary ml-auto"><?php echo __('Save'); ?></button>
      </div>
    </div>
  </div>
</form>
<?php endblock(); ?>
<?php block('body_end'); ?>
<?php insert('admin::pages/partials/script.php'); ?>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Create Page'),
      'body_class' => 'pages pages-create',
      'page_heading' => __('Create Page'),
      'page_subheading' => __('Add a new page.'),
    ]
);
