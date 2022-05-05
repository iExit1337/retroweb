<?= $navigation ?>
<div class="grid_11">
    <div class="box">
        <div class="innerbox">
            <a href="<?= $config->get("site", "url") ?>admin/homepage/news/add">
                <div class="button green" style="float: right">News verfassen</div>
            </a>
            <div class="title" style="margin-bottom: 25px">News</div>
            <?php
            foreach ($newsList as $news) {
                ?>
                <div class="news" style="cursor:pointer;"
                     onclick="window.location.href='<?= $config->get("site", "url") ?>admin/homepage/news/edit/<?= $news->getInt("id") ?>'">
                    <div class="image"
                         style="background-image: url(<?= str_replace('%path%', $config->get('site', 'url'), $news->get("image")) ?>)"></div>
                    <div class="title"><?= $this->filter($news->get("title")) ?></div>
                    <div class="desc"><?= $this->filter($news->get("teaser")) ?></div>
                </div>

            <?php } ?>
        </div>
    </div>
</div>
