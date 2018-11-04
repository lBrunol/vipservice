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

<section class="orcamento">
	<div class="container-contato" id="fale">
	<h3 class="texto titulo-grande">FAÇA SEU ORÇAMENTO AGORA!</h3> 
    <div class="container">    
		<div class="row">  
        	<div class="col-sm-6"> 
          		<div class="box"> 
					<p>Para solicitar seu orçamento,preencha o formulário ao lado ou se preferir, entre em contato através de nossas redes sociais.</p>            
					<p><img src="img/whatsapp.png" class="icones">(11)96672-8816/ (11)9476-6398</p>
					<p>facebook.com/vipservice<img src="img/facebook.png" style="float:left;"></p>    
					<p>instagram.com/vipservice <img src="img/instagram.png" style="float:left;"></p>			
					<p>Aceitamos todos tipos de cartões</p>			
					<img src="img/cartoes.jpg">
					<br> 
					<h6>*Todos os orçamentos serão respondidos em no máximo 24 horas</h6>                      
	          	</div>
          	</div>
			<div class="col-md-6">
				<div class="servicos-orcamento">
					{{ loading }}
					<template v-if="posts.length > 0">
						<div class="col-md-3" v-for="post in posts">
							<div :class="'post --first' + post.id">
								<post class="box-post" v-bind="{post, handleState, chooseService}"></post>
							</div>
						</div>
					</template>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row">
					<template v-if="selectedPosts.length > 0">
						<selected-services v-bind="{posts: selectedPosts, removePost: removePost, sumPostsPrice: sumPostsPrice}"></selected-services>
					</template>
				</div>
			</div>
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
