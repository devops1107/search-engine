<script type="text/javascript">
  $(function() {

    <?php echo sp_dropzone_js('#img-uploader', '#feat_image'); ?>

    <?php if (!$t['update_page']) : ?>
    var slug_input = $('#content_slug');
    var title_input = $('#content_title');

    title_input.on('keyup', function (e) {
      slug = title_input.val();
      slug = $spark.slugify(slug);
      slug_input.val(slug);
      return true;
    });

    slug_input.on('blur', function (e) {
      slug = slug_input.val();
      slug = $spark.slugify(slug);
      slug_input.val(slug);
      return true;
    });
    <?php endif; ?>

    $('#content_body').trumbowyg({
      autogrow: true,
      btnsDef: {
        image: {
          dropdown: ['insertImage', 'upload'],
          ico: 'insertImage'
        },
      },
      btns: [
      ['viewHTML'],
      ['formatting'],
      ['strong', 'em', 'del'],
      ['link'],
      ['image'],
      ['justifyLeft', 'justifyCenter', 'justifyRight'],
      ['unorderedList', 'orderedList'],
      ['horizontalRule'],
      ['removeformat'],
      ['fullscreen']
      ],

      plugins: {
        upload: {
          serverPath: "<?php echo e_attr(url_for('dashboard.gallery.create_post')); ?>",
          fileFieldName: 'file',
          urlPropertyName: "content_url",
          data: [{name: 'csrf_token', value: "<?php echo $t['csrf_token']?>"}],
        }
      },
  });
  });
</script>
