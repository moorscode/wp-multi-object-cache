<?php

namespace WPMultiObjectCache;

class AdminNotifier {
	/** @var AdminNotification[] List of notifications */
	protected $notifications = [];

	/**
	 * Registers WordPress hooks.
	 */
	public function add_hooks() {
		if ( is_admin() ) {
			add_action( 'admin_notices', [ $this, 'display_notices' ] );
		}
	}

	/**
	 * Adds a notification.
	 *
	 * @param AdminNotification $notification
	 */
	public function add( AdminNotification $notification ) {
		$this->notifications[] = $notification;
	}

	/**
	 * Displays received notifications.
	 */
	public function display_notices() {
		if ( empty( $this->notifications ) ) {
			return;
		}

		array_map( [ $this, 'display_notice' ], $this->notifications );
	}

	/**
	 * Outputs a notification.
	 *
	 * @param AdminNotification $notification
	 */
	private function display_notice( AdminNotification $notification ) {
		printf( '<div class="notice notice-%1$s"><p>%2$s</p></div>', esc_attr( $notification->getType() ), esc_html( $notification->getMessage() ) );
	}
}
