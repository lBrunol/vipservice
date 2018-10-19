<?php 

/**
 * Tipo de conteúdo personalizado: Serviço
 *
 * Serviço principal e servico do meio
 *
 * @package Hcor
 */

function custom_type_servico() {

    // Define os textos que serão exibidos no admin
	$labels = array(
        'name' => 'Serviços',
        'singular_name' => 'Serviço',
        'add_new' => 'Adicionar Novo',
        'add_new_item' => 'Novo Serviço',
        'edit_item' => 'Editar Serviço',
        'new_item' => 'Novo Serviço',
        'view_item' => 'Ver Serviço',
        'search_items' => 'Procurar Serviço',
        'not_found' =>  'Nenhum Serviço encontrado',
        'not_found_in_trash' => 'Nenhum Serviço encontrado na lixeira',
        'menu_name' => 'Serviços',
        'all_items' => 'Todos os Serviços'
    );

    // Define as configurações
	$args = array(
		'labels' => $labels,
		'capability_type' => 'page',
        'public' => false,
        'show_ui' => true,
        'show_in_rest' => true,
		'menu_position' => 7,
		'menu_icon' => 'dashicons-format-image',
		'supports' => array( 'title', 'thumbnail', 'page-attributes'),
        'register_meta_box_cb' => 'servico_meta_box',
        'rewrite' => false,
        'query_var' => false,
        'delete_with_user' => false,
        'hierarchical' => true
	);

	register_post_type( 'servicos', $args );
}

add_action('init', 'custom_type_servico');

// Adiciona uma meta box pra inserir conteúdo personalizado
function servico_meta_box() {        
    add_meta_box(
        'meta_box_servico',
        'Configurações do Serviço',
        'servico_meta',
        'servicos',
        'normal',
        'high'
    );
}
//Adiciona meta fields a API
function servicos_api() {
    
    register_rest_field( 'servicos', 'servico_preco', 
    	array(
        	'get_callback' => 'theme_get_api'
    	) 
    );
    register_rest_field( 'servicos', 'has_children', 
    	array(
        	'get_callback' => 'theme_has_children'
    	) 
    );
}

add_action( 'rest_api_init', 'servicos_api' );

// Configura os campos da meta box e imprime na tela do admin
function servico_meta(){
    global $post;
    
    $servico_preco = get_post_meta( $post->ID, 'servico_preco', true );
    
?>
    <div class="form-field">
        <label for="servico_preco">Preço</label><br>
        <input type="text" name="servico_preco" id="servico_preco" value="<?php echo $servico_preco; ?>" />
    </div>
    <br>
<?php
}

// Salva os dados do meta box ao salvar o depoimento
add_action( 'save_post', 'save_servico_post' );

function save_servico_post( $post_id ) {
    if (!isset( $_POST['_inline_edit'] )){
        if ( get_post_type( $post_id ) == 'servicos' && isset($_POST['servico_preco'])) {

            $fields = array(
                array(
                    'field' => 'servico_preco',
                    'value' => $_POST['servico_preco']
                ),
            );
            
            //Chama a função para salvar os posts meta
            foreach( $fields as $field ){
                theme_save_post_meta( $post_id, $field['field'], $field['value'] );
            }
        }
    }
}

?>