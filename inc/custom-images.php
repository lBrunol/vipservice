<?php

/*
 * Classe auxiliar que permite adicionar novos campos de imagem a posts e custom types
 * Author: Darren Krape
 * Edited by: Angelo Santos/Foster
 */

class CustomPostImages {
    public function __construct($types, $images, $isTaxonomy) {
        $this -> types = $types;
        $this -> images = $images;
        $this -> isTaxonomy = $isTaxonomy;
        
        add_action( 'add_meta_boxes', array( $this, 'cpi_add_meta_box' ) );
		add_action( 'save_post', array( $this, 'cpi_save' ) );
	    add_action( 'admin_print_styles', array( $this, 'cpi_admin_styles' ) );
	    add_action( 'admin_enqueue_scripts', array( $this, 'cpi_image_enqueue' ) );
    }
    
	public function cpi_add_meta_box( $post_type ) {
        if ( in_array( $post_type, $this->types ) ) {
			add_meta_box(
				'cpi_meta_box',
				__('Imagens Adicionais', 'cpi-textdomain' ),
				array( $this, 'cpi_render_meta_box_content' ),
				$post_type,
				'advanced',
				'high'
			);
        }
	}
    
	public function cpi_render_meta_box_content( $post ) {
		wp_nonce_field( basename( __FILE__ ), 'cpi_nonce' );
		$cpi_stored_meta = get_post_meta( $post->ID );
		echo '<ul id="cpi">';
		foreach( $this->images as $cpi_image ) {
            $cpi_type_name = "cpi-type-" . $cpi_image['slug'];
            ?>

            <li class="cpi-upload <?php if ( isset ( $cpi_stored_meta[$cpi_type_name] ) ) echo 'active'; ?>" id="<?php echo $cpi_type_name; ?>">
                <p class="cpi-upload-header"><?php echo $cpi_image['title']; ?></p>
                <div class="cpi-upload-thumbnail">
                    <?php
                    	if ( isset( $cpi_stored_meta[$cpi_type_name] ) ) {
	                        if( $cpi_stored_meta[$cpi_type_name] ) 
	                            echo wp_get_attachment_image( $cpi_stored_meta[$cpi_type_name][0], 'full', false );
                    	}
                    ?>
                </div>

                <input type="button" class="button cpi-button cpi-upload-button" value="<?php _e( 'Selecionar imagem ', 'cpi-textdomain' )?>" />
                <button type="button" class="button cpi-button cpi-upload-clear button-primary"><?php _e( 'Remover imagem ', 'cpi-textdomain' )?></button>
                <input class="cpi-upload-id" type="hidden" name="<?php echo $cpi_type_name ?>" value="<?php if ( isset ( $cpi_stored_meta[$cpi_type_name] ) ) echo $cpi_stored_meta[$cpi_type_name][0]; ?>" />
            </li>

            <?php
		}
		
		echo '<ul>';
	}
    
	public function cpi_save( $post_id ) {
		if ( ! isset( $_POST['cpi_nonce'] ) )
			return $post_id;

		$nonce = $_POST['cpi_nonce'];
        
		if ( ! wp_verify_nonce( $nonce, basename( __FILE__ ) ) )
			return $post_id;
        
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
        
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
        

        foreach( $this->images as $cpi_image ) {
            $cpi_type_name = "cpi-type-" . $cpi_image['slug'];

            if ( array_key_exists( $cpi_type_name, $_POST ) ) {
                $cpi_data = sanitize_text_field( $_POST[ $cpi_type_name ] );
            
                if( $cpi_data ) {
                    update_post_meta( $post_id, $cpi_type_name, $cpi_data );
                } else {
                    delete_post_meta( $post_id, $cpi_type_name );
                }
            }
		}
	}
    
	public function cpi_image_enqueue() {
		global $typenow;
        if ( in_array( $typenow, $this->types )) {
			
		}
	}
    
	public function cpi_admin_styles() {
		global $typenow;
        
        if ( in_array( $typenow, $this->types )) {
            wp_enqueue_style( 'cpi_meta_box_styles', get_template_directory_uri() . '/css/custom-images.css' );
    
            wp_enqueue_media();
            wp_register_script( 'cpi-meta-box-image', get_template_directory_uri() . '/js/custom-images.js' );
            wp_localize_script( 'cpi-meta-box-image', 'meta_image',
                array(
                    'title' => __( 'Selecione ou envie uma nova imagem', 'cpi-textdomain' ),
                    'button' => __( 'Selecionar imagem', 'cpi-textdomain' ),
                )
            );

            wp_enqueue_script( 'cpi-meta-box-image' );
		}
	}
}

?>