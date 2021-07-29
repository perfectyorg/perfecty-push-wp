<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Perfecty_Push_Admin_Categories_Table extends WP_List_Table {
	private const MAX_LENGTH = 100;

	function __construct() {
		global $status, $page;

		parent::__construct(
			array(
				'singular' => 'category',
				'plural'   => 'categories',
			)
		);
	}

	function column_default( $item, $column_name ) {
		return $this->limit_text( $item[ $column_name ] );
	}

	function column_created_at( $item ) {
		$action_nonce = wp_create_nonce( 'bulk-' . $this->_args['plural'] );
		$page         = esc_html( sanitize_key( $_REQUEST['page'] ) );

		$actions = array(
			'delete' => sprintf( '<a href="#" class="perfecty-push-confirm-action" data-page="%s" data-action="%s" data-id="%d" data-nonce="%s">%s</a>', $page, 'delete', $item['id'], $action_nonce, esc_html__( 'Delete', 'perfecty-push-notifications' ) ),
		);

		return sprintf(
			'%s %s',
			$item['created_at'],
			$this->row_actions( $actions )
		);
	}
	
	function column_name( $item ) {
		$page         = esc_html( sanitize_key( $_REQUEST['page'] ) );

		return sprintf(
			'%s',
			$item['name']
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
			'cb'         => '<input type="checkbox" />',
			'created_at' => esc_html__( 'Date', 'perfecty-push-notifications' ),
			'name'    => esc_html__( 'Name', 'perfecty-push-notifications' ),
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'created_at' => array( 'created_at', true ),
			'name'    => esc_html__( 'Name', 'perfecty-push-notifications' ),
		);
		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(
			'delete' => esc_html__( 'Delete', 'perfecty-push-notifications' ),
		);
		return $actions;
	}

	function process_bulk_action() {
		$action = $this->current_action();
		if ( in_array( $action, array( 'delete', 'cancel' ) ) ) {
			$nonce = 'bulk-' . $this->_args['plural'];
			if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], $nonce ) ) {
				wp_die( esc_html__( 'Could not verify the action', 'perfecty-push-notifications' ) );
			}

			if ( ! isset( $_REQUEST['id'] ) ) {
				wp_die( esc_html__( 'No params were specified', 'perfecty-push-notifications' ) );
			}

			// validate, sanitize and filter
			$ids = is_array( $_REQUEST['id'] ) ? $_REQUEST['id'] : array( $_REQUEST['id'] );
			$ids = $this->filter_array( $ids );

			switch ( $action ) {
				case 'delete':
					Perfecty_Push_Lib_Db::delete_categories( $ids );
					break;
				
			}
		}
	}

	function filter_array( $ids ) {
		$ids = array_map(
			function( $item ) {
				$item = sanitize_key( $item );
				return intval( $item );
			},
			$ids
		);
		return $ids;
	}

	function prepare_items() {
		$per_page = 10;

		$columns  = $this->get_columns();
		$hidden   = array( 'perfecty-push-notifications-bulk', wp_create_nonce( 'perfecty-push-notifications-bulk' ) );
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$affected = $this->process_bulk_action();

		$total_items = Perfecty_Push_Lib_Db::get_categories_total();

		$paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] ) - 1 ) : 0;
		$orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_REQUEST['orderby'] : 'created_at';
		$order   = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array( 'asc', 'desc' ) ) ) ? $_REQUEST['order'] : 'desc';

		$categories = Perfecty_Push_Lib_Db::get_admin_categories( $paged * $per_page, $per_page, $orderby, $order, ARRAY_A );
		$this->items   = (array) $categories;

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
