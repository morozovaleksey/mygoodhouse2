
<!--Hotel-->
<section class="intro" id="start">
  <!-- Down Arrow -->
  <a href="#welcome" class="down_block go"><i class="fa fa-angle-down"></i></a>
  <!-- Wrapper -->
  <div class="intro_wrapper">

    <?php foreach ($banners as $banner) { ?>
      <!-- Item -->
      <div class="intro_item">

        <!-- Over -->
        <div class="over" data-opacity="0.2" data-image="/assets/images/overlay.png" data-color="#302313"></div>
        <div class="into_back image_bck" data-image="/image/<?php echo $banner['image']; ?>"></div>
        <div class="text_content">
          <div class="intro_text intro_text_lc text-right text_up">
            <span class="great_title great_title_big"><?php echo $banner['title']; ?></span>
            <span class="great_subtitle great_subtitle_big"><?php echo $banner['sub_title']; ?></span>
            <span class="into_txt"></span>
                  <span>
                    <a href="/" class="btn btn-white">К выбору материалов</a>
                    <a class="btn btn-white" href="javascript:$zopim.livechat.window.show();">Связаться с консультантом</a>
                  </span>
          </div>

        </div>
      </div>
    <?php } ?>




  </div>
  <!-- Wrapper End -->

  <!-- Intro End -->
  <!-- Slider Border -->
  <div class="after_slider_border"></div>
</section>