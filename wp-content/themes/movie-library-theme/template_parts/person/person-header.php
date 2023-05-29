<?php
/**
 * Template part for single person header section to display info about the person.
 *
 * @package movie-library-theme
 */

$person_occupation  = get_the_terms( get_the_ID(), 'rt-person-career' );
$person_occupations = array();

foreach ( $person_occupation as $occupation ) {
	$person_occupations[] = $occupation->name;
}

$args                  = array(
	'post_type'      => 'rt-movie',
	'posts_per_page' => '2',
	//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	'tax_query'      => array(
		'relation' => 'AND',
		array(
			'taxonomy' => '_rt-movie-person',
			'field'    => 'slug',
			'terms'    => 'person-' . get_the_ID(),
		),
		array(
			'taxonomy' => 'rt-movie-label',
			'field'    => 'slug',
			'terms'    => 'upcoming-movies',
		),
	),
);
$data                  = new WP_Query( $args );
$upcoming_movies_array = $data->posts;
$upcoming_movies       = array();
foreach ( $upcoming_movies_array as $upcoming_movie ) {
	$upcoming_movie_name         = $upcoming_movie->post_title;
	$upcoming_movie_release_date = get_person_post_meta( $upcoming_movie->ID, 'rt-movie-meta-basic', true );
	$upcoming_movie_release_year = strtok( $upcoming_movie_release_date['rt-movie-meta-basic-release-date'], '-' );
	$upcoming_movies[]           = $upcoming_movie_name . ' (' . $upcoming_movie_release_year . ')';
}
$person_basic_meta_data = get_person_post_meta( get_the_ID(), 'rt-person-meta-basic', true );
$person_birth_date      = date_create( $person_basic_meta_data['rt-person-meta-basic-birth-date'] );
$birth_date             = $person_birth_date->format( 'd M Y' );

$person_full_name        = get_person_post_meta( get_the_ID(), 'rt-person-full-name', true );
$person_debut_year       = get_person_post_meta( get_the_ID(), 'rt-person-debut-year', true );
$person_debut_movie      = get_person_post_meta( get_the_ID(), 'rt-person-debut-movie', true );
$person_debut_movie_year = get_person_post_meta( get_the_ID(), 'rt-person-debut-movie-year', true );

$social_links = get_person_post_meta( get_the_ID(), 'rt-person-meta-social', true );
?>

	<section class="main-person">
		<div class="person-image">
			<?php
			if ( has_post_thumbnail( get_the_ID() ) ) {
				echo get_the_post_thumbnail();
			} else {
				?>
				<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/images/dummy.png' ); ?>" alt=" <?php esc_attr_e( 'fallback placeholder image', 'movie-library-theme' ); ?>">
				<?php
			}
			?>
		</div>
		<div class="person-stats">
			<div class="person-details">
				<div class="person-name">
				<?php echo esc_html( the_title() ); ?><span><?php echo esc_html( $person_full_name ); ?></span>	
				</div>
				<div class="person-full-details-grid">
					<div class="person-detail-row">
						<div class="label">
							<span>
								<?php esc_html_e( 'Occupation', 'movie-library-theme' ); ?>:
							</span>
						</div>
						<div class="label">
							<span>
								<?php esc_html_e( 'Born', 'movie-library-theme' ); ?>:
							</span>
						</div>
						<div class="label">
							<span>
								<?php esc_html_e( 'Birthplace', 'movie-library-theme' ); ?>:
							</span>
						</div>
						<div class="label">
							<span>
								<?php esc_html_e( 'Years Active', 'movie-library-theme' ); ?>:
							</span>
						</div>
						<div class="label">
							<span>
								<?php esc_html_e( 'Debut Movie', 'movie-library-theme' ); ?>:
							</span>
						</div>
						<div class="label">
							<span>
								<?php esc_html_e( 'Upcoming Movies', 'movie-library-theme' ); ?>:
							</span>
						</div>
						<div class="label">
							<span>
								<?php esc_html_e( 'Socials', 'movie-library-theme' ); ?>:
							</span>
						</div>
					</div>
					<div class="person-detail-row">
						<div class="value">
							<span>
								<?php
								if ( $person_occupations ) {
									echo esc_html( implode( ', ', $person_occupations ) );
								} else {
									esc_html_e( 'No data available!', 'movie-library' );
								}
								?>
							</span>
						</div>
						<div class="value">
							<span>
								<?php echo esc_html( $birth_date ); ?> (age <?php echo esc_html( gmdate( 'Y' ) - strtok( $person_basic_meta_data['rt-person-meta-basic-birth-date'], '-' ) ); ?> years)
							</span>
						</div>
						<div class="value">
							<span>
								<?php
								if ( $person_basic_meta_data['rt-person-meta-basic-birth-place'] ) {
									echo esc_html( $person_basic_meta_data['rt-person-meta-basic-birth-place'] );
								} else {
									esc_html_e( 'No birth information available.', 'movie-library-theme' );
								}
								?>
							</span>
						</div>
						<div class="value">
							<span>
								<?php
								if ( $person_debut_year ) {
									echo esc_html( $person_debut_year );
									?>
									-
									<?php
									esc_html_e( 'Present', 'movie-library-theme' );
								} else {
									esc_html_e( 'No data available!', 'movie-library' );
								}
								?>
							</span>
						</div>
						<div class="value">
							<span>
								<?php
								if ( $person_debut_movie ) {
									echo esc_html( $person_debut_movie ) . ' (' . esc_html( $person_debut_movie_year ) . ')';
								} else {
									esc_html_e( 'No debut movie found!', 'movie-library' );
								}
								?>
							</span>
						</div>
						<div class="value">
							<span>
								<?php
								if ( $upcoming_movies ) {
									echo esc_html( implode( ', ', $upcoming_movies ) );
								} else {
									esc_html_e( 'No upcoming movies to show.', 'movie-library-theme' );
								}
								?>
							</span>
						</div>
						<div class="value">
							<a href="<?php echo esc_url( $social_links['rt-person-meta-social-instagram'] ); ?>" target="<?php echo esc_attr( '_blank' ); ?>">
								<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/instagram.svg' ); ?>" alt="<?php esc_attr_e( 'instagram logo image', 'movie-library-theme' ); ?>">
							</a>
							<a href="<?php echo esc_url( $social_links['rt-person-meta-social-twitter'] ); ?>" target="<?php echo esc_attr( '_blank' ); ?>">
								<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/twitter.svg' ); ?>" alt="<?php esc_attr_e( 'twitter logo image', 'movie-library-theme' ); ?>">
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php
