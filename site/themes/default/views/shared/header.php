<?php
/**
 * Header Template
 *
 * This template contains the global header of the theme. Mainly the navbar
 */

defined('SPARKIN') or die('xD');
?>
<div id="site-navbar" class="site-navbar">
        <div class="searchbar">
    <div class="container search-container">
        <div class="row">
            <div class=" mr-2 d-flex align-items-center searchbar-logo-wrap">
                <a href="<?php echo e_attr(url_for('site.home')); ?>" class="sp-link">
                    <img src="<?php echo e_attr($t['search_logo_url']); ?>" alt="<?php echo e_attr(get_option('site_name')); ?>" class="search-logo">
                </a>
            </div>
            <div class="col-md-6 mt-sm-0 mt-4">
                <form method="GET" action="<?php echo e_attr(url_for('site.search')); ?>" id="searchForm" data-ajax-form="true" data-before-callback="preventEmptySubmit">
                    <input type="hidden" name="engine" value="<?php echo e_attr($t['current_engine_id'] ? $t['current_engine_id'] : $t['default_engine']); ?>" id="engine">
                    <div class="form-group searchbox-group searchbar-group">
                        <input type="text" class="form-control search-input" name="q"  data-autocomplete="true" autocorrect="off" autocapitalize="off" autocomplete="off" spellcheck="false" value="<?php echo e_attr($t['search_query']); ?>" data-search-input="true">
                        <button type="submit" class="has-spinner search-btn right-0"><span class="btn-text"><?php echo svg_icon('search', 'svg-md'); ?></span>
                     <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></button>
                    </div>

                </form>
            </div>
            <!-- ./col-md-6 -->
            <div class="col-auto ml-auto searchbar-toggler-pull-up right-0">
                  <button class="navbar-toggler d-flex" type="button" id="dropdownMenuButton" data-target="#offcanvas" data-toggle="navdrawer">
                    <?php echo svg_icon('menu', 'svg-md'); ?>
                </button>

            </div>
        </div>
    </div>
    </div>
    <!-- ./searchbar -->

    <?php if (has_items($t['engines'])) : ?>
        <div class="search-tabs">
            <div class="container search-container nav-scrollable px-sm-3 px-0">
                <ul class="nav nav-tabs <?php echo darkmode_value('', 'nav-inverse'); ?>" role="tablist">
                    <?php foreach ($t['engines'] as $tab) : ?>
                      <li class="nav-item">
                        <a class="sp-link nav-link <?php echo $t->get("{$tab['engine_id']}_active"); ?>"
                            href="<?php echo e_attr(url_for('site.search')); ?>?q=<?php echo e_attr($t['search_query']); ?>&engine=<?php echo e_attr($tab['engine_id']); ?>">
                            <?php echo e(__($tab['engine_name'], _T)); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($t['header_heading']) : ?>
        <div class="search-titlebar">
            <div class="container search-container">
        <h3 class="searchbar-title"><?php echo e($t['header_heading']); ?></h3>
    </div>
    </div>
    <?php endif; ?>

</div>

<?php insert('shared/offcanvas_menu.php'); ?>
