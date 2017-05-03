
<div class="widget">
  <h6 class="title">Каталог</h6>

<ul class="metisFolder" id="menu2">
  <?php foreach ($categories as $category) { ?>
    <?php if ($category['category_id'] == $category_id) { ?>
      <li class="active">
        <a rel="nofollow" href="<?php echo $category['href']; ?>" class="doubleTapToGo"><span class="fa fa-angle-double-right"></span> <?php echo $category['name']; ?>
        </a>
        <?php if ($category['children']) { ?>
          <ul id="inside-ul" class="in collapse" aria-expanded="true">
            <?php foreach ($category['children'] as $child) { ?>
              <?php if ($child['category_id'] == $child_id) { ?>
                <li class="active"><a rel="nofollow" href="<?php echo $child['href']; ?>"><span class="fa fa-angle-double-right"></span> <?php echo $child['name']; ?></a>
              <?php } else { ?>
                <li><a rel="nofollow" href="<?php echo $child['href']; ?>"><span class="fa fa-angle-double-right"></span> <?php echo $child['name']; ?></a>
              <?php } ?>
                <?php if ($child['children']) { ?>
            <?php if ($child['category_id'] == $child_id) { ?>
                <ul id="inside-ul" class="in collapse" aria-expanded="true">
                  <?php } else { ?>
                  <ul id="inside-ul" class="collapse" aria-expanded="true">
                    <?php } ?>
                  <?php foreach ($child['children'] as $sub_child) { ?>
                    <?php if ($sub_child['category_id'] == $sub_child_id) { ?>
                      <li class="active"><a rel="nofollow" href="<?php echo $sub_child['href']; ?>"><span class="fa fa-angle-double-right"></span> <?php echo $sub_child['name']; ?></a></li>
                    <?php } else { ?>
                      <li><a rel="nofollow" href="<?php echo $sub_child['href']; ?>"><span class="fa fa-angle-double-right"></span> <?php echo $sub_child['name']; ?></a></li>
                    <?php } ?>
                  <?php } ?>
                </ul>
              <?php } ?>
                  </li>
            <?php } ?>
          </ul>
        <?php } ?>
      </li>

    <?php } else { ?>
      <li class="">
        <a rel="nofollow" href="<?php echo $category['href']; ?>" ><span class="fa fa-angle-double-right"></span> <?php echo $category['name']; ?>
        </a>
        <?php if ($category['children']) { ?>
          <ul id="inside-ul" class="collapse" aria-expanded="false">
            <?php foreach ($category['children'] as $child) { ?>
              <?php if ($child['category_id'] == $child_id) { ?>
                <li class=""><a rel="nofollow" href="<?php echo $child['href']; ?>"><span class="fa fa-angle-double-right"></span> <?php echo $child['name']; ?></a>
              <?php } else { ?>
                <li><a rel="nofollow" href="<?php echo $child['href']; ?>"><span class="fa fa-angle-double-right"></span> <?php echo $child['name']; ?></a>
              <?php } ?>
              <?php if ($child['children']) { ?>
                <ul id="inside-ul" class="collapse" aria-expanded="true">
                  <?php foreach ($child['children'] as $sub_child) { ?>

                    <?php if ($sub_child['category_id'] == $sub_child_id) { ?>
                      <li class=""><a rel="nofollow" href="<?php echo $sub_child['href']; ?>"><span class="fa fa-angle-double-right"></span> <?php echo $sub_child['name']; ?></a></li>
                    <?php } else { ?>
                      <li><a rel="nofollow" href="<?php echo $sub_child['href']; ?>"><span class="fa fa-angle-double-right"></span> <?php echo $sub_child['name']; ?></a></li>
                    <?php } ?>
                  <?php } ?>
                </ul>
              <?php } ?>
              </li>
            <?php } ?>
          </ul>
        <?php } ?>
      </li>
    <?php } ?>
  <?php } ?>
</ul>
</div>

<div class="widget">
</div>





