<?php
/**
 * Main container for the theme
 *
 * This template acts as a global container of the frontend which
 * combines all the necessary parts of the site and outputs it .
 */
defined('SPARKIN') or die('xD');
?>
<!DOCTYPE html>
<html class="<?php echo e_attr($t['html_class']); ?>" dir="<?php echo e_attr($t['locale_direction']); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge,chrome=1">
    <meta name="theme-color" content="#070404">
    <link rel="preconnect" href="//fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="//fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="//cse.google.com" crossorigin>
    <link rel="preconnect" href="//www.google.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cse.google.com">
    <link rel="dns-prefetch" href="//www.google.com">
    <?php
    /**
     * Dynamic header assets
     */
    sp_head();

    // Partial header scripts
    insert('shared/head_scripts.php');
    ?>
</head>

<body class="<?php echo e_attr($t['body_class']); ?>">
    <?php
    /**
     * SVG Sprites
     */
     insert('shared/sprites.svg');
    ?>

    <!-- Ajax Loader -->
    <div id="ajax-loader" role="bar" style="width:0;display:none" aria-hidden="true"><div class="peg"></div></div>
    <div id="ajax-loader-infinite" role="bar" style="display:none" aria-hidden="true">
        <div class="progress">
          <div class="progress-bar progress-bar-indeterminate" role="progressbar"></div>
      </div>
  </div>


    <header id="header">
    <?php
    /**
     * Site header
     */
    if (!$t['hide_header']) {
        echo $t['site_header'];
    }
    ?>
    </header>

    <main id="content" class="main-content">
    <?php
    /**
     * Site content
     */
    echo $t['site_content'];
    ?>


    </main>

    <footer id="footer">
    <?php
    /**
     * Site footer
     */
    if (!$t['hide_footer']) {
        echo $t['site_footer'];
    }
    ?>
    </footer>
    <?php
    /**
     * Dynamic footer assets
     */
    sp_footer();
    ?>

<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
    <script>
        $(window).on('load', function() {

            var adsIframe = $('.search-results').find('iframe').css( "color", "red" );

            var adsblock = adsIframe.contents().find("#adBlock").html();
            $( "*", document.body ).click(function( event ) {

                event.stopPropagation();
                $( "span" ).first().text( "Clicked on - " + domElement.nodeName );

            });

            // $(body).find('iframe')
            // reportHtml = $('#report_iframe').contents().find("#report-template").html();
            // var x = document.getElementById("adBlock");
            // var y = x.getElementsByTagName("div");
            // var i;
            // for (i = 0; i < y.length; i++) {
            //     y[i].style.backgroundColor = "red";
            // }
        });
        $(document).ready(function(){

        });
        // $(document).on('click','a',function() {
        //     var x  = $('#adBlock').children();
        //     console.log(x);
        //     $2y$10$JiiRJJQhDct0Mbt1vsKBNuXY4qtotiudK8AyjTrbTUuvua.lmv0CG
        //     alert("ooooooooookkkkkkkkkkkkkkkkkk");
        // });
    </script>
</body>
</html>
