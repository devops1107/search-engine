<?php
/**
 * Base Skeleton for Admin Dashboard's non logged pages
 *
 * @view \weed\admin
 */
?>
<!doctype html>
<html>
<head>
    <?php insert('admin::partials/html_head.php'); ?>
</head>
<body class="<?php echo e_attr($t['body_class']); ?>">
    <?php insert('admin::partials/svg_sprites.svg'); ?>
    <?php insert('admin::partials/overlays.php'); ?>
<div class="page">
      <div class="page-single">
        <div class="container">
          <div class="row">
            <div class="col col-login mx-auto"><div class="auth-wrap px-6">

              <form class="mb-3 card" action="<?php echo sp_current_form_uri(); ?>" method="post" id="account-form" data-parsley-validate>
                <?php echo $t['csrf_html']; ?>
                <div class="card-body">

              <div class="py-1 mb-2">
                <a href="<?php echo e_attr(url_for('site.home')); ?>">
                <img src="<?php echo e_attr(sp_logo_uri()); ?>" style="width:100px;height:auto" alt="<?php echo e_attr(get_option('site_name')); ?>">
              </a>
              </div>

                <?php echo sp_alert_flashes('account', true, false); ?>

                  <div class="card-title"><?php echo $t['form_heading']; ?></div>
                    <?php section('form_content'); ?>
                </div>
              </form>

              <div class="px-4 py-2 text-center">
                <?php section('form-after'); ?>
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- .page -->

    <?php sp_footer(); ?>
    <?php section('body_end'); ?>
    <?php insert('admin::partials/html_foot.php'); ?>
</body>
</html>
