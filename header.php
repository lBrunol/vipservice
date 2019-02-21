<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package vipservice
 */

?>
<?php 
	$menu_principal = wp_get_nav_menu_items( wp_get_nav_menu_object( 'principal' ));

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
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Poppins:400,700" rel="stylesheet">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div class="header-bar" id="header-bar">
	<div class="container">
		<div class="row">
			<div class="col-md-5">
				<div class="horario-funcionamento">
					<i class="icon-clock"></i> <span>Horário de funcionamento</span> Seg. a Sex. 09h as 18h. Sáb. 09h as 14h
				</div>
			</div>
			<div class="col-md-5">
				<div class="telefones-contato">
					<i class="icon icon-phone"></i> <span>Contatos</span> <span class="bigger">·</span> (11) 96772-8616 <span class="bigger">·</span> (11) 2402-1104 <span class="bigger">·</span> (11) 94776-6398
				</div>
			</div>
			<div class="col-md-2">
				<button class="btn btn-white -smaller">Faça seu orçamento</button>
			</div>
		</div>
	</div>
</div>
<section class="banner">
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
    			if ( $position == 'principal' || $position == 'principal-video' ) :
			?>
				<?php if($position == 'principal-video') : ?>
					<div class="item-video">
						<?php echo '<a class="owl-video" href="http://vipservice.dev.com.br/wp-content/uploads/2018/11/mov_bbb.mp4"></a>' ?>
					</div>
				<?php else : ?>
					<div class="item">
						<?php if ( !empty( $link ) ) : ?>
							<a href="<?php echo $link; ?>" target="<?php echo $nova_guia; ?>">
						<?php endif; ?>						
							<img src="<?php echo $imagem[0]; ?>" alt="<?php echo get_the_title(); ?>" class="js-img-responsive img-responsive image" data-img-default="<?php echo $imagem[0]; ?>" />
						<?php if ( !empty( $link ) ) : ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			<?php endif; endforeach; ?>
		</div>
	<?php endif; ?>
</section>

<section class="topo">      
    <div class="navbar">
		<div class="container">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          		<span class="icon-bar"></span>
          		<span class="icon-bar"></span>
          		<span class="icon-bar"></span>
        	</button> 
			<a class="navbar-brand" href="#"><img src="<?= theme_get_custom_logo() ?>" alt="<?= bloginfo( 'name' ); ?>" /></a>
        	<div class="navbar-collapse collapse navbar-right">
				<?php if($menu_principal) : ?>
					<ul class="nav navbar-nav menu-site">
						<?php foreach($menu_principal as $item) : ?>
						
							<li class="<?= implode(' ', $item->classes) ?>"><a href="<?= $item->url ?>" target="<?= $item->target ?>"><?= $item->title ?></a></li>
						<?php endforeach ?>
					</ul>
				<?php endif;?>
        	</div>
      	</div>
    	</div>
</section>


