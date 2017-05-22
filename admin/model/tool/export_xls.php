<?php
class ModelToolExportXls extends Model {

	public function __construct($registry) {
        parent::__construct($registry);

        $loader = new Loader($registry);
		$loader->model('tool/import_xls');
	}

	public function format_products($product, $all_options, $all_attributes, $all_filters, $all_manufacturers, $all_categories)
	{
		$general_data = array();
		$final_products = array();
		
		//Model
			$general_data['model'] = !empty($product['model']) ? htmlspecialchars_decode($product['model']) : '';
		//Name
			$general_data['name'] = !empty($product['name']) ? htmlspecialchars_decode($product['name']) : '';
		//Description
			$general_data['description'] = !empty($product['description']) ? htmlspecialchars_decode($product['description']) : '';
		//Meta description
			$general_data['meta_description'] = !empty($product['meta_description']) ? htmlspecialchars_decode($product['meta_description']) : '';
		//Meta title
			$general_data['meta_title'] = !empty($product['meta_title']) ? htmlspecialchars_decode($product['meta_title']) : '';
		//Meta keywords
			$general_data['meta_keyword'] = !empty($product['meta_keyword']) ? htmlspecialchars_decode($product['meta_keyword']) : '';
		//SEO url
			$general_data['keyword'] = !empty($product['keyword']) ? htmlspecialchars_decode($product['keyword']) : '';
		//Tags
			$general_data['tag'] = !empty($product['tag']) ? htmlspecialchars_decode($product['tag']) : '';
		//SKU
			$general_data['sku'] = !empty($product['sku']) ? htmlspecialchars_decode($product['sku']) : '';
		//EAN
			$general_data['ean'] = !empty($product['ean']) ? htmlspecialchars_decode($product['ean']) : '';
		//UPC
			$general_data['upc'] = !empty($product['upc']) ? htmlspecialchars_decode($product['upc']) : '';
		//JAN
			$general_data['jan'] = !empty($product['jan']) ? htmlspecialchars_decode($product['jan']) : '';
		//MPN
			$general_data['mpn'] = !empty($product['mpn']) ? htmlspecialchars_decode($product['mpn']) : '';
		//ISBN
			$general_data['isbn'] = !empty($product['isbn']) ? htmlspecialchars_decode($product['isbn']) : '';
		//Quantity
			$general_data['quantity'] = !empty($product['quantity']) ? $product['quantity'] : 0;
		//Minimum
			$general_data['minimum'] = !empty($product['minimum']) ? $product['minimum'] : 0;
		//Subtract
			$general_data['subtract'] = !empty($product['subtract']) ? $product['subtract'] : 0;
		//Out stock status
			$general_data['stock_status_id'] = !empty($product['stock_status_id']) ? $product['stock_status_id'] : 5;
		//Price
			$general_data['price'] = !empty($product['price']) ? $product['price'] : 0;
		//Special Customer Group Id
			$general_data['special_customer_group_id'] = '';
		//Special Priority
			$general_data['special_priority'] = '';
		//Special Price
			$general_data['special_price'] = '';
		//Special Date start
			$general_data['special_date_start'] = '';
		//Special Date end
			$general_data['special_date_end'] = '';

		//Add extra product fields here

		//Multilanguage description
	        if($this->count_languages > 1)
	        {
				foreach ($this->languages as $key => $lang) {
					$temporal_sql = "SELECT * FROM `" . DB_PREFIX . "product_description` WHERE language_id = ".$lang['language_id']." AND product_id = ".$product['product_id'].";";
					$descriptions = $this->db->query($temporal_sql);

					$general_data['name_'.$lang['code']] = !empty($descriptions->row['name']) ? $descriptions->row['name'] : '';
					$general_data['description_'.$lang['code']] = !empty($descriptions->row['description']) ? $descriptions->row['description'] : '';
					$general_data['meta_description_'.$lang['code']] = !empty($descriptions->row['meta_description']) ? $descriptions->row['meta_description'] : '';
					$general_data['meta_keyword_'.$lang['code']] = !empty($descriptions->row['meta_keyword']) ? $descriptions->row['meta_keyword'] : '';
					$general_data['tag_'.$lang['code']] = !empty($descriptions->row['tag']) ? $descriptions->row['tag'] : '';

					if(version_compare(VERSION, '2.0.0.0', '>='))
						$general_data['meta_title_'.$lang['code']] = !empty($descriptions->row['meta_title']) ? $descriptions->row['meta_title'] : '';
				}
			}
		//END Multilanguage description

		//Special
			if (!empty($product['product_special']))
			{
				//GET ONLY LAST SPECIAL, IN EXCEL ONLY CAN 1 SPECIAL
					$special = $product['product_special'][count($product['product_special'])-1];

					$general_data['special_customer_group_id'] = $special['customer_group_id'];
					$general_data['special_priority'] = $special['priority'];
					$general_data['special_price'] = $special['price'];
					$general_data['special_date_start'] = $special['date_start'];
					$general_data['special_date_end'] = $special['date_end'];
			}

		//Discount
			for ($i=1; $i <= 3 ; $i++) { 
				$general_data['discount_customer_group_id_'.$i] = '';
				$general_data['discount_quantity_'.$i] = '';
				$general_data['discount_priority_'.$i] = '';
				$general_data['discount_price_'.$i] = '';
				$general_data['discount_date_start_'.$i] = '';
				$general_data['discount_date_end_'.$i] = '';
			}

			if(!empty($product['product_discount']))
			{
				foreach ($product['product_discount'] as $key => $pd) {
					$general_data['discount_customer_group_id_'.($key+1)] = $pd['customer_group_id'];
					$general_data['discount_quantity_'.($key+1)] = $pd['quantity'];
					$general_data['discount_priority_'.($key+1)] = $pd['priority'];
					$general_data['discount_price_'.($key+1)] = $pd['price'];
					$general_data['discount_date_start_'.($key+1)] = $pd['date_start'];
					$general_data['discount_date_end_'.($key+1)] = $pd['date_end'];

					if(($key+1) >= 3)
						break;
				}
			}

		//Manufacturer
			$general_data['manufacturer'] = !empty($all_manufacturers[$product['manufacturer_id']]) ? $all_manufacturers[$product['manufacturer_id']]: '';

		//Main Category - Category 2- Category 3- Category 4- Category 5
			$general_data['category_1'] = '';
			$general_data['category_2'] = '';
			$general_data['category_3'] = '';
			$general_data['category_4'] = '';
			$general_data['category_5'] = '';

		if(!empty($product['product_categories']))
		{
			if(!$this->config->get('import_xls_categories_last_tree'))
			{
				$count_categories_assigns = 1;
				foreach ($product['product_categories'] as $key => $cat_id) {
					$general_data['category_'.($key+1)] = !empty($all_categories[$cat_id]) ? $all_categories[$cat_id] : '';

					if(($key+1) == $this->categoryNumber)
						break;
				}
			}
			else
			{
				$category_tree = $this->get_tree_categories($product['product_categories']);

				if(!empty($category_tree))
				{
					$category_tree = $category_tree[0];

					for ($i=1; $i <= $this->categoryNumber; $i++) { 
						
						$general_data['category_'.$i] = $category_tree['name'];

						if(!empty($category_tree['childrens'][0]))
							$category_tree = $category_tree['childrens'][0];
						else
							break;
					}
				}
			}
		}

		//Main image
			$general_data['main_image'] = !empty($product['image']) ? $product['image'] : '';

		//Image 2 - Image 3 - Image 4 - Image 5
			$general_data['image_2'] = '';
			$general_data['image_3'] = '';
			$general_data['image_4'] = '';
			$general_data['image_5'] = '';
			if(!empty($product['product_images']))
			{
				foreach ($product['product_images'] as $key => $img) {
					$general_data['image_'.($key+2)] = $img['image'];
				}
			}

		//Date available
			$general_data['date_available'] = !empty($product['date_available']) ? $product['date_available'] : date('Y-m-d');
		//Points
			$general_data['points'] = !empty($product['points']) ? $product['points'] : 0;

			foreach ($this->customer_groups as $key => $cg) {
				$general_data['points_'.$cg['customer_group_id']] = !empty($product['product_reward'][$cg['customer_group_id']]['points']) ? $product['product_reward'][$cg['customer_group_id']]['points'] : 0;
			}
		//Requires shipping
			$general_data['shipping'] = isset($product['shipping']) && $product['shipping'] == 0 ? 0 : 1;
		//Location
			$general_data['location'] = !empty($product['location']) ? $product['location'] : '';
		//Tax class
			$general_data['tax_class_id'] = !empty($product['tax_class_id']) ? $product['tax_class_id'] : '';
		//Sort order
			$general_data['sort_order'] = !empty($product['sort_order']) ? $product['sort_order'] : 0;
		//Store
			$stores = '';
			foreach ($product['product_store'] as $key => $store) {
				$stores .= $store;
				if($key+1 < count($product['product_store']))
					$stores .= "|";
			}
			$general_data['store'] = !empty($stores) ? $stores : 0;
		//Status
			$general_data['status'] = !empty($product['status']) ? $product['status'] : 1;
		//Class weight
			$general_data['weight_class_id'] = !empty($product['weight_class_id']) ? $product['weight_class_id'] : '';
		//Weight
			$general_data['weight'] = !empty($product['weight']) ? $product['weight'] : '';
		//Class length
			$general_data['length_class_id'] = !empty($product['length_class_id']) ? $product['length_class_id'] : '';
		//Length
			$general_data['length'] = !empty($product['length']) ? $product['length'] : '';
		//Width
			$general_data['width'] = !empty($product['width']) ? $product['width'] : '';
		//Height
			$general_data['height'] = !empty($product['height']) ? $product['height'] : '';
		//Layout
			$general_data['layout'] = !empty($product['product_layout'][0]) ? $product['product_layout'][0] : '';

		//Add extra general data here
		
		//Attributes
			for ($i=1; $i <= $this->attributeNumber; $i++) {
				if($this->count_languages == 1)
				{
					$general_data['attribute_group_'.$i] = '';
					$general_data['attribute_attribute_'.$i] = '';
					$general_data['attribute_value_'.$i] = '';
				}
				else
				{
					foreach ($this->languages as $key => $lang) {
						$general_data['attribute_group_'.$i.'_'.$lang['code']] = '';
						$general_data['attribute_attribute_'.$i.'_'.$lang['code']] = '';
						$general_data['attribute_value_'.$i.'_'.$lang['code']] = '';
					}
				}
			}

			foreach ($product['product_attributes'] as $key => $attr) {
				if($this->count_languages == 1)
				{
					$general_data['attribute_group_'.($key+1)] = htmlspecialchars_decode($all_attributes[$attr['attribute_id']]['attribute_group_name']);
					$general_data['attribute_attribute_'.($key+1)] = htmlspecialchars_decode($all_attributes[$attr['attribute_id']]['attribute_name']);
					$general_data['attribute_value_'.($key+1)] = htmlspecialchars_decode($attr['product_attribute_description'][(int)$this->config->get('config_language_id')]['text']);
				}
				else
				{
					foreach ($this->languages as $key2 => $lang) {
						$attr_group_name = !empty($all_attributes[$attr['attribute_id']]['translates_attribute_group'][$lang['code']]) ? $all_attributes[$attr['attribute_id']]['translates_attribute_group'][$lang['code']] : '';
						$general_data['attribute_group_'.($key+1).'_'.$lang['code']] = htmlspecialchars_decode($attr_group_name);

						$attr_name = !empty($all_attributes[$attr['attribute_id']]['translates_attribute'][$lang['code']]) ? $all_attributes[$attr['attribute_id']]['translates_attribute'][$lang['code']] : '';
						$general_data['attribute_attribute_'.($key+1).'_'.$lang['code']] = htmlspecialchars_decode($attr_name);
						
						$attr_value = !empty($attr['product_attribute_description'][$lang['language_id']]['text']) ? $attr['product_attribute_description'][$lang['language_id']]['text'] : '';
						$general_data['attribute_value_'.($key+1).'_'.$lang['code']] = htmlspecialchars_decode($attr_value);
					}
				}
			}

			if($this->count_languages > 1)
			{
				foreach ($this->languages as $key => $lang) {
					
				}
			}

		//Filters
			//Group product filters in filter groups.
			for ($i=1; $i <= $this->filterGroupNumber ; $i++) { 
				$general_data['filter_group_'.$i] = '';
				for ($j=1; $j <= $this->filterGroupFilterNumber; $j++) { 
					$general_data['filter_group_'.$i.'_filter_'.$j] = '';
				}
			}

			$final_filters = array();
			foreach ($product['product_filters'] as $key => $filter_id) {
				$id_group = !empty($all_filters[$filter_id]['filter_group_id']) ? $all_filters[$filter_id]['filter_group_id'] : '';
				if(!empty($id_group))
				{
					if(!isset($final_filters[$id_group]))
						$final_filters[$id_group] = array('group_name' => $all_filters[$filter_id]['group_name'], 'filters' => array());
					
					array_push($final_filters[$id_group]['filters'], $all_filters[$filter_id]['name']);
				}
			}
			$count_group_filters = 1;

			foreach ($final_filters as $key => $group_filter) {
				$general_data['filter_group_'.$count_group_filters] = $group_filter['group_name'];
				$count_filters = 1;
				foreach ($group_filter['filters'] as $key2 => $filter) {
					if($count_filters > $this->filterGroupNumber)
						break;
					$general_data['filter_group_'.$count_group_filters.'_filter_'.$count_filters] = $filter;

					$count_filters++;
				}
				$count_group_filters++;

				if($count_group_filters > $this->filterGroupFilterNumber)
					break;
			}  
		
		//Options
			$general_data['option'] = '';

			if($this->count_languages > 1)
			{
				foreach ($this->languages as $key => $lang) {
					$general_data['option_'.$lang['code']] = '';
					$general_data['option_value_'.$lang['code']] = '';
				}
			}

			$general_data['option_type'] = '';
			$general_data['option_value'] = '';
			$general_data['option_required'] = '';
			$general_data['option_subtract'] = '';
			$general_data['option_sku'] = '';
			$general_data['option_image'] = '';
			$general_data['option_price_prefix'] = '';
			$general_data['option_points_prefix'] = '';
			$general_data['option_weight_prefix'] = '';
			$general_data['option_sku_option_boost'] = '';
			$general_data['option_image_option_boost'] = '';

			if (empty($product['product_options']))
			{
				$final_products[] = $general_data;
			}
			else
			{
				$first_row = true;

				$first_price = $general_data['price'];
				$first_weight = $general_data['weight'];
				$first_points = $general_data['points'];
				$first_quantity = $general_data['quantity'];
				foreach ($product['product_options'] as $key => $opt) {
					$general_data['option'] = $opt['name'];
					$general_data['option_type'] = $opt['type'];
					$general_data['option_required'] = $opt['required'];

					if($opt['type'] != 'text')
					{
						foreach ($opt['product_option_value'] as $key2 => $opt_val) {
							//Option value image
								$temporal_sql = "SELECT image FROM `" . DB_PREFIX . "option_value` WHERE option_value_id = ".$opt_val['option_value_id'].";";
								$result = $this->db->query( $temporal_sql );

								if(!empty($result->row['image']))
									$general_data['option_image'] = $result->row['image'];
							//END Option value image

							if(!$first_row)
							{
								//Fill empty all not required values to option data
								$general_data['description'] = '';
								$general_data['meta_description'] = '';
								$general_data['meta_keyword'] = '';
								$general_data['meta_title'] = '';
								$general_data['keyword'] = '';
								$general_data['tag'] = '';
								$general_data['sku'] = '';
								$general_data['ean'] = '';
								$general_data['upc'] = '';
								$general_data['jan'] = '';
								$general_data['mpn'] = '';
								$general_data['isbn'] = '';
								$general_data['minimum'] = '';
								$general_data['subtract'] = '';
								$general_data['stock_status_id'] = '';
								$general_data['special_customer_group_id'] = '';
								$general_data['special_priority'] = '';
								$general_data['special_price'] = '';
								$general_data['special_date_start'] = '';
								$general_data['special_date_end'] = '';
								$general_data['discount_customer_group_id_1'] = '';
								$general_data['discount_quantity_1'] = '';
								$general_data['discount_priority_1'] = '';
								$general_data['discount_price_1'] = '';
								$general_data['discount_date_start_1'] = '';
								$general_data['discount_date_end_1'] = '';
								$general_data['discount_customer_group_id_2'] = '';
								$general_data['discount_quantity_2'] = '';
								$general_data['discount_priority_2'] = '';
								$general_data['discount_price_2'] = '';
								$general_data['discount_date_start_2'] = '';
								$general_data['discount_date_end_2'] = '';
								$general_data['discount_customer_group_id_3'] = '';
								$general_data['discount_quantity_3'] = '';
								$general_data['discount_priority_3'] = '';
								$general_data['discount_price_3'] = '';
								$general_data['discount_date_start_3'] = '';
								$general_data['discount_date_end_3'] = '';
								$general_data['manufacturer'] = '';
								$general_data['category_1'] = '';
								$general_data['category_2'] = '';
								$general_data['category_3'] = '';
								$general_data['category_4'] = '';
								$general_data['category_5'] = '';
								$general_data['main_image'] = '';
								$general_data['image_2'] = '';
								$general_data['image_3'] = '';
								$general_data['image_4'] = '';
								$general_data['image_5'] = '';
								$general_data['date_available'] = '';
								$general_data['shipping'] = '';
								$general_data['location'] = '';
								$general_data['tax_class_id'] = '';
								$general_data['sort_order'] = '';
								$general_data['store'] = '';
								$general_data['status'] = '';
								$general_data['length_class_id'] = '';
								$general_data['length'] = '';
								$general_data['width'] = '';
								$general_data['height'] = '';
								$general_data['layout'] = '';

								for ($i=1; $i <= $this->attributeNumber; $i++) { 
									$general_data['attribute_group_'.$i] = '';
									$general_data['attribute_attribute_'.$i] = '';
									$general_data['attribute_value_'.$i] = '';
								}

								for ($i=1; $i <= $this->filterGroupNumber; $i++) { 
						            $general_data['filter_group_'.$i] = '';
						            for ($j=1; $j <= $this->filterGroupFilterNumber; $j++) { 
						               $general_data['filter_group_'.$i.'_filter_'.$j] = '';
						            }
						        }

								$general_data['price'] = $opt_val['price'];
								$general_data['points'] = $opt_val['points'];
								$general_data['weight'] = $opt_val['weight'];
								$general_data['option_price_prefix'] = $opt_val['price_prefix'];
								$general_data['option_points_prefix'] = $opt_val['points_prefix'];
								$general_data['option_weight_prefix'] = $opt_val['weight_prefix'];
							}
							else
							{
								$general_data['option_price_prefix'] = '';
								$general_data['option_points_prefix'] = '';
								$general_data['option_weight_prefix'] = '';
								$first_row = false;
							}

							$general_data['quantity'] = $opt_val['quantity'];

							$general_data['option_value'] = '';

							if(!empty($all_options[$opt['option_id']]['option_values'][$opt_val['option_value_id']]))
							{
								$general_data['option_value'] = htmlspecialchars_decode($all_options[$opt['option_id']]['option_values'][$opt_val['option_value_id']]);

								if($this->count_languages > 1)
								{
									foreach ($this->languages as $key => $lang) {
										//BEGIN Option
											$temporal_sql = "SELECT * FROM `" . DB_PREFIX . "option_description` WHERE language_id = ".$lang['language_id']." AND option_id = ".$opt['option_id'].";";
											$descriptions = 
											$this->db->query($temporal_sql);
											$general_data['option_'.$lang['code']] = !empty($descriptions->row['name']) ? $descriptions->row['name'] : '';
										//END Option

										//BEGIN Option value
											$temporal_sql = "SELECT * FROM `" . DB_PREFIX . "option_value_description` WHERE language_id = ".$lang['language_id']." AND option_value_id = ".$opt_val['option_value_id'].";";
											$descriptions = $this->db->query($temporal_sql);
											$general_data['option_value_'.$lang['code']] = !empty($descriptions->row['name']) ? $descriptions->row['name'] : '';
										//END Option value
									}
								}
							}
								
							$general_data['option_subtract'] = $opt_val['subtract'];
							$general_data['option_sku_option_boost'] = !empty($opt_val['sku']) ? $opt_val['sku'] : '';
							$general_data['option_image_option_boost'] = !empty($opt_val['image']) ? $opt_val['image'] : '';
							$final_products[] = $general_data;
						}
					}
					else
					{
						$general_data['option_value'] = $opt['value'];

						if($this->count_languages > 1)
						{
							foreach ($this->languages as $key => $lang) {
								//BEGIN Option
									$temporal_sql = "SELECT * FROM `" . DB_PREFIX . "option_description` WHERE language_id = ".$lang['language_id']." AND option_id = ".$opt['option_id'].";";
									$descriptions = 
									$this->db->query($temporal_sql);
									$general_data['option_'.$lang['code']] = !empty($descriptions->row['name']) ? $descriptions->row['name'] : '';
								//END Option

								$general_data['option_value_'.$lang['code']] = !empty($opt['value']) ? $opt['value'] : '';
							}
						}
						$final_products[] = $general_data;
					}
				}
			}
		return $final_products;
	}

