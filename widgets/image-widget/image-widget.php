<?php

// Block direct requests
if( !defined( 'ABSPATH' ) )
	die( '-1' );

/**
 * Register our Image widget. Ensure we don't register this if the user has the Tribe Image Widget
 * Based on the Image Widget by  Modern Tribe, Inc.
 *
 * @author Richard Tape
 * @package Themeists_Image_Widget
 * @since 1.0
 * @param None
 * @return None
 */


if( !class_exists( 'Themeists_Image_Widget' ) && !class_exists( 'themeists_image_widget' ) ) :

	class Themeists_Image_Widget extends WP_Widget
	{

		/**
		 * Register ourselves
		 *
		 * @author Richard Tape
		 * @package Themeists_Image_Widget
		 * @since 1.0
		 * @param None
		 * @return None
		 */
		

		function Themeists_Image_Widget()
		{

			$this->loadPluginTextDomain();
			$widget_ops = array( 'classname' => 'widget_sp_image', 'description' => __( 'Showcase a single image with a Title, URL, and a Description', 'image_widget' ) );
			$control_ops = array( 'id_base' => 'widget_sp_image' );
			$this->WP_Widget('widget_sp_image', __('Themeists Image Widget', 'image_widget'), $widget_ops, $control_ops);
			add_action( 'admin_init', array( $this, 'admin_setup' ) );

		}/* Themeists_Image_Widget() */


		/* =============================================================================== */


		/**
		 * Set up our scripts/styles and some filters
		 *
		 * @author Richard Tape
		 * @package Themeists_Image_Widget
		 * @since 1.0
		 * @param None
		 * @return None
		 */
		
		function admin_setup()
		{

			global $pagenow;

			if( 'widgets.php' == $pagenow )
			{

				wp_enqueue_style( 'thickbox' );
				wp_enqueue_script( 'tribe-image-widget', plugins_url('resources/js/image-widget.js', __FILE__), array('thickbox'), FALSE, TRUE );
				add_action( 'admin_head-widgets.php', array( $this, 'admin_head' ) );

			}
			elseif( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow )
			{

				wp_enqueue_script( 'tribe-image-widget-fix-uploader', plugins_url('resources/js/image-widget-upload-fixer.js', __FILE__), array('jquery'), FALSE, TRUE );
				add_filter( 'image_send_to_editor', array( $this,'image_send_to_editor'), 1, 8 );
				add_filter( 'gettext', array( $this, 'replace_text_in_thickbox' ), 1, 3 );
				add_filter( 'media_upload_tabs', array( $this, 'media_upload_tabs' ) );
				add_filter( 'image_widget_image_url', array( $this, 'https_cleanup' ) );

			}

			$this->fix_async_upload_image();

		}/* admin_setup() */


		/* =============================================================================== */


		/**
		 * 
		 *
		 * @author Richard Tape
		 * @package Themeists_Image_Widget
		 * @since 1.0
		 * @param None
		 * @return None
		 */

		function fix_async_upload_image()
		{
		
			if(isset($_REQUEST['attachment_id']))
			{
				$id = (int) $_REQUEST['attachment_id'];
				$GLOBALS['post'] = get_post( $id );
			}

		}/* fix_async_upload_image() */


		/* =============================================================================== */


		/**
		 * loadPluginTextDomain
		 *
		 * @author Richard Tape
		 * @package 
		 * @since 1.0
		 * @param 
		 * @return 
		 */
		

		function loadPluginTextDomain()
		{
			load_plugin_textdomain( 'image_widget', false, trailingslashit(basename(dirname(__FILE__))) . 'lang/');
		}/* loadPluginTextDomain() */


		/* =============================================================================== */


		/**
		 * Retrieve resized image URL
		 *
		 * @param int $id Post ID or Attachment ID
		 * @param int $width desired width of image (optional)
		 * @param int $height desired height of image (optional)
		 * @return string URL
		 * @author Modern Tribe, Inc. (Peter Chester)
		 */

		function get_image_url( $id, $width=false, $height=false )
		{

			// Get attachment and resize but return attachment path (needs to return url)
			$attachment = wp_get_attachment_metadata( $id );
			$attachment_url = wp_get_attachment_url( $id );
			if(isset($attachment_url))
			{

				if( $width && $height )
				{

					$uploads = wp_upload_dir();
					$imgpath = $uploads['basedir'].'/'.$attachment['file'];
					if( WP_DEBUG )
					{
						error_log(__CLASS__.'->'.__FUNCTION__.'() $imgpath = '.$imgpath);
					}
					
					$image = image_resize( $imgpath, $width, $height );

					if( $image && !is_wp_error( $image ) )
					{
						$image = path_join( dirname($attachment_url), basename($image) );
					}
					else
					{
						$image = $attachment_url;
					}

				}
				else
				{
					$image = $attachment_url;
				}

				if( isset( $image ) ) 
				{
					return $image;
				}

			}

		}/* get_image_url() */


		/* =============================================================================== */


		/**
		 * Test context to see if the uploader is being used for the image widget or for other regular uploads
		 *
		 * @author Modern Tribe, Inc. (Peter Chester)
		 */

		function is_sp_widget_context()
		{
			if( isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$this->id_base) !== false )
			{
				return true;
			}
			elseif( isset($_REQUEST['_wp_http_referer']) && strpos($_REQUEST['_wp_http_referer'],$this->id_base) !== false )
			{
				return true;
			}
			elseif( isset($_REQUEST['widget_id']) && strpos($_REQUEST['widget_id'],$this->id_base) !== false )
			{
				return true;
			}

			return false;

		}/* is_sp_widget_context() */


		/* =============================================================================== */


		/**
		 * Somewhat hacky way of replacing "Insert into Post" with "Insert into Widget"
		 *
		 * @param string $translated_text text that has already been translated (normally passed straight through)
		 * @param string $source_text text as it is in the code
		 * @param string $domain domain of the text
		 * @author Modern Tribe, Inc. (Peter Chester)
		 */

		function replace_text_in_thickbox( $translated_text, $source_text, $domain )
		{

			if( $this->is_sp_widget_context() )
			{

				if( 'Insert into Post' == $source_text )
				{
					return __( 'Insert Into Widget', 'image_widget' );
				}

			}

			return $translated_text;
		
		}/* replace_text_in_thickbox() */


		/* =============================================================================== */


		/**
		 * Filter image_end_to_editor results
		 *
		 * @param string $html
		 * @param int $id
		 * @param string $alt
		 * @param string $title
		 * @param string $align
		 * @param string $url
		 * @param array $size
		 * @return string javascript array of attachment url and id or just the url
		 * @author Modern Tribe, Inc. (Peter Chester)
		 */

		function image_send_to_editor( $html, $id, $caption, $title, $align, $url, $size, $alt = '' )
		{

			// Normally, media uploader return an HTML string (in this case, typically a complete image tag surrounded by a caption).
			// Don't change that; instead, send custom javascript variables back to opener.
			// Check that this is for the widget. Shouldn't hurt anything if it runs, but let's do it needlessly.
			if( $this->is_sp_widget_context() )
			{

				if($alt=='') $alt = $title;
				?>
				<script type="text/javascript">
					// send image variables back to opener
					var win = window.dialogArguments || opener || parent || top;
					win.IW_html = '<?php echo addslashes($html); ?>';
					win.IW_img_id = '<?php echo $id; ?>';
					win.IW_alt = '<?php echo addslashes($alt); ?>';
					win.IW_caption = '<?php echo addslashes($caption); ?>';
					win.IW_title = '<?php echo addslashes($title); ?>';
					win.IW_align = '<?php echo esc_attr($align); ?>';
					win.IW_url = '<?php echo esc_url($url); ?>';
					win.IW_size = '<?php echo esc_attr($size); ?>';
				</script>
				<?php

			}

			return $html;

		}/* image_send_to_editor() */


		/* =============================================================================== */


		/**
		 * Remove from url tab until that functionality is added to widgets.
		 *
		 * @param array $tabs
		 * @author Modern Tribe, Inc. (Peter Chester)
		 */

		function media_upload_tabs( $tabs )
		{

			if( $this->is_sp_widget_context() )
			{
				unset($tabs['type_url']);
			}

			return $tabs;

		}/* media_upload_tabs() */


		/* =============================================================================== */


		/**
		 * Widget frontend output
		 *
		 * @param array $args
		 * @param array $instance
		 * @author Modern Tribe, Inc. (Peter Chester)
		 */

		function widget( $args, $instance )
		{

			extract( $args );
			extract( $instance );
			if( !empty( $imageurl ) )
			{

				$title = apply_filters( 'widget_title', empty( $title ) ? '' : $title );
				$description = apply_filters( 'widget_text', $description, $args, $instance );
				$imageurl = apply_filters( 'image_widget_image_url', esc_url( $imageurl ), $args, $instance );
				
				if( $link )
				{
					$link = apply_filters( 'image_widget_image_link', esc_url( $link ), $args, $instance );
					$linktarget = apply_filters( 'image_widget_image_link_target', esc_attr( $linktarget ), $args, $instance );
				}

				$width = apply_filters( 'image_widget_image_width', $width, $args, $instance );
				$height = apply_filters( 'image_widget_image_height', $height, $args, $instance );
				$align = apply_filters( 'image_widget_image_align', esc_attr( $align ), $args, $instance );
				$alt = apply_filters( 'image_widget_image_alt', esc_attr( $alt ), $args, $instance );
				include( $this->getTemplateHierarchy( 'widget' ) );

			}

		}/* widget() */


		/* =============================================================================== */


		/**
		 * Update widget options
		 *
		 * @param object $new_instance Widget Instance
		 * @param object $old_instance Widget Instance
		 * @return object
		 * @author Modern Tribe, Inc. (Peter Chester)
		 */

		function update( $new_instance, $old_instance )
		{

			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			if( isset($new_instance['description']) )
			{

				if( current_user_can('unfiltered_html') )
				{
					$instance['description'] = $new_instance['description'];
				}
				else
				{
					$instance['description'] = wp_filter_post_kses($new_instance['description']);
				}

			}

			$instance['link'] = $new_instance['link'];
			$instance['image'] = $new_instance['image'];
			$instance['imageurl'] = $this->get_image_url($new_instance['image'],$new_instance['width'],$new_instance['height']);  // image resizing not working right now
			$instance['linktarget'] = $new_instance['linktarget'];
			$instance['width'] = $new_instance['width'];
			$instance['height'] = $new_instance['height'];
			$instance['align'] = $new_instance['align'];
			$instance['alt'] = $new_instance['alt'];

			return $instance;

		}/* update() */


		/* =============================================================================== */


		/**
		 * Form UI
		 *
		 * @param object $instance Widget Instance
		 * @author Modern Tribe, Inc. (Peter Chester)
		 */
		function form( $instance )
		{

			$instance = wp_parse_args( (array) $instance, array(
				'title' => '',
				'description' => '',
				'link' => '',
				'linktarget' => '',
				'width' => '',
				'height' => '',
				'image' => '',
				'imageurl' => '',
				'align' => '',
				'alt' => ''
			) );

			include( $this->getTemplateHierarchy( 'widget-admin' ) );

		}/* form() */


		/* =============================================================================== */


		/**
		 * Admin header css
		 *
		 * @author Modern Tribe, Inc. (Peter Chester)
		 */
		function admin_head()
		{

			?>
			<style type="text/css">
				.aligncenter {
					display: block;
					margin-left: auto;
					margin-right: auto;
				}
			</style>
			<?php

		}/* admin_head() */


		/* =============================================================================== */


		/**
		 * Adjust the image url on output to account for SSL.
		 *
		 * @param string $imageurl
		 * @return string $imageurl
		 * @author Modern Tribe, Inc. (Peter Chester)
		 */

		function https_cleanup( $imageurl = '' )
		{

			if( isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" )
			{
				$imageurl = str_replace('http://', 'https://', $imageurl);
			}
			else
			{
				$imageurl = str_replace('https://', 'http://', $imageurl);
			}

			return $imageurl;

		}/* https_cleanup() */


		/* =============================================================================== */


		/**
		 * Loads theme files in appropriate hierarchy: 1) child theme,
		 * 2) parent template, 3) plugin resources. will look in the image-widget/
		 * directory in a theme and the views/ directory in the plugin
		 *
		 * @param string $template template file to search for
		 * @return template path
		 * @author Modern Tribe, Inc. (Matt Wiebe)
		 **/

		function getTemplateHierarchy( $template )
		{

			// whether or not .php was added
			$template_slug = rtrim( $template, '.php' );
			$template = $template_slug . '.php';

			if( $theme_file = locate_template( array( 'image-widget/' . $template ) ) )
			{
				$file = $theme_file;
			}
			else
			{
				$file = 'views/' . $template;
			}

			return apply_filters( 'sp_template_image-widget_'.$template, $file );

		}/* getTemplateHierarchy() */

	}/* class Themeists_Image_Widget */

endif;


/**
 * Load the widget on widgets_init
 *
 * @author Richard Tape
 * @package Themeists_Image_Widget
 * @since 1.0
 * @param None
 * @return None
 */

function tribe_load_image_widget()
{
	register_widget( 'Themeists_Image_Widget' );
}/* tribe_load_image_widget() */

add_action( 'widgets_init', 'tribe_load_image_widget' );

?>