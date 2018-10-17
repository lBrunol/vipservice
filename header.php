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
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<header>
	<div class="container">
		<div class="row">		
			<div class="col-sm-3">
				<img src="<?= theme_get_custom_logo() ?>" alt="<?= bloginfo( 'name' ); ?>" />
			</div>
			<div class="col-sm-9">	
				<?php if($menu_principal) : ?>
					<nav>
						<ul>
							<?php foreach($menu_principal as $item) : ?>
								<li><a href="<?= $item->url ?>" target="<?= $item->target ?>"><?= $item->title ?></a></li>
							<?php endforeach ?>
						</ul>
					</nav>
				<?php endif;?>
			</div>
		</div>
	</div>
</header>


