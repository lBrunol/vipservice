<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package vipservice
 */

get_header();

$banners = new WP_Query( array(
	'post_type' => 'banners',
	'posts_per_page' => 50,
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => 'banner_active',
			'value' => 'true',
			'compare' => '='
		)
	)
) );

?>

<section>
	<?php 
		if ( $banners -> have_posts() ) : 
			$banners -> the_post();
	?>
		<div class="owl-carousel main-banner">
			<?php foreach( $banners -> posts as $banner ) : 
				$imagem = wp_get_attachment_image_src( get_post_thumbnail_id( $banner -> ID ), 'full' );
				$nova_guia = !empty( get_post_meta( $banner -> ID, 'banner_target', true ) ) ? '_blank' : '_self';
				$link = get_post_meta( $banner -> ID, 'banner_link', true );
    			$position = get_post_meta( $banner -> ID, 'banner_position', true);
    			// if ( $position == 'principal' ) :
			?>
				<div class="item">
					<?php if ( !empty( $link ) ) : ?>
						<a href="<?php echo $link; ?>" target="<?php echo $nova_guia; ?>">
					<?php endif; ?>

					<img src="<?php echo $imagem[0]; ?>" alt="<?php echo get_the_title(); ?>" class="js-img-responsive img-responsive image" data-img-default="<?php echo $imagem[0]; ?>" />

					<?php if ( !empty( $link ) ) : ?>
						</a>
					<?php endif; ?>
				</div>
				<?php /*endif;*/ endforeach; ?>
		</div>
	<?php endif; ?>
</section>
<section class="orcamento">
	<div class="col-md-6">
		<div class="servicos-orcamento">
			{{ loading }}
			<div class="row">
				<template v-if="posts.length > 0">
					<div class="col-md-3" v-for="post in posts">
						<div :class="'post --first' + post.id">
							<post v-bind="{post, changeState, previous, chooseService}"></post>
							<!--<template v-if="post.children.length > 0">
								<div class="p2">
									<template v-for="post2 in post.children">
										<div :class="'p2 p' + post2.id">
											<post v-bind:post="post2"></post>
											<template v-if="post2.children.length > 0">
												<div class="p3">
													<template v-for="post3 in post2.children">
														<div :class="'p3 p' + post3.id">
															<post v-bind:post="post3"></post>
														</div>
													</template>
												</div>
											</template>
										</div>
									</template>
								</div>
							</template>-->
						</div>
					</div>
				</template>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="row">
			<template v-if="selectedPosts.length > 0">
				<selected-services v-bind="{posts: selectedPosts, removePost: removePost, sumPostsPrice: sumPostsPrice}"></selected-services>
			</template>
		</div>
	</div>
</section>
<?php
get_footer();
?>
<script>
	$(function(){
		$('.owl-carousel').owlCarousel({
			responsive: {
				0: {
					items: 1
				}
			},
			dots: true,
			nav: false
		});
	});
</script>