	//GET FUNCTIONS
		public function get_all_categories()
		{
			$temporal_sql = "SELECT * FROM `" . DB_PREFIX . "category`;";
			$result = $this->db->query( $temporal_sql );

			$category_array = array();

			foreach ($result->rows as $key => $category) {
				$temp = array();
				$temp['category_id'] = $category['category_id'];

				$temporal_sql = "SELECT * FROM `" . DB_PREFIX . "category_description` WHERE category_id = ".$category['category_id']." AND language_id = ".(int)$this->config->get('config_language_id').";";
				$result = $this->db->query( $temporal_sql );

				$temp['name'] = $result->row['name'];
				$category_array[$temp['category_id']] = $temp['name'];
			}

			return $category_array;   
		}

		public function get_tree_categories($categories)
		{
			$final_categories = array();
			
			//Get name and parent of all categories
				foreach ($categories as $key => $cat_id) {
					$temporal_sql = "SELECT c.category_id,c.parent_id,cd.name FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (c.category_id = cd.category_id AND cd.language_id = ".(int)$this->config->get('config_language_id').") WHERE c.category_id = ".$cat_id;
					$result = $this->db->query( $temporal_sql );

					if(!empty($result->row))
						$final_categories[] = $result->row;
				}
			//END Get name and parent of all categories

			//Construct tree categories
				if(!empty($final_categories))
				{
					$final_categories = $this->model_tool_import_xls->buildTree($final_categories);
				}
			//END Construct tree categories
				
			return $final_categories;
		}

