<?php

if ($count > 0) {

	?>

	<div class="mobile_display">

		<div id="item-list">

			<ul>

				<?php

				while($row = mysqli_fetch_object($query)) {

					$id_id = $row->id;

					if($row->reply == "1") {

						$id_id = $row->post;

					}

					$reply = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT COUNT(`id`) FROM `ticket`  WHERE `reply`='1' AND `reply_to`='".$row->id."'")));

					$topic_user = getUser($row->user);

					?>

					<li class="item-list pure-g category_home">

						<a href="<?=$setting["forum_url"]?>/view/<?=$id_id?>">

							<div class="topic-item-main">

								<div class="icon pure-u-3-24 hide_mobile">

									<?php

									if($topic_user->icon == "") {

										echo '<div class="item-thumb" style="background-color:#'.$topic_user->color.';">'.ucfirst(substr($topic_user->username,0,1)).'</div>';

									}
									else {

										echo '<img class="item-thumb" src="'.$setting["url"].'/common/'.$topic_user->icon.'">';

									}

									?>

								</div><div class="pure-u-15-24 topic-item-topic">

									<?php

									if($row->reply_to > 0) {

										$reply_to = mysqli_fetch_object(mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `ticket`.`user` FROM `ticket` WHERE `id` = '".$row->reply_to."'"));

										$reply_user = getUser($reply_to->user);

										if($reply_to->user != $row->user) {

											echo '<p class="reply_to">reply to <span class="reply_user">'.$reply_user->username.'</span></p>';

										}

									}

									?>

									<h5 class="topic-user-name"><?=limit_text($topic_user->username,3)?></h5>

									<span class="topic-date"><?=time_elapsed_string(strtotime($row->timestamp))?></span>

									<h5 class="topic-item-name">

										<?=limit_text($row->subject,9)?>

									</h5>

									<div class="item-desc"><?=limit_text(strip_tags($row->ticket),30)?></div>

								</div><div class="pure-u-3-24 topic-item-static">

									<?=$reply?> <b>REPLIES</b>

								</div><div class="pure-u-3-24 topic-item-static">

									<?=$row->views?> <b>VIEWS</b>

								</div>

							</div>

						</a>

					</li>

					<?php

					}

					?>

				</ul>

				<div class="pagination">

					<?php

					$previous = $page-1;

					$next = $page+1;

					$total = ceil($count / $limit);

					$url = $setting["admin_url"].'/topics/';

					if ($page > 1){

						echo '<li><a href="'.$url.$previous.'">Previous</a></li>';

					}

					for ($i = max(1, $page - 5); $i <= min($page + 5, $total); $i++) {

						echo '<li><a href="'.$url.$i.'">'.$i.'</a></li>';

					}

					if ($page < $total){

						echo '<li><a href="'.$url.$next.'">Next</a></li>';

					}

					?>

				</div>

			</div>

		</div>

		<?php

}
else { ?>

	<div>

		<h1>No post where found.</h1>

	</div><?php

}
?>
