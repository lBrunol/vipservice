<?php 
	function make_budget( WP_REST_Request $request ) {

        $parameters = $request->get_params();
        $servicos = [];
        $admin_mail = get_option('admin_email');
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $required_params = [
            [ 'type' => 'string', 'field' => 'nome' ],
            [ 'type' => 'string', 'field' => 'email' ],
            [ 'type' => 'string', 'field' => 'mensagem' ],
            [ 'type' => 'array', 'field' => 'servicos' ],
        ];

        foreach ($required_params as $item) {
            if($item['type'] === 'string'){
                if(!isset($parameters[$item['field']])){
                    return new WP_Error( 'parameters_required', 'Parâmetro ' . $item['field'] . ' é obrigatório', array( 'status' => 500 ) );
                }
            }else if ($item['type'] === 'array'){
                if(!isset($parameters[$item['field']])){
                    return new WP_Error( 'parameters_required', 'Parâmetro ' . $item['field'] . ' é obrigatório', array( 'status' => 500 ) );
                } else {                    
                    if(!is_array($parameters[$item['field']])){
                        return new WP_Error( 'parameter_invalid', 'Parâmetro ' . $item['field'] . ' está inválido. Deveria ser um Array.', array( 'status' => 500 ) );                    
                    }
                    if(count($parameters[$item['field']]) === 0){
                        return new WP_Error( 'parameters_required', 'Parâmetro ' . $item['field'] . ' é obrigatório', array( 'status' => 500 ) );
                    }
                }
            }
        }

        if(count($parameters['servicos']) > 0){
            foreach ($parameters['servicos'] as $service_id) {
                $servico = get_post($service_id);
                if($servico){
                    $preco = get_post_meta($servico->ID, 'servico_orcamento_preco', true );
                    array_push($servicos, [ 'id' => $servico->ID, 'nome' => $servico->post_title, 'preco' => $preco ]);
                } else {
                    return new WP_Error( 'service_id_invalid', 'ID ' . $service_id . ' do serviço é inválido', array( 'status' => 500 ) );                    
                }
            }
        }

        $orcamento_id = wp_insert_post([
            'post_title' => 'Orçamento - ' . $parameters['nome'],
            'post_type' => 'orcamento',
            'post_status' => 'publish',
        ], false);

        if($orcamento_id === 0)
            return new WP_Error( 'post_not_inserted', 'Post não inserido', array( 'status' => 500 ) );
            
        if($orcamento_id > 0){
            $meta_fields = array(
                'orcamento_nome',
                'orcamento_email',
                'orcamento_telefone',
                'orcamento_mensagem',
                'orcamento_servicos',
                'orcamento_desconto',
                'orcamento_status'
            );            

            if(isset($parameters['nome'])){
                add_post_meta($orcamento_id, 'orcamento_nome', $parameters['nome']);
            }
            if(isset($parameters['email'])){
                add_post_meta($orcamento_id, 'orcamento_email', $parameters['email']);
            }
            if(isset($parameters['telefone'])){
                add_post_meta($orcamento_id, 'orcamento_telefone', $parameters['telefone']);
            }
            if(isset($parameters['mensagem'])){
                add_post_meta($orcamento_id, 'orcamento_mensagem', $parameters['mensagem']);
            }
            if(count($servicos) > 0){
                add_post_meta($orcamento_id, 'orcamento_servicos', $servicos);
            }

            add_post_meta($orcamento_id, 'orcamento_desconto', 0);
            add_post_meta($orcamento_id, 'orcamento_status', 'Novo');

            $preco = 0;
            $content = "
                <p><b>Nome: </b>" . $parameters['nome'] . "</p>" .
                "<p><b>E-mail: </b>" . $parameters['email'] . "</p>" .
                "<p><b>Mensagem: </b>" . $parameters['mensagem'] . "</p>" .
                "<p><b>Serviço</b> - <b>Valor</b></p>"
                ;

            foreach($servicos as $servico){
                $content .= "<p>" . $servico['nome'] . " - " . $servico['preco'] . "</p>";
                $preco += $servico['preco'] * 1;
            }
            $content .= "<p><b>Total</b> - " . $preco . "</p>";

            wp_mail( $admin_mail, 'Novo orçamento - ' . $parameters['nome'], $content, $headers);
        } else {
            return new WP_Error( 'post_not_inserted', 'Post não inserido', array( 'status' => 500 ) );
        }
		
		// Cria a resposta 
		$response = new WP_REST_Response( get_post($orcamento_id) );

		// Seta o código para 201 (sucesso)
		$response -> set_status( 201 );

		return $response;
	}

	add_action ( 'rest_api_init', function() { 
			register_rest_route( 'vipservice/v1', '/budget', array(
					'methods' => 'POST',
					'callback' => 'make_budget',
				)
			);
		}
	);
?>