<?php

$title="Setting";

$row = mysqli_fetch_object(mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `users` WHERE `id`='".$user->id."'"));

$count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

if ($count > 0) {

	ini_set('memory_limit', '1024M');

	ini_set('max_execution_time', 300);

	require_once('include/header.php');

	require_once('include/main_header.php');

	if (isset($_POST['submit'])) {

		$errors = array();

		$email = $_POST["email"];

		$password = $_POST["password"];

		$street = $_POST["street"];

		$city = $_POST["city"];

		$state = $_POST["state"];

		$zip = $_POST["zip"];

		$country = $_POST["country"];

		if($email !== $row->email) {

			mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `email` FROM `users` WHERE `email`='".$email."'");

			$email_count=array_pop(mysqli_fetch_array(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

			if($email_count > 0) {

				$errors[]="email already exists.";

			}


			if (!validate_email($email)) {

				$errors[]="please insert a valid email.";

  			}

		}

		if(!$email) {

			$errors[]="please make sure that you have correctly filled in all of the fields.";

		}


		if($password !== "") {

			$salt = randomurl(randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"]));

			$password = md5($salt . $password);

			$passwordsalt = "`password`='".$password."', `salt`='".$salt."', ";

		}

		if(isset($_FILES["thumb"])) {

			$array_image = getthumbimage($_FILES["thumb"]["name"], $_FILES['thumb']['tmp_name'], $_FILES["thumb"]["size"], $user_thumb);

			$errors = array_merge($array_image['error'], $errors);

			if(count($array_image['error']) == 0) {

				if($row->icon !== 0) {

					unlink($row->icon);

				}

				mysqli_query($setting["Lid"],"UPDATE `users` SET `icon`='".$user_thumb["thumb_dir"].$array_image['thumb']."' WHERE `id`='".$row->id."';");

			}

		}

		$error_count = count($errors);

	}

	if($row->icon == "") {

		$row->icon = "files/image/profile_default.png";

	}

	?>

	<main>

		<article id="otherbody">

			<div id="form-main">

				<form method="post" enctype="multipart/form-data">

						<?php

						if(isset($_POST['thumb']) && $error_count > 0) {

							foreach ($array_image['error'] as $error) {

								echo "<div>".$error."</div>";

							}

						}

						?>

						<div id="thumb-upload" class="">

							<div class="thumb-upload-item">

								<input class="inputfile" name="thumb" id="thumb" type="file">

								<label for="thumb"><span class="fa fa-download icon" aria-hidden="true"></span><span>Upload thumb</span></label>

							</div>

						</div>

						<div class="main-form">

							<label>Street</label>

							<input name="street" type="text" value="<?=htmlentities($row->street)?>" placeholder="Street">

							<label>City</label>

							<input name="city" type="text" value="<?=htmlentities($row->city)?>" placeholder="City">

							<label>State</label>

							<input name="state" type="text" value="<?=htmlentities($row->state)?>" placeholder="State">

							<label>Zip</label>

							<input name="zip" type="text" value="<?=htmlentities($row->zip)?>" placeholder="Zip">

							<label>Country</label>

								<?php

								$array=country();

								?>

								<select name="country">

									<?php

									foreach ($array as $key => $item ) {

										echo '<option value="'.$key.'">'.$item.'</option>';

									}

									?>

								</select>

								<label>Email</label>

								<input name="email" type="email" value="<?=htmlentities($row->email)?>" placeholder="example<?=htmlentities($email_1)?>">

								<label>Password</label>

								<input name="password" type="password" value="" placeholder="********">

								<?php

								if(isset($_POST['submit'])) {

									if($errors_count > 0) {

										foreach ($errors as $errors) {

											echo "<div>".$errors."</div>";

										}

									}
									else {

										mysqli_query($setting["Lid"],"UPDATE `users` SET ".$passwordsalt."`city`='".$city."',`email`='".$email."',`state`='".$state."',`street`='".$street."',`zip`='".$zip."',`country`='".$country."' WHERE `id`='".$row->id."' ");

										echo "<div>Your account details have been successfully updated.</div>";

									}

								}

								?>

							</div>

							<div class="form-content-footer  pure-g">

									<div class="pure-u-1-2"></div>

									<div class="form-content-controls pure-u-1-2"><input name="submit" type="submit" class="secondary-button pure-button" value="Update"></input></div>

							</div>

					</form>

			</div>

			<?php require_once("include/main_footer.php"); ?>

		</article>

	</main>

	<?php

	require_once("include/footer.php");

}

else {

	require_once('pages/404.php');

}

?>
