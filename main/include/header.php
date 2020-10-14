<!DOCTYPE html>

<html>

    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="viewport" content="initial-scale=1, maximum-scale=1,width=device-width, shrink-to-fit=no">

        <link rel="shortcut icon" href="<?=$setting["url"]?>/common/files/image/favicon.ico" type="image/x-icon">

        <link rel="icon" href="<?=$setting["url"]?>/common/files/image/favicon.ico" type="image/x-icon">
        
        <script type="text/javascript">
            var site_url = "<?=$setting["main_url"]?>";
            var url = "<?=$setting["url"]?>";
        </script>

        <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/styles/bootstrap4/bootstrap.min.css?q=1">
        <link href="<?=$setting["main_url"]?>/main/plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/plugins/OwlCarousel2-2.2.1/owl.carousel.css">
        <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
        <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/plugins/OwlCarousel2-2.2.1/animate.css">
        <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/plugins/jquery.mb.YTPlayer-3.1.12/jquery.mb.YTPlayer.css">
        <?php
        if($action == "view") { ?>
            <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/styles/post.css?q=4">
            <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/styles/post_responsive.css?q=1">
        <?php
        }
        else if($action == "404" || $action == 'error') { ?>
            <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/styles/contact.css?q=2">
            <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/styles/contact_responsive.css?q=1"><?php

        }
        else if($action == "post" || isset($_GET['q']) && !$verifier->space($_GET['q'])) { ?>
            <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/styles/category.css?q=6">
            <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/styles/category_responsive.css?q=3">
            <script>document.getElementsByTagName("html")[0].className += " js";</script>
            <link rel="stylesheet" href="<?=$setting["main_url"]?>/main/assets/css/style.css?q=4">
        <?php
        }
        else { ?>
            <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/styles/main_styles.css?q=10">
            <link rel="stylesheet" type="text/css" href="<?=$setting["main_url"]?>/main/styles/responsive.css?q=2"><?php
        }
        ?>

        <title><?=$title?></title>

        <meta name="description" content="<?=$description?>">

        <meta name="keywords" content="<?=$keywords?>">

    </head>

<body class="body-color">
