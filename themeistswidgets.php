<?php
/*
Plugin Name: Themeists Widgets
Plugin URI: #
Description: Just like our custom post types, our widgets are free-to-use whichever theme you are using. If you do switch themes, you may need to copy the widget styling section from a Themeists stylesheet into your other theme.
Version: 1.0
Author: Themeists
Author URI: #
License: GPL2
*/

	if( !class_exists( 'ThemeistsWidgets' ) ):


		/**
		 * Adds our custom widgets based on what the theme supports. If we are not using a Themeists theme, then we just
		 * register all of the widgets. Each widget is in its own subfolder of this plugin.
		 *
		 * @author Richard Tape
		 * @package Incipio
		 * @since 1.0
		 */
		
		class ThemeistsWidgets
		{


			/**
			 * We might not be using a themeists theme (which means we can't add anything to the options panel). By default,
			 * we'll say we are not. We check if the theme's author is Themeists to set this to true during instantiation.
			 *
			 * @author Richard Tape
			 * @package ThemeistsWidgets
			 * @since 1.0
			 */
			
			var $using_themeists_theme = false;


			/**
			 * Initialise ourselves and do a bit of setup
			 *
			 * @author Richard Tape
			 * @package ThemeistsWidgets
			 * @since 1.0
			 * @param None
			 * @return None
			 */

			function ThemeistsWidgets()
			{
				
				add_action( 'widgets_init', array( &$this, 'register_widgets' ), 1 );

				$theme_data = wp_get_theme();
				$theme_author = $theme_data->display( 'Author', false );

				if( strtolower( trim( $theme_author ) ) == "themeists" )
					$this->using_themeists_theme = true;

				if( $this->using_themeists_theme )
					add_action( 'after_setup_theme', 			array( &$this, 'add_new_image_size' ) );

			}/* ThemeistsWidgets() */


			/**
			 * Method to call the widget registration for each widget that the current theme supports. If we're not using
			 * a themeists theme, then we register all widgets
			 *
			 * @author Richard Tape
			 * @package ThemeistsWidgets
			 * @since 1.0
			 * @param None
			 * @return None
			 */

			function register_widgets()
			{

				$custom_widgets = array();
				$custom_widgets = get_theme_support( 'custom-widgets' );

				//If there's theme support or we're not on a themeists theme
				if( ( !$this->using_themeists_theme ) || !empty( $custom_widgets ) )
				{

					//Get the array of widgets to initialise. If we're on a themeists theme, it's what the them
					//supports, otherwise it's all of the widgets in the /widgets/ subfolder

					if( !empty( $custom_widgets ) )
					{

						//We're using a theme which has registered support for custom widgets
						foreach( $custom_widgets[0] as $widget_filename )
						{
							require_once( 'widgets/' . $widget_filename . '/' . $widget_filename . '.php' );
						}

					}
					else
					{

						//Parse the /widgets/ subdirectory and require each widget file
						if( $handle = opendir( dirname( __FILE__ ) . '/widgets/' ) )
						{
							
							while( false !== ( $widget_filename = readdir( $handle ) ) )
							{
							
								if( $widget_filename != "." && $widget_filename != ".." )
								{
									require_once( 'widgets/' . $widget_filename . '/' . $widget_filename . '.php' );
								}
							
							}
							
							closedir( $handle );
						
						}

					}

				}

			}/* register_widgets() */


			/**
			 * Add a new image size
			 *
			 * @author Richard Tape
			 * @package 
			 * @since 1.0
			 */

			function add_new_image_size()
			{

				add_image_size( 'lbp_thumb', 400, 233, true );

			}/* add_new_image_size() */
			


		}/* class ThemeistsWidgets */


	endif;

	$themeistswidgets = new ThemeistsWidgets;

?>