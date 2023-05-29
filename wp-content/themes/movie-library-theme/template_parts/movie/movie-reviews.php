<?php
/**
 * Template part for single movie review section to display user reviews on the movie.
 *
 * @package movie-library-theme
 */

$comments_data = get_comments(
	array(
		'post_id' => get_the_ID(),
	)
);
?>
<section class="reviews">
	<div class="reviews-header">
		<?php esc_html_e( 'Reviews', 'movie-library-theme' ); ?>
	</div>
	<?php
	if ( $comments_data ) {
		?>
		<div class="reviews-grid">
			<?php foreach ( $comments_data as $comment_part ) : ?>
			<div class="review">
				<div class="review-top-row">
					<div class="review-user">
						<div class="user-image">
							<img src="<?php echo esc_url( get_avatar_url( $comment_part->user_id ) ); ?>" alt="<?php esc_attr_e( 'comment author avatar image', 'movie-library-theme' ); ?>">
						</div>
						<div class="user-name">
							<span>
								<?php echo esc_html( $comment_part->comment_author ); ?>
							</span>
						</div>
					</div>
					<div class="user-rating">
						<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/star.svg' ); ?>" alt="<?php esc_attr_e( 'star image for user rating', 'movie-library-theme' ); ?>"> 8.4/10
					</div>
				</div>
				<div class="review-text">
					<p>
						<?php echo esc_html( $comment_part->comment_content ); ?>	
					</p>
				</div>
				<div class="review-bottom-row">
					<div class="review-date">
						<span>
							<?php
								$date = date_create( $comment_part->comment_date );
								echo esc_html( $date->format( 'd M Y' ) );
							?>
						</span>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
	</div>
		<?php
	} else {
		?>
			<span>
				<?php esc_html_e( 'No Reviews yet!', 'movie-library-theme' ); ?>
			</span>
		<?php
	}
	?>
</section>
<?php
