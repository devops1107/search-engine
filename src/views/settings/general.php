<?php
sp_enqueue_script('dropzone-js', 2, ['dashboard-core-js']);
breadcrumb_add('dashboard.settings.general', __('General Settings'));
?>
<?php block('form-content'); ?>
<div class="form-group">
  <label class="form-label" for="site_name"><?php echo __('Site Name'); ?></label>
  <input type="text" class="form-control" name="site_name" id="site_name" value="<?php echo sp_post('site_name', get_option('site_name')); ?>" maxlength="200" required>
</div>
<div class="form-group">
  <label class="form-label" for="site_tagline"><?php echo __('Site Tagline'); ?></label>
  <input type="text" class="form-control" name="site_tagline" id="site_tagline" value="<?php echo sp_post('site_tagline', get_option('site_tagline')); ?>" maxlength="200" required>
</div>
<div class="form-group">
  <label class="form-label" for="site_description"><?php echo __('Site Description'); ?></label>
  <textarea class="form-control" name="site_description" maxlength="6000" id="site_description"><?php echo sp_post('site_description', get_option('site_description'));?></textarea>
</div>


<div class="form-group">
  <label class="form-label" for="timezone"><?php echo __('Site Timezone'); ?></label>
  <select id="timezone" name="timezone" class="form-control" required>
    <?php foreach (timezone_list() as $key => $timezone) :?>
      <option value="<?php echo$key?>" <?php selected($key, sp_post('timezone', get_option('timezone'))); ?>><?php echo$timezone?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="form-group">
  <label class="form-label" for="header_scripts"><?php echo __('Header Scripts'); ?></label>
  <textarea class="form-control" rows="4" name="header_scripts" id="header_scripts"><?php echo sp_post('header_scripts', get_option('header_scripts')); ?></textarea>
  <span class="form-text text-muted"><?php echo __('Code to be excecuted inside &lt;head&gt;...&lt;/head&gt;'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="footer_scripts"><?php echo __('Footer Scripts'); ?></label>
  <textarea class="form-control" rows="4" name="footer_scripts" id="footer_scripts"><?php echo sp_post('footer_scripts', get_option('footer_scripts')); ?></textarea>
  <span class="form-text text-muted"><?php echo __('Code to be excecuted before the ...&lt;/body&gt; tag'); ?></span>
</div>
<?php endblock(); ?>
<?php block('body_end'); ?>
<script type="text/javascript">
  $(function () {
  });
</script>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
        'title' => __('General Settings'),
        'body_class' => 'settings general-settings',
        'page_heading' => __('General Settings'),
        'page_subheading' => __('Configure the basics.'),
    ]
);
