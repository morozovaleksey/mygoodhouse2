<?php
class ModelCatalogCertificate extends Model {
	public function addCertificate($data) {
		$this->event->trigger('pre.admin.certificate.add', $data);

		$this->load->model('localisation/language');
		$language_info = $this->model_localisation_language->getLanguageByCode($this->config->get('config_language'));
    	$front_language_id = $language_info['language_id'];

		$this->db->query("INSERT INTO " . DB_PREFIX . "certificate SET name = '" . $this->db->escape($data['name']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$certificate_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "certificate SET image = '" . $this->db->escape($data['image']) . "' WHERE certificate_id = '" . (int)$certificate_id . "'");
		}


		$this->cache->delete('certificate');

		$this->event->trigger('post.admin.certificate.add', $certificate_id);

		return $certificate_id;
	}

	public function editCertificate($certificate_id, $data) {

		$this->event->trigger('pre.admin.certificate.edit', $data);

		$this->load->model('localisation/language');
		$language_info = $this->model_localisation_language->getLanguageByCode($this->config->get('config_language'));
    	$front_language_id = $language_info['language_id'];
		$data['name'] = $data['name'];

		$this->db->query("UPDATE " . DB_PREFIX . "certificate SET name = '" . $this->db->escape($data['name']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE certificate_id = '" . (int)$certificate_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "certificate SET image = '" . $this->db->escape($data['image']) . "' WHERE certificate_id = '" . (int)$certificate_id . "'");
		}


		$this->cache->delete('certificate');

		$this->event->trigger('post.admin.certificate.edit', $certificate_id);
	}

	public function deleteCertificate($certificate_id) {
		$this->event->trigger('pre.admin.certificate.delete', $certificate_id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "certificate WHERE certificate_id = '" . (int)$certificate_id . "'");


		$this->cache->delete('certificate');

		$this->event->trigger('post.admin.certificate.delete', $certificate_id);
	}

	public function getCertificate($certificate_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "certificate WHERE certificate_id = '" . (int)$certificate_id . "'");

		return $query->row;
	}
	public function getCertificateByManufacturer($manufacturer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "certificate WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row;
	}

	public function getTotalCertificates() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "certificate");

		return $query->row['total'];
	}

	public function getCertificates($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "certificate";

//		$sql = "SELECT c.manufacturer_id, md.name, c.sort_order FROM " . DB_PREFIX . "manufacturer c LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (c.manufacturer_id = md.manufacturer_id) WHERE md.language_id = '" . (int)$this->config->get('config_language_id') . "'";


		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

}
