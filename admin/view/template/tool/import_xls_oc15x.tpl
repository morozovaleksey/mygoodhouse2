<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if (!empty($this->session->data['error'])) { ?>
    <div class="warning"><?php echo $this->session->data['error']; unset($this->session->data['error']) ?></div>
  <?php } ?>
  <?php if (!empty($this->session->data['success'])) { ?>
    <div class="success"><?php echo $this->session->data['success']; unset($this->session->data['success']) ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons">
        <?php if (!empty($button_apply_allowed)){ ?>
          <a onclick="$('input[name=no_exit]').val(1);save_configuration_ajax();" class="button"><?php echo $apply_changes; ?></a>
        <?php } ?>

        <?php if (!empty($button_save_allowed)){ ?>
          <a onclick="ajax_loading_open();$('input[name=no_exit]').val(0);$('form').submit();" class="button"><?php echo $button_save; ?></a>
        <?php } ?>
        <a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <?php echo $form; ?>
    </div>
  </div>
</div>
<script type="text/javascript">
  $( document ).ready(function() {
    $('#tabs a').tabs();
    $('.date').datepicker({dateFormat: 'yy-mm-dd'});
  });
</script>

<script type="text/javascript">
  var token = '<?php echo $token; ?>';
</script>

<script type="text/javascript">
  function image_upload(field, thumb) {
    $('#dialog').remove();
    
    $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
    
    $('#dialog').dialog({
      title: '<?php echo $text_image_manager; ?>',
      close: function (event, ui) {
        if ($('#' + field).attr('value')) {
          $.ajax({
            url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
            dataType: 'text',
            success: function(data) {
              $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
            }
          });
        }
      },  
      bgiframe: false,
      width: 800,
      height: 400,
      resizable: false,
      modal: false
    });
  };
</script> 


<script type="text/javascript">
  var url_export = '<?php echo $url_export; ?>';
  var convert_to_innodb_url = '<?php echo htmlspecialchars_decode($convert_to_innodb); ?>';
  var save_configuration_url = '<?php echo htmlspecialchars_decode($save_configuration); ?>';
</script>

<?php echo $footer; ?>