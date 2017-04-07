<?php

namespace MultiObjectCache\Cache;

class CurrentBlogManager {
	/** @var int Blog ID */
	protected $blog_id;

	/**
	 * CurrentBlogManager constructor.
	 *
	 * @param int $blog_id Current Blog ID.
	 */
	public function __construct( $blog_id ) {
		$this->switch_to_blog( $blog_id );
	}

	/**
	 * Switches to a specific blog_id.
	 *
	 * @param int $blog_id Blog to switch to.
	 */
	public function switch_to_blog( $blog_id ) {
		$this->blog_id = $blog_id;
	}

	/**
	 * Returns the current blog ID
	 *
	 * @return int
	 */
	public function get_blog_id() {
		return $this->blog_id;
	}
}