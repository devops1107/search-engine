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
                <?php if ($t['invalid']) : ?>
                    <div class="card shadow-none">
                        <div class="card-body">
                            <h3 class="auth-heading"><?php echo __('email-link-expired-heading', _T); ?></h3>
                            <p class="auth-help"><?php echo __('email-link-expired', _T); ?></p>
                            <div class="form-group text-right">
                                <a href="<?php echo e_attr(url_for('auth.forgotpass')); ?>" class="btn btn-link"><?php echo __("request", _T); ?></a>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
            <form
            class="card shadow-none"
            method="POST"
            action="<?= e_attr(url_for('auth.resetpass_post', ['token' => $t['token']])); ?><?php echo $t['query_string']; ?>"
            data-recaptcha-id="auth-captcha"
            data-ajax-form="true"
            data-reset="true"
            data-parsley-validate>
                    <?php echo $t['csrf_html'] . $t['honeypot_html']; ?>

            <div class="card-body">
             <div class="auth-logo-wrap">
                <a href="<?php echo e_attr(url_for('site.home')); ?>" class="auth-logo-link">
                    <img src="<?php echo e_attr($t['auth_logo_url']); ?>" class="auth-logo" alt="<?php echo e_attr(get_option('site_name')); ?>">
                </a>
            </div>

            <h3 class="auth-heading"><?php echo __('reset-password-heading', _T); ?></h3>
            <p class="auth-help"><?php echo __('reset-password-help', _T); ?></p>


                    <?php echo sp_alert_flashes('account', true, false); ?>

            <div class="form-group">
                <div class="floating-label textfield-box">
                    <label class="form-label" for="password"><?php echo __('new-password', _T); ?></label>
                    <input
                    type="password" name="password" id="password" class="form-control"
                    placeholder="<?php echo __("new-password-placeholder", _T); ?>"
                    minlength="<?php echo e_attr(config('internal.password_minlength')); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <div class="floating-label textfield-box">
                    <label class="form-label" for="confirm_password"><?php echo __('confirm-password', _T); ?></label>
                    <input
                    type="password" name="confirm_password" id="confirm_password" class="form-control"
                    placeholder="<?php echo __("confirm-password-placeholder", _T); ?>"
                    minlength="<?php echo e_attr(config('internal.password_minlength')); ?>"
                    data-parsley-equalto="#password"
                    required>
                </div>
            </div>

        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary">
                <span class="btn-text"><?php echo __("reset-password", _T); ?></span>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>

        <p class="d-sm-none py-3">
                    <?php if (is_logged()) : ?>
                        <?php echo __("done-heading", _T); ?> <?php echo __("go-back-to", _T); ?>
                <a href="<?php echo e_attr(url_for('site.home')); ?>" class=""><?php echo __("homepage", _T); ?></a>
                    <?php else : ?>
                        <?php echo __("already-have-an-account", _T); ?>
                <a href="<?php echo e_attr(url_for('auth.signin') . $t['query_string']);  ?>" class=""><?php echo __("sign-in-now", _T); ?></a>
                    <?php endif; ?>
        </p>
</div>
</div>
</form>
<!-- ./card -->
                <?php endif; ?>
</div>

<div class="col-sm-7 auth-sidebar-right">
    <?php if (is_logged()) : ?>
       <h3 class="auth-heading"><?php echo __("done-heading", _T); ?></h3>
       <h6 class="auth-subheading"><?php echo __("go-back-to", _T); ?></h6>
       <a href="<?php echo e_attr(url_for('site.home')); ?>" class="btn btn-outline-dark btn-lg"><?php echo __("homepage", _T); ?></a>
    <?php else : ?>
     <h3 class="auth-heading"><?php echo __("already-have-an-account", _T); ?></h3>
     <h6 class="auth-subheading"><?php echo __("already-have-an-account-subtitle", _T); ?></h6>
     <a href="<?php echo e_attr(url_for('auth.signin') . $t['query_string']);  ?>" class="btn btn-outline-dark btn-lg"><?php echo __("sign-in-now", _T); ?></a>
    <?php endif; ?>
</div>
</div>
<!-- ./container -->
