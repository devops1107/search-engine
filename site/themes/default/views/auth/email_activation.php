<?php
/**
 * Reset password page template
 *
 * This file contains the reset page of forgot password
 */
defined('SPARKIN') or die('xD');
?>

<div class="row no-gutters auth-wrap">
    <div class="auth-sidebar-left col-sm-5 d-flex justify-content-center flex-column h-100">

        <div class="container auth-form-container">
            <form
            class="card shadow-none"
            method="POST"    action="<?= e_attr(url_for('auth.activation_post')); ?><?php echo $t['redirect_to_query']; ?>"
            data-recaptcha-id="auth-captcha"
            data-ajax-form="true"
            data-reset="true"
            data-parsley-validate>
                    <?php echo $t['csrf_html'] . $t['honeypot_html']; ?>

            <div class="card-body">
             <div class="auth-logo-wrap">
                <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link auth-logo-link">
                    <img src="<?php echo e_attr($t['auth_logo_url']); ?>" class="auth-logo" alt="<?php echo e_attr(get_option('site_name')); ?>">
                </a>
            </div>


            <h3 class="auth-heading"><?php echo __('email-activation-heading', _T); ?></h3>

            <p class="auth-help">
                <?php echo __('email-activation-help', _T, ['email' => $t['user.email']]); ?>
            </p>



            <?php echo sp_alert_flashes('account', true, false); ?>

            <?php echo sp_google_recaptcha('auth.activation', '<div class="recaptcha-container">', '</div>', false, [], 'auth-captcha');?>


        <div class="form-group text-right">
            <button type="submit" class="btn btn-link">
                <span class="btn-text"><?php echo __("request", _T); ?></span>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>

        <p class="d-sm-none py-3 auth-help-footer">
            <?php echo __("done-heading", _T); ?> <?php echo __("go-back-to", _T); ?>
            <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link"><?php echo __("homepage", _T); ?></a>
        </p>
</div>
</div>
</form>
<!-- ./card -->
</div>

<div class="col-sm-7 auth-sidebar-right">
       <h3 class="auth-heading"><?php echo __("done-heading", _T); ?></h3>
       <h6 class="auth-subheading"><?php echo __("go-back-to", _T); ?></h6>
       <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link btn btn-outline-dark btn-lg"><?php echo __("homepage", _T); ?></a>
</div>
</div>
<!-- ./container -->
