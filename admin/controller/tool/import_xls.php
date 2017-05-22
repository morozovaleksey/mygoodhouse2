<?php 
class ControllerToolImportXls extends Controller { 
    private $error = array();

    private $data_to_view = array(
        'button_apply_allowed' => false,
        'button_save_allowed' => false,
    );

    private $extension_type = 'tool';
    private $extension_url_cancel_oc_15x = 'common/home';
    private $extension_url_cancel_oc_2x = 'common/dashboard';

    public function __construct($registry) {
        parent::__construct($registry);
        $this->extension_name = 'import_xls';
        $this->extension_group_config = 'import_xls';
        $this->extension_id = '542068d4-ed24-47e4-8165-0994fa641b0a';

        $this->attributeNumber = 6;
        $this->filterGroupNumber = 3;
        $this->filterGroupFilterNumber = 3;
        $this->categoryNumber = 5;
        $this->allow_options = true;

        $this->letters_columns = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
            'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
            'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
            'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ',
            'EA','EB','EC','ED','EE','EF','EG','EH','EI','EJ','EK','EL','EM','EN','EO','EP','EQ','ER','ES','ET','EU','EV','EW','EX','EY','EZ',
            'FA','FB','FC','FD','FE','FF','FG','FH','FI','FJ','FK','FL','FM','FN','FO','FP','FQ','FR','FS','FT','FU','FV','FW','FX','FY','FZ',
            'GA','GB','GC','GD','GE','GF','GG','GH','GI','GJ','GK','GL','GM','GN','GO','GP','GQ','GR','GS','GT','GU','GV','GW','GX','GY','GZ',
            'HA','HB','HC','HD','HE','HF','HG','HH','HI','HJ','HK','HL','HM','HN','HO','HP','HQ','HR','HS','HT','HU','HV','HW','HX','HY','HZ',
            'IA','IB','IC','ID','IE','IF','IG','IH','II','IJ','IK','IL','IM','IN','IO','IP','IQ','IR','IS','IT','IU','IV','IW','IX','IY','IZ',
            'JA','JB','JC','JD','JE','JF','JG','JH','JI','JJ','JK','JL','JM','JN','JO','JP','JQ','JR','JS','JT','JU','JV','JW','JX','JY','JZ'
        );

        $fields_multilanguage = array('name', 'description', 'meta_description', 'meta_title', 'meta_keyword', 'tag', 'option', 'option_value');
        for ($i=1; $i <= $this->attributeNumber; $i++) { 
            array_push($fields_multilanguage, 'attribute_group_'.$i);
            array_push($fields_multilanguage, 'attribute_attribute_'.$i);
            array_push($fields_multilanguage, 'attribute_value_'.$i);
        }
        $this->fields_multilanguage = $fields_multilanguage;


        $fields_multilanguage_names = array('*Name', 'Description', 'Meta description', 'Meta title', 'Meta keywords', 'Tags', 'Option', 'Option value');
        for ($i=1; $i <= $this->attributeNumber; $i++) { 
            array_push($fields_multilanguage_names, 'Attr. Group '.$i);
            array_push($fields_multilanguage_names, 'Attribute '.$i);
            array_push($fields_multilanguage_names, 'Attribute value '.$i);
        }
        $this->fields_multilanguage_names = $fields_multilanguage_names;
        $loader = new Loader($registry);

        //Get all customer groups
            if(version_compare(VERSION, '2.0.3.1', '<='))
            {
                $loader->model('sale/customer_group');
                $this->customer_groups = $this->model_sale_customer_group->getCustomerGroups();
            }
            else
            {
                $loader->model('customer/customer_group');
                $this->customer_groups = $this->model_customer_customer_group->getCustomerGroups();
            }
        //END Get all customer groups

        //Count languages active
            $loader->model('localisation/language');
            $this->languages = $this->model_localisation_language->getLanguages();

            $this->count_languages = 0;

            foreach ($this->languages as $key => $lang) {
                if($lang['status'])
                    $this->count_languages++;
                if($lang['language_id'] == $this->config->get('config_language_id'))
                    $this->default_language_code = $lang['code'];
            }
        //END Count languages active

