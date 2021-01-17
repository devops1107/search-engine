<?php block('pre_header'); ?>
<?php endblock(); ?>

<?php block('body_intro'); ?>
<p><?php echo sprintf(__('Hello <strong>%s</strong>!'), $t['full_name']);?></p>
<p><?php echo sprintf(__("Thanks for joining %s. Please click the link below to verify your account."), get_option('site_name')); ?></p>
<?php endblock(); ?>

<?php block('body_footer'); ?>
<p><?php echo __("If you didn't create any account, you can just ignore this email."); ?></p>
<?php endblock(); ?>

<?php block('email_footer'); ?>
<span class="apple-link"></span><br/>
<?php endblock(); ?>

<?php

extend(
    'admin::layouts/call_to_action_email.php',
    [
        'action_label' => __('Verify Account'),
    ]
);
