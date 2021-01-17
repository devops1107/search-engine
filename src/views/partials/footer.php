<footer class="footer border-0 px-3 mt-0">
  <div class="container-fluid">
    <div class="row align-items-center flex-row-reverse">
      <div class="col-12 col-sm-auto ml-lg-auto">
        <ul class="list-inline list-inline-dots mb-0">
          <li class="list-inline-item"><a href="<?php echo e_attr(url_for('dashboard.credits')); ?>"><?php echo __('Licenses')?></a></li>
          <li class="list-inline-item"><a href="https://gitHub.com/MirazMac" target="_blank"><?php echo __('GitHub')?></a></li>
          <li class="list-inline-item"><a href="https://fb.me/MirazMac" target="_blank"><?php echo __('Facebook')?></a></li>
          <li class="list-inline-item"><a href="https://twitter.com/miraz_mac" target="_blank"><?php echo __('Twitter')?></a></li>
        </ul>
      </div>
      <div class="col-auto">
      </div>
      <div class="col-12 col-lg-auto mt-3 mt-lg-0">
        <?php echo __('Powered by:'); ?>
        <?php echo e(APP_NAME); ?>
        v<?php echo e(APP_VERSION); ?>
      </div>
    </div>
  </div>
</footer>
