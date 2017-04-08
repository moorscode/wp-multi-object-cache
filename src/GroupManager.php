<?php

namespace WPMultiObjectCache;

class GroupManager implements GroupManagerInterface {
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
	public function addAlias( $group, $alias ) {
		$this->group_aliases[ $alias ] = $group;
	}

	/**
	 * Returns the usable group from a potential alias
	 *
	 * @param string $group Group to de-alias.
	 *
	 * @return string
	 * @throws \LogicException
	 */
	public function get( $group ) {
		$original_group = $group;

		// Make sure we don't end up in an infinite loop.
		$checked = [];

		while ( array_key_exists( $group, $this->group_aliases ) && ! in_array( $group, $checked, true ) ) {
			$checked[] = $group;
			$group     = $this->group_aliases[ $group ];
		}

		if ( in_array( $group, $checked, true ) ) {
			throw new \LogicException( sprintf( 'Group alias-loop detected on %s for %s', $original_group, $group ) );
		}

		return $group;
	}
}
