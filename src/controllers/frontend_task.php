<?php

use Valitron\Validator;
use spark\drivers\Nav\Pagination;

view_set('active_locale', get_theme_active_locale_item());

$locale = get_theme_active_locale();

// Valitron throws exceptions, so make sure the file exists before replacing
$langDir = theme_locale_dir() . '/' . $locale;
$valitronFile = $langDir . '/valitron.php';
if (is_file($valitronFile)) {
    Validator::langDir($langDir);
    Validator::lang('valitron');
}

Pagination::setTranslations([
    'first' => __('first', _T),
    'last'  => __('last', _T),
    'next'  => __('next', _T),
    'prev'  => __('prev', _T),
    'numeric'  => [
      '1' => __('num_1', _T),
      '2' => __('num_2', _T),
      '3' => __('num_3', _T),
      '4' => __('num_4', _T),
      '5' => __('num_5', _T),
      '6' => __('num_6', _T),
      '7' => __('num_7', _T),
      '8' => __('num_8', _T),
      '9' => __('num_9', _T),
      '0' => __('num_0', _T),
    ]
]);

breadcrumb_add('home', __('home', _T), base_uri());
