<?php
/**
 * Template for flip coin instant answer
 */
defined('SPARKIN') or die('xD');
?>
<div class="card instant-answer-card">
    <div class="card-body">
        <h4 class="card-title"><?php echo __('flip-coin-result', _T); ?></h4>
        <p><?php echo __("flip-coin-{$t['ia_data']}", _T); ?></p>
    </div>
</div>
