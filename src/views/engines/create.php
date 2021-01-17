<?php block('content'); ?>
<div class="row">
  <div class="container">
    <?php echo sp_alert_flashes('engines'); ?>
    <form method="post" action="?" class="card" data-parsley-validate>
        <?php echo $t['csrf_html']?>
      <div class="card-body">

            <div class="form-group">
            <label class="form-label" for="engine_name"><?php echo __('Engine Name'); ?></label>
            <input type="text" name="engine_name" id="engine_name" class="form-control" value="<?php echo sp_post('engine_name'); ?>" maxlength="200" required>
            <div class="form-text text-muted">
                <?php echo __('The name for the engine'); ?>
            </div>
            </div>
            <div class="form-group">
            <label class="form-label" for="engine_cse_id"><?php echo __('Google CSE ID'); ?></label>
            <input type="text" name="engine_cse_id" id="engine_cse_id" class="form-control" value="<?php echo sp_post('engine_cse_id'); ?>" maxlength="200" required>
            <div class="form-text text-muted">
                <?php echo __('Google CSE ID for this engine.'); ?>
            </div>
            </div>
        <div class="form-group">
          <label class="form-label" for="engine_is_image"><?php echo __('Image search'); ?></label>
          <label class="custom-switch mt-3">
            <input type="hidden" name="engine_is_image" value="0">
            <input type="checkbox" id="engine_is_image" name="engine_is_image" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('engine_is_image', '0')); ?>>
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description"> <?php echo __('Image search'); ?></span>
          </label>
          <span class="form-text text-muted"><?php echo __('Choose if the result type is images or not'); ?></span>
        </div>
        <div class="form-group">
          <label class="form-label" for="engine_show_thumb"><?php echo __('Thumbnails'); ?></label>
          <label class="custom-switch mt-3">
            <input type="hidden" name="engine_show_thumb" value="0">
            <input type="checkbox" id="engine_show_thumb" name="engine_show_thumb" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('engine_show_thumb', '0')); ?>>
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description"> <?php echo __('Show Thumbnails'); ?></span>
          </label>
          <span class="form-text text-muted"><?php echo __('Choose if thumbnails will be shown, when available (web results only)'); ?></span>
        </div>
        <div class="form-group">
          <label class="form-label" for="engine_show_ads"><?php echo __('Ads'); ?></label>
          <label class="custom-switch mt-3">
            <input type="hidden" name="engine_show_ads" value="0">
            <input type="checkbox" id="engine_show_ads" name="engine_show_ads" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('engine_show_ads', '0')); ?>>
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description"> <?php echo __('Display Search Ads'); ?></span>
          </label>
          <span class="form-text text-muted"><?php echo __('Choose whether to show Google CSE ads for this engine.'); ?></span>
        </div>
        <!--<div class="form-group">
          <label class="form-label" for="engine_log_search"><?php echo __('Log Search'); ?></label>
          <label class="custom-switch mt-3">
            <input type="hidden" name="engine_log_search" value="0">
            <input type="checkbox" id="engine_log_search" name="engine_log_search" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('engine_log_search', '0')); ?>>
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description"> <?php echo __('Enable Search Logging'); ?></span>
          </label>
          <span class="form-text text-muted"><?php echo __('Choose to log the search history for this engine, overrides global setting.'); ?></span>
        </div>-->
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-secondary ml-auto"><?php echo __('Create')?></button>
      </div>
    </form>
  </div>
</div>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Create Engine'),
      'body_class' => 'engines engines-create',
      'page_heading' => __('Create Engine'),
      'page_subheading' => __('Add a new engine.'),
    ]
);
