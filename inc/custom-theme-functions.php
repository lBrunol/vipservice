<?php

    function theme_get_custom_logo(){
        $id = get_theme_mod('custom_logo');
        $image = wp_get_attachment_image_src($id, 'full');
        if($image)
            return $image[0];
        else 
            return '';
    }

    function theme_get_the_archive_title() {
        if ( is_category() ) {            
            $title = single_cat_title( '', false );
        } elseif ( is_tag() ) {            
            $title = single_tag_title( '', false );
        } elseif ( is_author() ) {            
            $title = get_the_author();        
        } elseif ( is_post_type_archive() ) {
            $title = post_type_archive_title( '', false );
        } elseif ( is_tax() ) {
            $tax = get_taxonomy( get_queried_object()->taxonomy );
            $title = sprintf( __( '%1$s: %2$s' ), $tax->labels->singular_name, single_term_title( '', false ) );
        } else {
            $title = 'Ministério Ebenézer';
        }

        return apply_filters( 'theme_get_the_archive_title', $title );
    }

    function theme_get_the_archive_description() {
        $description = isset( get_queried_object() -> description ) ? get_queried_object() -> description : false;
    
        return apply_filters( 'theme_get_the_archive_description', $description );
    }

    /**
    * Prints HTML with meta information for the current post-date/time and author.
    */
    function theme_get_post_date( $post_id = null ) {
        $time_string = array();

        $time_string = array(
            'date_complete' => esc_attr( get_the_date( 'c', $post_id ) ),
            'date_friendly' => esc_html( get_the_date( '', $post_id ) )
        );
        
        if ( get_the_time( 'U', $post_id ) !== get_the_modified_time( 'U', $post_id ) ) {
            array_push( $time_string, array(
                'modified_date_complete' => esc_attr( get_the_modified_date( 'c', $post_id ) ),
                'modified_date_friendly' => esc_html( get_the_modified_date( '', $post_id ) )
            ) );
        }

        return $time_string;
            
    }

    function theme_get_the_post_tags() {

        if ( 'post' === get_post_type() ) {
            $tags_list = get_the_tag_list( '', esc_html__( ', ', 'theme' ) );
            if ( $tags_list ) {
                return $tags_list;
            }
        }

        return false;
    }

    /**
    * Retorna o objeto de inicialização do post type
    * @param int  $post_id  id do post da qual é necessário o objeto com as informaçãos do post type
    * @return retorna o objeto contendo as informações do post type 
    */
    function theme_get_post_type_data( $post_id = 0 ) {
        if ( $post_id ) {
            $archive_data = get_post_type_object( get_post_type( $post_id ) );
        } else {
            $archive_data = get_post_type_object( get_post_type() );
        }

        return $archive_data;
    }

    /**
    * Salva post meta no banco
    *
    * @param  int  $post_id  id do post
    * @param  string  $field  nome do campo na tabela de post meta
    * @param  mixed  $value  valor do campo
    *
    */
    function theme_save_post_meta( $post_id, $field, $value ){

        $old_value = get_post_meta( $post_id, $field, true );

        if ( !empty( $value ) ) {
            
            $new_value = $value;

            if ( !empty( $old_value ) ) {
                update_post_meta( $post_id, $field, $new_value );
            } else {
                add_post_meta( $post_id, $field, $new_value );
            }

        } else if( !empty( $old_value ) ) {
            delete_post_meta( $post_id, $field, $old_value );
        }
    }

    /**
     * Corta uma string de acordo com o comprimento passado via parâmetro.
     * @param int  $maxLength  Comprimento máximo da string.
     * @param string   $str  string que será cortada.
     * @return string retorna o texto cortado quando o comprimento for menor que o da string passada. Quando a string passada tem o comprimento maior
     * do que o passado via parâmetro, a função retornará a mesma string.
     */
    function theme_crop_text( $str, $maxLength = 100) {

        $outStr = $str;
        $lenghtSaida;

        if ( strlen( $outStr ) > $maxLength && $maxLength > 0 ) {
            $outStr = substr( $outStr, 0, $maxLength );
            $lenghtSaida = strlen( $outStr );
        } else {
            return $str;
        }

        if ( substr( $str, $lenghtSaida, 1 ) != ' ' ) {
            $lastSpace = strrpos( $outStr, " ");
            
            if ( $lastSpace !== false ) {
                $outStr = substr( $outStr, 0, $lastSpace );
            }
        }

        $outStr = $outStr . '...';

        return $outStr;
    }

    /*
    * Adiciona um meta termo à api rest
    */
    function theme_get_api( $object, $field_name, $request ) {
        return get_post_meta( $object[ 'id' ], $field_name, true );
    }

    /*
    * Atualiza/Adiciona postmeta
    */
    function theme_update_api( $value, $object, $field_name ) {
        return theme_save_post_meta( $object->ID, $field_name, $value );
    }

    /**
     * Verifica se existem filhos
     */
    function theme_has_children($object, $field_name, $request){
        $query = new WP_Query(array('post_parent' => $object[ 'id' ], 'post_type' => 'servicos'));
        return $query->have_posts();
        // return count(get_pages(array('child_of' => $object[ 'id' ] ))) > 0;
    }

?>