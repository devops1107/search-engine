<?php block('content'); ?>
<div class="row">
  <div class="col-12">

    <?php echo sp_alert_flashes('engines'); ?>

    <div class="px-1 pb-2">
      <div class="row align-items-center">

        <div class="col-md-4 text-left">
          <a href="<?php echo e_attr(url_for('dashboard.engines.create')); ?>" class="btn btn-link btn-lg d-block d-md-inline-block"><?php echo svg_icon('add', 'mr-2'); ?><?php echo __('Add new');?></a>
        </div>

        <div class="col-md-8 ml-lg-auto text-center text-md-right">
            <a class="btn btn-sm btn-primary" href="<?php echo e_attr(url_for('dashboard.engines.reorder')); ?>">
                <?php echo svg_icon('cursor-move', 'mr-1'); ?>
                <?php echo __('Re-order'); ?>
            </a>
            <?php if (!empty($t['sorting_rules'])) : ?>
            <div class="dropdown">
              <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" id="sort-button-text">
                <?php echo e(sprintf(__('Sort: %s'), sp_sort_label($t['sort_type']))); ?>
              </button>
              <div class="dropdown-menu">
                <?php foreach ($t['sorting_rules'] as $sort) : ?>
                  <a href="?<?php echo e_attr("page={$t['current_page']}&sort={$sort}{$t['query_str']}"); ?>" class="dropdown-item <?php echo $sort === $t['sort_type'] ? 'active' : '' ?>">
                    <?php echo e(sp_sort_label($sort)); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
          <thead>
            <tr>
              <th><?php echo __('Name'); ?></th>
              <th><?php echo __('Type'); ?></th>
              <th><?php echo __('Created'); ?></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($t['list_entries'])) :?>
                <?php foreach ($t['list_entries'] as $item) :?>
                <tr>
                  <td>
                    <div>
                        <?php echo e($item['engine_name']); ?>
                        <?php if (get_option('default_engine') == $item['engine_id']) : ?>
                            <span class="badge badge-info"><?php echo __('Default'); ?></span>
                        <?php endif; ?>
                        <span class="d-block text-muted small">
                            <?php echo e($item['engine_cse_id']); ?>
                        </span>
                    </div>
                  </td>
                  <td>
                    <div><?php echo (int) $item['engine_is_image'] ? __('Image') : 'Web'; ?></div>
                  </td>
                  <td>
                    <div><?php echo time_ago($item['created_at']); ?></div>
                  </td>
                  <td class="text-center">

                          <a href="<?php echo e_attr(url_for('dashboard.engines.update', ['id' => $item['engine_id']])); ?>"
                            class="btn btn-sm btn-outline-dark">
                            <?php echo svg_icon('create', 'mr-1'); ?> <?php echo __('Edit'); ?>
                            </a>
                          <a href="<?php echo e_attr(url_for('dashboard.engines.delete', ['id' => $item['engine_id']])); ?>"
                            class="delete-entry btn btn-sm btn-danger <?php echo get_option('default_engine') == $item['engine_id'] ? 'disabled' : '';?>">
                            <?php echo svg_icon('trash', 'mr-1'); ?> <?php echo __('Delete'); ?>
                          </a>

                            <form method="post" action="<?php echo e_attr(url_for('dashboard.engines.default_engine_post', ['id' => $item['engine_id']])); ?>" class="d-inline-block">
                                <?php echo $t['csrf_html']; ?>
                                <button type="submit" class="btn btn-info btn-sm" <?php echo get_option('default_engine') == $item['engine_id'] ? 'disabled' : '';?>>
                                    <?php echo svg_icon('checkmark', 'mr-1'); ?>
                                    <?php echo __('Set Default'); ?>
                                </button>
                            </form>
                  </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="7" class="p-0"><div class="alert alert-light m-0 rounded-0 border-0"><?php echo __('No entries found'); ?></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <!-- ./table-responsive -->

      <div class="card-footer">
        <div class="container">
          <div class="row align-items-end flex-row-reverse">

            <div class="col-md-4 col-xs-12 mb-5 text-right">
                <?php echo sprintf(__('Showing %s-%s of total %s entries.'), $t['offset'], $t['current_items'], $t['total_items']); ?>

            </div>
            <div class="col-md-8 col-xs-12 text-left">
              <nav class="table-responsive mb-2">
                <?php echo $t['pagination_html']; ?>
            </nav>
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
  $(function() {
    $(document).on('click', '.delete-entry', function (e) {
      e.preventDefault();
      var endpoint = $(this).attr('href');

      lnv.confirm({
        title: '<?php echo __("Confirm Deletion"); ?>',
        content: '<?php echo __("Are you sure you want to delete this engine?"); ?>',
        confirmBtnText: '<?php echo __("Confirm"); ?>',
        confirmHandler: function () {
          $spark.ajaxPost(endpoint, {}, function () {
            $spark.selfReload();
          });
        },
        cancelBtnText: '<?php echo __("Cancel"); ?>',
        cancelHandler: function() {
        }
      })
    });
  });
</script>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
    'title' => __('Engines'),
    'body_class' => 'engines engines-list',
    'page_heading' => __('Engines'),
    'page_subheading' => __('Manage engines.'),
    ]
);
