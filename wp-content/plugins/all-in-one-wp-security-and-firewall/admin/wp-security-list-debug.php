<?php
if(!defined('ABSPATH')){
    exit;//Exit if accessed directly
}

class AIOWPSecurity_List_Debug_Log extends AIOWPSecurity_List_Table
{

    /**
     * Sets up some table attributes (i.e: the plurals and whether it's ajax or not)
     */
    public function __construct()
    {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'entry',     //singular name of the listed records
            'plural' => 'entries',    //plural name of the listed records
            'ajax' => false        //does this table support ajax?
        ));

    }

    /**
     * Returns the default column item
     *
     * @param object $item
     * @param string $column_name
     * @return void
     */
    public function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * Sets the columns for the table
     *
     * @return array
     */
    public function get_columns()
    {
        $columns = array(
            'id' => 'ID',
            'created' => __('Date and time', 'all-in-one-security-and-firewall'),
            'level' => __('Level', 'all-in-one-wp-security-and-firewall'),
            'message' => __('Message', 'all-in-one-wp-security-and-firewall'),
            'type' => __('Type', 'all-in-one-wp-security-and-firewall')
        );
        return $columns;
    }

    /**
     * Sets which of the columns the table data can be sorted by
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'created' => array('created', false),
            'type' => array('type', false),
            'level' => array('level', false),
            'message'=>array('message', false)
        );
        return $sortable_columns;
    }

    /**
     * Grabs the data from database and handles the pagination
     *
     * @return void
     */
    public function prepare_items()
    {
        /**
         * First, lets decide how many records per page to show
         */
        if (defined('AIOWPSEC_DEBUG_LOG_PER_PAGE')) {
            $per_page = absint(AIOWPSEC_DEBUG_LOG_PER_PAGE);
        }

        $per_page = empty($per_page) ? 15 : $per_page;

        $columns = $this->get_columns();
        $hidden = array('id'); // we really don't need the IDs of the log entries displayed
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        global $wpdb;

        $debug_log_tbl = $wpdb->prefix . 'aiowps_debug_log';

        /* -- Ordering parameters -- */
        
        //Parameters that are going to be used to order the result
        isset($_GET["orderby"]) ? $orderby = strip_tags($_GET["orderby"]) : $orderby = '';
        isset($_GET["order"]) ? $order = strip_tags($_GET["order"]) : $order = '';

        // By default show the most recent debug log entries.
        $orderby = !empty($orderby) ? esc_sql($orderby) : 'created';
        $order = !empty($order) ? esc_sql($order) : 'DESC';

        $orderby = AIOWPSecurity_Utility::sanitize_value_by_array($orderby, $sortable);
        $order = AIOWPSecurity_Utility::sanitize_value_by_array($order, array('DESC' => '1', 'ASC' => '1'));

        $orderby = sanitize_sql_orderby($orderby);
        $order = sanitize_sql_orderby($order);

        $data = $wpdb->get_results("SELECT * FROM {$debug_log_tbl} ORDER BY {$orderby} {$order}", 'ARRAY_A');

        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
        $this->items = $data;
        $this->set_pagination_args(array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page' => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
        ));
    }
}