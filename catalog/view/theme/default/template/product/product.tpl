<?php echo $header; ?>

<div class="inside_title image_bck bordered_wht_border white_txt" data-image="/image/<?php echo $back_thumb; ?>">
  <!-- Over -->
  <div class="over" data-opacity="0.5" data-color="#000"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-4"><h1><?php echo $heading_title; ?></h1></div>
      <div class="col-md-8 text-right">


        <div class="breadcrumbs">
          <ol>
            <li><a href="/products">Каталог</a><span id="separator"></span></li>
            <?php
            $numItems = (count($breadcrumbs)-1);

            foreach ($breadcrumbs as $key => $breadcrumb) {
              if($breadcrumb['text'] !="Главная") { ?>
                <?php if($numItems == $key)  { ?>
                  <li>
                    <span><?php echo $breadcrumb['text']; ?></span>
                  </li>
                <?php } else {?>
                  <li>
                    <a><?php echo $breadcrumb['text']; ?></a>
                    <span id="separator"></span>
                  </li>
                <?php } } } ?>
          </ol>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="bordered_block col-md-12 grey_border image_bck no-cover product-list" data-image="/assets/images/squairy_light-9367283cff05119b95cd9f5b0a234af4b1e2981e91a662dbfdf9b1b03b0a34c5.png">
        <div class="container">
          <div class="row">
            <!--Sidebar-->
            <div class="col-md-9 col-md-push-3 col-xs-12 product-body-row">
              <!-- Carousel and Anons -->
              <div class="row product_inside" itemscope="" itemtype="https://schema.org/Product">
                <div class="col-md-6 col-xs-6">

                  <div id="product-images">
                    <!-- Carousel -->
                    <div class="products_inside_wrapper product-img-fix">
                      <div id="main-image" class="classes_inside_item bordered_wht_border">
                        <img itemprop="image" alt="Футуро серый" src="<?php echo $thumb; ?>">

                      </div>
                    </div>
                    <!-- Carousel End -->
                    <div id="thumbnails" data-hook="">

                    </div>
                  </div>

                  <div data-hook="product_properties" class="product_properties">
                    <table id="product-properties" class="table table-striped" data-hook="">


                      <tbody>
                      <?php if($model){ ?>
                      <tr class="even">

                        <td>Модель</td>
                        <td><?php echo $model; ?> </td>

                      </tr>
                      <?php } ?>
                      <?php if($manufacturer) {?>
                      <tr class="even">

                        <td>Бренд</td>
                        <td><?php echo $manufacturer; ?> </td>

                      </tr>

                      <?php } ?>
                      <?php if($attributes) {?>
                          <?php foreach($attributes as $attribute){ ?>
                            <tr class="even">

                              <td><?php echo $attribute['name'] ?></td>
                              <td><?php echo $attribute['text']; ?> </td>

                            </tr>
                          <?php } ?>
                      <?php } ?>


                      </tbody></table>

                  </div>

                </div>
                <div class="col-md-6 col-xs-12">
                  <h3 class="title" itemprop="name"><?php echo $heading_title; ?></h3>
                  <?php echo $short_description; ?>

                  <div id="cart-form" data-hook="cart_form" class="clearfix">

                    <form action="/orders/populate" accept-charset="UTF-8" method="post"><input name="utf8" type="hidden" value="✓"><input type="hidden" name="authenticity_token" value="xM8C4r/QzTgxZrBZ9QqGua/CgYe5THWospTuTdFh0X2L8RZV6CTkTSrukisbArmgaIxfyQMTxrDJYZxdiMt0lA==">
                      <div id="inside-product-cart-form" data-hook="inside_product_cart_form" itemprop="offers" itemscope="" itemtype="https://schema.org/Offer">
                        <div data-hook="product_price">
                          <div id="product-price" class="price-box">
                            <h3 class="product-section-title">
                              <small>Цена за кв.м.:</small>
                            </h3>
                            <div>
                              <span class="lead price selling special-price" itemprop="price" style="<?php if($special) { echo 'text-decoration: line-through';} ?>">
                                <?php if($price == '0 р.') { echo 'Под заказ'; } else { echo $price; } ?>
                              </span>
                              <span class="lead price selling special-price" itemprop="price">
                                <?php if($special) {echo $special;} ?>
                              </span>

                              <span itemprop="priceCurrency" content="RUB"></span>
                            </div>
                            <h3 class="product-section-title">
                              <small>Цена за шт.:</small>
                            </h3>
                            <div>
                              <span class="lead price selling special-price" itemprop="price">
                                 <?php if($price2 == '0 р.') { echo 'Под заказ'; } else { echo $price2; } ?>
                              </span>

                              <span itemprop="priceCurrency" content="RUB"></span>
                            </div>

                            <link itemprop="availability" href="https://schema.org/InStock">
                          </div>

                        </div>
                      </div>
                    </form>
                  </div>


                  <div class="add-to-box">
                    <p>Заказать легко! Просто сообщите нам нужное количество, сроки и адрес доставки.</p>
                    <a class="btn btn-default" id="orderButton" href="#"><i class="fa fa-shopping-cart"></i>
                      <span>Заказать</span></a>
                    <a class="btn btn-default" href="javascript:void(0);" onclick="ZCallbackWidget.showCallback();return false;"><i class="fa fa-phone"></i>
                      <span>Консультация</span></a>
                    <!-- Button trigger modal -->


                    <!-- Modal -->

                  </div>


                </div>
              </div>

              <ul id="myTab" class="nav nav-tabs product-tabs" role="tablist">
                <li role="presentation" class="active">
                  <a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">Описание</a>
                </li>








                <li role="presentation" class="">
                  <a href="#profile" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile" aria-expanded="false">Отзывы и
                    вопросы</a>
                </li>
              </ul>

              <div id="myTabContent" class="tab-content">
                <div role="tabpanel" class="tab-pane fade active in" id="home" aria-labelledby="home-tab">
                  <h3>Описание</h3>
                  <?php echo $description; ?>
                  <?php if($products) {?>
                    <h3 class="title">Посмотрите похожие товары</h3>
                    <ul class="same-products">
                      <?php foreach ($products as $key=>$product) {?>
                        <li><a href="<?php echo $product['href'] ?>"><?php echo $product['name'] ?></a></li>
                      <?php } ?>
                    </ul>
                  <? } ?>

                </div>
                <div role="tabpanel" class="tab-pane fade" id="desc2" aria-labelledby="home-tab">
                  <h3>Тех. характеристики</h3>
                  <p></p>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="desc3" aria-labelledby="home-tab">
                  <h3>Применение</h3>
                  <p></p>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="delivery" aria-labelledby="home-tab">
                  <h3>Сопутствующие товары</h3>
                  <section class="comments clearfix">
                    <div class="comments-content">


                    </div>
                  </section>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="profile" aria-labelledby="profile-tab">
                  <h3>ЕСТЬ ВОПРОСЫ? ХОТИТЕ ОСТАВИТЬ ОТЗЫВ? ДЕЙСТВУЙТЕ!</h3>
                  <p>Задавайте вопросы, оставляйте отзывы. Мы знаем все о нашем товаре, кроме того, ваше мнение
                    крайне важно для нас.</p>
                  <!--  Comments -->
                  <section class="comments clearfix">
                    <div class="comments-title">
                      <h3 class="title">Комментарии</h3>
                      <p>Ставьте лайки, делитесь полезным материалом с друзьями, оставляйте вопросы и комментарии.</p>
                    </div>
                    <div class="comments-content">
                      <div id="hypercomments_widget"></div>
                    </div>
                  </section>
                  <!-- End Comments -->
                </div>
              </div>
              <h3>Смотрите также</h3>
              <div class="form-quotation">
                <div id="tg-vehicletype-slider-related-products" class="tg-vehicletype-slider owl-carousel owl-theme">
                    <?php foreach ($related_products as $key=>$related_product) { ?>
                      <div class="item tg-vehicle-type brand-filter">
                        <a href="<?php echo $related_product['href']; ?>">
                          <img src="<?php echo $related_product['thumb']; ?>">
                          <label for=" ">
                            <span><?php echo $related_product['name']; ?></span>

                          </label>
                        </a>


                      </div>
                    <?php } ?>



                </div>
              </div>
            </div>
            <!--Sidebar End-->
            <div class="col-md-3 col-md-pull-9 hidden-xs hidden-sm">

              <?php echo $column_left;?>

            </div>


          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Заказать</h4>
      </div>
      <div class="modal-body">
        <form id="order-product-form">
          <p class="success_message"></p>
          <div class="form-group">
            <label >Название товара:</label>  <i ><?php echo $heading_title; ?></i>
            <input type="hidden" name="orderNameProduct" value="<?php echo $heading_title; ?>">
            <?php if($special){ ?>
            <input type="hidden" name="orderPriceProduct" value="<?php echo $price_number_special; ?>">
            <?php } else { ?>
            <input type="hidden" name="orderPriceProduct" value="<?php echo $price_number; ?>">
            <?php } ?>
            <?php if(isset($price_eur)) { ?>
            <input type="hidden" name="orderPriceProductEur" value="<?php echo $price_eur; ?>">
            <?php } ?>
            <input type="hidden" name="orderIdProduct" value="<?php echo $product_id; ?>">
            <input type="hidden" name="orderCurrency" value="<?php echo $currency; ?>">
          </div>
          <div class="form-group">
            <label >Артикул товара:</label>  <i><?php echo $sku; ?></i>
            <input type="hidden" name="orderArticulProduct" value="<?php echo $sku; ?>">
          </div>
          <div class="form-group">
            <label >Количество:</label>
            <input type="number" name="orderCount" class="form-control" value="1" id="">
          </div>
          <div class="form-group">
            <label >Имя:</label>
            <input type="text" name="orderNameCustomer" class="form-control" id="">
          </div>
          <div class="form-group">
            <label >Телефон:</label>
            <input type="text" name="orderPhone" class="form-control" id="">
          </div>
          <div class="form-group">
            <label>Email:</label>
            <input type="email" name="orderEmail" class="form-control" id="">
          </div>
          <div class="form-group">
            <label for="comment">Комментарий:</label>
            <textarea class="form-control" name="orderComment" rows="5"></textarea>
          </div>



        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-default btn-order-product">Заказать</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  _hcwp = window._hcwp || [];
  _hcwp.push({widget: "Stream", widget_id: 66199});
  (function () {
    if ("HC_LOAD_INIT" in window)return;
    HC_LOAD_INIT = true;
    var lang = ("ru").substr(0, 2).toLowerCase();
    var hcc = document.createElement("script");
    hcc.type = "text/javascript";
    hcc.async = true;
    hcc.src = ("https:" == document.location.protocol ? "https" : "http") + "://w.hypercomments.com/widget/hc/66199/" + lang + "/widget.js";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hcc, s.nextSibling);
  })();
</script>
<?php echo $footer; ?>
