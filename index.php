<?php

/* -------------------------------------------------------
Cargador:   
    carga yamls a drupal 8 mediante la gracia de Spyc.
------------------------------------------------------- */
$debug = 0;
$drupal = 1;


require_once "y.php";
use Drupal\node\Entity\Node;


$archivos_ruta = 'yamls/';
$archivos_yaml = scandir($archivos_ruta);

foreach ($archivos_yaml as $y){
    if (preg_match('/\.y.?ml$/',$y)){
        $Coso = spyc_load_file($archivos_ruta . $y);
        if ($debug){
            print "<span>" . $y . "</span>";
            print "<pre>";
            print_r( $Coso );
            print "</pre>";
            print "<hr />";
        }

    if ($drupal){
        //mandar cosas a Drupal
        $node = new stdClass();
        $node->language = LANGUAGE_NONE;
        $node->type                 = 'producto';
        $node->title                = $Coso['CODIGO'];
        $node->field_codigo_interno = $Coso['CODIGO-INTERNO'];
        $node->field_descripcion    = $Coso['DESCRIPCION'];
        $node->field_tags           = $Coso['ETIQUETAS'];
        $node->field_imagen         = $Coso['IMAGEN'];
        $node->field_linea          = $Coso['LINEA'];
        $node->field_nombre         = $Coso['NOMBRE'];
        $node->field_presentacion   = $Coso['PRESENTACION'];
        $node->field_tipo           = $Coso['TIPO'];
        node_save($node);
    }
    
    
    //foreach ($yaml['categories'] as $category) {
        //$term = reset(taxonomy_get_term_by_name($category));
        //$node->field_category[$node->language][]['tid'] = $term->tid;
    //}
    //$node->body[ $node->language ][0]['value'] = $Coso['body'];

    
    }
}


?>

