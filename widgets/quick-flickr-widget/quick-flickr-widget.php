<?php

if( !class_exists( 'Quick_Flickr_Widget' ) ) :
	
	class Quick_Flickr_Widget extends WP_Widget
	{

		/**
		 * Based on Kovshenin's rather excellent Quick Flickr Widget. We've included it as part of our package
		 * as we intend - in the future - to add an AJAX-based front-end as well as lightbox additions. We've
		 * also modified the output. You have to love Open Source and the GPL :)
		 *
		 * @author Richard Tape
		 * @package Quick_Flickr_Widget
		 * @since 1.0
		 */
		
		public function __construct()
		{

			parent::__construct( 'themeists-flickr-widget', 'Themeists Flickr Widget', array(
				'description' => 'Display up to 20 of your latest Flickr submissions in your sidebar.',
			) );

		}/* __construct() */


		/**
		 * Displays the widget contents.
		 *
		 * @author Richard Tape
		 * @package Quick_Flickr_Widget
		 * @since 1.0
		 * @param 
		 * @return 
		 */

		public function widget( $args, $instance )
		{

			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $args['before_widget'];
			if ( ! empty( $title ) )
				echo $args['before_title'] . $title . $args['after_title'];

			$photos = $this->get_photos( array(
				'username' => $instance['username'],
				'count' => $instance['count'],
				'tags' => $instance['tags'],
			) );

			echo '<div class="themeists-flickr-widget-inner-container">';

				if ( is_wp_error( $photos ) )
				{
					echo $photos->get_error_message();
				}
				else
				{
					foreach ( $photos as $photo ) {
						$link = esc_url( $photo->link );
						$src = esc_url( $photo->media->m );
						$title = esc_attr( $photo->title );

						$item = sprintf( '<a href="%s"><img src="%s" alt="%s" /></a>', $link, $src, $title );
						$item = sprintf( '<div class="themeists-flickr-item"><div class="square">%s</div></div>', $item );
						echo $item;
					}
				}

			echo '</div>';

			echo $args['after_widget'];

		}/* widget() */


		/**
		 * Returns an array of photos on a WP_Error.
		 *
		 * @author Richard Tape
		 * @package Quick_Flickr_Widget
		 * @since 1.0
		 * @param 
		 * @return 
		 */
		
		private function get_photos( $args = array() )
		{

			$transient_key = md5( 'aquick-flickr-cache-' . print_r( $args, true ) );
			$cached = get_transient( $transient_key );
			if ( $cached )
				return $cached;

			$username = isset( $args['username'] ) ? $args['username'] : '';
			$tags = isset( $args['tags'] ) ? $args['tags'] : '';
			$count = isset( $args['count'] ) ? absint( $args['count'] ) : 10;
			$query = array(
				'tagmode' => 'any',
				'tags' => $tags,
			);

			// If username is an RSS feed
			if ( preg_match( '#^https?://api\.flickr\.com/services/feeds/photos_public\.gne#', $username ) ) {
				$url = parse_url( $username );
				$url_query = array();
				wp_parse_str( $url['query'], $url_query );
				$query = array_merge( $query, $url_query );
			} else {
				$user = $this->request( 'flickr.people.findByUsername', array( 'username' => $username ) );
				if ( is_wp_error( $user ) )
					return $user;

				$user_id = $user->user->id;
				$query['id'] = $user_id;
			}

			$photos = $this->request_feed( 'photos_public', $query );

			if ( ! $photos )
				return new WP_Error( 'error', 'Could not fetch photos.' );

			$photos = array_slice( $photos, 0, $count );
			set_transient( $transient_key, $photos, apply_filters( 'quick_flickr_widget_cache_timeout', 3600 ) );
			return $photos;
		
		}/* get_photos() */


		/**
		 * Make a request to the Flickr API.
		 *
		 * @author Richard Tape
		 * @package Quick_Flickr_Widget
		 * @since 1.0
		 * @param 
		 * @return 
		 */
		
		private function request( $method, $args )
		{

			$args['method'] = $method;
			$args['format'] = 'json';
			$args['api_key'] = 'd348e6e1216a46f2a4c9e28f93d75a48';
			$args['nojsoncallback'] = 1;
			$url = esc_url_raw( add_query_arg( $args, 'http://api.flickr.com/services/rest/' ) );

			$response = wp_remote_get( $url );
			if ( is_wp_error( $response ) )
				return false;

			$body = wp_remote_retrieve_body( $response );
	 		$obj = json_decode( $body );

			if ( $obj && $obj->stat == 'fail' )
				return new WP_Error( 'error', $obj->message );

			return $obj ? $obj : false;

		}/* request() */


		/**
		 * Fetch items from the Flickr Feed API.
		 *
		 * @author Richard Tape
		 * @package Quick_Flickr_Widget
		 * @since 1.0
		 * @param 
		 * @return 
		 */
		
		private function request_feed( $feed = 'photos_public', $args = array() )
		{

			$args['format'] = 'json';
			$args['nojsoncallback'] = 1;
			$url = sprintf( 'http://api.flickr.com/services/feeds/%s.gne', $feed );
			$url = esc_url_raw( add_query_arg( $args, $url ) );

			$response = wp_remote_get( $url );
			if ( is_wp_error( $response ) )
				return false;
			
			$body = wp_remote_retrieve_body( $response );
			$body = preg_replace( "#\\\\'#", "\\\\\\'", $body );
	 		$obj = json_decode( $body );

			return $obj ? $obj->items : false;

		}/* request_feed() */


		/**
		 * Validate and update widget options.
		 *
		 * @author Richard Tape
		 * @package Quick_Flickr_Widget
		 * @since 1.0
		 * @param 
		 * @return 
		 */
		
		public function update( $new_instance, $old_instance )
		{

			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['username'] = strip_tags( $new_instance['username'] );
			$instance['tags'] = strip_tags( $new_instance['tags'] );
			$instance['count'] = absint( $new_instance['count'] );
			
			return $new_instance;

		}/* update() */


		/**
		 * Render widget controls.
		 *
		 * @author Richard Tape
		 * @package Quick_Flickr_Widget
		 * @since 1.0
		 * @param 
		 * @return 
		 */
		
		public function form( $instance )
		{

			$title = isset( $instance['title'] ) ? $instance['title'] : 'Photostream';
			$username = isset( $instance['username'] ) ? $instance['username'] : '';
			$tags = isset( $instance['tags'] ) ? $instance['tags'] : '';
			$count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 10;
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'Username or RSS:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php _e( 'Tags:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" type="text" value="<?php echo esc_attr( $tags ); ?>" /><br />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Count:' ); ?></label><br />
				<input type="number" min="1" max="20" value="<?php echo esc_attr( $count ); ?>" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" />
			</p>

			<?php

		}/* form() */

	}/* class Quick_Flickr_Widget */

