<?php block('content'); ?>
<div class="row">
  <div class="container">

    <?php echo sp_alert_flashes('engines'); ?>
<div class="card">
    <div class="card-body">
        <p class="text-muted">
            <?php echo __('Drag and adjust the order of the engines and save from below.'); ?>
        </p>
<?php if (has_items($t['engines'])) : ?>
    <ol class="dd-list" id="items">
        <?php foreach ($t['engines'] as $engine) : ?>
            <li class="dd-item mb-2" data-id="<?php echo e_attr($engine['engine_id']); ?>"><div class="dd-handle"><?php echo e($engine['engine_name']); ?>
                        <?php if (get_option('default_engine') == $engine['engine_id']) : ?>
                            <span class="badge badge-info"><?php echo __('Default'); ?></span>
                        <?php endif; ?></div></li>
        <?php endforeach; ?>
    </ol>
<?php endif; ?>

</div>

<form method="post" action="?">
    <?php echo $t['csrf_html']; ?>
    <input type="hidden" name="order" id="order" value='<?php echo json_encode($t['engine_order'], JSON_UNESCAPED_SLASHES); ?>'>

      <div class="card-footer text-right">
        <button type="submit" class="btn btn-secondary ml-auto"><?php echo __('Save')?></button>
      </div>
</form>
</div>
</div>
</div>
<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
    var orders_el = $('#order');

    jQuery(document).ready(function($) {
        var el = document.getElementById("items");
        var options = {
            ghostClass: 'blue-background-class',
            onUpdate: function (e) {
                orders_el.val(window.JSON.stringify(sortable.toArray()));
            },
        };
        var sortable = Sortable.create(el, options);
    });
</script>
<?php endblock(); ?>

<?php
extend(
    'admin::layouts/skeleton.php',
    [
    'title' => __('Reorder Engines'),
    'body_class' => 'engines engines-reorder',
    'page_heading' => __('Reorder Engines'),
    'page_subheading' => __('Manage the order of the engines.'),
    ]
);
