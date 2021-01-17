<?php block('content'); ?>
<div class="row row-cards">
<?php foreach ($t['cards'] as $t['card']) : ?>
      <div class="col-6 col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body p-3 text-center d-flex justify-content-center">
          <div class="d-flex align-items-center text-center w-100">
            <span class="stamp stamp-md <?php echo e_attr($t['card.bg_class']); ?> mr-3">
              <?php echo svg_icon($t['card.icon'], 'svg-md'); ?>
          </span>
          <div style="flex:1">
            <h1 class="m-0 h3"><?php echo $t['card.count']; ?></h1>
            <small class="text-muted"><?php echo $t['card.label']; ?></small>
          </div>
          </div>
      </div>
  </div>
</div>
<?php endforeach; ?>
</div>



<div class="row row-cards row-deck">
      <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">
        <?php echo __('Welcome'); ?>
    </h4>
      </div>
      <div class="card-body">

        <?php echo __('Thanks for choosing %app%.<br>You are currently running version <strong>%version%</strong>', null, ['app' => APP_NAME, 'version' => APP_VERSION]); ?>
      </div>
    </div>
</div>

<?php if ($t['mod_security']) : ?>
  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">
            <?php echo __('Warning'); ?>
        </h4>
    </div>
    <div class="card-body">
        <div class="alert alert-danger">
            <?php echo __('Your server has <strong>ModSecurity</strong> enabled. You won\'t be able to save Ad codes or header/footer scripts because of this. Please disable <strong>ModSecurity</strong> for your domain or ask your hosting provider to do it for you.'); ?>
        </div>
    </div>
</div>
</div>
<?php endif; ?>

  <?php if (current_user_can('change_settings') && $t['cron_job_needed']) : ?>
  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">
        <?php echo __('Setup Cron Job'); ?>
    </h4>
      </div>
      <div class="card-body">
        <?php echo __('Dont forget to set-up cron jobs otherwise automated actions won\'t work.'); ?>
      </div>
      <div class="card-footer text-right">
        <a href="<?php echo e_attr(url_for('dashboard.settings', ['type' => 'debug'])); ?>" class="btn btn-primary"><?php echo __('Go now'); ?></a>
      </div>
    </div>
  </div>

  <?php endif; ?>
    <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-header">

        <h4 class="card-title">
        <?php echo __('Sitemap'); ?>
    </h4>
      </div>
      <div class="card-body">
        <p>
        <?php echo __('Sitemap URL:'); ?> <a href="<?php echo e_attr(url_for('sitemap.index')); ?>"><?php echo e_attr(url_for('sitemap.index')); ?></a>
    </p>
      </div>
    </div>
  </div>

</div>
<?php endblock(); ?>

<?php
// Extends the base skeleton
extend(
    'admin::layouts/skeleton.php',
    [
        'title' => __('Dashboard'),
        'body_class' => 'dashboard dashboard-index',
        'page_heading' => __('Dashboard'),
        'page_subheading' => __('Site overview'),
    ]
);
