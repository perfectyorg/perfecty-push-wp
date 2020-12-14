<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Perfecty_Push_Admin_Notifications_Table extends WP_List_Table {
	private const MAX_LENGTH = 100;

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
		return $this->limit_text( $item[ $column_name ] );
	}

	function column_creation_time( $item ) {
		$action_nonce = wp_create_nonce( 'bulk-' . $this->_args['plural'] );
		$actions      = array(
			'view'   => sprintf( '<a href="?page=%s&action=%s&id=%s">%s</a>', $_REQUEST['page'], 'view', $item['id'], 'View' ),
			'delete' => sprintf( '<a href="#" class="perfecty-push-confirm-action" data-page="%s" data-action="%s" data-id="%d" data-nonce="%s">%s</a>', $_REQUEST['page'], 'delete', $item['id'], $action_nonce, 'Delete' ),
		);
		if ( $item['status'] == Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_RUNNING || $item['status'] == Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED ) {
			$actions['cancel'] = sprintf( '<a href="#" class="perfecty-push-confirm-action" data-page="%s" data-action="%s" data-id="%d" data-nonce="%s">%s</a>', $_REQUEST['page'], 'cancel', $item['id'], $action_nonce, 'Cancel' );
		}

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

	function column_payload( $item ) {
		$actions     = array(
			'view' => sprintf( '<a href="?page=%s&action=%s&id=%s">%s</a>', $_REQUEST['page'], 'view', $item['id'], 'View details' ),
		);
		$row_actions = $this->row_actions( $actions );

		$payload = json_decode( $item['payload'] );
		$body    = $this->limit_text( $payload->body );
		$title   = $payload->title;

		return sprintf( 'Title: %s<br /> Content: %s<br />%s', $title, $body, $row_actions );
	}

	/**
	 * Limit the max length of the text and adds '...' if it exceeds it
	 *
	 * @param string $content Content to limit
	 * @return string
	 */
	function limit_text( $content ) {
		if ( is_string( $content ) && strlen( $content ) > self::MAX_LENGTH ) {
			return substr( $content, 0, self::MAX_LENGTH ) . '...';
		} else {
			return $content;
		}
	}

	function get_columns() {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'creation_time' => 'Date',
			'payload'       => 'Payload',
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

			// filter data
			$ids = is_array( $_REQUEST['id'] ) ? $_REQUEST['id'] : array( $_REQUEST['id'] );
			$ids = array_map(
				function( $item ) {
					return intval( $item );
				},
				$ids
			);

			Perfecty_Push_Lib_Db::delete_notifications( $ids );
		}
	}

	function prepare_items() {
		$per_page = 10;

		$columns  = $this->get_columns();
		$hidden   = array( 'perfecty-push-notifications-bulk', wp_create_nonce( 'perfecty-push-notifications-bulk' ) );
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
