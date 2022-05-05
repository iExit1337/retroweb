<div class="grid_4">
	<div class="box">
		<div class="innerbox">
			<a href="<?= $config->get("site", "url") ?>messages/create">
				<div class="button green" style="width: 100%">Nachricht verfassen</div>
			</a>
			<div style="clear: both"></div>
		</div>
	</div>
</div>
<div class="grid_12">
	<div class="box">
		<div class="innerbox">
			<div class="title">Meine Nachrichten</div>
			<div class="desc" style="margin-bottom: 10px">Hier werden all deine erhaltenen und versendeten Nachrichten
				aufgelistet.
			</div>
			<?php if (count($messagesTopics) == 0) { ?>
				<div style="text-align: center">
					Du hast noch keine Nachricht erhalten oder verfasst.
				</div>

				<div style="clear: both"></div>
				<a href="<?= $config->get("site", "url") ?>messages/create" style="text-decoration: none">
					<div class="button green" style="width: 200px; margin: 0 auto;float: none">Nachricht verfassen</div>
				</a>
				<div style="clear: both"></div>
			<?php } else {
				foreach ($messagesTopics as $topic) {
					$latestMessage = $topic->getLatestMessage();
					$isUnseen = $topic->hasUnreadMessages($myUser);
					?>
					<div class="message<?= $isUnseen ? ' new' : '' ?>">
						<div class="subject"><i class="fa fa-<?= $isUnseen ? 'envelope' : 'envelope-open-o' ?>"
									aria-hidden="true"></i>
							RE:
							<a href="<?= $config->get("site", "url") ?>messages/<?= $topic->getInt("id") ?>"><?= $this->filter($topic->get("subject")) ?></a>
						</div>
						<div class="subscribers">
							<?php foreach ($topic->getSubscriberEntries() as $subscriberEntry) { ?>
								<div class="subscriber"><?= $this->filter($subscriberEntry->getUser()
																						  ->get("username")) ?></div>
							<?php } ?>
						</div>
						<div class="latest_message">
							<div class="sender_name"><?= $this->filter($latestMessage->getUser()->get("username")) ?>:
							</div>
							<div class="sender_avatar"
									style="background-image: url(https://www.habbo.nl/habbo-imaging/avatarimage?figure=<?= $this->filter($latestMessage->getUser()
																																					   ->get("look")) ?>&head_direction=4&direction=4)"></div>
							<?= $this->filter($this->summary($latestMessage->get("message"), 80)) ?>
						</div>
					</div>

				<?php }
			} ?>
		</div>
	</div>
</div>