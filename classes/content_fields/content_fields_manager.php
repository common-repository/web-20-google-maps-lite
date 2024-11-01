<?php

class w2gm_content_fields_manager {
	public $menu_page_hook;
	
	public function __construct() {
		global $pagenow;

		if ($pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'admin-ajax.php') {
			add_action('add_meta_boxes', array($this, 'addContentFieldsMetabox'));
		}
		
		add_action('admin_menu', array($this, 'menu'));

		add_action('delete_term_taxonomy', array($this, 'renew_assigned_categories'));
	}
	
	public function menu() {
		$this->menu_page_hook = add_submenu_page('w2gm_settings',
			__('Content fields', 'W2GM'),
			__('Content fields', 'W2GM'),
			'administrator',
			'w2gm_content_fields',
			array($this, 'w2gm_content_fields')
		);
	}
	
	public function w2gm_content_fields() {
		if (isset($_GET['action']) && $_GET['action'] == 'add') {
			$this->addOrEditContentField();
		} elseif (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['field_id'])) {
			$this->addOrEditContentField($_GET['field_id']);
		} elseif (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['field_id'])) {
			$this->deleteContentField($_GET['field_id']);
		} elseif (isset($_GET['action']) && $_GET['action'] == 'configure' && isset($_GET['field_id'])) {
			$this->configureContentField($_GET['field_id']);
		} elseif (isset($_GET['action']) && $_GET['action'] == 'add_group') {
			$this->addOrEditContentFieldsGroup();
		} elseif (isset($_GET['action']) && $_GET['action'] == 'edit_group' && isset($_GET['group_id'])) {
			$this->addOrEditContentFieldsGroup($_GET['group_id']);
		} elseif (isset($_GET['action']) && $_GET['action'] == 'delete_group' && isset($_GET['group_id'])) {
			$this->deleteContentFieldsGroup($_GET['group_id']);
		} elseif (!isset($_GET['action'])) {
			$this->showContentFieldsTable();
		}
	}
	
	public function showContentFieldsTable() {
		wp_enqueue_script('jquery-ui-sortable');
		$content_fields = new w2gm_content_fields;

		if (isset($_POST['submit_table'])) {
			if (isset($_POST['content_fields_order']) && $_POST['content_fields_order']) {
				if ($content_fields->saveOrder())
					w2gm_addMessage(__('Content fields order were updated!', 'W2GM'), 'updated');
			}
			if ($content_fields->saveGroupsRelations())
				w2gm_addMessage(__('Content fields relations were updated!', 'W2GM'), 'updated');
		}

		$content_fields_table = new w2gm_manage_content_fields_table();
		$content_fields_table->prepareItems($content_fields);

		$content_fields_groups_table = new w2gm_manage_content_fields_groups_table();
		$content_fields_groups_table->prepareItems($content_fields);
		
		w2gm_renderTemplate('content_fields/content_fields_table.tpl.php', array('content_fields_table' => $content_fields_table, 'content_fields_groups_table' => $content_fields_groups_table, 'fields_types_names' => $content_fields->fields_types_names));
	}
	
	public function addOrEditContentField($field_id = null) {
		global $w2gm_instance;
	
		$content_fields = $w2gm_instance->content_fields;
	
		if (!$content_field = $content_fields->getContentFieldById($field_id)) {
			// this will be new field
			if (isset($_POST['type']) && $_POST['type']) {
				// load dummy content field by its type from $_POST
				$field_class_name = 'w2gm_content_field_' . $_POST['type'];
				if (class_exists($field_class_name)) {
					$content_field = new $field_class_name;
				}
			} else 
				$content_field = new w2gm_content_field();
		}

		if (w2gm_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2gm_content_fields_nonce'], W2GM_PATH)) {
			$validation = $content_field->validation();

			if ($validation->run()) {
				if ($content_field->id) {
					if ($content_fields->saveContentFieldFromArray($field_id, $validation->result_array())) {
						w2gm_addMessage(__('Content field was updated successfully!', 'W2GM'));
					}
				} else {
					if ($content_fields->createContentFieldFromArray($validation->result_array())) {
						w2gm_addMessage(__('Content field was created succcessfully!', 'W2GM'));
					}
				}
				$this->showContentFieldsTable();
			} else {
				$content_field->buildContentFieldFromArray($validation->result_array());
				w2gm_addMessage($validation->error_string(), 'error');
	
				w2gm_renderTemplate('content_fields/add_edit_content_field.tpl.php', array('content_fields' => $content_fields, 'content_field' => $content_field, 'field_id' => $field_id, 'fields_types_names' => $content_fields->fields_types_names));
			}
		} else {
			w2gm_renderTemplate('content_fields/add_edit_content_field.tpl.php', array('content_fields' => $content_fields, 'content_field' => $content_field, 'field_id' => $field_id, 'fields_types_names' => $content_fields->fields_types_names));
		}
	}

	public function addOrEditContentFieldsGroup($group_id = null) {
		global $w2gm_instance;
	
		$content_fields = $w2gm_instance->content_fields;
	
		if (!$content_fields_group = $content_fields->getContentFieldsGroupById($group_id)) {
			// this will be new fields group
			$content_fields_group = new w2gm_content_fields_group();
		}

		if (w2gm_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2gm_content_fields_nonce'], W2GM_PATH)) {
			$validation = $content_fields_group->validation();

			if ($validation->run()) {
				if ($content_fields_group->id) {
					if ($content_fields->saveContentFieldsGroupFromArray($group_id, $validation->result_array())) {
						w2gm_addMessage(__('Content fields group was updated successfully!', 'W2GM'));
					}
				} else {
					if ($content_fields->createContentFieldsGroupFromArray($validation->result_array())) {
						w2gm_addMessage(__('Content fields group was created succcessfully!', 'W2GM'));
					}
				}
				$this->showContentFieldsTable();
			} else {
				$content_fields->buildContentFieldsGroupFromArray($validation->result_array());
				w2gm_addMessage($validation->error_string(), 'error');
	
				w2gm_renderTemplate('content_fields/add_edit_content_fields_group.tpl.php', array('content_fields' => $content_fields, 'content_fields_group' => $content_fields_group, 'group_id' => $group_id));
			}
		} else {
			w2gm_renderTemplate('content_fields/add_edit_content_fields_group.tpl.php', array('content_fields' => $content_fields, 'content_fields_group' => $content_fields_group, 'group_id' => $group_id));
		}
	}

	public function configureContentField($field_id) {
		global $w2gm_instance;
	
		if (($content_field = $w2gm_instance->content_fields->getContentFieldById($field_id)) && $content_field->isConfigurationPage())
			$content_field->configure();
		else {
			w2gm_addMessage(esc_attr__("This content field can't be configured", 'W2GM'), 'error');
			$this->showContentFieldsTable();
		}
	}

	public function deleteContentField($field_id) {
		global $w2gm_instance;
	
		$content_fields = $w2gm_instance->content_fields;
		// core fields can't be deleted
		if (($content_field = $content_fields->getContentFieldById($field_id)) && !$content_field->is_core_field) {
			if (w2gm_getValue($_POST, 'submit')) {
				if ($content_fields->deleteContentField($field_id))
					w2gm_addMessage(__('Content field was deleted successfully!', 'W2GM'));
	
				$this->showContentFieldsTable();
			} else
				w2gm_renderTemplate('delete_question.tpl.php', array('heading' => __('Delete content field', 'W2GM'), 'question' => sprintf(__('Are you sure you want delete "%s" content field?', 'W2GM'), $content_field->name), 'item_name' => $content_field->name));
		} else
			$this->showContentFieldsTable();
	}

	public function deleteContentFieldsGroup($group_id) {
		global $w2gm_instance;
	
		$content_fields = $w2gm_instance->content_fields;
		if ($content_fields_group = $content_fields->getContentFieldsGroupById($group_id)) {
			if (w2gm_getValue($_POST, 'submit')) {
				if ($content_fields->deleteContentFieldsGroup($group_id))
					w2gm_addMessage(__('Content fields group was deleted successfully!', 'W2GM'));
	
				$this->showContentFieldsTable();
			} else
				w2gm_renderTemplate('delete_question.tpl.php', array('heading' => __('Delete content fields group', 'W2GM'), 'question' => sprintf(__('Are you sure you want delete "%s" content fields group?', 'W2GM'), $content_fields_group->name), 'item_name' => $content_fields_group->name));
		} else
			$this->showContentFieldsTable();
	}

	public function addContentFieldsMetabox($post_type) {
		if ($post_type == W2GM_POST_TYPE) {
			global $w2gm_instance;
			
			if ($w2gm_instance->content_fields->isNotCoreContentFields())
				add_meta_box('w2gm_content_fields',
						__('Content fields', 'W2GM'),
						array($this, 'contentFieldsMetabox'),
						W2GM_POST_TYPE,
						'normal',
						'high');
		}
	}
	
	public function contentFieldsMetabox($post) {
		global $w2gm_instance;

		if ($listing = w2gm_getCurrentListingInAdmin()) {
			$content_fields = $listing->content_fields + $w2gm_instance->content_fields->content_fields_array;

			// now need to order content fields by their order_num values, because after merge the order is broken
			$order_keys = array_keys($w2gm_instance->content_fields->content_fields_array);
			$ordered_content_fields = array();
			foreach($order_keys as $key) {
				if(array_key_exists($key, $content_fields)) {
					$ordered_content_fields[$key] = $content_fields[$key];
					unset($content_fields[$key]);
				}
			}
			$content_fields = array();
			foreach ($ordered_content_fields AS &$content_field)
				$content_fields[] = $content_field;
		} else
			$content_fields = $w2gm_instance->content_fields->content_fields_array;
		w2gm_renderTemplate('content_fields/content_fields_metabox.tpl.php', array('content_fields' => $content_fields));
	}

	/**
	 * This action called before maps category item would be deleted,
	 * refresh categories array, those assigned with content fields.
	 * 
	 * @param int $tt_id - term taxonomy id
	 */
	public function renew_assigned_categories($tt_id) {
		if ($term = get_term_by('term_taxonomy_id', $tt_id, W2GM_CATEGORIES_TAX)) {
			global $wpdb;
			$content_fields = $wpdb->get_results("SELECT * FROM {$wpdb->w2gm_content_fields}", ARRAY_A);
			foreach ($content_fields AS $content_field) {
				if ($content_field['categories']) {
					$unserialized_categories = unserialize($content_field['categories']);
					if (count($unserialized_categories) > 1 || $unserialized_categories != array(''))
						if (($key = array_search($term->term_id, $unserialized_categories)) !== FALSE) {
							unset($unserialized_categories[$key]);
							$wpdb->update($wpdb->w2gm_content_fields, array('categories' => serialize($unserialized_categories)), array('id' => $content_field['id']));
						}
				}
			}
		}
	}
}

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class w2gm_manage_content_fields_table extends WP_List_Table {

	public function __construct() {
		parent::__construct(array(
				'singular' => __('content field', 'W2GM'),
				'plural' => __('content fields', 'W2GM'),
				'ajax' => false
		));
	}

	public function get_columns() {
		$columns = array(
				'id' => __('ID', 'W2GM'),
				'field_name' => __('Name', 'W2GM'),
				'field_type' => __('Field type', 'W2GM'),
				'required' => __('Required', 'W2GM'),
				'icon_image' => __('Icon image', 'W2GM'),
				'in_pages' => __('Visibility', 'W2GM'),
				'group_id' => __('Group', 'W2GM'),
		);
		$columns = apply_filters('w2gm_content_field_table_header', $columns);

		return $columns;
	}

	public function getItems($content_fields_object) {
		$items_array = array();
		foreach ($content_fields_object->content_fields_array as $id=>$content_field) {
			$items_array[$id] = array(
					'id' => $content_field->id,
					'is_core_field' => $content_field->is_core_field,
					'field_name' => $content_field->name,
					'field_type' => $content_field->type,
					'required' => $content_field->is_required,
					'can_be_required' => $content_field->canBeRequired(),
					'is_configuration_page' => $content_field->isConfigurationPage(),
					'is_search_configuration_page' => $content_field->isSearchConfigurationPage(),
					'icon_image' => $content_field->icon_image,
					'on_search_form' => $content_field->on_search_form,
					'on_map' => $content_field->on_map,
					'on_listing_page' => $content_field->on_listing_page,
					'on_listing_sidebar' => $content_field->on_listing_sidebar,
					'group_id' => $content_field->group_id,
			);
			$items_array[$id] = apply_filters('w2gm_content_field_table_row', $items_array[$id], $content_field);
		}
		return $items_array;
	}

	public function prepareItems($content_fields_object) {
		$this->_column_headers = array($this->get_columns(), array(), array());

		$this->items = $this->getItems($content_fields_object);
	}

	public function column_field_name($item) {
		$actions['edit'] = sprintf('<a href="?page=%s&action=%s&field_id=%d">' . __('Edit', 'W2GM') . '</a>', $_GET['page'], 'edit', $item['id']);
		if ($item['is_configuration_page'])
			$actions['configure'] = sprintf('<a href="?page=%s&action=%s&field_id=%d">' . __('Configure', 'W2GM') . '</a>', $_GET['page'], 'configure', $item['id']);
		if ($item['is_search_configuration_page'])
			$actions['search_configure'] = sprintf('<a href="?page=%s&action=%s&field_id=%d">' . __('Configure search', 'W2GM') . '</a>', $_GET['page'], 'configure_search', $item['id']);

		$actions = apply_filters('w2gm_content_fields_column_options', $actions, $item);

		if (!$item['is_core_field'])
			$actions['delete'] = sprintf('<a href="?page=%s&action=%s&field_id=%d">' . __('Delete', 'W2GM') . '</a>', $_GET['page'], 'delete', $item['id']);
		return sprintf('%1$s %2$s', sprintf('<a href="?page=%s&action=%s&field_id=%d">' . $item['field_name'] . '</a><input type="hidden" class="content_field_weight_id" value="%d" />', $_GET['page'], 'edit', $item['id'], $item['id']), $this->row_actions($actions));
	}

	public function column_field_type($item) {
		global $w2gm_instance;

		return $w2gm_instance->content_fields->fields_types_names[$item['field_type']];
	}

	public function column_required($item) {
		if ($item['can_be_required'])
			if ($item['required'])
				return '<img src="' . W2GM_RESOURCES_URL . 'images/accept.png" />';
			else
				return '<img src="' . W2GM_RESOURCES_URL . 'images/delete.png" />';
		else
			return ' ';
	}

	public function column_icon_image($item) {
		if ($item['icon_image'])
			return '<span class="w2gm-icon-tag w2gm-fa ' . $item['icon_image'] . '"></span>';
		else
			return ' ';
	}

	public function column_in_pages($item) {
		$html = array();
		if ($item['on_listing_page'])
			$html[] = __('In listing window', 'W2GM');
		if ($item['on_map'])
			$html[] = __('In map marker InfoWindow', 'W2GM');
		if ($item['on_listing_sidebar'])
			$html[] = __('In listings sidebar', 'W2GM');
		if ($item['on_search_form'])
			$html_array[] = __('On search form', 'W2GM');
		
		$html = apply_filters('w2gm_content_fields_in_pages_options', $html, $item);
		
		if ($html)
			return implode('<br />', $html);
		else
			return ' ';
	}
	
	public function column_group_id($item) {
		global $w2gm_instance;

		echo '<select name="group_id_' . $item['id'] . '">';
		echo '<option value=0>' . __('- Without group -', 'W2GM') . '</option>';
		foreach ($w2gm_instance->content_fields->content_fields_groups_array AS $group)
			echo '<option value=' . $group->id . ' ' . selected($item['group_id'], $group->id) . '>' . $group->name . '</option>';
		echo '</select>';
	}

	public function column_default($item, $column_name) {
		switch($column_name) {
			default:
				return $item[$column_name];
		}
	}

	public function no_items() {
		__('No content fields found.', 'W2GM');
	}
}

