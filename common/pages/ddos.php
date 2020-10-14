<?php

$title="Unusual traffic from your computer network";

$action = 'error';

require_once('../main/include/header.php');

require_once('../main/include/main_header.php');

$error_code ="Unusual traffic from your computer network";

$error_message = "The requested URL ".$_SERVER['REQUEST_URI'].", We are experiencing unusual traffic from your computer network. That's all we know.";

?>

<div class="super_container">

	<!-- Home -->

	<?php require_once("pages/404_require.php"); ?>

	<?php require_once("../main/include/main_footer.php"); ?>

</div>

<?php require_once("../main/include/footer.php"); ?>
