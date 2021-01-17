<?php block('content'); ?>
<div class="row">
  <div class="col-12">

    <?php echo sp_alert_flashes('queries'); ?>

    <div class="px-1 pb-2">
      <div class="row align-items-center">

        <div class="col-4 text-left">
            <?php echo __('Log Searches'); ?>
            <?php if ((int) get_option('search_log')) : ?>
                <span class="badge badge-success"><?php echo __('Enabled'); ?></span>
            <?php else : ?>
                <span class="badge badge-danger"><?php echo __('Disabled'); ?></span>
            <?php endif; ?>
        </div>

        <div class="col-8 ml-lg-auto text-right">
            <?php if (!empty($t['sorting_rules'])) : ?>
            <div class="dropdown">
              <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" id="sort-button-text">
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

    <form class="card" id="multi-form" method="post" action="<?php echo e_attr(url_for('dashboard.queries_post')); ?>">
      <?php echo $t['csrf_html']; ?>
      <div class="table-responsive">
        <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
          <thead>
            <tr>

            <th class="w-1 text-center"><label class="custom-control custom-checkbox custom-control-inline">
              <input type="checkbox" class="custom-control-input" id="check-all"><span class="custom-control-label"></span>
              </label></th>
              <th><?php echo __('Query'); ?></th>
              <th><?php echo __('Total searched'); ?></th>
              <th><?php echo __('First searched'); ?></th>
              <th><?php echo __('Last searched'); ?></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($t['list_entries'])) :?>
                <?php foreach ($t['list_entries'] as $item) :?>
                <tr>

                     <td>
                       <label class="custom-control custom-checkbox custom-control-inline">
              <input type="checkbox" class="custom-control-input" name="item_multi[]" value="<?php echo e_attr($item['query_id']); ?>"><span class="custom-control-label"></span>
              </label>
          </td>
                  <td>
                    <div>
                        <?php echo e($item['query_term']); ?>
                    </div>
                  </td>
                  <td>
                    <div><?php echo e($item['query_count']); ?></div>
                  </td>
                  <td>
                    <div><?php echo time_ago($item['created_at']); ?></div>
                  </td>
                  <td>
                    <div><?php echo time_ago($item['updated_at']); ?></div>
                  </td>
                  <td class="text-center">
                          <a href="<?php echo e_attr(url_for('dashboard.queries.delete', ['id' => $item['query_id']])); ?>"
                            class="delete-entry btn btn-sm btn-danger ">
                            <?php echo svg_icon('trash', 'mr-1'); ?> <?php echo __('Delete'); ?>
                          </a>
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

      <div class="px-4 py-4 bg-light border-top">
        <label class="form-label" for="action">
            <?php echo __('With Selected:'); ?> </label>
            <div class="d-flex">
                <select name="action" class="form-control w-25" id="action">
                    <option value="delete"><?php echo __('Delete'); ?></option>
                </select>

                <button type="submit" class="btn btn-primary"><?php echo __('Apply'); ?></button>
            </div>

        </div>
    </form>

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
        content: '<?php echo __("Are you sure you want to delete this query?"); ?>',
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

    // check all boxes
    $('#check-all').on('change', function () {
      var check_all = $(this);
      if (check_all.prop('checked')) {
        $("input[name='item_multi[]").prop('checked', true);
      } else {
        $("input[name='item_multi[]").prop('checked', false);
      }
    });

  });
</script>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
    'title' => __('Queries'),
    'body_class' => 'queries queries-list',
    'page_heading' => __('Queries'),
    'page_subheading' => __('Manage queries.'),
    ]
);
