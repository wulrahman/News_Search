   <!-- Mobile Menu start -->
    <div class="mobile-menu-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="mobile-menu">
                        <nav id="dropdown">
                            <ul class="mobile-menu-nav">
                                <li><a href="<?=$setting["admin_url"]?>">Home</a>
                                </li>
                                <li><a data-toggle="collapse" data-target="#demodepart" href="#">Content</a>
                                    <ul id="demodepart" class="collapse dropdown-header-top">
                                        <li><a href="<?=$setting["admin_url"]?>/?type=news">Content Management</a></li>
                                        <li><a href="<?=$setting["admin_url"]?>/?type=training_set">Training Set</a></li>
                                        <li><a href="<?=$setting["admin_url"]?>/?type=calculate_ai">Text Classifier</a></li>

                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mobile Menu end -->

    <!-- Main Menu area start-->
    <div class="main-menu-area mg-tb-40">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
                        <li class="active"><a href="<?=$setting["admin_url"]?>"><i class="notika-icon notika-house"></i>Home</a>
                        </li>
                        <li><a data-toggle="tab" href="#Tables"><i class="notika-icon notika-windows"></i>Content</a>
                        </li>
                    </ul>
                    <div class="tab-content custom-menu-content">

                        <div id="Tables" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="<?=$setting["admin_url"]?>/?type=news">Content Management</a></li>
                                <li><a href="<?=$setting["admin_url"]?>/?type=training_set">Training Set</a></li>
                                <li><a href="<?=$setting["admin_url"]?>/?type=calculate_ai">Text Classifier</a></li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
