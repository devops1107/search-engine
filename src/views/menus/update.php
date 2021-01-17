<?php block('content'); ?>
<div class="row">
  <div class="col-md-3">
    <form method="post" id="addLinkForm" action="<?php echo e_attr(url_for('dashboard.menus.add_menu_post', ['id' => $t['menu.menu_id']])); ?>" class="card" data-spark-ajax data-response-target="#menu-response" data-success-callback="addMenuCallback" data-parsley-validate>
        <?php echo $t['csrf_html']; ?>
      <div class="card-body">
            <div class="form-group">
            <label class="form-label" for="item_label"><?= __('Label'); ?></label>
             <input type="text" name="item_label" id="item_label" class="form-control" maxlength="200" required>
             <span class="form-text text-muted"><?php echo __('Menu item name'); ?></span>
          </div>

            <div class="form-group">
            <label class="form-label" for="item_url"><?= __('URL'); ?></label>
             <input type="text" name="item_url" id="item_url" class="form-control" required>
             <span class="form-text text-muted"><?php echo __('Absolute URL or relative path'); ?></span>
          </div>

            <div class="form-group">
            <label class="form-label" for="item_class"><?= __('HTML Classes'); ?></label>
             <input type="text" name="item_class" id="item_class" class="form-control">
             <span class="form-text text-muted"><?php echo __('Custom HTML classes for the menu item (optional)'); ?></span>
          </div>

            <div class="form-group">
            <label class="form-label" for="item_icon"><?= __('Icon ID'); ?></label>
             <input type="text" name="item_icon" id="item_icon" class="form-control" maxlength="200">
             <span class="form-text text-muted"><?php echo __('Icon identifier for the menu (optional)'); ?></span>
          </div>


        </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-secondary ml-auto"><?php echo __('Add')?></button>
      </div>
    </form>
  </div>
  <div class="col-md-9">
    <?php echo sp_alert_flashes('menus'); ?>
    <div id="menu-response">

    </div>
    <form method="post" action="?" class="card" data-parsley-validate>
        <?php echo $t['csrf_html']?>
      <div class="card-body">
            <div class="form-group">
            <label class="form-label" for="menu_name"><?= __('Menu Name'); ?></label>
            <input type="text" name="menu_name" id="menu_name" class="form-control" value="<?= sp_post('menu_name', $t['menu.menu_name']); ?>" maxlength="200" required>
            </div>

    <div class="cf nestable-lists py-4">
      <div class="py-2">
        <button type="button" data-action="expand-all" class="action btn btn-sm btn-outline-secondary"><?php echo __('Expand All'); ?></button>
        <button type="button" data-action="collapse-all" class="action btn btn-sm btn-secondary"><?php echo __('Collapse All'); ?></button>
      </div>
      <p class="form-text text-muted"><?php echo __('Add menu items and drag and move to re-arrange them.'); ?></p>
        <div class="dd" id="nestable">
            <?php echo $t['menu_html']; ?>
          </div>
        </div>


      <div class="form-group">
        <label class="form-label" for="menu_location"><?php echo __('Menu Locations'); ?></label>
        <?php if (empty($t['menu_locations'])) : ?>
          <span class="form-text text-muted"><?php echo __('Current theme has no registered menus.'); ?></span>
        <?php else : ?>
          <div class="custom-controls-stacked">
          <?php foreach ($t['menu_locations'] as $key => $description) : ?>
            <label class="custom-control custom-checkbox py-1">
              <input type="checkbox" class="custom-control-input" name="menu_locations[<?php echo e_attr($key); ?>]" value="<?= e_attr($t['menu.menu_id']); ?>" <?php echo checked($t['menu.menu_id'], get_active_menu_id($key)); ?>>
              <span class="custom-control-label"><?php echo e($description); ?></span>
            </label>
          <?php endforeach; ?>
        </div>

          <span class="form-text text-muted"><?php echo __('Choose the places where you want this menu to appear'); ?></span>
        <?php endif; ?>
      </div>
      </div>

      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary ml-auto"><?php echo __('Save')?></button>
      </div>
    </form>

  </div>
</div>
<input type="hidden" id="nestable-output">

