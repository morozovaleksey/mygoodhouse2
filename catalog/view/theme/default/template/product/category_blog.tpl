<?php echo $header;
?>

<div class="inside_title image_bck bordered_wht_border white_txt" data-image="/image/<?php echo $back_thumb; ?>">
  <!-- Over -->
  <div class="over" data-opacity="0.5" data-color="#000"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-4"><h1><?php echo $heading_title; ?></h1></div>
      <div class="col-md-8 text-right">


        <div class="breadcrumbs">
          <ol>

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

              <?php if($products) { ?>
                <div class="row masonry " style="">
                  <?php foreach($products as $product) { ?>


                    <div class="col-sm-6 post-snippet masonry-item" style="">
                      <a href="<?php echo $product['href']; ?>">
                        <?php if(isset($product['attributes'][0])) {?>
                        <img alt="" src="http://img.youtube.com/vi/<?php echo $product['attributes'][0]['text']; ?>/hqdefault.jpg">
                        <? } else { ?>
                        <img alt="" src="<?php echo $product['thumb']; ?>">
                        <?php } ?>
                      </a>

                      <div class="inner">
                        <a href="<?php echo $product['href']; ?>">
                        </a>
                        <h4 class="title"><a href="<?php echo $product['href']; ?>">

                          </a>
                          <a href="<?php echo $product['href']; ?>"><?php echo $product['name'] ?></a>
                        </h4>
                        <span class="date"> <?php echo $product['date_available']; ?></span>
                        <ul class="post-meta list-unstyled list-inline">
                          <li>
                            <i class="fa fa-folder-o"></i>
                              <span>
                                  <a href="<?php echo $product['category_href'] ?>"><?php echo $product['category']['name'];?></a>
                              </span>
                          </li>
                        </ul>
                        <?php echo $product['short_description']; ?>
                        <a class="btn btn-default" href="<?php echo $product['href']; ?>">Читать полностью</a>
                      </div>
                    </div>


                <?php }?>
                </div>
              <?php }?>


              <div class="text-center">
                <?php echo $pagination; ?>
              </div>



            </div>
            <div class="col-md-3 col-md-pull-9 hidden-xs hidden-sm">

              <div class="widget">
                <h6 class="title">Блог Good House</h6>
                <p>
                  В этот разделе мы делимся с вами ценными знаниями. Акции, новинки рынка элитных материалов, инструкции, обзоры,
                  полезные советы - все здесь.
                </p>
              </div>
              <div class="widget">
                <h6 class="title">Категории</h6>
                <ul class="list-unstyled">
                  <?php foreach($blogs as $blog){ ?>
                  <li><a href="<?php echo $blog['href']; ?>"><?php echo $blog['name']; ?></a></li>
                  <?php } ?>
                </ul>
              </div>
              <div class="widget">
                <h6 class="title">Недавние записи</h6>
                <ul class="list-unstyled recent-posts">

                  <?php foreach($products_recent_blog as $product_recent_blog) { ?>
                  <li>
                    <a href="<?php echo $product_recent_blog['href']; ?>">
                      <?php echo $product_recent_blog['name'] ?>
                    </a>              <span class="date"><?php echo $product_recent_blog['date_available'];?></span>
                  </li>
                  <?php } ?>

                </ul>
              </div>


            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
