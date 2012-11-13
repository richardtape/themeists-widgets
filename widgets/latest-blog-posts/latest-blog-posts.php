<?php

	if( !class_exists( 'themeists_latest_blog_posts' ) )
	{

		class themeists_latest_blog_posts extends WP_Widget
		{
		
			
			/**
			 * The name shown in the widgets panel
			 *
			 * @author Richard Tape
			 * @package themeists_latest_blog_posts
			 * @since 1.0
			 */
			
			const name 		= 'Themeists Latest Blog Posts';

			/**
			 * For helping with translations
			 *
			 * @author Richard Tape
			 * @package themeists_latest_blog_posts
			 * @since 1.0
			 */

			const locale 	= THEMENAME;

			/**
			 * The slug for this widget, which is shown on output
			 *
			 * @author Richard Tape
			 * @package themeists_latest_blog_posts
			 * @since 1.0
			 */
			
			const slug 		= 'themeists_latest_blog_posts';
		

			/* ============================================================================ */
		
			/**
			 * The widget constructor. Specifies the classname and description, instantiates
			 * the widget, loads localization files, and includes necessary scripts and
			 * styles. 
			 *
			 * @author Richard Tape
			 * @package themeists_latest_blog_posts
			 * @since 1.0
			 * @param None
			 * @return None
			 */
			
			function themeists_latest_blog_posts()
			{
		
				//load_plugin_textdomain( self::locale, false, plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . '/lang/' );

		
				$widget_opts = array (

					'classname' => 'themeists_latest_blog_posts', 
					'description' => __( 'A latest posts widget which allows you to choose which post type, the number to show, whether to show an image, comments, date and author.', self::locale )

				);

				$control_options = array(

					'width' => '400'

				);

				//Register the widget
				$this->WP_Widget( self::slug, __( self::name, self::locale ), $widget_opts, $control_options );
		
		    	// Load JavaScript and stylesheets
		    	$this->register_scripts_and_styles();
		
			}/* themeists_latest_blog_posts() */
		

			/* ============================================================================ */


			/**
			 * Outputs the content of the widget.
			 *
			 * @author Richard Tape
			 * @package themeists_latest_blog_posts
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
		    		$title					=	apply_filters( 
		    		                           		'widget_title', 
		    		                           		empty( $instance['title'] ) ? false : $instance['title'], 
		    		                           		$instance, 
		    		                           		self::slug
		    		                           	);

		    		$subtitle				=	apply_filters( 
		    		                           		'widget_title', 
		    		                           		empty( $instance['subtitle'] ) ? false : $instance['subtitle'], 
		    		                           		$instance, 
		    		                           		self::slug
		    		                           	);

		    		//How many posts to show
		    		$num_to_show			=	isset( $instance['num_to_show'] ) ? intval( $instance['num_to_show'] )  : 3;

		    		//What post type to show
		    		$type_to_show			=	isset( $instance['type_to_show'] ) ? $instance['type_to_show'] : 'post';

		    		//Whether to limit to a specific taxonomy or not
		    		$tax_to_show			=	isset( $instance['tax_to_show'] ) ? $instance['tax_to_show'] : false;

		    		//Whether to limit to a specific taxonomy term or not
		    		$term_to_show			=	isset( $instance['term_to_show'] ) ? $instance['term_to_show'] : false;

		    		//Show a "read more" button
		    		$show_more_button		=	isset( $instance['show_more_button'] ) ? $instance['show_more_button'] : false;
		    		$button_text			=	isset( $instance['button_text'] ) ? strip_tags( $instance['button_text'] ) : false;
		    		$button_link			=	isset( $instance['button_link'] ) ? $instance['button_link'] : false;

		    		//Show the thumbnail?
		    		$show_thumbnail			=	isset( $instance['show_thumbnail'] ) ? $instance['show_thumbnail'] : false;

		    		//Show the number of comments?
		    		$show_comments			=	isset( $instance['show_comments'] ) ? $instance['show_comments'] : false;

		    		//Show date
		    		$show_date				=	isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		    		//Show author
		    		$show_author			=	isset( $instance['show_author'] ) ? $instance['show_author'] : false;

		    		//If our like plugin is activated, show the option to show the number of likes for the post
		    		$show_likes				=	isset( $instance['show_likes'] ) ? $instance['show_likes'] : false;

		    		//Now we have our values, we are going to run them through some custom filters so we can modify the output
		    		//externally

		    		?>

		    		<?php echo apply_filters( 'themeists_widget_lbp_container_open', '<div class="row">' ); ?>
		    		
		    			<?php if( $title ) echo $before_title . $title . $after_title; ?>

		    			<?php

		    				/*
								Build the loop query based on the widget settings
								=================================================
		    				*/

							$query_args = array(

								'posts_per_page' => $num_to_show,
								'post_status' => 'publish',

							);

							//If we have a post type selected, add it to the args
							if( $type_to_show )
								$query_args['post_type'] = $type_to_show;

							//If we have a taxonomy selected, add that to the args
							if( $tax_to_show && $term_to_show )
								$query_args['tax_query'] = array( array( 'taxonomy' => $tax_to_show, 'field' => 'slug', 'terms' => $term_to_show ) );

							//Get ourselves a query object **don't forget to run wp_reset_postdata()**
							$lbp = new WP_Query( apply_filters( 'themeists_widget_lbp_query_args', $query_args ) );


							/* ===================================================================
							
							Now the loop is built, we need to output our markup. We check if the 
							current theme has a template file for this widget. If it does, we use
							that, otherwise we output the default widget markup.
								
							=================================================================== */


							if( $lbp->have_posts() ) : 

								if( locate_template( '/templates/latest_blog_posts_widget.php' ) != '' )
								{
									include locate_template( '/templates/latest_blog_posts_widget.php' );
								}
								else
								{

									if( $show_more_button == 1 )
										echo apply_filters( 'themeists_widget_lbp_more_button', '' );

									echo apply_filters( 'themeists_widget_lbp_list_open', "<ul class='show_$num_to_show'>", $query_args, $num_to_show );

									while( $lbp->have_posts() ) : $lbp->the_post();

										

									endwhile; wp_reset_postdata();

									echo apply_filters( 'themeists_widget_lbp_list_close', '</ul>', $query_args );

								}

							endif;

		    			?>
		    		
		    		<?php echo apply_filters( 'themeists_widget_lbp_container_close', '</div>' ); ?>

		    		<?php

				echo $after_widget;
		
			}/* widget() */


			/* ============================================================================ */

		
			/**
			 * Processes the widget's options to be saved.
			 *
			 * @author Richard Tape
			 * @package themeists_latest_blog_posts
			 * @since 1.0
			 * @param $new_instance	The previous instance of values before the update.
			 * @param @old_instance	The new instance of values to be generated via the update. 
			 * @return $instance The saved values
			 */
			
			function update( $new_instance, $old_instance )
			{
		
				$instance = $old_instance;
		
		    	$instance['title'] 						= 	strip_tags( $new_instance['title'] );
		    	$instance['subtitle'] 					= 	strip_tags( $new_instance['subtitle'] );
		    	$instance['num_to_show']				=	absint( $new_instance['num_to_show'] );
	    		$instance['type_to_show']				=	sanitize_text_field( $new_instance['type_to_show'] );
	    		$instance['tax_to_show']				=	sanitize_text_field( $new_instance['tax_to_show'] );
	    		$instance['term_to_show']				=	sanitize_text_field( $new_instance['term_to_show'] );
	    		$instance['show_more_button']			=	absint( $new_instance['show_more_button'] );
	    		$instance['button_text']				=	strip_tags( $new_instance['button_text'] );
	    		$instance['button_link']				=	esc_url( $new_instance['button_link'] );
	    		$instance['show_thumbnail']				=	absint( $new_instance['show_thumbnail'] );
	    		$instance['show_comments']				=	absint( $new_instance['show_comments'] );
	    		$instance['show_date']					=	absint( $new_instance['show_date'] );
	    		$instance['show_author']				=	absint( $new_instance['show_author'] );
	    		$instance['show_likes']					=	absint( $new_instance['show_likes'] );
		    
				return $instance;
		
			}/* update() */


			/* ============================================================================ */


			/**
			 * Generates the administration form for the widget.
			 *
			 * @author Richard Tape
			 * @package themeists_latest_blog_posts
			 * @since 1.0
			 * @param $instance	The array of keys and values for the widget.
			 * @return None
			 */
			

			function form( $instance )
			{
		
				$instance = wp_parse_args(

					(array)$instance,
					array(
						'title' 			=> 'This is the title for this widget',
						'subtitle' 			=> 'And this is the (optional) subtitle. We think it looks pretty neat!',
						'num_to_show' 		=> '3',
						'type_to_show' 		=> 'post',
						'tax_to_show' 		=> '',
						'show_more_button' 	=> '',
						'button_text' 		=> __( 'Read All', self::locale ),
						'button_link' 		=> 'http://www.google.com/',
						'show_thumbnail' 	=> '',
						'show_comments' 	=> '',
						'show_date' 		=> '',
						'show_author' 		=> '',
						'show_likes' 		=> ''
					)

				);

				/* ======================================================================= */

				//List all registered, non-builtin post types
				$post_types = get_post_types( '', 'objects', '' );
				
				//Remove some of the useless stuff, set an array of posttypes to remove, run that through
				//a filter so we can modify this and then unset from the $post_types array
				$dont_show_these =	apply_filters( 
				                   		'themeists_widget_lbp_post_types_to_remove', 
				                   		array( 'revision', 'nav_menu_item', 'optionsframework' )
				                   	);

				foreach( $dont_show_these as $post_type_to_remove )
					unset( $post_types[$post_type_to_remove] );

				//Prepend a blank valued "Select a ..." option
				$select_title = array( 'name' => '', 'labels' => array( 'name' => __( 'Select Post Type', self::locale ) ) );
				$select_title['labels'] = (object) $select_title['labels'];
				$select_title = (object) $select_title;

				array_unshift(
	              	$post_types,
	              	apply_filters(
	                	'themeists_widget_lbp_prepend_to_post_types', 
	                	$select_title
	                )
				);

				/* ======================================================================= */

				//List all registered, non-built in Taxonomies
				$taxonomies = get_taxonomies( '', 'objects', '' );

				//Remove some of the useless stuff, set an array of taxonomies to remove, run that through
				//a filter so we can modify this and then unset from the $taxonomies array
				$dont_show_these_taxs =		apply_filters( 
				                   				'themeists_widget_lbp_taxonomies_to_remove', 
				                   				array( 'link_category', 'nav_menu' )
				                   			);

				foreach( $dont_show_these_taxs as $taxonomy_to_remove )
					unset( $taxonomies[$taxonomy_to_remove] );

				$select_title = array( 'name' => '', 'labels' => array( 'name' => __( 'Select Taxonomy', self::locale ) ) );
				$select_title['labels'] = (object) $select_title['labels'];
				$select_title = (object) $select_title;

				array_unshift(
	              	$taxonomies,
	              	apply_filters(
	                	'themeists_widget_lbp_prepend_to_taxonomies', 
	                	$select_title
	                )
				);

				/* ======================================================================= */
		
		    	?>
		    	
		    		<p>
						<label for="<?php echo $this->get_field_id( 'title' ); ?>">
							<?php _e( "Title", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php esc_attr_e( $instance['title'] ); ?>" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'subtitle' ); ?>">
							<?php _e( "Subtitle", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'subtitle' ); ?>" name="<?php echo $this->get_field_name( 'subtitle' ); ?>" value="<?php esc_attr_e( $instance['subtitle'] ); ?>" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'num_to_show' ); ?>">
							<?php _e( "Number of posts", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'num_to_show' ); ?>" name="<?php echo $this->get_field_name( 'num_to_show' ); ?>" value="<?php esc_attr_e( $instance['num_to_show'] ); ?>" />
					</p>
						<label for="<?php echo $this->get_field_id( 'type_to_show' ); ?>">
							<?php _e( "Post Type", THEMENAME ); ?>
						</label>
						<select name="<?php echo $this->get_field_name( 'type_to_show' ); ?>" id="<?php echo $this->get_field_id(' type_to_show '); ?>" class="widefat">
							<?php foreach( $post_types as $post_type ) : ?>
							<option value="<?php echo $post_type->name; ?>" <?php selected( $instance['type_to_show'], $post_type->name ); ?>><?php echo $post_type->labels->name; ?></option>
							<?php endforeach; ?>
						</select>
					</p>

					</p>
						<label for="<?php echo $this->get_field_id( 'tax_to_show' ); ?>">
							<?php _e( "Taxonomy Type", THEMENAME ); ?>
						</label>
						<select name="<?php echo $this->get_field_name( 'tax_to_show' ); ?>" id="<?php echo $this->get_field_id(' tax_to_show '); ?>" class="widefat">
							<?php foreach( $taxonomies as $taxonomy ) : ?>
							<option value="<?php echo $taxonomy->name; ?>" <?php selected( $instance['tax_to_show'], $taxonomy->name ); ?>><?php echo $taxonomy->labels->name; ?></option>
							<?php endforeach; ?>
						</select>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'term_to_show' ); ?>">
							<?php _e( "Taxonomy Term", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'term_to_show' ); ?>" name="<?php echo $this->get_field_name( 'term_to_show' ); ?>" value="<?php esc_attr_e( $instance['term_to_show'] ); ?>" />
					</p>

					<p>

						<input class="checkbox" type="checkbox" <?php checked( $instance['show_more_button'], 1 ) ?> id="<?php echo $this->get_field_id('show_more_button'); ?>" name="<?php echo $this->get_field_name('show_more_button'); ?>" value="1" />
						<label for="<?php echo $this->get_field_id('show_more_button'); ?>"><?php _e('Show More Button'); ?></label><br />

					</p>

					<div data-show-on="show_more_button">

						<p>
							<label for="<?php echo $this->get_field_id( 'button_text' ); ?>">
								<?php _e( "Button Text", THEMENAME ); ?>
							</label>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" value="<?php esc_attr_e( $instance['button_text'] ); ?>" />
						</p>

						<p>
							<label for="<?php echo $this->get_field_id( 'button_link' ); ?>">
								<?php _e( "Button Link", THEMENAME ); ?>
							</label>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'button_link' ); ?>" name="<?php echo $this->get_field_name( 'button_link' ); ?>" value="<?php esc_attr_e( $instance['button_link'] ); ?>" />
						</p>

					</div>

					<p>

						<input class="checkbox" type="checkbox" <?php checked( $instance['show_thumbnail'], 1 ) ?> id="<?php echo $this->get_field_id('show_thumbnail'); ?>" name="<?php echo $this->get_field_name('show_thumbnail'); ?>" value="1" />
						<label for="<?php echo $this->get_field_id('show_thumbnail'); ?>"><?php _e('Show Thumbnail'); ?></label><br />

					</p>

					<p>

						<input class="checkbox" type="checkbox" <?php checked( $instance['show_comments'], 1 ) ?> id="<?php echo $this->get_field_id('show_comments'); ?>" name="<?php echo $this->get_field_name('show_comments'); ?>" value="1" />
						<label for="<?php echo $this->get_field_id('show_comments'); ?>"><?php _e('Show Comments'); ?></label><br />

					</p>

					<p>

						<input class="checkbox" type="checkbox" <?php checked( $instance['show_date'], 1 ) ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" value="1" />
						<label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Show Date'); ?></label><br />

					</p>

					<p>

						<input class="checkbox" type="checkbox" <?php checked( $instance['show_author'], 1 ) ?> id="<?php echo $this->get_field_id('show_author'); ?>" name="<?php echo $this->get_field_name('show_author'); ?>" value="1" />
						<label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Show Author'); ?></label><br />

					</p>

					<?php if( is_plugin_active( 'themeists-likethis/themeistslikethis.php') ) : ?>

						<p>

							<input class="checkbox" type="checkbox" <?php checked( $instance['show_likes'], 1 ) ?> id="<?php echo $this->get_field_id('show_likes'); ?>" name="<?php echo $this->get_field_name('show_likes'); ?>" value="1" />
							<label for="<?php echo $this->get_field_id('show_likes'); ?>"><?php _e('Show "Likes"'); ?></label><br />

						</p>

					<?php endif; ?>
		    	
		    	<?php
		
			}/* form() */


			/* ============================================================================ */
		

			/**
			 * Registers and enqueues stylesheets for the administration panel and the
			 * public facing site.
			 *
			 * @author Richard Tape
			 * @package themeists_latest_blog_posts
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
			 * @package themeists_latest_blog_posts
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
		
		
		}/* class themeists_latest_blog_posts */

	}

	//Register The widget
	//register_widget( "themeists_latest_blog_posts" );
	add_action( 'widgets_init', create_function( '', 'register_widget( "themeists_latest_blog_posts" );' ) );

?>