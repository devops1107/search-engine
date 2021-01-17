<?php block('content'); ?>
<div class="row">
  <div class="col-12">

    <?php echo sp_alert_flashes('gallery'); ?>

    <?php if (current_user_can('manage_gallery')) : ?>
      <form action="<?php echo e_attr(url_for('dashboard.gallery.create_post')); ?>" class="dz my-4" id="gallery">
        <div class="dz-message dz-small"><strong class="h4"><?php echo __('Drop files here or click to upload'); ?></strong>
          <div class="dz-allowed mt-2">
            <p>
              <small class="d-block"><?php echo strtoupper(join(', ', $t['allowed_filetypes'])); ?></small>
              <small class="d-block"><?php echo sprintf(__('Maximum file size: %s MB'), $t['max_upload_size']); ?></small>
            </p>
          </div>
        </div>
        <?php echo $t['csrf_html']; ?>
      </form>
    <?php endif; ?>

    <div class="px-1 py-3">
      <div class="row align-items-center">

        <div class="col-4 text-left">
            <?php if (!empty($t['mime_rules'])) : ?>
            <div class="dropdown">
              <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><?php echo ucfirst(e($t['mime_type'])); ?>
              </button>
              <div class="dropdown-menu">
                <?php foreach ($t['mime_rules'] as $mime) : ?>
                  <a href="?<?php echo e_attr("page={$t['current_page']}&sort={$t['sort_type']}&type={$mime}{$t['query_str']}"); ?>" class="dropdown-item <?php echo $mime == $t['mime_type'] ? 'active' : '' ?>">
                    <?php echo ucfirst(e($mime)); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-8 ml-lg-auto text-right">
            <?php if (!empty($t['sorting_rules'])) : ?>
            <div class="dropdown">
              <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                <?php echo e(sprintf(__('Sort: %s'), sp_sort_label($t['sort_type']))); ?>
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <?php foreach ($t['sorting_rules'] as $sort) : ?>
                  <a href="?<?php echo e_attr("page={$t['current_page']}&sort={$sort}&type={$t['mime_type']}{$t['query_str']}"); ?>" class="dropdown-item <?php echo $sort == $t['sort_type'] ? 'active' : '' ?>">
                    <?php echo e(sp_sort_label($sort)); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
        </div>


      </div>
    </div>

    <?php if (!empty($t['list_entries'])) :?>
      <div class="row" id="gallery-content">
        <?php foreach ($t['list_entries'] as $item) :?>
            <?php insert('admin::gallery/partials/gallery_loop.php', ['item' => $item]); ?>
        <?php endforeach; ?>
      </div>
    <?php else : ?>
      <div class="row" id="gallery-content">
        <div class="text-center py-8 col-12 no-entries">
          <span class="h3 text-muted"><?php echo __('No entries found.'); ?></span>
        </div>
      </div>
    <?php endif; ?>

    <div id="load-more" class="py-2 text-center mt-5">
          <h3 class="h3 text-muted" id="end-of-result" style="display:none"><?php echo __('End of results.'); ?></h3>
    </div>
  </div>
</div>

