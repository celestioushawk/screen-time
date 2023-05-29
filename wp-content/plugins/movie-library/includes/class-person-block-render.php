<?php
/**
 * Render function for custom single movie and movie list block.
 *
 * @package movie-library
 */

namespace Includes\Class_Person_Block_Render;

define( 'BUILD_FOLDER', __DIR__ . '/../blocks/custom-blocks/build' );

use Includes\Class_Rt_Person\Rt_Person;
/**
 * Class to define render functions for single person and person list block.
 */
class Person_Block_Render {
	/**
	 * Enqueue the render function for blocks on init hook.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'create_block_person_list_block_init' ) );
	}
	/**
	 * Render callback function for person list block
	 *
	 * @param array $attributes Array of attributes recieved from block.
	 * @return string The HTML output.
	 */
	public function block_person_list_render_callback( $attributes ) {
		$tax_query_args = array();

		if ( empty( $attributes ) ) {
			return __( 'Please select a filter.', 'movie-library' );
		}

		if ( ! empty( $attributes['personCareer'] ) ) {
			$tax_query_args[] = array(
				'taxonomy' => Rt_Person::RT_PERSON_CAREER,
				'field'    => 'term_id',
				'terms'    => sanitize_text_field( $attributes['personCareer'] ),
			);
		}

		$person_query_args = array(
			'post_type'              => Rt_Person::RT_PERSON_SLUG,
			'posts_per_page'         => ! empty( $attributes['personCount'] ) ? absint( $attributes['personCount'] ) : '',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'no_found_rows'          => true,
			//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'              => array(
				array(
					$tax_query_args,
				),
			),
		);

		$result_people_query = new \WP_Query( $person_query_args );

		ob_start();

		if ( $result_people_query->have_posts() ) {
			?>
			<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
			<?php
			while ( $result_people_query->have_posts() ) :
				$result_people_query->the_post();
				$person_career = get_the_terms( get_the_ID(), Rt_Person::RT_PERSON_CAREER );
				?>
					<div class="person-container">
						<div class="person image">
							<?php the_post_thumbnail(); ?>
						</div>
						<div class="person-details">
							<a href="<?php esc_url( the_permalink() ); ?>">
								<h4 class="person-title">
									<?php the_title(); ?>
								</h4>
							</a>
							<p class="person-career">
								<?php echo esc_html( $person_career[0]->name ); ?>				
							</p>
						</div>
					</div>
				<?php
			endwhile;
			?>
			</div>
			<?php
		} else {
			echo esc_html_e( 'No Posts to Show', 'movie-library' );
		}

		wp_reset_postdata();

		return ob_get_clean();

	}
	/**
	 * Render callback function for single person block
	 *
	 * @param array $attributes Array of attributes recieved from block.
	 * @return string The HTML output.
	 */
	public function block_single_person_render_callback( $attributes ) {
		if ( empty( $attributes['personId'] ) ) {
			return __( 'Search for a person.', 'movie-library' );
		}

		$person_query_args = array(
			'post_type'              => Rt_Person::RT_PERSON_SLUG,
			'p'                      => absint( $attributes['personId'] ),
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'no_found_rows'          => true,
		);

		$result_person_query = new \WP_Query( $person_query_args );

		ob_start();

		if ( $result_person_query->have_posts() ) {
			?>
			<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
			<?php
			while ( $result_person_query->have_posts() ) :
				$result_person_query->the_post();
				$person_career = get_the_terms( get_the_ID(), Rt_Person::RT_PERSON_CAREER );
				?>
					<div class="person-container">
						<div class="person image">
							<?php the_post_thumbnail(); ?>
						</div>
						<div class="person-details">
							<a href="<?php esc_url( the_permalink() ); ?>">
								<h4 class="person-title">
									<?php the_title(); ?>
								</h4>
							</a>
							<p class="person-career">
								<?php echo esc_html( $person_career[0]->name ); ?>				
							</p>
						</div>
					</div>
				<?php
			endwhile;
			?>
			</div>
			<?php
		} else {
			echo esc_html_e( 'No Posts to Show', 'movie-library' );
		}

		wp_reset_postdata();

		return ob_get_clean();
	}
	/**
	 * Function to enqueue custom gutenberg block
	 *
	 * @return void
	 */
	public function create_block_person_list_block_init() {
		// Register person list block.
		register_block_type(
			BUILD_FOLDER . '/person-list',
			array(
				'render_callback' => array( $this, 'block_person_list_render_callback' ),
			),
		);
		// Register single person block.
		register_block_type(
			BUILD_FOLDER . '/single-person',
			array(
				'render_callback' => array( $this, 'block_single_person_render_callback' ),
			)
		);
	}

}
