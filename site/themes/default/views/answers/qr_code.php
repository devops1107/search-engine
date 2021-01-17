<?php
/**
 * Template for qrcode instant answer
 */
defined('SPARKIN') or die('xD');
?>
<div class="card instant-answer-card">
    <div class="card-body">
        <h4 class="card-title"><?php echo __('qrcode-for', _T, ['q' => e($t['ia_term'])]); ?></h4>
        <p><img src="<?php echo $t['ia_data']; ?>" class="qr-image"></p>
    </div>
</div>