endif;



/**
 * Register The Widget
 *
 * @author Richard Tape
 * @package Quick_Flickr_Widget
 * @since 1.0
 * @param 
 * @return 
 */

function quick_flickr_widget_init()
{
	register_widget( 'Quick_Flickr_Widget' );
}

add_action( 'widgets_init', 'quick_flickr_widget_init' );



/**
 * Upgrade from old versions.
 *
 * @author Richard Tape
 * @package 
 * @since 1.0
 * @param 
 * @return 
 */

function quick_flickr_widget_upgrade()
{

	$old = get_option( 'widget_quickflickr', false );
	if ( ! $old )
		return;

	$new = get_option( 'widget_quick-flickr-widget' );
	$new[] = array(
		'title' => isset( $old['title'] ) ? strip_tags( $old['title'] ) : 'Photostream',
		'count' => isset( $old['items'] ) ? absint( $old['items'] ) : 10,
		'tags' => isset( $old['tags'] ) ? strip_tags( $old['tags'] ) : '',
		'username' => isset( $old['username'] ) ? strip_tags( $old['username'] ) : '',
	);
	end( $new );
	$new_index = key( $new );
	update_option( 'widget_quick-flickr-widget', $new );

	$sidebars_widgets = get_option( 'sidebars_widgets' );
	foreach ( $sidebars_widgets as $sidebar => $widgets )
		if ( is_array( $widgets ) )
			foreach ( $widgets as $key => $widget )
				if ( $widget == 'quick-flickr' )
					$sidebars_widgets[$sidebar][$key] = 'quick-flickr-widget-' . $new_index;

	update_option( 'sidebars_widgets', $sidebars_widgets );
	delete_option( 'widget_quickflickr' );

}/* quick_flickr_widget_upgrade() */

add_action( 'admin_init', 'quick_flickr_widget_upgrade' );