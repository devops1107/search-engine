<?php breadcrumb_add('dashboard.settings.ads', __('Ads Settings')); ?>

<?php block('form-content'); ?>

<div class="form-group">
  <label class="form-label" for="ad_unit_1"><?php echo __('Search results before ad slot'); ?></label>
  <textarea class="form-control" rows="4" name="ad_unit_1" id="ad_unit_1"><?php echo sp_post('ad_unit_1', get_option('ad_unit_1')); ?></textarea>
  <span class="form-text text-muted"><?php echo __('This ad code will be shown before the search results start'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="ad_unit_2"><?php echo __('Search results after ad slot'); ?></label>
  <textarea class="form-control" rows="4" name="ad_unit_2" id="ad_unit_2"><?php echo sp_post('ad_unit_2', get_option('ad_unit_2')); ?></textarea>
  <span class="form-text text-muted"><?php echo __('This ad code will be shown after the search results end'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="ad_unit_3"><?php echo __('Search sidebar ad slot'); ?></label>
  <textarea class="form-control" rows="4" name="ad_unit_3" id="ad_unit_3"><?php echo sp_post('ad_unit_3', get_option('ad_unit_3')); ?></textarea>
  <span class="form-text text-muted"><?php echo __('This ad code will be shown on search sidebar (Desktop only)'); ?></span>
</div>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
    'title' => __('Advertisement Settings'),
    'body_class' => 'settings ads-settings',
    'page_heading' => __('Advertisement Settings'),
    'page_subheading' => __("Good ol' ad slots"),
    ]
);
