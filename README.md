[![Build Status](https://travis-ci.org/moorscode/wp-multi-object-cache.svg?branch=master)](https://travis-ci.org/moorscode/wp-multi-object-cache)

# wp-multi-object-cache
Configure per "group" what cache implementation should be used.

## Requirements
- Composer
- PHP 5.4

# Installation

1. Clone this repository to the `wp-content/mu-plugins` folder in your WordPress installation.
    1. Create the folder if it does not exist already.
1. Copy (or symlink) `object-cache.php` to the `wp-content` folder.
    1. If you have not used `wp-multi-object-cache` as folder name in the `mu-plugins` folder, update the `$base_path` variable in the `object-cache.php` to match the chosen directory name.
1. Create a configuration file at `config/object-cache.config.php`.
    1. An example configuration file has been provided in the `config` directory.
1. Run `composer install` in the repository directory.
    1. Install composer if needed, for instructions see https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx

## Example configuration
In the configuration file the `transient` and `site-transient` groups are stored in Memcached, `non-persistent` group (and aliases of this group) are stored in PHP, while the rest of the groups are stored in Redis.

It is very simple to configure one or more dedicated (Redis|Memcached) services to hold all user-related, options or posts cache.

#### Two Redis and a Memcached container
For an example which places default data in `Redis #1`, **user** data inside `Redis #2` and all **transients** in a `memcached` instance see https://gist.github.com/moorscode/19cc541522037ef439a785646b2628f2

This example works with the following docker-compose configuration: https://gist.github.com/moorscode/0eaeeb05d966bee7051d800967edd68a

1. Create a directory and download the latest WordPress files inside it https://wordpress.org/latest.zip
1. Run `docker-compose up` inside the directory to spin up the docker instances
    1. To learn more about this configuration see http://docs.docker4wordpress.org/en/latest/
1. Follow the installation instructions in this repository
1. _Optionally._ Install the `Query Monitor` WordPress plugin to see how many queries are executed
1. **Note:** The first time visiting the site will be slow because the cache has not been filled yet.


## You say "groups"?

In the WordPress cache layer, data is divided in groups.
This results in prefixes per keys, so keys will not overlap. It also provides the basis to support a per-group cache implementation.
 
The groups used in WordPress are the following:
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

## Cache implementations?
Currently implemented services:
- Memcached
- Redis
- Predis
- PHP (non-persistent)
- Memcache _(untested)_

Upcoming services:
- _Create issue (or a PR) to send a request_


# Choices and considerations
The project has been setup with a couple of requirements, PHP 5.4 and composer. In WordPress the main philosophy is that it should be easy to use for everyone.
I understand and support this point of view but it should not reduce the quality or functionality of a project.

It it a decision I had to get used to for a moment.

While looking at the current implementations of Object Cache projects for WordPress I came across a couple of projects (which are mentioned below).
In these implementations the default WordPress Cache API has been enriched with additional functions.
I have decided to only support the basic API to allow for better integration with the dynamic approach of this project.
It is meant as a core enhancement to allow scalability and control over the location (cached) data is stored. 

# Credits and thanks

Thanks to the amazing work done by https://github.com/Nyholm and https://github.com/aequasi setting up the PHP Cache initiative with PSR-6 and Simple Cache implementations for all major Cache methods.
 See https://github.com/php-cache/ for code and http://www.php-cache.com/ for more information.
 
### Redis
Configuration inspiration taken from the https://wordpress.org/plugins/redis-cache/ and https://github.com/ericmann/Redis-Object-Cache projects.
 
### Memcached
Configuration inspiration taken from the https://github.com/tollmanz/wordpress-pecl-memcached-object-cache project.