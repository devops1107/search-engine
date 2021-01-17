<?php

breadcrumb_add('dashboard.settings.Search', __('Search Settings'));
?>

<?php block('form-content'); ?>
<div class="form-group">
  <label class="form-label" for="search_log"><?php echo __('Log Searches'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="search_log" value="0">
    <input type="checkbox" id="search_log" name="search_log" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('search_log', get_option('search_log'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Enable search logging'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Log searches performed by users.'); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="show_entities"><?php echo __('Information Card'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="show_entities" value="0">
    <input type="checkbox" id="show_entities" name="show_entities" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('show_entities', get_option('show_entities'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Show Rich Information Card'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('If enabled it will show detailed information card about an entity when available'); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="show_answers"><?php echo __('Instant Answers'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="show_answers" value="0">
    <input type="checkbox" id="show_answers" name="show_answers" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('show_answers', get_option('show_answers'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Show Instant Answers'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('If enabled it will show short answers to queries if available'); ?></span>
</div>


<div class="form-group">
  <label class="form-label" for="search_autocomplete"><?php echo __('Search autocomplete'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="search_autocomplete" value="0">
    <input type="checkbox" id="search_autocomplete" name="search_autocomplete" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('search_autocomplete', get_option('search_autocomplete'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Show search suggestions as the user types.'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Can be overwritten by the user.'); ?></span>
</div>


<div class="form-group">
  <label class="form-label" for="search_links_newwindow"><?php echo __('Where results open'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="search_links_newwindow" value="0">
    <input type="checkbox" id="search_links_newwindow" name="search_links_newwindow" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('search_links_newwindow', get_option('search_links_newwindow'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Open each selected result in a new browser window. '); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Can be overwritten by the user.'); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="search_items_count"><?php echo __('Web results per page'); ?></label>
  <input type="number" class="form-control" name="search_items_count" id="search_items_count" value="<?php echo sp_post('search_items_count', get_option('search_items_count')); ?>" min="1" max="20" required>
  <span class="form-text text-muted"><?php echo __('Number of web results per page. Max. 20 as limited by Google.'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="image_search_items_count"><?php echo __('Image results per page'); ?></label>
  <input type="number" class="form-control" name="image_search_items_count" id="image_search_items_count" value="<?php echo sp_post('image_search_items_count', get_option('image_search_items_count')); ?>" min="1" max="20" required>
  <span class="form-text text-muted"><?php echo __('Number of image results per page. Max. 20 as limited by Google.'); ?></span>
</div>

<div class="form-group">
    <label class="form-label" for="safesearch_status"><?php echo __('Safesearch'); ?></label>
    <select class="form-control" id="safesearch_status" name="safesearch_status">
        <?php foreach ($t['safesearch'] as $type) : ?>
            <option value="<?php echo e_attr($type); ?>" <?php echo selected($type, get_option('safesearch_status')); ?>><?php echo ucfirst($type); ?></option>
        <?php endforeach; ?>
    </select>

    <span class="form-text text-muted"><?php echo __('Default safesearch status. Can be overwritten by the user.'); ?></span>
</div>


<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
    'title' => __('Search Settings'),
    'body_class' => 'settings search-settings',
    'page_heading' => __('Search Settings'),
    'page_subheading' => __("Search results settings"),
    ]
);
