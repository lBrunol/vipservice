<?php 

/**
 * Tipo de conteúdo personalizado: Serviço
 *
 * Serviço principal e servico do meio
 *
 * @package Hcor
 */

function custom_type_servico_orcamento() {

    // Define os textos que serão exibidos no admin
	$labels = array(
        'name' => 'Serviços Orçamentos',
        'singular_name' => 'Serviço orçamento',
        'add_new' => 'Adicionar Novo',
        'add_new_item' => 'Novo Serviço de orçamento',
        'edit_item' => 'Editar Serviço de orçamento',
        'new_item' => 'Novo Serviço de orçamento',
        'view_item' => 'Ver Serviço de orçamento',
        'search_items' => 'Procurar Serviço de orçamento',
        'not_found' =>  'Nenhum Serviço de orçamento encontrado',
        'not_found_in_trash' => 'Nenhum Serviço de orçamento encontrado na lixeira',
        'menu_name' => 'Serviços de orçamentos',
        'all_items' => 'Todos os Serviços de orçamentos'
    );

    // Define as configurações
	$args = array(
		'labels' => $labels,
		'capability_type' => 'page',
        'public' => false,
        'show_ui' => true,
        'show_in_rest' => true,
		'menu_position' => 7,
		'menu_icon' => 'dashicons-feedback',
		'supports' => array( 'title', 'thumbnail', 'page-attributes'),
        'register_meta_box_cb' => 'servico_orcamento_meta_box',
        'rewrite' => false,
        'query_var' => false,
        'delete_with_user' => false,
        'hierarchical' => true
	);

	register_post_type( 'servicos_orcamento', $args );
}

add_action('init', 'custom_type_servico_orcamento');

// Adiciona uma meta box pra inserir conteúdo personalizado
function servico_orcamento_meta_box() {        
    add_meta_box(
        'meta_box_servico',
        'Propriedades',
        'servico_orcamento_meta',
        'servicos_orcamento',
        'normal',
        'high'
    );
}
//Adiciona meta fields a API
function servicos_orcamento_api() {
    
    register_rest_field( 'servicos_orcamento', 'servico_orcamento_preco', 
    	array(
        	'get_callback' => 'theme_get_api'
    	) 
    );
    register_rest_field( 'servicos_orcamento', 'has_children', 
    	array(
        	'get_callback' => 'theme_has_children'
    	) 
    );
}

add_action( 'rest_api_init', 'servicos_orcamento_api' );

// Configura os campos da meta box e imprime na tela do admin
function servico_orcamento_meta(){
    global $post;
    
    $servico_orcamento_preco = get_post_meta( $post->ID, 'servico_orcamento_preco', true );
    
?>
    <div class="form-field">
        <label for="servico_orcamento_preco">Preço</label><br>
        <input type="text" name="servico_orcamento_preco" id="servico_orcamento_preco" value="<?php echo $servico_orcamento_preco; ?>" />
    </div>
    <br>
<?php
}

// Salva os dados do meta box ao salvar o depoimento
add_action( 'save_post', 'save_servico_orcamento_post' );

function save_servico_orcamento_post( $post_id ) {
    if (!isset( $_POST['_inline_edit'] )){
        if ( get_post_type( $post_id ) == 'servicos_orcamento' && isset($_POST['servico_orcamento_preco'])) {

            $fields = [
                'servico_orcamento_preco',
            ];
            $values = [];

            foreach($fields as $field){
                if(isset($_POST[$field])) {
                    array_push($values, ['field' => $field, 'value' => $_POST[$field]]);
                }
            }
            
            //Chama a função para salvar os posts meta
            if(count($values) > 0){
                foreach( $values as $value ){
                    theme_save_post_meta( $post_id, $value['field'], $value['value'] );
                }
            }
        }
    }
}

?>