<?php

sp_enqueue_script('dropzone-js', 2, ['dashboard-core-js']);
breadcrumb_add('dashboard.settings.site', __('Site Settings')); ?>

<?php block('form-content'); ?>

    <h4 class="text-divider mt-0"><span class="divider-text"><?php echo __('Logo and Images'); ?></span></h4>
<div class="form-group">

  <label class="form-label" for="site_logo">

    <?php echo __('Site Logo'); ?>


    </label>


  <div class="img-preview shadow rounded p-1 text-center my-2 bg-light" style="max-width:200px">
   <img src="<?php echo e_attr(ensure_abs_url(sp_logo_uri())); ?>" id="logo-preview" class="img-fluid rounded">
</div>


  <input type="text" class="form-control" name="site_logo" id="site_logo" value="<?php echo sp_post('site_logo', get_option('site_logo')); ?>" data-image-preview="true" data-target="#logo-preview" required>
    <?php if (current_user_can('manage_gallery')) : ?>
  <span class="form-text text-muted"><?php echo __('You may provide a URL or upload via the uploader given below.'); ?></span>
  <div id="logo-uploader" class="dz my-5">
    <div class="dz-message dz-small"><strong>
        <?php echo __('Drop logo here or click to upload.'); ?></strong>
      </div>
    </div>
    <?php endif; ?>
</div>
<div class="form-group">

  <label class="form-label" for="dark_logo">

    <?php echo __('Site Logo (For Dark Background)'); ?>


    </label>


  <div class="img-preview shadow rounded p-1 text-center my-2 bg-dark" style="max-width:200px">
   <img src="<?php echo e_attr(ensure_abs_url(get_option('dark_logo'))); ?>" id="dark-logo-preview" class="img-fluid rounded">
</div>


  <input type="text" class="form-control" name="dark_logo" id="dark_logo" value="<?php echo sp_post('dark-logo', get_option('dark_logo')); ?>" data-image-preview="true" data-target="#dark-logo-preview" required>
    <?php if (current_user_can('manage_gallery')) : ?>
  <span class="form-text text-muted"><?php echo __('You may provide a URL or upload via the uploader given below.'); ?></span>
  <div id="dark-logo-uploader" class="dz my-5">
    <div class="dz-message dz-small"><strong>
        <?php echo __('Drop logo here or click to upload.'); ?></strong>
      </div>
    </div>
    <?php endif; ?>
</div>

<div class="form-group">
    <label class="form-label" for="search_logo">
    <?php echo __('Search Logo'); ?>
    </label>
  <div class="img-preview shadow rounded p-1 text-center my-2 bg-light" style="max-width:200px">
   <img src="<?php echo e_attr(ensure_abs_url(get_option('search_logo'))); ?>" id="search-logo-preview" class="img-fluid rounded">
</div>


  <input type="text" class="form-control" name="search_logo" id="search_logo" value="<?php echo sp_post('search-logo', get_option('search_logo')); ?>" data-image-preview="true" data-target="#search-logo-preview" required>
    <?php if (current_user_can('manage_gallery')) : ?>
  <span class="form-text text-muted"><?php echo __('You may provide a URL or upload via the uploader given below.'); ?></span>
  <div id="search-logo-uploader" class="dz my-5">
    <div class="dz-message dz-small"><strong>
        <?php echo __('Drop logo here or click to upload.'); ?></strong>
      </div>
    </div>
    <?php endif; ?>

</div>
<div class="form-group">
    <label class="form-label" for="search_logo_dark">
    <?php echo __('Search Logo (For Dark Background)'); ?>
    </label>
  <div class="img-preview shadow rounded p-1 text-center my-2 bg-dark" style="max-width:200px">
   <img src="<?php echo e_attr(ensure_abs_url(get_option('search_logo_dark'))); ?>" id="search-logo-dark-preview" class="img-fluid rounded">
