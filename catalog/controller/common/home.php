<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink(HTTP_SERVER, 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		//category
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('module/testimonial');
		$data['categories'] = array();
		$data['children_data'] = array();
		$data['reviews'] = array();
		$data['reviews'] = $this->model_module_testimonial->getReviews();


		$categories = $this->model_catalog_category->getCategories(0);


		$children_data = array();
		foreach ($categories as $category) {
			if($category['category_id'] !=92) {
				$children = $this->model_catalog_category->getCategories($category['category_id']);


				$data['categories'][] = array(
					'category_id' => $category['category_id'],
					'name' => $category['name'],
					'href' => $this->url->link('product/category', 'path=' . $category['category_id']),
					'metka_slider' => strtolower($this->model_catalog_category->get_in_translate_to_en($category['name']))
				);

				foreach ($children as $child) {
					$filter_data = array('filter_category_id' => $child['category_id'], 'filter_sub_category' => true);

					$data['children_data'][] = array(
						'category_id' => $child['category_id'],
						'category_parent_id' => $child['parent_id'],
						'name' => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
						'metka_slider' => strtolower($this->model_catalog_category->get_in_translate_to_en($category['name'])),
						'thumb' => $this->model_tool_image->resize($child['image'], 278, 189)
					);
				}
			}
		}

		$filter_data_recent_blog = array(
			'filter_category_id' => 92,
			'filter_filter'      => '',
			'sort'               => 'p.date_available',
			'order'              => 'DESC',
			'start'              => 0,
			'limit'              => 3
		);

		$data['products_recent_blog'] = array();
		$products_recent_blog = $this->model_catalog_product->getProducts($filter_data_recent_blog);

		foreach($products_recent_blog as $product_recent_blog ) {
			$attribute_groups = $this->model_catalog_product->getProductAttributes($product_recent_blog['product_id']);
			$currentCat = $this->model_catalog_category->getCategoryName($product_recent_blog['product_id']);

			$category_info_product = $this->model_catalog_category->getCategory($currentCat['category_id']);
			if($attribute_groups) {
				$attributes = $attribute_groups[0]['attribute'];
			}
			else {
				$attributes='';
			}
			if ($product_recent_blog['image']) {
				$image = $this->model_tool_image->resize($product_recent_blog['image'], 357, 201);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			}
			$data['products_recent_blog'][] = array(
				'product_id'  => $product_recent_blog['product_id'],
				'name'        => $product_recent_blog['name'],
				'category'    => $currentCat,
				'category_href'    => $this->url->link('product/category', 'path=' . $currentCat['category_id']),
				'attributes'  => $attributes,
				'thumb'       => $image,
				'description' => utf8_substr(strip_tags(html_entity_decode($product_recent_blog['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
				'short_description' => utf8_substr(strip_tags(html_entity_decode($product_recent_blog['short_description'], ENT_QUOTES, 'UTF-8')), 0, 300) . '..',
				'date_available'     => date($this->language->get('date_format_short'), strtotime($product_recent_blog['date_available'])),
				'href'        => $this->url->link('product/product', '&product_id=' . $product_recent_blog['product_id'])
			);
		}


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/home.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/common/home.tpl', $data));
		}
	}
}