
<!-- Contacts -->
<section class="boxes about" id="contacts" >
  <div class="container-fluid">

    <div class="row">

      <!-- Contacts -->
      <div class="col-md-5 bordered_block image_bck bordered_wht_border white_txt address-block" data-color="#292929">
        <div class="over" data-opacity="0.6" data-color="#292929"></div>
        <div class="col-md-12 simple_block text-left">
          <h3>Контакты</h3>
          <h4>
            <small class="gold_txt">Самара</small>
          </h4>
          <!--span class="contacts_ti ti-headphone-alt"></span>тел/факс: +7 (846) 267-57-56<br/-->
          <span class="contacts_ti ti-mobile"></span>моб: +7(917) 107-57-56<br/>
          <span class="contacts_ti ti-email"></span>
          <a encode="hex" href="mailto:samara@mygood.house">samara@mygood.house</a>
          <br/>
          <span class="contacts_ti ti-location-pin"></span>г. Самара, ул. Санфировой, дом 95, офис 205
          <br><br>
          <h4>
            <small class="gold_txt">Казань</small>
          </h4>
          <span class="contacts_ti ti-headphone-alt"></span>тел/факс: +7 (843) 226-60-88<br/>
          <span class="contacts_ti ti-mobile"></span>моб: +7 (987) 277-11-00<br/>
          <span class="contacts_ti ti-email"></span>
          <a encode="hex" href="mailto:kazan@mygood.house">kazan@mygood.house</a>
          <br/>
          <span class="contacts_ti ti-location-pin"></span>г. Казань, ул. Васильченко, дом 16
          <br><br>
          <h4>
            <small class="gold_txt">Набережные Челны</small>
          </h4>
          <span class="contacts_ti ti-headphone-alt"></span>тел/факс: +7 (8552) 36-69-86, 53-48-56<br/>
          <span class="contacts_ti ti-mobile"></span>моб: +7 (917) 396-09-06<br/>
          <span class="contacts_ti ti-email"></span>
          <a encode="hex" href="mailto:chelny@mygood.house">chelny@mygood.house</a>
          <br/>
          <span class="contacts_ti ti-location-pin"></span>г. Набережные Челны, проспект Мира, дом 103
        </div>
      </div>

      <!-- Write Us -->
      <div class="col-md-7 bordered_block bordered_wht_border map iframe_full">
        <div id="gmap-menu" style="with:auto;height:100%; z-index: 100000"></div>
      </div>
      <!-- Write Us End -->

    </div>
    <!-- Row End -->
  </div>
</section>
<!-- Contacts End -->


<script defer type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5743e4857ad1e613"></script>
<script src="http://maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.22&key=AIzaSyBWUuwUH-eitrLNSklH1xnsYtZcR1Vv8xQ"></script>
<script type='text/javascript'>

  $(document).ready(function () {

    new Maplace({
      locations: [
        {
          lat: 53.217499,
          lon: 50.197741,
          title: 'Офис в Самаре',
          html: [
            '<h6>ул. Санфировой, дом 95, офис 205</h6>',
            '<p class="gold_txt">Консультации экспертов, услуги дизайнера и архитектора, шоу-рум.</p>'
          ].join(''),
          zoom: 17,
          icon: 'http://maps.google.com/mapfiles/markerA.png',
          animation: google.maps.Animation.DROP
        },
        {
          lat: 55.838626,
          lon: 49.040577,
          title: 'Офис в Казани',
          html: [
            '<h6>г. Казань, ул. Васильченко, дом 16 </h6>',
            '<p class="gold_txt">Консультации экспертов, услуги дизайнера и архитектора, шоу-рум.</p>'
          ].join(''),
          zoom: 17,
          icon: 'http://maps.google.com/mapfiles/markerB.png',
          animation: google.maps.Animation.DROP
        },
        {
          lat: 55.764035,
          lon: 52.444637,
          title: 'Офис в Набережных Челнах',
          html: [
            '<h6>г. Набережные Челны, проспект Мира, дом 103</h6>',
            '<p class="gold_txt">Консультации экспертов, услуги дизайнера и архитектора, шоу-рум.</p>'
          ].join(''),
          zoom: 17,
          icon: 'http://maps.google.com/mapfiles/markerC.png',
          animation: google.maps.Animation.DROP
        }
      ],
      map_div: '#gmap-menu',
      controls_type: 'list',
      controls_on_map: true,
      view_all: false
    }).Load();
  });
