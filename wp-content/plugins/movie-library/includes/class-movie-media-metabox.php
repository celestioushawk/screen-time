<?php
/**
 * Create image and video metaboxes for rt-movie and rt-person
 *
 * @package movie-library
 */

namespace Includes\Class_Movie_Media_Metabox;

use Includes\Class_Rt_Movie\Rt_Movie;
use Includes\Class_Rt_Person\Rt_Person;
use Movie_Library\Movie_Library;

/**
 * Create image and video meta boxes for rt-movie and rt-person
 */
class Movie_Media_Metabox {
	/**
	 * Initialize the class and creates hooks the functions to their respective hooks.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'create_media_box' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'save_post', [ $this, 'save_media_box' ] );
	}

	/**
	 * Wrapper function for creating meta boxes for image and video.
	 *
	 * @return void
	 */
	public function create_media_box() {
			$this->create_image_meta_box();
			$this->create_video_meta_box();
	}

	/**
	 * Wrapper function for creating meta boxes for image and video.
	 *
	 * @param int $post_id The post id of the post being saved.
	 * @return void
	 */
	public function save_media_box( $post_id ) {
		$this->save_image_meta_box( $post_id );
		$this->save_video_meta_box( $post_id );
	}

	/**
	 * Wrapper function for registering scripts for image and video media boxes.
	 *
	 * @param string $hook_suffix the type of page being loaded.
	 * @return void
	 */
	public function register_scripts( $hook_suffix ) {
		if ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) {
			if ( Rt_Movie::RT_MOVIE_SLUG === get_post_type() || Rt_Person::RT_PERSON_SLUG === get_post_type() ) {
				$this->register_video_script();
				$this->register_image_script();
			}
		}
	}

	/**
	 * Register the image meta box
	 *
	 * @return void
	 */
	public function create_image_meta_box() {
		add_meta_box(
			'rt-media-meta-images',
			__( 'Media Images', 'movie-library' ),
			array( $this, 'media_meta_box_html' ),
			array( Rt_Movie::RT_MOVIE_SLUG, Rt_Person::RT_PERSON_SLUG ),
			'side'
		);
	}

	/**
	 * Enqueue the script file for the wp.media
	 *
	 * @return void
	 */
	public function register_image_script() {
		wp_enqueue_media();
		wp_enqueue_script( 'image-upload', MOVIE_LIBRARY_URL . '/js/image-upload.js', array( 'jquery', 'wp-i18n' ), filemtime( MOVIE_LIBRARY_PATH . '/js/image-upload.js' ), true );
	}

	/**
	 * Create the HTML structure for the meta boxes
	 *
	 * @param \WP_Post $post The post object of the post being saved.
	 * @return void
	 */
	public function media_meta_box_html( $post ) {
		$image_urls      = array();
		$saved_image_ids = '';
		if ( Rt_Movie::RT_MOVIE_SLUG === get_post_type( $post->ID ) ) {
			$image_ids = get_movie_post_meta( $post->ID, 'rt-media-meta-images', true );
		} else {
			$image_ids = get_person_post_meta( $post->ID, 'rt-media-meta-images', true );
		}
		if ( ! empty( $image_ids ) ) {
			foreach ( $image_ids as $image ) {
				$image_post = wp_get_attachment_url( $image );
				if ( $image_post ) {
					$image_urls[] = $image_post;
				}
			}
			$image_urls_string = implode( ', ', $image_urls );
			$saved_image_ids   = implode( ',', $image_ids );
		}
		?>
		<input type="button" id="image-upload-btn" value="<?php esc_attr_e( 'Add Image', 'movie-library' ); ?>">
		<div id="meta-box-wrapper">
			</div>
			<input type="hidden" id="img-hidden-id-field" name="img-hidden-id-field" value="<?php echo esc_attr( $saved_image_ids ); ?>" >
			<input type="hidden" id="img-hidden-url-field" name="img-hidden-url-field" value="<?php echo esc_attr( $image_urls_string ); ?>" >

			<?php wp_nonce_field( 'rt-media-nonce', 'rt-media-nonce' ); ?>	
		<?php
	}

	/**
	 * Save the meta data recieved from the image meta box
	 *
	 * @param int $post_id The post id of the post being saved.
	 * @return void
	 */
	public function save_image_meta_box( $post_id ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( empty( $_POST['rt-media-nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['rt-media-nonce'] ) ), 'rt-media-nonce' ) ) {
			return;
		}
		if ( get_post_type() === Rt_Movie::RT_MOVIE_SLUG || get_post_type() === Rt_Person::RT_PERSON_SLUG ) {

			if ( isset( $_POST['img-hidden-id-field'] ) ) {
				$images_arr = sanitize_text_field( wp_unslash( $_POST['img-hidden-id-field'] ) );
				$images_arr = explode( ',', $images_arr );
				$images_arr = array_map( 'absint', $images_arr );
				if ( ! empty( $images_arr ) ) {
					if ( Rt_Movie::RT_MOVIE_SLUG === get_post_type( $post_id ) ) {
						update_movie_post_meta( $post_id, 'rt-media-meta-images', $images_arr );
					} else {
						update_person_post_meta( $post_id, 'rt-media-meta-images', $images_arr );
					}
				}
			} else {
				if ( Rt_Movie::RT_MOVIE_SLUG === get_post_type( $post_id ) ) {
					delete_movie_post_meta( $post_id, 'rt-media-meta-images' );
				} else {
					delete_person_post_meta( $post_id, 'rt-media-meta-images' );
				}
			}
		}

	}

	/**
	 * Register the video meta box
	 *
	 * @return void
	 */
	public function create_video_meta_box() {
		add_meta_box(
			'rt-media-meta-videos',
			__( 'Media Videos', 'movie-library' ),
			array( $this, 'video_meta_box_html' ),
			array( Rt_Movie::RT_MOVIE_SLUG, Rt_Person::RT_PERSON_SLUG ),
			'side'
		);
	}

	/**
	 * Enqueue the script file for wpmedia
	 *
	 * @return void
	 */
	public function register_video_script() {
		wp_enqueue_media();
		wp_enqueue_script( 'video-upload', MOVIE_LIBRARY_URL . '/js/video-upload.js', array( 'jquery', 'wp-i18n' ), filemtime( MOVIE_LIBRARY_PATH . '/js/video-upload.js' ), true );
	}

	/**
	 * Create the HTML structure for video meta boxes.
	 *
	 * @param \WP_Post $post Global post object.
	 * @return void
	 */
	public function video_meta_box_html( $post ) {

		$video_urls      = array();
		$saved_video_ids = '';
		if ( Rt_Movie::RT_MOVIE_SLUG === get_post_type( $post->ID ) ) {
			$video_ids = get_movie_post_meta( $post->ID, 'rt-media-meta-videos', true );
		} else {
			$video_ids = get_person_post_meta( $post->ID, 'rt-media-meta-videos', true );
		}
		if ( ! empty( $video_ids ) ) {
			foreach ( $video_ids as $video ) {
				$video_post = wp_get_attachment_url( $video );
				if ( $video_post ) {
					$video_urls[] = $video_post;
				}
			}
			$video_urls_string = implode( ', ', $video_urls );
			$saved_video_ids   = implode( ',', $video_ids );
		}
		?>
		<div id="video-box-wrapper">
			<input type="hidden" id="video-hidden-id-field" name="video-hidden-id-field" value="<?php echo esc_attr( $saved_video_ids ); ?>" >
			<input type="hidden" id="video-hidden-url-field" name="video-hidden-url-field" value="<?php echo esc_attr( $video_urls_string ); ?>" >
			<input type="button" id="video-upload-btn" value="<?php esc_attr_e( 'Add Video', 'movie-library' ); ?>">
		</div>
		<?php
	}

	/**
	 * Save the meta data recieved from the video meta box
	 *
	 * @param int $post_id The post id of the post being saved.
	 * @return void
	 */
	public function save_video_meta_box( $post_id ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( empty( $_POST['rt-media-nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['rt-media-nonce'] ) ), 'rt-media-nonce' ) ) {
			return;
		}
		if ( get_post_type() === Rt_Movie::RT_MOVIE_SLUG || get_post_type() === Rt_Person::RT_PERSON_SLUG ) {
			if ( isset( $_POST['video-hidden-id-field'] ) ) {
				$videos_arr = sanitize_text_field( wp_unslash( $_POST['video-hidden-id-field'] ) );
				$videos_arr = explode( ',', $videos_arr );
				$videos_arr = array_map( 'absint', $videos_arr );
				if ( ! empty( $videos_arr ) ) {
					if ( Rt_Movie::RT_MOVIE_SLUG === get_post_type( $post_id ) ) {
						update_movie_post_meta( $post_id, 'rt-media-meta-videos', $videos_arr );
					} else {
						update_person_post_meta( $post_id, 'rt-media-meta-videos', $videos_arr );
					}
				}
			}
		}
	}

}
