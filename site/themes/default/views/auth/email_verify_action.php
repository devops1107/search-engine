<?php
/**
 * Email verification action template
 *
 * This file contains the template that is shown when user clicks the email verification link
 */
defined('SPARKIN') or die('xD');
?>

<div class="row no-gutters auth-wrap">
    <div class="auth-sidebar-left col-sm-5 d-flex justify-content-center flex-column h-100">

        <div class="container auth-form-container">

            <div class="card shadow-none">

                <div class="card-body">
                   <div class="auth-logo-wrap">
                    <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link auth-logo-link">
                        <img src="<?php echo e_attr($t['auth_logo_url']); ?>" class="auth-logo" alt="<?php echo e_attr(get_option('site_name')); ?>">
                    </a>
                </div>

                <?php echo sp_alert_flashes('account', true, false); ?>

                <?php if ($t['invalid']) : ?>
                    <h3 class="auth-heading"><?php echo __('email-link-expired-heading', _T); ?></h3>
                    <p class="auth-help"><?php echo __('email-link-expired', _T); ?></p>
                    <div class="form-group text-right">
                        <a href="<?php echo e_attr(url_for('auth.activation')); ?>" class="sp-link btn btn-link"><?php echo __("request", _T); ?></a>
                    </div>

                <?php else : ?>
                    <h3 class="auth-heading"><?php echo __('email-activation-heading', _T); ?></h3>
                    <p class="auth-help"><?php echo __('thanks-for-email-verify', _T); ?></p>
                       <div class="form-group text-right">
                        <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link btn btn-link"><?php echo __("homepage", _T); ?></a>
                    </div>
                <?php endif; ?>

                <p class="d-sm-none py-3">
                    <?php if (is_logged()) : ?>
                        <?php echo __("done-heading", _T); ?> <?php echo __("go-back-to", _T); ?>
                        <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link"><?php echo __("homepage", _T); ?></a>
                    <?php else : ?>
                        <?php echo __("already-have-an-account", _T); ?>
                        <a href="<?php echo e_attr(url_for('auth.signin') . $t['query_string']);  ?>" class="sp-link"><?php echo __("sign-in-now", _T); ?></a>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    <!-- ./card -->
</div>

<div class="col-sm-7 auth-sidebar-right">
    <?php if (is_logged()) : ?>
     <h3 class="auth-heading"><?php echo __("done-heading", _T); ?></h3>
     <h6 class="auth-subheading"><?php echo __("go-back-to", _T); ?></h6>
     <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link btn btn-outline-dark btn-lg"><?php echo __("homepage", _T); ?></a>
    <?php else : ?>
   <h3 class="auth-heading"><?php echo __("already-have-an-account", _T); ?></h3>
   <h6 class="auth-subheading"><?php echo __("already-have-an-account-subtitle", _T); ?></h6>
   <a href="<?php echo e_attr(url_for('auth.signin') . $t['query_string']);  ?>" class="sp-link btn btn-outline-dark btn-lg"><?php echo __("sign-in-now", _T); ?></a>
    <?php endif; ?>
</div>
</div>
<!-- ./container -->
