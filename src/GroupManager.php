<?php

namespace MultiObjectCache\Cache;

class GroupManager {
	/** @var array */
	protected $group_aliases = array();

	/**
	 * Adds an alias to a group, so the same controller will be used.
	 *
	 * @param string $group Group to add an alias for.
	 * @param string $alias Alias of the group.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function add_alias( $group, $alias ) {
		if ( isset( $this->group_aliases[ $alias ] ) && $this->group_aliases[ $alias ] === $group ) {
			throw new \InvalidArgumentException( sprintf( '%s has been aliased to %s already.', $alias, $group ) );
		}

		$this->group_aliases[ $group ] = $alias;
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
