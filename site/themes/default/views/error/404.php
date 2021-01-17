<?php defined('SPARKIN') or die('xD'); ?>
<div class="container d-flex h-100 justify-content-center align-items-center">
    <div class="text-center py-4 px-1">
        <div class="mb-2">
            <?php echo svg_icon('sad', 'svg-xl text-muted'); ?>
        </div>
        <h4 class="h5"><?php echo __('404-not-found', _T); ?></h4>
        <p class="text-muted">
            <?php echo __('404-desc', _T); ?>
        </p>
        <p>
            <a class="btn btn-primary sp-link" href="<?php echo e_attr(base_uri()); ?>"><?php echo __('homepage', _T); ?></a>
        </p>
    </div>
</div>
</div>


