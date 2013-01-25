<?php

	if( !class_exists( 'ThemeistsFeatureHighlight' ) )
	{

		class ThemeistsFeatureHighlight extends WP_Widget
		{
		
			
			/**
			 * The name shown in the widgets panel
			 *
			 * @author Richard Tape
			 * @package ThemeistsFeatureHighlight
			 * @since 1.0
			 */
			
			const name 		= 'Themeists Feature Highlight';

			/**
			 * For helping with translations
			 *
			 * @author Richard Tape
			 * @package ThemeistsFeatureHighlight
			 * @since 1.0
			 */

			const locale 	= THEMENAME;

			/**
			 * The slug for this widget, which is shown on output
			 *
			 * @author Richard Tape
			 * @package ThemeistsFeatureHighlight
			 * @since 1.0
			 */
			
			const slug 		= 'ThemeistsFeatureHighlight';
		

			/* ============================================================================ */
		
			/**
			 * The widget constructor. Specifies the classname and description, instantiates
			 * the widget, loads localization files, and includes necessary scripts and
			 * styles. 
			 *
			 * @author Richard Tape
			 * @package ThemeistsFeatureHighlight
			 * @since 1.0
			 * @param None
			 * @return None
			 */
			
			function ThemeistsFeatureHighlight()
			{
		
				//load_plugin_textdomain( self::locale, false, plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . '/lang/' );

		
				$widget_opts = array (

					'classname' => 'ThemeistsFeatureHighlight', 
					'description' => __( 'A title, some text and an image or icon to highlight anything you like', self::locale )

				);

				$control_options = array(

					'width' => '400'

				);

				//Register the widget
				$this->WP_Widget( self::slug, __( self::name, self::locale ), $widget_opts, $control_options );
		
		    	// Load JavaScript and stylesheets
		    	$this->register_scripts_and_styles();
		
			}/* ThemeistsFeatureHighlight() */
		

			/* ============================================================================ */


			/**
			 * Outputs the content of the widget.
			 *
			 * @author Richard Tape
			 * @package ThemeistsFeatureHighlight
			 * @since 1.0
			 * @param (array) $args - The array of form elements
			 * @param (array) $instance - The saved options from the widget controls
			 * @return None
			 */
			

			function widget( $args, $instance )
			{
		
				extract( $args, EXTR_SKIP );
		
				echo $before_widget;
		
					//Get vars
		    		$title					=	$instance['title'];
		    		$subtitle				=	$instance['subtitle'];
		    		$icon_reference				=	$instance['icon_reference'];
		    		$button_link			=	$instance['button_link'];

		    		?>

		    		<div class="feature_container">

		    			<h4><?php echo $title; ?></h4>

		    			<p><?php echo $subtitle; ?></p>

		    			<?php if( !empty( $icon_reference ) ) : ?>
		    				<span class="pictogram"><?php echo $icon_reference; ?></span>
		    			<?php endif; ?>

		    			<a href="<?php echo $button_link ?>" title=""><?php _e( 'More', THEMENAME ); ?></a>

		    		</div>

		    		<?php

				echo $after_widget;
		
			}/* widget() */


			/* ============================================================================ */

		
			/**
			 * Processes the widget's options to be saved.
			 *
			 * @author Richard Tape
			 * @package ThemeistsFeatureHighlight
			 * @since 1.0
			 * @param $new_instance	The previous instance of values before the update.
			 * @param @old_instance	The new instance of values to be generated via the update. 
			 * @return $instance The saved values
			 */
			
			function update( $new_instance, $old_instance )
			{
		
				$instance = $old_instance;
		
		    	$instance['title'] 			= 	$new_instance['title'];
		    	$instance['subtitle'] 		= 	$new_instance['subtitle'];
		    	$instance['icon_reference']		= 	$new_instance['icon_reference'];
		    	$instance['button_link'] 	= 	$new_instance['button_link'];
		    
				return $instance;
		
			}/* update() */


			/* ============================================================================ */


			/**
			 * Generates the administration form for the widget.
			 *
			 * @author Richard Tape
			 * @package ThemeistsFeatureHighlight
			 * @since 1.0
			 * @param $instance	The array of keys and values for the widget.
			 * @return None
			 */
			

			function form( $instance )
			{
		
				$instance = wp_parse_args(

					(array)$instance,
					array(
						'title' => 'This is the title of this feature',
						'subtitle' => 'And this is the nice piece of text underneath that title. It explains your feature.',
						'icon_reference' => '',
						'button_link' => '#'
					)

				);
		
		    	?>
		    	
		    		<p>
						<label for="<?php echo $this->get_field_id( 'title' ); ?>">
							<?php _e( "Title", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'subtitle' ); ?>">
							<?php _e( "Subtitle", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'subtitle' ); ?>" name="<?php echo $this->get_field_name( 'subtitle' ); ?>" value="<?php echo $instance['subtitle']; ?>" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'icon_reference' ); ?>">
							<?php _e( "Icon Code ( see http://entypo.com/characters.php )", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'icon_reference' ); ?>" name="<?php echo $this->get_field_name( 'icon_reference' ); ?>" value="<?php echo $instance['icon_reference']; ?>" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'button_link' ); ?>">
							<?php _e( "Button Link", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'button_link' ); ?>" name="<?php echo $this->get_field_name( 'button_link' ); ?>" value="<?php echo $instance['button_link']; ?>" />
					</p>
		    	
		    	<?php
		
			}/* form() */


			/* ============================================================================ */
		

			/**
			 * Registers and enqueues stylesheets for the administration panel and the
			 * public facing site.
			 *
			 * @author Richard Tape
			 * @package ThemeistsFeatureHighlight
			 * @since 1.0
			 * @param None
			 * @return None
			 */
			

			private function register_scripts_and_styles()
			{

				if( is_admin() )
				{

		      		//$this->load_file('friendly_widgets_admin_js', '/themes/'.THEMENAME.'/admin/js/widgets.js', true);

				}
				else
				{ 

		      		//$this->load_file('friendly_widgets', '/themes/'.THEMENAME.'/theme_assets/js/widgets.js', true);

				}

			}/* register_scripts_and_styles() */


			/* ============================================================================ */


			/**
			 * Helper function for registering and enqueueing scripts and styles.
			 *
			 * @author Richard Tape
			 * @package ThemeistsFeatureHighlight
			 * @since 1.0
			 * @param $name 		The ID to register with WordPress
			 * @param $file_path	The path to the actual file
			 * @param $is_script	Optional argument for if the incoming file_path is a JavaScript source file.
			 * @return None
			 */
			
			function load_file( $name, $file_path, $is_script = false )
			{
		
		    	$url = content_url( $file_path, __FILE__ );
				$file = $file_path;
					
				if( $is_script )
				{

					wp_register_script( $name, $url, '', '', true );
					wp_enqueue_script( $name );

				}
				else
				{

					wp_register_style( $name, $url, '', '', true );
					wp_enqueue_style( $name );

				}
			
			}/* load_file() */
		
		
		}/* class ThemeistsFeatureHighlight */

	}

	//Register The widget
	//register_widget( "ThemeistsFeatureHighlight" );
	add_action( 'widgets_init', create_function( '', 'register_widget( "ThemeistsFeatureHighlight" );' ) );

?>