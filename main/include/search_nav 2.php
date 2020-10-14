<?php

$main_nav_nav=array(1 => "web","isch","vid");

$nav=array(1 => "Web","Images","Videos");

foreach($nav as $key => $i) {

	if(isset($_GET['q']) && !space($q)) { 
	
		echo '<li><a href="'.$setting["search_url"].'/?q='.urlencode($q).'&tbm='.$main_nav_nav[$key].'">'.$i.'</a></li>';
		
	}
	else {
	
		echo '<li><a href="'.$setting["search_url"].'/?tbm='.$main_nav_nav[$key].'">'.$i.'</a></li>';
		
	}
	
}

if(isset($_GET['q']) && !space($q)) { ?>

	<li><a href="<?=$setting["main_url"]?>/news?q=<?=urlencode($q)?>">News</a></li>

	<li><a href="<?=$setting["games_url"]?>/?q=<?=urlencode($q)?>">Games</a></li>

<?php

}
else { ?>

	<li><a href="<?=$setting["main_url"]?>">News</a></li>

	<li><a href="<?=$setting["games_url"]?>">Games</a></li>

<?php

}

?>