<?php
/**
 * Template for the sitemap index
 *
 */
defined('SPARKIN') or die('xD');
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <?php for ($i=1; $i <= $t['total_sitemaps']; $i++) :?>
      <sitemap>
         <loc><?= e(url_for('sitemap.list', ['id' => $i])); ?></loc>
      </sitemap>
   <?php endfor; ?>
</sitemapindex>
