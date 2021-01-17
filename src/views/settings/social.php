<?php breadcrumb_add('dashboard.settings.social', __('Social Settings')); ?>

<?php block('form-content'); ?>
<label class="form-label" for="facebook_username"><?php echo __('Facebook Username'); ?></label>

<div class="form-group">
<div class="input-group">
  <div class="input-group-prepend">
    <span class="input-group-text">facebook.com/</span>
  </div>
  <input type="text" class="form-control" name="facebook_username" id="facebook_username" value="<?php echo sp_post('facebook_username', get_option('facebook_username')); ?>" maxlength="200">
</div>
<p class="form-text text-muted">
    <?php echo __('Username for the facebook page/brand/profile of this site'); ?>
</p>
</div>
<div class="form-group">
<label class="form-label" for="twitter_username"><?php echo __('Twitter Username'); ?></label>
<div class="input-group">
  <div class="input-group-prepend">
    <span class="input-group-text">twitter.com/</span>
  </div>
  <input type="text" class="form-control" name="twitter_username" id="twitter_username" value="<?php echo sp_post('twitter_username', get_option('twitter_username')); ?>" maxlength="200">
</div>
<p class="form-text text-muted">
    <?php echo __('Username for the twitter page/brand/profile of this site'); ?>
</p>
</div>
<div class="form-group">
<label class="form-label" for="youtube_username"><?php echo __('YouTube Username'); ?></label>
<div class="input-group">
  <div class="input-group-prepend">
    <span class="input-group-text">youtube.com/user/</span>
  </div>
  <input type="text" class="form-control" name="youtube_username" id="youtube_username" value="<?php echo sp_post('youtube_username', get_option('youtube_username')); ?>" maxlength="200">
</div>
<p class="form-text text-muted">
    <?php echo __('Username for the YouTube channel of this site'); ?>
</p>
</div>
<div class="form-group">
<label class="form-label" for="instagram_username"><?php echo __('Instagram Username'); ?></label>
<div class="input-group">
  <div class="input-group-prepend">
    <span class="input-group-text">instagram.com/</span>
  </div>
  <input type="text" class="form-control" name="instagram_username" id="instagram_username" value="<?php echo sp_post('instagram_username', get_option('instagram_username')); ?>" maxlength="200">
</div>
<p class="form-text text-muted">
    <?php echo __('Username for the instagram page/brand/profile of this site'); ?>
</p>
</div>
<div class="form-group">
<label class="form-label" for="linkedin_username"><?php echo __('Linkedin Username'); ?></label>
<div class="input-group">
  <div class="input-group-prepend">
    <span class="input-group-text">linkedin.com/in/</span>
  </div>
  <input type="text" class="form-control" name="linkedin_username" id="linkedin_username" value="<?php echo sp_post('linkedin_username', get_option('linkedin_username')); ?>" maxlength="200">
</div>
<p class="form-text text-muted">
    <?php echo __('Username for the linkedin page/profile of this site'); ?>
</p>
</div>
<div class="form-group">
<label class="form-label" for="vk_username"><?php echo __('VK Username'); ?></label>
<div class="input-group">
  <div class="input-group-prepend">
    <span class="input-group-text">vk.com/</span>
  </div>
  <input type="text" class="form-control" name="vk_username" id="vk_username" value="<?php echo sp_post('vk_username', get_option('vk_username')); ?>" maxlength="200">
</div>
  <p class="form-text text-muted">
    <?php echo __('Username for the VK page/brand/profile of this site'); ?>
</p>
</div>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
    'title' => __('Social Settings'),
    'body_class' => 'settings social-settings',
    'page_heading' => __('Social Settings'),
    'page_subheading' => __("Because we live in a society"),
    ]
);
