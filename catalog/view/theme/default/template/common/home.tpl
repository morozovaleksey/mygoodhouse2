<?php echo $header; ?>
<?php echo $content_top; ?>

<!-- Features -->
<section class="boxes">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12 bordered_block grey_border image_bck" data-image="/assets/images/white_back.jpg">

        <div class="container text-center">
          <h2>Почему именно мы</h2>

          <div class="row">

            <!-- Item -->
            <div class="col-md-4 text-center">
              <div class="icon"><span><img src="http://mygood.house//assets/hand_wb_1.svg" width="80" height="80"
                                           alt=""></span></div>
              <h3>Безупречное качество <br>продукции</h3>
              Мы работаем с лучшими европейскими и отечественными производителями фасадных, кровельных,
              ландшафтных материалов.
            </div>

            <!-- Item -->
            <div class="col-md-4 text-center">
              <div class="icon"><span><img src="/assets/images/hand_wb_2.svg" width="80" height="80"
                                           alt=""></span></div>
              <h3>Безупречное качество <br>сервиса</h3>
              Мы знаем все тонкости монтажа, обладаем огромной базой тех.решений мы не продавцы мы
              проффесиональные специалисты по применению.
            </div>

            <!-- Item -->
            <div class="col-md-4 text-center">
              <div class="icon"><span><img src="/assets/images/hand_c_3.svg" width="80" height="80"
                                           alt=""></span></div>
              <h3>Безупречная <br>репутация</h3>
              Нас рекомендуют известные девелоперы и многочисленные частные клиенты. Good House - это
              качество, надежность и ответственность.
            </div>


          </div>
          <!-- Row End -->

        </div>
      </div>
    </div>
    <!-- Row End -->

  </div>
</section>
<!-- Features End -->
<div id="services" class="content">
  <div class="container-fluid">

    <div class="row">
      <div class="bordered_block col-md-12 grey_border no-cover image_bck" data-image="/assets/images/light_wool-dcfbe8bc092d0fccfe25cfedcda1272e7e8bb916d4a7a64273de1f51c226f7fa.png">

        <div class="container">
          <div class="row">

            <!--Sidebar-->
            <div class="col-md-12 col-xs-12">

              <!-- ToolBar -->
              <div class="toolbar">
                <p class="amount pull-left">
                  <strong>В НАШЕМ АССОРТИМЕНТЕ:</strong>
                </p>

                <div class="sorter pull-right filter-button-group">
                  <button class="btn btn-default" data-filter="*">Все</button>
                  <?php foreach($categories as $key=>$category) { ?>
                    <?php if($category['category_id'] != 59) { ?>
                      <button class="btn btn-default" data-filter=".<?php echo $category['metka_slider']; ?>"><?php echo $category['name'];?></button>
                    <?php } ?>

                 <?php } ?>
                </div>
                <div class="clear"></div>
              </div>

              <div class="row masonry">
                <?php foreach ($children_data as $child) { ?>
                    <?php if($child['category_parent_id'] != 59) { ?>
                      <div class="col-sm-3 masonry-item taxons-list <?php echo $child['metka_slider']?>" >
                        <a class="product_item text-center" href="<?php echo $child['href']; ?>">
                          <span class="product_photo bordered_wht_border">
                            <img src="<?php echo $child['thumb']; ?>">
                          </span>
                          <span class="product_title"><?php echo $child['name']; ?></span></a>
                      </div>
                    <?php } ?>
                <?php } ?>
              </div>
            </div>
            <!--Sidebar End-->


          </div>
          <!--Row End-->

        </div>
      </div>
    </div>
    <!-- Row End -->

  </div>
</div>