class w2gm_manage_content_fields_groups_table extends WP_List_Table {

	public function __construct() {
		parent::__construct(array(
				'singular' => __('content fields group', 'W2GM'),
				'plural' => __('content fields groups', 'W2GM'),
				'ajax' => false
		));
	}

	public function get_columns() {
		$columns = array(
				'group_name' => __('Name', 'W2GM'),
				'on_tab' => __('On tab', 'W2GM'),
				'hide_anonymous' => __('Hide from anonymous', 'W2GM'),
		);
		$columns = apply_filters('w2gm_content_field_table_header', $columns);

		return $columns;
	}

	public function getItems($content_fields_object) {
		$items_array = array();
		foreach ($content_fields_object->content_fields_groups_array as $id=>$content_fields_group) {
			$items_array[$id] = array(
					'id' => $content_fields_group->id,
					'group_name' => $content_fields_group->name,
					'on_tab' => $content_fields_group->on_tab,
					'hide_anonymous' => $content_fields_group->hide_anonymous,
			);
		}
		return $items_array;
	}

	public function prepareItems($content_fields_object) {
		$this->_column_headers = array($this->get_columns(), array(), array());

		$this->items = $this->getItems($content_fields_object);
	}

	public function column_group_name($item) {
		$actions['edit'] = sprintf('<a href="?page=%s&action=%s&group_id=%d">' . __('Edit', 'W2GM') . '</a>', $_GET['page'], 'edit_group', $item['id']);
		$actions['delete'] = sprintf('<a href="?page=%s&action=%s&group_id=%d">' . __('Delete', 'W2GM') . '</a>', $_GET['page'], 'delete_group', $item['id']);
		return sprintf('%1$s %2$s', sprintf('<a href="?page=%s&action=%s&group_id=%d">' . $item['group_name'] . '</a>', $_GET['page'], 'edit_group', $item['id']), $this->row_actions($actions));
	}

	public function column_on_tab($item) {
		if ($item['on_tab'])
			return '<img src="' . W2GM_RESOURCES_URL . 'images/accept.png" />';
		else
			return '<img src="' . W2GM_RESOURCES_URL . 'images/delete.png" />';
	}

	public function column_hide_anonymous($item) {
		if ($item['hide_anonymous'])
			return '<img src="' . W2GM_RESOURCES_URL . 'images/accept.png" />';
		else
			return '<img src="' . W2GM_RESOURCES_URL . 'images/delete.png" />';
	}

	public function column_default($item, $column_name) {
		switch($column_name) {
			default:
				return $item[$column_name];
		}
	}

	public function no_items() {
		__('No content fields groups found.', 'W2GM');
	}
}
?>