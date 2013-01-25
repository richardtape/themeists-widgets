<?php

	if( !class_exists( 'ThemeistsLogoShowcase' ) )
	{

		class ThemeistsLogoShowcase extends WP_Widget
		{
		
			
			/**
			 * The name shown in the widgets panel
			 *
			 * @author Richard Tape
			 * @package ThemeistsLogoShowcase
			 * @since 1.0
			 */
			
			const name 		= 'Themeists Logo Showcase';

			/**
			 * For helping with translations
			 *
			 * @author Richard Tape
			 * @package ThemeistsLogoShowcase
			 * @since 1.0
			 */

			const locale 	= THEMENAME;

			/**
			 * The slug for this widget, which is shown on output
			 *
			 * @author Richard Tape
			 * @package ThemeistsLogoShowcase
			 * @since 1.0
			 */
			
			const slug 		= 'ThemeistsLogoShowcase';
		

			/* ============================================================================ */
		
			/**
			 * The widget constructor. Specifies the classname and description, instantiates
			 * the widget, loads localization files, and includes necessary scripts and
			 * styles. 
			 *
			 * @author Richard Tape
			 * @package ThemeistsLogoShowcase
			 * @since 1.0
			 * @param None
			 * @return None
			 */
			
			function ThemeistsLogoShowcase()
			{
		
				//load_plugin_textdomain( self::locale, false, plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . '/lang/' );

		
				$widget_opts = array (

					'classname' => 'ThemeistsLogoShowcase', 
					'description' => __( 'A simple widget to showcase your client\'s logos', self::locale )

				);

				$control_options = array(

					'width' => '400'

				);

				//Register the widget
				$this->WP_Widget( self::slug, __( self::name, self::locale ), $widget_opts, $control_options );
		
		    	// Load JavaScript and stylesheets
		    	$this->register_scripts_and_styles();
		
			}/* ThemeistsLogoShowcase() */
		

			/* ============================================================================ */


			/**
			 * Outputs the content of the widget.
			 *
			 * @author Richard Tape
			 * @package ThemeistsLogoShowcase
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
		    		$num_to_show			=	$instance['num_to_show'];

		    		echo $before_title . $title . $after_title;

		    		$cols = incipio_convert_number_to_words( 12/$num_to_show );

		    		

			    		//We need to get x-number (where x = $num_to_show) of 'project' post type that
			    		//contain logos. Logos are stored as a custom field with a key of _logo
			    		$query_args = array(
			    			'post_type' => 'project',
			    			'meta_key' => '_logo',
			    			'posts_per_page' => $num_to_show
			    		);

			    		$latest_logos = new WP_Query( $query_args );

			    		if( $latest_logos->have_posts() ) :

				    		echo '<div class="logo_showcase row show_'. $num_to_show . '">';

				    		while ( $latest_logos->have_posts() ) : $latest_logos->the_post();

				    		$image_id = get_post_meta( get_the_ID(), '_logo', true );
				    		$image_attr = wp_get_attachment_image_src( $image_id, 'logo_6up_146_40' );
				    		$image_url = $image_attr[0];

				    		

				    		?>

				    		<div class="<?php echo $cols; ?> columns">
				    		
				    			<a href="<?php the_permalink(); ?>" title=""><img src="<?php echo $image_url; ?>" alt="" /></a>
				    		
				    		</div><!-- .cols -->

				    		<?php

			    			endwhile; wp_reset_postdata();

			    		endif;

		    		echo '</div>';

				echo $after_widget;
		
			}/* widget() */


			/* ============================================================================ */

		
			/**
			 * Processes the widget's options to be saved.
			 *
			 * @author Richard Tape
			 * @package ThemeistsLogoShowcase
			 * @since 1.0
			 * @param $new_instance	The previous instance of values before the update.
			 * @param @old_instance	The new instance of values to be generated via the update. 
			 * @return $instance The saved values
			 */
			
			function update( $new_instance, $old_instance )
			{
		
				$instance = $old_instance;
		
		    	$instance['title'] 			= 	$new_instance['title'];
		    	$instance['num_to_show'] 	= 	$new_instance['num_to_show'];
		    
				return $instance;
		
			}/* update() */


			/* ============================================================================ */


			/**
			 * Generates the administration form for the widget.
			 *
			 * @author Richard Tape
			 * @package ThemeistsLogoShowcase
			 * @since 1.0
			 * @param $instance	The array of keys and values for the widget.
			 * @return None
			 */
			

			function form( $instance )
			{
		
				$instance = wp_parse_args(

					(array)$instance,
					array(
						'title' => 'Clients',
						'num_to_show' => '6',
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
						<label for="<?php echo $this->get_field_id( 'num_to_show' ); ?>">
							<?php _e( "Number of Logos to show", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'num_to_show' ); ?>" name="<?php echo $this->get_field_name( 'num_to_show' ); ?>" value="<?php echo $instance['num_to_show']; ?>" />
					</p>
		    	
		    	<?php
		
			}/* form() */


			/* ============================================================================ */
		

			/**
			 * Registers and enqueues stylesheets for the administration panel and the
			 * public facing site.
			 *
			 * @author Richard Tape
			 * @package ThemeistsLogoShowcase
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
			 * @package ThemeistsLogoShowcase
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
		
		
		}/* class ThemeistsLogoShowcase */

	}

	//Register The widget
	//register_widget( "ThemeistsLogoShowcase" );
	add_action( 'widgets_init', create_function( '', 'register_widget( "ThemeistsLogoShowcase" );' ) );

?>