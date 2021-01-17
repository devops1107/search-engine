<?php
/**
 * Template for the offcanvas menu
 *
 */
defined('SPARKIN') or die('xD');
?>
<div aria-hidden="true" class="navdrawer <?php echo rtl_value('navdrawer-right', 'navdrawer-left'); ?>" id="offcanvas" tabindex="-1">
  <div class="navdrawer-content">
    <nav class="navdrawer-nav">
        <li class="nav-item text-right">
            <button class="offcanvas-close bg-transparent border-0" data-target="#offcanvas" data-toggle="navdrawer" href="javascript:void(0);"><?php echo svg_icon('close', 'svg-md'); ?></button>
        </li>
        <?php if ($t['show_engines_offcanvas']) : ?>
            <p class="navdrawer-subheader text-uppercase small"><?php echo __('search-engines', _T); ?></p>
            <?php foreach ($t['engines'] as $engine) :?>
                <li class="nav-item">
                    <a
                    class="nav-link <?php echo $engine['engine_id'] == $t['default_engine'] ? 'active' : '' ; ?>"
                    href="javascript:void();"
                    data-engine-id="<?php echo e_attr($engine['engine_id']); ?>"
                    data-engine-active="<?php echo $engine['engine_id'] == $t['default_engine'] ? 'true' : 'false' ; ?>"
                    data-target="#engine"
                    data-text-target="#engine-name"
                    >
                    <?php echo e($engine['engine_name']); ?>

                </a></li>
            <?php endforeach; ?>
        <?php endif; ?>


        <p class="navdrawer-subheader text-uppercase small mt-2"><?php echo __('prefrences', _T); ?></p>
        <li class="nav-item">
            <a class="nav-link sp-link" href="<?php echo e_attr(url_for('site.preferences')); ?>#safesearch">
                <span class="menu-label"><?php echo __('darkmode', _T); ?>

                </span>     <span class="ml-auto text-muted">
                    <?php echo $t['preferences.darkmode'] ? __('on', _T) : __('off', _T); ?>
                </span>
            </a></li>
        <li class="nav-item">
            <a class="nav-link sp-link" href="<?php echo e_attr(url_for('site.preferences')); ?>#safesearch">
                <span class="menu-label"><?php echo __('safesearch', _T); ?>

                </span>     <span class="ml-auto text-muted">
                    <?php echo __($t['preferences.safesearch'], _T); ?>
                </span>
            </a></li>
            <li class="nav-item">
                <a class="nav-link sp-link" href="<?php echo e_attr(url_for('site.preferences')); ?>#language">
                    <span class="menu-label"><?php echo __('language', _T); ?>

                    </span>     <span class="ml-auto text-muted">
                            <?php echo e_attr($t['active_locale.name']); ?>
                    </span>
                </a></li>
            <li class="nav-item">
                <a class="nav-link sp-link" href="<?php echo e_attr(url_for('site.preferences')); ?>#all">
                    <span class="menu-label"><?php echo __('more-settings', _T); ?>

                    </span>
                </a></li>

                <?php
                echo render_nav_menu(
                    'offcanvas-nav',
                    [
                        'no_container' => true,
                        'menu_id'  => 'offcanvas-header-nav',
                        'before_html' => '<p class="navdrawer-subheader text-uppercase small mt-2">' . __('who-are-we', _T) . '</p>',
                        'after_html' => '',
                    ]
                );
                ?>

                <?php if (is_logged()) :  ?>
                    <?php if (current_user_can('access_dashboard')) : ?>
                        <p class="navdrawer-subheader text-uppercase small mt-2"><?php echo __('management', _T); ?></p>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e_attr(url_for('dashboard')); ?>" target="_blank">
                                <span class="menu-label"><?php echo __('dashboard', _T); ?></span>
                            </a></li>
                    <?php endif; ?>
                <?php endif; ?>

                </nav>
            </div>
        </div>
