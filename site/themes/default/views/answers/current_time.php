<?php
/**
 * Template for current time instant answer
 */
defined('SPARKIN') or die('xD');
?>
<div class="card instant-answer-card">
    <div class="card-body">
        <h4 class="card-title"><?php echo __('current-time-is', _T); ?></h4>
        <p class="lead mb-1"><span class="current-time"></span></p>
        <p class="card-text small"><span class="current-date"></span></p>
    </div>
</div>
<script type="text/javascript">
    var today = new Date();
    var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
    var day = today.getDay();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var monthKey = 'month-' + mm;
    var dayKey = 'day-' + day;
    var yyyy = today.getFullYear();
    day = locale[dayKey];
    var date = day + ', ' + localizeNumbers(dd) + ' ' + locale[monthKey] + ', ' + localizeNumbers(yyyy);

    $('.current-time').text(localizeNumbers(time));
    $('.current-date').text(date);
</script>