<div class="modal fade" tabindex="-1" role="dialog" id="editMenuModal">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo __('Edit Menu Item'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" id="editMenuForm" action="" data-success-callback="editMenuCallback" data-response-target="#editMenuModalStatus" data-parsley-validate data-spark-ajax>
        <?= $t['csrf_html']; ?>
        <div class="modal-body">
          <div id="editMenuModalStatus"></div>
            <div class="form-group">
            <label class="form-label" for="item_label_modal"><?= __('Label'); ?></label>
             <input type="text" name="item_label" id="item_label_modal" class="form-control" maxlength="200" required>
             <span class="form-text text-muted"><?php echo __('Menu item name'); ?></span>
          </div>

            <div class="form-group">
            <label class="form-label" for="item_url_modal"><?= __('URL'); ?></label>
             <input type="text" name="item_url" id="item_url_modal" class="form-control" required>
             <span class="form-text text-muted"><?php echo __('Absolute URL or relative path'); ?></span>
          </div>

            <div class="form-group">
            <label class="form-label" for="item_class_modal"><?= __('HTML Classes'); ?></label>
             <input type="text" name="item_class" id="item_class_modal" class="form-control">
             <span class="form-text text-muted"><?php echo __('Custom HTML classes for the menu item (optional)'); ?></span>
          </div>

            <div class="form-group">
            <label class="form-label" for="item_icon_modal"><?= __('Icon ID'); ?></label>
             <input type="text" name="item_icon" id="item_icon_modal" class="form-control" maxlength="200">
             <span class="form-text text-muted"><?php echo __('Icon identifier for the menu (optional)'); ?></span>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><?php echo __('Save'); ?></button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo __('Close'); ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endblock(); ?>
<?php block('body_end'); ?>
  <script type="text/javascript">
    /**
     * Handles menu item drag and rearranges
     *
     * @param  Object e
     */
    var updateOutput = function(e)
    {
        var list   = e.length ? e : $(e.target),
            output = list.data('output');
            output.val(window.JSON.stringify(list.nestable('serialize')));
    };

    /**
     * Handles actions that are done after adding a menu item
     *
     * @param  Object response
     */
    function addMenuCallback(response)
    {
      $('#nestable').append(response.html);
      $('#addLinkForm').trigger('reset').parsley().reset();
    }

    /**
     * Handles actions that are done after updating a menu item
     *
     * @param  Object response
     */
    function editMenuCallback(response) {
      if (response.data.item_id) {
        // instant update on the screen as well
        $("#menu-item-label-" + response.data.item_id).text(response.data.item_label);
        $("#menu-item-url-" + response.data.item_id).text(response.data.item_url);

        // The data ids
        var actions = $('a[data-id="'+ response.data.item_id +'"]');

        actions.attr('data-label', response.data.item_label);
        actions.attr('data-url', response.data.item_url);
        actions.attr('data-class', response.data.item_class);
        actions.attr('data-icon', response.data.item_icon);
      }
    }

    /**
     * Binds the updateOutput function to nestable element
     */
    $(function() {
      $('#nestable').nestable({
        group: 1
      }).on('change', updateOutput);


    // output initial serialized data
    updateOutput($('#nestable').data('output', $('#nestable-output')));

    /**
     * Fires whenever menus are rearranged
     */
    $(document).on('change', '.dd', function (e) {
      var order_endpoint = '<?php echo e_attr(url_for('dashboard.menus.order_menu_post', ['id' => $t['menu.menu_id']])); ?>';

      var data = {
        data: $("#nestable-output").val(),
      };

      $spark.ajaxPost(order_endpoint, data, function (response) {
      });
    });

    /**
     * Handles expand and collapsing
     */
    $(document).on('click', '.action', function(e) {
      e.preventDefault();

      var target = $(this),
      action = target.data('action');
      if (action === 'expand-all') {
        $('.dd').nestable('expandAll');
      }
      if (action === 'collapse-all') {
        $('.dd').nestable('collapseAll');
      }
    });

    /**
     * Handles deletion of a menu item
     */
    $(document).on('click', '.delete-entry', function (e) {
        e.preventDefault();
        var endpoint = $(this).data('endpoint');
        var id = $(this).data('id');

        lnv.confirm({
          title: '<?php echo __("Confirm Removal"); ?>',
          content: '<?php echo __("Are you sure you want to remove this menu item?"); ?>',
          confirmBtnText: '<?php echo __("Confirm"); ?>',
          confirmHandler: function () {
            $spark.ajaxPost(endpoint, {}, function (response) {

              if (response.success) {
                $("li[data-id='" + id +"']").remove();
                // Update the output
                updateOutput($('#nestable').data('output', $('#nestable-output')));
              }

              var alert = $spark.buildAlert(response.message, response.type, response.dismissable);
              $('#menu-response').hide().html(alert).fadeIn();
            });
          },
          cancelBtnText: '<?php echo __("Cancel"); ?>',
          cancelHandler: function() {
          }
        })
      });


      // Handle edit modal
      $(document).on('click', '.edit-modal', function(e) {
        e.preventDefault();

        var menu_item = $(this);
        // We use attr instead of data() because the latter caches the value and we don't want that
        var menu_item_id = menu_item.attr('data-id');

        var form_action = "<?php echo e_attr(url_for('dashboard.menus.edit_menu_post', ['id' => ''])); ?>";
        form_action +=  menu_item_id;

        $('#editMenuForm').attr('action', form_action);

        $('#item_label_modal').val(menu_item.attr('data-label'));
        $('#item_url_modal').val(menu_item.attr('data-url'));
        $('#item_class_modal').val(menu_item.attr('data-class'));
        $('#item_icon_modal').val(menu_item.attr('data-icon'));

        $('#editMenuModal').modal('show');
      });

      // Reset modal form status
      $(document).on('hidden.bs.modal', '#editMenuModal', function(e) {
        $('#editMenuForm').trigger('reset').parsley().reset();
        $('#editMenuModalStatus').hide();
      });
    });


        jQuery(document).ready(function($) {
        var xhr;
        $('#item_label').autoComplete({
            source: function(term, response){
                try { xhr.abort(); } catch(e){}
                xhr = $.getJSON('<?php echo e_attr(url_for('dashboard.ajax.suggest_menu_items')); ?>', { q: term }, function(data){ response(data); });
            },
            renderItem: function (item, search) {
                search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
                return '<div class="autocomplete-suggestion" data-label="' + item.item_label + '" data-url="' + item.item_url + '">'+ item.item_label.replace(re, "<b>$1</b>") + '</div>';
            },
            onSelect : function (e, term, data) {
                e.preventDefault();
                var item = $(data);

                $('#item_label').val(item.data('label'));
                $('#item_url').val(item.data('url'));
            }
        });
    });


</script>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Update Menu'),
      'body_class' => 'menus menus-create',
      'page_heading' => __('Update Menu'),
      'page_subheading' => __('Modify existing menu.'),
    ]
);
