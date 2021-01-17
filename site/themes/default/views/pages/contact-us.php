<div class="container search-container py-4">
    <div class="row">
        <div class="col-md-8">
            <?php echo sp_alert_flashes('pages', true, false); ?>
            <div class="page-content">
                <?php echo $t['page.content_body']; ?>
            </div>


            <form method="post" class="mt-3" action="<?php echo e_attr(url_for('site.contact_form_action')); ?>"
                data-reset="true" data-ajax-form="true" data-recaptcha-id="contact-captcha" data-parsley-validate>

                <?php echo $t['csrf_html']; ?>
                <?php echo $t['honeypot_html']; ?>
                <div class="form-group">
                    <div class="floating-label textfield-box">
                        <label class="form-label" for="name"><?php echo __('contact-your-name-label', _T); ?></label>
                        <input
                        type="text" class="form-control" id="name" name="name"
                        value="<?php echo sp_post('name'); ?>"
                        required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="floating-label textfield-box">
                    <label for="email"><?php echo __('contact-your-email-label', _T); ?></label>
                    <input type="email" name="email" id="email" value="<?php echo sp_post('email'); ?>" class="form-control" required>
                </div>
                </div>

                <div class="form-group">
                    <div class="floating-label textfield-box">
                    <label for="subject"><?php echo __('contact-subject-label', _T); ?></label>
                    <input type="subject" name="subject" id="subject" value="<?php echo sp_post('subject'); ?>" class="form-control" minlength="10" maxlength="200" placeholder="<?php echo e_attr(__('contact-subject-help', _T)); ?>">
                </div>
                </div>
                <div class="form-group">

                    <div class="floating-label textfield-box">
                    <label for="message"><?php echo __('contact-message-label', _T); ?></label>
                    <textarea name="message" id="message" class="form-control" rows="5" minlength="0" maxlength="5000" required><?php echo sp_post('message'); ?></textarea>
                </div>
                </div>


                <?php
                echo sp_google_recaptcha(
                    'page.contact',
                    '<div class="text-center">',
                    '</div>',
                    false,
                    [],
                    'contact-captcha'
                );
                ?>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">
                     <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                     <span class="btn-text"><?php echo __('send-message', _T); ?></span>
                 </button>
             </div>
         </form>


            <?php echo breadcrumb_render(); ?>
        </div>
    </div>
</div>