<section class="boxes">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12 bordered_block bordered_wht_border image_bck no-cover text-center" data-image="/assets/images/wild_oliva-1a632d956a35a4c49b49be28f16c4e8eea8e821f19f9babbf0193f2d0c380567.png">
        <div class="container">
          <span class="great_title great_title_big white_txt">Нет времени искать нужный товар?</span>
          <h4 class="subtitle gold_txt">Обратитесь к персональному менеджеру, он решит все задачи</h4>
          <p class="white_txt">Вы не обязаны разбираться в материале и архитектурных трендах.<br>
            Каждый день мы работаем с серьезными заказчиками над серьезными проектами и у нас получается.</p>
          <span><a href="javascript:void(0);" onclick="ZCallbackWidget.showCallback();return false;" class="btn btn-white">Заказать звонок</a><a href="javascript:$zopim.livechat.window.show();" class="btn btn-white">Начать общение онлайн</a></span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="boxes about" id="welcome">
  <div class="container-fluid">
    <div class="row">

      <div class="col-md-5 bordered_block image_bck white_txt" data-color="#292929">
        <div class="simple_block text-left">
          <h3 class="wow fadeInUp animated">Наши клиенты приобретают ценности</h3>
          <h4 class="subtitle wow fadeInUp gold_txt animated" data-wow-delay="0.2s">самоуважение начинается с хорошего дома</h4>
          <p class="wow fadeInUp animated" data-wow-delay="0.4s">Основным направлением деятельности ГК "Гуд Хаус" является
            поставка кровельных, фасадных и ландшафтных материалов, а также комплексные проектные поставки на объекты
            строительства!
            <br><br>
            В шоу-румах компании Гуд Хаус в Казани, Набережных Челнах и Самаре представлен широкий ассортимент
            предлагаемой продукции - российский и европейский облицовочный кирпич, фасадная плитка "под кирпич",
            фиброцементный сайдинг на фасад, керамические блоки для несущих стен, натуральная керамическая черепица,
            итальянская и российская гибкая черепица, напольная клинкерная плитка и ступени, тротуарный клинкерный
            кирпич и брусчатка, профилированные мембраны и рулонные наплавляемые материалы для фундамента, а также
            сопутствующие материалы для строительства: сухие строительные смеси, цветные кладочные растворы, аксессуары
            для кровли, строительный инструмент и аксессуары для монтажа.
            <br><br>
            Основными сервисными направлениями для клиентов Гуд Хаус являются качественный технический расчёт материалов
            с соблюдением соответствующих тех.запасов, доставка стройматериалов в любую точку России в максимально
            короткие сроки, а также помощь в поиске квалифицированной строительно-монтажной бригады!
          </p>
          <blockquote>
            <p>Высокое качество, гибкая ценовая политика и индивидуальный подход к каждому клиенту - основные принципы
              нашего бизнеса!</p>
          </blockquote>
        </div>
      </div>

      <!--Image-->

      <div class="col-md-7 image_bck bordered_block height550" data-image="/assets/images/goodhouse-7ffe7009a620f893ed880af77654c59ab9ac050ef9fe3279f9ab94401ff7771f.jpg" >
        <div class="over" data-opacity="0.4" data-image="/assets/images/overlay-46f6e349ff66089f1f1cc905b3f027c0e59887fda98933c86e34a4c718d51043.png" data-color="#302313"></div>
        <div class="simple_block text-left white_txt">

        </div>
      </div>

    </div>
    <!--Row End-->

  </div>
</section>

<section class="boxes">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12 bordered_block grey_border image_bck" data-image="/assets/images/white_back-20762f622c7784d6e6f1b552b37703eae9c2e535908544771f37730c9f71bf38.jpg">

        <div class="container text-center">
          <h2>Как мы работаем</h2>
          <h4 class="text-center subtitle">Предоставляем всесь спектр строительных услуг и несем
            ответственность за результат</h4>

          <div class="row">
            <div class="col-md-4 col-sm-6 wow animate fadeInLeft animated" style="visibility: visible; animation-name: fadeInLeft;">
              <div class="c-content-step-1 c-opt-1">
                <div class="c-icon">
                                    <span class="c-hr c-hr-first">
                                        <span class="c-content-line-icon c-icon-14 c-theme"><img src="/assets/images/steps-1.jpg" class="img-responsive img-circle img-thumbnail"></span>
                                    </span>
                </div>
                <div class="c-title c-font-20 c-font-bold c-font-uppercase">1. Подготовка проекта</div>
                <div class="c-description c-font-17"> Мы хорошо понимаем, что нам предстоит строить на
                  века, и мы готову вложить душу. Ведь ваше имение станет центром семейных ценностей,
                  центром фамилии.
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 wow animate fadeInLeft animated" data-wow-delay="0.2s" style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInLeft;">
              <div class="c-content-step-1 c-opt-1">
                <div class="c-icon">
                                    <span class="c-hr">
                                        <span class="c-content-line-icon c-icon-14 c-theme"><img src="/assets/images/steps-2.jpg" class="img-responsive img-circle img-thumbnail"></span>
                                    </span>
                </div>
                <div class="c-title c-font-20 c-font-bold c-font-uppercase">2. Подбор материалов,
                  оптимизация цен
                </div>
                <div class="c-description c-font-17"> Работа с дорогим, элитным материалом не терпит
                  дилетантства. Некоторые вещи нужно знать опеределенно, но большинство моментов нужно
                  просто чувствовать.
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-12 wow animate fadeInLeft animated" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInLeft;">
              <div class="c-content-step-1 c-opt-1">
                <div class="c-icon">
                                    <span class="c-hr c-hr-last">
                                        <span class="c-content-line-icon c-icon-14 c-theme"><img src="/assets/images/steps-3.jpg" class="img-responsive img-circle img-thumbnail"></span>
                                    </span>
                </div>
                <div class="c-title c-font-20 c-font-bold c-font-uppercase">3. Контроль строительства на
                  всех этапах
                </div>
                <div class="c-description c-font-17"> На объекте не достаточно только хорошего прораба и
                  опытного каменщика. Должен быть тот, кто отвечает за то, чтобы каждый камень встал
                  как бриллиант в оправу.
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    <!-- Row End -->

  </div>
