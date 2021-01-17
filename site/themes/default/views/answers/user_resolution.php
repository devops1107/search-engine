<?php
/**
 * Template for useragent instant answer
 */
defined('SPARKIN') or die('xD');
?>
<div class="card instant-answer-card">
    <div class="card-body">
        <h4 class="card-title"><?php echo __('your-resolution-is', _T); ?></h4>
        <p><span class="user-resolution-width"></span>x<span class="user-resolution-height"></span></p>
    </div>
</div>
<script type="text/javascript">
    $('.user-resolution-width').text(localizeNumbers(window.screen.width));
    $('.user-resolution-height').text(localizeNumbers(window.screen.height));
</script>
