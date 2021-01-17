<?php block('pre_header'); ?>
<?php endblock(); ?>

<?php block('body_intro'); ?>
<p><?php echo sprintf(__('Hello <strong>%s</strong>!'), $t['full_name']);?></p>
<p><?php echo __("Someone requested a password reset for your account. Please click the link below to proceed."); ?></p>
<?php endblock(); ?>

<?php block('body_footer'); ?>
<p><?php echo __("If you didn't request any password reset, you can just ignore this email."); ?></p>
<?php endblock(); ?>

<?php block('email_footer'); ?>
<span class="apple-link"></span><br/>
<?php endblock(); ?>

<?php

extend(
    'admin::layouts/call_to_action_email.php',
    [
        'action_label' => __('Reset Password'),
    ]
);
