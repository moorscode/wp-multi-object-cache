<?php

class Object_Cache_Manager
{
    /** @var array */
    protected static $group_aliases = array();
    /** @var  array */
    protected static $controllers = array();
    /** @var array */
    protected static $controller_groups = array();

    /** @var int Blog ID */
    protected static $blog_id;

    public static function initialize()
    {
        // read configuration
        $config = [
            'controllers' => [
                // Default/fallback controller.
                'Redis_Controller' => [
                    'data' => [
                        'config' => [
                            'ip' => '127.0.0.1',
                            'port' => '1112'
                        ]
                    ],
                    'groups' => [
                        ''
                    ]
                ],
                // Use Memcached controller for transients.
                'Memcache_Controller' => [
                    'data' => [
                        'config' => [
                            'ip' => '127.0.0.1',
                            'port' => '1112'
                        ]
                    ],
                    'groups' => [
                        'site-transient'
                    ],
                ],
                // Use Non Persistent Controller.
                'Non_Persistent_Controller' => [
                    'data' => [
                        'config' => []
                    ],
                    'groups' => [
                        'non-persistent'
                    ],

                ]
            ]
        ];

        self::switch_to_blog(get_current_blog_id());
        self::register_controllers($config['controllers']);
    }

    /**
     * @param Object_Cache_Controller_Interface $controller
     * @param string $group
     */
    public static function assign_group(Object_Cache_Controller_Interface $controller, $group)
    {
        self::$controller_groups[$group] = $controller;
    }

    /**
     * @param string $group
     *
     * @return Object_Cache_Controller_Interface
     */
    public static function get_controller($group = '')
    {
        if (isset(self::$group_aliases[$group])) {
            $group = self::$group_aliases[$group];
        }

        $controller = new Null_Controller(array());
        if (isset(self::$controller_groups[$group])) {
            $controller = self::$controller_groups[$group];
        }

        return new Object_Cache_Key_Controller($controller);
    }

    /**
     * @param int $blog_id Blog to switch to
     */
    public static function switch_to_blog($blog_id)
    {
        self::$blog_id = $blog_id;
    }

    /**
     * @param string $group
     * @param string $alias
     */
    public static function group_alias($group, $alias)
    {
        self::$group_aliases[$group] = $alias;
    }

    /**
     * @return mixed
     */
    private static function is_multisite()
    {
        static $multisite;

        if (null === $multisite) {
            $multisite = is_multisite();
        }

        return $multisite;
    }

    public static function get_key_format()
    {
        $blog_format = '%s';
        if (self::is_multisite()) {
            $blog_format = self::$blog_id . ':%s';
        }

        return $blog_format;
    }

    /**
     * @param array $controllers
     */
    protected static function register_controllers($controllers)
    {
        // Register controllers
        foreach ($controllers as $controller => $data) {
            // @todo do class exists checks etc.
            $controller_instance = new $controller($data['config']);
            foreach ($data['groups'] as $group) {
                self::assign_group($controller_instance, $group);
            }
        }
    }
}
