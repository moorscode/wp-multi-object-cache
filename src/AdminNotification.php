<?php

namespace WPMultiObjectCache;

class AdminNotification {
	/** @var string Type */
	protected $type;

	/** @var string Message */
	protected $message;

	/**
	 * AdminNotification constructor.
	 *
	 * @param string $message
	 * @param string $type
	 */
	public function __construct( $message, $type = 'info' ) {
		$this->type    = $type;
		$this->message = $message;
	}

	/**
	 * Gets the type of the notification.
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Gets the message of the notification.
	 *
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}
}
