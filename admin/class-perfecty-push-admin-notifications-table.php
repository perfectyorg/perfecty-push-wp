<?php

use Perfecty_Push_Lib_Log as Log;

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

	function column_created_at( $item ) {
		$action_nonce = wp_create_nonce( 'bulk-' . $this->_args['plural'] );
		$page         = esc_html( sanitize_key( $_REQUEST['page'] ) );

		$actions = array(
			'view'   => sprintf( '<a href="?page=%s&action=%s&id=%s">%s</a>', $page, 'view', $item['id'], esc_html__( 'View', 'perfecty-push-notifications' ) ),
			'delete' => sprintf( '<a href="#" class="perfecty-push-confirm-action" data-page="%s" data-action="%s" data-id="%d" data-nonce="%s">%s</a>', $page, 'delete', $item['id'], $action_nonce, esc_html__( 'Delete', 'perfecty-push-notifications' ) ),
		);
		if ( $item['status'] == Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_RUNNING || $item['status'] == Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED ) {
			$actions['cancel'] = sprintf( '<a href="#" class="perfecty-push-confirm-action" data-page="%s" data-action="%s" data-id="%d" data-nonce="%s">%s</a>', $page, 'cancel', $item['id'], $action_nonce, esc_html__( 'Cancel', 'perfecty-push-notifications' ) );
		}
		if ( $item['status'] == Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_CANCELED || $item['status'] == Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_FAILED ) {
			$actions['retry'] = sprintf( '<a href="#" class="perfecty-push-confirm-action" data-page="%s" data-action="%s" data-id="%d" data-nonce="%s">%s</a>', $page, 'retry', $item['id'], $action_nonce, esc_html__( 'Retry', 'perfecty-push-notifications' ) );
		}

		return sprintf(
			'%s %s',
			get_date_from_gmt( $item['created_at'] ),
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
		$page = esc_html( sanitize_key( $_REQUEST['page'] ) );

		$actions     = array(
			'view' => sprintf( '<a href="?page=%s&action=%s&id=%s">%s</a>', $page, 'view', $item['id'], esc_html__( 'View details', 'perfecty-push-notifications' ) ),
		);
		$row_actions = $this->row_actions( $actions );

		$payload = json_decode( $item['payload'] );
		$body    = $this->limit_text( $payload->body );
		$title   = $payload->title;

		return sprintf( esc_html__( 'Title: %1$s %2$s Content: %3$s %4$s %5$s', 'perfecty-push-notifications' ), $title, '<br />', $body, '<br />', $row_actions );
	}

	function column_status( $item ) {
		if ( $item['status'] == Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED ) {
			return sprintf(
				'%s %s',
				$item['status'],
				'<br />' . esc_html__( 'at', 'perfecty-push-notifications' ) . ' ' . get_date_from_gmt( $item['scheduled_at'] )
			);
		} else {
			return $item['status'];
		}
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
			'cb'         => '<input type="checkbox" />',
			'created_at' => esc_html__( 'Date', 'perfecty-push-notifications' ),
			'payload'    => esc_html__( 'Payload', 'perfecty-push-notifications' ),
			'status'     => esc_html__( 'Status', 'perfecty-push-notifications' ),
			'total'      => esc_html__( 'Total', 'perfecty-push-notifications' ),
			'succeeded'  => esc_html__( 'Succeeded', 'perfecty-push-notifications' ),
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'created_at' => array( 'created_at', true ),
			'total'      => array( 'total', true ),
			'succeeded'  => array( 'succeeded', true ),
			'status'     => array( 'status', true ),
		);
		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(
			'delete' => esc_html__( 'Delete', 'perfecty-push-notifications' ),
			'cancel' => esc_html__( 'Cancel', 'perfecty-push-notifications' ),
		);
		return $actions;
	}

	function process_bulk_action() {
		$action = $this->current_action();
		if ( in_array( $action, array( 'delete', 'cancel', 'retry' ) ) ) {
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
					Perfecty_Push_Lib_Db::delete_notifications( $ids );
					break;
				case 'cancel':
					$this->mark_notifications_cancel( $ids );
					break;
				case 'retry':
					$this->retry_notifications( $ids );
					break;
			}
		}
	}

	function mark_notifications_cancel( $ids ) {
		foreach ( $ids as $id ) {
			$notification              = Perfecty_Push_Lib_Db::get_notification( $id );
			$notification->status      = Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_CANCELED;
			$notification->finished_at = current_time( 'mysql', 1 );
			$notification->is_taken    = 0;
			Perfecty_Push_Lib_Db::update_notification( $notification );

			$scheduled_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $notification->scheduled_at );
			Perfecty_Push_Lib_Push_Server::unschedule_job( $id, $scheduled_date->getTimestamp() );
			Log::info( 'Cancelling job id=' . $id );
		}
	}

	function retry_notifications( $ids ) {
		foreach ( $ids as $id ) {
			$scheduled_time                  = time();
			$notification                    = Perfecty_Push_Lib_Db::get_notification( $id );
			$notification->status            = Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_RUNNING;
			$notification->is_taken          = 0;
			$notification->finished_at       = null;
			$notification->last_execution_at = current_time( 'mysql', 1 );
			$notification->scheduled_at      = date( 'Y-m-d H:i:s', $scheduled_time );
			Perfecty_Push_Lib_Db::update_notification( $notification );

			Perfecty_Push_Lib_Push_Server::schedule_job( $id, $scheduled_time );
			Log::info( 'Retrying job id=' . $id );
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

		$total_items = Perfecty_Push_Lib_Db::get_notifications_total();

		$paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] ) - 1 ) : 0;
		$orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_REQUEST['orderby'] : 'created_at';
		$order   = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array( 'asc', 'desc' ) ) ) ? $_REQUEST['order'] : 'desc';

		$notifications = Perfecty_Push_Lib_Db::get_notifications( $paged * $per_page, $per_page, $orderby, $order, ARRAY_A );
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
