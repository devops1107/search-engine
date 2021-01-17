<?php block('content'); ?>
<div class="row">
  <div class="col-12">

    <?php echo sp_alert_flashes('roles'); ?>

    <div class="p-1 mb-3">
      <div class="row align-items-center">

        <div class="col-4 text-left">
            <?php

            ?>
          <a href="<?php echo url_for('dashboard.roles.create'); ?>" class="btn btn-link btn-lg"><?php echo svg_icon('add', 'mr-1'); ?><?php echo __('Add new');?></a>

            <?php

            ?>
        </div>
        <div class="col-8 ml-lg-auto text-right">
            <?php

            ?>
            <?php if (!empty($t['sorting_rules'])) : ?>
            <div class="dropdown">
              <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                <?php echo e(sprintf(__('Sort: %s'), sp_sort_label($t['sort_type']))); ?>
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <?php foreach ($t['sorting_rules'] as $sort) : ?>
                  <a href="?<?php echo e_attr("page={$t['current_page']}&sort={$sort}{$t['query_str']}"); ?>" class="dropdown-item <?php echo $sort == $t['sort_type'] ? 'active' : '' ?>">
                    <?php echo e(sp_sort_label($sort)); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>

            <?php

            ?>
        </div>


      </div>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover table-outline text-nowrap table-vcenter card-table">
          <thead>
            <tr>
              <th><?php echo __('ID'); ?></th>
              <th><?php echo __('Role Name'); ?></th>
              <th><?php echo __('Permissions'); ?></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($t['list_entries'])) :?>
                <?php foreach ($t['list_entries'] as $item) :?>
                <tr>
                  <td>
                    <div>
                      <?php echo e($item['role_id']); ?>
                    </div>
                  </td>
                  <td>
                    <div>
                      <?php if ($item['is_protected']) : ?>
                            <?php echo svg_icon(
                                'lock',
                                'text-danger',
                                ['data-toggle' => 'tooltip', 'title' => __('Protected'), 'data-placement' => 'auto']
                            );?>
                      <?php endif; ?>
                      <?php echo e($item['role_name']); ?>

                    </div>
                  </td>
                  <td>
                    <div class="tags">
                      <?php echo sp_array_wrap(
                          $item['permissions'],
                          '<span class="tag tag-success">',
                          '</span>',
                          3,
                          '<span class="tag">' . __('none') . '</span>',
                          '<span class="tag bg-transparent">' . __('...and %d more') . '</span>'
                      ); ?>
                    </div>
                  </td>
                  <td class="text-center">

                          <a href="<?php echo e_attr(url_for('dashboard.roles.update', ['id' => $item['role_id']])); ?>"
                            class="btn btn-sm btn-outline-dark <?php echo !current_user_can('edit_role') ? 'disabled' : ''; ?>">
                            <?php echo svg_icon('create', 'mr-1'); ?> <?php echo __('Edit'); ?>
                          </a>
                          <a href="<?php echo e_attr(url_for('dashboard.roles.delete', ['id' => $item['role_id']])); ?>"
                            class="btn btn-sm btn-danger <?php echo $item['is_protected'] || !current_user_can('delete_role') ? 'disabled' : 'delete-entry'; ?>">
                            <?php echo svg_icon('trash', 'mr-1'); ?> <?php echo __('Delete'); ?>
                          </a>
                          <a class="btn btn-sm btn-outline-primary" href="<?php echo e_attr(url_for('dashboard.users')); ?>?role_id=<?php echo$item['role_id']?>">
                            <?php echo svg_icon('people', 'mr-1'); ?> <?php echo __('Users'); ?>
                          </a>
                  </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="7" class="p-0"><div class="alert alert-light m-0 border-0"><?php echo __('No entries found'); ?></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <!-- ./table-responsive -->

      <div class="card-footer">
        <?php

        ?>
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
        <?php

        ?>
      </div>
    </div>

  </div>
</div>
<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
  $(function() {
    $('.delete-entry').on('click', function (e) {
      e.preventDefault();
      var endpoint = $(this).attr('href');

      lnv.confirm({
        title: '<?php echo __("Confirm Deletion"); ?>',
        content: '<?php echo __("Are you sure you want to delete this role?"); ?>',
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
    'title' => __('User Roles'),
    'body_class' => 'roles role-list',
    'page_heading' => __('User Roles'),
    'page_subheading' => __('Manage user roles.'),
    ]
);
