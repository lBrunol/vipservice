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
							<li><a href="<?= $item->url ?>" target="<?= $item->target ?>"><?= $item->title ?></a></li>
						<?php endforeach ?>
					</ul>
				<?php endif;?>
        	</div>
      	</div>
    	</div>
</section>


