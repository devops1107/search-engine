<div class="container search-container py-4">
    <div class="row">
        <div class="col-md-5">
            <?php echo sp_alert_flashes('preferences', true, false); ?>
            <form method="POST" action="<?php echo e_attr(url_for('site.preferences_post')); ?>" data-ajax-form="true">
                <?php echo $t['csrf_html']; ?>

                <label class="form-label mb-3" for="darkmode"><?php echo __('darkmode', _T); ?></label>
                <div class="form-group">
                    <div class="pl-3">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="darkmode" value="0">
                          <input type="checkbox" class="custom-control-input" id="darkmode" name="darkmode" value="1" <?php checked(1, $t['preferences.darkmode']); ?>>
                          <label class="custom-control-label" for="darkmode" name="darkmode"><?php echo __('enable-dark-mode', _T); ?></label>
                      </div>
                  </div>
              </div>


                <label class="form-label mb-3" for="backgrounds"><?php echo __('show-backgrounds', _T); ?></label>
                <div class="form-group">
                    <div class="pl-3">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="backgrounds" value="0">
                          <input type="checkbox" class="custom-control-input" id="backgrounds" name="backgrounds" value="1" <?php checked(1, $t['preferences.backgrounds']); ?>>
                          <label class="custom-control-label" for="backgrounds" name="backgrounds"><?php echo __('show-backgrounds-in-homepage', _T); ?></label>
                      </div>
                  </div>
              </div>

                <label class="form-label mb-3" for="new_window"><?php echo __('where-results-open', _T); ?></label>
                <div class="form-group">
                    <div class="pl-3">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="new_window" value="0">
                          <input type="checkbox" class="custom-control-input" id="new_window" name="new_window" value="1" <?php checked(1, $t['preferences.new_window']); ?>>
                          <label class="custom-control-label" for="new_window" name="new_window"><?php echo __('open-links-in-new-tab', _T); ?></label>
                      </div>
                  </div>
              </div>
              <label class="form-label mb-3" for="search_autocomplete"><?php echo __('search-autocomplete', _T); ?></label>
              <div class="form-group">
                <div class="pl-3">
                    <div class="custom-control custom-switch">
                      <input type="hidden" name="search_autocomplete" value="0">
                      <input type="checkbox" class="custom-control-input" id="search_autocomplete" name="search_autocomplete" value="1" <?php checked(1, $t['preferences.search_autocomplete']); ?>>
                      <label class="custom-control-label" for="search_autocomplete" name="search_autocomplete"><?php echo __('show-suggestions-as-you-type', _T); ?></label>
                  </div>
              </div>
          </div>

          <label class="form-label mb-3"><?php echo __('safesearch-filters', _T); ?></label>
          <div class="form-group">
              <?php foreach ($t['safesearch_types'] as $type) : ?>
                <div class="custom-control custom-radio">
                  <input type="radio" id="safesearch-<?php echo e_attr($type); ?>" name="safesearch" class="custom-control-input" value="<?php echo e_attr($type); ?>" <?php checked($type, $t['preferences.safesearch']); ?>>
                  <label class="custom-control-label d-block" for="safesearch-<?php echo e_attr($type); ?>"><?php echo __($type, _T); ?></label>
              </div>
              <?php endforeach; ?>

      </div>

      <div class="form-group">
          <label class="form-label" for="language"><?php echo __('language', _T); ?></label>
          <select class="custom-select" name="language" id="language">
            <?php foreach (get_theme_locales() as $key => $locale) : ?>
                <option value="<?php echo e_attr($key); ?>" <?php echo $locale['active'] ? 'selected' : ''; ?>>
                    <?php echo e($locale['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="btn-text"><?php echo __('save-preferences', _T); ?></span>
        </button>
    </div>

</form>
</div>
</div>
</div>
