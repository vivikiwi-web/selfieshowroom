<?php

/**
 * Footer layout overrides.
 *
 * @package Sober_Child
 */

defined('ABSPATH') || exit;

add_action('after_setup_theme', 'sober_child_replace_footer_info');

/**
 * Replace the parent footer info callback with the child layout.
 *
 * @return void
 */
function sober_child_replace_footer_info()
{
	remove_action('sober_footer', 'sober_footer_info');
	add_action('sober_footer', 'sober_child_footer_info');
}

/**
 * Display site footer with a 10/2 column split.
 *
 * @return void
 */
function sober_child_footer_info()
{
	$social_extra  = sober_get_option('footer_social_extra');
	$wrapper       = sober_get_option('footer_wrapper');
	$wrapper_class = 'wrapped' === $wrapper ? 'container' : 'sober-container';
	$has_socials   = has_nav_menu('socials') || $social_extra;
?>
	<div class="footer-info footer-<?php echo esc_attr($wrapper); ?>">
		<div class="<?php echo esc_attr($wrapper_class); ?>">
			<div class="row">

				<div class="footer-menu-container col-md-12">
					<?php
					if (has_nav_menu('footer')) {
						wp_nav_menu(
							array(
								'container'       => 'nav',
								'container_class' => 'footer-menu',
								'theme_location'  => 'footer',
								'menu_id'         => 'footer-menu',
								'depth'           => 1,
							)
						);
					}
					?>
				</div>

				<div class="site-info <?php echo $has_socials ? 'col-md-10' : 'col-md-12'; ?>">
					<div class="copyright">
						<?php echo do_shortcode(wp_kses_post(sober_get_option('footer_copyright'))); ?>
					</div>
				</div>

				<?php if ($has_socials) : ?>
					<div class="footer-social col-md-2">
						<?php
						if (has_nav_menu('socials')) {
							wp_nav_menu(
								array(
									'theme_location'  => 'socials',
									'container_class' => 'socials-menu ',
									'menu_id'         => 'footer-socials',
									'depth'           => 1,
								)
							);
						}

						if ($social_extra) {
							printf('<div class="socials-extra">%s</div>', do_shortcode(wp_kses_post($social_extra)));
						}
						?>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
<?php
}
