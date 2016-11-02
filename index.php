<?php

/* -------------------------------------------------------
Cargador:   
    carga yamls a drupal 8 mediante la gracia de Spyc.
------------------------------------------------------- */
$debug = 0;
$drupal = 1;


use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

require_once "./d8-charger-yaml/y.php";



$archivos_ruta = './d8-charger-yaml/yamls/';
//$archivos_yaml = scandir($archivos_ruta);
$archivos_yaml = file_scan_directory($archivos_ruta, '/[.*.y.?ml]$/');

foreach ($archivos_yaml as $y){
    //if (preg_match('/\.y.?ml$/',$y)){
    //$Coso = spyc_load_file($archivos_ruta . $y);
        $Coso = spyc_load_file($y->uri);
        if ($debug){
            print "<span>" . $y . "</span>";
            print "<pre>";
            print_r( $Coso );
            print "</pre>";
            print "<hr />";
        }

    if ($drupal){
        //mandar cosas a Drupal
        $node = Node::create([
                //'language'             => LANGUAGE_NONE,
            'type'                 => 'producto',
            'title'                => $Coso['CODIGO'],
            'field_codigo_interno' => $Coso['CODIGO-INTERNO'],
            'field_descripcion'    => $Coso['DESCRIPCION'],
            'field_tags'           => $Coso['ETIQUETAS'],
            'field_imagen'         => $Coso['IMAGEN'],
            'field_linea'          => $Coso['LINEA'],
            'field_nombre'         => $Coso['NOMBRE'],
            'field_presentacion'   => $Coso['PRESENTACION'],
            'field_tipo'           => $Coso['TIPO'],
        ]);
        $node->save();
    }
    
    
    //foreach ($yaml['categories'] as $category) {
        //$term = reset(taxonomy_get_term_by_name($category));
        //$node->field_category[$node->language][]['tid'] = $term->tid;
    //}
    //$node->body[ $node->language ][0]['value'] = $Coso['body'];

    
    //}
}


?>

