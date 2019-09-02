<?php
/**
 * Plugin Name:       carouF
 * Description:       Permet de faire un caroussel
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Nigma
 */

add_action('init', "carouF_init");
add_action("add_meta_boxes", "carrousel_metaboxes");
add_action("save_post", "carrousel_savePost", 10, 2);

/**
 * Permet d'initialiser mon plugins
 */
function carouF_init(){

    $labels = array(
        "name" => "Carrousel",
        "singular_name" => "Carrousel",
        "add_new" => "Ajouter un Carrousel",
        "add_new_item" => "Ajouter un nouveau Slide",
        "new_item" => "Nouveau Carrousel",
        "view_item" => "Voir Carrousel",
        "search_item" => "Rechercher un Carrousel",
        "not_found" => "aucun Carrousel",
        "not_found_in_trash" => "aucun Caroussel dans la corbeille",
        "parent_item_colon" => "",
        "menu_name" => "Carrousel"

    );

    register_post_type("slide", array(
        "public" => true,
        "publicity_queryable" => false,
        "labels" => $labels,
        "menu_position" => 8,
        "capability_type" => "post",
        "supports" => array("title", "thumbnail")
    ));

    add_image_size("Caroussel", 1000, 3000, true);
}

/**
 * permet de gerer la meta-box
 */
function carrousel_metaboxes(){
    add_meta_box("MonCarrousel", "lien", "carrousel_metabox");
}

/**
 * permet de gerer le lien
 * @param $objet
 */
function carrousel_metabox($objet){
    //wp_nonce_field a vérifier
    ?>
    <div class="meta-box-item-title">
        <h4>lien de cette photo</h4>
    </div>
    <div class="meta-box-item-content">
        <input type="text" name="caroussel_lien" style="width: 100%;" value="<?= esc_attr(get_post_meta( $objet->ID, "_monLink", true )) ?>">
    </div>
    <?php

}

/**
 * permet d'enregistrer le lien
 * @param $post_id
 * @param $post
 * @return mixed
 */
function carrousel_savePost($post_id, $post){

    if(!isset($_POST["caroussel_lien"])){
        return $post_id;
    }

    $type = get_post_type_object($post->post_type);
    if(!current_user_can($type->cap->edit_post)){
        return $post_id;
    }

    update_post_meta($post_id, "_monLink", $_POST["caroussel_lien"]);
}

/**
 * permet l'affichage du plugins
 * @param int $limit
 */
function carouf_show($limit = 10){

    wp_enqueue_script("jquery", plugins_url("/carouF/js/jquery.js"), null, "3.4.1", true);
    wp_enqueue_script("caroufredsel", plugins_url("/carouF/js/jquery.carouFredSel-6.2.1.js"), null, "5.6.4", true);

    add_action("wp_footer", "carrousel_script", 30);

    $carrousel = new WP_Query("post_type='slide&post_per_page=$limit");
    ?>
    <div id="Mon-carrousel">
    <?php
        while ($carrousel->have_posts()){
            $carrousel->the_post();
            global $post;
            the_post_thumbnail("Caroussel", array("style" => "width:1000px !important"));
        }
     ?>
    </div>
    <?php
}

/**
 * initialise le js
 */
function carrousel_script(){
    ?>
    <script type="text/javascript">

        (function($){
            $("#Mon-carrousel").caroufredsel();
        })(jQuery);

    </script>

    <?php
}