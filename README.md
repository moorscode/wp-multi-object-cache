[![Build Status](https://travis-ci.org/moorscode/wp-multi-object-cache.svg?branch=master)](https://travis-ci.org/moorscode/wp-multi-object-cache)

# wp-multi-object-cache
Configure per "group" what cache implementation should be used.

WordPress groups are:
- transient
- options
- users
- user_meta
- userlogins
- counts
- posts
- post_meta
- themes
- comment
- site-transient
- site-options
- non-persistent

Currently implemented services:
- Memcached
- Redis
- Predis
- PHP (non-persistent)

Upcoming services:
- Memcache
- _Create issue (or a PR) to send a request_

## Example configuration
In the configuration file the `transient` and `site-transient` groups are stored in Memcached, `non-persistent` group (and aliases of this group) are stored in PHP, while the rest of the groups are stored in Redis.

## Requirements
- Composer
- PHP 5.4