</section>

<section class="boxes blog" id="news">
  <div class="container-fluid">
    <div class="row">

      <!-- col -->
      <div class="col-md-12 bordered_block grey_border image_bck no-cover" data-image="/assets/images/wild_oliva-1a632d956a35a4c49b49be28f16c4e8eea8e821f19f9babbf0193f2d0c380567.png">


        <div class="container">

          <h2 class="text-center white_txt">Свежие записи блога</h2>
          <h4 class="text-center subtitle">Новости, акции, полезная информация </h4>

          <div class="row">

            <?php foreach($products_recent_blog as $product_recent_blog) {?>
            <!-- Item -->
              <div class="col-sm-4 post-snippet masonry-item">
              <a href="<?php echo $product_recent_blog['href']; ?>" class="bordered_wht_border">
                <?php if(isset($product_recent_blog['attributes'][0])) {?>
                  <img alt="" src="http://img.youtube.com/vi/<?php echo $product_recent_blog['attributes'][0]['text']; ?>/hqdefault.jpg">
                <? } else { ?>
                  <img alt="" src="<?php echo $product_recent_blog['thumb']; ?>">
                <?php } ?>

              </a>

              <div class="inner">
                <a href="<?php echo $product_recent_blog['href']; ?>">
                  <h4 class="title">О<?php echo $product_recent_blog['name']; ?></h4>
                  <span class="date"> <?php echo $product_recent_blog['date_available']; ?> </span>
                </a><ul class="post-meta list-unstyled list-inline"><a href="<?php echo $product_recent_blog['href']; ?>">
                  </a><li><a href="<?php echo $product_recent_blog['href']; ?>">
                      <i class="fa fa-folder-o"></i>
        <span>
                                            </span></a><a href="<?php echo $product_recent_blog['category_href']; ?>"><?php echo $product_recent_blog['category']['name']; ?></a>

                  </li>
                </ul>
                <?php echo $product_recent_blog['short_description']; ?>
                <a class="btn btn-default" href="<?php echo $product_recent_blog['href']; ?>">Дальше</a>
              </div>
            </div>
            <?php } ?>



          </div>
          <!-- Row End -->

          <div class="text-center">
            <a href="/blog" type="button" class="btn btn-default btn-lg">Все материалы блога</a>
          </div>

        </div>
      </div>
    </div>
    <!-- Row End -->
  </div>
</section>

<!-- Reviews -->
<section class="boxes" id="reviews">
  <div class="container-fluid">

    <div class="row">

      <!-- col -->
      <div class="col-md-12 bordered_block image_bck white_txt" data-image="/assets/images/family.jpg">

        <!-- Over -->
        <div class="over" data-opacity="0.6" data-image="/assets/images/overlay.png" data-color="#292929"></div>

        <div class="container">

          <h2 class="text-center white_txt">Отзывы</h2>
          <h4 class="text-center subtitle">Клиенты о нас</h4>

          <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center wow fadeInUp">
              <!-- Wrapper -->
              <div class="review_single_wrapper">
                <?php foreach($reviews as $review) { ?>
                  <div class="reviews_single_item">
                    <p><?php echo $review['text']; ?>
                      <small class="feedback-font">- <?php echo $review['author']; ?></small>
                    </p>
                  </div>
                <?php } ?>

              </div>
              <!-- Wrapper End -->

            </div>
          </div>
        </div>
      </div>
      <!-- Col End -->


    </div>
    <!-- Row End -->

  </div>
</section>
<!-- Reviews End-->
<section class="boxes">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12 bordered_block bordered_wht_border image_bck no-cover text-center" data-image="/assets/images/wild_oliva-1a632d956a35a4c49b49be28f16c4e8eea8e821f19f9babbf0193f2d0c380567.png">
        <div class="container">
          <span class="great_title great_title_big white_txt">Дизайнер и Архитектор</span>
          <h4 class="subtitle gold_txt">в распоряжении каждого нашего клиента</h4>
          <span><a href="javascript:void(0);" onclick="ZCallbackWidget.showCallback();return false;" class="btn btn-white">Заказать звонок</a><a href="javascript:$zopim.livechat.window.show();" class="btn btn-white">Начать общение онлайн</a></span>
        </div>
      </div>
    </div>
  </div>
</section>