        $this->filename = 'import_export_xls_product_tools';
    }

    public function index() {
        $this->remove_vqmod_files();

        //Load languages
            $this->load->language($this->extension_type.'/'.$this->extension_name);

        //Set document title
            $this->document->setTitle($this->language->get('heading_title'));

        //Add scripts and css
            if(version_compare(VERSION, '2.0.0.0', '<'))
            {
                $this->document->addScript('view/javascript/opqualityextensions/bootstrap.min.js');
                $this->document->addStyle('view/stylesheet/opqualityextensions/bootstrap.min.css');
            }
            
            $this->document->addStyle('view/stylesheet/opqualityextensions/colpick.css');
            $this->document->addStyle('view/stylesheet/opqualityextensions/bootstrap-select.min.css');
            $this->document->addScript('view/javascript/opqualityextensions/colpick.js');
            $this->document->addScript('view/javascript/opqualityextensions/bootstrap-select.min.js');
            $this->document->addScript('view/javascript/opqualityextensions/tools.js');

            if(version_compare(VERSION, '2.0.0.0', '>='))
            {
                $this->document->addScript('view/javascript/opqualityextensions/oc2x.js');
                $this->document->addStyle('view/stylesheet/opqualityextensions/oc2x.css');
            }
            else
            {
                $this->document->addScript('view/javascript/opqualityextensions/oc15x.js');
                $this->document->addStyle('view/stylesheet/opqualityextensions/oc15x.css');
                $this->document->addScript('view/javascript/ckeditor/ckeditor.js');
                $this->document->addStyle('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
            }
        //END Add scripts and css

        //Add custom js
            if(file_exists('view/javascript/ocqe_'.$this->extension_name.'.js'))
                $this->document->addScript('view/javascript/ocqe_'.$this->extension_name.'.js');

            if(version_compare(VERSION, '2.0.0.0', '>=') && file_exists('view/javascript/ocqe_'.$this->extension_name.'_oc2x.js'))
                $this->document->addScript('view/javascript/ocqe_'.$this->extension_name.'_oc2x.js');
            elseif(file_exists('view/javascript/ocqe_'.$this->extension_name.'_oc15x.js'))
                $this->document->addScript('view/javascript/ocqe_'.$this->extension_name.'_oc15x.js');

        //Add custom css
            if(file_exists('view/stylesheet/ocqe_'.$this->extension_name.'.css'))
                $this->document->addStyle('view/stylesheet/ocqe_'.$this->extension_name.'.css');

            if(version_compare(VERSION, '2.0.0.0', '>=') && file_exists('view/stylesheet/ocqe_'.$this->extension_name.'_oc2x.css'))
                $this->document->addStyle('view/stylesheet/ocqe_'.$this->extension_name.'_oc2x.css');
            elseif(file_exists('view/stylesheet/ocqe_'.$this->extension_name.'_oc15x.css'))
                $this->document->addStyle('view/stylesheet/ocqe_'.$this->extension_name.'_oc15x.css');
            
        //Pressed save button
            if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
                $this->session->data['error'] = '';

                $file = $this->request->files['upload']['tmp_name'];

                $extension_file = pathinfo($this->request->files['upload']['name'], PATHINFO_EXTENSION);
                if(empty($file) || !in_array($extension_file, array('xls', 'xlsx')))
                {
                    $this->session->data['error'] .= $this->language->get('error_upload_file');
                    $this->response->redirect($this->url->link('tool/import_xls', 'token=' . $this->session->data['token'], 'SSL'));
                }

                $xls_data = $this->read_xls_full($file);
                $pattern = $this->get_xls_pattern($xls_data);

                //Validate columns names
                    $errors_checked = $this->validate_columns_names($xls_data);
                    if(!empty($errors_checked))
                    {
                        $this->session->data['error'] .= '<ul>'.$errors_checked.'</ul>';
                        $this->response->redirect($this->url->link('tool/import_xls', 'token=' . $this->session->data['token'], 'SSL'));
                    }
                //END Validate columns names

                //Validate XLS data
                    $errors_checked = $this->validate_columns_data($xls_data, $pattern);

                    if(!empty($errors_checked))
                        $this->session->data['error'] .= '<ul>'.$errors_checked.'</ul>';
                //END Validate XLS data

                if(empty($this->session->data['error']))
                {
                    $this->load->model('tool/import_xls');
                    $this->db->query("START TRANSACTION");

                    $error_while_import = $this->model_tool_import_xls->start_import($xls_data, $pattern);

                    if(!empty($error_while_import))
                        $this->session->data['error'] .= '<ul>'.$error_while_import.'</ul>';
                    else
                        $this->session->data['success'] = $this->language->get('import_success');
                }

                //OC Versions compatibility
                if(version_compare(VERSION, '2.0.0.0', '>='))
                    $this->response->redirect($this->url->link($this->extension_type.'/'.$this->extension_name, 'token=' . $this->session->data['token'], 'SSL'));
                else
                    $this->redirect($this->url->link($this->extension_type.'/'.$this->extension_name, 'token=' . $this->session->data['token'], 'SSL'));
            }
        //END Pressed save button

        //Send token to view
            $this->data_to_view['token'] = $this->session->data['token'];

        //Actions
            $this->data_to_view['action'] = $this->url->link($this->extension_type.'/'.$this->extension_name, 'token=' . $this->session->data['token'], 'SSL');

            $this->data_to_view['cancel'] = $this->url->link(version_compare(VERSION, '2.0.0.0', '>=') ? $this->extension_url_cancel_oc_2x : $this->extension_url_cancel_oc_15x, 'token=' . $this->session->data['token'], 'SSL');  

        //Set layouts 
            $this->load->model('design/layout');
            $layouts_temp = $this->model_design_layout->getLayouts();
            $layouts = array();
            foreach ($layouts_temp as $key => $layout) {
                $layouts[$layout['layout_id']] = $layout['name'];
            }
        //END Set layouts

        //Set statuses
            $statuses = array(
                1 => $this->language->get('active'),
                0 => $this->language->get('disabled')
            );
        //END Set statuses

        //Set positions
            $positions = array(
                'content_top' => $this->language->get('text_content_top'),
                'content_bottom' => $this->language->get('text_content_bottom'),
                'column_left' => $this->language->get('text_column_left'),
                'column_right' => $this->language->get('text_column_right'),
            );
        //END Set positions

        //Load extension languages
            $lang_array = array(
                'heading_title',
                'button_save',
                'button_cancel',
                'apply_changes',
                'text_image_manager',
                'text_browse',
                'text_clear',
                'image_upload_description'
            );

            foreach ($lang_array as $key => $value) {
                $this->data_to_view[$value] = $this->language->get($value);
            }
        //END Load extension languages

        //Construct view template form
            $form_view = $this->_contruct_view_form();
            $this->data_to_view['form_view'] = $form_view;

            //Load opencartqualityextensions/tools.php model
                $this->load->model('opencartqualityextensions/tools');

            //OC Versions compatibility
                $this->data_to_view['form'] = $this->model_opencartqualityextensions_tools->generateForm($form_view);
        //END Construct view template form

        $this->data_to_view['breadcrumbs'] = array();
        $this->data_to_view['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        if(!in_array($this->extension_type, array('tool')))
        {
            $this->data_to_view['breadcrumbs'][] = array(
                'text'      => $this->language->get('text_'.$this->extension_type),
                'href'      => $this->url->link($this->extension_url_cancel, 'token=' . $this->session->data['token'], 'SSL'),
                'separator' => ' :: '
            );
        }

        $this->data_to_view['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link($this->extension_type.'/'.$this->extension_name, 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        //OC Versions compatibility

            if(version_compare(VERSION, '2.0.0.0', '>='))
            {

                $data = $this->data_to_view;
                $data['header'] = $this->load->controller('common/header');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['footer'] = $this->load->controller('common/footer');

                $this->response->setOutput($this->load->view($this->extension_type.'/'.$this->extension_name.'_oc2x.tpl', $data));
            }
            else
            {

                $this->data = $this->data_to_view;
                $this->template = $this->extension_type.'/'.$this->extension_name.'_oc15x.tpl';
                $this->children = array(
                    'common/header',
                    'common/footer'
                );

                $this->response->setOutput($this->render());
            }
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', $this->extension_type.'/'.$this->extension_name)) {
            if(!empty($this->request->post['no_exit']))
            {
                $array_return = array(
                    'error' => true,
                    'message' => $this->language->get('error_permission')
                );
                echo json_encode($array_return); die;
            }
            else
                $this->session->data['error'] = $this->language->get('error_permission');
            return false;
        }
        return true;
    }

    public function convert_to_innodb()
    {
        $this->load->language('tool/import_xls');
        $array_return = array('error' => false, 'message' => $this->language->get('success_inno_db'));

        $rs = $this->db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = '".DB_DATABASE."' 
        AND ENGINE = 'MyISAM'");

        foreach ($rs->rows as $key => $table) {
            if(!$this->db->query("ALTER TABLE `".$table['TABLE_NAME']."` ENGINE=INNODB"))
            {
                $array_return['error'] = true;
                $array_return['message'] = $this->language->get('error_inno_db');
            }
        }

        if(!$array_return['error'])
        {
            $temp = array(
                'import_xls_innodb_converted' => true
            );

            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('import_xls', $temp);
        }

        echo json_encode($array_return); die;
    }

    public function save_configuration()
    {
        $this->load->language('tool/import_xls');
        $inputs_to_save = array('import_xls_categories_last_tree');

        $post_data = $this->request->post;
        foreach ($post_data as $key => $value) {
            if(!in_array($key, $inputs_to_save))
                unset($post_data[$key]);
        }
        $post_data['import_xls_innodb_converted'] = $this->config->get('import_xls_innodb_converted');
        
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('import_xls', $post_data);

        $array_return = array('error' => false, 'message' => $this->language->get('save_configuration_success'));
        echo json_encode($array_return); die;
    }

    public function read_xls_full($file)
    {
        require_once DIR_SYSTEM . 'PHPExcel/Classes/PHPExcel.php';
        require_once DIR_SYSTEM . 'PHPExcel/Classes/PHPExcel/IOFactory.php';

        $inputFileType = PHPExcel_IOFactory::identify($file);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($file);

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $final_excel = array(
            'columns' => array(),
            'data' => array(),
        );

        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            if ($row == 1)
            {
                foreach($rowData[0] as $k=>$v)
                    if(!empty($v))
                        $final_excel['columns'][] = $v;
            }
            else
            {
                $final_excel['data'][$row] = array();
                $empty = true;
                foreach($rowData[0] as $k=>$v)
                {
                    if ($v == "#VALUE!" || empty($v) || strlen(trim($v)) == 0)
                    {
                        $v = ($v == '0') ? '0' : '';
                    }
                    else
                        $empty = false;

                    $final_excel['data'][$row][] = htmlspecialchars($v);
                }
                if($empty)
                    unset($final_excel['data'][$row]);
            }
        }

        return $final_excel;
    }

    public function validate_columns_names($xls_data)
    {
        $error = false;

        $hopped_columns = $this->get_hopped_columns();
        $xls_columns = $xls_data['columns'];
        
        foreach ($xls_columns as $key => $ec) {
            $found = false;
            foreach ($hopped_columns as $key2 => $hc) {
                if($hc == $ec)
                    $found = true;
            }
            if(!$found)
            {
                $error .=  '<li>'.sprintf($this->language->get('error_column_name'), $ec).'</li>';
            }
        }

        if($this->count_languages == 1 && !in_array('*Name', $xls_columns))
            $error .=  '<li>'.$this->language->get('error_column_name_required').'</li>';

        if(!in_array('*Model', $xls_columns))
            $error .=  '<li>'.$this->language->get('error_column_model_required').'</li>';

        return $error;
    }

    public function validate_columns_data($xls_data, $pattern)
    {
        $error = false;

        $column_name = '*Name'.($this->count_languages > 1 ? ' '.$this->default_language_code : '');

        //Get all models
        $models = $this->db->query('SELECT model FROM '.DB_PREFIX.'product');
        
        $model_products = array();

        foreach ($xls_data['data'] as $key => $data) {
            $model = $data[$pattern['*Model']['index']];
            if(!isset($model_products[$model]))
                $model_products[$model] = $data[$pattern[$column_name]['index']];          
        }

        foreach ($xls_data['data'] as $row => $data) {
            
            $model = $data[$pattern['*Model']['index']];

            //Model empty
            if(empty($model))
                $error .= '<li>'.sprintf($this->language->get('error_data_empty_model'), $row).'</li>';

            //Name empty
                if($this->count_languages == 1 && empty($data[$pattern[$column_name]['index']]))
                    $error .= '<li>'.sprintf($this->language->get('error_data_empty_name'), $row).'</li>';
                elseif($this->count_languages > 1)
                {
                    foreach ($this->languages as $key => $lang) {
                        if(empty($data[$pattern['*Name '.$lang['code']]['index']]))
                          $error .= '<li>'.sprintf($this->language->get('error_data_empty_name'), $row).'</li>';  
                    }
                }
            //END Name empty

            //Check model repeat
                if(!isset($model_products[$model]))
                    $model_products[$model] = $data[$pattern[$column_name]['index']];

                if($model_products[$model] != $data[$pattern[$column_name]['index']])
                    $error .= '<li>'.sprintf($this->language->get('error_data_model_repeat'), $row).'</li>';
            //END Check model repeat

            //Option, Option type or option value empty
                if($this->count_languages == 1)
                {
                    if(!empty($pattern['Option']['index']) && !empty($pattern['Option value']['index']))
                    {
                        if(!empty($data[$pattern['Option']['index']]) || !empty($data[$pattern['Option type']['index']]) || !empty($data[$pattern['Option value']['index']]))
                        {
                            if (empty($data[$pattern['Option value']['index']]))
                                $error .= '<li>'.sprintf($this->language->get('error_data_empty_option_value'), $row).'</li>';

                            if (empty($data[$pattern['Option']['index']]))
                                $error .= '<li>'.sprintf($this->language->get('error_data_empty_option'), $row).'</li>';
                        }
                    }
                }
                else
                {
                    $all_options_empty = true;
                    $all_option_values_empty = true;

                    foreach ($this->languages as $key => $lang) {
                        if (!empty($data[$pattern['Option '.$lang['code']]['index']]))
                            $all_options_empty = false;

                        if (!empty($data[$pattern['Option value '.$lang['code']]['index']]))
                            $all_option_values_empty = false;
                    }

                    if(!$all_options_empty || !$all_option_values_empty)
                    {
                        foreach ($this->languages as $key => $lang) {
                            if(!empty($data[$pattern['Option '.$lang['code']]['index']]) || !empty($data[$pattern['Option type']['index']]) || !empty($data[$pattern['Option value '.$lang['code']]['index']]))
                            {
                                if (empty($data[$pattern['Option value '.$lang['code']]['index']]))
                                    $error .= '<li>'.sprintf($this->language->get('error_data_empty_option_value'), $row).'</li>';

                                if (empty($data[$pattern['Option '.$lang['code']]['index']]))
                                    $error .= '<li>'.sprintf($this->language->get('error_data_empty_option'), $row).'</li>';
                            }
                        }
                    }

                }
            //END Option, Option type or option value empty

            //Option type incorrect
                $option_types = array('select', 'radio', 'text', 'checkbox', 'image');
                if(!empty($pattern['Option type']['index']) && !empty($data[$pattern['Option type']['index']]))
                {
                    if(!in_array($data[$pattern['Option type']['index']], $option_types))
                        $error .= '<li>'.sprintf($this->language->get('error_data_wrong_options_type'), $row).'</li>';
                }
            //END Option type incorrect

            //Option prefix incorrect
                $option_prefix = array('+', '-', '*', '=', '%');
                if(!empty($pattern['Option price prefix']['index']) && !empty($data[$pattern['Option price prefix']['index']]))
                {
                    if(!in_array($data[$pattern['Option price prefix']['index']], $option_prefix))
                        $error .= '<li>'.sprintf($this->language->get('error_data_wrong_options_prefix'), $row).'</li>';
                }
                if(!empty($pattern['Option points prefix']['index']) && !empty($data[$pattern['Option points prefix']['index']]))
                {
                    if(!in_array($data[$pattern['Option points prefix']['index']], $option_prefix))
                        $error .= '<li>'.sprintf($this->language->get('error_data_wrong_options_prefix'), $row).'</li>';
                }
                if(!empty($pattern['Option weight prefix']['index']) && !empty($data[$pattern['Option weight prefix']['index']]))
                {
                    if(!in_array($data[$pattern['Option weight prefix']['index']], $option_prefix))
                        $error .= '<li>'.sprintf($this->language->get('error_data_wrong_options_prefix'), $row).'</li>';
                }
            //END Option prefix incorrect
        }
        return $error;
    }

    public function get_xls_pattern($xls_data)
    {
        $hopped_columns = $this->get_hopped_columns();
        $xls_columns = $xls_data['columns'];

        $array_columns = array();
        $index = 0;

        foreach ($xls_columns as $key => $xc) {
            $found = false;
            foreach ($hopped_columns as $key2 => $hc) {
                if($hc == $xc)
                    $found = true;
            }
            $array_columns[$xc] = array();
            $array_columns[$xc]['created'] = $found;
            $array_columns[$xc]['index'] = false;

            if($found)
            {
                $array_columns[$xc]['index'] = $index;
                $index++;
            }
        }
        return $array_columns;
    }

    public function get_hopped_columns()
    {
        $columns = array(
            '*Model',
            '*Name',
            'Description',
            'Meta description',
            'Meta title',
            'Meta keywords',
            'SEO url',
            'Tags',
            'SKU',
            'EAN',
            'UPC',
            'JAN',
            'MPN',
            'ISBN',
            'Quantity',
            'Minimum',
            'Subtract',
            'Out stock status',
            'Option',
            'Option type',
            'Option value',
            'Option required',
            'Option subtract',
            'Option image',
            'Option price prefix',
            'Option points prefix',
            'Option weight prefix',
            'Option SKU (Options Boost)',
            'Option image (Options Boost)',
            'Price',
            'Spe. Customer Group',
            'Spe. Priority',
            'Spe. Price',
            'Spe. Date start',
            'Spe. Date end',
            'Dis. Customer Group 1',
            'Dis. Quantity 1',
            'Dis. Priority 1',
            'Dis. Price 1',
            'Dis. Date start 1',
            'Dis. Date end 1',
            'Dis. Customer Group 2',
            'Dis. Quantity 2',
            'Dis. Priority 2',
            'Dis. Price 2',
            'Dis. Date start 2',
            'Dis. Date end 2',
            'Dis. Customer Group 3',
            'Dis. Quantity 3',
            'Dis. Priority 3',
            'Dis. Price 3',
            'Dis. Date start 3',
            'Dis. Date end 3',
            'Manufacturer',
            'Cat. level 1',
            '> Cat. level 2',
            '> Cat. level 3',
            '> Cat. level 4',
            '> Cat. level 5',
            'Main image',
            'Image 2',
            'Image 3',
            'Image 4',
            'Image 5',
            'Date available',
            'Points',
            'Requires shipping',
            'Location',
            'Tax class',
            'Sort order',
            'Store',
            'Status',
            'Class weight',
            'Weight',
            'Class length',
            'Length',
            'Width',
            'Height',
        );

        for ($i=1; $i <= $this->attributeNumber; $i++) { 
            array_push($columns, 'Attr. Group '.$i);
            array_push($columns, 'Attribute '.$i);
            array_push($columns, 'Attribute value '.$i);
        }
        for ($i=1; $i <= $this->filterGroupNumber; $i++) { 
            array_push($columns, 'Filter Group '.$i);
            for ($j=1; $j <= $this->filterGroupFilterNumber; $j++) { 
                array_push($columns, 'Filter Gr.'.$i.' filter '.$j);
            }
        }
        array_push($columns, 'Layout');

        //Add extra columns here

        //Multilanguage
            if($this->count_languages > 1)
            {
                $final_columns = array();

                $languages = $this->languages;

                $multilanguage_fields_tmp = $this->fields_multilanguage_names;

                foreach ($columns as $key => $col_name) {
                    if(!in_array($col_name, $this->fields_multilanguage_names))
                        $final_columns[] = $col_name;
                    elseif(!empty($multilanguage_fields_tmp[0]))
                    {
                        foreach ($languages as $key => $value) {
                            $final_columns[] = $multilanguage_fields_tmp[0].' '.$value['code'];
                        }
                        unset($multilanguage_fields_tmp[0]);
                        $multilanguage_fields_tmp = array_values($multilanguage_fields_tmp);
                    }
                }

                $columns = $final_columns;
            }
        //END Multilanguage

        //Customer group Points
            $final_columns = array();

            foreach ($columns as $key => $col_name) {
                $final_columns[] = $col_name;
                if($col_name == 'Points')
                {
                    foreach ($this->customer_groups as $key => $cg) {
                        $final_columns[] = 'Points '.$cg['name'];
                    } 
                }
            }
            $columns = $final_columns;
        //END Customer group points

        //OC Versions compatibility
        if(version_compare(VERSION, '2.0.0.0', '<'))
        {
            foreach ($columns as $key => $col_name) {
                if(strstr($col_name,'Meta title'))
                    unset($columns[$key]);
            }
        }
        $columns = array_values($columns);

        return $columns;
    }

    public function get_classes_length()
    {
        $this->load->model('localisation/length_class');
        $length_classes = $this->model_localisation_length_class->getLengthClasses();
        $config = $this->config->get('config_length_class_id');
        foreach ($length_classes as $key => $class_length) {
            if($config == $class_length['length_class_id'])
            {
                $length_classes[$key]['default'] = true;
                break;
            }
        }
        return $length_classes;
    }

    public function get_classes_tax()
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "tax_class;";
        $result = $this->db->query( $sql );
        return $result->rows;
    }

    public function get_default_values()
    {
        $this->load->model('tool/import_xls');
        //Get Out stock default
            $stock_status = $this->model_tool_import_xls->get_stock_statuses();
            $stock_status = array_values($stock_status);
        //END Get Out stock statuses

        //Get class weight default
            $weight_class_default = $this->model_tool_import_xls->get_classes_weight();
            foreach ($weight_class_default as $key => $class_weight) {
                if(!empty($class_weight['default']))
                {
                    $weight_class_default = $class_weight;
                    break;
                }
            }
        //END Get class weight default

        //Get class length default
            $length_class_default = $this->get_classes_length();
            foreach ($length_class_default as $key => $class_length) {
                if(!empty($class_length['default']))
                {
                    $length_class_default = $class_length;
                    break;
                }
            }
        //END Get class length default

        $hopped_columns = $this->get_hopped_columns();

        $default_values = array();

        foreach ($hopped_columns as $key => $col_name) {

            $temp = array(
                ($key+1),
                $col_name,
            );
            switch ($col_name) {

                //REQUIRED
                case '*Model':
                case strstr($col_name,'*Name'):
                    array_push($temp, $this->language->get('dv_required'));
                    array_push($temp, sprintf($this->language->get('dv_is_required'), $col_name));
                break;

                //WILL BE EMPTY
                case strstr($col_name,'Description'):
                case strstr($col_name,'Meta description'):
                case strstr($col_name,'Meta keywords'):
                case strstr($col_name,'Tags'):
                case 'SKU':
                case 'EAN':
                case 'UPC':
                case 'JAN':
                case 'MPN':
                case 'ISBN':
                case 'Option value':
                case 'Location':
                    array_push($temp, $this->language->get('dv_empty'));
                    array_push($temp, sprintf($this->language->get('dv_will_be_empty'), $col_name));
                break;

                //WILL BE 1
                case 'Minimum':
                case 'Spe. Customer Group':
                case 'Dis. Customer Group 1':
                case 'Dis. Customer Group 2':
                case 'Dis. Customer Group 3':
                case 'Dis. Quantity 1':
                case 'Dis. Quantity 2':
                case 'Dis. Quantity 3':
                case 'Minimum':
                    array_push($temp, 1);
                    array_push($temp, sprintf($this->language->get('dv_will_be'), $col_name, 1));
                break;

                //WILL BE 0
                case 'Price':
                case 'Spe. Priority':
                case 'Dis. Priority 1':
                case 'Dis. Priority 2':
                case 'Dis. Priority 3':
                case strstr($col_name,'Points'):
                case 'Sort order':
                case 'Weight':
                case 'Length':
                case 'Width':
                case 'Height':
                case 'Quantity':
                    array_push($temp, 0);
                    array_push($temp, sprintf($this->language->get('dv_will_be'), $col_name, 0));
                break;

                //WILL BE YES
                case 'Subtract':
                case 'Option required':
                case 'Option subtract':
                case 'Requires shipping':
                    array_push($temp, $this->language->get('dv_yes'));
                    array_push($temp, sprintf($this->language->get('dv_will_be_yes'), $col_name));
                break;

                //WILL BE ENABLED
                case 'Status':
                    array_push($temp, $this->language->get('dv_enabled'));
                    array_push($temp, sprintf($this->language->get('dv_will_be_enabled'), $col_name));
                break;

                //WON'T HAVE
                case strstr($col_name,'Option'):
                case strstr($col_name,'Option image'):
                case strstr($col_name,'Option SKU (Options Boost)'):
                case strstr($col_name,'Option image (Options Boost)'):
                case strstr($col_name,'Spe. Price'):
                case strstr($col_name,'Spe. Date start'):
                case strstr($col_name,'Spe. Date end'):
                case strstr($col_name,'Dis. Price 1'):
                case strstr($col_name,'Dis. Date start 1'):
                case strstr($col_name,'Dis. Date end 1'):
                case strstr($col_name,'Dis. Price 2'):
                case strstr($col_name,'Dis. Date start 2'):
                case strstr($col_name,'Dis. Date end 2'):
                case strstr($col_name,'Dis. Price 3'):
                case strstr($col_name,'Dis. Date start 3'):
                case strstr($col_name,'Dis. Date end 3'):
                case strstr($col_name,'Manufacturer'):
                case strstr($col_name,'Cat. level 1'):
                case strstr($col_name,'> Cat. level 2'):
                case strstr($col_name,'> Cat. level 3'):
                case strstr($col_name,'> Cat. level 4'):
                case strstr($col_name,'> Cat. level 5'):
                case strstr($col_name,'Image 2'):
                case strstr($col_name,'Image 3'):
                case strstr($col_name,'Image 4'):
                case strstr($col_name,'Image 5'):
                case strstr($col_name,'Tax class'):
                case strstr($col_name,'Attr. Group'):
                case strstr($col_name,'Attribute'):
                case strstr($col_name,'Attribute value'):
                case strstr($col_name,'Filter Group'):
                case strstr($col_name,'Filter Gr.'):
                case strstr($col_name,'Layout'):
                    array_push($temp, $this->language->get('dv_empty'));
                    array_push($temp, sprintf($this->language->get('dv_wont_have'), $col_name));
                break;

                case strstr($col_name,'Meta title'):
                    array_push($temp, $this->language->get('dv_autogenerate'));
                    array_push($temp, sprintf($this->language->get('dv_will_be_autogenerate_meta_title'), $col_name));
                break;

                case 'SEO url':
                    array_push($temp, $this->language->get('dv_autogenerate'));
                    array_push($temp, sprintf($this->language->get('dv_will_be_autogenerate'), $col_name));
                break;

                case 'Main image':
                    array_push($temp, $this->language->get('dv_autogenerate'));
                    array_push($temp, sprintf($this->language->get('dv_will_be_autogenerate_image'), $col_name));
                break;

                case 'Option price prefix':
                case 'Option points prefix':
                case 'Option weight prefix':
                    array_push($temp, $this->language->get('dv_autocalculated'));
                    array_push($temp, sprintf($this->language->get('dv_will_be_autocalculate_option_prefix'), $col_name));
                break;

                case 'Date available':
                    array_push($temp, $this->language->get('dv_autogenerate'));
                    array_push($temp, sprintf($this->language->get('dv_will_be_autogenerate_available'), $col_name));
                break;          
                
                case 'Out stock status':
                    array_push($temp, '<b>'.$stock_status[0]['stock_status_id'].'</b> ('.$stock_status[0]['name'].')');
                    array_push($temp, sprintf($this->language->get('dv_will_be'), $col_name, $stock_status[0]['stock_status_id']));
                break;

                case 'Class weight':
                    array_push($temp, '<b>'.$weight_class_default['weight_class_id'].'</b> ('.$weight_class_default['title'].')');
                    array_push($temp, sprintf($this->language->get('dv_will_be'), $col_name, $weight_class_default['weight_class_id']));
                break;

                case 'Class length':
                    array_push($temp, '<b>'.$length_class_default['length_class_id'].'</b> ('.$length_class_default['title'].')');
                    array_push($temp, sprintf($this->language->get('dv_will_be'), $col_name, $length_class_default['length_class_id']));
                break;

                case 'Store':
                    array_push($temp, '<b>0</b> ('.$this->language->get('dv_will_default_store').').');
                    array_push($temp, sprintf($this->language->get('dv_will_be'), $col_name, 0));
                break;

                case 'Option type':
                    array_push($temp, 'select');
                    array_push($temp, sprintf($this->language->get('dv_will_be'), $col_name, 'select'));
                break;

                default:
                    array_push($temp, 'NO ADDED');
                    array_push($temp, 'NO ADDED');
                break;
            }

            $default_values[] = $temp;
        }
        return $default_values;
    }

    public function get_possible_values()
    {
        $this->load->model('tool/import_xls');
        $this->load->model('design/layout');

        $possible_values = array();

        //Out stock status
            $stock_statuses = $this->model_tool_import_xls->get_stock_statuses();
            $temp = array();
            array_push($temp, $this->language->get('possible_values_out_stock'));

            $text = '<ul>';
                foreach ($stock_statuses as $key => $value) {
                    $text .= '<li><b>'.$value['stock_status_id'].'</b>: '.$value['name'].'</li>';
                }
            $text .= '</ul>';
            array_push($temp, $text);

            $possible_values[] = $temp;

        //Option type
            $temp = array();
            array_push($temp, $this->language->get('possible_values_option_types'));

            $text = '<ul><li>select</li><li>radio</li><li>checkbox</li><li>image</li><li>text</li></ul>';
            array_push($temp, $text);

            $possible_values[] = $temp;

        //Option prefix
            $temp = array();
            array_push($temp, $this->language->get('possible_values_option_prefixes'));

            $text = '<ul><li>+</li><li>-</li><li>= (option boost)</li><li>* (option boost)</li><li>% (option boost)</li></ul>';
            array_push($temp, $text);

            $possible_values[] = $temp;

        //Tax Class
            $tax_classes = $this->get_classes_tax();
            $temp = array();
            array_push($temp, $this->language->get('possible_values_tax_class'));

            $text = '<ul>';
                foreach ($tax_classes as $key => $value) {
                    $text .= '<li><b>'.$value['tax_class_id'].'</b>: '.$value['title'].' ['.$value['description'].']</li>';
                }
            $text .= '</ul>';
            array_push($temp, $text);

            $possible_values[] = $temp;

        //Store
            $stores = $this->model_tool_import_xls->get_stores();
            $temp = array();
            array_push($temp, $this->language->get('possible_values_store'));

            $text = '<ul>';
                foreach ($stores as $key => $value) {
                    $text .= '<li><b>'.$value['store_id'].'</b>: '.$value['name'].'</li>';
                }
            $text .= '</ul>';
            array_push($temp, $text);

            $possible_values[] = $temp;

        //Class weight
            $weight_classes = $this->model_tool_import_xls->get_classes_weight();
            $temp = array();
            array_push($temp, $this->language->get('possible_values_class_weight'));

            $text = '<ul>';
                foreach ($weight_classes as $key => $value) {
                    $text .= '<li><b>'.$value['weight_class_id'].'</b>: '.$value['title'].'</li>';
                }
            $text .= '</ul>';
            array_push($temp, $text);

            $possible_values[] = $temp;

        //Class length
            $length_classes = $this->get_classes_length();
            $temp = array();
            array_push($temp, $this->language->get('possible_values_class_length'));

            $text = '<ul>';
                foreach ($length_classes as $key => $value) {
                    $text .= '<li><b>'.$value['length_class_id'].'</b>: '.$value['title'].'</li>';
                }
            $text .= '</ul>';
            array_push($temp, $text);

            $possible_values[] = $temp;

        //Customer group
            $customer_groups = $this->model_tool_import_xls->getCustomerGroups();
            $temp = array();
            array_push($temp, $this->language->get('possible_values_customer_groups'));

            $text = '<ul>';
                foreach ($customer_groups as $key => $layout) {
                    $text .= '<li><b>'.$layout['customer_group_id'].'</b>: '.$layout['name'].'</li>';
                }
            $text .= '</ul>';
            array_push($temp, $text);

            $possible_values[] = $temp;

        //Layouts
            $layouts = $this->model_design_layout->getLayouts();

            $temp = array();
            array_push($temp, $this->language->get('possible_values_layouts'));

            $text = '<ul>';
                foreach ($layouts as $key => $layout) {
                    $text .= '<li><b>'.$layout['layout_id'].'</b>: '.$layout['name'].'</li>';
                }
            $text .= '</ul>';
            array_push($temp, $text);

            $possible_values[] = $temp;


        return $possible_values;
    }

    public function search_images_not_found()
    {
        $database =& $this->db;
        $count_images=0;
        $images_not_found= array();
        $count_images_not_found=0;
    
        $sql = "SELECT p.product_id, p.model, p.image, pd.name FROM " . DB_PREFIX . "product as p INNER JOIN " . DB_PREFIX . "product_description AS pd ON p.product_id=pd.product_id WHERE p.status=1";
        $result = $database->query( $sql );

        $this->load->model('catalog/product');

        foreach($result->rows as $key => $row)
        {
            
            //Main image
            if(!file_exists(DIR_IMAGE. DIRECTORY_SEPARATOR .$row["image"])){
                $images_not_found[] = array(
                    $count_images_not_found+1,
                    $row["product_id"],
                    $row["model"],
                    $row["name"],
                    $row["image"]
                );

                $count_images_not_found++;
            }
            $count_images++;

            //Secondary images
            $other_images = $this->model_catalog_product->getProductImages($row["product_id"]);
            foreach ($other_images as $key => $value) {

                if(!file_exists(DIR_IMAGE. DIRECTORY_SEPARATOR .$value['image'])){
                    $images_not_found[] = array(
                        $count_images_not_found+1,
                        $row["product_id"],
                        $row["model"],
                        $row["name"],
                        $value['image']
                    );

                    $count_images_not_found++;
                }
                $count_images++;
            }
        }

        $array_return = array(
            'message' => sprintf($this->language->get('images_not_found'), $count_images_not_found, $count_images),
            'images' => $images_not_found
        );
        return $array_return;
    }

    /*Export functionality*/            
        public function export(){

            // Loading PHP Excel library
            require_once DIR_SYSTEM . 'PHPExcel/Classes/PHPExcel.php';
            require_once DIR_SYSTEM . 'PHPExcel/Classes/PHPExcel/IOFactory.php';

            // Create a new worksheet Excel and set the counter at 1
            $this->mainCounter = 1;

            if(empty($this->request->get['empty_file']))
            {
                $this->language->load('tool/import_xls');

                $this->load->model('tool/export_xls');
                $this->load->model('catalog/product');
                $this->load->model('catalog/category');
                $this->load->model('catalog/filter');
                $this->load->model('catalog/attribute');
                $this->load->model('catalog/option');

                $where_inserted = false;

                //Get all options
                $all_options = $this->model_tool_export_xls->get_all_options();

                //Get all attributes
                $all_attributes = $this->model_tool_export_xls->get_all_attributes();

                //Get all filters
                $all_filters = $this->model_tool_export_xls->get_all_filters();
                
                //Get all manufacturers
                $all_manufacturers = $this->model_tool_export_xls->get_all_manufacturers();

                //Get all categories
                $all_categories = $this->model_tool_export_xls->get_all_categories();

                //Get all products
                $database =& $this->db;        
                $sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p";

                //Category range
                    $categories = !empty($this->request->get['export_categories']) && $this->request->get['export_categories'] != 'null' ? $this->request->get['export_categories'] : '';
                    if(!empty($categories))
                    {
                        $categories = explode(',', $categories);
                        $sql .= " INNER JOIN " . DB_PREFIX . "product_to_category pc ON (";
                        foreach ($categories as $key => $cat_id) {
                            $sql .= " (pc.product_id = p.product_id AND category_id = ".$cat_id.')';
                            if(count($categories) > ($key+1))
                                $sql .= ' OR ';
                        }

                        $sql .= ")";
                    }
                //END Category range

                //Price range
                    $price_from = $this->request->get['export_range_price_from'];
                    $price_to = $this->request->get['export_range_price_to'];

                    if(!empty($price_from) || !empty($price_to))
                    {
                        if(!$where_inserted)
                        {
                            $sql .= ' WHERE ';
                            $where_inserted = true;
                        }
                        
                        if(!empty($price_from))
                        {
                            $sql .= ' p.price >= '.$price_from;
                        }
                        if(!empty($price_to))
                        {
                            if(!empty($price_from))  $sql .= ' AND ';
                            $sql .= ' p.price <= '.$price_to;
                        }
                    }
                //END Price range

                //Manufacturer range
                    $manufacturers = !empty($this->request->get['export_manufacturers']) && $this->request->get['export_manufacturers'] != 'null' ? $this->request->get['export_manufacturers'] : '';
                    if(!empty($manufacturers))
                    {
                        if(!$where_inserted)
                        {
                            $sql .= ' WHERE ';
                            $where_inserted = true;
                        }
                        else
                            $sql .= ' AND ';

                        $sql .= ' (';

                        $manufacturers = explode(',', $manufacturers);
                        foreach ($manufacturers as $key => $ma_id) {
                            $sql .= " p.manufacturer_id = ".$ma_id;
                            if(count($manufacturers) > ($key+1))
                                $sql .= ' OR ';
                        }

                        $sql .= ")";
                    }
                //END Manufacturer range

                //Models
                    $models = !empty($this->request->get['export_models']) && $this->request->get['export_models'] != 'null' ? explode("|", $this->request->get['export_models']) : '';
                    if(!empty($models))
                    {
                        if(!$where_inserted)
                        {
                            $sql .= ' WHERE ';
                            $where_inserted = true;
                        }
                        else
                            $sql .= ' AND ';

                        $sql .= ' (';

                        foreach ($models as $key => $model) {
                            $sql .= " p.model = '".$model."'";
                            if(count($models) > ($key+1))
                                $sql .= ' OR ';
                        }

                        $sql .= ")";
                    }
                //END Models

                if(!empty($categories))
                {
                    $sql .= ' GROUP BY p.product_id ';
                }

                //Check ranges
                    if(!empty($this->request->get['export_range_from']) && !empty($this->request->get['export_range_to']))
                    {
                        $from = $this->request->get['export_range_from'];
                        $to = $this->request->get['export_range_to'];

                        if(!is_numeric($from) || !is_numeric($to))
                        {
                            $this->session->data['error'] = $this->language->get('export_range_error_number');
                            $error = true;
                        }
                        elseif($from > $to)
                        {
                            $this->session->data['error'] = $this->language->get('export_range_error_more');
                            $error = true;
                        }

                        if(!empty($error))
                        {
                            if(version_compare(VERSION, '2.0.0.0', '>='))
                                $this->response->redirect($this->url->link('tool/import_xls', 'token=' . $this->session->data['token'], 'SSL'));
                            else
                                $this->redirect($this->url->link('tool/import_xls', 'token=' . $this->session->data['token'], 'SSL'));
                        }else
                        {
                            $sql .= ' limit '.($from-1).','.($to-($from-1));
                        }
                    }
                //END Check ranges
                $result = $database->query( $sql );
            }

            $path_excel = DIR_TEMPLATE.'tool/import_xls_files/'.$this->filename.'.xls';
            $this->excel_file = PHPExcel_IOFactory::createReader('Excel2007');
            $this->excel_file = PHPExcel_IOFactory::load($path_excel);

            $this->createColumnsNameExcel();
            $this->mainCounter++;

            if(!empty($result))
            {
                foreach ($result->rows as $pro) {
                    $product = $this->model_catalog_product->getProduct($pro['product_id']);
                    $product['product_description'] = $this->model_catalog_product->getProductDescriptions($pro['product_id']);
                    $product['product_store'] = $this->model_catalog_product->getProductStores($pro['product_id']);
                    $product['product_categories'] = $this->model_catalog_product->getProductCategories($pro['product_id']);
                    $product['product_filters'] = $this->model_catalog_product->getProductFilters($pro['product_id']);
                    $product['product_attributes'] = $this->model_catalog_product->getProductAttributes($pro['product_id']);
                    $product['product_options'] = $this->model_catalog_product->getProductOptions($pro['product_id']);
                    $product['product_options_values'] = array();

                    foreach ($product['product_options'] as $product_option) {
                        if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                            if (!isset($product['product_options_values'][$product_option['option_id']])) {
                                $product['product_options_values'][$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);
                            }
                        }elseif($product_option['type'] == 'text')
                        {

                        }
                    }

                    $product['product_discount'] = $this->model_catalog_product->getProductDiscounts($pro['product_id']);
                    $product['product_special'] = $this->model_catalog_product->getProductSpecials($pro['product_id']);
                    $product['product_images'] = $this->model_catalog_product->getProductImages($pro['product_id']);
                    $product['product_related'] = $this->model_catalog_product->getProductRelated($pro['product_id']);
                    $product['product_layout'] = $this->model_catalog_product->getProductLayouts($pro['product_id']);
                    $product['product_reward'] = $this->model_catalog_product->getProductRewards($pro['product_id']);

                    $products = $this->model_tool_export_xls->format_products($product, $all_options, $all_attributes, $all_filters, $all_manufacturers, $all_categories);
                    $this->excel_file->setActiveSheetIndex(0);

                    foreach ($products as $key => $product) {
                        $this->createExcelWorksheet($product);
                        $this->mainCounter++;
                    }
                }
            }
            $this->getDownloadXlsFile();
        }

        private function getDownloadXlsFile(){
            if(!empty($this->request->get['empty_file']))
                $filename_temp = $this->filename;
            else
                $filename_temp = '['.date('Y-m-d H.i.s').'] - '.$this->config->get('config_meta_title');
            // Redirect output to a client’s web browser (Excel2007)
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
            header("Content-type:   application/x-msexcel; charset=utf-8");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename_temp.'.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($this->excel_file, 'Excel2007');
            $objWriter->save('php://output');
            exit();
        }

        private function createExcelWorksheet($product)
        {
            $product_fields = array(
                'model',
                'name',
                'description',
                'meta_description',
                'meta_title',
                'meta_keyword',
                'keyword',
                'tag',
                'sku',
                'ean',
                'upc',
                'jan',
                'mpn',
                'isbn',
                'quantity',
                'minimum',
                'subtract',
                'stock_status_id',
                'option',
                'option_type',
                'option_value',
                'option_required',
                'option_subtract',
                'option_image',
                'option_price_prefix',
                'option_points_prefix',
                'option_weight_prefix',
                'option_sku_option_boost',
                'option_image_option_boost',
                'price',
                'special_customer_group_id',
                'special_priority',
                'special_price',
                'special_date_start',
                'special_date_end',
                'discount_customer_group_id_1',
                'discount_quantity_1',
                'discount_priority_1',
                'discount_price_1',
                'discount_date_start_1',
                'discount_date_end_1',
                'discount_customer_group_id_2',
                'discount_quantity_2',
                'discount_priority_2',
                'discount_price_2',
                'discount_date_start_2',
                'discount_date_end_2',
                'discount_customer_group_id_3',
                'discount_quantity_3',
                'discount_priority_3',
                'discount_price_3',
                'discount_date_start_3',
                'discount_date_end_3',
                'manufacturer',
                'category_1',
                'category_2',
                'category_3',
                'category_4',
                'category_5',
                'main_image',
                'image_2',
                'image_3',
                'image_4',
                'image_5',
                'date_available',
                'points',
                'shipping',
                'location',
                'tax_class_id',
                'sort_order',
                'store',
                'status',
                'weight_class_id',
                'weight',
                'length_class_id',
                'length',
                'width',
                'height',
            );

            for ($i=1; $i <= $this->attributeNumber; $i++) { 
                array_push($product_fields, 'attribute_group_'.$i);
                array_push($product_fields, 'attribute_attribute_'.$i);
                array_push($product_fields, 'attribute_value_'.$i);
            }
            for ($i=1; $i <= $this->filterGroupNumber; $i++) { 
                array_push($product_fields, 'filter_group_'.$i);
                for ($j=1; $j <= $this->filterGroupFilterNumber; $j++) { 
                    array_push($product_fields, 'filter_group_'.$i.'_filter_'.$j);
                }
            }

            array_push($product_fields, 'layout');

            //Add extra fields here

            //Multilanguage
                if($this->count_languages > 1)
                {
                    $final_product_field = array();

                    $languages = $this->languages;

                    $multilanguage_fields_tmp = $this->fields_multilanguage;

                    foreach ($product_fields as $key => $field_name) {
                        if(!in_array($field_name, $this->fields_multilanguage))
                            $final_product_field[] = $field_name;
                        elseif(!empty($multilanguage_fields_tmp[0]))
                        {
                            foreach ($languages as $key => $value) {
                                $final_product_field[] = $multilanguage_fields_tmp[0].'_'.$value['code'];
                            }
                            unset($multilanguage_fields_tmp[0]);
                            $multilanguage_fields_tmp = array_values($multilanguage_fields_tmp);
                        }
                    }
                    $product_fields = $final_product_field;
                }
            //END Multilanguage
            
            //Product rewards
                $final_product_field = array();
                foreach ($product_fields as $key => $field_name) {
                    $final_product_field[] = $field_name;
                    if($field_name == 'points')
                    {
                        foreach ($this->customer_groups as $key => $cg) {
                            $final_product_field[] = 'points_'.$cg['customer_group_id'];
                        }
                    }
                }
                $product_fields = $final_product_field;
            //END Product rewards

            $num_colums = count($product_fields);

            $letters_columns = $this->letters_columns;

            $letters_columns = array_slice($letters_columns,0,$num_colums);

            if(version_compare(VERSION, '2.0.0.0', '<'))
            {
                foreach ($product_fields as $key => $field) {
                    if(strpos($field, 'meta_title') !== false)
                        unset($product_fields[$key]); //META_TITLE
                }

                $product_fields = array_values($product_fields);
            }

            foreach ($product_fields as $key => $field) {
                if (strpos($field, 'description') !== false)
                    $product[$field] = html_entity_decode($product[$field]);

                if(in_array($field, $product_fields))
                {
                    if (strpos($field, 'model') !== false)
                        $this->excel_file->getActiveSheet()->setCellValueExplicit($letters_columns[$key] . $this->mainCounter, $product[$field], PHPExcel_Cell_DataType::TYPE_STRING);
                    else
                        $this->excel_file->getActiveSheet()->setCellValue($letters_columns[$key] . $this->mainCounter, $product[$field]);
                }
            }
        }

        private function createColumnsNameExcel()
        {
            $letters_columns = $this->letters_columns;

            $columns = $this->get_column_styles();

            $count_column = 0;
            foreach ($columns as $col_name => $col_information) {
                $this->excel_file->getActiveSheet()->setCellValue($letters_columns[$count_column] . $this->mainCounter, $col_name);
                $this->excel_file->getActiveSheet()->getColumnDimension($letters_columns[$count_column])->setAutoSize(true);
                $this->excel_file->getActiveSheet()->getStyle($letters_columns[$count_column].'1')->applyFromArray($col_information['cell_style']);
                $count_column++;
            }
        }

        private function get_column_styles()
        {
            $columns = $this->get_hopped_columns();
            $final_columns = array();

            $array_styles = array('30c5f0','31869b','60497a','e26b0a','c0504d','9bbb59','948a54','4f6228','1f497d','494529','30c5f0','403151','a6a6a6','974706','595959');

            foreach ($columns as $key => $col_name) {
                switch ($col_name) {
                    case strstr($col_name,'*Model'):
                    case strstr($col_name,'*Name'):
                    case strstr($col_name,'Description'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[0]);
                    break;

                    case strstr($col_name,'Meta description'):
                    case strstr($col_name,'Meta title'):
                    case strstr($col_name,'Meta keywords'):
                    case strstr($col_name,'SEO url'):
                    case strstr($col_name,'Tags'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[1]);
                    break;

                    case strstr($col_name,'SKU'):
                    case strstr($col_name,'EAN'):
                    case strstr($col_name,'UPC'):
                    case strstr($col_name,'JAN'):
                    case strstr($col_name,'MPN'):
                    case strstr($col_name,'ISBN'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[2]);
                    break;

                    case strstr($col_name,'Quantity'):
                    case strstr($col_name,'Minimum'):
                    case strstr($col_name,'Subtract'):
                    case strstr($col_name,'Out stock status'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[3]);
                    break;

                    case strstr($col_name,'Option'):
                    case strstr($col_name,'Option type'):
                    case strstr($col_name,'Option value'):
                    case strstr($col_name,'Option required'):
                    case strstr($col_name,'Option subtract'):
                    case strstr($col_name,'Option image'):
                    case strstr($col_name,'Option price prefix'):
                    case strstr($col_name,'Option points prefix'):
                    case strstr($col_name,'Option weight prefix'):
                    case strstr($col_name,'Option SKU (Options Boost)'):
                    case strstr($col_name,'Option image (Options Boost)'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[4]);
                    break;

                    case strstr($col_name,'Price'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[5]);
                    break;

                    case strstr($col_name,'Spe. Customer Group'):
                    case strstr($col_name,'Spe. Priority'):
                    case strstr($col_name,'Spe. Price'):
                    case strstr($col_name,'Spe. Date start'):
                    case strstr($col_name,'Spe. Date end'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[6]);
                    break;



                    case strstr($col_name,'Dis. Customer Group 1'):
                    case strstr($col_name,'Dis. Quantity 1'):
                    case strstr($col_name,'Dis. Priority 1'):
                    case strstr($col_name,'Dis. Price 1'):
                    case strstr($col_name,'Dis. Date start 1'):
                    case strstr($col_name,'Dis. Date end 1'):
                    case strstr($col_name,'Dis. Customer Group 2'):
                    case strstr($col_name,'Dis. Quantity 2'):
                    case strstr($col_name,'Dis. Priority 2'):
                    case strstr($col_name,'Dis. Price 2'):
                    case strstr($col_name,'Dis. Date start 2'):
                    case strstr($col_name,'Dis. Date end 2'):
                    case strstr($col_name,'Dis. Customer Group 3'):
                    case strstr($col_name,'Dis. Quantity 3'):
                    case strstr($col_name,'Dis. Priority 3'):
                    case strstr($col_name,'Dis. Price 3'):
                    case strstr($col_name,'Dis. Date start 3'):
                    case strstr($col_name,'Dis. Date end 3'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[7]);
                    break;

                    case strstr($col_name,'Manufacturer'):
                    case strstr($col_name,'Cat. level 1'):
                    case strstr($col_name,'> Cat. level 2'):
                    case strstr($col_name,'> Cat. level 3'):
                    case strstr($col_name,'> Cat. level 4'):
                    case strstr($col_name,'> Cat. level 5'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[8]);
                    break;

                    case strstr($col_name,'Main image'):
                    case strstr($col_name,'Image 2'):
                    case strstr($col_name,'Image 3'):
                    case strstr($col_name,'Image 4'):
                    case strstr($col_name,'Image 5'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[9]);
                    break;

                    case strstr($col_name,'Date available'):
                    case strstr($col_name,'Points'):
                    case strstr($col_name,'Requires shipping'):
                    case strstr($col_name,'Location'):
                    case strstr($col_name,'Tax class'):
                    case strstr($col_name,'Sort order'):
                    case strstr($col_name,'Store'):
                    case strstr($col_name,'Status'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[10]);
                    break;

                    case strstr($col_name,'Class weight'):
                    case strstr($col_name,'Weight'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[11]);
                    break;

                    case strstr($col_name,'Class length'):
                    case strstr($col_name,'Length'):
                    case strstr($col_name,'Width'):
                    case strstr($col_name,'Height'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[12]);
                    break;


                    case strstr($col_name,'Attr. Group'):
                    case strstr($col_name,'Attribute'):
                    case strstr($col_name,'Attribute value'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[13]);
                    break;

                    case strstr($col_name,'Filter Group'):
                    case strstr($col_name,'Filter Gr.'):
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[14]);
                    break;

                    default:
                        $final_columns[$col_name]['cell_style'] = $this->construct_style($array_styles[0]);
                    break;
                }
                # code...
            }

            return $final_columns;
        }

        private function construct_style($color_rgb)
        {
            return array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 10
                ),
                'fill' => array(
                    'color' => array('rgb' => $color_rgb),
                )
            );
        }
    /*END Export functionality*/

    public function _contruct_view_form()
    {
        $this->data_to_view['action'] = $this->url->link('tool/import_xls', 'token=' . $this->session->data['token'], 'SSL');
        $this->data_to_view['convert_to_innodb'] = $this->url->link('tool/import_xls/convert_to_innodb', 'token=' . $this->session->data['token'], 'SSL');
        $this->data_to_view['save_configuration'] = $this->url->link('tool/import_xls/save_configuration', 'token=' . $this->session->data['token'], 'SSL');
        //Get categories
            $this->load->model('catalog/category');

            $categories = $this->model_catalog_category->getCategories(true);
            
            $finals_categories = array();

            foreach ($categories as $key => $cat) {
                $finals_categories[$cat['category_id']] = $cat['name'];
            }
            asort($finals_categories);
        //END Get categories

        //Get categories
            $this->load->model('catalog/manufacturer');
            $categories = $this->model_catalog_manufacturer->getManufacturers();
            
            $finals_manufacturers = array();

            foreach ($categories as $key => $ma) {
                $finals_manufacturers[$ma['manufacturer_id']] = $ma['name'];
            }
            asort($finals_manufacturers);
        //END Get categories

        //Search loosed images
            $images_not_found = $this->search_images_not_found();
        //END Search loosed images

        //Table images not found
            $theads_images = array(
                $this->language->get('thead_number'),
                $this->language->get('thead_id'),
                $this->language->get('thead_model'),
                $this->language->get('thead_name'),
                $this->language->get('thead_image'),
            );
        //END Table images not found

        //Table default values
            $theads_default_values = array(
                $this->language->get('thead_dv_number'),
                $this->language->get('thead_dv_name'),
                $this->language->get('thead_dv_default_value'),
                $this->language->get('thead_dv_explain'),
            );
            $default_values = $this->get_default_values();
        //END Table default values

        //Table default values
            $possible_values = $this->get_possible_values();
        //END Table default values

        //HTML code to general rules
            $general_rules = array(
                $this->language->get('general_rules_model_name'),
                $this->language->get('general_rules_product_edit'),
                $this->language->get('general_rules_categories'),
                $this->language->get('general_rules_date'),             
                $this->language->get('general_rules_manufactures'),
                $this->language->get('general_rules_options'),
                $this->language->get('general_rules_options_stock'),                
                $this->language->get('general_rules_images'),
                $this->language->get('general_rules_stores'),
            );
            $html_code_general_rules = '<div class="general_rules" style="display:none;"><ul class="general_rules">';
                foreach ($general_rules as $key => $rule) {
                    $html_code_general_rules .= '<li><i class="fa fa-pencil-square-o"></i>'.$rule.'</li>';
                }
            $html_code_general_rules .= '</div>';
        //END code to general rules

        //HTML code to creating options
            $html_code_creating_options  = '<div class="creating_options" style="display:none;">';
                $html_code_creating_options .= '<img src="view/template/tool/import_xls_files/option_example.jpg"><br><br>';
                $html_code_creating_options .= $this->language->get('creating_options_explain');
            $html_code_creating_options .= '</div>';
        //END HTML code to creating options

        //HTML code to creating options
            $updating_rules = array(
                $this->language->get('up_rule_1'),
                $this->language->get('up_rule_2'),
                $this->language->get('up_rule_3'),
                $this->language->get('up_rule_4'),
            );

            $html_code_updating_products = '<div class="updating_products" style="display:none;"><ul class="general_rules">';
                foreach ($updating_rules as $key => $rule) {
                    $html_code_updating_products .= '<li><i class="fa fa-pencil-square-o"></i>'.$rule.'</li>';
                }
            $html_code_updating_products .= '</div>';
        //END HTML code to creating options
        
        //Count products
            $sql = "SELECT COUNT(*) FROM " . DB_PREFIX . "product";
            $products_in_shop = $this->db->query($sql);
            $products_in_shop = $products_in_shop->row['COUNT(*)'];
        //END Count products

        $this->data_to_view['url_export'] = 'index.php?route=tool/import_xls/export&token='.$this->session->data['token'];

        //FILE NAME
            if(version_compare(VERSION, '2.0.0.0', '>='))
                $demo_filename = 'oc2x_xls_import_product_demo';
            else
                $demo_filename = 'oc15x_xls_import_product_demo';
        //END FILE NAME

        //HREF DOWNLOAD FILE
            $href_download_xls = $this->url->link($this->extension_type.'/'.$this->extension_name.'/export&empty_file=true', 'token=' . $this->session->data['token'], 'SSL');
        //END HREF DOWNLOAD FILE

        $form_view = array(
            'action' => $this->url->link($this->extension_type.'/'.$this->extension_name, 'token=' . $this->session->data['token'], 'SSL'),
            'id' => $this->extension_name,
            'extension_name' => $this->extension_name,
            'columns' => 1,
            'tabs' => array(
                $this->language->get('tab_import') => array(
                    'icon' => '<i class="fa fa-download"></i>',
                    'fields' => array(
                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('step_recommended'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'html_code',
                            'html_code' => $this->language->get('step_recommended_explain'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'button',
                            'text' => '<i class="fa fa-database"></i> '.$this->language->get('convert_to_innodb'),
                            'onclick' => 'convert_to_innodb();'
                        ),

                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('step_0'),
                        ),
                        array(
                            'label' => $this->language->get('categories_last_tree'),
                            'help' => $this->language->get('categories_last_tree_help'),
                            'type' => 'boolean',
                            'name' => 'import_xls_categories_last_tree',
                        ),
                        array(
                            'type' => 'button',
                            'label' => $this->language->get('save_configuration'),
                            'text' => '<i class="fa fa-floppy-o"></i> '.$this->language->get('save_configuration'),
                            'onclick' => 'save_configuration();'
                        ),
                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('step_1'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'button',
                            'label' => $this->language->get('step_1_button_download'),
                            'text' => '<i class="fa fa-file-excel-o"></i> '.$this->language->get('step_1_button_download'),
                            'href' => $href_download_xls
                        ),

                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('step_2'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'html_code',
                            'html_code' => $this->language->get('step_2_explain'),
                        ),

                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('step_3'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'button',
                            'class' => 'button_upload_xls',
                            'label' => $this->language->get('upload_file'),
                            'text' => '<i class="fa fa-upload"></i> '.$this->language->get('upload_file').'<span></span>',
                            'onclick' => "$(this).next('input').click();",
                            'help' =>$this->language->get('upload_file_help'),
                            'after' => '<input onchange="readURL($(this));" name="upload" type="file" style="display:none;">'
                        ),

                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('step_4'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => version_compare(VERSION, '2.0.0.0', '>=') ? 'button' : 'button_upload_images',
                            'label' => $this->language->get('upload_images'),
                            'help' => $this->language->get('upload_images_help'),
                            'text' => '<i class="fa fa-file-image-o"></i> '.$this->language->get('upload_images'),
                            'data' => version_compare(VERSION, '2.0.0.0', '>=') ? array('name' => 'toggle','value' => 'image') : array()
                        ),

                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('step_5'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'button',
                            'label' => $this->language->get('import'),
                            'text' => '<i class="fa fa-download"></i> '.$this->language->get('import_start'),
                            'help' => sprintf($this->language->get('import_help'), $this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL')),
                            'onclick' => '$(\'form#import_xls\').submit();ajax_loading_open();'
                        ),

                        array(
                            'type' => 'legend',
                            'text' => $images_not_found['message'],
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'table',
                            'theads' => $theads_images,
                            'data' => $images_not_found['images']
                        ),
                    ),
                ),

                $this->language->get('tab_export') => array(
                    'icon' => '<i class="fa fa-upload"></i>',
                    'fields' => array(
                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('export_legend'),
                            'remove_border_button' => true
                        ),

                        array(
                            'label' => $this->language->get('export_range_from'),
                            'help' => sprintf($this->language->get('export_range_from_help'), $products_in_shop),
                            'type' => 'text',
                            'value' => '',
                            'name' => 'export_range_from'
                        ),

                        array(
                            'label' => $this->language->get('export_range_to'),
                            'help' => sprintf($this->language->get('export_range_to_help'), $products_in_shop),
                            'type' => 'text',
                            'value' => '',
                            'name' => 'export_range_to'
                        ),

                        array(
                            'label' => $this->language->get('export_price_between_from'),
                            'help' => $this->language->get('export_price_between_from_help'),
                            'type' => 'text',
                            'value' => '',
                            'name' => 'export_price_between_from'
                        ),

                        array(
                            'label' => $this->language->get('export_price_between_to'),
                            'help' => $this->language->get('export_price_between_to_help'),
                            'type' => 'text',
                            'value' => '',
                            'name' => 'export_price_between_to'
                        ),

                        array(
                            'label' => $this->language->get('export_models'),
                            'help' => $this->language->get('export_models_help'),
                            'type' => 'text',
                            'value' => '',
                            'name' => 'export_models'
                        ),

                        array(
                            'label' => $this->language->get('export_categories'),
                            'type' => 'select',
                            'multiple' => true,
                            'all_options' => true,
                            'value' => '',
                            'options' => $finals_categories,
                            'name' => 'export_categories'
                        ),

                        array(
                            'label' => $this->language->get('export_manufacturers'),
                            'type' => 'select',
                            'multiple' => true,
                            'all_options' => true,
                            'value' => '',
                            'options' => $finals_manufacturers,
                            'name' => 'export_manufacturers'
                        ),

                        array(
                            'type' => 'button',
                            'label' => $this->language->get('export_button'),
                            'text' => '<i class="fa fa-file-excel-o"></i> '.$this->language->get('export_button'),
                            'onclick' => 'export_start();'
                        ),
                    ),
                ),

                $this->language->get('tab_rules') => array(
                    'icon' => '<i class="fa fa-file-text"></i>',
                    'fields' => array(

                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('general_rules_legend'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'button',
                            'label' => false,
                            'text' => '<i class="fa fa-eye"></i> '.$this->language->get('general_rules_see_hide'),
                            'onclick' => '$(\'div.general_rules\').toggle();',
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'html_code',
                            'html_code' => $html_code_general_rules
                        ),

                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('creating_options_legend'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'button',
                            'label' => false,
                            'text' => '<i class="fa fa-eye"></i> '.$this->language->get('creating_options_see_hide'),
                            'onclick' => '$(\'div.creating_options\').toggle();',
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'html_code',
                            'html_code' => $html_code_creating_options
                        ),

                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('default_values_legend'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'button',
                            'label' => false,
                            'text' => '<i class="fa fa-eye"></i> '.$this->language->get('default_values_see_hide'),
                            'onclick' => '$(\'table.default_values\').toggle();',
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'table',
                            'class' => 'default_values',
                            'style' => 'display:none;',
                            'theads' => $theads_default_values,
                            'data' => $default_values
                        ),

                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('updating_products_legend'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'button',
                            'label' => false,
                            'text' => '<i class="fa fa-eye"></i> '.$this->language->get('updating_products_see_hide'),
                            'onclick' => '$(\'div.updating_products\').toggle();',
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'html_code',
                            'html_code' => $html_code_updating_products
                        ),

                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('possible_values_legend'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'button',
                            'label' => false,
                            'text' => '<i class="fa fa-eye"></i> '.$this->language->get('possible_values_see_hide'),
                            'onclick' => '$(\'table.possible_values\').toggle();',
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'table',
                            'class' => 'possible_values',
                            'style' => 'display:none;',
                            'theads' => false,
                            'data' => $possible_values
                        ),

                        array(
                            'type' => 'legend',
                            'text' => $this->language->get('downlod_demo_pack'),
                            'remove_border_button' => true
                        ),

                        array(
                            'type' => 'html_code',
                            'html_code' => sprintf($this->language->get('downlod_demo_pack_text'), $demo_filename)
                        ),
                    ),
                ),

                $this->language->get('tab_help') => array(
                    'form_colums' => 2,
                    'icon' => '<i class="fa fa-question-circle"></i>',
                    'custom_content' => '<iframe src="http://opencartqualityextensions.com/open_ticket" style="height:500px; width: 100%;"></iframe>'
                ),

                $this->language->get('tab_changelog') => array(
                    'form_colums' => 2,
                    'icon' => '<i class="fa fa-file-text"></i>',
                    'custom_content' => '<iframe src="http://opencartqualityextensions.com/changelogs/changelogs/get_changelogs/'.$this->extension_id.'" style="width: 100%; border:none;"></iframe>'
                ),
            )
        );

        if($this->config->get('import_xls_innodb_converted'))
        {
            unset($form_view['tabs'][$this->language->get('tab_import')]['fields'][0]);
            unset($form_view['tabs'][$this->language->get('tab_import')]['fields'][1]);
            unset($form_view['tabs'][$this->language->get('tab_import')]['fields'][2]);
        }

        return $form_view;
    }

    public function install()
    {
        $this->remove_vqmod_files();
    }

    public function remove_vqmod_files()
    {
        $path = str_replace('admin/', '', DIR_APPLICATION);
        $files_to_delete = array();

        if(version_compare(VERSION, '1.5.6.4', '<='))
        {
            $files_to_delete[] = $path.'vqmod/xml/'.$this->extension_name.'_oc20x.xml';
            $files_to_delete[] = DIR_APPLICATION.'view/template/'.$this->extension_type.'/'.$this->extension_name.'_oc2x.tpl';
        }
        elseif(version_compare(VERSION, '1.5.6.4', '>'))
        {
            $files_to_delete[] = $path.'vqmod/xml/'.$this->extension_name.'_oc15x.xml';
            $files_to_delete[] = DIR_APPLICATION.'view/template/'.$this->extension_type.'/'.$this->extension_name.'_oc15x.tpl';
        }

        foreach ($files_to_delete as $key => $file) {
            if(file_exists($file))
                unlink($file);
        }
    }
}
?>