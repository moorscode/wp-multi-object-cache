<?php

namespace WPMultiObjectCache\Admin;

class Notifier
{
    /**
     * @var Notification[] List of notifications
     */
    protected $notifications = [];

    /**
     * Registers WordPress hooks.
     */
    public function addHooks()
    {
        add_action('admin_notices', [$this, 'displayNotices']);
    }

    /**
     * Adds a notification.
     *
     * @param Notification $notification
     */
    public function add(Notification $notification)
    {
        $this->notifications[] = $notification;
    }

    /**
     * Displays received notifications.
     */
    public function displayNotices()
    {
        if (empty($this->notifications)) {
            return;
        }

        array_map([$this, 'displayNotice'], $this->notifications);
    }

    /**
     * Outputs a notification.
     *
     * @param Notification $notification
     */
    private function displayNotice(Notification $notification)
    {
        printf(
            '<div class="notice notice-%1$s"><p>%2$s</p></div>',
            esc_attr($notification->getType()),
            esc_html($notification->getMessage())
        );
    }
}
