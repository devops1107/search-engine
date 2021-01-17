<?php defined('SPARKIN') or die('xD'); ?>
<?php block('form-content'); ?>

<h4 class="text-divider mt-0 mx-0"><span class="divider-text"><?php echo __('Homepage'); ?></span></h4>
<div class="form-group">
  <label class="form-label" for="enable_backgrounds"><?php echo __('Home Backgrounds'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="enable_backgrounds" value="0">
    <input type="checkbox" id="enable_backgrounds" name="enable_backgrounds" value="1" class="custom-switch-input"  <?php checked(1, (int) sp_post('enable_backgrounds', get_option('enable_backgrounds'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Enable Backgrounds'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Toggle background images for the homepage'); ?><br>
      <?php echo __('Note: Upload your backgrounds as <strong>jpg/png</strong> images in this directory:'); ?>
      <code>/<?php echo SITE_DIR; ?>/backgrounds/</code></span>
</div>

<div class="form-group">
  <label class="form-label" for="show_engines_in_offcanvas"><?php echo __('Show Engines in Homepage\'s Offcanvas Menu'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="show_engines_in_offcanvas" value="0">
    <input type="checkbox" id="show_engines_in_offcanvas" name="show_engines_in_offcanvas" value="1" class="custom-switch-input"  <?php checked(1, (int) sp_post('show_engines_in_offcanvas', get_option('show_engines_in_offcanvas'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Show Engines in Homepage\'s Offcanvas'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Toggle display of the engines in the offcanvas menu that is shown in the homepage.'); ?></span>
</div>
<div class="form-group">
    <label for="theme_home_max_engines_count" class="form-label" for="theme_home_max_engines_count"><?php echo __('Homepage Header Max. engines'); ?></label>
    <input type="number" name="theme_home_max_engines_count" value="<?php echo e_attr(get_option('theme_home_max_engines_count', 5)); ?>" min="0" id="theme_home_max_engines_count" class="form-control">
    <span class="form-text text-muted"><?php echo __('Maximum engines to show in the homepage navigation menu'); ?></span>
</div>
<div class="form-group">
    <label for="home_logo_align" class="form-label"><?php echo __('Homepage logo alignment'); ?></label>
    <select name="home_logo_align" id="home_logo_align" class="form-control">
        <?php foreach (['left', 'center', 'right'] as $key) : ?>
            <option value="<?php echo e_attr($key); ?>" <?php echo selected($key, sp_post('home_logo_align', get_option('home_logo_align', 'center'))); ?>><?php echo ucfirst($key); ?></option>
        <?php endforeach; ?>
    </select>
    <span class="form-text text-muted"><?php echo __('Choose logo alignment for the homepage'); ?></span>
</div>

<label class="form-label" for="home_logo_width"><?php echo __('Homepage Logo Width'); ?></label>
<div class="form-group">
<div class="input-group" style="max-width:200px">
  <input type="number" class="form-control" name="home_logo_width" id="home_logo_width" value="<?php echo sp_post('home_logo_width', get_option('home_logo_width')); ?>" min="1" maxlength="10" required>

  <div class="input-group-append">
    <span class="input-group-text">px</span>
  </div>
</div>
<p class="form-text text-muted">
    <?php echo __('CSS logo width for homepage, this should be half of your logo width. For example, if your logo is 200px width, use 100px as the width for good and sharp display'); ?>
</p>
</div>

<br>
<h4 class="text-divider mx-0"><span class="divider-text"><?php echo __('Search page'); ?></span></h4>

<div class="row">
<div class="form-group col-md-3">
    <label for="serp_link_color" class="form-label"><?php echo __('Result link color'); ?></label>
    <input type="color" name="serp_link_color" id="serp_link_color" class="border-0 p-0 rounded w-100" value="<?php echo e_attr(get_option('serp_link_color')); ?>" data-colorpicker="true" data-target="#web-result-title">
</div>
<div class="form-group col-md-3">
    <label for="serp_domain_color" class="form-label"><?php echo __('Result Domain color'); ?></label>
    <input type="color" name="serp_domain_color" id="serp_domain_color" class="border-0 p-0 rounded w-100" value="<?php echo e_attr(get_option('serp_domain_color')); ?>" data-colorpicker="true" data-target="#web-result-domain">
</div>
<div class="form-group col-md-3">
    <label for="serp_text_color" class="form-label"><?php echo __('Result text color'); ?></label>
    <input type="color" name="serp_text_color" id="serp_text_color" class="border-0 p-0 rounded w-100" value="<?php echo e_attr(get_option('serp_text_color')); ?>" data-colorpicker="true" data-target="#web-result-desc">
</div>
</div>

<div class="web-result">
    <a id="web-result-title" class="web-result-title" href="javascript:void(0);"><h3 class="web-result-title-heading">
    Demo web search result title...</h3></a>
    <div class="web-result-domain" id="web-result-domain"><img src="https://www.google.com/s2/favicons?domain=www.google.com" class="web-result-favicon">www.site.com/url/path</div>
    <p class="web-result-desc" id="web-result-desc">This is the search item description text, you can style this with your preferred color..</p>
</div>

<label class="form-label" for="search_logo_width"><?php echo __('Search Logo Width'); ?></label>
<div class="form-group">
<div class="input-group" style="max-width:200px">
  <input type="number" class="form-control" name="search_logo_width" id="search_logo_width" value="<?php echo sp_post('search_logo_width', get_option('search_logo_width')); ?>" min="1" maxlength="10" required>

  <div class="input-group-append">
    <span class="input-group-text">px</span>
  </div>
</div>
<p class="form-text text-muted">
    <?php echo __('CSS logo width for searchbar, this should be half of your logo width. For example, if your logo is 200px width, use 100px as the width for good and sharp display'); ?>
</p>
</div>
<br>
<h4 class="text-divider"><span class="divider-text"><?php echo __('Overall'); ?></span></h4>
<div class="form-group">
  <label class="form-label" for="enable_darkmode"><?php echo __('Dark Mode'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="enable_darkmode" value="0">
    <input type="checkbox" id="enable_darkmode" name="enable_darkmode" value="1" class="custom-switch-input"  <?php checked(1, (int) sp_post('enable_darkmode', get_option('enable_darkmode'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Force Dark Mode'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Force dark mode by default'); ?>
</div>
<div class="form-group">
  <label class="form-label" for="enable_ajax_nav"><?php echo __('Ajax Navigation'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="enable_ajax_nav" value="0">
    <input type="checkbox" id="enable_ajax_nav" name="enable_ajax_nav" value="1" class="custom-switch-input"  <?php checked(1, (int) sp_post('enable_ajax_nav', get_option('enable_ajax_nav'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?php echo __('Enable Ajax Navigation'); ?></span>
  </label>
  <span class="form-text text-muted"><?php echo __('Toggle ajax navigation for the site'); ?>
</div>
<div class="form-group">
    <label for="site_language" class="form-label"><?php echo __('Language'); ?></label>
    <select name="site_language" id="site_language" class="form-control">
        <?php foreach (get_theme_locales() as $key => $locale) : ?>
            <option value="<?php echo e_attr($key); ?>" <?php echo selected($key, sp_post('site_language', get_option('site_language', 'en_US'))); ?>><?php echo e($locale['name']); ?></option>
        <?php endforeach; ?>
    </select>
    <span class="form-text text-muted"><?php echo __('Default language for the site frontend. Site language may vary due to change via frontend switcher.'); ?></span>
</div>

<?php endblock(); ?>
<?php block('body_start'); ?>
<style type="text/css">
    .web-result {
  padding: 1rem 1rem;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  margin-bottom: 1.2rem;
  overflow: hidden; }
  .web-result .web-thumb-img {
    float: left;
    margin-right: 1rem;
    object-fit: cover;
    width: 140px;
    height: 84px;
    max-width: 140px;
    border-radius: 4px; }

    .web-result .web-result-title {
        color: <?php echo e_attr(get_option('serp_link_color')); ?>
    }

  .web-result .web-result-title-heading {
    margin: 0 0 .3rem;
    padding: 0;
    font-size: 1rem;
    line-height: 1rem; }
  .web-result .web-result-domain {
    padding: .2rem 0;
    white-space: nowrap;
    overflow: hidden;
    color: <?php echo e_attr(get_option('serp_domain_color')); ?>;
    text-overflow: ellipsis; }
  .web-result .web-result-desc {
    margin: .3rem 0 0;
    color: <?php echo e_attr(get_option('serp_text_color')); ?>;
    font-size: 0.9rem; }
  .web-result .web-result-favicon {
    margin-right: .3rem;
    display: inline-block; }
</style>
<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $(document).on('input', '[data-colorpicker]', function(event) {
            var el = $(this);

            $(el.data('target')).css('color', el.val());
        });
    });
</script>
<?php endblock(); ?>
<?php
// Extends the plugins options skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
        'title'           => __('Appearance'),
        'body_class'      => 'settings appearance-settings',
        'page_heading'    => __('Appearance'),
        'page_subheading' => __('Customize the appearance'),
    ]
);
