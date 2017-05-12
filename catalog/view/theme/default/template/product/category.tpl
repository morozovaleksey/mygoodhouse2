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
            <div class="col-md-9 col-md-push-3 col-xs-12">

              <div class="row no-gutter taxodesc">
                <!-- Item -->
                <div class="col-sm-5 post-snippet">
                  <a href="##taxotext" class="bordered_wht_border">
                    <img alt="<?php echo $heading_title ?>" src="<?php echo $thumb; ?>">
                  </a>
                </div>
                <div class="col-sm-7 post-snippet">
                  <div class="inner">

                      <h4 class="title"><?php echo $heading_title ?> </h4>

                    <?php echo $short_description; ?>
                  </div>
                </div>
              </div>

              <?php
              if($brands) {?>
              <h3 class="amount">Бренд:</h3>
              <div class="form-quotation">
                <div id="tg-vehicletype-slider" class="tg-vehicletype-slider owl-carousel owl-theme">

                  <?php foreach($brands as $brand) { ?>

                  <div class="item tg-vehicle-type brand-filter">

                      <i class=""><img src="<?php echo $brand['image']; ?>"></i>
                      <input id="<?php echo $brand['name']; ?> " value="<?php echo $brand['value_id']; ?>" type="checkbox" name="brands">
                      <label for="<?php echo $brand['name']; ?> "><span>
                        <?php

                        if($brand['products_count']== 1) {
                          echo $brand['products_count']." товар";
                        } elseif($brand['products_count'] == 2 or $brand['products_count']== 3 or $brand['products_count']== 3 ){
                          echo $brand['products_count']." товара";
                        } else {
                          echo $brand['products_count']." товаров";
                        }

                        ?>
                      </span>

                      </label>



                  </div>

                  <?php } ?>
                </div>
              </div>
              <?php } ?>



              <div class="text-left">
                <?php echo $pagination; ?>
              </div>


              <?php if($products) { ?>
                <div class="row masonry no-gutter" style="">
                  <?php foreach($products as $product) { ?>


                      <div class="col-sm-4 masonry-item" id="55667" itemscope="" itemtype="https://schema.org/Product">
                        <a itemprop="url" class="product_item text-center" href="<?php echo $product['href']; ?>">
                          <span class="product_photo bordered_wht_border"><img itemprop="image" alt="<?php echo $product['name'] ?>" src="<?php echo $product['thumb']; ?>"></span>
                          <span class="product_title" itemprop="name" title="Футуро серый"><?php echo $product['name'] ?></span>
                            <span class="product_price" itemprop="price" itemscope="" itemtype="https://schema.org/Offer">
                              Цена за кв.м.: <?php echo $product['price']; ?>
                            </span>
                            <span class="product_price" itemprop="price" itemscope="" itemtype="https://schema.org/Offer">
                              Цена за шт.: <?php if($product['price2'] == '0 р.') { echo 'Под заказ'; } else { echo $product['price2']; } ?>
                            </span>
                          <!--span class="sale">Sale</span>
                          <span class="new">New</span-->
                        </a>
                      </div>


                <?php }?>
                </div>
              <?php }?>


              <div class="text-left">
                <?php echo $pagination; ?>
              </div>

              <div class="col-md-12 col-xs-12" id="taxotext">
                <!--Item-->
                <div class="post-snippet bottom">
                  <h4 class="inline-block"><?php echo $heading_title; ?></h4>

                  <?php echo $description; ?>
                </div>
              </div>

            </div>
            <div class="col-md-3 col-md-pull-9 hidden-xs hidden-sm">

                <?php echo $column_left;?>


            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
