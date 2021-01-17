<div class="col-md-3 col-sm-3 col-lg-2 col-6 gallery-item-wrap" id="gallery-item-<?php echo e_attr($t['item.content_id']); ?>">
  <div class="gallery-item card">
    <div class="gallery-item-inner">
      <div class="gallery-thumbnail view-entry"
      data-thumbnail="<?php echo e_attr($t['item.content_thumbnail']); ?>"
      data-relative-url="/<?php echo e_attr($t['item.content_rel_path']); ?>"
      data-ext="<?php echo e_attr($t['item.content_ext']); ?>"
      data-name="<?php echo e_attr($t['item.content_title']); ?>"
      data-size="<?php echo e_attr($t['item.content_readable_size']); ?>"
      data-filetype="<?php echo e_attr($t['item.content_file_type']); ?>"
      data-url="<?php echo e_attr($t['item.content_url']); ?>">
      <div class="centered">
        <img src="<?php echo e_attr($t['item.content_thumbnail']); ?>" class="gallery-img-thumb">
      </div>
    </div>
    <?php if (current_user_can('manage_gallery')) : ?>
      <button data-endpoint="<?php echo e_attr(url_for('dashboard.gallery.delete_post', ['id' => $t['item.content_id']])); ?>" class="delete-entry btn btn-sm btn-danger"><?php echo svg_icon('trash', 'mr-1'); ?></button>
    <?php endif; ?>
    <?php if ($t['item.content_file_type'] !== 'image') : ?>
      <div class="gallery-item-title text-truncate"><?php echo e($t['item.content_filename']); ?></div>
    <?php endif; ?>
  </div>
</div>
</div>
