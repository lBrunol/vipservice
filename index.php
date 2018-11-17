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

$servicos_posts = new WP_Query( array(
	'post_type' => 'servicos',
	'posts_per_page' => 50,
	'post_status' => 'publish'
) );

$banners_antes_depois_posts = new WP_Query( array(
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


$page_quem_somos = get_page_by_path('quem-somos');
?>

<?php if($page_quem_somos) : ?>
<section class="quem-somos">
	<div class="container">
		<?php echo $page_quem_somos->post_content ?>
	</div>
</section>
<?php endif; ?>

<?php if($servicos_posts->have_posts()) : $servicos_posts->the_post(); ?>
<section class="nossos-servicos" id="nossos-servicos" style="<?php echo 'background-image: url(' . get_template_directory_uri() . '/images/bg-servicos.PNG);' ?>">
    <h1 class="nossos-servicos-titulo titulo-grande">Nossos serviços</h1>
    <div class="container">
      	<div class="row">
			<?php foreach($servicos_posts->posts as $s_post) : ?>
				<?php $imagem = wp_get_attachment_image_src( get_post_thumbnail_id($s_post->ID), 'full' ); ?>
				<div class="col-md-3 col-sm-6">
					<div class="box-servico">
						<div class="box-servico-centraliza">
							<h3 class="box-servico-titulo"><?= get_the_title() ?></h3>
							<img src="<?= $imagem[0] ?>" alt="<?= get_the_title() ?>"  class="box-servico-img"/>
							<p class="box-servico-descricao"><?= get_the_content() ?></p>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
      	</div>
    </div>
</section>
<?php endif; ?>

<?php if ( $banners_antes_depois_posts -> have_posts() ) :  $banners_antes_depois_posts -> the_post(); ?>
<section class="trabalhos-realizados">
	<h2 class="titulo-grande">Trabalhos Realizados</h2>
	<div class="container">
		<div class="owl-carousel trabalhos-realizados-carousel">
			<?php foreach( $banners_antes_depois_posts->posts as $b_antes_depos_post ) : 
				$imagem = wp_get_attachment_image_src( get_post_thumbnail_id( $b_antes_depos_post -> ID ), 'full' );
				$imagem_antes = wp_get_attachment_url( get_post_meta( $b_antes_depos_post -> ID, 'cpi-type-banner_img01', true ) );
				$imagem_depois = wp_get_attachment_url( get_post_meta( $b_antes_depos_post -> ID, 'cpi-type-banner_img02', true ) );
				$nova_guia = !empty( get_post_meta( $b_antes_depos_post -> ID, 'banner_target', true ) ) ? '_blank' : '_self';
				$position = get_post_meta( $b_antes_depos_post -> ID, 'banner_position', true);
				if ( $position == 'antes-depois' ) :
			?>
				<div class="item">
					<a href="#"><img src="<?= $imagem_depois ?>" alt="<?php echo get_the_title(); ?>" class="img-responsive image" /></a>
					<?php if($imagem_antes) : ?>
						<a href="#" class="hide"><img src="<?= $imagem_antes ?>" alt="<?php echo get_the_title(); ?>" class="img-responsive image" /></a>
					<?php endif; ?>
				</div>
				<?php endif; endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="orcamento">
	<div class="container-contato" id="fale">
	<h3 class="texto titulo-grande">FAÇA SEU ORÇAMENTO AGORA!</h3> 
    <div class="container">    
		<div class="row">  
        	<div class="col-sm-6"> 
          		<div class="box"> 
					<p>Para solicitar seu orçamento,preencha o formulário ao lado ou se preferir, entre em contato através de nossas redes sociais.</p>            
					<p><img src="images/whatsapp.png" class="icones">(11)96672-8816/ (11)9476-6398</p>
					<p>facebook.com/vipservice<img src="images/facebook.png" style="float:left;"></p>    
					<p>instagram.com/vipservice <img src="images/instagram.png" style="float:left;"></p>			
					<p>Aceitamos todos tipos de cartões</p>			
					<img src="images/cartoes.jpg">
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
		$('.main-banner').owlCarousel({
			responsive: {
				0: {
					items: 1
				}
			},
			dots: true,
			nav: false
		});

		$('.trabalhos-realizados-carousel').owlCarousel({
			loop:true,
			margin:10,
			nav: false,
			dots: true,
			responsive:{
				0:{
					items:1
				},
				540:{
					items:3
				}
			}
		});
	});
</script>
