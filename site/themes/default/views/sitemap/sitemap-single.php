<?= '<?xml version="1.0" encoding="utf-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($t['entries'] as $item) : ?>
          <url>
                <loc><?php echo e($item['url']); ?></loc>
                <lastmod><?php echo date('c', $item['updated_at']); ?></lastmod>
                <changefreq><?php echo $t['changefreq']; ?></changefreq>
          </url>
    <?php endforeach; ?>
</urlset>
