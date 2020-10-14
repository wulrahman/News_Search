<div id="mainnav">

	<div id="logo"><a href="<?=$setting["url"]?>"><img src="<?=$setting["url"]?>/common/files/img/logo.png"></img></a></div>

	<form method="GET" id="search" action="<?=$setting["url"]?>/content">

		<div><input type="search" name="q" autofocus="" placeholder="search" value="<?=htmlspecialchars($_GET['q'])?>"></input><input type="image" src="<?=$setting["url"]?>/common/files/img/search.png" name="submit"></input></div>

	</form>

	<div id="navfloatright">

		<nav class="pure-menu custom-restricted-width">

			<ul class="pure-menu-list">

				<li id="navbutton" class="pure-menu-has-children pure-menu-allow-hover">

					<a href="#" id="menuLink1" class="pure-menu-link"><img src="<?=$setting["url"]?>/common/files/img/menu.png"></img></a>

					<ul class="pure-menu-children">

						<?php

						if($user->login_status == 1) { ?>

							<li class="pure-menu-item"><a class="pure-menu-link" href="<?=$setting["url"]?>/setting">Setting</a></li>

							<li class="pure-menu-item"><a class="pure-menu-link" href="<?=$setting["url"]?>/logout">Logout</a></li><?php

						}
						else if($user->login_status == 0) { ?>

							<li class="pure-menu-item"><a class="pure-menu-link" href="<?=$setting["url"]?>/login">Login</a></li>

							<li class="pure-menu-item"><a class="pure-menu-link" href="<?=$setting["url"]?>/register">Register</a></li><?php

						}
						?>
                                                
                        <li class="pure-menu-item"><a class="pure-menu-link" href="<?=$setting['main_url']?>">Home</a></li>
    
                        <li class="pure-menu-item"><a class="pure-menu-link" href="<?=$setting['search_url']?>">Search</a></li>
    
                        <li class="pure-menu-item"><a class="pure-menu-link" href="<?=$setting['games_url']?>">Games</a></li>

					</ul>

				</li>

            </ul>

        </nav>

	</div>

</div>