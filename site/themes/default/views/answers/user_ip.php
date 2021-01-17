<?php
/**
 * Template for user IP instant answer
 */
defined('SPARKIN') or die('xD');
?>
<div class="card instant-answer-card">
    <div class="card-body">
        <h4 class="card-title"><?php echo __('your-ip-is', _T); ?></h4>
        <p><?php echo $t['ia_data']; ?></p>
    </div>
</div>
