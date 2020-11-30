<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Perfecty_Push_Admin_Notifictions_Table extends WP_List_Table {

	function __construct() {
		global $status, $page;

		parent::__construct(
			array(
				'singular' => 'notification',
				'plural'   => 'notifications',
			)
		);
	}


	function column_default( $item, $column_name ) {
		$max_lengh = 150;
		$column    = $item[ $column_name ];

		if ( is_string( $column ) && strlen( $column ) > $max_lengh ) {
			return substr( $item[ $column_name ], 0, $max_lengh ) . '...';
		} else {
			return $item[ $column_name ];
		}
	}


	function column_video_url( $item ) {
	}


	function column_date( $item ) {
	}


	function column_cb( $item ) {
	}

	function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'date' => array( 'date', true ),
		);
		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete',
		);
		return $actions;
	}

	function process_bulk_action() {
	}

	function prepare_items() {
		$per_page = 10;

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$affected = $this->process_bulk_action();

		$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name" );

		$paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] ) - 1 ) : 0;
		$orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_REQUEST['orderby'] : 'date';
		$order   = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array( 'asc', 'desc' ) ) ) ? $_REQUEST['order'] : 'desc';

		$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged ), ARRAY_A );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);

		return $affected;
	}
}
