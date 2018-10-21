<?php 

/**
 * Tipo conteúdo personalizado: Serviço
 *
 * Serviço principal e servico do meio
 *
 * @package Hcor
 */

function custom_type_servico() {

    // Define os textos que serão exibidos no admin
	$labels = array(
        'name' => 'Serviços',
        'singular_name' => 'serviço',
        'add_new' => 'Adicionar Novo',
        'add_new_item' => 'Novo serviço',
        'edit_item' => 'Editar serviço',
        'new_item' => 'Novo serviço',
        'view_item' => 'Ver serviço',
        'search_items' => 'Procurar serviço',
        'not_found' =>  'Nenhum Serviço encontrado',
        'not_found_in_trash' => 'Nenhum serviço encontrado na lixeira',
        'menu_name' => 'Serviços',
        'all_items' => 'Todos os serviços'
    );

    // Define as configurações
	$args = array(
		'labels' => $labels,
		'capability_type' => 'page',
        'public' => false,
        'show_ui' => true,
        'show_in_rest' => true,
		'menu_position' => 6,
		'menu_icon' => 'dashicons-tickets-alt',
		'supports' => array( 'title', 'editor', 'thumbnail'),
        'rewrite' => false,
        'query_var' => false,
        'delete_with_user' => false,
        'hierarchical' => true
	);

	register_post_type( 'servicos', $args );
}

add_action('init', 'custom_type_servico');

?>