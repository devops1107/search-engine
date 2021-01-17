<?php block('body_content'); ?>
<h3>
<?php echo sprintf(__('Hello, you have a message from your contact form.'));?>
</h3>
<hr/>
<?php echo nl2br($t['message'], true); ?>
<br/>
<hr/>
<?php echo __('Sender Name:'); ?> <span class="text-gray"><?php echo e($t['name']); ?></span><br/>
<?php echo __('Sender E-Mail:'); ?> <span class="text-gray"><?php echo e($t['email']); ?></span><br/>
<?php echo __('Sender IP:'); ?> <span class="text-gray"><?php echo e($t['user_ip']); ?></span><br/>
<?php echo __('Sender Browser:'); ?> <span class="text-gray"><?php echo e($t['user_agent']); ?></span><br/>
<?php endblock(); ?>

<?php block('email_footer'); ?>
<?php echo sprintf(__('This email was sent via the contact form on %s, please note the information provided here should not be trusted as they can be faked.'), base_uri()); ?>
<?php endblock(); ?>

<?php

extend(
    'admin::layouts/email_basic.php',
    [
    ]
);
