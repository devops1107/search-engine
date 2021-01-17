<?php if (!empty($t['answer.Abstract'])) : ?>
    <?php $count = 0; ?>
    <div class="card my-3 infobox-card" id="infobox-list">
        <div class="card-body">
            <?php if ($t['answer.Image']) : ?>
                <?php if (!filter_var($t['answer.Image'], FILTER_VALIDATE_URL)) : ?>
                    <?php $t['answer.Image'] = 'https://duckduckgo.com' . $t['answer.Image']; ?>
                <?php endif; ?>
                <img src="<?= e_attr($t['answer.Image']); ?>" class="abstract-img mb-2 float-right ml-2">
            <?php endif; ?>
            <h4 class="card-title"><?= $t['answer.Heading']; ?></h4>
            <p class="card-text"><?= limit_words($t['answer.Abstract'], 30); ?>
                <?php if (!empty($t['answer.AbstractSource'])) : ?>
                    <a href="<?= e_attr($t['answer.AbstractURL']); ?>" rel="nofollow noreferer" target="_blank" class="font-weight-bold">
                        <?= e($t['answer.AbstractSource']); ?>
                    </a>
                <?php endif; ?>
            </p>

            <?php if (is_array($t['answer.Infobox.content'])) : ?>
                <?php $t['infobox_count'] = count($t['answer.Infobox.content']); ?>
                <ul class="list-unstyled infobox m-0">
                    <?php

                    $socials  = [];

                    foreach ($t['answer.Infobox.content'] as $t['info']) :?>
                        <?php

                        switch ($t['info.data_type']) {
                            case 'facebook_profile':
                                $socials[1] = [
                                'label' => $t['info.label'],
                                'url'   => "https://facebook.com/{$t['info.value']}",
                                'class' => 'facebook'
                                ];
                                break;
                            case 'twitter_profile':
                                $socials[2] = [
                                'label' => $t['info.label'],
                                'url' => "https://twitter.com/{$t['info.value']}",
                                'class' => 'twitter'
                                ];
                                break;
                            case 'instagram_profile':
                                $socials[3] = [
                                'label' => $t['info.label'],
                                'url' => "https://instagram.com/{$t['info.value']}",
                                'class' => 'instagram'
                                ];
                                break;
                            case 'youtube_channel':
                                $socials[4] = [
                                'label' => $t['info.label'],
                                'url' => "https://youtube.com/channel/{$t['info.value']}",
                                'class' => 'youtube',
                                ];
                                break;
                            case 'github_profile':
                                $socials[5] = [
                                'label' => $t['info.label'],
                                'url' => "https://github.com/{$t['info.value']}",
                                'class' => 'github'
                                ];
                                break;
                        }

                        if ($t['info.data_type'] != 'string') {
                            continue;
                        }

                        if (mb_strtolower($t['info.label']) === 'website') {
                            $t['info.value'] = trim($t['info.value'], '[]');
                            $socials[0] = [
                            'label' => $t['info.label'],
                            'url' => "http://{$t['info.value']}",
                            'class' => 'website',
                            ];
                        }

                        $count++;
                        ?>
                    <li class="text-truncate py-1" title="<?= e_attr($t['info.value']); ?>">
                        <span class="<?php echo darkmode_value('text-dark', 'text-muted'); ?> font-weight-bold"><?= e($t['info.label']) ?>:</span>&nbsp;
                        <span class="card-text"><?= e($t['info.value']) ?></span>
                    </li>
                    <?php endforeach; ?>
                <li class="text-truncate py-1">
                    <span class="<?php echo darkmode_value('text-dark', 'text-muted'); ?> font-weight-bold"><?= __('data-source', _T); ?></span>&nbsp;
                    <span class="card-text">
                        <a href="https://duckduckgo.com" rel="external" target="_blank" class="<?php echo darkmode_value('text-dark', 'text-muted'); ?>">DuckDuckGo</a></span>
                    </li>
                </ul>

                <?php if (has_items($socials)) : ?>
                    <ul class="d-flex list-unstyled flex-wrap m-0 justify-content-center py-1 mt-2">
                        <?php
                        ksort($socials);

                        foreach ($socials as $id => $social) : ?>
                        <li>
                            <a href="<?= e_attr($social['url']); ?>" class="btn btn-outline-provider-<?php echo e_attr($social['class']); ?> border-0
                                btn-outline-<?= $social['class']; ?>" target="_blank" rel="nofollow" title="<?= e_attr($social['label']); ?>" data-toggle="tooltip">
                                <?= svg_icon("logo-{$social['class']}", 'svg-sm'); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            <?php endif; ?>

    </div>


    <?php if ($count > 3) : ?>
        <div class="card-footer p-0 <?php echo darkmode_value('bg-light', 'bg-dark'); ?>">
            <button class="border-0 btn-block btn-light rounded-0 shadow-none bg-transparent infobox-toggle py-1 <?php echo darkmode_value('', 'text-muted'); ?>" data-toggle="class" data-target="#infobox-list" data-classes="infobox-expanded">
                <?= svg_icon('keyboard-arrow-down', 'svg-md infobox-toggle-icon'); ?>
            </button>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
