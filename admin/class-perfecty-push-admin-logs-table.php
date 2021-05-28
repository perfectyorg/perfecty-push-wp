<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Perfecty_Push_Admin_Logs_Table extends WP_List_Table {
	private const MAX_LENGTH = 1500;

	function __construct() {
		global $status, $page;

		parent::__construct(
			array(
				'singular' => 'log',
				'plural'   => 'logs',
			)
		);
	}

	function column_default( $item, $column_name ) {
		$content = $item['created_at'] . ' | ' . strtoupper( $item['level'] ) . ' | ' . $item['message'];

		if ( is_string( $content ) && strlen( $content ) > self::MAX_LENGTH ) {
			return substr( $content, 0, self::MAX_LENGTH ) . '...';
		} else {
			return $content;
		}
	}

	function get_columns() {
		$columns = array(
			'entry' => esc_html__( 'Log entries', 'perfecty-push-notifications' ),
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'entry' => array( 'created_at', true ),
		);
		return $sortable_columns;
	}

	function prepare_items() {
		$per_page = 500;

		$columns  = $this->get_columns();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, array(), $sortable );

		$total_items = Perfecty_Push_Lib_Db::get_total_logs();

		$paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] ) - 1 ) : 0;
		$orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_REQUEST['orderby'] : 'created_at';
		$order   = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array( 'asc', 'desc' ) ) ) ? $_REQUEST['order'] : 'desc';

		$logs        = Perfecty_Push_Lib_Db::get_logs( $paged * $per_page, $per_page, $orderby, $order, ARRAY_A );
		$this->items = (array) $logs;

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);

		return 0;
	}
}
