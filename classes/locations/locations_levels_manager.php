<?php 

class w2gm_locations_levels_manager {
	
	public function __construct() {
		add_action('admin_menu', array($this, 'menu'));
	}

	public function menu() {
		add_submenu_page('w2gm_settings',
				__('Locations levels', 'W2GM'),
				__('Locations levels', 'W2GM'),
				'administrator',
				'w2gm_locations_levels',
				array($this, 'w2gm_locations_levels')
		);
	}
	
	public function w2gm_locations_levels() {
		if (isset($_GET['action']) && $_GET['action'] == 'add') {
			$this->addOrEditLocationsLevel();
		} elseif (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['level_id'])) {
			$this->addOrEditLocationsLevel($_GET['level_id']);
		} elseif (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['level_id'])) {
			$this->deleteLocationsLevel($_GET['level_id']);
		} else {
			$this->showLocationsLevelsTable();
		}
	}
	
	public function showLocationsLevelsTable() {
		$locations_levels = new w2gm_locations_levels;
	
		$locations_levels_table = new w2gm_manage_locations_levels_table();
		$locations_levels_table->prepareItems($locations_levels);
	
		w2gm_renderTemplate('locations/locations_levels_table.tpl.php', array('locations_levels_table' => $locations_levels_table));
	}
	
	public function addOrEditLocationsLevel($level_id = null) {
		global $w2gm_instance;
	
		$locations_levels = $w2gm_instance->locations_levels;
	
		if (!$locations_level = $locations_levels->getLevelById($level_id))
			$locations_level = new w2gm_locations_level();
	
		if (w2gm_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2gm_locations_levels_nonce'], W2GM_PATH)) {
			$validation = new w2gm_form_validation();
			$validation->set_rules('name', __('Level name', 'W2GM'), 'required');
			//$validation->set_rules('in_widget', __('In locations widget', 'W2GM'), 'is_checked');
			$validation->set_rules('in_address_line', __('In address line', 'W2GM'), 'is_checked');
	
			if ($validation->run()) {
				if ($locations_level->id) {
					if ($locations_levels->saveLevelFromArray($level_id, $validation->result_array())) {
						w2gm_addMessage(__('Level was updated successfully!', 'W2GM'));
					}
				} else {
					if ($locations_levels->createLevelFromArray($validation->result_array())) {
						w2gm_addMessage(__('Level was created succcessfully!', 'W2GM'));
					}
				}
				$this->showLocationsLevelsTable();
			} else {
				$locations_level->buildLevelFromArray($validation->result_array());
				w2gm_addMessage($validation->error_string(), 'error');
	
				w2gm_renderTemplate('locations/add_edit_locations_level.tpl.php', array('locations_level' => $locations_level, 'locations_level_id' => $level_id));
			}
		} else {
			w2gm_renderTemplate('locations/add_edit_locations_level.tpl.php', array('locations_level' => $locations_level, 'locations_level_id' => $level_id));
		}
	}
	
	public function deleteLocationsLevel($level_id) {
		global $w2gm_instance;
	
		$locations_levels = $w2gm_instance->locations_levels;
		if ($locations_level = $locations_levels->getLevelById($level_id)) {
			if (w2gm_getValue($_POST, 'submit')) {
				if ($locations_levels->deleteLevel($level_id))
					w2gm_addMessage(__('Level was deleted successfully!', 'W2GM'));
	
				$this->showLocationsLevelsTable();
			} else
				w2gm_renderTemplate('delete_question.tpl.php', array('heading' => __('Delete locations level', 'W2GM'), 'question' => sprintf(__('Are you sure you want delete "%s" locations level?', 'W2GM'), $locations_level->name), 'item_name' => $locations_level->name));
		} else
			$this->showLocationsLevelsTable();
	}
}

?>