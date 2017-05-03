<div class="widget">
  <h6 class="title">Недавно Просмотренные</h6>
  <ul class="list-unstyled recent-posts">
    <?php foreach ($products as $product) { ?>
      <li>
        <a class="clearfix recent_item" href="<?php echo $product['href']; ?>">
          <span class="recent_photo"><img alt="К250RF90" src="<?php echo $product['thumb']; ?>"></span>
                  <span class="recent_txt"><?php echo $product['name']; ?>
                    <br><?php echo $product['price'] ?></span>
        </a>
      </li>
    <?php } ?>
  </ul>
</div>




