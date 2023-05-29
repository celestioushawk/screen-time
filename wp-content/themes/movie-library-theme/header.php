<?php
/**
 * Template part for header section of the site which will be common to all the pages.
 *
 * @package movie-library-theme
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'body-style' ); ?>>
	<?php wp_body_open(); ?>
	<header>
		<div class="top-nav">
			<div class="search-div">
				<?php get_template_part( 'template_parts/search-form' ); ?>
				<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/search.svg' ); ?>" alt="<?php esc_attr_e( 'search svg image', 'movie-library-theme' ); ?>" class="search-image-click"> <span class="search-title"><?php esc_html_e( 'Search', 'movie-library-theme' ); ?></span>
			</div>
			<div class="heading">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'rt-movie' ) ); ?>"><?php esc_html_e( 'Screen', 'movie-library-theme' ); ?> <span><?php esc_html_e( 'Time', 'movie-library-theme' ); ?></span></a>
			</div>
			<div class="header-options">
				<div class="user">
					<a href="<?php echo esc_url( wp_registration_url() ); ?>">
						<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/user.svg' ); ?>" alt="<?php esc_attr_e( 'user svg image', 'movie-library-theme' ); ?>"> <?php esc_html_e( 'Sign In', 'movie-library-theme' ); ?>
					</a>
				</div>
				<div class="language">
					<?php esc_html_e( 'Eng', 'movie-library-theme' ); ?> &#9660;
				</div>
			</div>
			<div class="mobile-hamburger-menu">
				<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/hamburger.svg' ); ?>" alt="<?php esc_attr_e( 'hamburger menu svg image', 'movie-library-theme' ); ?>">
			</div>
			<div class="mobile-menu-close">
				<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/close.svg' ); ?>" alt="<?php esc_attr_e( 'menu close svg image', 'movie-library-theme' ); ?>">
			</div>
		</div>
		<div class="mobile-nav display-none">
			<div class="mobile-nav-header-buttons">
				<div class="mobile-nav-sign-in">
					<span>
						<?php esc_html_e( 'Sign In', 'movie-library-theme' ); ?>
					</span>
				</div>
				<div class="mobile-nav-register">
					<span>
						<?php esc_html_e( 'Register for FREE', 'movie-library-theme' ); ?>
					</span>
				</div>
			</div>

			<div class="line-divider"></div>

			<div class="mobile-explore-menu">
				<div class="mobile-explore-menu-header">
					<div>
						<span>
							<?php esc_html_e( 'Explore', 'movie-library-theme' ); ?>
						</span>
					</div>
					<div>
						<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/down-arrow.svg' ); ?>" alt="<?php esc_attr_e( 'down arrow svg image', 'movie-library-theme' ); ?>">
					</div>
				</div>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'mobile_menu',
						)
					);
					?>
			</div>
			<div class="line-divider"></div>
			<div class="mobile-settings-menu">
				<div class="mobile-settings-menu-header">
					<div>
						<span>
							<?php esc_html_e( 'Settings', 'movie-library-theme' ); ?>
						</span>
					</div>
					<div>
						<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/down-arrow.svg' ); ?>" alt="<?php esc_attr_e( 'down arrow svg image', 'movie-library-theme' ); ?>">
					</div>
				</div>
				<div class="mobile-settings-menu-child">
					<?php esc_html_e( 'Language', 'movie-library-theme' ); ?>: <span><?php esc_html_e( 'Eng', 'movie-library-theme' ); ?></span>
				</div>
				<div class="mobile-settings-menu-child">
					<span>
						<?php esc_html_e( 'Preference', 'movie-library-theme' ); ?>
					</span>
				</div>
				<div class="mobile-settings-menu-child last-menu-item">
					<span>
						<?php esc_html_e( 'Location', 'movie-library-theme' ); ?>
					</span>
				</div>
			</div>
			<div class="line-divider"></div>
			<div class="mobile-menu-version">
				<span>
					<?php esc_html_e( 'Version', 'movie-library-theme' ); ?>: 3.9.2
				</span>
			</div>
		</div>
		<div class="bottom-nav">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'header_bottom_menu',
					)
				);
				?>
		</div>
	</header>
