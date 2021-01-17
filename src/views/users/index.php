<?php block('content'); ?>
<div class="row">
  <div class="col-12">

    <?php echo sp_alert_flashes('users'); ?>

    <div class="p-1 mb-3">
      <div class="row align-items-center">

        <div class="col-md-8 text-left">

            <?php

            ?>
            <?php if (current_user_can('add_user')) : ?>
            <a href="<?php echo e_attr(url_for('dashboard.users.create')); ?>" class="btn btn-link btn-lg d-block d-md-inline-block">
                <?php echo svg_icon('add', 'mr-1'); ?><?php echo __('Add new');?>
              </a>
            <?php endif; ?>

            <?php

            ?>
        </div>

        <div class="col-md-4 mt-3 mt-lg-auto text-right d-flex align-items-end">
            <?php

            ?>
          <form method="GET" action="?<?php echo e_attr($t['query_str']); ?>" class="d-inline-block"><div class="input-icon mr-2">
                  <span class="input-icon-addon">
                    <?php echo svg_icon('search'); ?>
                  </span>
                  <input type="text" class="form-control w-12" name="s"
                  placeholder="<?php echo e_attr(__('Search users..')); ?>" value="<?php echo e_attr($t['search']); ?>">
                </div>
              </form>
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

    <div class="px-1 py-2"><ul class="list-inline list-inline-dots m-0">

        <?php foreach ($t['role_list'] as $_role) : ?>
        <li class="list-inline-item">
          <a class="<?php echo $_role['role_id'] == $t['role.role_id'] ? 'text-dark' : ''; ?>" href="?role_id=<?php echo e_attr($_role['role_id'] . request_build_query(['page', 'role_id', 's'])); ?>">
            <?php echo e($_role['role_name']); ?> (<?php echo $_role['users_count']; ?>)
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    </div>

    <form class="card" id="multi-form" method="post" action="<?php echo sp_current_form_uri(); ?>">
        <?php echo $t['csrf_html']; ?>
      <div class="table-responsive">
        <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
          <thead>
            <tr>
            <th class="w-1 text-center"><label class="custom-control custom-checkbox custom-control-inline">
              <input type="checkbox" class="custom-control-input" id="check-all"><span class="custom-control-label"></span>
              </label></th>
              <th class="w-1"></th>
              <th><?php echo __('User'); ?></th>
              <th><?php echo __('E-Mail'); ?></th>
              <th><?php echo __('Role'); ?></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($t['list_entries'])) :?>
                <?php foreach ($t['list_entries'] as $item) :?>
                    <?php
                    $current_user = $item['user_id'] == current_user_ID();
                    $online_class = 'bg-red';
                    if ($item['last_seen'] > 0) {
                        $online_class = sp_is_online($item['last_seen']) ? 'bg-green' : 'bg-red';
                    }
                    ?>
                <tr>
                     <td>
                       <label class="custom-control custom-checkbox custom-control-inline">
              <input type="checkbox" class="custom-control-input" name="item_multi[]" value="<?php echo e_attr($item['user_id']); ?>"><span class="custom-control-label"></span>
              </label>
                     </td>
                  <td class="text-center">
                    <div class="avatar d-block"
                    style="background-image: url(<?php echo e_attr(sp_user_avatar_uri($item['avatar'], $item['email'])); ?>)">
                    <span class="avatar-status <?php echo $online_class ?>"></span>
                  </div>
                </td>
                <td>
                  <div><small>#<?php echo e($item['user_id']); ?></small> <?php echo e($item['full_name']); ?>
                    <?php if ($current_user) : ?>
                    <span class="badge badge-dark"><?php echo __('You'); ?></span>
                    <?php endif;?>
                    <?php if ($item['is_blocked']) : ?>
                    <span class="badge badge-danger"><?php echo __('Blocked'); ?></span>
                    <?php endif;?>

                    <?php if ($item['is_verified']) : ?>
                      <span class="badge badge-success"><?php echo __('Email Verified'); ?></span>
                    <?php else : ?>
                      <span class="badge badge-warning"><?php echo __('Non Email Verified'); ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="small text-muted">

                    <?php if ($item['username']) :?>
                      @<?php echo e($item['username']); ?> &middot;
                    <?php endif; ?>
                    <?php echo e($item['user_ip']); ?>

                  </div>
                </td>
                <td>
                  <div><?php echo e($item['email']); ?></div>

                </td>
                <td>
                  <div>
                    <a
                    title="<?php echo e_attr(__('Click to view all users under this role.')); ?>"
                    href="?<?php echo e_attr("sort={$t['sort_type']}&role_id={$item['role_id']}"); ?>">
                    <?php echo e($item['role_name']); ?>
                    </a>

                  <div class="small text-muted">
                    <?php echo __('Registered:') . ' ' . date('M d, Y', $item['created_at']); ?>
                  </div>
                  </div>
                </td>
                    <?php
                    $edit_disabled = !current_user_can('edit_user') || $current_user ? 'disabled' : '';
                    $delete_disabled = !current_user_can('delete_user') || $current_user ? 'disabled' : '';
                    ?>
                <td class="text-center">
                  <a href="<?php echo e_attr(url_for('dashboard.users.update', ['id' => $item['user_id']])); ?>"
                    class="btn btn-sm btn-outline-dark <?php echo $edit_disabled; ?>">
                    <?php echo svg_icon('create', 'mr-1'); ?> <?php echo __('Edit'); ?>
                  </a>
                  <a href="<?php echo e_attr(url_for('dashboard.users.delete', ['id' => $item['user_id']])); ?>"
                    class="delete-entry btn btn-sm btn-danger <?php echo$delete_disabled;?>">
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

        <div class="px-4 py-4 bg-light border-top">
            <label class="form-label" for="action">
            <?php echo __('With Selected:'); ?> </label>
          <div class="d-flex">
            <select name="action" class="form-control w-25" id="action">
            <?php if (current_user_can('delete_user')) : ?>
            <option value="delete"><?php echo __('Delete'); ?></option>
            <?php endif; ?>
            <?php if (current_user_can('edit_user')) : ?>
            <option value="verify"><?php echo __('Verify'); ?></option>
            <option value="unverify"><?php echo __('Un-verify'); ?></option>
            <?php endif; ?>
            <?php if (current_user_can('change_user_status')) : ?>
            <option value="block"><?php echo __('Block'); ?></option>
            <option value="unblock"><?php echo __('Un-block'); ?></option>
            <?php endif; ?>
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
    $('.delete-entry').on('click', function (e) {
      e.preventDefault();
      var endpoint = $(this).attr('href');

      lnv.confirm({
        title: '<?php echo __("Confirm Deletion"); ?>',
        content: '<?php echo __("Are you sure you want to delete this user?"); ?>',
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
    'title' => __('Users'),
    'body_class' => 'users users-list',
    'page_heading' => $t->get('page_heading', __('Users')),
    ]
);