</script>

<!-- Footer -->
<div class="footer white_txt image_bck no-cover" data-image="/assets/images/black_denim-b3c167a61005409ac9fea8e72122a1045ad2ac8be4dd5597ce26fbc993c5224b.png">
  <div class="container">

    <div class="row">
      <div class="col-md-4 col-sm-4 mgt28">
        <div class="logo text-left">
          <a href="/"><img class="maxilogo" src="/assets/images/logo-final-e9cb58b9fbd15f8273427e906ecfebb8212a753555103d9687b68a0697d8514a.png" alt="Logo final"></a>
        </div>
        <p class="underlogo white_txt">Гуд Хаус - это поставщик керамического, облицовочного, клинкерного кирпича и
          черепицы в Самаре, Казани,
          Набережных Челнах. <br>Мы - официально аккредитованные представители ведущих европейских и российских заводов
          и
          поставляем материал по гарантировано минимальной цене.</p>
      </div>

      <div class="col-md-4 col-sm-4 hidden-xs">
        <div class="widget">
          <h4>Каталог материалов</h4>
          <ul class="list-unstyled">
            <li>
              <a href="/t/spietspriedlozhieniia">Спецпредложения</a>
              <span class="date">Скидки, хиты продаж и новинки</span>
            </li>
            <li>
              <a href="/t/fasad">Фасадные материалы</a>
              <span class="date">Лучшие материалы для возведения стен и облицовки</span>
            </li>
            <li>
              <a href="/t/krovlya">Кровельные материалы</a>
              <span class="date">Что может быть важнее хорошей кровли!</span>
            </li>
            <li>
              <a href="/t/sukhiie-stroitielnyie-smiesi">Сухие строительные смеси</a>
              <span class="date">Все виды сухих смесей для фасадных работ и ландшафтного дизайна</span>
            </li>
            <li>
              <a href="/t/blaghoustroistvo">Благоустройство</a>
              <span class="date">Все лучшее для ландшафтного дизайна</span>
            </li>
          </ul>
        </div>

      </div>

      <!--div class="col-md-3 col-sm-3 hidden-xs">
        <div class="widget">
          <h4>Новости</h4>
          <%#= render 'spree/shared/news2footer', recent_blog_entries: Spree::BlogEntry.recent(4) %>
        </div>
      </div-->

      <div class="col-md-4 col-sm-4 hidden-xs">
        <div class="widget">
          <h4>Горячая линия</h4>
          <address>
            <strong>Не стесняйтесь звонить:</strong><br>
            <abbr title="Phone">Самара:</abbr> +7 (917) 107-57-56<br>
            <abbr title="Phone">Казань:</abbr> +7 (843) 22-66-088<br>
            <abbr title="Phone">Набережные Челны:</abbr> +7 (8552) 36-69-86, 53-48-56
          </address>
        </div>
        <!--end of widget-->
      </div>

    </div>
    <!--Row End-->

  </div>
  <!-- Container End -->

  <!-- Footer Copyrights -->
  <div class="footer_end">
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          <span class="sub">&copy;
            ОГРН: 1111690034708, ГК "Гуд Хаус" 2016 | создание сайта: <a target="_blank" href="http://connecticus.ru">CONNECTICUS</a></span>
        </div>
        <div class="col-sm-6 text-right">
          <!-- Go to www.addthis.com/dashboard to customize your tools -->
          <div class="addthis_sharing_toolbox"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- Copyrights End -->


</div>
<!-- Footer End -->

</div>

<!--
OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donation.
Please donate via PayPal to donate@opencart.com
//-->

<!-- Theme created by Welford Media for OpenCart 2.0 www.welfordmedia.co.uk -->


</body></html>