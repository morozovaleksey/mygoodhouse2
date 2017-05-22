


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

                    <h4 class="inline-block"><?php echo $heading_title; ?></h4>

                <!-- Item -->
                <?php if (($results_certificates)) { ?>
                    <h3>Сертификаты</h3>
                <ul>
                    <?php foreach($results_certificates as $key => $certificate) {?>

                    <li><a href="/image/<?php echo $certificate['image']; ?>"><?php echo $certificate['name'];?></a></li>
                    <?php } ?>
                </ul>
                <?php } else { ?>
                  <p>У данного производителя нет сертификатов</p>
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
