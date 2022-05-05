<?php if ($isCommentingEnabled && $canUserComment) { ?>
    <script>
        var NEWS_ID = <?=$news->getInt("id")?>;
    </script>
<?php } ?>
<div class="grid_5">
    <div class="box">
        <div class="innerbox">
            <div class="title" style="margin-bottom: 10px;">News</div>
            <ul class="newslist">
                <?php foreach ($newsList as $newsEntry) { ?>
                    <li><a<?php if ($newsEntry->getInt("id") == $news->getInt("id")) { ?> class="active" <?php } ?>
                                href="<?= $config->get("site", "url") ?>/articles/<?= $newsEntry->getInt("id") ?>"><?= $this->filter($newsEntry->get("title")); ?></a>
                    </li>
                <?php } ?>
            </ul>
            <div id="search_article">
                Nicht gefunden? <a href="<?= $config->get("site", "url") ?>articles/search">Artikel
                    suchen &raquo;</a>
            </div>
        </div>
    </div>
</div>
<div class="grid_11">
    <div class="box">
        <div class="innerbox">
            <div class="desc" style="float: right"><?php echo date('d.m.Y - H:i:s', $news->get("timestamp")); ?>
                Uhr
            </div>
            <div class="title"
                 style="font-size: 25px;margin-bottom: 10px;"><?php echo $this->filter($news->get("title")); ?></div>
            <div class="desc"><?php echo $this->filter($news->get("teaser")); ?></div>

            <br/>
            <span style="line-height: 150%;font-family: Ubuntu"><?= str_replace("%username%", $this->filter($username), $news->get("text")); ?></span>
            <br/>
            <br/>
            <?php
            if ($isVotingEnabled) {
                ?>
                <div style="float: right" id="vote_box">
                    <?php
                    if (($sum = $likes + $dislikes) > 0) {
                        $likesPercentage = $likes / $sum * 100;
                        $dislikesPercentage = $dislikes / $sum * 100;
                    } else {
                        $likesPercentage = 0;
                        $dislikesPercentage = 0;
                    }

                    $hasAlreadyVoted = $myVote != null;

                    ?>

                    <div class="status">
                        <div class="likes" style="width: <?= $likesPercentage ?>%"></div>
                        <div class="dislikes" style="width: <?= $dislikesPercentage ?>%"></div>
                    </div>
                    <div id="like"
                         <?php if (!$hasAlreadyVoted && $canUserVote) { ?>onclick="window.location.href='<?= $config->get("site", "url") ?>articles/<?= $news->getInt("id") ?>/vote/<?= base64_encode(1) ?>/<?= \System\Security\CSRF::getToken() ?>'"<?php } ?>
                         class="vote_button" <?= $hasAlreadyVoted && $myVote->getInt("type") == 1 ? 'style="font-weight: 900"' : '' ?>>
                        <i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Like <span
                                class="count_amount">(<?= $likes ?>)</span></div>
                    <div id="dislike"
                         <?php if (!$hasAlreadyVoted && $canUserVote) { ?>onclick="window.location.href='<?= $config->get("site", "url") ?>articles/<?= $news->getInt("id") ?>/vote/<?= base64_encode(0) ?>/<?= \System\Security\CSRF::getToken() ?>'"<?php } ?>
                         class="vote_button" <?= $hasAlreadyVoted && $myVote->getInt("type") == 0 ? 'style="font-weight: 900"' : '' ?>>
                        <i class="fa fa-thumbs-o-down" aria-hidden="true"></i> Dislike <span
                                class="count_amount">(<?= $dislikes ?>)</span></div>
                </div>
            <?php } ?>
            <div
                    style="font-weight: bold; font-size: 15px;">
                - <?php echo $news->getAuthor()->get("username"); ?></div>
        </div>
    </div>

    <!-- Kommentare -->
    <?php if ($isCommentingEnabled) { ?>
        <div id="error-msg" class="msg error" style="display: none"></div>
        <div id="success-msg" class="msg success" style="display: none">
            Dein Kommentar wurde erfolgreich gepostet!
        </div>
        <div class="box">
            <div class="innerbox">
                <div class="title">News kommentieren</div>
                <div class="desc">Schick uns deine Meinung zu diesen News!</div>

                <?php if ($canUserComment) { ?>
                    <textarea id="news-comment" style="height: 100px;width: 100%;resize: none"
                              class="input"></textarea>
                    <div id="send-button" class="button green" style="float: right; margin-top: 10px">Absenden</div>
                    <div style="clear: both"></div>

                <?php } else {
                    if ($myUser == null) {
                        ?>
                        <br/> Du musst eingeloggt sein um zu kommentieren.
                        <?php
                    } else {
                        ?>
                        <br/>
                        <div class="msg error">Dein Benutzerkonto wurde f&uuml;r die Kommentierung von News gesperrt.
                            Solltest du diese Sperrung für nicht richtig empfinden, so melde dich bei unserem <a
                                    href="<?= $config->get("site", "url") ?>/support">Support</a>.
                        </div>
                        <?php
                    }
                } ?>
            </div>
        </div>
    <?php } ?>
    <div id="news-comments">
        <?php
        $canVote = $myUser != null;
        $canDeleteComments = $myUser != null && $myUser->canDeleteComments();
        for ($i = 0;
             $i < count($comments);
             $i++) {

            $comment = $comments[$i];
            $user = $comment->getAuthor();
            $myVote = $myUser != null ? $comment->getVoteByUser($myUser) : null;
            $likes = $comment->getLikesCount();
            $dislikes = $comment->getDislikesCount();
            if ($i % 2 == 0) {
                ?>
                <div class="news-comment odd">
                    <div class="circle user"
                         style="background-image: url('https://www.habbo.nl/habbo-imaging/avatarimage?figure=<?= $this->filter($user->get("look")) ?>&direction=4')"></div>
                    <div class='text'>
                        <?php echo $this->filter($comment->get("text")); ?>
                        <br/>
                        <span style='float: right' class='desc'>geschrieben am <span
                                    style='font-weight: bold'><?= date('d.m.Y - H:i:s', $comment->get("timestamp")); ?></span> von <span
                                    style='font-weight: bold'><?= $this->filter($user->get("username")); ?></span></span>
                        <?php if ($canDeleteComments) { ?>
                            <span style="float: left; margin-right: 10px">
                                <a href="<?= $config->get("site", "url") ?>articles/<?= $news->getInt("id") ?>/delete/<?= base64_encode($comment->getInt("id")) ?>/<?= \System\Security\CSRF::getToken() ?>"><img
                                            src="<?= $config->get("site", "url") ?>public/images/articles/delete.gif"></a>
                            </span>
                        <?php } ?>
                        <?php if ($canVote) { ?>
                            <div style="float: left;font-size: 16px">
                                <div class="comment_vote <?= $myVote == null ? 'like' : ($myVote->getInt("type") == 1 ? 'voted' : 'not_voted') ?>">
                                        <span<?php if ($myVote == null) { ?> onclick="window.location.href='<?= $config->get("site", "url") ?>articles/comment/vote/<?= $news->getInt("id") ?>/<?= $comment->getInt("id") ?>/<?= base64_encode(1) ?>/<?= \System\Security\CSRF::getToken() ?>'"<?php } ?>><i
                                                    class="fa fa-thumbs-o-up"
                                                    aria-hidden="true"></i></span> <?= $likes ?>
                                </div>
                                <div class="comment_vote <?= $myVote == null ? 'dislike' : ($myVote->getInt("type") == 0 ? 'voted' : 'not_voted') ?>">
                                        <span<?php if ($myVote == null) { ?> onclick="window.location.href='<?= $config->get("site", "url") ?>articles/comment/vote/<?= $news->getInt("id") ?>/<?= $comment->getInt("id") ?>/<?= base64_encode(0) ?>/<?= \System\Security\CSRF::getToken() ?>'"<?php } ?>><i
                                                    class="fa fa-thumbs-o-down"
                                                    aria-hidden="true"></i></span> <?= $dislikes ?>
                                </div>
                            </div>
                        <?php } ?>
                        <br/>
                    </div>
                </div>
            <?php } else { ?>
                <div class="news-comment even">
                    <div class="circle user"
                         style="background-image: url('https://www.habbo.nl/habbo-imaging/avatarimage?figure=<?= $this->filter($user->get("look")) ?>')"></div>
                    <div class='text'>
                        <?php echo $this->filter($comment->get("text")); ?>
                        <br/>

                        <span style='float: right' class='desc'>geschrieben am <span
                                    style='font-weight: bold'><?= date('d.m.Y - H:i:s', $comment->get("timestamp")); ?></span> von <span
                                    style='font-weight: bold'><?= $this->filter($user->get("username")); ?></span></span>
                        <?php if ($canDeleteComments) { ?>
                            <span style="float: left; margin-right: 10px">
                                <a href="<?= $config->get("site", "url") ?>articles/<?= $news->getInt("id") ?>/delete/<?= base64_encode($comment->getInt("id")) ?>/<?= \System\Security\CSRF::getToken() ?>"><img
                                            src="<?= $config->get("site", "url") ?>public/images/articles/delete.gif"></a>
                            </span>
                        <?php } ?>
                        <?php if ($canVote) { ?>
                            <div style="float: left;font-size: 16px">
                                <div class="comment_vote <?= $myVote == null ? 'like' : ($myVote->getInt("type") == 1 ? 'voted' : 'not_voted') ?>">
                                        <span<?php if ($myVote == null) { ?> onclick="window.location.href='<?= $config->get("site", "url") ?>articles/comment/vote/<?= $news->getInt("id") ?>/<?= $comment->getInt("id") ?>/<?= base64_encode(1) ?>/<?= \System\Security\CSRF::getToken() ?>'"<?php } ?>><i
                                                    class="fa fa-thumbs-o-up"
                                                    aria-hidden="true"></i></span> <?= $likes ?>
                                </div>
                                <div class="comment_vote <?= $myVote == null ? 'dislike' : ($myVote->getInt("type") == 0 ? 'voted' : 'not_voted') ?>">
                                        <span<?php if ($myVote == null) { ?> onclick="window.location.href='<?= $config->get("site", "url") ?>articles/comment/vote/<?= $news->getInt("id") ?>/<?= $comment->getInt("id") ?>/<?= base64_encode(0) ?>/<?= \System\Security\CSRF::getToken() ?>'"<?php } ?>><i
                                                    class="fa fa-thumbs-o-down"
                                                    aria-hidden="true"></i></span> <?= $dislikes ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div style='clear: both'></div>
        <?php } ?>
    </div>
</div>