<?php

	if( !class_exists( 'themeists_dribbble' ) )
	{

		class themeists_dribbble extends WP_Widget
		{
		
			
			/**
			 * The name shown in the widgets panel
			 *
			 * @author Richard Tape
			 * @package themeists_dribbble
			 * @since 1.0
			 */
			
			const name 		= 'Themeists Dribbble';

			/**
			 * For helping with translations
			 *
			 * @author Richard Tape
			 * @package themeists_dribbble
			 * @since 1.0
			 */

			const locale 	= THEMENAME;

			/**
			 * The slug for this widget, which is shown on output
			 *
			 * @author Richard Tape
			 * @package themeists_dribbble
			 * @since 1.0
			 */
			
			const slug 		= 'themeists_dribbble';
		

			/* ============================================================================ */
		
			/**
			 * The widget constructor. Specifies the classname and description, instantiates
			 * the widget, loads localization files, and includes necessary scripts and
			 * styles. 
			 *
			 * @author Richard Tape
			 * @package themeists_dribbble
			 * @since 1.0
			 * @param None
			 * @return None
			 */
			
			function themeists_dribbble()
			{
		
				//load_plugin_textdomain( self::locale, false, plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . '/lang/' );

		
				$widget_opts = array (

					'classname' => 'themeists_dribbble', 
					'description' => __( 'Display your latest dribbble shots', self::locale )

				);

				$control_options = array(

					'width' => '400'

				);

				//we need to add a filter to plugins_url as we use symlinks in our dev setup
				add_filter( 'plugins_url', array( &$this, 'local_dev_symlink_plugins_url_fix' ), 10, 3 );

				//Register the widget
				$this->WP_Widget( self::slug, __( self::name, self::locale ), $widget_opts, $control_options );
		
		    	// Load JavaScript and stylesheets
		    	$this->register_scripts_and_styles();

		    	
		
			}/* themeists_dribbble() */
		

			/* ============================================================================ */


			/**
			 * Outputs the content of the widget.
			 *
			 * @author Richard Tape
			 * @package themeists_dribbble
			 * @since 1.0
			 * @param (array) $args - The array of form elements
			 * @param (array) $instance - The saved options from the widget controls
			 * @return None
			 */
			

			function widget( $args, $instance )
			{
		
				include_once( ABSPATH . WPINC . '/feed.php' );

				extract( $args, EXTR_SKIP );
		
				echo $before_widget;

				//Get vars
	    		$title					=	$instance['title'];
	    		$playerName				=	$instance['playerName'];
	    		$maxItems				=	$instance['maxItems'];
	    		$bigImage				=	$instance['bigImage'];

		    	if( function_exists( 'fetch_feed' ) ) :
		
					$rss = fetch_feed( "http://dribbble.com/players/$playerName/shots.rss" );
		
					add_filter( 'wp_feed_cache_transient_lifetime', create_function( '$a', 'return 1800;' ) );
		
					if( !is_wp_error( $rss ) )
						$items = $rss->get_items( 0, $rss->get_item_quantity( $maxItems ) ); 

				endif;

				// Title of widget (before and after defined by themes)
				if( !empty( $title ) ) echo $before_title . $title . $after_title;

	
				if( !empty( $items ) ) :

				?>

				<ol class="dribbbles">

				<?php	
				
					foreach( $items as $item ) :
	
						$title = $item->get_title();
						$link = $item->get_permalink();
						$date = $item->get_date( 'F d, Y' );
						$description = $item->get_description();

						preg_match( "/src=\"(http.*(jpg|jpeg|gif|png))/", $description, $image_url );
						$image = $image_url[1];
	
						if( !$bigImage )
							$image = preg_replace( '/.(jpg|jpeg|gif|png)/', '_teaser.$1', $image ); 

				?>
	
					<li class="dribbble-shot"> 
				
						<a href="<?php echo $link; ?>" class="dribbble-link">
							<img src="<?php echo $image; ?>" alt="<?php echo $title;?>"/>
						</a> 
	
						<a href="<?php echo $link; ?>" class="dribbble-over">
							<strong><?php echo $title; ?></strong> 
							<span class="dim"><?php echo $playerName; ?></span>
							<em><?php echo $date; ?></em> 
						</a>
							
 					</li>

					<?php endforeach;?>

				</ol>

				<?php endif;

				echo $after_widget;
		
			}/* widget() */


			/* ============================================================================ */

		
			/**
			 * Processes the widget's options to be saved.
			 *
			 * @author Richard Tape
			 * @package themeists_dribbble
			 * @since 1.0
			 * @param $new_instance	The previous instance of values before the update.
			 * @param @old_instance	The new instance of values to be generated via the update. 
			 * @return $instance The saved values
			 */
			
			function update( $new_instance, $old_instance )
			{
		
				$instance = $old_instance;
		
		    	$instance['title'] 			= 	$new_instance['title'];
		    	$instance['playerName'] 	= 	$new_instance['playerName'];
		    	$instance['maxItems']		= 	$new_instance['maxItems'];
		    	$instance['bigImage'] 		= 	$new_instance['bigImage'];
		    
				return $instance;
		
			}/* update() */


			/* ============================================================================ */


			/**
			 * Generates the administration form for the widget.
			 *
			 * @author Richard Tape
			 * @package themeists_dribbble
			 * @since 1.0
			 * @param $instance	The array of keys and values for the widget.
			 * @return None
			 */
			

			function form( $instance )
			{
		
				$instance = wp_parse_args(

					(array)$instance,
					array(
						'title' => 'Latest dribbble shots',
						'playerName' => 'dribbble',
						'maxItems' => '6',
						'bigImage' => ''
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
						<label for="<?php echo $this->get_field_id( 'playerName' ); ?>">
							<?php _e( "Player Name (Username)", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'playerName' ); ?>" name="<?php echo $this->get_field_name( 'playerName' ); ?>" value="<?php echo $instance['playerName']; ?>" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'maxItems' ); ?>">
							<?php _e( "Max # Shots", THEMENAME ); ?>
						</label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'maxItems' ); ?>" name="<?php echo $this->get_field_name( 'maxItems' ); ?>" value="<?php echo $instance['maxItems']; ?>" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'bigImage' ); ?>">
							<?php _e( "Show Big Image", THEMENAME ); ?>
						</label>
						<input id="<?php echo $this->get_field_id( 'bigImage' ); ?>" value="1" name="<?php echo $this->get_field_name( 'bigImage' ); ?>" type="checkbox" <?php checked( $instance['bigImage'], 1 ); ?>>
					</p>
		    	
		    	<?php
		
			}/* form() */


			/* ============================================================================ */
		

			/**
			 * Registers and enqueues stylesheets for the administration panel and the
			 * public facing site.
			 *
			 * @author Richard Tape
			 * @package themeists_dribbble
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

		      		wp_enqueue_style( 'dribbble-css', plugins_url( 'assets/css/dribbble.css', __FILE__ ) );

				}

			}/* register_scripts_and_styles() */


			/* ============================================================================ */


			/**
			 * Edit the plugins_url() url to be appropriate for this widget (we use symlinks on local dev)
			 *
			 * @author Richard Tape
			 * @package themeists_iview_slider_widget
			 * @since 1.0
			 */
			
			function local_dev_symlink_plugins_url_fix( $url, $path, $plugin )
			{

				// Do it only for this plugin
				if ( strstr( $plugin, basename( __FILE__ ) ) )
					return str_replace( dirname( __FILE__ ), '/' . basename( dirname( dirname( dirname( $plugin ) ) ) ) . '/widgets/dribbble/', $url );

				return $url;
					

				return $url;

			}/* local_dev_symlink_plugins_url_fix() */

		
		
		}/* class themeists_dribbble */

	}

	//Register The widget
	//register_widget( "themeists_dribbble" );
	add_action( 'widgets_init', create_function( '', 'register_widget( "themeists_dribbble" );' ) );

?>