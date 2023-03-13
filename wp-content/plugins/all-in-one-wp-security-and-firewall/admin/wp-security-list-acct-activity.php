<?php
if(!defined('ABSPATH')){
    exit;//Exit if accessed directly
}

class AIOWPSecurity_List_Account_Activity extends AIOWPSecurity_List_Table {
    
    /**
     * Class constructor
     */
    public function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'item',     //singular name of the listed records
            'plural'    => 'items',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }

    public function column_default($item, $column_name){
    	return $item[$column_name];
    }
        
    public function column_user_id($item){
        $tab = strip_tags(stripslashes($_REQUEST['tab']));
        $delete_url = sprintf('admin.php?page=%s&tab=%s&action=%s&activity_login_rec=%s', AIOWPSEC_USER_LOGIN_MENU_SLUG, $tab, 'delete_acct_activity_rec', $item['id']);
        //Add nonce to delete URL
        $delete_url_nonce = wp_nonce_url($delete_url, "delete_acct_activity_log", "aiowps_nonce");
        
        //Build row actions
        $actions = array(
            'delete' => '<a href="'.$delete_url_nonce.'" onclick="return confirm(\''.esc_js(__('Are you sure you want to delete this item?', 'all-in-one-wp-security-and-firewall')).'\')">'.__('Delete').'</a>',
        );
        
        //Return the user_login contents
        return sprintf('%1$s <span style="color:silver"></span>%2$s',
            /*$1%s*/ $item['user_id'],
            /*$2%s*/ $this->row_actions($actions)
        );
    }

    public function column_logout_date($item) {
        return '1000-10-10 10:00:00' == $item['logout_date'] ? __('Login session still active', 'all-in-one-wp-security-and-firewall') : $item['logout_date'];
    }

    public function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
       );
    }
    
    public function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox
            'user_id' => __('User ID', 'all-in-one-wp-security-and-firewall'),
            'user_login' => __('Username', 'all-in-one-wp-security-and-firewall'),
			'login_date' => __('Login date', 'all-in-one-wp-security-and-firewall'),
			'logout_date' => __('Logout date', 'all-in-one-wp-security-and-firewall'),
            'login_ip' => 'IP'
        );
        return $columns;
    }
    
    public function get_sortable_columns() {
        $sortable_columns = array(
            'user_id' => array('user_id',false),
            'user_login' => array('user_login',false),
            'login_date' => array('login_date',false),
            'login_ip' => array('login_ip',false),
            'logout_date' => array('logout_date',false),
        );
        return $sortable_columns;
    }
    
    public function get_bulk_actions() {
        $actions = array(
			'delete' => __('Delete', 'all-in-one-wp-security-and-firewall')
        );
        return $actions;
    }

    public function process_bulk_action() {
            if ('delete'===$this->current_action()) 
            {//Process delete bulk actions
                if (!isset($_REQUEST['item'])) {
                    $error_msg = '<div id="message" class="error"><p><strong>';
                    $error_msg .= __('Please select some records using the checkboxes','all-in-one-wp-security-and-firewall');
                    $error_msg .= '</strong></p></div>';
                    echo $error_msg;
                } else{
					$delete_login_activity_ids = array_filter(array_map('intval', $_REQUEST['item']));
                    $this->delete_login_activity_records($delete_login_activity_ids);
                }
            }
    }

	/**
	 * Deletes one or more records from the AIOWPSEC_TBL_USER_LOGIN_ACTIVITY table.
	 *
	 * @param Array|String|Integer - ids or a single id
	 *
	 * @return Void
	 */
	public function delete_login_activity_records($entries) {
        global $wpdb, $aio_wp_security;
        $login_activity_table = AIOWPSEC_TBL_USER_LOGIN_ACTIVITY;
        if (is_array($entries))
        {
            if (isset($_REQUEST['_wp_http_referer']))
            {
                //Delete multiple records
                $tab = strip_tags(stripslashes($_REQUEST['tab']));

                $entries = array_filter($entries, 'is_numeric'); //discard non-numeric ID values
                $id_list = "(" .implode(",",$entries) .")"; //Create comma separate list for DB operation
                $delete_command = "DELETE FROM ".$login_activity_table." WHERE id IN ".$id_list;
                $result = $wpdb->query($delete_command);
				if ($result) {
					AIOWPSecurity_Admin_Menu::show_msg_record_deleted_st();
				} else {
					// Error on bulk delete
					$aio_wp_security->debug_logger->log_debug('Database error occurred when deleting rows from User Login Activity table. Database error: '.$wpdb->last_error, 4);
					AIOWPSecurity_Admin_Menu::show_msg_record_not_deleted_st();
				}
            }
        } elseif ($entries != NULL) {
            $nonce=isset($_GET['aiowps_nonce']) ? stripslashes($_GET['aiowps_nonce']) : '';
            if (!isset($nonce) ||!wp_verify_nonce($nonce, 'delete_acct_activity_log'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for delete selected account activity logs operation!",4);
                die(__('Nonce check failed for delete selected account activity logs operation!','all-in-one-wp-security-and-firewall'));
            }
            //Delete single record
            $delete_command = "DELETE FROM ".$login_activity_table." WHERE id = '".absint($entries)."'";
            $result = $wpdb->query($delete_command);
			if ($result) {
				AIOWPSecurity_Admin_Menu::show_msg_record_deleted_st();
			} elseif ($result === false) {
				// Error on single delete
				$aio_wp_security->debug_logger->log_debug('Database error occurred when deleting rows from User Login Activity table. Database error: '.$wpdb->last_error, 4);
				AIOWPSecurity_Admin_Menu::show_msg_record_not_deleted_st();
			}
        }
    }

	/**
	 * Retrieves all items from AIOWPSEC_TBL_USER_LOGIN_ACTIVITY according to a search term inside $_REQUEST['s']. It then assigns to $this->items.
	 *
	 * @param Boolean $ignore_pagination - whether to not paginate
	 *
	 * @return Void
	 */
	public function prepare_items($ignore_pagination = false) {
		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 100;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$search_term = isset($_REQUEST['s']) ? sanitize_text_field(stripslashes($_REQUEST['s'])) : '';

		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		global $wpdb;
		$login_activity_table = AIOWPSEC_TBL_USER_LOGIN_ACTIVITY;

		/* -- Ordering parameters -- */
		//Parameters that are going to be used to order the result

		$orderby = isset($_GET['orderby']) ? strip_tags(stripslashes($_GET['orderby'])) : $orderby = '';
		$order = isset($_GET['order']) ? strip_tags(stripslashes($_GET['order'])) : $order = '';

		$orderby = !empty($orderby) ? esc_sql($orderby) : 'login_date';
		$order = !empty($order) ? esc_sql($order) : 'DESC';

		$orderby = AIOWPSecurity_Utility::sanitize_value_by_array($orderby, $sortable);
		$order = AIOWPSecurity_Utility::sanitize_value_by_array($order, array('DESC' => '1', 'ASC' => '1'));

		if (empty($search_term)) {
			$data = $wpdb->get_results("SELECT * FROM $login_activity_table ORDER BY $orderby $order", ARRAY_A);
		} else {
			$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $login_activity_table WHERE `user_login` LIKE '%%%s%%' OR `login_ip` LIKE '%%%s%%' ORDER BY $orderby $order  LIMIT 100", $search_term, $search_term), ARRAY_A);
		}

		if (!$ignore_pagination) {
			$current_page = $this->get_pagenum();
			$total_items = count($data);
			$data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
			$this->set_pagination_args(array(
				'total_items' => $total_items, //WE have to calculate the total number of items
				'per_page' => $per_page, //WE have to determine how many items to show on a page
				'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
			));
		}

		foreach ($data as $index => $row) {
			$data[$index]['login_date'] = get_date_from_gmt(mysql2date('Y-m-d H:i:s', $row['login_date']), $this->get_wp_date_time_format());
			if ('1000-10-10 10:00:00' != $row['logout_date']) {
				$data[$index]['logout_date'] = get_date_from_gmt(mysql2date('Y-m-d H:i:s', $row['logout_date']), $this->get_wp_date_time_format());
			}
		}

		$this->items = $data;
	}

}
