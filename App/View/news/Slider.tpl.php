<div class="grid_<?= $grid ?>">
    <div id="newsslider" class="box">
        <div id='newsslider-overlay'></div>
        <?php for ($i = 1; $i <= $newsCount; $i++) {
            $currentNews = $news[$i - 1]; ?>
            <div news-real-link="<?= $currentNews->getInt("id") ?>" news-id="<?= $i ?>"
                 class="news-slide-box <?= $i == 1 ? 'show' : 'hide'?>"
                 style="background-image: url('<?= str_replace('%path%', $config->get('site', 'url'), $currentNews->get("image")) ?>')">
                <a href="<?= $config->get('site', 'url') ?>articles/<?= $currentNews->getInt("id") ?>">
                    <div class='news-title'><?= $this->filter($currentNews->get("title")) ?></div>
                </a>
                <div class='news-desc'><?= $this->filter($currentNews->get("teaser")) ?></div>
            </div>
        <?php } ?>
        <div id='navigationbar'>
            <div id='news-go-to-previous'>
                &laquo; Vorherige
            </div>
            <div id='news-go-to-next'>
                Nächste &raquo;
            </div>
        </div>
    </div>
</div>
<script>
    var availableSlides = <?=$newsCount?>;
    var newsID = $('div[news-id="1"]').attr('news-real-link');
</script>
