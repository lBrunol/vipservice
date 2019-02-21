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
<div class="footer-line scroll">
	<hr class="line"/>
	<a href="#header-bar" class="btn btn-xs btn-up"><i class="icon-angle-left"></i></a>
</div>
<footer>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<p>Copyright© 2018</p>
			</div>
		</div>
	</div>
</footer>

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
