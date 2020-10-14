<?php

header('HTTP/1.0 404 Not Found');

$title="(404) Error !";

require_once('include/header.php');

require_once('include/main_header.php');

?>

<div class="super_container">

	<!-- Home -->

	<div class="home">
		<div class="home_background parallax-window" data-parallax="scroll" data-image-src="<?=$setting['main_url']?>/main/images/regular.jpg" data-speed="0.8"></div>
		<div class="home_content">
			<div class="container">
				<div class="row">
					<div class="col-lg-6 offset-lg-3">
						<!-- Post Comment -->
						<div class="post_comment">
							<div class="contact_form_container">
								<h2>404 That's an error</h2>

								<div>The requested URL <?=$_SERVER['REQUEST_URI']?> was not found on this server. That's all we know.</div>

							</div>
						</div>
					</div>
				</div>
			</div>		
		</div>
	</div>


	<?php require_once("include/main_footer.php"); ?>

</div>

<?php require_once("include/footer.php"); ?>
