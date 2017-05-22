<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $title; if (isset($_GET['page'])) { echo " - ". ((int) $_GET['page'])." ".$text_page;} ?></title>

    <?php if ($noindex) { ?>
        <!-- OCFilter Start -->
        <meta name="robots" content="noindex,nofollow" />
        <!-- OCFilter End -->
    <?php } ?>

    <base href="<?php echo $base; ?>" />
    <?php if ($description) { ?>
        <meta name="description" content="<?php echo $description; if (isset($_GET['page'])) { echo " - ". ((int) $_GET['page'])." ".$text_page;} ?>" />
    <?php } ?>
    <?php if ($keywords) { ?>
        <meta name="keywords" content= "<?php echo $keywords; ?>" />
    <?php } ?>
    <meta property="og:title" content="<?php echo $title; if (isset($_GET['page'])) { echo " - ". ((int) $_GET['page'])." ".$text_page;} ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo $og_url; ?>" />
    <?php if ($og_image) { ?>
        <meta property="og:image" content="<?php echo $og_image; ?>" />
    <?php } else { ?>
        <meta property="og:image" content="<?php echo $logo; ?>" />
    <?php } ?>
    <meta property="og:site_name" content="<?php echo $name; ?>" />
    <script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
    <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
    <script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700" rel="stylesheet" type="text/css" />
    <link href="catalog/view/theme/default/stylesheet/stylesheet.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu+Condensed" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <link href="/assets/css/themify-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/fonts/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/css/metisMenu.min.css">
    <link href="/assets/css/animate.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">

    <?php foreach ($styles as $style) { ?>
        <link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
    <?php } ?>
    <script src="catalog/view/javascript/common.js" type="text/javascript"></script>
    <?php foreach ($links as $link) { ?>
        <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
    <?php } ?>
    <?php foreach ($scripts as $script) { ?>
        <script src="<?php echo $script; ?>" type="text/javascript"></script>
    <?php } ?>
    <?php foreach ($analytics as $analytic) { ?>
        <?php echo $analytic; ?>
    <?php } ?>
    <script src="/assets/js/jquery.nav.js" type="text/javascript" ></script>
    <script src="/assets/js/metisMenu.js" type="text/javascript" ></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/assets/js/wow.min.js"></script>
    <link href="/assets/js/magnicpopup/magnific-popup.css" rel="stylesheet">
    <script src="/assets/js/magnicpopup/jquery.magnific-popup.min.js"></script>
    <script src="/assets/js/isotope.pkgd.min.js"></script>
    <script src="/assets/js/textillate/assets/jquery.lettering.js"></script>
    <link href="/assets/js/textillate/assets/animate.css" rel="stylesheet">
    <script src="/assets/js/textillate/jquery.textillate.js"></script>
    <script src="/assets/js/maplace.js"></script>
    <script src="/assets/js/owl.carousel.min.js"></script>
    <script src="/assets/js/script.js" type="text/javascript" ></script>
    <script type="text/javascript">
        function downloadJSAtOnload() {
            var element = document.createElement("script");
            element.src = "/assets/js/widgets/zopim.js";
            document.body.appendChild(element);
        }
        if (window.addEventListener)
            window.addEventListener("load", downloadJSAtOnload, false);
        else if (window.attachEvent)
            window.attachEvent("onload", downloadJSAtOnload);
        else window.onload = downloadJSAtOnload;
    </script>
    <script type="text/javascript">
        function downloadJSAtOnload() {
            var element = document.createElement("script");
            element.src = "/assets/js/widgets/zadarma.js";
            document.body.appendChild(element);
        }
        if (window.addEventListener)
            window.addEventListener("load", downloadJSAtOnload, false);
        else if (window.attachEvent)
            window.attachEvent("onload", downloadJSAtOnload);
        else window.onload = downloadJSAtOnload;
    </script>
</head>
<body class="<?php echo $class; ?> one-col passp_light" id="default">
<div class="page" id="page">
    <div class="head_bck" data-color="#292929" data-opacity="0.8"></div>
    <header class="simple_menu st">

        <!-- Logo -->
        <div class="logo pull-left">
            <a href="/"><img class="minilogo" src="/assets/images/logo-mini.png" alt="Logo final mini"><img class="maxilogo" src="/assets/images/logo-maxi.png" alt="Logo final"></a>
        </div>

        <!-- Header Buttons -->
        <div class="header_btns_wrapper">

            <!-- Main Menu Btn -->
            <div class="main_menu">
                <i class="ti-menu"></i><i class="ti-close"></i>
            </div>

            <!-- Sub Menu -->
            <div class="sub_menu">
                <div class="sub_cont">
                    <ul id="nav">
                        <li><a href="/#start">Начало</a></li>
                        <li><a href="/#services" class="parents">Каталог стройматериалов</a>
                            <ul class="mega_menu">
                                <li class="mega_sub">
                                    <ul>
                                        <ul>
                                            <?php foreach($categories as $category) {?>
                                                <li>
                                                    <a class="external" href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
                                                </li>

                                            <? } ?>
                                        </ul>
                                    </ul>
                                </li>
                            </ul>
                        </li>




                        <li><a href="/#welcome">О нас</a></li>

                        <!--li><a href="#" class="parents">Акции</a>
                          <ul class="mega_menu">
                            <li class="mega_sub">
                              <ul>
                                <li><a href="/tegola-top-shingle-deshevo" class="external">Черепица Тегола Топ Шингл</a></li>
                                <li><a href="/top-premium-deshevo" class="external">Черепица Тегола Премиум</a></li>
                              </ul>
                            </li>
                          </ul>
                        </li-->

                        <li><a href="/blog" class="parents">Блог</a>
                            <ul class="mega_menu">
                                <li class="mega_sub">
                                    <ul>
                                        <?php foreach($blogs as $blog) { ?>
                                            <li><a class="external" href="<?php echo $blog['href']; ?>"><?php  echo $blog['name'];?></a></li>

                                        <?php } ?>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                        <li><a href="/#contacts">Контакты</a></li>
                        <li><a href="/brands">Сертификаты</a></li>

                        <li>
                            <a href="#" class="parents">Конфигураторы материалов</a>
                            <ul class="mega_menu">
                                <li class="mega_sub">
                                    <ul>
                                        <li><a href="/feldhaus">Конфигуратор Feldhaus Klinker - облицовка фасада</a></li>
                                        <li><a href="#" class="external popup-iframe-tegola">Конфигуратор TEGOLA - гибкая черепица</a></li>
                                        <li>
                                            <a href="#" class="external popup-iframe-eternit">Конфигуратор Cedral - фиброцементный сайдинг</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
            <!-- Sub Menu End -->

        </div>
        <!-- Header Buttons End -->

        <!-- Up Arrow -->
        <a href="#page" class="up_block go"><i class="fa fa-angle-up"></i></a>

    </header>

