<?php 

/**
 * Tipo de conteúdo personalizado: Serviço
 *
 * Serviço principal e servico do meio
 *
 * @package Hcor
 */

function custom_type_orcamento() {

    // Define os textos que serão exibidos no admin
	$labels = array(
        'name' => 'Orçamentos',
        'singular_name' => 'Orçamento',
        'add_new' => 'Adicionar Novo',
        'add_new_item' => 'Novo Orçamento',
        'edit_item' => 'Editar Orçamento',
        'new_item' => 'Novo Orçamento',
        'view_item' => 'Ver Orçamentos',
        'search_items' => 'Procurar Orçamentos',
        'not_found' =>  'Nenhum Orçamento encontrado',
        'not_found_in_trash' => 'Nenhum Orçamento encontrado na lixeira',
        'menu_name' => 'Orçamentos',
        'all_items' => 'Todos os orçamentos'
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
        'register_meta_box_cb' => 'orcamento_meta_box',
        'rewrite' => false,
        'query_var' => false,
        'delete_with_user' => false,
        'hierarchical' => true
	);

	register_post_type( 'orcamento', $args );
}

add_action('init', 'custom_type_orcamento');

// Adiciona uma meta box pra inserir conteúdo personalizado
function orcamento_meta_box() {        
    add_meta_box(
        'meta_box_servico',
        'Propriedades',
        'orcamento_meta',
        'orcamento',
        'normal',
        'high'
    );
}

function orcamento_api() {
    //Meta Fields that should be added to the API 
    $meta_fields = array(
        'orcamento_nome',
        'orcamento_email',
        'orcamento_telefone',
        'orcamento_mensagem',
        'orcamento_servicos',
        'orcamento_desconto',
        'orcamento_status'
    );
    //Iterate through all fields and add register each of them to the API
    foreach ($meta_fields as $field) {
        register_rest_field( 'orcamento',
            $field,
            array(
                'get_callback'    => 'theme_get_api',
                'update_callback' => 'theme_update_api',
                'schema'          => null,
            )
        );
    }
}

add_action( 'rest_api_init', 'orcamento_api' );

// Configura os campos da meta box e imprime na tela do admin
function orcamento_meta(){
    global $post;
    
    $orcamento_nome = get_post_meta( $post->ID, 'orcamento_nome', true );
    $orcamento_email = get_post_meta( $post->ID, 'orcamento_email', true );
    $orcamento_telefone = get_post_meta( $post->ID, 'orcamento_telefone', true );
    $orcamento_mensagem = get_post_meta( $post->ID, 'orcamento_mensagem', true );
    $orcamento_servicos = get_post_meta( $post->ID, 'orcamento_servicos', true );
    $orcamento_desconto = get_post_meta( $post->ID, 'orcamento_desconto', true );
    $orcamento_status = get_post_meta( $post->ID, 'orcamento_status', true );
    $total = 0;
    
?>
    <div class="form-field">
        <label for="orcamento_nome">Nome</label><br>
        <input type="text" name="orcamento_nome" id="orcamento_nome" value="<?php echo $orcamento_nome; ?>" />
    </div>
    <div class="form-field">
        <label for="orcamento_email">E-mail</label><br>
        <input type="text" name="orcamento_email" id="orcamento_email" value="<?php echo $orcamento_email; ?>" />
    </div>
    <div class="form-field">
        <label for="orcamento_telefone">Telefone</label><br>
        <input type="text" name="orcamento_telefone" id="orcamento_telefone" value="<?php echo $orcamento_telefone; ?>" />
    </div>
    <div class="form-field">
        <label for="orcamento_mensagem">Mensagem</label><br>
        <input type="text" name="orcamento_mensagem" id="orcamento_mensagem" value="<?php echo $orcamento_mensagem; ?>" />
    </div>
    <p><strong>Serviços solicitados</strong></p>
    <?php if($orcamento_servicos) : ?>
        <?php if(count($orcamento_servicos) > 0) : ?>
            <table>
                <tr>
                    <th>Serviço</th>
                    <th>Valor</th>
                </tr>
                <?php foreach($orcamento_servicos as $orc) : ?>
                    <tr>
                        <?php
                            $servico = get_post($orc);
                            if($servico){
                                $preco = get_post_meta( $servico->ID, 'servico_orcamento_preco', true );
                                $total += $preco;
                                echo '<td>' . $servico->post_title . '</td><td> ' . $preco . '</td>';
                            }
                        ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div class="form-field">
                <label for="orcamento_desconto">Desconto</label><br>
                <input type="text" name="orcamento_desconto" id="orcamento_desconto" value="" />
            </div>
            <p><strong><?= $total ?></strong></p>
            <button type="button" class="button button-primary button-large">Enviar proposta ao cliente</button>
            <button type="button" class="button button-secondary button-large">Imprimir</button>
        <?php endif; ?>
    <?php endif ?>
    <div class="form-field">
        <label for="orcamento_status">Status</label><br>
        <select>    
            <option>Novo</option>
            <option>Rejeitado</option>
            <option>Negociando</option>
            <option>Aprovado</option>
            <option>Liquidado</option>
        </select>
    </div>
    <br>
<?php
}

// Salva os dados do meta box ao salvar o depoimento
add_action( 'save_post', 'save_orcamento_post' );

function save_orcamento_post( $post_id ) {
    if (!isset( $_POST['_inline_edit'] )){
        if ( get_post_type( $post_id ) == 'orcamento') {

            $fields = [
                'orcamento_nome',
                'orcamento_email',
                'orcamento_telefone',
                'orcamento_mensagem',
                'orcamento_servicos'
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