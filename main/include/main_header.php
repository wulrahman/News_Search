

    <header class="header" style="<?=$style?>">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="header_content d-flex flex-row align-items-center justify-content-start">
                        <div class="logo"><a href="<?=$setting['main_url']?>">DATA BREACH</a></div>
                        <nav class="main_nav">
                            <ul>
                                <li class="active"><a href="<?=$setting['main_url']?>">Home</a></li>
                                <?php
                                    
                                $main_category = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `categorys` ORDER BY `cat_order` ASC LIMIT 0,4");

                                while($row = mysqli_fetch_object($main_category)) { ?>
                                        <li>
                                            <a href="<?=$setting['main_url']?>/?action=post&category=<?=urlencode($row->name)?>"><?=$row->name?></a>
                                        </li><?php
                                }

                                ?>
                            </ul>
                        </nav>
                        <div class="search_container ml-auto">
                            <form action="#">
                                <input type="search" class="header_search_input" required="required" name="q" placeholder="Type to Search...">
                                <img class="header_search_icon" src="<?=$setting['main_url']?>/main/images/search.png" alt="">
                            </form>
                            
                        </div>
                        <div class="hamburger ml-auto menu_mm">
                            <i class="fa fa-bars trans_200 menu_mm" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>


    <!-- Menu -->

    <div class="menu d-flex flex-column align-items-end justify-content-start text-right menu_mm trans_400">
        <div class="menu_close_container"><div class="menu_close"><div></div><div></div></div></div>
        <div class="logo menu_mm"><a href="#">Avision</a></div>
        <div class="search">
            <form action="#">
                <input type="search" class="header_search_input menu_mm" required="required" name="q" placeholder="Type to Search...">
                <img class="header_search_icon menu_mm" src="<?=$setting['main_url']?>/main/images/search_2.png" alt="">
            </form>
        </div>
        <nav class="menu_nav">
            <ul class="menu_mm">
                <li class="menu_mm"><a href="<?=$setting['main_url']?>">home</a></li>
                <?php
                                                   
                $main_category = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `categorys` ORDER BY `cat_order` ASC LIMIT 0,4");

                while($row = mysqli_fetch_object($main_category)) { ?>
                    <li>
                        <a href="<?=$setting['main_url']?>/?action=post&tag=<?=urlencode($row->name)?>"><?=$row->name?></a>
                    </li><?php
                }

                ?>
            </ul>
        </nav>
    </div>


