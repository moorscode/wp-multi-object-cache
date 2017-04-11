<?php

namespace WPMultiObjectCache;

class AdminNotificationTypeError implements AdminNotificationTypeInterface {
	public function get() {
		return 'error';
	}
}
