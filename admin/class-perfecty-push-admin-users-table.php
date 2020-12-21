<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Perfecty_Push_Admin_Users_Table extends WP_List_Table {
	private const MAX_LENGTH = 100;

	function __construct() {
		global $status, $page;

		parent::__construct(
			array(
				'singular' => 'user',
				'plural'   => 'users',
			)
		);
	}

	function column_default( $item, $column_name ) {
		$content = $item[ $column_name ];

		if ( is_string( $content ) && strlen( $content ) > self::MAX_LENGTH ) {
			return substr( $content, 0, self::MAX_LENGTH ) . '...';
		} else {
			return $content;
		}
	}

	function column_uuid( $item ) {
		$action_nonce = wp_create_nonce( 'bulk-' . $this->_args['plural'] );
		$page         = esc_html( sanitize_key( $_REQUEST['page'] ) );

		$actions = array(
			'view'   => sprintf( '<a href="?page=%s&action=%s&id=%s">%s</a>', $page, 'view', $item['id'], 'View' ),
			'delete' => sprintf( '<a href="#" class="perfecty-push-confirm-action" data-page="%s" data-action="%s" data-id="%d" data-nonce="%s">%s</a>', $page, 'delete', $item['id'], $action_nonce, 'Delete' ),
		);

		return sprintf(
			'%s %s',
			$item['uuid'],
			$this->row_actions( $actions )
		);
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />',
			$item['id']
		);
	}

	function column_is_active( $item ) {
		return $item['is_active'] == 1 ? 'Yes' : 'No';
	}

	function column_disabled( $item ) {
		return $item['disabled'] == 1 ? 'Yes' : 'No';
	}

	function get_columns() {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'uuid'          => 'UUID',
			'remote_ip'     => 'IP',
			'endpoint'      => 'Endpoint',
			'is_active'     => 'Active',
			'disabled'      => 'Disabled',
			'creation_time' => 'Registered at',
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'creation_time' => array( 'creation_time', true ),
		);
		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete',
			'cancel' => 'Cancel',
		);
		return $actions;
	}

	function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {
			$nonce = 'bulk-' . $this->_args['plural'];
			if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], $nonce ) ) {
				wp_die( 'Could not verify the action' );
			}

			if ( ! isset( $_REQUEST['id'] ) ) {
				wp_die( 'No params were specified' );
			}

			// validate, sanitize and filter
			$ids = is_array( $_REQUEST['id'] ) ? $_REQUEST['id'] : array( $_REQUEST['id'] );
			$ids = $this->filter_array( $ids );

			Perfecty_Push_Lib_Db::delete_users( $ids );
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
		$hidden   = array( 'perfecty-push-users-bulk', wp_create_nonce( 'perfecty-push-users-bulk' ) );
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$affected = $this->process_bulk_action();

		$total_items = Perfecty_Push_Lib_Db::get_total_users();

		$paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] ) - 1 ) : 0;
		$orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_REQUEST['orderby'] : 'creation_time';
		$order   = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array( 'asc', 'desc' ) ) ) ? $_REQUEST['order'] : 'desc';

		$users       = Perfecty_Push_Lib_Db::get_users( $paged, $per_page, $orderby, $order, false, ARRAY_A );
		$this->items = (array) $users;

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
