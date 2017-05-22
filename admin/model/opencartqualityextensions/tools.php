<?php 

/*
    V 1.2.1.4

    CHANGELOG:
    
    2016-04-09: Fix to flag image to OC 2.2.X
    2016-05-08: Added fields to generate table function OC.2.X. (before only allow text)
    2016-05-17: Fix to index 'value' not defined in date input. Add class "products_autocomplete" to input text of input type "products_autocomplete"
    2016-05-18: Fix to loose fields in table inputs. Add multilanguage in table input
    2016-05-20: Added fields to generate table function OC.1.5.X. (before only allow text)
    2016-06-01: Fix to insert loose values in inputs table.
    2016-06-06: Unify all code.
    2016-06-07: Fixed miror bugs in OC2
    2016-06-08: Add button upload_image (only to OC.1.5.X)
    2016-06-11: Add select multiple and bootstrap select
    2016-06-12: Some bug fixed in form OC15X
    2016-06-16: Added $this->extension_group_config to generate field function - Bug fixed to 1.5.x versions in no table inputs
    2016-07-23: Add input type password
    2016-08-25: Fix problem pass array values configuration to input tables, conflict only in OC 2.0.0.0 version
    2016-09-10: Fix multi store document on ready, put 0 instead of value select store beacuse was "undefined"
    2016-09-28: Fix loose value boolean in table inputs
    2016-10-03: Fix error javascript document ready hide all stores less 0 in multistore form
    2016-10-20: Added "style_container" params
    2016-10-21: Added "force_function" params
    2016-11-20: Added function "events_after_add_new_row_table_inputs" jquery to button add new row
*/

class ModelOpencartqualityextensionsTools extends Model {

    public function generateForm($form_view)
    {
        $html = '<form autocomplete="off" action="'.$form_view['action'].'" method="post" enctype="multipart/form-data" id="'.$form_view['id'].'" class="form-horizontal"><input type="hidden" value="0" name="no_exit"><input type="hidden" value="" name="force_function">';
            $html .= $this->generateTabs($form_view);
            $html .= $this->generateContent($form_view);
        $html .= '</form>';

        return $html;
    }

