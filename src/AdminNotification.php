<?php

namespace WPMultiObjectCache;

class AdminNotification {
	/** @var AdminNotificationTypeInterface Type */
	protected $type;

	/** @var string Message */
	protected $message;

	/**
	 * AdminNotification constructor.
	 *
	 * @param AdminNotificationTypeInterface $type
	 * @param string                         $message
	 */
	public function __construct( AdminNotificationTypeInterface $type, $message ) {
		$this->type    = $type;
		$this->message = $message;
	}

	/**
	 * Gets the type of the notification.
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type->get();
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
