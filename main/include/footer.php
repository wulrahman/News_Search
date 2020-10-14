  </body>
</html>

<script src="<?=$setting['main_url']?>/main/js/jquery-3.2.1.min.js"></script>
<script src="<?=$setting['main_url']?>/main/styles/bootstrap4/popper.js"></script>
<script src="<?=$setting['main_url']?>/main/styles/bootstrap4/bootstrap.min.js"></script>
<script src="<?=$setting['main_url']?>/main/plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
<script src="<?=$setting['main_url']?>/main/plugins/jquery.mb.YTPlayer-3.1.12/jquery.mb.YTPlayer.js"></script>
<script src="<?=$setting['main_url']?>/main/plugins/easing/easing.js"></script>
<?php
if($action == "view") { ?>
    <script src="<?=$setting['main_url']?>/main/plugins/masonry/masonry.js"></script>
    <script src="<?=$setting['main_url']?>/main/plugins/parallax-js-master/parallax.min.js"></script>
    <script src="<?=$setting['main_url']?>/main/js/post.js"></script>
<?php
}
else if($action == "post" || isset($_GET['q']) && !$verifier->space($_GET['q'])) { ?>
    <script src="<?=$setting['main_url']?>/main/plugins/parallax-js-master/parallax.min.js"></script>
    <script src="<?=$setting['main_url']?>/main/js/category.js?q=1"></script>
    <script src="<?=$setting['main_url']?>/main/js/scroll_page_search.mjs?q=5"></script>
    <script src="<?=$setting['main_url']?>/main/assets/js/main.js"></script>

<?php

}
else if($action == '404' || $action == 'error') { ?>
    <script src="<?=$setting['main_url']?>/main/plugins/masonry/masonry.js"></script>
    <script src="<?=$setting['main_url']?>/main/plugins/parallax-js-master/parallax.min.js"></script>
    <script src="<?=$setting['main_url']?>/main/js/contact.js"></script> <?php
}
else { ?>
    <script src="<?=$setting['main_url']?>/main/plugins/masonry/masonry.js"></script>
    <script src="<?=$setting['main_url']?>/main/plugins/masonry/images_loaded.js"></script>
    <script src="<?=$setting['main_url']?>/main/js/custom.js?q=43"></script>
    <script src="<?=$setting['main_url']?>/main/js/scroll_page_search.mjs?q=5"></script><?php
}

?>
