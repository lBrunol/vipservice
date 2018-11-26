<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package vipservice
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="site-info">
			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'vipservice' ) ); ?>">
				<?php
				/* translators: %s: CMS name, i.e. WordPress. */
				printf( esc_html__( 'Proudly powered by %s', 'vipservice' ), 'WordPress' );
				?>
			</a>
			<span class="sep"> | </span>
				<?php
				/* translators: 1: Theme name, 2: Theme author. */
				printf( esc_html__( 'Theme: %1$s by %2$s.', 'vipservice' ), 'vipservice', '<a href="http://underscores.me/">Underscores.me</a>' );
				?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
<div class="floater-site -gallery -large">
	<div class="floater-site-dialog">
		<div class="floater-site-content">
			<button type="button" class="close"><i class="icon icon-cancel"></i></button>
			<div class="floater-gallery">
				<img src="/wp-content/themes/vipservice/images/loader-white.svg" alt="" class="floater-gallery-image -loader" />
				<img src="javascript:;" alt="" class="floater-gallery-image -photo js-hidden" />
				<p class="floater-gallery-message js-hidden">Não foi possível carregar a imagem</p>
			</div>
		</div>
	</div>
</div>
<div class="floater-background"></div>
</body>
</html>
