<?php defined('SPARKIN') or die('xD'); ?>
<div class="main-footer footer <?php echo $t['backgrounds_enabled'] ? 'footer-dark bg-dark' : darkmode_value('footer-light bg-light', 'footer-dark bg-dark'); ?> mt-3">

    <div class="footer-section px-0 py-0 m-0">
      <div class="container search-container">
        <nav class="navbar <?php echo $t['backgrounds_enabled'] ? 'home-footer-navbar navbar-dark' : darkmode_value('navbar-light', 'navbar-dark'); ?> navbar-expand-sm p-0 <?php echo $t['backgrounds_enabled'] ? 'min-height-auto' : ''; ?>">
            <ul class="navbar-nav w-100 flex-row flex-wrap">
               <?php
                echo render_nav_menu(
                    'footer-nav',
                    [
                    'no_container' => true,
                    'menu_class' => 'list-unstyled text-small',
                    'link_class' => 'nav-link footer-link',
                    'fallback_text' => __('no-menu-set', _T)
                    ]
                );
                ?>

                <li class="nav-item ml-sm-auto">
                    <a class="nav-link">
                        <?php echo __('footer-copyright', _T, ['year' => date('Y'), 'sitename' => get_option('site_name')]); ?>
                    </a>
                </li>
    </ul>
</nav>
</div>
</div>

</div>

