



<?php echo $header; ?>

<div class="inside_title image_bck bordered_wht_border white_txt" data-image="/image/catalog/categories/back/fff.jpg">
  <!-- Over -->
  <div class="over" data-opacity="0.5" data-color="#000"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-4"><h1><?php echo $heading_title; ?></h1></div>
      <div class="col-md-8 text-right">


        <div class="breadcrumbs">
          <ol>
            <li><a href="/">Главная</a><span id="separator"></span></li>
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

              <div class="post-snippet" style="background: rgba(255, 255, 255, 0.7); padding: 25px;">
                <!-- Item -->
                <?php if ($categories) { ?>
                  <p><strong><?php echo $text_index; ?></strong>
                    <?php foreach ($categories as $category) { ?>
                      &nbsp;&nbsp;&nbsp;<a href="index.php?route=product/manufacturer#<?php echo $category['name']; ?>"><?php echo $category['name']; ?></a>
                    <?php } ?>
                  </p>
                  <?php foreach ($categories as $category) { ?>
                    <h2 id="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></h2>
                    <?php if ($category['manufacturer']) { ?>

                      <?php foreach (array_chunk($category['manufacturer'], 5) as $manufacturers) { ?>
                        <div class="row">
                          <div class="form-quotation">
                            <div id="" class="tg-vehicletype-slider owl-carousel owl-theme tg-vehicletype-slider-related-products">
                              <?php

                              foreach ($manufacturers as $manufacturer) {
                                ?>
<!--                                <div class="col-sm-3"><a href="--><?php //echo $manufacturer['href']; ?><!--">--><?php //echo $manufacturer['name']; ?><!--</a></div>-->
                                <div class="item tg-vehicle-type brand-filter">
                                  <a href="<?php echo $manufacturer['href']; ?>">
                                    <img src="<?php echo $manufacturer['thumb']; ?>">
                                    <label for=" ">
                                      <span><?php echo $manufacturer['name']; ?></span>

                                    </label>
                                  </a>


                                </div>
                              <?php } ?>

                            </div>
                          </div>

                        </div>
                      <?php } ?>
                    <?php } ?>
                  <?php } ?>
                <?php } else { ?>
                  <p><?php echo $text_empty; ?></p>
                  <div class="buttons clearfix">
                    <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
                  </div>
                <?php } ?>
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

