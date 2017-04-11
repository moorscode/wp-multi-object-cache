<?php

namespace WPMultiObjectCache;

class AdminNotificationTypeSuccess implements AdminNotificationTypeInterface {
	public function get() {
		return 'success';
	}
}