</div>


  <input type="text" class="form-control" name="search_logo_dark" id="search_logo_dark" value="<?php echo sp_post('search-logo', get_option('search_logo_dark')); ?>" data-image-preview="true" data-target="#search-logo-dark-preview" required>
    <?php if (current_user_can('manage_gallery')) : ?>
  <span class="form-text text-muted"><?php echo __('You may provide a URL or upload via the uploader given below.'); ?></span>
  <div id="search-logo-dark-uploader" class="dz my-5">
    <div class="dz-message dz-small"><strong>
        <?php echo __('Drop logo here or click to upload.'); ?></strong>
      </div>
    </div>
    <?php endif; ?>

</div>


<div class="form-group">
  <label class="form-label" for="site_favicon">
    <?php echo __('Site Favicon'); ?>
    </label>

  <div class="img-preview shadow rounded p-1 text-center my-2 bg-light d-flex align-items-center justify-content-center" style="width:50px;height:50px">
   <img src="<?php echo e_attr(ensure_abs_url(get_option('site_favicon'))); ?>" id="favicon-preview" class="img-fluid rounded">
</div>

  <input type="text" class="form-control" name="site_favicon" id="site_favicon" value="<?php echo sp_post('site_favicon', get_option('site_favicon')); ?>" data-image-preview="true" data-target="#favicon-preview" required>
    <?php if (current_user_can('manage_gallery')) : ?>
  <span class="form-text text-muted"><?php echo __('You may provide a URL or upload via the uploader given below.'); ?></span>
  <div id="favicon-uploader" class="dz my-5">
    <div class="dz-message dz-small"><strong>
        <?php echo __('Drop favicon here or click to upload.'); ?></strong>
      </div>
    </div>
    <?php endif; ?>
</div>

<div class="form-group">
  <label class="form-label" for="opengraph_image"><?php echo __('Site Social Image'); ?>
  </label>

  <div class="img-preview shadow rounded p-1 text-center my-2 bg-light" style="max-width:500px">
   <img src="<?php echo e_attr(ensure_abs_url(get_option('opengraph_image'))); ?>" id="og-preview" class="img-fluid rounded">
</div>
  <input type="text" class="form-control" name="opengraph_image" id="opengraph_image" value="<?php echo sp_post('opengraph_image', get_option('opengraph_image')); ?>" data-image-preview="true" data-target="#og-preview" required>
    <?php if (current_user_can('manage_gallery')) : ?>
  <span class="form-text text-muted"><?php echo __('You may provide a URL or upload via the uploader given below.'); ?></span>
  <div id="og-uploader" class="dz my-5">
    <div class="dz-message dz-small"><strong>
        <?php echo __('Drop image here or click to upload.'); ?></strong>
      </div>
    </div>
    <?php endif; ?>
</div>

<br>
<h4 class="text-divider"><span class="divider-text"><?php echo __('Sitemap'); ?></span></h4>
<div class="form-group">
  <label class="form-label" for="sitemap_links_per_page"><?php echo __('Links per page'); ?></label>
  <input type="number" class="form-control" name="sitemap_links_per_page" id="sitemap_links_per_page" value="<?php echo sp_post('sitemap_links_per_page', get_option('sitemap_links_per_page')); ?>" min="1" maxlength="10" required>
  <span class="form-text text-muted"><?php echo __('Number of links per page in sitemap'); ?></span>
</div>

<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
  $(function () {
    <?php echo sp_dropzone_js('#logo-uploader', '#site_logo'); ?>
    <?php echo sp_dropzone_js('#dark-logo-uploader', '#dark_logo'); ?>
    <?php echo sp_dropzone_js('#search-logo-dark-uploader', '#search_logo_dark'); ?>
    <?php echo sp_dropzone_js('#favicon-uploader', '#site_favicon'); ?>
    <?php echo sp_dropzone_js('#og-uploader', '#opengraph_image'); ?>
    <?php echo sp_dropzone_js('#search-logo-uploader', '#search_logo'); ?>
  });
</script>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
    'title' => __('Site Settings'),
    'body_class' => 'settings site-settings',
    'page_heading' => __('Site Settings'),
    'page_subheading' => __("Main site settings"),
    ]
);
