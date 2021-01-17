<?php
/**
 * Template for base64 encode instant answer
 */
defined('SPARKIN') or die('xD');
?>
<div class="card instant-answer-card">
    <div class="card-body">
        <h4 class="card-title"><?php echo __('base64-encode-for', _T, ['q' => e($t['ia_term'])]); ?></h4>
        <p><?php echo $t['ia_data']; ?></p>
    </div>
</div>
