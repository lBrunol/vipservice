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
$page_orcamento = get_page_by_path('faca-seu-orcamento');
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
					<a href="#" class="link-banner img-link" data-img="<?= $imagem[0] ?>"><img src="<?= $imagem_depois ?>" alt="<?php echo get_the_title(); ?>" class="img-responsive image" /></a>
					<?php if($imagem_antes) : ?>
						<a href="#" class="hide link-banner img-link-antes" data-img="<?= $imagem[0] ?>"><img src="<?= $imagem_antes ?>" alt="<?php echo get_the_title(); ?>" class="img-responsive image" /></a>
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
				<?php echo $page_orcamento->post_content ?>
          	</div>
			<div class="col-sm-6">
				<h4 class="titulo-orcamento titulo-medio">SELECIONE O SERVIÇO DESEJADO!</h4>
				<img v-if="loading" src="/wp-content/themes/vipservice/images/loader-white.svg" alt="" class="floater-gallery-image -loader block-center" />
				<div class="row" v-if="step == 1">
					<div class="col-sm-12">
						<div class="servicos-orcamento">
							<template v-if="posts.length > 0">
								<div class="row">
									<div class="col-md-4" v-for="post in posts">
										<div :class="'post --first' + post.id">
											<post class="box-post" v-bind="{post, handleState, chooseService}"></post>
										</div>
									</div>
								</div>
							</template>
							<button v-if="prevPosts.length > 0" class="btn btn-white btn-orcamento" type="button" @click="previous(0)">Voltar</button>
						</div>
					</div>
				</div>
				<div class="row" v-if="step == 2">
					<div class="col-sm-12">
						<form action="#" method="post" novalidate="novalidate" class="wpcf7-form">
							<div class="form-group">
								<label class="form-label">Nome
									<small>(obrigatório)</small>
									<br>
									<input required type="text" name="nome" value="" size="40" aria-required="true" aria-invalid="false" class="form-control">
								</label>
							</div>
							<div class="form-group">
								<label class="form-label">E-mail
									<small>(obrigatório)</small>
									<br>
									<input required type="email" name="email" value="" size="40" aria-required="true" aria-invalid="false" class="form-control">
								</label>
							</div>
							<div class="form-group">
								<label class="form-label">Telefone
									<small>(obrigatório)</small>
									<br>
									<input required type="text" name="telefone" value="" size="40" aria-invalid="false" class="form-control">
								</label>
							</div>
							<div class="form-group">
								<label class="form-label">Mensagem
									<br>
									<textarea required name="mensagem" cols="40" rows="10" aria-invalid="false" class="form-control"></textarea>
								</label>
							</div>
						</form>
					</div>
				</div>
				<div class="row" v-if="message != ''">
					<div class="col-sm-12">
						<div class="alert alert-success">
							{{ message }}
						</div>
					</div>
				</div>				
				<div class="row" v-if="errorMessage != ''">
					<div class="col-sm-12">
						<div class="alert alert-danger">
							{{ errorMessage }}
						</div>
					</div>
				</div>
				<div class="row" v-if="selectedPosts.length > 0">
					<div class="col-sm-12">
						<h3 class="titulo-medio" style="color: #fff;">Serviços selecionados</h3>
						<selected-services v-bind="{posts: selectedPosts, removePost: removePost, writePostsPrice: writePostsPrice}"></selected-services>
						<button v-if="selectedPosts.length > 0 && step == 1" class="btn btn-white" type="button" @click="shouldNextStep()">Próximo passo</button>
						<button v-if="selectedPosts.length > 0 && step == 2" class="btn btn-white" type="button" @click="submitForm($event)">Finalizar</button>
						<button v-if="step > 1" class="btn btn-white" type="button" @click="previousStep()">Voltar</button>
						<span v-if="sendingBudget" class="ajax-loader"></span>
					</div>
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