    public function generateTabs($form_view)
    {
        $stores = $this->getStores();
        $html = '';

        //Multi Store - Add select stores
            if(!empty($form_view['multi_store']))
            {
                $html .= '<script type="text/javascript">
                                $(document).on(\'ready\', function(){';
                                    if(version_compare(VERSION, '2.0.0.0', '>='))
                                    {
                                        $html .= '$(\'div.tab-content div.store_input\').hide();';
                                        $html .= '$(\'div.tab-content div.store_0\').show();';
                                    }else
                                    {
                                        $html .= '$(\'div.content form tr.store_input\').hide();';
                                        $html .= '$(\'div.content form tr.store_0\').show();';
                                    }

                                $html .= '});';
                            $html .= '</script>';

                $options_select = array();
                foreach ($stores as $key => $store)
                    $options_select[$store['store_id']] = $store['name'];

                $temp_input = array(
                    'label' => '<i class="fa fa-home"></i>'.$this->language->get('choose_store'),
                    'type' => 'select',
                    'name' => 'stores',
                    'onchange' => 'change_store($(this).val());',
                    'value' => '',
                    'options' => $options_select
                );

                if(version_compare(VERSION, '1.5.6.4', '<='))
                {
                    $temp_input['remove_border_button'] = true;
                    $temp_input['before'] = '<table class="form multistore_table"><tbody>';
                    $temp_input['after'] = '</tbody></table>';
                }

                $html .= $this->generateField($temp_input);
            }
        //END Multi Store - Add select stores

        if(version_compare(VERSION, '2.0.0.0', '>='))
        {
            $html .= '<ul class="nav nav-tabs">';
                $count = 0;
                foreach ($form_view['tabs'] as $tab_name => $tab) {
                    $html .= '<li '.($count == 0 ? 'class="active"':'').'><a class="tab_'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'" href="#tab-'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'" data-toggle="tab">'.(!empty($tab['icon'])?$tab['icon']:'').$tab_name.'</a></li>';
                    $count++;
                }
            $html .= '</ul>';
        }
        else
        {
            $html .= '<div id="tabs" class="htabs">';
                $count = 0;
                foreach ($form_view['tabs'] as $tab_name => $tab) {
                    $html .= '<a class="'.($count == 0 ? 'selected ':'').'tab_'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'" href="#tab-'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'">'.(!empty($tab['icon'])?$tab['icon']:'').$tab_name.'</a>';
                    $count++;
                }
            $html .= '</div>';
        }

        return $html;
    }

    public function generateField($field, $language = null)
    {
        $extension_name = $this->extension_group_config;

        if ($field['type'] == 'html_hard')
            return $field['html_code'];

        if(empty($field['name']))
            $field['name'] = '';
        
        //If possible that call to this function from table inputs, not add extension name to field name.
        if(substr($field['name'], 0, strlen ($extension_name)) != $extension_name)
            $field['name'] = !empty($extension_name) ? $extension_name.'_'.$field['name'] : $field['name'];

        //If input if multilanguage edit id and name
            if(!empty($language))
            {
                $temp_name = $field['name'];
                if(!empty($field['name']) && !empty($language) && substr($field['name'], -1) == ']')
                {
                    $field['name'] = $temp_name.'['.$language['code'].']';
                    $field['id'] = $temp_name.'['.$language['code'].']';
                }
                else
                {
                    $field['name'] = $temp_name.'_'.$language['code'];
                    $field['id'] = $temp_name.'_'.$language['code'];
                }
            }
        //END If input if multilanguage edit id and name

        if(empty($field['value']))
            $field['value'] = $this->config->get($field['name']);

        $only_input = isset($field['only_input']) ? $field['only_input'] : false;
        $table = !empty($field['table']) ? true : false;

        $html = '';
        if(version_compare(VERSION, '2.0.0.0', '>='))
        {
            $input_container_begin = '<div class="form-group'.(!empty($field['class_container']) ? ' '.$field['class_container'] : '').(!empty($field['store']) ? ' store_input '.$field['store']:'').'" '.($field['type'] == 'hidden' ? 'style="display:none;"':'').(!empty($field['style_container']) ? ' style="'.$field['style_container'].'"' : '').'>';
            $input_container_end = '</div></div>';
        }
        else
        {
            $html = '';
            $input_container_begin = '';
            $input_container_end = '';

            if($table)
            {
                $html = '<tr>';
                $input_container_begin = '';
                $input_container_end = '</td>';
            }
            else
            {
                if(!$only_input && in_array($field['type'], array('text', 'boolean', 'select', 'textarea', 'html_editor')))
                {
                    $html = '<div class="row_no_table">';
                    $input_container_begin = '<div class="input_no_table">';
                    $input_container_end = '</div></div>';
                }
                
            }
        }

        if($only_input){
            $input_container_begin = '';
            $input_container_end = '';
        }

        $placeholder = !empty($field['placeholder']) ? $field['placeholder'] : '';
        $placeholder = empty($placeholder) && !empty($field['label']) ? $field['label'] : $placeholder;

        if(version_compare(VERSION, '2.0.0.0', '>='))
            $html .= $input_container_begin;

        //Label
            if (!empty($field['label']))
            {
                if(version_compare(VERSION, '2.0.0.0', '>='))
                {
                    if (empty($language['code']))
                        $html .= '<label class="col-sm-2 control-label">'.$field['label'].(!empty($field['help'])?'<span data-toggle="tooltip" title="'.$field['help'].'"></span>':'').'</label>';
                    else
                    {
                        if(version_compare(VERSION, '2.2.0.0', '>='))
                            $flag_route = 'language/'.$language['code'].'/'.$language['code'].'.png';
                        else
                            $flag_route = 'view/image/flags/'.$language['image'];  

                        $html .= '<label class="col-sm-2 control-label"><img src="'.$flag_route.'">&nbsp;&nbsp;'.$field['label'].(!empty($field['help'])?'<span data-toggle="tooltip" title="'.$field['help'].'"></span>':'').'</label>';
                    }
                }
                else
                {
                    if($table)
                    {
                        $html .= '<td>'.(!empty($language['code']) ? '<img src="view/image/flags/'.$language['image'].'">&nbsp;&nbsp;':'').$field['label'].(!empty($field['help']) ? '<br><span class="help">'.$field['help'].'</span>':'').'</td>'.$input_container_begin;
                    }
                    elseif(!$table)
                    {
                        $html .= '<label class="col-sm-2 control-label">'.(!empty($language['code']) ? '<img src="view/image/flags/'.$language['image'].'">&nbsp;&nbsp;':'').$field['label'].(!empty($field['help']) ? '<br><span class="help">'.$field['help'].'</span>':'').'</label>'.$input_container_begin;
                    }
                }
            }
        //END Label

        //Container parent input
            if(!$only_input && version_compare(VERSION, '2.0.0.0', '>='))
            {
                $full_width = in_array($field['type'], array('legend','html_code','table_inputs')) || empty($field['label']);

                $html .= '<div class="col-sm-'.($full_width ? '12': '10').'"'.(!empty($field['style_content']) ? ' style="'.$field['style_content'].'"': '').'>';
            }elseif(!$only_input && version_compare(VERSION, '1.5.6.4', '<='))
            {
                $full_width = in_array($field['type'], array('legend','html_code','module','button','table','table_inputs')) && empty($field['label']);

                if($full_width)
                    $html .= $field['type'] != "module" ? '<td colspan="2">':'<td style="padding:20px 0px !important;" colspan="2">';
                else
                    $html .= '<td'.(!empty($field['style_content']) ? ' style="'.$field['style_content'].'"': '').'>';
            }
        //END Container parent input

        //Input
            switch ($field['type']) {
                case 'boolean':
                    $html .= '<label class="checkbox_container"><input name="'.$field['name'].'" type="checkbox" class="ios-switch green" value="1"'.($field['value']==1 ? 'checked="selected"':'').'/><div><div></div></div></label>';
                break;

                case 'text':
                case 'password':
                    $html .= '<input '.(!empty($placeholder) ? 'placeholder="'.$placeholder.'" ':'').'id="'.$field['name'].'" name="'.$field['name'].'" type="'.$field['type'].'" value="'.$field['value'].'" class="form-control'.(!empty($field['class']) ? ' '.$field['class']:'').'"'.(!empty($field['onchange']) ? ' onChange="'.$field['onchange'].'"' : '').(!empty($field['onkeyup']) ? ' onkeyUp="'.$field['onkeyup'].'"' : '').(!empty($field['onkeyup']) ? ' onkeyUp="'.$field['onkeyup'].'"' : '').'/>';
                break;

                case 'select':
                    $html .= '<select'.(!empty($field['multiple']) ? ' multiple="multiple"':'').(!empty($field['all_options']) ? ' data-actions-box="true"':'').' name="'.$field['name'].'" class="selectpicker form-control'.(!empty($field['class']) ? ' '.$field['class']:'').'"'.(!empty($field['onchange']) ? 'onChange="'.$field['onchange'].'"' : '').' data-live-search="true">';
                        foreach ($field['options'] as $option_value => $option_name) {
                            $html .= '<option value="'.$option_value.'"'.($option_value == $field['value'] ? ' selected="selected"': '').'>'.$option_name.'</option>';
                        }
                    $html .= '</select>';
                break;

                break;

                case 'legend':
                    $html .= '<legend'.(!empty($field['style']) ? ' style="'.$field['style'].'"': '').'>'.$field['text'].'</legend><div style="clear:both;"></div>';
                break;

                case 'html_code':
                    $html .= $field['html_code'];
                break;

                case 'date':
                    $html .= '<div class="input-group date">';
                        $html .= '<input type="text" name="'.$field['name'].'" value="'.$field['value'].'" data-date-format="YYYY-MM-DD" id="'.$field['name'].'" class="form-control date" />';
                        $html .= '<span class="input-group-btn">';
                            $html .= '<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>';
                        $html .= '</span>';
                    $html .= '</div>';
                break;

                case 'textarea':             
                    $html .= '<textarea '.(!empty($field['style']) ? 'style="'.$field['style'].'" ':'').' placeholder="'.$placeholder.'" class="form-control" id="'.$field['name'].'" name="'.$field['name'].'"/>'.$field['value'].'</textarea>';
                break;

                case 'html_editor':
                    $id = $field['name'];
                    if(version_compare(VERSION, '2.0.0.0', '>='))
                    {
                        $html .= '<textarea '.(!empty($field['style']) ? 'style="'.$field['style'].'" ':'').' placeholder="'.$placeholder.'" class="form-control" id="'.$field['name'].'" name="'.$field['name'].'"/>'.$field['value'].'</textarea>';
                        $html .= '<script type="text/javascript">';
                            $html .= '$(document).on(\'ready\', function(){';
                                $html .= '$(\'#'.$id.'\').summernote({height: '.(!empty($field['height']) ? $field['height'] : 300).'});';
                            $html .= '});';
                        $html .= '</script>';
                    }
                    else
                    {
                        $html .= '<textarea '.(!empty($field['style']) ? 'style="'.$field['style'].'" ':'').' placeholder="'.$placeholder.'" class="form-control" id="'.$field['name'].'" name="'.$field['name'].'"/>'.$field['value'].'</textarea>';

                        $html .= '<script type="text/javascript">';
                            $html .= '$(document).on(\'ready\', function(){';
                                $html .= 'CKEDITOR.replace(\''.$id.'\', {';
                                  $html .= 'filebrowserBrowseUrl: \'index.php?route=common/filemanager&token='.$this->session->data['token'].'\',';
                                  $html .= 'filebrowserImageBrowseUrl: \'index.php?route=common/filemanager&token='.$this->session->data['token'].'\',';
                                  $html .= 'filebrowserFlashBrowseUrl: \'index.php?route=common/filemanager&token='.$this->session->data['token'].'\',';
                                  $html .= 'filebrowserUploadUrl: \'index.php?route=common/filemanager&token='.$this->session->data['token'].'\',';
                                  $html .= 'filebrowserImageUploadUrl: \'index.php?route=common/filemanager&token='.$this->session->data['token'].'\',';
                                  $html .= 'filebrowserFlashUploadUrl: \'index.php?route=common/filemanager&token='.$this->session->data['token'].'\'';
                                $html .= '});';
                         
                            $html .= '});';
                          $html .= '</script>';
                    }
                break;

                case 'file':            
                    $html .= '<input class="form-control" id="'.$field['name'].'" placeholder="'.$placeholder.' name="'.$field['name'].'" type="file"/>';
                break;

                case 'hidden':
                    $html .= '<input id="'.$field['name'].'" name="'.$field['name'].'" type="hidden" value="'.$field['value'].'"/>';
                break;

                case 'colpick':
                    /*
                    Need Colpick library in view.tpl and call event in ready.
                    <link rel="stylesheet" type="text/css" href="view/stylesheet/colpick.css" />
                    <script type="text/javascript" src="view/javascript/colpick.js"></script>
                    */
                    $html .= '<input name="'.$field['name'].'" type="hidden" value="'.$field['value'].'">';
                    $html .= '<div id="'.$field['name'].'"></div>';

                    $html .= '<script type="text/javascript">';
                        $html .= '$(document).on(\'ready\', function(){';
                            $html .= '$(\'#'.$field["name"].'\').colpick({';
                                $html .= 'flat:true,';
                                $html .= 'layout:\'rgbhex\',';
                                $html .= 'submit:0,';
                                if ($field["value"] != "")
                                    $html .= 'color: \''.$field["value"].'\',';
                                $html .= 'onChange: function (hsb, hex, rgb) {';
                                    $html .= '$(\'input[name="'.$field["name"].'"]\').val(hex);';
                                $html .= '}';
                            $html .= '});';
                        $html .= '});';
                    $html .= '</script>';
                break;

                case 'image':
                    if(version_compare(VERSION, '2.0.0.0', '>='))
                    {
                        $this->load->model('tool/image');
                        $image = !empty($field['value']) ? $field['value'] : 'no_image.png';
                        $placeholder_empty = $this->model_tool_image->resize('no_image.png', 100, 100);
                        $placeholder = $this->model_tool_image->resize($image, 100, 100);

                        $html .= '<a id="thumb-'.$field["name"].'" href="" data-toggle="image" class="img-thumbnail"><img src="'.$placeholder.'" alt="" title="" data-placeholder="'.$placeholder_empty.'" /></a>';
                        $html .= '<input id="input-'.$field["name"].'" type="hidden" name="'.$field["name"].'" value="'.$field['value'].'" />';
                    }
                    else
                    {
                        $this->load->model('tool/image');
                        $image = !empty($field['value']) ? $field['value'] : 'no_image.jpg';
                        $placeholder_empty = $this->model_tool_image->resize('no_image.jpg', 100, 100);
                        $placeholder = $this->model_tool_image->resize($image, 100, 100);

                        $html .= '<div class="image">';
                            $html .= '<img src="'.$placeholder.'" alt="" id="thumb-'.$field['name'].'" />';
                            $html .= '<input type="hidden" name="'.$field['name'].'" value="'.$field['value'].'" id="image-'.$field['name'].'" /><br />';
                            $html .= '<a onclick="image_upload(\'image-'.$field['name'].'\', \'thumb-'.$field['name'].'\');">'.$this->language->get('text_browse').'</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
                            $html .= '<a onclick="$(\'#thumb-'.$field['name'].'\').attr(\'src\', \''.$placeholder_empty.'\'); $(\'#image-'.$field['name'].'\').attr(\'value\', \'\');">'.$this->language->get('text_clear').'</a>';
                        $html .= '</div>';
                    }
                break;

                case 'button':
                    $html .= '<a'.(!empty($field['data']) ? ' data-'.$field['data']['name'].'="'.$field['data']['value'].'"' : '').''.(!empty($field['onclick']) ? ' onclick="'.$field['onclick'].'"' : '').''.(!empty($field['href']) ?  ' href="'.$field['href'].'"' : '').' class="button'.(!empty($field['class']) ? ' '.$field['class']:'').'">'.$field['text'].'</a>';
                break;

                case 'table':
                    $html .= $this->field_generate_table($field['theads'], $field['data'], $field, !empty($field['add_button']) ? $field['add_button'] : false);
                break;

                case 'table_inputs':
                    $html .= $this->field_generate_table_inputs($field);
                break;

                case 'button_upload_images':
                    $html .= '<a onclick="image_upload();" class="button">'.$field['text'].'</a>';
                break;

                case 'products_autocomplete':
                    $html .= $this->field_generate_products_autocomplete($field);
                break;

                case 'module':
                    $html .= $this->field_generate_module_oc15x($field);
                break;

                default:
                # code...
                break;
            }
        //END Input

        if(!empty($field['after']))
            $html .= $field['after'];

        $html .= $input_container_end;

        return $html;
    }

    public function field_generate_table($theads, $data, $field)
    {
        $html = '';

        $html = '<table class="list table table-bordered table-hover '.(!empty($field['class']) ? $field['class'] : '').'"'.(!empty($field['style']) ? ' style="'.$field['style'].'"' : '').'>';
            if(!empty($theads))
            {
                $html .= '<thead>';
                    $html .= '<tr>';
                        foreach ($theads as $key => $th_name) {
                            $html .= '<td class="left">'.$th_name.'</td>';
                        }
                    $html .= '</tr>';
                $html .= '</thead>';
            }
            $html .= '<tbody>';
                foreach ($data as $key => $values) {
                    $html .= '<tr>';
                        foreach ($values as $key2 => $val_real) {
                            if(!is_array($val_real))
                                $html .= '<td class="text-left">'.$val_real.'</td>';
                            else
                                $html .= '<td class="text-left">'.$this->generateField($val_real).'</td>';
                        }
                    $html .= '</tr>';
                }
            $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }

    public function generateContent($form_view)
    {
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        $stores = $this->getStores();

        $html = '';
        if(version_compare(VERSION, '2.0.0.0', '>=')) $html = '<div class="tab-content">';

            $count = 0;
            foreach ($form_view['tabs'] as $tab_name => $tab) {
                $table = false;
                if(version_compare(VERSION, '2.0.0.0', '>='))
                {
                    $html .= '<div class="tab-pane'.($count == 0 ? ' active':'').'" id="tab-'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'">';
                }
                else
                {
                    $table = !isset($tab['no_table']) || !$tab['no_table'];

                    $html .= '<div id="tab-'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'"'.(!$table ? ' class="no_table"' : '').'>';
                    if(!$table) $html .= '<div style="position:relative; float:left; width:98%; padding: 10px;">';
                    if($table) $html .= '<table class="form">';
                        if($table) $html .= '<tbody>';
                }
                    if (!empty($tab['fields']))
                    {
                        foreach ($tab['fields'] as $key => $input)
                        {
                            $input['table'] = $table;

                            if (isset($input['multilanguage']))
                            {
                                foreach ($languages as $key => $lng)
                                {
                                    if(!empty($form_view['multi_store']) && !in_array($input['type'], array('legend', 'html_code')))
                                    {
                                        foreach ($stores as $key => $store)
                                        {
                                            $temp_input = $input;
                                            $temp_input['store'] = 'store_'.$store['store_id'];
                                            $temp_input['name'] .= '_'.$store['store_id'];
                                            $temp_input['value'] = $this->config->get($temp_input['name']);
                                            $base_name_input = $temp_input['name'];
                                            $base_value_input = $temp_input['value'];
                                            $repeat = !empty($temp_input['repeat']) ? $temp_input['repeat'] : 1;

                                            for ($i=0; $i < $repeat; $i++) {
                                                if($repeat > 1)
                                                {
                                                    $temp_input['name'] = $base_name_input . '['.$i.']';
                                                    $temp_input['value'] = $base_value_input[$i];
                                                }
                                                $html .= $this->generateField($temp_input, $lng);
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $repeat = !empty($input['repeat']) ? $input['repeat'] : 1;
                                        $base_name_input = $input['name'];
                                        $base_value_input = !empty($input['value']) ? $input['value'] : '';
                                        for ($i=0; $i < $repeat; $i++) {
                                            if($repeat > 1)
                                            {
                                                $input['name'] = $base_name_input . '['.$i.']';
                                                $input['value'] = !empty($base_value_input[$i]) ? $base_value_input[$i] : '';
                                            }
                                            $html .= $this->generateField($input, $lng);
                                        }
                                    }
                                }
                            }
                            else
                            {
                                if(!empty($form_view['multi_store']) && !in_array($input['type'], array('legend', 'html_code')))
                                {
                                    foreach ($stores as $key => $store)
                                    {
                                        $temp_input = $input;

                                        if(!isset($temp_input['name']))
                                            $temp_input['name'] = '';
                                        
                                        $temp_input['store'] = 'store_'.$store['store_id'];
                                        $temp_input['name'] .= '_'.$store['store_id'];
                                        $temp_input['value'] = $this->config->get($temp_input['name']);

                                        $repeat = !empty($temp_input['repeat']) ? $temp_input['repeat'] : 1;
                                        $base_name_input = $temp_input['name'];
                                        for ($i=0; $i < $repeat; $i++) {
                                            if($repeat > 1)
                                                $temp_input['name'] = $base_name_input . '['.$i.']';
                                            $html .= $this->generateField($temp_input);
                                        }
                                    }
                                }
                                else
                                {
                                    $repeat = !empty($input['repeat']) ? $input['repeat'] : 1;
                                    $base_name_input = !empty($input['name']) ? $input['name'] : '';
                                    for ($i=0; $i < $repeat; $i++) {
                                        if($repeat > 1)
                                            $input['name'] = $base_name_input . '['.$i.']';
                                        $html .= $this->generateField($input);
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        $html .= $tab['custom_content'];
                    }
                if(version_compare(VERSION, '1.5.6.4', '<='))
                {
                    if($table) $html .= '</tbody>';
                    if($table) $html .= '</table>';
                    if(!$table) $html .= '</div><div style="clear:both;"></div>';
                    $html .= '</div>';
                }
                else
                    $html .= '</div>';
                $count++;
            }

        if(version_compare(VERSION, '2.0.0.0', '>=')) $html .= '</div>';

        return $html;
    }

    public function field_generate_table_inputs($table_input)
    {
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();

        $html = '';

        $add_button = isset($table_input['add_button']) ? $table_input['add_button'] : true;
        $delete_button = isset($table_input['delete_button']) ? $table_input['delete_button'] : true;
        $theads = $table_input['theads'];
        $count_theads = 0;
        $model_row = $table_input['model_row'];

        $data = array();

        if(!empty($table_input['value']))
        {
            $datas = !empty($table_input['value']) && !is_array($table_input['value']) ? unserialize(base64_decode($table_input['value'])) : $table_input['value'];
        }

        $num_rows = !empty($datas) ? count($datas)+1 : 0;
        $html = '<table data-rows="'.$num_rows.'" class="list table table-bordered table-hover'.(!empty($table_input['store']) ? ' store_input '.$table_input['store']:'').' '.(!empty($table_input['class']) ? $table_input['class'] : '').'"'.(!empty($table_input['style']) ? ' style="'.$table_input['style'].'"' : '').'>';

            $html .= '<thead>';
                $html .= '<tr>';
                    foreach ($theads as $key => $th_name) {
                        $html .= '<td class="left"'.(empty($th_name) ? ' style="display:none;"':'').'>'.$th_name.'</td>';
                        if(!empty($th_name)) $count_theads++;
                    }
                    if($add_button || $delete_button)
                        $html .= '<td></td>';
                $html .= '</tr>';
            $html .= '</thead>';
        
            $html .= '<tbody>';
                if(!empty($datas))
                {
                    //Openquart Quality Extensions - info@opencartqualityextensions.com - 2016-09-28 21:27:53 - Insert possibles lost values
                        //Get indexes array of model row
                            $indexes_model_row = array();
                            foreach ($model_row as $key => $value) {
                               $indexes_model_row[] = $value['name'];
                            }
                        //END
                        foreach ($datas as $key => $dats) {
                            //Get indexes array
                                $indexes_model_dat = array();
                                foreach ($dats as $key3 => $dat) {
                                    $indexes_model_dat[] = $key3;
                                }
                            //END
                            
                            if($indexes_model_row != $indexes_model_dat)
                            {
                                $temp_dat = array();
                                $count = 0;

                                foreach ($indexes_model_row as $key4 => $index) {
                                    if(!isset($dats[$index]))
                                        $temp_dat[$index] = '';
                                    else
                                        $temp_dat[$index] = $dats[$index];
                                    # code...
                                }
                            
                                $datas[$key] = $temp_dat;
                            }
                            # code...
                        }
                    //END

                    foreach ($datas as $key => $datas_inputs) {
                        $html .= '<tr>';
                            foreach ($datas_inputs as $input_name => $value) {
                                $last_index = 0;
                                //Search input in model row
                                    $input_final = array();
                                    foreach ($model_row as $key2 => $field) {
                                        if($field['name'] == $input_name)
                                        {
                                            $input_final = $field;
                                            $input_final['name'] = $table_input['name'].'['.$key.']['.$input_name.']';
                                            $input_final['value'] = $value;
                                            $input_final['only_input'] = true;

                                            $repeat = !empty($input_final['repeat']) ? $input_final['repeat'] : 1;
                                            $base_name_input = $input_final['name'];
                                            $base_value_input = $input_final['value'];

                                            $html .= '<td'.(!empty($field['td_hidden']) ? ' style="display:none;"':'').'>';
                                                for ($i=0; $i < $repeat; $i++) {
                                                    if($repeat > 1)
                                                    {
                                                        $input_final['name'] = $base_name_input . '['.$i.']';
                                                        $input_final['value'] = $base_value_input[$i];
                                                    }

                                                    if(!empty($input_final['multilanguage']))
                                                    {
                                                        foreach ($input_final['value'] as $language_code => $value) {
                                                            $language = $languages[$language_code];

                                                            if(version_compare(VERSION, '2.2.0.0', '>='))
                                                                $flag_route = 'language/'.$language['code'].'/'.$language['code'].'.png';
                                                            else
                                                                $flag_route = 'view/image/flags/'.$language['image'];

                                                            $temp_input = $input_final;
                                                            $temp_input['value'] = $temp_input['value'][$language_code];
                                                            $temp_input['name'] = $temp_input['name'].'['.$language_code.']';
                                                            $html .= '<img src="'.$flag_route.'">&nbsp;&nbsp;';

                                                            $html .= $this->generateField($temp_input);

                                                            $html .= '<div style="clear:both;"></div>'; 
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $html .= $this->generateField($input_final);
                                                    }
                                                }
                                            $html .= '</td>';

                                            $last_index = $key2;
                                        }
                                    }
                                    if(empty($input_final))
                                    {
                                        echo $last_index; die;
                                    }
                                //END Search input in model row
                            }
                            if($add_button)
                            {
                                $html .= '<td>';
                                    if($delete_button)
                                        $html .= '<a class="btn btn-danger" onclick="$(this).closest(\'tr\').remove();"><i class="fa fa-minus-circle" style="margin-right:0px;"></i></a>';
                                $html .= '</td>';
                            }  
                        $html .= '</tr>';
                    }
                }

                $html .= '<tr class="model_row" style="display:none;">';
                    foreach ($model_row as $key => $field) {
                        $field['only_input'] = true;
                        $field['name'] = $table_input['name'].'[replace_by_number]['.$field['name'].']';
                        $html .= '<td'.(!empty($field['td_hidden']) ? ' style="display:none;"':'').'>';
                            $repeat = !empty($field['repeat']) ? $field['repeat'] : 1;
                            $base_name_input = $field['name'];
                            for ($i=0; $i < $repeat; $i++) {
                                if($repeat > 1)
                                    $field['name'] = $base_name_input . '['.$i.']';
                                if (!empty($field['multilanguage']))
                                {
                                    foreach ($languages as $key_lng => $lng) {
                                        if(version_compare(VERSION, '2.2.0.0', '>='))
                                            $flag_route = 'language/'.$lng['code'].'/'.$lng['code'].'.png';
                                        else
                                            $flag_route = 'view/image/flags/'.$lng['image']; 

                                        $html .= '<img src="'.$flag_route.'">&nbsp;&nbsp;';
                                        $html .= $this->generateField($field, $lng);
                                        $html .= '<div style="clear:both;"></div>';
                                    }
                                }
                                else
                                {
                                    $html .= $this->generateField($field);
                                }
                            }
                        $html .= '</td>';
                    }
                    if($delete_button)
                        $html .= '<td><a class="btn btn-danger" onclick="$(this).closest(\'tr\').remove();"><i class="fa fa-minus-circle" style="margin-right:0px;"></i></a></td>';
                $html .= '</tr>';

            $html .= '</tbody>';

            if($add_button)
            {
                $html .= '<tfoot>';
                    $html .= '<tr><td colspan="'.($count_theads).'"></td><td><a class="btn btn-primary" onclick="add_row_table_input($(this));events_after_add_new_row_table_inputs($(this));'.(!empty($table_input['add_button_extra_action']) ? $table_input['add_button_extra_action'] : '').'"><i class="fa fa-plus-circle" style="margin-right:0px;"></i></a></td></tr>';
                $html .= '</tfoot>';
            }
        $html .= '</table>';

        return $html;
    }

    public function field_generate_products_autocomplete($field)
    {
        $only_input = !empty($field['only_input']);

        $html = "";
        $this->load->model('catalog/product');

        if (!empty($field['value']))
            $products_temp = $field['value'];
        else
            $products_temp = $this->config->get($field['name']);

        $products = array();

        if (!empty($products_temp))
        {
            foreach ($products_temp as $product_id)
            {
                $product_info = $this->model_catalog_product->getProduct($product_id);

                if ($product_info)
                {
                    $products[] = array(
                        'product_id' => $product_info['product_id'],
                        'name'       => $product_info['name']
                    );
                }
            }
        }

        $html .= '<input name="'.$field['name'].'" '.(!empty($field['label']) ? 'placeholder="'.$field['label'].'" ':'').'class="form-control products_autocomplete">';

        if(version_compare(VERSION, '2.0.0.0', '>='))
        {
            $html .= '<div id="'.$field['name'].'" class="well well-sm" style="height: 150px; overflow: auto;">';
                if(!empty($products))
                {
                    foreach ($products as $product)
                    {
                        $html .= '<div id="element-'.$product['product_id'].'"><i class="fa fa-minus-circle"></i> ';
                            $html .= $product['name'];
                            $html .= '<input type="hidden" name="'.$field['name'].'[]" value="'.$product['product_id'].'" />';
                        $html .= '</div>';
                    }
                }
            $html .= '</div>';
        }
        else
        {
            if(!$only_input) $html .= '</td></tr>';
            if(!$only_input) $html .= '<tr>';
                if(!$only_input) $html .= '<td></td>';
                if(!$only_input) $html .= '<td>';
                    $html .= '<div id="'.$field['name'].'" class="scrollbox">';
                        $class = 'odd';
                        foreach ($products as $product) {
                            $class = ($class == 'even' ? 'odd' : 'even');
                            $html .= '<div id="element-'.$product['product_id'].'" class="'.$class.'">';
                                $html .= $product['name'].'<img class="delete_item_autocomplete" src="view/image/delete.png" alt="" />';
                                $html .= '<input type="hidden" name="'.$field['name'].'[]" value="'.$product['product_id'].'" />';
                            $html .= '</div>';
                        }
                    $html .= '</div>';
                if(!$only_input) $html .= '</td>';
            if(!$only_input) $html .= '</tr>';
        }

        return $html;
    }

    //1.5.X FUNCTIONS
        public function field_generate_module_oc15x($field)
        {
            $this->load->model('localisation/language');
            $languages = $this->model_localisation_language->getLanguages();

            $html = '<table id="'.$field['name'].'-module" class="list">';
                $html .= '<thead>';
                    $html .= '<tr>';
                        foreach ($field['theads'] as $key => $value) {
                            $html .= '<td class="left">'.$value.'</td>';
                        }
                        $html .= '<td></td>';
                    $html .= '</tr>';
                $html .= '</thead>';


                //Get config modules
                $modules = $this->config->get($field['name']);

                $count_modules = 0;

                if(!empty($modules))
                {          
                    foreach ($modules as $mod_key => $mod) {            
                        $html .= '<tbody id="'.$field['name'].'-module-row-'.$mod_key.'">';
                            $html .= '<tr>';
                                //Set all values to empty & set names
                                $temp_field = $field;
                                foreach ($temp_field['inputs'] as $key => $fi) {   
                                    if (empty($fi['multilanguage']))
                                    {                                   
                                        $temp_field['inputs'][$key]['value'] = !empty($mod[$fi['name']])? $mod[$fi['name']]:'';
                                        $temp_field['inputs'][$key]['name'] = $temp_field['name'].'['.$mod_key.']['.$fi['name'].']';
                                    }
                                }                

                                foreach ($temp_field['inputs'] as $key2 => $fie) {
                                    if (!empty($fie['multilanguage']))
                                    {                                                
                                        $html .= '<td class="left">';
                                            foreach ($languages as $key_lng => $lng) {
                                                $temp_fi = $fie;
                                                $temp_fi['value'] = $mod[$temp_fi['name'].'_'.$lng['code']]; 
                                                $temp_fi['name'] = $temp_field['name'].'['.$mod_key.']['.$temp_fi['name'].'_'.$lng['code'].']';
                                                $temp_fi['only_input'] = true;
                                                $html .= '<img src="view/image/flags/'.$lng['image'].'">&nbsp;&nbsp;'.$this->generateField($temp_fi).'<div style="clear:both;"></div>';
                                            }
                                        $html .= '</td>';
                                    }
                                    else
                                    {
                                        $fie['only_input'] = true;
                                        $html .= '<td class="left">'.$this->generateField($fie).'</td>';
                                    }
                                }

                                $html .= '<td class="left"><a class="button" onclick="$(\'#'.$field['name'].'-module-row-'.$mod_key.'\').remove();">'.$this->language->get('text_remove').'</a></td>';
                                $html .= '</tr>';
                        $html .= '</tbody>';
                        $count_modules++;
                    }
                }
                else
                {
                    //Set all values to empty & set names
                    $temp_field = $field;
                    foreach ($temp_field['inputs'] as $key => $fi) {
                        $temp_field['inputs'][$key]['value'] = "";
                        if (empty($fi['multilanguage']))
                            $temp_field['inputs'][$key]['name'] = $temp_field['name'].'[0]['.$fi['name'].']';
                    }

                    //Insert empty row
                    $html .= '<tbody id="'.$temp_field['name'].'-module-row-0">';
                        $html .= '<tr>';              
                            foreach ($temp_field['inputs'] as $key2 => $fi) {
                                if (!empty($fi['multilanguage']))
                                {
                                    $html .= '<td class="left">';
                                        foreach ($languages as $key => $lng) {
                                            $temp_fi = $fi;
                                            $temp_fi['name'] = $temp_field['name'].'[0]['.$fi['name'].'_'.$lng['code'].']';
                                            $temp_fi['only_input'] = true;     
                                            $html .= '<img src="view/image/flags/'.$lng['image'].'">&nbsp;&nbsp;'.$this->generateField($temp_fi).'<div style="clear:both;"></div>';
                                        }
                                        $fi['only_input'] = true;
                                        $this->generateField($fi);
                                    $html .= '</td>';

                                }
                                else
                                {
                                    $fi['only_input'] = true;
                                    $html .= '<td class="left">'.$this->generateField($fi).'</td>';
                                }
                            }
                            $html .= '<td></td>';
                        $html .= '</tr>';
                    $html .= '</tbody>';

                    $count_modules++;
                }

                $html .= '<tfoot>';
                    $html .= '<tr>';
                        $html .= '<td colspan="'.count($field['inputs']).'"></td>';
                        $html .= '<td class="left"><a class="button" onclick="addModule_'.$field['name'].'();">'.$this->language->get('text_add_module').'</a></td>';
                    $html .= '</tr>';
                $html .= '</tfoot>';
            $html .= '</table>';

            $html .= $this->field_generate_script_add_module_oc15x($field, $count_modules);

            return $html;
        }

        public function field_generate_script_add_module_oc15x($field, $count_modules)
        {
            $this->load->model('localisation/language');
            $languages = $this->model_localisation_language->getLanguages();

            $script = '<script type="text/javascript">';
                $script .= 'var module_row = '.$count_modules.';';

                $script .= 'function addModule_'.$field['name'].'(){';
                    $script .= "html  = '<tbody id=\"".$field['name']."-module-row-' + module_row + '\">';";
                        $script .= "html  += '<tr>';";
                            //Set all values to empty & set names
                            foreach ($field['inputs'] as $key => $fi) {
                                $field['inputs'][$key]['value'] = "";

                                if (empty($fi['multilanguage']))
                                    $field['inputs'][$key]['name'] = $field['name'].'[\'+module_row+\']['.$fi['name'].']';
                            }

                            foreach ($field['inputs'] as $key2 => $fi) {
                                if (isset($fi['multilanguage']))
                                {                           
                                    $script .= "html  += '<td class=\"left\">";                  
                                        foreach ($languages as $key => $lng) {
                                            $temp_field = $fi;     
                                            $temp_field['name'] = $field['name'].'[\'+module_row+\']['.$fi['name'].'_'.$lng['code'].']'; 
                                            $temp_field['only_input'] = true;                
                                            $script .= '<img src="view/image/flags/'.$lng['image'].'">&nbsp;&nbsp;'.$this->generateField($temp_field).'<div style="clear:both;"></div>';
                                        }
                                    $script .= "</td>';";
                                }
                                else
                                {
                                    $fi['only_input'] = true;
                                    $script .= "html  += '<td class=\"left\">".$this->generateField($fi)."</td>';";
                                }
                            }
                            $script .= "html  += '<td class=\"left\"><a onclick=\"$(\'#".$field['name']."-module-row-' + module_row + '\').remove();\" class=\"button\">".$this->language->get('text_remove')."</a></td>';";
                        $script .= "html  += '</tr>';";
                    $script .= "html  += '</tbody>';";
                    $script .= "$('#".$field['name']."-module tfoot').before(html);";
                    $script .= "module_row++;";
                $script .= '}';
            $script .= '</script>';

            return $script;
        }
    //END 1.5.X FUNCTIONS

    //Anothers functions
        public function getStores()
        {
            $this->load->model('setting/store');
            $stores = array();
            $stores[0] = array(
                'store_id' => '0',
                'name' => $this->config->get('config_name')
            );

            $stores_temp = $this->model_setting_store->getStores();
            foreach ($stores_temp as $key => $value) {
                $stores[] = $value;
            }
            return $stores;
        }
        public function formatName($name)
        {
            $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
            return strtr( $name, $unwanted_array );
        }
        public function aasort ($array, $key) {
            $sorter=array();
            $ret=array();
            reset($array);
            foreach ($array as $ii => $va) {
                $sorter[$ii]=$va[$key];
            }
            asort($sorter);
            foreach ($sorter as $ii => $va) {
                $ret[$ii]=$array[$ii];
            }
            return $ret;
        }
    //END Anothers functions
}
?>