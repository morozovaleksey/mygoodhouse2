<?php
class ModelToolImportXls extends Model {

	public function __construct($registry) {
        parent::__construct($registry);
    }

	function start_import($xls_data, $pattern)
	{
		ini_set("memory_limit","2048M");
		ini_set("max_execution_time",360);

		$this->load->model('localisation/language');
		$this->load->model('setting/store');
		$this->load->model('catalog/product');

		$all_categories = $this->get_all_categories();
		$all_manufacturers = $this->get_all_manufacturers();
		$all_options = $this->get_all_options();
		$all_filters = $this->get_all_filters();
		$all_attributes = $this->get_all_attributes();
		$all_languages = $this->model_localisation_language->getLanguages();
		$all_stores = $this->model_setting_store->getStores();

		try {

	        //Create news categories
				$this->create_categories($xls_data, $pattern, $all_categories, $all_languages, $all_stores);

			//Create news manufacturers
				$this->create_manufacturers($xls_data, $pattern, $all_manufacturers, $all_stores);

			//Create news optionºs
				$this->create_options($xls_data, $pattern, $all_options, $all_languages);
			
			//Create news attributes
				$this->create_attributes($xls_data, $pattern, $all_attributes, $all_languages);

			//Create news filters
				$this->create_filters($xls_data, $pattern, $all_filters, $all_languages);

			//Format products
				$products = $this->format_products($xls_data, $pattern, $all_languages);

			//Option boost?
				$option_boost = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_option_value` LIKE 'ob_image'");
				$option_boost_installed = !empty($option_boost->row) ? true : false;

			//Downloading images
				$errors_images = '';

				foreach ($products as $key => $pro) {
					if(filter_var($pro['image'], FILTER_VALIDATE_URL))
					{
						$ext = pathinfo($pro['image'], PATHINFO_EXTENSION);
						$image_name = (version_compare(VERSION, '2.0.0.0', '<') ? 'catalog/' : '').$pro['model'].'.'.$ext;
						
						$downloaded = $this->download_remote_image($pro['model'], $pro['image'], $image_name);

						if($downloaded['error'])
							$errors_images .= '<li>'.$downloaded['message'].'</li>';
						else
							$products[$key]['image'] = $image_name;
					}

					foreach ($pro['product_image'] as $key2 => $pro_img) {
						if(filter_var($pro_img['image'], FILTER_VALIDATE_URL))
						{
							$ext = pathinfo($pro_img['image'], PATHINFO_EXTENSION);
							$image_name = (version_compare(VERSION, '2.0.0.0', '<') ? 'catalog/' : '').$pro['model'].'-'.$key2.'.'.$ext;
							$downloaded = $this->download_remote_image($pro['model'], $pro_img['image'], $image_name);

							if($downloaded['error'])
								$errors_images .= '<li>'.$downloaded['message'].'</li>';
							else
								$products[$key]['product_image'][$key2]['image'] = $image_name;
						}
					}
				}

				if(!empty($errors_images))
					return $this->language->get('error_downloading_image_main').$errors_images;

			//Import products
				foreach ($products as $key => $pro) {
					if($pro['editting'])
						$this->editProduct($pro['editting'], $pro, $all_languages, $option_boost_installed);
					else
						$this->model_catalog_product->addProduct($pro);
				}

	    } catch (Exception $e) {
	      $this->db->query("ROLLBACK");
	      return $e;
	    }

	   	$this->db->query("COMMIT");

	   	return false;
	}

	//GET FUNCTIONS
		public function get_all_categories()
		{
			$temporal_sql = "SELECT c.category_id,c.parent_id,cd.name FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (c.category_id = cd.category_id AND cd.language_id = ".(int)$this->config->get('config_language_id').");";
			$result = $this->db->query( $temporal_sql );
			$tree = $this->buildTree($result->rows);

			return $tree;
		}

		public function get_category_parent_tree($categories, $cat_name)
		{
			foreach ($categories as $key => $cat) {
				if($cat['name'] == $cat_name)
					return $cat;				
			}
			return false;
		}

		public function get_all_manufacturers()
		{
			$this->load->model('catalog/manufacturer');
			$manufacturers = $this->model_catalog_manufacturer->getManufacturers();

			$manufacturers_final = array();

			foreach ($manufacturers as $key => $ma) {
				$manufacturers_final[$ma['name']] = $ma['manufacturer_id'];
			}
			return $manufacturers_final;        
		}

		public function get_all_filters()
		{
			$this->load->model('catalog/filter');
			$filters = $this->model_catalog_filter->getFilters(false);

			$filters_final = array();
			foreach ($filters as $key => $fi) {
				if (!isset($filters_final[$fi['group']]))
					$filters_final[$fi['group']] = array();

				if (!isset($filters_final[$fi['group']]['filters']))
					$filters_final[$fi['group']]['filters'] = array();

				$filters_final[$fi['group']]['filter_group_id'] = $fi['filter_group_id'];
				$filters_final[$fi['group']]['filters'][$fi['name']] = $fi['filter_id'];
			}
			return $filters_final;        
		}

		public function get_all_attributes()
		{
			$this->load->model('catalog/attribute');
			$attributes = $this->model_catalog_attribute->getAttributes();

			$attributes_final = array();
			foreach ($attributes as $key => $at) {
				if (!isset($attributes_final[$at['attribute_group']]))
					$attributes_final[$at['attribute_group']] = array();

				$attributes_final[$at['attribute_group']]['attribute_group_id'] = $at['attribute_group_id'];

				$attributes_final[$at['attribute_group']]['attributes'][$at['name']] = $at['attribute_id'];
			}
			return $attributes_final;         
		}

		public function get_all_options()
		{
			$this->load->model('catalog/option');
			$all_options = $this->model_catalog_option->getOptions();

			$options_final = array();

			//Format options
			foreach ($all_options as $key => $op) {
				$options_final[$op['name'].'_'.$op['type']] = array(
					'option_id' => $op['option_id'],
					'option_name' => $op['name'],
					'option_values' => array()
				);
			}

			//Get all options values to each option
			foreach ($options_final as $option_name => $op) {
				$optuion_values = $this->model_catalog_option->getOptionValues($op['option_id']);

				//Format option values
				$option_values_final = array();

				foreach ($optuion_values as $key => $op) {
					$option_values_final[$op['name']] = $op['option_value_id'];
				}

				$options_final[$option_name]['option_values'] = $option_values_final;
			}

			return $options_final;         
		}

		public function get_stock_statuses()
		{
			$sql = "SELECT * FROM " . DB_PREFIX . "stock_status WHERE language_id = ".(int)$this->config->get('config_language_id').";";
			$result = $this->db->query( $sql );
			$stock_statuses = $result->rows;
			
			$this->load->model('opencartqualityextensions/tools');
			$stock_statuses = $this->model_opencartqualityextensions_tools->aasort($stock_statuses, 'name');
			return $stock_statuses;
		}

		public function get_classes_weight()
		{
			$this->load->model('localisation/weight_class');
			$weight_classes = $this->model_localisation_weight_class->getWeightClasses();
			$config = $this->config->get('config_weight_class_id');
			foreach ($weight_classes as $key => $class_weight) {
				if($config == $class_weight['weight_class_id'])
				{
					$weight_classes[$key]['default'] = true;
					break;
				}
			}
			return $weight_classes;
		}

		public function get_stores()
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
	//END GET FUNCTIONS

	//CREATE FUNCTIONS
		public function create_categories($xls_data, $pattern, $all_categories, $all_languages, $all_stores)
		{
			$this->load->model('catalog/category');

			$reload_categories = true;
			foreach ($xls_data['data'] as $key => $data) {
				if($reload_categories)
				{
					$all_categories = $this->get_all_categories();
					$reload_categories = false;
				}

				$columns_category = array('Cat. level 1', '> Cat. level 2', '> Cat. level 3', '> Cat. level 4', '> Cat. level 5');
				$array_categories = array();

				foreach ($columns_category as $key => $col_name) {
					if(!empty($pattern[$col_name]['index']) && !empty($data[$pattern[$col_name]['index']]))
						$array_categories[] = $data[$pattern[$col_name]['index']];
				}

				foreach ($array_categories as $key => $cat) {
					//BEGIN Get tree of the parent
						if($key == 0)
						{
							$category_tree = $this->get_category_tree($all_categories, $cat);
							if(!$category_tree)
							{
								$parent_id = $this->create_category($cat, 0, $all_languages, $all_stores);
								$reload_categories = true;
							}
							else
								$parent_id = $category_tree['category_id'];
						}
						else
						{
							if(!empty($category_tree))
								$category_tree = $this->get_category_tree($category_tree['childrens'], $cat);

							if(!$category_tree)
							{
								$parent_id = $this->create_category($cat, $parent_id, $all_languages, $all_stores);
								$reload_categories = true;
							}
							else
								$parent_id = $category_tree['category_id'];
						}
					//END Get tree of the parent
				}

			}
			
			
		}

		public function create_category($cat_name, $parent_id = 0, $all_languages, $all_stores)
		{
			$temp = array(
				'category_description' => '',
				'category_store' => array(0),
				'path' => null,
				'parent_id' => $parent_id,
				'filter' => null,
				'keyword' => $this->autogenerate_seo_url($cat_name),
				'image' => 'no_image.jpg',
				'top' => 1,
				'column' => 1,
				'sort_order' => 0,
				'status' => 1,
			);
			
			foreach ($all_languages as $key => $lng) {
				$temp['category_description'][$lng['language_id']] = array(
					'name' => $cat_name,
					'meta_title' => $cat_name,
                    'meta_description' => null,
                    'meta_keyword' => null,
                    'description' => null,
				);
			}

			foreach ($all_stores as $key => $store) {
				$temp['category_store'][] = $store['store_id'];
			}

			return $this->model_catalog_category->addCategory($temp);
		}

		public function create_manufacturers($xls_data, $pattern, $all_manufacturers, $all_stores)
		{
			$this->load->model('catalog/manufacturer');

			$array_manufacturers = array();
			$index_manufacturer = !empty($pattern['Manufacturer']['index']) ? $pattern['Manufacturer']['index'] : '';
			if(!empty($index_manufacturer))
			{
				foreach ($xls_data['data'] as $key => $data) {				
					if(!empty($data[$index_manufacturer]))
						$array_manufacturers[] = $data[$index_manufacturer];
				}
				$array_manufacturers = array_unique($array_manufacturers);
				
				foreach ($array_manufacturers as $key => $ma) {
					if(!isset($all_manufacturers[$ma]))
					{
						$temp = array(
							'name' => $ma,
					    	'manufacturer_store' => array(0),
					    	'keyword' => $this->autogenerate_seo_url($ma),
					    	'image' => 'no_image.jpg',
					    	'sort_order' => 0,
						);

						foreach ($all_stores as $key => $store) {
							$temp['manufacturer_store'][] = $store['store_id'];
						}

						$this->model_catalog_manufacturer->addManufacturer($temp);
					}
				}
			}
		}

		public function create_options($xls_data, $pattern, $all_options, $all_languages)
		{
			$this->load->model('catalog/option');
			
			if($this->count_languages == 1)
			{
				$index_option = !empty($pattern['Option']['index']) ? $pattern['Option']['index'] : '';
				$index_option_value = !empty($pattern['Option value']['index']) ? $pattern['Option value']['index'] : '';
			}
			else
			{
				$index_option = !empty($pattern['Option '.$this->default_language_code]['index']) ? $pattern['Option '.$this->default_language_code]['index'] : '';
				$index_option_value = !empty($pattern['Option value '.$this->default_language_code]['index']) ? $pattern['Option value '.$this->default_language_code]['index'] : '';
			}

			$index_option_type = !empty($pattern['Option type']['index']) ? $pattern['Option type']['index'] : '';
			$index_option_image = !empty($pattern['Option image']['index']) ? $pattern['Option image']['index'] : '';

			$array_options = array();

			if(!empty($index_option)  && !empty($index_option_value))
			{
				foreach ($xls_data['data'] as $key => $data) {
					$option = $data[$index_option];
					$option_type = !empty($data[$index_option_type]) ? $data[$index_option_type] : 'select';
					$option_value = $data[$index_option_value];
					$option_image = !empty($data[$index_option_image]) ? $data[$index_option_image] : '';

					if(!empty($option) && !empty($option_type) && !empty($option_value))
					{
						if(!isset($array_options[$option.'_'.$option_type]))
							$array_options[$option.'_'.$option_type] = array(
								'option_type' => $option_type,
								'option_values' => array(),
								'option_name' => $option,
								//Only multilanguage
								'option_names' => array(),
							);

						$options_values_multilanguage = array();
						if($this->count_languages > 1)
						{
							foreach ($this->languages as $key => $lang) {
								//Options multilanguage
									$index_option_temp = $pattern['Option '.$lang['code']]['index'];
									$array_options[$option.'_'.$option_type]['option_names'][$lang['language_id']]['name'] = !empty($data[$index_option_temp]) ? $data[$index_option_temp] : $option;
								//END Options multilanguage

								//Option values multilanguage
									$index_option_value_temp = $pattern['Option value '.$lang['code']]['index'];
									$options_values_multilanguage[$lang['language_id']]['name'] = !empty($data[$index_option_value_temp]) ? $data[$index_option_value_temp] : $option_value;
								//END Option values multilanguage
							}
						}

						$array_options[$option.'_'.$option_type]['option_values'][] = array('option_value' => $option_value, 'option_values' => $options_values_multilanguage, 'option_value_image' => $option_image);
					}
				}
				foreach ($array_options as $key => $opt) {
					$array_options[$key]['option_values'] = $this->remove_duplicate('option_value',$opt['option_values']);
				}

				foreach ($array_options as $opt_name => $opt) {
					$temp = array(
						'option_description' => array(),
						'type' => $opt['option_type'],
						'sort_order' => 0,
						'option_value' => array(),
					);

					if($this->count_languages > 1)
						$temp['option_description'] = $opt['option_names'];
					else
						$temp['option_description'][$this->config->get('config_language_id')]['name'] = $opt['option_name'];

					foreach ($opt['option_values'] as $key2 => $opt_val) {

						$option_value_name = array();

						if($this->count_languages > 1)
							$option_value_name = $opt_val['option_values'];
						else
							$option_value_name[$this->config->get('config_language_id')]['name'] = $opt_val['option_value'];

						$temp_opt_val = array(
							'option_value_id' => '',
							'image' => $opt_val['option_value_image'],
							'sort_order' => 0,
							'name' => $opt_val['option_value'],
							'option_value_description' => $option_value_name
						);

						$temp['option_value'][] = $temp_opt_val; 
					}

					if(!isset($all_options[$opt_name]))
					{
						//Create new option with values
						$this->model_catalog_option->addOption($temp);
					}
					else
					{
						//Create new option values
						foreach ($temp['option_value'] as $key => $opt_val) {
							if(!isset($all_options[$opt_name]['option_values'][$opt_val['name']]))
							{
								$option_id = $all_options[$opt_name]['option_id'];
								$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$option_id . "', image = '".$opt_val['image']."', sort_order = '0'");
								$option_value_id = $this->db->getLastId();

								foreach ($all_languages as $key => $lng) {
									$this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$option_value_id . "', language_id = '" . (int)$lng['language_id'] . "', option_id = '" . (int)$option_id . "', name = '" . $this->db->escape($opt_val['name']) . "'");
								}
							}
						}
					}
				}
			}
		}

		public function create_attributes($xls_data, $pattern, $all_attributes, $all_languages)
		{
			$final_attributes = array();
			foreach ($xls_data['data'] as $key => $data) {
				for ($i=1; $i <= $this->attributeNumber; $i++) { 

					if($this->count_languages == 1)
					{
						$attr_group_index = !empty($pattern['Attr. Group '.$i]['index']) ? $pattern['Attr. Group '.$i]['index'] : '';
						$attr_index = !empty($pattern['Attribute '.$i]['index']) ? $pattern['Attribute '.$i]['index'] : '';
					}
					else
					{
						$name_attr_group_column = 'Attr. Group '.$i.' '.$this->default_language_code;
						$name_attr_colum = 'Attribute '.$i.' '.$this->default_language_code;

						$attr_group_index = !empty($pattern[$name_attr_group_column]['index']) ? $pattern[$name_attr_group_column]['index'] : '';
						$attr_index = !empty($pattern[$name_attr_colum]['index']) ? $pattern[$name_attr_colum]['index'] : '';
					}

					if(!empty($attr_group_index) && !empty($attr_index) && !empty($data[$attr_group_index]) && !empty($data[$attr_index]))
					{
						$temp = array();

						$attribute_group_name = $data[$attr_group_index];
						$attribute_name = $data[$attr_index];

						if(!isset($final_attributes[$attribute_group_name]['attributes']))
							$final_attributes[$attribute_group_name]['attributes'] = array();

						if(!isset($final_attributes[$attribute_group_name]['attributes'][$attribute_name]))
							$final_attributes[$attribute_group_name]['attributes'][$attribute_name] = array();

						$attribute_group_name_original = $attribute_group_name;
						$attribute_name_original = $attribute_name;

						foreach ($this->languages as $lang_code => $lang) {
							if(!isset($final_attributes[$attribute_group_name]['translates']))
								$final_attributes[$attribute_group_name]['translates'] = array();

							$name_attr_group_column = 'Attr. Group '.$i.' '.$lang_code;
							$name_attr_colum = 'Attribute '.$i.' '.$lang_code;

							$attr_index_trans = !empty($pattern[$name_attr_colum]['index']) ? $pattern[$name_attr_colum]['index'] : '';
							$attribute_name = !empty($data[$attr_index_trans]) ? $data[$attr_index_trans] : $attribute_name_original;

							$attr_group_index_trans = !empty($pattern[$name_attr_group_column]['index']) ? $pattern[$name_attr_group_column]['index'] : '';
							$attribute_group_name_trans = !empty($data[$attr_group_index_trans]) ? $data[$attr_group_index_trans] : $attribute_group_name_original;

							$final_attributes[$attribute_group_name_original]['translates'][$lang_code] = $attribute_group_name_trans;

							if(!isset($final_attributes[$attribute_group_name_original]['attributes'][$attribute_name_original]['translates']))
								$final_attributes[$attribute_group_name_original]['attributes'][$attribute_name_original]['translates'] = array();

							$final_attributes[$attribute_group_name_original]['attributes'][$attribute_name_original]['translates'][$lang_code] = $attribute_name;
						}
					}
					
				}
			}

			/*foreach ($final_attributes as $key => $attrs) {
				$final_attributes[$key] = array_unique($attrs);
			}*/

			foreach ($final_attributes as $attr_group_name => $attrs) {
				//Insert attributes groups
					if(!isset($all_attributes[$attr_group_name]))
					{
						$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group SET sort_order = '0'");

						$attribute_group_id = $this->db->getLastId();
						
						foreach ($this->languages as $code => $lng) {

							$attr_group_name = $attrs['translates'][$lng['code']];
							
							$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description SET attribute_group_id = '" . (int)$attribute_group_id . "', language_id = '" . (int)$lng['language_id'] . "', name = '" . $this->db->escape($attr_group_name) . "'");
						}
					}
					else
						$attribute_group_id = $all_attributes[$attr_group_name]['attribute_group_id'];
				//END Insert attributes groups

				//Insert attributes
					foreach ($attrs['attributes'] as $attr_name => $attributes) {

						if(!isset($all_attributes[$attr_group_name]['attributes'][$attr_name]))
						{
							$this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$attribute_group_id . "', sort_order = '0'");

							$attribute_id = $this->db->getLastId();

							foreach ($this->languages as $key => $lng) {
								$attr_group_name = $attributes['translates'][$lng['code']];
								$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$lng['language_id'] . "', name = '" . $this->db->escape($attr_group_name) . "'");
							}
						}
					}
				//END Insert attributes
			}
		}

		public function create_filters($xls_data, $pattern, $all_filters, $all_languages)
		{
			$final_filters = array();
			foreach ($xls_data['data'] as $key => $data) {
				for ($i=1; $i <= $this->filterGroupNumber; $i++) {
					$filter_group_index = !empty($pattern['Filter Group '.$i]['index']) ? $pattern['Filter Group '.$i]['index'] : '';

					for ($j=1; $j <= $this->filterGroupFilterNumber; $j++) { 
						$filter_index = !empty($pattern['Filter Gr.'.$i.' filter '.$j]['index']) ? $pattern['Filter Gr.'.$i.' filter '.$j]['index'] : '';
						if(
							!empty($filter_group_index) && !empty($filter_index) &&
							!empty($data[$filter_group_index]) && !empty($data[$filter_index])
						)
						{
							if(!isset($final_filters[$data[$filter_group_index]]))
								$final_filters[$data[$filter_group_index]] = array();

							$final_filters[$data[$filter_group_index]][] = $data[$filter_index];
						}
					}
				}
			}

			foreach ($final_filters as $key => $filters) {
				$final_filters[$key] = array_unique($filters);
			}
			
			foreach ($final_filters as $group_name => $filters) {
				if(!isset($all_filters[$group_name]))
				{
					$this->db->query("INSERT INTO `" . DB_PREFIX . "filter_group` SET sort_order = '0'");

					$filter_group_id = $this->db->getLastId();

					foreach ($all_languages as $key => $lng) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "filter_group_description SET filter_group_id = '" . (int)$filter_group_id . "', language_id = '" . (int)$lng['language_id'] . "', name = '" . $this->db->escape($group_name) . "'");
					}
				}
				else
					$filter_group_id = $all_filters[$group_name]['filter_group_id'];

				foreach ($filters as $key => $filter) {
					if(!isset($all_filters[$group_name]['filters'][$filter]))
					{
						$this->db->query("INSERT INTO " . DB_PREFIX . "filter SET filter_group_id = '" . (int)$filter_group_id . "', sort_order = '0'");

						$filter_id = $this->db->getLastId();

						foreach ($all_languages as $key => $lng) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "filter_description SET filter_id = '" . (int)$filter_id . "', language_id = '" . (int)$lng['language_id'] . "', filter_group_id = '" . (int)$filter_group_id . "', name = '" . $this->db->escape($filter) . "'");
						}
					}
				}			
			}
		}
	//END CREATE FUNCTIONS

	//OTHERS FUNCTIONS
		/*function autogenerate_seo_url($str)
		{
			$friendlyURL = htmlentities($str, ENT_COMPAT, "UTF-8", false); 
		    $friendlyURL = preg_replace('/&([a-z]{1,2})(?:acute|circ|lig|grave|ring|tilde|uml|cedil|caron);/i','\1',$friendlyURL);
		    $friendlyURL = html_entity_decode($friendlyURL,ENT_COMPAT, "UTF-8"); 
		    $friendlyURL = preg_replace('/[^a-z0-9-]+/i', '-', $friendlyURL);
		    $friendlyURL = preg_replace('/-+/', '-', $friendlyURL);
		    $friendlyURL = trim($friendlyURL, '-');
		    $friendlyURL = strtolower($friendlyURL);
		    return $friendlyURL;
		}*/

		private function autogenerate_seo_url($string) {

			$cyrylicFrom = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');

	        $cyrylicTo   = array('A', 'B', 'W', 'G', 'D', 'Ie', 'Io', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Ch', 'C', 'Tch', 'Sh', 'Shtch', '', 'Y', '', 'E', 'Iu', 'Ia', 'a', 'b', 'w', 'g', 'd', 'ie', 'io', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ch', 'c', 'tch', 'sh', 'shtch', '', 'y', '', 'e', 'iu', 'ia');

			$from = array("Á", "À", "Â", "Ä", "Ă", "Ā", "Ã", "Å", "Ą", "Æ", "Ć", "Ċ", "Ĉ", "Č", "Ç", "Ď", "Đ", "Ð", "É", "È", "Ė", "Ê", "Ë", "Ě", "Ē", "Ę", "Ə", "Ġ", "Ĝ", "Ğ", "Ģ", "á", "à", "â", "ä", "ă", "ā", "ã", "å", "ą", "æ", "ć", "ċ", "ĉ", "č", "ç", "ď", "đ", "ð", "é", "è", "ė", "ê", "ë", "ě", "ē", "ę", "ə", "ġ", "ĝ", "ğ", "ģ", "Ĥ", "Ħ", "I", "Í", "Ì", "İ", "Î", "Ï", "Ī", "Į", "Ĳ", "Ĵ", "Ķ", "Ļ", "Ł", "Ń", "Ň", "Ñ", "Ņ", "Ó", "Ò", "Ô", "Ö", "Õ", "Ő", "Ø", "Ơ", "Œ", "ĥ", "ħ", "ı", "í", "ì", "i", "î", "ï", "ī", "į", "ĳ", "ĵ", "ķ", "ļ", "ł", "ń", "ň", "ñ", "ņ", "ó", "ò", "ô", "ö", "õ", "ő", "ø", "ơ", "œ", "Ŕ", "Ř", "Ś", "Ŝ", "Š", "Ş", "Ť", "Ţ", "Þ", "Ú", "Ù", "Û", "Ü", "Ŭ", "Ū", "Ů", "Ų", "Ű", "Ư", "Ŵ", "Ý", "Ŷ", "Ÿ", "Ź", "Ż", "Ž", "ŕ", "ř", "ś", "ŝ", "š", "ş", "ß", "ť", "ţ", "þ", "ú", "ù", "û", "ü", "ŭ", "ū", "ů", "ų", "ű", "ư", "ŵ", "ý", "ŷ", "ÿ", "ź", "ż", "ž");

			$to   = array("A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "B", "t", "t", "b", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z");

			$from = array_merge($from, $cyrylicFrom);
			$to = array_merge($to, $cyrylicTo);

			$str = str_replace($from, $to, html_entity_decode($string, ENT_QUOTES, 'UTF-8'));

			$friendlyURL = htmlentities($str, ENT_COMPAT, "UTF-8", false); 
		    $friendlyURL = preg_replace('/&([a-z]{1,2})(?:acute|circ|lig|grave|ring|tilde|uml|cedil|caron);/i','\1',$friendlyURL);
		    $friendlyURL = html_entity_decode($friendlyURL,ENT_COMPAT, "UTF-8"); 
		    $friendlyURL = preg_replace('/[^a-z0-9-]+/i', '-', $friendlyURL);
		    $friendlyURL = preg_replace('/-+/', '-', $friendlyURL);
		    $friendlyURL = trim($friendlyURL, '-');
		    $friendlyURL = strtolower($friendlyURL);

		    return $friendlyURL;
	    }

		function format_products($xls_data, $pattern, $all_languages)
		{
			$all_categories = $this->get_all_categories();
			$all_manufacturers = $this->get_all_manufacturers();
			$all_options = $this->get_all_options();
			$all_filters = $this->get_all_filters();
			$all_attributes = $this->get_all_attributes();
			$all_stores = $this->model_setting_store->getStores();
			$all_customer_groups = $this->getCustomerGroups();
			$stock_statuses = $this->get_stock_statuses();
			$stock_statuses = array_values($stock_statuses);
			$prods_by_name = array();

			$column_name = '*Name'.($this->count_languages > 1 ? ' '.$this->default_language_code : '');

			if($this->allow_options)
			{
				foreach ($xls_data['data'] as $key => $data) {
					$index_name = $pattern[$column_name]['index'];
					if(!isset($prods_by_name[$data[$index_name]]))
						$prods_by_name[$data[$index_name]] = array();

					$prods_by_name[$data[$index_name]][] = $data;
				}
			}
			else
				$prods_by_name = $xls_data['data'];

			$final_products = array();
			foreach ($prods_by_name as $product_name => $products) {
				$count_products = 0;
				$temp = array();
				$quantity = 0;
				$final_options = array();

				if(!$this->allow_options)
				{
					$temp_products = array();
					$temp_products[] = $products;
					$products = $temp_products;
				}
				
				foreach ($products as $key => $pro) {
					if($count_products == 0)
					{
						//Check product exists
							$editting = false;
							$temporal_sql = "SELECT product_id FROM `" . DB_PREFIX . "product` WHERE model = '".$pro[$pattern['*Model']['index']]."';";
							$result = $this->db->query( $temporal_sql );

							if(!empty($result->row['product_id']))
								$editting = $result->row['product_id'];
						//END Check product exists

						//Default values 0 or 1
							$subtract = 1;
							if(!empty($pattern['Subtract']['index']) && isset($pro[$pattern['Subtract']['index']]) && $pro[$pattern['Subtract']['index']] === '0') $subtract = 0;

							$requires_shipping = 1;
							if(!empty($pattern['Requires shipping']['index']) && isset($pro[$pattern['Requires shipping']['index']]) && $pro[$pattern['Requires shipping']['index']] === '0') $requires_shipping = 0;

							$status = 1;
							if(!empty($pattern['Status']['index']) && isset($pro[$pattern['Status']['index']]) && $pro[$pattern['Status']['index']] === '0') $status = 0;
						//END Default values 0 or 1

						$temp = array(
							'editting' => $editting,
							'product_description' => array(),
							'model' => $pro[$pattern['*Model']['index']],
							'sku' => !empty($pattern['SKU']['index']) && !empty($pro[$pattern['SKU']['index']]) ? $pro[$pattern['SKU']['index']] : '',
							'upc' => !empty($pattern['UPC']['index']) && !empty($pro[$pattern['UPC']['index']]) ? $pro[$pattern['UPC']['index']] : '',
							'ean' => !empty($pattern['EAN']['index']) && !empty($pro[$pattern['EAN']['index']]) ? $pro[$pattern['EAN']['index']] : '',
							'jan' => !empty($pattern['JAN']['index']) && !empty($pro[$pattern['JAN']['index']]) ? $pro[$pattern['JAN']['index']] : '',
							'isbn' => !empty($pattern['ISBN']['index']) && !empty($pro[$pattern['ISBN']['index']]) ? $pro[$pattern['ISBN']['index']] : '',
							'mpn' => !empty($pattern['MPN']['index']) && !empty($pro[$pattern['MPN']['index']]) ? $pro[$pattern['MPN']['index']] : '',
							'subtract' => $subtract,
							'location' => !empty($pattern['Location']['index']) && !empty($pro[$pattern['Location']['index']]) ? $pro[$pattern['Location']['index']] : '',
							'price' => !empty($pattern['Price']['index']) && !empty($pro[$pattern['Price']['index']]) ? $pro[$pattern['Price']['index']] : '',
							'tax_class_id' => !empty($pattern['Tax class']['index']) && !empty($pro[$pattern['Tax class']['index']]) ? $pro[$pattern['Tax class']['index']] : 0,
							'quantity' => !empty($pattern['Quantity']['index']) && !empty($pro[$pattern['Quantity']['index']]) ? $pro[$pattern['Quantity']['index']] : 0,
							'minimum' => !empty($pattern['Minimum']['index']) && !empty($pro[$pattern['Minimum']['index']]) ? $pro[$pattern['Minimum']['index']] : 1,
							'stock_status_id' => !empty($pattern['Out stock status']['index']) && !empty($pro[$pattern['Out stock status']['index']]) ? $pro[$pattern['Out stock status']['index']] : $stock_statuses[0]['stock_status_id'],
							'shipping' => $requires_shipping,
							'keyword' => !empty($pattern['SEO url']['index']) && !empty($pro[$pattern['SEO url']['index']]) ? $pro[$pattern['SEO url']['index']] : $this->autogenerate_seo_url($pro[$pattern[$column_name]['index']]),
							'image' => !empty($pattern['Main image']['index']) && !empty($pro[$pattern['Main image']['index']]) ? $pro[$pattern['Main image']['index']] : $pro[$pattern['*Model']['index']].'.jpg',
							'date_available' => !empty($pattern['Date available']['index']) && !empty($pro[$pattern['Date available']['index']]) ? date('Y-m-d', strtotime(str_replace('/', '-', $pro[$pattern['Date available']['index']]))) : date('Y-m-d'),
							'length' => !empty($pattern['Length']['index']) && !empty($pro[$pattern['Length']['index']]) ? $pro[$pattern['Length']['index']] : '',
							'width' => !empty($pattern['Width']['index']) && !empty($pro[$pattern['Width']['index']]) ? $pro[$pattern['Width']['index']] : '',
							'height' => !empty($pattern['Height']['index']) && !empty($pro[$pattern['Height']['index']]) ? $pro[$pattern['Height']['index']] : '',
							'weight' => !empty($pattern['Weight']['index']) && !empty($pro[$pattern['Weight']['index']]) ? $pro[$pattern['Weight']['index']] : '',
							'length_class_id' => !empty($pattern['Class length']['index']) && !empty($pro[$pattern['Class length']['index']]) ? $pro[$pattern['Class length']['index']] : $this->config->get('config_length_class_id'),
							'weight_class_id' => !empty($pattern['Class weight']['index']) && !empty($pro[$pattern['Class weight']['index']]) ? $pro[$pattern['Class weight']['index']] : $this->config->get('config_weight_class_id'),
							'status' => $status,
							'sort_order' => !empty($pattern['Sort order']['index']) && !empty($pro[$pattern['Sort order']['index']]) ? $pro[$pattern['Sort order']['index']] : 0,
							'manufacturer_id' => !empty($pattern['Manufacturer']['index']) && !empty($pro[$pattern['Manufacturer']['index']]) ? $all_manufacturers[$pro[$pattern['Manufacturer']['index']]] : 0,
							'product_category' => array(),
							'product_filter' => array(),
							'product_store' => !empty($pattern['Store']['index']) && !empty($pro[$pattern['Store']['index']]) ? explode('|', $pro[$pattern['Store']['index']]) : array(0),
							'download' => '',
							'related' => '',
							'option' => '',
							'points' => !empty($pattern['Points']['index']) && !empty($pro[$pattern['Points']['index']]) ? $pro[$pattern['Points']['index']] : 0,
							'product_reward' => array(),
							'product_attribute' => array(),
							'product_layout' => array(),
							'product_image' => array(),
							'product_special' => array(),
							'product_discount' => array(),
						);
						
						//Add extra product fields here

						//If editting unset all index that hasn't columns
							if($editting)
							{
								if(!isset($pattern['SKU']['created'])) unset($temp['sku']);
								if(!isset($pattern['UPC']['created'])) unset($temp['upc']);
								if(!isset($pattern['EAN']['created'])) unset($temp['ean']);
								if(!isset($pattern['JAN']['created'])) unset($temp['jan']);
								if(!isset($pattern['ISBN']['created'])) unset($temp['isbn']);
								if(!isset($pattern['MPN']['created'])) unset($temp['mpn']);
								if(!isset($pattern['Subtract']['created'])) unset($temp['subtract']);
								if(!isset($pattern['Location']['created'])) unset($temp['location']);
								if(!isset($pattern['Price']['created'])) unset($temp['price']);
								if(!isset($pattern['Tax class']['created'])) unset($temp['tax_class_id']);
								if(!isset($pattern['Quantity']['created'])) unset($temp['quantity']);
								if(!isset($pattern['Minimum']['created'])) unset($temp['minimum']);
								if(!isset($pattern['Out stock status']['created'])) unset($temp['stock_status_id']);
								if(!isset($pattern['Requires shipping']['created'])) unset($temp['shipping']);
								if(!isset($pattern['SEO url']['created'])) unset($temp['keyword']);
								if(!isset($pattern['Main image']['created'])) unset($temp['image']);
								if(!isset($pattern['Date available']['created'])) unset($temp['date_available']);
								if(!isset($pattern['Length']['created'])) unset($temp['length']);
								if(!isset($pattern['Width']['created'])) unset($temp['width']);
								if(!isset($pattern['Height']['created'])) unset($temp['height']);
								if(!isset($pattern['Weight']['created'])) unset($temp['weight']);
								if(!isset($pattern['Class length']['created'])) unset($temp['length_class_id']);
								if(!isset($pattern['Class weight']['created'])) unset($temp['weight_class_id']);
								if(!isset($pattern['Status']['created'])) unset($temp['status']);
								if(!isset($pattern['Sort order']['created'])) unset($temp['sort_order']);
								if(!isset($pattern['Manufacturer']['created'])) unset($temp['manufacturer_id']);
								if(!isset($pattern['Store']['created'])) unset($temp['product_store']);
								if(!isset($pattern['Points']['created'])) unset($temp['points']);
							}
						//END If editting unset all index that hasn't columns

						//Fix to values 0/empty
							$indexs = array();

						//Save this variables to options
							$first_price = !empty($temp['price']) ? $temp['price'] : 0;
							$first_points = !empty($temp['points']) ? $temp['points'] : 0;
							$first_weight = !empty($temp['weight']) ? $temp['weight'] : 0;

						//Categories
							$array_categories = array('Cat. level 1', '> Cat. level 2', '> Cat. level 3', '> Cat. level 4', '> Cat. level 5');
							$last_category_id = '';
							foreach ($array_categories as $key => $col_name) {
								if(!empty($pattern[$col_name]['index']) && !empty($pro[$pattern[$col_name]['index']]))
								{
									if($key == 0)
									{
										$parent_tree = $this->get_category_parent_tree($all_categories, $pro[$pattern[$col_name]['index']]);

										if(!$this->config->get('import_xls_categories_last_tree'))
											$temp['product_category'][] = $parent_tree['category_id'];

										$last_category_id = $parent_tree['category_id'];

										$parent_tree = !empty($parent_tree['childrens']) ? $parent_tree['childrens'] : array();
									}
									else
									{
										$parent_tree = $this->get_category_parent_tree($parent_tree, $pro[$pattern[$col_name]['index']]);

										if(!$this->config->get('import_xls_categories_last_tree'))
											$temp['product_category'][] = $parent_tree['category_id'];

										$last_category_id = $parent_tree['category_id'];

										$parent_tree = !empty($parent_tree['childrens']) ? $parent_tree['childrens'] : array();
									}
								}
							}

							if($this->config->get('import_xls_categories_last_tree') && !empty($last_category_id))
								$temp['product_category'][] = $last_category_id;

						//Descriptions
							foreach ($all_languages as $key => $lng) {
								if($this->count_languages > 1)
								{
									$name = !empty($pattern['*Name '.$lng['code']]) && !empty($pro[$pattern['*Name '.$lng['code']]['index']]) ? $pro[$pattern['*Name '.$lng['code']]['index']] : '';

									$description = !empty($pattern['Description '.$lng['code']]) && !empty($pro[$pattern['Description '.$lng['code']]['index']]) ? $pro[$pattern['Description '.$lng['code']]['index']] : '';

									$meta_description = !empty($pattern['Meta description '.$lng['code']]) && !empty($pro[$pattern['Meta description '.$lng['code']]['index']]) ? $pro[$pattern['Meta description '.$lng['code']]['index']] : '';

									$meta_title = !empty($pattern['Meta title '.$lng['code']]) && !empty($pro[$pattern['Meta title '.$lng['code']]['index']]) ? $pro[$pattern['Meta title '.$lng['code']]['index']] : $name;

									$meta_keyword = !empty($pattern['Meta keywords '.$lng['code']]) && !empty($pro[$pattern['Meta keywords '.$lng['code']]['index']]) ? $pro[$pattern['Meta keywords '.$lng['code']]['index']] : '';

									$tags = !empty($pattern['Tags '.$lng['code']]) && !empty($pro[$pattern['Tags '.$lng['code']]['index']]) ? $pro[$pattern['Tags '.$lng['code']]['index']] : '';
								}
								else
								{
									$name = !empty($pattern['*Name']) && !empty($pro[$pattern['*Name']['index']]) ? $pro[$pattern['*Name']['index']] : '';

									$description = !empty($pattern['Description']) && !empty($pro[$pattern['Description']['index']]) ? $pro[$pattern['Description']['index']] : '';

									$meta_description = !empty($pattern['Meta description']) && !empty($pro[$pattern['Meta description']['index']]) ? $pro[$pattern['Meta description']['index']] : '';

									$meta_title = !empty($pattern['Meta title']['index']) && !empty($pro[$pattern['Meta title']['index']]) ? $pro[$pattern['Meta title']['index']] : $name;

									$meta_keyword = !empty($pattern['Meta keywords']['index']) && !empty($pro[$pattern['Meta keywords']['index']]) ? $pro[$pattern['Meta keywords']['index']] : '';

									$tags = !empty($pattern['Tags']) && !empty($pro[$pattern['Tags']['index']]) ? $pro[$pattern['Tags']['index']] : '';
								}
								$temp['product_description'][$lng['language_id']] = array(
									'name' => $name,
				                    'meta_description' => $meta_description,
				                    'meta_title' => $meta_title,
				                    'meta_keyword' => $meta_keyword,
				                    'description' => $description,
				                    'tag' => $tags,
								);
							}

							if($editting)
							{
								foreach ($temp['product_description'] as $language_id => $desc) {
									if(empty($pattern['Meta description']['created'])) unset($temp['product_description'][$language_id]['meta_description']);
									if(empty($pattern['Meta title']['created'])) unset($temp['product_description'][$language_id]['meta_title']);
									if(empty($pattern['Meta keywords']['created'])) unset($temp['product_description'][$language_id]['meta_keyword']);
									if(empty($pattern['Description']['created'])) unset($temp['product_description'][$language_id]['description']);
									if(empty($pattern['Tags']['created'])) unset($temp['product_description'][$language_id]['tag']);
								}
							}
							
						//Filters
							for ($i=1; $i <= $this->filterGroupNumber; $i++) {
								$index_filter_group = !empty($pattern['Filter Group '.$i]['index']) ? $pattern['Filter Group '.$i]['index'] : '';
								if(!empty($index_filter_group) && !empty($pro[$index_filter_group]))
								{
									for ($j=1; $j < $this->filterGroupFilterNumber; $j++) { 
										$index_filter = !empty($pattern['Filter Gr.'.$i.' filter '.$j]['index']) ? $pattern['Filter Gr.'.$i.' filter '.$j]['index'] : '';
										if(!empty($index_filter) && !empty($pro[$index_filter]))
											$temp['product_filter'][] = $all_filters[$pro[$index_filter_group]]['filters'][$pro[$index_filter]];
									}
								}
							}

						//Layouts
							if(!empty($pattern['Layout']['index']) && !empty($pro[$pattern['Layout']['index']]))
							{
								$temp['product_layout'][0] = $pro[$pattern['Layout']['index']];
								foreach ($all_stores as $key => $store) {
									$temp['product_layout'][$store['store_id']] = $pro[$pattern['Layout']['index']];
								}
							}

						//Reward points
							foreach ($all_customer_groups as $key => $cg) {
								$points = 0;

								if(!empty($pattern['Points '.$cg['name']]['index']))
									$points = !empty($pro[$pattern['Points '.$cg['name']]['index']]) ? $pro[$pattern['Points '.$cg['name']]['index']] : 0;

								$temp['product_reward'][$cg['customer_group_id']]['points'] = $points;
							}
							
							//if($editting && !isset($pattern['Points']['created'])) unset($temp['product_reward']);

						//Images
							for ($i=2; $i <= 5; $i++) {
								if(!empty($pattern['Image '.$i]['index']) && !empty($pro[$pattern['Image '.$i]['index']]))
								{
									$temp['product_image'][] = array(
										'image' => $pro[$pattern['Image '.$i]['index']],
										'sort_order' => $i-2
									);
								}
							}

						//Special
							if(!empty($pattern['Spe. Price']['index']) && !empty($pro[$pattern['Spe. Price']['index']]))
							{
								$temp['product_special'][] = array(
									'customer_group_id' => !empty($pattern['Spe. Customer Group']['index']) && !empty($pro[$pattern['Spe. Customer Group']['index']]) ? $pro[$pattern['Spe. Customer Group']['index']] : 1,
									'priority' => !empty($pattern['Spe. Priority']['index']) && !empty($pro[$pattern['Spe. Priority']['index']]) ? $pro[$pattern['Spe. Priority']['index']] : 0,
									'price' => $pro[$pattern['Spe. Price']['index']],
									'date_start' => !empty($pattern['Spe. Date start']['index']) && !empty($pro[$pattern['Spe. Date start']['index']]) ? date('Y-m-d', strtotime(str_replace('/', '-', $pro[$pattern['Spe. Date start']['index']]))) : '',
									'date_end' => !empty($pattern['Spe. Date end']['index']) && !empty($pro[$pattern['Spe. Date end']['index']]) ? date('Y-m-d', strtotime(str_replace('/', '-', $pro[$pattern['Spe. Date end']['index']]))) : '',
								);
							}
						//END Special

						//Discount
							for ($i=1; $i <= 3; $i++) { 
								if(!empty($pattern['Dis. Price '.$i]['index']) && !empty($pro[$pattern['Dis. Price '.$i]['index']]) && !empty($pattern['Dis. Quantity '.$i]['index']) && !empty($pro[$pattern['Dis. Quantity '.$i]['index']]))
								{
									$temp['product_discount'][] = array(
										'customer_group_id' => !empty($pattern['Dis. Customer Group '.$i]['index']) && !empty($pro[$pattern['Dis. Customer Group '.$i]['index']]) ? $pro[$pattern['Dis. Customer Group '.$i]['index']] : 1,
										'quantity' => !empty($pattern['Dis. Quantity '.$i]['index']) && !empty($pro[$pattern['Dis. Quantity '.$i]['index']]) ? $pro[$pattern['Dis. Quantity '.$i]['index']] : 1,
										'priority' => !empty($pattern['Dis. Priority '.$i]['index']) && !empty($pro[$pattern['Dis. Priority '.$i]['index']]) ? $pro[$pattern['Dis. Priority '.$i]['index']] : 0,
										'price' => $pro[$pattern['Dis. Price '.$i]['index']],
										'date_start' => !empty($pattern['Dis. Date start '.$i]['index']) && !empty($pro[$pattern['Dis. Date start '.$i]['index']]) ? date('Y-m-d', strtotime(str_replace('/', '-', $pro[$pattern['Dis. Date start '.$i]['index']]))) : '',
										'date_end' => !empty($pattern['Dis. Date end '.$i]['index']) && !empty($pro[$pattern['Dis. Date end '.$i]['index']]) ? date('Y-m-d', strtotime(str_replace('/', '-', $pro[$pattern['Dis. Date end '.$i]['index']]))) : '',
									);
								}
							}
						//END Discount

						//Attributes
							for ($i=1; $i <= $this->attributeNumber; $i++) {

								if($this->count_languages == 1)
								{
									$attr_group_index = 'Attr. Group '.$i;
									$attr_index = 'Attribute '.$i;
									$attr_value_index = 'Attribute value '.$i;
								}
								else
								{
									$attr_group_index = 'Attr. Group '.$i.' '.$this->default_language_code;
									$attr_index = 'Attribute '.$i.' '.$this->default_language_code;
									$attr_value_index = 'Attribute value '.$i.' '.$this->default_language_code;
								}

								if(
									!empty($pattern[$attr_group_index]['index']) && !empty($pro[$pattern[$attr_group_index]['index']])
									&&
									!empty($pattern[$attr_index]['index']) && !empty($pro[$pattern[$attr_index]['index']])
									&&
									!empty($pattern[$attr_value_index]['index']) && !empty($pro[$pattern[$attr_value_index]['index']])
								)
								{
									$attribute_name = !empty($pro[$pattern[$attr_index]['index']]) ? $pro[$pattern[$attr_index]['index']] : '';
									$attribute_id = $all_attributes[$pro[$pattern[$attr_group_index]['index']]]['attributes'][$pro[$pattern[$attr_index]['index']]];
									$attribute_description = array();

									foreach ($this->languages as $key => $lng) {
										if($this->count_languages > 1)
											$attr_value_index = 'Attribute value '.$i.' '.$lng['code'];

										$attribute_description[$lng['language_id']] = array(
											'text' => !empty($pro[$pattern[$attr_value_index]['index']]) ? $pro[$pattern[$attr_value_index]['index']] : '',
										);
									}

									$temp['product_attribute'][] = array(
										'name' => $attribute_name,
										'attribute_id' => $attribute_id,
										'product_attribute_description' => $attribute_description
									);
								}
							}
					}

					if(!empty($pattern['Quantity']['index']) && !empty($pro[$pattern['Quantity']['index']]))
						$quantity += $pro[$pattern['Quantity']['index']];
					else
						$quantity = 0;

					//Options
						$column_option = 'Option'.($this->count_languages > 1 ? ' '.$this->default_language_code : '');
						$column_option_value = 'Option value'.($this->count_languages > 1 ? ' '.$this->default_language_code : '');

						$index_option = !empty($pattern[$column_option]['index']) ? $pattern[$column_option]['index'] : '';
						$option_type = !empty($pattern['Option type']['index']) && !empty($pro[$pattern['Option type']['index']]) ? $pro[$pattern['Option type']['index']] : 'select';
						$index_option_value = !empty($pattern[$column_option_value]['index']) ? $pattern[$column_option_value]['index'] : '';

						if(
							!empty($index_option) && !empty($index_option_value) &&
							!empty($pro[$index_option]) && !empty($pro[$index_option_value])
						)
						{		
							//Calc price
								$current_price = !empty($pattern['Price']['index']) && !empty($pro[$pattern['Price']['index']]) ? $pro[$pattern['Price']['index']] : 0;
								
								if(!empty($pattern['Option price prefix']['index']) && !empty($pro[$pattern['Option price prefix']['index']]))
								{
									$price_symbol = $pro[$pattern['Option price prefix']['index']];
									$price_total = $current_price;
								}
								else
								{
									if($current_price == 0)
										$price_symbol = '+';
									else
										$price_symbol = $first_price > $current_price ? '-':'+';

									if($current_price == 0)
										$price_total = 0;
									else
										$price_total = $first_price > $current_price ? $first_price - $current_price : $current_price - $first_price;
								}
							//END Calc price

							//Calc points
								$current_points = !empty($pattern['Points']['index']) && !empty($pro[$pattern['Points']['index']]) ? $pro[$pattern['Points']['index']] : 0;
								
								if(!empty($pattern['Option points prefix']['index']) && !empty($pro[$pattern['Option points prefix']['index']]))
								{
									$points_symbol = $pro[$pattern['Option points prefix']['index']];
									$points_total = $current_points;
								}
								else
								{
									if($current_points == 0)
										$points_symbol = '+';
									else
										$points_symbol = $first_points > $current_points ? '-':'+';

									if($current_points == 0)
										$points_total = 0;
									else
										$points_total = $first_points > $current_points ? $first_points - $current_points : $current_points - $first_points;
								}
							//END Calc points

							//Calc weight
								$current_weight = !empty($pattern['Weight']['index']) && !empty($pro[$pattern['Weight']['index']]) ? $pro[$pattern['Weight']['index']] : 0;
								
								if(!empty($pattern['Option weight prefix']['index']) && !empty($pro[$pattern['Option weight prefix']['index']]))
								{
									$weight_symbol = $pro[$pattern['Option weight prefix']['index']];
									$weight_total = $current_weight;
								}
								else
								{
									if($current_weight == 0)
										$weight_symbol = '+';
									else
										$weight_symbol = $first_weight > $current_weight ? '-':'+';

									if($current_weight == 0)
										$weight_total = 0;
									else
										$weight_total = $first_weight > $current_weight ? $first_weight - $current_weight : $current_weight - $first_weight;
								}
							//END Calc weight

							$option_id = $all_options[$pro[$index_option].'_'.$option_type]['option_id'];
							$option_value_id = $all_options[$pro[$index_option].'_'.$option_type]['option_values'][$pro[$index_option_value]];

							if(!isset($final_options[$option_id]))
							{
								$option_required = 1;
								if(!empty($pattern['Option required']['index']) && isset($pro[$pattern['Option required']['index']]) && $pro[$pattern['Option required']['index']] === '0') $option_required = 0;

								$final_options[$option_id] = array(
									'option_id' => $option_id,
									'type' => $option_type,
									'required' => $option_required,
									'product_option_value' => array()
								);
							}

							$option_val_subtract = 1;
							if(!empty($pattern['Option subtract']['index']) && isset($pro[$pattern['Option subtract']['index']]) && $pro[$pattern['Option subtract']['index']] === '0') $option_val_subtract = 0;

							if(!empty($pro[$pattern['Option type']['index']]) && $pro[$pattern['Option type']['index']] != 'text')
							{
								$temp_option_value = array(
									'option_value_id' => $option_value_id,
	                                'product_option_value_id' => null,
	                                'quantity' => !empty($pattern['Quantity']['index']) && !empty($pro[$pattern['Quantity']['index']]) ? $pro[$pattern['Quantity']['index']] : 1,
	                                'subtract' => $option_val_subtract,
	                                'price_prefix' => $price_symbol,
	                                'price' => $price_total,
	                                'points_prefix' => $points_symbol,
	                                'points' => $points_total,
	                                'weight_prefix' => $weight_symbol,
	                                'weight' => $weight_total,
	                                'ob_sku' => !empty($pattern['Option SKU (Options Boost)']['index']) && !empty($pro[$pattern['Option SKU (Options Boost)']['index']]) ? $pro[$pattern['Option SKU (Options Boost)']['index']] : '',
									'ob_image' => !empty($pattern['Option image (Options Boost)']['index']) && !empty($pro[$pattern['Option image (Options Boost)']['index']]) ? $pro[$pattern['Option image (Options Boost)']['index']] : '',
									'uo_sku' => !empty($pattern['Option SKU (Options Boost)']['index']) && !empty($pro[$pattern['Option SKU (Options Boost)']['index']]) ? $pro[$pattern['Option SKU (Options Boost)']['index']] : '',
									'uo_swap_image' => !empty($pattern['Option image (Options Boost)']['index']) && !empty($pro[$pattern['Option image (Options Boost)']['index']]) ? $pro[$pattern['Option image (Options Boost)']['index']] : '',
								);

								$final_options[$option_id]['product_option_value'][] = $temp_option_value;
							}
							else
							{
								unset($final_options[$option_id]['product_option_value']);
								if($this->count_languages == 1)
								{
									$final_options[$option_id]['value'] = !empty($pro[$pattern['Option value']['index']]) ? !empty($pro[$pattern['Option value']['index']]) : '';
								}
								else
								{
									$final_options[$option_id]['value'] = !empty($pro[$pattern['Option value '.$this->default_language_code]['index']]) ? $pro[$pattern['Option value '.$this->default_language_code]['index']] : '';
								}
							}
						}
					//END Options

					$count_products++;
				}

				if(!empty($final_options))
				{
					foreach ($final_options as $key => $opt) {
						$temp['product_option'][] = $opt;
					}
				}

				if($editting && !$pattern['Quantity']['created'])
					unset($temp['quantity']);
				else
					$temp['quantity'] = $quantity;

				$final_products[] = $temp;
			}

			return $final_products;
		}

		function remove_duplicate($key,$data)
		{
			$_data = array();
			foreach ($data as $v) {
				if (isset($_data[$v[$key]])) {
					continue;
				}
				$_data[$v[$key]] = $v;
			}
			$data = array_values($_data);
			return $data;
		}

		function editProduct($product_id, $data, $all_languages, $option_boost_installed)
		{
			//Update basic datas
				$sql = "UPDATE " . DB_PREFIX . "product SET ";

					$basic_datas = array(
						'model','sku', 'upc','jan','ean','isbn','mpn','location','quantity',
						'minimum','subtract','stock_status_id','date_available',
						'manufacturer_id','shipping','price','points','weight',
						'weight_class_id','length','width','height','length_class_id',
						'status','tax_class_id','sort_order', 'image'
					);

					foreach ($basic_datas as $key => $value) {
						if(isset($data[$value]))
							$sql .= $value." = '" . $this->db->escape($data[$value])."', ";
					}
					$sql .= 'date_modified = NOW() ';
				$sql .= "WHERE product_id = '" . (int)$product_id . "'";

				$this->db->query($sql);
			//END Update basic datas

			//Product language
				$language_datas = array('name','meta_description','meta_keyword','description','tag');

				if(version_compare(VERSION, '2.0.0.0', '>='))
					$language_datas[] = 'meta_title';
				
				foreach ($data['product_description'] as $language_id => $value) {
					$sql = "UPDATE " . DB_PREFIX . "product_description SET ";
						foreach ($language_datas as $key => $value) {
							if(isset($data['product_description'][$language_id][$value]))
								$sql .= $value." = '" . $this->db->escape($data['product_description'][$language_id][$value])."', ";
						}
					$sql = substr($sql, 0, -2).' ';
					$sql .= "WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'";
					$this->db->query($sql);
				}
			//END Product language

			//Product to store
				if(!empty($data['product_store']))
				{
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

					if (isset($data['product_store'])) {
						foreach ($data['product_store'] as $store_id) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
						}
					}
				}
			//END Product to store

			//Product Attributes
				if(!empty($data['product_attribute']))
				{
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
					foreach ($data['product_attribute'] as $key => $attribute)
					{
						foreach ($attribute['product_attribute_description'] as $language_id => $attrdescrip)
						{
							//Exist this attribute?
							$sql = "SELECT `text` FROM " . DB_PREFIX . "product_attribute ";
							$sql .= "WHERE attribute_id = '" . (int)$attribute['attribute_id'] . "' AND product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'";

							$results = $this->db->query($sql);

							if(!empty($results->row['text']))
							{
								//Update product attribute
								$sql = "UPDATE " . DB_PREFIX . "product_attribute SET text = '" . $this->db->escape($attrdescrip['text'])."' ";
								$sql .= "WHERE attribute_id = '" . (int)$attribute['attribute_id'] . "' AND product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'";
							}
							else
								//Insert product attribute
								$sql = "INSERT INTO " . DB_PREFIX . "product_attribute SET text = '" . $this->db->escape($attrdescrip['text'])."', attribute_id = '" . (int)$attribute['attribute_id'] . "', product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "'";

							$this->db->query($sql);
						}
					}
				}
			//END Product Attributes

			//Product Options
				//First delete options and option values
					$sql = "DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'";
					$this->db->query($sql);
					$sql = "DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'";
					$this->db->query($sql);
				//END First delete options and option values

				if(!empty($data['product_option']))
				{
					$pov_fields = array('quantity','subtract','price_prefix','price','points_prefix','points','weight_prefix','weight','ob_sku','ob_image');

					foreach ($data['product_option'] as $key => $option) {
						//PRODUCT OPTION
							if(version_compare(VERSION, '2.0.0.0', '<'))
								$name_column = 'option_value';
							else
								$name_column = 'value';

							$value = $option['type'] == 'text' ? $option['value'] : '';

							$sql = "INSERT INTO " . DB_PREFIX . "product_option SET ";
							$sql .=  "option_id = '" . (int)$option['option_id'] . "', ";
							$sql .=  "product_id = '" . (int)$product_id . "', ";
							$sql .=  $name_column." = '".$value."', ";
							$sql .=  "required = '" . $this->db->escape($option['required']) . "'";

							$this->db->query($sql);
							
							$product_option_id = $this->db->getLastId();
						//END PRODUCT OPTION

						//PRODUCT OPTION VALUES
							foreach ($option['product_option_value'] as $key2 => $option_value) {
								$sql = "INSERT INTO " . DB_PREFIX . "product_option_value SET ";
								$sql .= "product_option_id = '" . (int)$product_option_id . "', ";
								$sql .= "product_id = '" . (int)$product_id . "', ";
								$sql .= "option_id = '" . (int)$option['option_id'] . "', ";
								$sql .= "option_value_id = '" . (int)$option_value['option_value_id'] . "', ";
								foreach ($pov_fields as $key3 => $field) {
									if($option_boost_installed || (!$option_boost_installed && $field != "ob_image" && $field != "ob_sku"))
									{
										if($option_value[$field] !== '')
											$sql .= $field." = '" . $this->db->escape($option_value[$field])."', ";
										else
											$sql .= $field." = '', ";
									}
								}
								$sql = substr($sql, 0, -2);
								$this->db->query($sql);
							}
						//END PRODUCT OPTION VALUES
					}
				}
			//END Product Options

			//Product Discounts
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

				if (isset($data['product_discount'])) {
					foreach ($data['product_discount'] as $product_discount) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
					}
				}
			//END Product Discounts

			//Product Special
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

				if (isset($data['product_special'])) {
					foreach ($data['product_special'] as $product_special) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
					}
				}
			//END Product Special
			
			//Product Images - WILL BE ADDED IF NOT EXIST
				if (!empty($data['product_image'])) {
					foreach ($data['product_image'] as $product_image) {
						$sql = "SELECT `product_image_id` FROM " . DB_PREFIX . "product_image WHERE ";
						$sql .= "product_id = '" . (int)$product_id . "' AND ";
						$sql .= "image = '" . $product_image['image'] . "'";
						
						$results = $this->db->query($sql);
						if(empty($results->row['product_image_id']))
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
					}
				}
			//Product Images - WILL BE ADDED IF NOT EXIST

			//Product Category - WILL BE ADDED IF NOT EXIST
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

				if(!empty($data['product_category']))
				{
					foreach ($data['product_category'] as $category_id) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
					}
				}
			//END Product Category - WILL BE ADDED IF NOT EXIST

			//Product filter - WILL BE ADDED IF NOT EXIST
				if (!empty($data['product_filter']))
				{
					foreach ($data['product_filter'] as $filter_id) {
						$sql = "SELECT `product_id` FROM " . DB_PREFIX . "product_filter WHERE ";
						$sql .= "product_id = '" . (int)$product_id . "' AND ";
						$sql .= "filter_id = '" . (int)$filter_id . "'";
						
						$results = $this->db->query($sql);
						if(empty($results->row['product_id']))
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
					}
				}
			//END Product filter - WILL BE ADDED IF NOT EXIST

			//Product related
				/*
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

				if (isset($data['product_related'])) {
					foreach ($data['product_related'] as $related_id) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
					}
				}
				*/
			//END Product related

			//Product reward - WILL BE ADDED IF NOT EXIST
				if (!empty($data['product_reward']))
				{
					foreach ($data['product_reward'] as $customer_group_id => $points) {
						$sql = "SELECT `product_id` FROM " . DB_PREFIX . "product_reward WHERE ";
						$sql .= "product_id = '" . (int)$product_id . "' AND ";
						$sql .= "points = '" . (int)$points['points'] . "' AND ";
						$sql .= "customer_group_id = '" . (int)$customer_group_id . "'";

						$results = $this->db->query($sql);
						if(empty($results->row['product_id']))
						{
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "'");
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$points['points'] . "'");
						}
					}
				}
			//END Product reward - WILL BE ADDED IF NOT EXIST

			//Product layout - WILL BE ADDED IF NOT EXIST
				if (!empty($data['product_layout']))
				{
					foreach ($data['product_layout'] as $store_id => $layout_id) {
						$sql = "SELECT `product_id` FROM " . DB_PREFIX . "product_to_layout WHERE ";
						$sql .= "product_id = '" . (int)$product_id . "' AND ";
						$sql .= "store_id = '" . (int)$store_id . "' AND ";
						$sql .= "layout_id = '" . (int)$layout_id . "'";

						$results = $this->db->query($sql);
						if(empty($results->row['product_id']))
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
					}
				}
			//END Product layout - WILL BE ADDED IF NOT EXIST

			//Product Keyword
				if(!empty($data['keyword']))
				{
					$sql = "SELECT `url_alias_id` FROM " . DB_PREFIX . "url_alias WHERE ";
					$sql .= "query = 'product_id=" . (int)$product_id . "' AND ";
					$sql .= "keyword = '" . $this->db->escape($data['keyword']) . "'";

					$results = $this->db->query($sql);
					if(empty($results->row['url_alias_id']))
					{
						$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");
						$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
					}
				}
			//END Product Keyword
		}

		function download_remote_image($model, $image_url, $image_name)
		{
			$array_return = array('error' => false, 'message' => '');

			$downloaded_image = copy($image_url, DIR_IMAGE.$image_name); 
			if(!$downloaded_image)
			{
				$array_return['error'] = true;
				$array_return['message'] = sprintf($this->language->get('error_downloading_image'), $image_url, $model);
			}

			return $array_return;
		}

		/*
		PHP ERRORS WITH VARIABLE -> [[$children => []]];
		function buildTree($array, $id = 'category_id', $parent_id = 'parent_id', $children = 'childrens') {
			$tree = [[$children => []]];

			$references = [&$tree[0]];

			foreach($array as $item) {
				if(isset($references[$item[$id]])) {
					$item[$children] = $references[$item[$id]][$children];
				}

				$references[$item[$parent_id]][$children][] = $item;

				$references[$item[$id]] =& $references[$item[$parent_id]][$children][count($references[$item[$parent_id]][$children]) - 1];
			}

			return $tree[0][$children];
		}*/

		function buildTree(array $data) {
			$tree = array();
		    foreach($data as &$v){
				// Get childs
				if(isset($tree[$v['category_id']])) $v['childrens'] =& $tree[$v['category_id']];
				// push node into parent
				$tree[$v['parent_id']][$v['category_id']] =& $v;

				// push childrens into node
				$tree[$v['category_id']] =& $v['childrens'];
			}

			// return Tree
			if(!empty($tree[0]))
				return $this->array_values_recursive($tree[0]);
			else
				return array();
		}

		function array_values_recursive( $array ) {
		    $newarray = array();
		    if(!empty($array))
		    {
				foreach ($array as $value) {
			        $value["childrens"] = $this->array_values_recursive($value["childrens"]);
			        $newarray[] = $value;
				}
			}
			return $newarray;
		}

		public function get_category_tree($categories, $name = '')
		{
			foreach ($categories as $key => $cat) {
				if($cat['name'] == $name)
					return $cat;
			}

			return false;
		}
	//END OTHERS FUNCTIONS

	//CUSTOMER GROUP FUNCTION (AFTER OC 2.0.3.1 MODULE SALE/CUSTOMER_GROUP IS DELETED )
		public function getCustomerGroups($data = array()) {
			$sql = "SELECT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cgd.name ASC";
			$query = $this->db->query($sql);

			return $query->rows;
		}
	//END CUSTOMER GROUP FUNCTION (AFTER OC 2.0.3.1 MODULE SALE/CUSTOMER_GROUP IS DELETED )
}
?>