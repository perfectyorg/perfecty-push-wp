<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Perfecty_Push_Admin_Notifications_Table extends WP_List_Table {

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
		return $item[ $column_name ];
	}

	function column_creation_time( $item ) {
		$actions = array(
			'view'   => sprintf( '<a href="?page=perfecty-push-notification&id=%s">%s</a>', $item['id'], 'View' ),
			'delete' => sprintf( '<a href="#" class="delete-entry" data-page="%s" data-id="%d">%s</a>', $_REQUEST['page'], $item['id'], 'Delete' ),
		);

		return sprintf(
			'%s %s',
			$item['creation_time'],
			$this->row_actions( $actions )
		);
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />',
			$item['id']
		);
	}

	function get_columns() {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'creation_time' => 'Date',
			'status'        => 'Status',
			'total'         => 'Total',
			'succeeded'     => 'Succeeded',
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'creation_time' => array( 'creation_time', true ),
			'total'         => array( 'total', true ),
			'succeeded'     => array( 'succeeded', true ),
			'status'        => array( 'status', true ),
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
		if ( 'delete' === $this->current_action() ) {
			$ids = isset( $_REQUEST['id'] ) && is_array( $_REQUEST['id'] ) ? $_REQUEST['id'] : array();

			// filter data
			$ids = array_map(
				function( $item ) {
					return int( $item );
				},
				$ids
			);

			Perfecty_Push_Lib_Db::delete_notifications( $ids );
		}
	}

	function prepare_items() {
		$per_page = 10;

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$affected = $this->process_bulk_action();

		$total_items = Perfecty_Push_Lib_Db::get_notifications_total();

		$paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] ) - 1 ) : 0;
		$orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_REQUEST['orderby'] : 'creation_time';
		$order   = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array( 'asc', 'desc' ) ) ) ? $_REQUEST['order'] : 'desc';

		$notifications = Perfecty_Push_Lib_Db::get_notifications( $paged, $per_page, $orderby, $order, ARRAY_A );
		$this->items   = (array) $notifications;

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
