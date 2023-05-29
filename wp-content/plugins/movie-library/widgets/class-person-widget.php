<?php
/**
 * Create and register a person widget to display a list of people based on options taken from
 * the user.
 *
 * @package movie-library
 */

namespace Widgets\Class_Person_Widget;

use WP_Query;

use Includes\Class_Rt_Person\Rt_Person;

/**
 * Class to implement a custom widget to display a list of people based on input
 * taken from a user. This class extends the WP_Widget and overrides its methods
 * to imlplement a widget.
 */
class Person_Widget extends \WP_Widget {
	/**
	 * Call the parent class contructor and register a movie widget.
	 */
	public function __construct() {
		parent::__construct(
			'person_widget',
			__( 'Person Widget', 'movie-library' ),
			array( 'description' => __( 'A Person Widget', 'movie-library' ) ),
		);
	}
	/**
	 * Echo the widget content.
	 *
	 * @param array $args Display arguments 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( ! empty( $instance['career-taxonomy'] ) ) {
			$tax_query_args[] = array(
				'taxonomy' => Rt_Person::RT_PERSON_CAREER,
				'field'    => 'term_id',
				'terms'    => $instance['career-taxonomy'],
			);
		}
		$person_query_args = array(
			'post_type'      => Rt_Person::RT_PERSON_SLUG,
			'posts_per_page' => ! empty( $instance['person-count'] ) ? absint( $instance['person-count'] ) : '',
			's'              => ! empty( $instance['person-title'] ) ? $instance['person-title'] : '',
			//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => array(
				array(
					$tax_query_args,
				),
			),
		);

		$person_query  = new WP_Query( $person_query_args );
		$people_result = $person_query->posts;
		//phpcs:ignore
		echo $args['before_widget'];
		?>
		<div class="main-wrapper">
			<h3 class="person-wrapper-header">
				<?php esc_html_e( 'People', 'movie-library' ); ?>
			</h3>
			<div class="widget-person-wrapper">
			<?php
			foreach ( $people_result as $person ) :
				?>
					<div class="widget-person-container">
						<div class="widget-person-poster">
							<?php echo get_the_post_thumbnail( $person->ID ); ?>
						</div>
						<div class="widget-person-details">
							<a href="<?php echo esc_url( get_post_permalink( $person->ID ) ); ?>">
								<h2 class="widget-movie-title">
									<?php echo esc_html( $person->post_title ); ?>
								</h2>
							</a>
						</div>
					</div>
				<?php
			endforeach;
			?>
			</div>
		</div>
		<?php
		//phpcs:ignore
		echo $args['after_widget'];
	}

	/**
	 * Output the settings update form.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 * @return void
	 */
	public function form( $instance ) {

		$person_careers     = get_terms( array( 'rt-person-career' ) );
		$form_person_career = ! empty( $instance['career-taxonomy'] ) ? $instance['career-taxonomy'] : '';
		$form_person_title  = ! empty( $instance['person-title'] ) ? $instance['person-title'] : '';
		$form_person_count  = ! empty( $instance['person-count'] ) ? $instance['person-count'] : '';

		?>
			<h3>
				<?php esc_html_e( 'Filter People', 'movie-library' ); ?>
			</h3>


			<label for="<?php echo esc_attr( $this->get_field_id( 'person-title' ) ); ?>">
				<?php esc_html_e( 'Person Title', 'movie-library' ); ?>
			</label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'person-title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'person-title' ) ); ?>" value="<?php echo esc_attr( $form_person_title ); ?>">


			<label for="<?php echo esc_attr( $this->get_field_id( 'person-count' ) ); ?>">
				<?php esc_html_e( 'Person Count', 'movie-library' ); ?>
			</label>
			<input type="number" name="<?php echo esc_attr( $this->get_field_name( 'person-count' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'person-count' ) ); ?>" value="<?php echo esc_attr( $form_person_count ); ?>">


			<label for="<?php echo esc_attr( $this->get_field_id( 'career-taxonomy' ) ); ?>">
				<?php esc_html_e( 'Person Career', 'movie-library' ); ?>
			</label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'career-taxonomy' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'career-taxonomy' ) ); ?>">
			<option selected value="">
				<?php esc_html_e( 'Select an option', 'movie-library' ); ?>
			</option>
				<?php foreach ( $person_careers as $career ) : ?>
					<option value="<?php echo esc_attr( $career->term_id ); ?>" <?php selected( (int) $form_person_career === (int) $career->term_id, true ); ?>>
						<?php echo esc_html( $career->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>


		<?php
	}

	/**
	 * Update a particular instance of a widget.
	 *
	 * @param array $new_instance The new updated settings for the particular instance of the widget.
	 * @param array $old_instance The old settings for the particular instance of the widget.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance_keys = array(
			'person-title',
			'person-count',
			'career-taxonomy',
		);

		foreach ( $instance_keys as $instance_key ) {
			$instance[ $instance_key ] = isset( $new_instance[ $instance_key ] ) ? sanitize_text_field( $new_instance[ $instance_key ] ) : '';
		}

		return $instance;
	}
}
