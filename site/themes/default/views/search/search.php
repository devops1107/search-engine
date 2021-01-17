<?php
/**
 * Template for the search results page
 */
defined('SPARKIN') or die('xD');
?>

<script type="text/javascript"><?php echo $t['cse_script']; ?></script>

<div class="container search-container px-sm-3 px-1">
    <div class="row no-gutters">
        <!-- Full width column for image results, otherwise sized one -->
        <div class="<?php echo $t['is_image'] ? 'col-12' : 'col-sm-7 pr-sm-4 order-1 order-sm-0'; ?>">
            <div id="search-loader" class="p-4 text-center">
                <div class="progress-circular progress-circular-primary">
                  <div class="progress-circular-wrapper">
                    <div class="progress-circular-inner">
                      <div class="progress-circular-left">
                        <div class="progress-circular-spinner"></div>
                    </div>
                    <div class="progress-circular-gap"></div>
                    <div class="progress-circular-right">
                        <div class="progress-circular-spinner"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ad-unit">
        <?php echo get_option('ad_unit_1'); ?>
    </div>

    <?php if ($t['ia_view']) : ?>
        <div class="my-3" id="instant-answer" style="display:none">
            <?php insert("answers/{$t['ia_view']}"); ?>
        </div>
    <?php endif; ?>

    <?php echo $t['cse_element']; ?>
    <h></h>
    <div class="ad-unit">
        <?php echo get_option('ad_unit_2'); ?>
    </div>

</div>

<?php if (!$t['is_image']) : ?>
<div class="col-sm-4 px-sm-3 order-0 order-sm-1">
    <?php insert('search/partials/rich_card.php'); ?>

    <div class="ad-unit">
        <?php echo get_option('ad_unit_3'); ?>
    </div>
</div>
<?php endif; ?>

</div>
<!-- ./row-->

</div>
<!-- ./container -->
<script>


    // var x = document.getElementById("adBlock");
    // var y = x.getElementsByTagName("div");
    // var i;
    // console.log(x);
    // for (i = 0; i < y.length; i++) {
    //     y[i].style.backgroundColor = "red";
    // }
    // $(document).on('click', '#adBlock', function(){
    //
    // });
</script>