		public function get_all_manufacturers()
		{
			$this->load->model('catalog/manufacturer');
			$manufacturers = $this->model_catalog_manufacturer->getManufacturers();

			$manufacturers_final = array();

			foreach ($manufacturers as $key => $ma) {
				$manufacturers_final[$ma['manufacturer_id']] = $ma['name'];
			}
			return $manufacturers_final;        
		}

		public function get_all_filters()
		{
			$this->load->model('catalog/filter');
			$filters = $this->model_catalog_filter->getFilters(false);

			$filters_final = array();

			foreach ($filters as $key => $fi) {
				$filters_final[$fi['filter_id']] = array(
					'name' => $fi['name'],
					'group_name' => $fi['group'],
					'filter_group_id' => $fi['filter_group_id'],
				);
			}
			return $filters_final;        
		}

		public function get_all_attributes()
		{
			$this->load->model('catalog/attribute');
			$attributes = $this->model_catalog_attribute->getAttributes();

			$attributes_final = array();
			foreach ($attributes as $key => $at) {
				if (!isset($attributes_final[$at['attribute_id']]))
					$attributes_final[$at['attribute_id']] = array();

				$attributes_final[$at['attribute_id']]['attribute_group_name'] = $at['attribute_group'];
				$attributes_final[$at['attribute_id']]['attribute_group_id'] = $at['attribute_group_id'];
				$attributes_final[$at['attribute_id']]['attribute_id'] = $at['attribute_id'];
				$attributes_final[$at['attribute_id']]['attribute_name'] = $at['name'];
			}

			if($this->count_languages > 1)
			{
				foreach ($attributes_final as $key => $attr_group) {
					foreach ($this->languages as $key2 => $lang) {
						//Attribute group name translates
							$sql = "SELECT * FROM " . DB_PREFIX . "attribute_group_description WHERE attribute_group_id = ".$attr_group['attribute_group_id']." AND language_id = ".(int)$lang['language_id'].";";
							$attr_translate = $this->db->query($sql);
							
							if(!isset($attributes_final[$key]['translates_attribute_group']))
								$attributes_final[$key]['translates_attribute_group'] = array();

							$attributes_final[$key]['translates_attribute_group'][$lang['code']] = !empty($attr_translate->row['name']) ? $attr_translate->row['name'] : '';
						//END Attribute group name translates

						//Attribute name translates
							$sql = "SELECT * FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = ".$attr_group['attribute_id']." AND language_id = ".(int)$lang['language_id'].";";
							$attr_translate = $this->db->query($sql);
							
							if(!isset($attributes_final[$key]['translates_attribute']))
								$attributes_final[$key]['translates_attribute'] = array();

							$attributes_final[$key]['translates_attribute'][$lang['code']] = !empty($attr_translate->row['name']) ? $attr_translate->row['name'] : '';
						//END Attribute name translates
					}
				}
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
				$options_final[$op['option_id']] = array(
					'option_id' => $op['option_id'],
					'option_name' => $op['name'],
					'option_type' => $op['type'],
					'option_values' => array()
				);
			}

			//Get all options values to each option
			foreach ($options_final as $option_id => $op) {
				$optuion_values = $this->model_catalog_option->getOptionValues($option_id);

				//Format option values
				$option_values_final = array();

				foreach ($optuion_values as $key => $op) {
					$option_values_final[$op['option_value_id']] = $op['name'];
				}

				$options_final[$option_id]['option_values'] = $option_values_final;
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
}
?>