<!-- Attachment Preview Modal -->
<div class="modal modal-gallery animate slow fadeInDown p-0" id="preview-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-full" role="document">
    <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      <div class="modal-body py-0">
        <div class="row" style="min-height:100vh">
          <div class="col-md-8 p-0">
            <div id="preview-image" class="mb-lg-0 mb-2 text-center"></div>
          </div>
          <div class="col-md-4 mt-lg-2 d-md-flex flex-column justify-content-center px-md-5">
            <div class="form-group">
              <label class="form-label"><?php echo __('File Name'); ?></label>
              <div id="content-name" class="form-control-plaintext text-muted"></div>
            </div>
            <div class="form-group">
              <label class="form-label"><?php echo __('File Size'); ?></label>
              <div id="readable-size" class="form-control-plaintext text-muted"></div>
            </div>
            <div class="form-group">
              <label class="form-label" for="preview-url"><?php echo __('Absolute URL'); ?> <?php echo svg_icon('copy', 'ml-1 svg-md'); ?></label>
              <input readonly class="form-control" type="text" data-copy-to-clipboard data-trigger="focus" data-toggle="tooltip" title="<?php echo e_attr(__('Copied to clipboard!')); ?>" id="preview-url">
            </div>
            <div class="form-group">
              <label class="form-label" for="relative-url"><?php echo __('Relative URL'); ?> <?php echo svg_icon('copy', 'ml-1 svg-md'); ?></label>
              <input readonly class="form-control" type="text" data-copy-to-clipboard data-trigger="focus" data-toggle="tooltip" title="<?php echo e_attr(__('Copied to clipboard!')); ?>" id="relative-url">
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
  var items_per_page = <?php echo (int) $t['items_per_page']; ?>;
  var current_page = <?php echo (int) $t['current_page']; ?>;
  var present_items_count = <?php echo (int) $t['present_items_count']; ?>;
  var total_items = <?php echo (int) $t['total_items']; ?>;

  var request_pending = false;

  $(function() {

    $('#preview-modal').on('hidden.bs.modal', function () {
      $('#preview-image').html('');
    });

    $(document).on('click', '.view-entry', function (e) {
      e.preventDefault();
      var item = $(this);
      var filetype = item.data('filetype');
      var image = $('#preview-img-src');

      $('#preview-url').val(item.data('url'));
      $('#relative-url').val(item.data('relative-url'));
      $('#readable-size').text(item.data('size'));
      $('#content-name').text(item.data('name') + '.' + item.data('ext'));
      $('#preview-modal').modal('show');

      var imgSrc = item.data('thumbnail');

      if (filetype == 'image') {
        imgSrc = item.data('url');
      }


      var newImage = document.createElement('img');
      newImage.src = imgSrc;
      $('#preview-image').html(newImage);
    });


    $(document).on('click', '.delete-entry', function (e) {
      e.preventDefault();
      var endpoint = $(this).data('endpoint');

      lnv.confirm({
        title: '<?php echo __("Confirm Deletion"); ?>',
        content: '<?php echo __("Are you sure you want to delete this gallery item?"); ?>',
        confirmBtnText: '<?php echo __("Confirm"); ?>',
        confirmHandler: function () {
          $spark.ajaxPost(endpoint, {}, function (response) {
            if (response.success) {
              // Fade-out nicely, then remove the entire element
              $('#gallery-item-' + response.id).fadeOut().remove();
            }
          });
        },
        cancelBtnText: '<?php echo __("Cancel"); ?>',
        cancelHandler: function() {
        }
      })
    });

    $('#gallery').dropzone({
      paramName: "file",
      maxFilesize: "<?php echo e_attr($t['max_upload_size']); ?>",
      createImageThumbnails: true,
      addRemoveLinks: false,
      acceptedFiles: ".<?php echo e_attr(join(',.', $t['allowed_filetypes'])); ?>",
      accept: function(file, done) {
        done();
      },
      params: {
        mode: "gallery",
      },
      success: function (dropzone, response) {
        if (response.html) {
          $('#gallery-content').prepend(response.html);
          $('.no-entries').hide();
          total_items++;
        }
      },
      init: function() {
        this.on('queuecomplete', function () {
        });
      }
    });

    $(document).on('inview', '#load-more', function(event) {
      event.preventDefault();
    
      // no point then
      if (total_items < items_per_page || present_items_count >= total_items) {
        $('#end-of-result').show();
        return;
      }

      if (request_pending) {
        console.log('request already pending');
        return;
      }
      request_pending = true;

      $spark.ajaxLoader('show');

      current_page++;

      var data = {
        sort: "<?php echo e_attr($t['sort_type']); ?>",
        type: "<?php echo e_attr($t['mime_type']); ?>",
        page: current_page
      };

      $.ajax({cache: false, type: 'GET', url: '?', data: data}).done(function (response) {
        $('#gallery-content').append(response.html);
        request_pending = false;
        present_items_count = present_items_count + response.present_items_count;
      }).fail(function (response) {
        $spark.ajaxError('show');
      }).always(function (res) {
        $spark.ajaxLoader('hide');
      });
    });
  });
</script>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
    'title' => __('Gallery'),
    'body_class' => 'gallery gallery-list',
    'page_heading' => __('Gallery'),
    'page_subheading' => __('Manage gallery.'),
    ]
);
