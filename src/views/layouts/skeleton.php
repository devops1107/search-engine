<?php
/**
 * Base Skeleton for Admin Dashboard
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
    <?php section('body_start'); ?>
    <?php insert('admin::partials/svg_sprites.svg'); ?>
    <?php insert('admin::partials/overlays.php'); ?>
    <?php insert('admin::partials/navbar.php'); ?>
    <!-- Start Body Wrapper -->
    <div id="wrapper" class="row-offcanvas">

        <!-- Start Sidebar -->
        <div id="sidebar" class="">
            <?php insert('admin::partials/sidebar.php'); ?>
        </div>
        <!-- #/sidebar -->

        <!-- Content -->
        <div id="content">

            <!-- Content overlay -->
            <div id="overlay"></div>

            <!-- Body Content -->
            <div id="body-content">

<?php if (is_admin() && $t['updates.available']) : ?>
    <div class="px-lg-4 px-0 py-2">
        <div class="alert alert-light bg-white shadow border-primary" role="alert" style="border:0;border-left:4px solid">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
          <span class="lead"><?php echo __('Update available!'); ?></span><br>
          A new version of <strong><?php echo APP_NAME ?></strong> (<em><?php echo $t['updates.latest_version'] . ' ' . $t['updates.latest_version_codename']; ?></em>) was released <?php echo time_ago($t['updates.updated_at']); ?>. Please download and migrate to the latest version as soon as possible.
          <hr>
          <a href="<?php echo e_attr(!empty($t['updates.download_uri']) ? $t['updates.download_uri'] : $t['updates.download_page']); ?>" class="btn btn-sm btn-primary" target="_blank">Download</a>
      </div>
  </div>
<?php endif; ?>

<?php if ($t['parent_tabs_key']) : ?>
    <?php echo sp_render_tabs($t['parent_tabs_key'], 'mb-2 flex-column flex-lg-row d-none d-md-flex px-1 mx-2'); ?>
<?php endif; ?>

                <!-- Page Content -->
                <div class="container-fluid content-section <?php echo e_attr($t->get('content_section_classes', 'px-lg-5 pt-3')); ?>">
                    <?php echo sp_alert_flashes('dashboard'); ?>
                    <div id="global-xhr-response"></div>
                    <?php if (!(int)current_user_field('is_verified') && config('auth.force_email_verification')) : ?>
                        <?php echo
                        sp_bootstrap_alert(
                            sprintf(
                                __('It appears you haven\'t verfied your e-mail yet. Please check your e-mail inbox for a verification e-mail. To request a new one <a href="%s" class="btn btn-sm btn-white">click here</a>'),
                                e_attr(url_for('dashboard.account.activation') . '?redirect_to=' . urlencode(get_current_route_uri()))
                            ),
                            'primary',
                            svg_icon('notifications')
                        );
                        ?>
                    <?php endif; ?>
                    <?php

                    ?>
                    <?php section('content', __('No content block found')); ?>
                    <?php

                    ?>

                </div>
                <!-- ./content-section -->


            </div>
            <!-- #/body-content -->

        </div>
        <!-- #/content -->




        <?php insert('admin::partials/footer.php'); ?>
    </div>
    <!-- #/wrapper -->

    <?php sp_footer(); ?>
    <?php section('body_end'); ?>
    <?php insert('admin::partials/html_foot.php'); ?>
</body>
</html>
