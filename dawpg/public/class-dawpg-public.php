<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       ericktomaliwan
 * @since      1.0.0
 *
 * @package    Dawpg
 * @subpackage Dawpg/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Dawpg
 * @subpackage Dawpg/public
 * @author     Erick Tomaliwan <ericktoma@outlook.com>
 */
class Dawpg_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dawpg_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dawpg_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dawpg-public.css', array(), $this->version, 'all' );

		wp_localize_script( $this->plugin_name, 'ajax_custom', array( 'ajaxurl' => admin_url('admin-ajax.php')));


	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Dawpg_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Dawpg_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dawpg-public.js', array( 'jquery' ), $this->version, false );

	}

	public function downloadall_media_post_gallery( $output, $attr ) {
			$post = get_post();

			static $instance = 0;
			$instance++;

			if ( ! empty( $attr['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
				if ( empty( $attr['orderby'] ) ) {
					$attr['orderby'] = 'post__in';
				}
				$attr['include'] = $attr['ids'];
			}

		/* Had to be removed
		$output = apply_filters( 'post_gallery', '', $attr, $instance );

		if ( ! empty( $output ) ) {
			return $output;
		}
	)*/

	$html5 = current_theme_supports( 'html5', 'gallery' );
	$atts  = shortcode_atts(
		array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post ? $post->ID : 0,
			'itemtag'    => $html5 ? 'figure' : 'dl',
			'icontag'    => $html5 ? 'div' : 'dt',
			'captiontag' => $html5 ? 'figcaption' : 'dd',
			'columns'    => 3,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => '',
			'link'       => '',
		),
		$attr,
		'gallery'
	);

	$id = intval( $atts['id'] );

	if ( ! empty( $atts['include'] ) ) {
		$_attachments = get_posts(
			array(
				'include'        => $atts['include'],
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'order'          => $atts['order'],
				'orderby'        => $atts['orderby'],
			)
		);

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[ $val->ID ] = $_attachments[ $key ];
		}
	} elseif ( ! empty( $atts['exclude'] ) ) {
		$attachments = get_children(
			array(
				'post_parent'    => $id,
				'exclude'        => $atts['exclude'],
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'order'          => $atts['order'],
				'orderby'        => $atts['orderby'],
			)
		);
	} else {
		$attachments = get_children(
			array(
				'post_parent'    => $id,
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'order'          => $atts['order'],
				'orderby'        => $atts['orderby'],
			)
		);
	}

	if ( empty( $attachments ) ) {
		return '';
	}

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment ) {
			$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
		}
		return $output;
	}

	$itemtag    = tag_escape( $atts['itemtag'] );
	$captiontag = tag_escape( $atts['captiontag'] );
	$icontag    = tag_escape( $atts['icontag'] );
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) ) {
		$itemtag = 'dl';
	}
	if ( ! isset( $valid_tags[ $captiontag ] ) ) {
		$captiontag = 'dd';
	}
	if ( ! isset( $valid_tags[ $icontag ] ) ) {
		$icontag = 'dt';
	}

	$columns   = intval( $atts['columns'] );
	$itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
	$float     = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = '';

		/**
		 * Filters whether to print default gallery styles.
		 *
		 * @since 3.1.0
		 *
		 * @param bool $print Whether to print default gallery styles.
		 *                    Defaults to false if the theme supports HTML5 galleries.
		 *                    Otherwise, defaults to true.
		 */
		if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
			$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

			$gallery_style = "
			<style{$type_attr}>
				#{$selector} {
			margin: auto;
		}
				#{$selector} .gallery-item {
		float: {$float};
		margin-top: 10px;
		text-align: center;
		width: {$itemwidth}%;
	}
				#{$selector} img {
	border: 2px solid #cfcfcf;
	}
				#{$selector} .gallery-caption {
	margin-left: 0;
	}
	/* see gallery_shortcode() in wp-includes/media.php */
	</style>\n\t\t";
	}

	$size_class  = sanitize_html_class( $atts['size'] );
		# Initialise array for url passing
	$url_array = array();
	foreach ( $attachments as $id => $attachment ) {
			$url = wp_get_attachment_image_src( $id, 'full' ); // returns an array
			//echo $url[0]."<br />";
			$url_array[] = $url;
		}

		$postvalue = base64_encode(json_encode($url_array));

		$gallery_div = "<input type='button' name='btn_dl_all_gal' id='btn_dl_all_gal_id' class='btn btn-primary btn-sm' value='Download All'>";

		$gallery_div .= "<input type='hidden' name='btn_dl_all_gal_form' id='btn_dl_all_gal_form_id' value='{$postvalue}'>";

		$gallery_div .= "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";



		
		
		/**
		 * Filters the default gallery shortcode CSS styles.
		 *
		 * @since 2.5.0
		 *
		 * @param string $gallery_style Default CSS styles and opening HTML div container
		 *                              for the gallery shortcode output.
		 */
		$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

		$i = 0;

		foreach ( $attachments as $id => $attachment ) {

			$attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';

			if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
				$image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
			} elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
				$image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
			} else {
				$image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
			}

			$image_meta = wp_get_attachment_metadata( $id );

			$orientation = '';

			if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
				$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
			}

			$output .= "<{$itemtag} class='gallery-item'>";
			$output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
			$image_output
			</{$icontag}>";

			$url = wp_get_attachment_image_src( $id, 'full' ); // returns an array
			$output .= "
			<dt class='gallery-download'>
			<a href='{$url[0]}' download=''>Download</a>
			</dt>";

			if ( $captiontag && trim( $attachment->post_excerpt ) ) {
				$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
				" . wptexturize( $attachment->post_excerpt ) . "
				</{$captiontag}>";
			}

			$output .= "</{$itemtag}>";

			if ( ! $html5 && $columns > 0 && ++$i % $columns === 0 ) {
				$output .= '<br style="clear: both" />';
			}
		}

		if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
			$output .= "
			<br style='clear: both' />";
		}

		$output .= "
		</div>\n";

		return $output;
	}

	function delete_zip(){
		//$path_to_del = $_POST['zip_del_path'];

		//$admin_directory = ABSPATH.'/wp-admin/'.$path_to_del;

		//$split_zip = explode(".", $path_to_del);

		//unlink($admin_directory);
		//unlink($split_zip[0]);

		echo 'success';

		wp_die();
		//echo $split_zip[0];
	}

	function create_zip(){

		$uploads = wp_upload_dir();
			//print_r($uploads);

			//create zip folder in upload directory
		$downloadmedia_dirname = 'edel-atrewards-media';
		$downloadmedia_dir = $uploads['basedir'].'/'.$downloadmedia_dirname;
		$downloadmedia_url = $uploads['baseurl'].'/'.$downloadmedia_dirname;
		$date = date('Y-m-d');
		if ( ! file_exists($downloadmedia_dir) ) {
			wp_mkdir_p( $downloadmedia_dir );
		}

			$select_url = json_decode(base64_decode($_POST['url_arr'])); // Server side
			
			$zip = new ZipArchive(); //create new zip object
			
			//create a temp file & open it
			$tmp_filename = 'media-all_'.$date.'.zip';
			//$tmp_file = tempnam($downloadmedia_url, '');
			$tmp_file_zip_path =  $downloadmedia_dir.'/'.$tmp_filename;
			
			//check if can be open zip folder
			if ($zip->open($tmp_file_zip_path, ZipArchive::CREATE)!==TRUE) {
				exit("cannot open file\n");
			}

			//loop through each file
			foreach ($select_url as $file) {
				//download file
				$download_file = file_get_contents($file[0]);
				//add it to the zip
				$zip->addFromString(basename($file[0]), $download_file);
			}	

			$zip->close();

			$zip_file_url =  $downloadmedia_url.'/'.$tmp_filename;
			echo $zip_file_url;

			wp_die();


	}

}
