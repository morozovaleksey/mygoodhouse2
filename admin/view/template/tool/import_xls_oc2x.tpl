<?php echo $header; ?>
<?php echo $column_left; ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <?php if (!empty($button_apply_allowed)){ ?>
                  <button onclick="ajax_loading_open();$('input[name=no_exit]').val(1);save_configuration_ajax();" type="submit" form="form-account" data-toggle="tooltip" title="<?php echo $apply_changes; ?>" class="btn btn-primary"><i class="fa fa-check"></i></button>
                <?php } ?>

                <?php if (!empty($button_save_allowed)){ ?>
                  <button onclick="ajax_loading_open();$('input[name=no_exit]').val(0);$('form#<?php echo $form_view['id']; ?>').submit()" type="submit" form="form-account" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <?php } ?>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
                <h1><?php echo $heading_title; ?></h1>
                <ul class="breadcrumb">
                    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="container-fluid">
            <?php
                $error_message = '';
                foreach ($_SESSION as $key_session => $value) {
                    if(is_array($value))
                    {
                        foreach ($value as $key => $val) {
                            if($key == 'error' && !empty($val))
                            {
                                $error_message = $val;
                                unset($_SESSION[$key_session]['error']);
                            }
                        }
                    }
                }
                
                if(!empty($_SESSION['error']))
                {
                    $error_message = $_SESSION['error'];
                    unset($_SESSION['error']);
                }
                elseif(!empty($_SESSION['default']['error']))
                {
                    $error_message = $_SESSION['default']['error'];
                    unset($_SESSION['default']['error']);
                }
                ?>

                <?php
                $info_message = '';
                foreach ($_SESSION as $key_session => $value) {
                    if(is_array($value))
                    {
                        foreach ($value as $key => $val) {
                            if($key == 'info' && !empty($val))
                            {
                                $info_message = $val;
                                unset($_SESSION[$key_session]['info']);
                            }
                        }
                    }
                }
                
                if(!empty($_SESSION['info']))
                {
                    $info_message = $_SESSION['info'];
                    unset($_SESSION['info']);
                }
                elseif(!empty($_SESSION['default']['info']))
                {
                    $info_message = $_SESSION['default']['info'];
                    unset($_SESSION['default']['info']);
                }
                ?>

                <?php
                $success_message = '';
                foreach ($_SESSION as $key_session => $value) {
                    if(is_array($value))
                    {
                        foreach ($value as $key => $val) {
                            if($key == 'success' && !empty($val))
                            {
                                $success_message = $val;
                                unset($_SESSION[$key_session]['success']);
                            }
                        }
                    }
                }

                if(!empty($_SESSION['success']))
                {
                    $success_message = $_SESSION['success'];
                    unset($_SESSION['success']);
                }
                elseif(!empty($_SESSION['default']['success']))
                {
                    $success_message = $_SESSION['default']['success'];
                    unset($_SESSION['default']['success']);
                }
            ?>

            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php } ?>

            <?php if (!empty($success_message)) { ?>
                <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success_message; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php } ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
                </div>
                <div class="panel-body">
                    <?php echo $form; ?>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var token = '<?php echo $token; ?>';
    </script>

    <script type="text/javascript">
        var url_export = '<?php echo $url_export; ?>';
        var convert_to_innodb_url = '<?php echo htmlspecialchars_decode($convert_to_innodb); ?>';
        var save_configuration_url = '<?php echo htmlspecialchars_decode($save_configuration); ?>';
    </script>

<?php echo $footer; ?>