<!--About Cafe-->
<section class="boxes about" id="about">
  <div class="container-fluid">

    <div class="row">

      <div class="col-md-7 bordered_block">
        <div class="over" data-opacity="0.3" data-image="/assets/images/overlay.png" data-color="#292929"></div>

        <!--Wrapper-->
        <div class="intro_wrapper">
          <div class="item image_bck" data-image="/assets/images/showroom/showroom1-554115c5f0bff42a49488390c821521f7f09bcb49e6d5b78357d5e50420493fc.jpg"></div>
          <div class="item image_bck" data-image="/assets/images/showroom/showroom2-e1e3c2a59095f80e14bbc520b8572370bee13c826939de2d91dca48ab8fc4d1b.jpg"></div>
          <div class="item image_bck" data-image="/assets/images/showroom/showroom3-916a3281330cd556ad005f846f608e1f2d393329973e54ef3be5fe293ba5da0c.jpg"></div>
          <div class="item image_bck" data-image="/assets/images/showroom/showroom4-0096820a9e89785288a9216ec9a41af434368f0375915db368aedbbff5874411.jpg"></div>
          <div class="item image_bck" data-image="/assets/images/showroom/showroom5-0b87623e90e4156e467a0e69d565da0105dc0475caddc8653859d23c174c7ad1.jpg"></div>

        </div>
        <!--Wrapper End-->

      </div>

      <div class="col-md-5 bordered_block image_bck white_txt showroom_block" data-color="#292929">
        <div class="simple_block text-left white_txt">
          <h3 class="wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;"><b>Сеть шоу-румов</b>европейского
            строительного материала в Повольжье</h3>
          <div class="wow fadeInUp animated" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">

            <p class="wow fadeInUp gold_txt" data-wow-delay="0.4s">Важно начать знакомство с превосходным материалом в
              привильном месте. Мы собрали безупречную коллекцию строительного материала, доставленного специально для
              вас с лучших европейских заводов.<br> <br>Приходите, вы безусловно испытаете восторг, в вашей душе родится
              мечта о безупречном доме. Богатый опыт наших экспертов сделает процесс выбора простым и приятным. </p>
            <br> <br>
            <h4>Самара</h4>
            09:00-18:00<br>
            <h4>Казань</h4>
            09:00-18:00<br>
            <h4>Набережные Челны</h4>
            09:00-18:00<br>
            <br> <br>

            <a href="#contacts" class="btn btn-white">Узнать адреса</a>

          </div>
        </div>
      </div>


    </div>
    <!--Row End-->

  </div>
</section>
<!--About Cafe End-->

<!-- Pricing -->
<section class="boxes" id="price">
  <div class="container-fluid">
    <div class="row">

      <!-- col -->
      <div class="col-md-12 bordered_block grey_border image_bck" data-color="#f4f4f4">
        <div class="container">

          <h2 class="text-center">Мы готовы к сотрудничеству</h2>
          <h4 class="text-center subtitle">Будем рады партнерству</h4>

          <div class="row">
            <div class="col-md-4 col-sm-6">
              <div class="pricing-table text-center">
                <h5>с девелоперами</h5>
                <!-- Welcome Image -->
                <a class="btn btn-default" href="javascript:$zopim.livechat.window.show();">Связаться с
                  нами</a>
                <div class="intro_image_co intro_text_rb text-center wow fadeInLeft"
                     data-wow-duration="2s">
                  <img src="/assets/images/man1.jpg" alt="">
                </div>
              </div>
              <!--end of pricing table-->
            </div>
            <div class="col-md-4 col-sm-6">
              <div class="pricing-table text-center">
                <h5>с архитекторами
                  и проектировщиками</h5>
                <!-- Welcome Image -->
                <a class="btn btn-default" href="javascript:$zopim.livechat.window.show();">Связаться с
                  нами</a>
                <div class="intro_image_co intro_text_rb text-center wow fadeInUp"
                     data-wow-duration="2s">
                  <img src="/assets/images/man2.jpg" alt="">
                </div>
              </div>
              <!--end of pricing table-->
            </div>
            <div class="col-md-4 col-sm-6">
              <div class="pricing-table text-center">
                <h5>с дизайнерами интерьеров</h5>
                <!-- Welcome Image -->
                <a class="btn btn-default" href="javascript:$zopim.livechat.window.show();">Связаться с
                  нами</a>
                <div class="intro_image_co intro_text_rb text-center wow fadeInRight"
                     data-wow-duration="2s">
                  <img src="/assets/images/woman.jpg" alt="">
                </div>
              </div>
              <!--end of pricing table-->
            </div>
          </div>
        </div>
      </div>
      <!-- Col End -->
    </div>

  </div>
</section>
<!-- Pricing End -->
<?php echo $footer; ?>