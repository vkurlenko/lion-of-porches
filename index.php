<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define( 'WP_USE_THEMES', true );

require_once (dirname( __FILE__ ) . '/wp-content/themes/lion-of-porches/Crm.php');
require_once (dirname( __FILE__ ) . '/wp-content/themes/lion-of-porches/WooHelper.php');

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
