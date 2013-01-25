<?php
/*
Plugin Name: Themeists MailChimp Widget
Plugin URI: http://themeists.com/plugins/themeists-mailchimp-widget/
Description: Easily add a MailChimp subscription widget to your site
Author: Themeists
Version: 1.0
Author URI: https://themeists.com
License: GPL2
*/

/*
    This widget is taken mostly from the 'Mailchimp Widget' produced brilliantly by James Lafferty under the GPL at https://github.com/kalchas
*/

/**
 * Set up the autoloader.
 */

set_include_path( get_include_path() . PATH_SEPARATOR . realpath( dirname( __FILE__ ) . '/lib/' ) );

spl_autoload_extensions('.class.php');

if( !function_exists( 'buffered_autoloader' ) )
{

	function buffered_autoloader ($c) {
		try
        {
			spl_autoload($c);
		}
        catch( Exception $e )
        {
			$message = $e->getMessage();
			return $message;
		}

	}

}

spl_autoload_register('buffered_autoloader');

/**
 * Get the plugin object. All the bookkeeping and other setup stuff happens here.
 */

$ns_mc_plugin = NS_MC_Plugin::get_instance();

register_deactivation_hook( __FILE__, array( &$ns_mc_plugin, 'remove_options' ) );
?>
