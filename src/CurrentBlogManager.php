<?php

namespace WPMultiObjectCache;

class CurrentBlogManager
{
    /**
     * @var int Blog ID
     */
    protected $blogID;

    /**
     * CurrentBlogManager constructor.
     *
     * @param int $blogID Current Blog ID.
     */
    public function __construct($blogID)
    {
        $this->switchToBlog($blogID);
    }

    /**
     * Switches to a specific blog_id.
     *
     * @param int $blogID Blog to switch to.
     */
    public function switchToBlog($blogID)
    {
        $this->blogID = $blogID;
    }

    /**
     * Returns the current blog ID
     *
     * @return int
     */
    public function getBlogID()
    {
        return $this->blogID;
    }
}
