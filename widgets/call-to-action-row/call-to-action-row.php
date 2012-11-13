<?php

	if( !class_exists( 'themeists_call_to_action_row' ) )
	{

		class themeists_call_to_action_row extends WP_Widget
		{
		
			
			/**
			 * The name shown in the widgets panel
			 *
			 * @author Richard Tape
			 * @package themeists_call_to_action_row
			 * @since 1.0
			 */
			
			const name 		= 'Themeists Call To Action Row';

			/**
			 * For helping with translations
			 *
			 * @author Richard Tape
			 * @package themeists_call_to_action_row
			 * @since 1.0
			 */

			const locale 	= THEMENAME;

			/**
			 * The slug for this widget, which is shown on output
			 *
			 * @author Richard Tape
			 * @package themeists_call_to_action_row
			 * @since 1.0
			 */
			
			const slug 		= 'themeists_call_to_action_row';
		

			/* ============================================================================ */
		
			/**
			 * The widget constructor. Specifies the classname and description, instantiates
			 * the widget, loads localization files, and includes necessary scripts and
			 * styles. 
			 *
			 * @author Richard Tape
			 * @package themeists_call_to_action_row
			 * @since 1.0
			 * @param None
			 * @return None
			 */
			
			function themeists_call_to_action_row()
			{
		
				//load_plugin_textdomain( self::locale, false, plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . '/lang/' );

		
				$widget_opts = array (

					'classname' => 'themeists_call_to_action_row', 
					'description' => __( 'A title and optional subtitle and a call to action button', self::locale )

				);

				$control_options = array(

					'width' => '400'

				);

				//Register the widget
				$this->WP_Widget( self::slug, __( self::name, self::locale ), $widget_opts, $control_options );
		
		    	// Load JavaScript and stylesheets
		    	$this->register_scripts_and_styles();
		
			}/* themeists_call_to_action_row() */
		

			/* ============================================================================ */


			/**
			 * Outputs the content of the widget.
			 *
			 * @author Richard Tape
			 * @package themeists_call_to_action_row
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
		    		$button_text			=	$instance['button_text'];
		    		$button_link			=	$instance['button_link'];

		    		?>

		    		<div class="row cols_no_padding">
		    		
		    			<div class="nine columns cta_title_sub_title">
		    			
		    				<h3><?php echo $title; ?></h3>
		    				<h5><?php echo $subtitle; ?></h5>
		    			
		    			</div><!-- .cols -->

		    			<div class="three columns cta_button">
		    			
		    				<a href="<?php echo $button_link; ?>" class="button" title="<?php echo $button_text; ?>">
		    					<?php echo $button_text; ?>
		    				</a>
		    			
		    			</div><!-- .cols -->
		    		
		    		</div><!-- .row -->

		    		<?php

				echo $after_widget;
		
			}/* widget() */


			/* ============================================================================ */

		
			/**
			 * Processes the widget's options to be saved.
			 *
			 * @author Richard Tape
			 * @package themeists_call_to_action_row
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
		    	$instance['button_text']	= 	$new_instance['button_text'];
		    	$instance['button_link'] 	= 	$new_instance['button_link'];
		    
				return $instance;
		
			}/* update() */


			/* ============================================================================ */


			/**
			 * Generates the administration form for the widget.
			 *
			 * @author Richard Tape
			 * @package themeists_call_to_action_row
			 * @since 1.0
			 * @param $instance	The array of keys and values for the widget.
			 * @return None
			 */
			

			function form( $instance )
			{
		
				$instance = wp_parse_args(

					(array)$instance,
					array(
						'title' => 'This is the title for this widget',
						'subtitle' => 'And this is the (optional) subtitle. We think it looks pretty neat!',
						'button_text' => 'Learn More About It',
						'button_link' => 'http://www.google.com/'
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
						<label for="<?php echo $this->get_field_id( 'button_text' ); ?>">
							<?php _e( "Button Text", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" value="<?php echo $instance['button_text']; ?>" />
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
			 * @package themeists_call_to_action_row
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
			 * @package themeists_call_to_action_row
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
		
		
		}/* class themeists_call_to_action_row */

	}

	//Register The widget
	//register_widget( "themeists_call_to_action_row" );
	add_action( 'widgets_init', create_function( '', 'register_widget( "themeists_call_to_action_row" );' ) );

?>