<?php

$title="Account validation";

if(isset($_GET['id']) && isset($_GET['hash'])) {

	$errors=array();

	$id = intval($_GET['id']);

	$hash = $_GET['hash'];

	$query = mysqli_query($setting["Lid"], "SELECT SQL_CALC_FOUND_ROWS `email`, `hash`, `active`, `id` FROM `users` WHERE `id`='".$id."'");

	$email_count = array_pop(mysqli_fetch_array(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

	if($email_count > 0 ) {

		$row = mysqli_fetch_object($query);

		if($row->hash == $hash) {

			if($row->active == 1) {

				$errors[]='Your account has already been validated.';

			}

		}
		else {

			$errors[]='Incorrect validated code has been supplied.';

		}

	}
	else {

		$errors[]='Incorrect validated code has been supplied.';

	}

	$error_count = count($errors);

	if($error_count == 0) {

		mysqli_query($setting["Lid"],"UPDATE `users` SET `active` = '1' WHERE `id` = '".$row->id."'");

		$errors[] = 'Your account has been successfully validated.';

		$error_count = count($errors);

	}

}
else {

	$errors[]='Please make sure all the required fields have been supplied.';

	$error_count = count($errors);

}

require_once('include/header.php');

require_once('include/main_header.php');

?>

<main>

	<article id="otherbody">

		<div>

				<?php

				if($error_count > 0) {

					foreach ($errors as $error) {

						echo "<div>".$error."</div>";

					}

				}

				?>

		</div>

		<?php require_once("include/main_footer.php"); ?>

	</article>

</main>

<?php require_once("include/footer.php"); ?>
