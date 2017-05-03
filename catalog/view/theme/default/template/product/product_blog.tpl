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

              <!--Item-->
              <div class="post-snippet">
                <?php if(($attributes !="")) { ?>
                  <iframe width="853" height="480" src="https://www.youtube-nocookie.com/embed/<?php echo $attributes[0]['text']; ?>" frameborder="0" allowfullscreen=""></iframe>

                <?php } else {?>
                  <img alt="<?php echo $heading_title;?>" src="<?php echo $thumb; ?>">
                <?php } ?>
                <div class="post-title">
                  <span class="label"> <?php echo $date_available; ?></span>
                  <h4 class="inline-block"><?php echo $heading_title;?></h4>
                </div>

                <ul class="post-meta list-unstyled list-inline">
                  <li>
                    <i class="fa fa-folder-o"></i>
                    <span>
                      <a href="<?php echo $category_href; ?>"><?php echo $category_info_product['name']; ?></a>
                    </span>
                  </li>
                </ul>



                <?php echo $description; ?>


              </div>

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
