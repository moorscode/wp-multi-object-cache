<?php

namespace MultiObjectCache\Cache;

class GroupManager {
	/** @var array Aliases */
	protected $group_aliases = [];

	/**
	 * Adds an alias to a group, so the same controller will be used.
	 *
	 * @param string $group Group to add an alias for.
	 * @param string $alias Alias of the group.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function add_alias( $group, $alias ) {
		$this->group_aliases[ $alias ] = $group;
	}

	/**
	 * Returns the usable group from a potential alias
	 *
	 * @param string $group Group to de-alias.
	 *
	 * @return string
	 */
	public function get( $group ) {
		while ( isset( $this->group_aliases[ $group ] ) ) {
			$group = $this->group_aliases[ $group ];
		}

		return $group;
	}
}
