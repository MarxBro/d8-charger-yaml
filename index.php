<?php

/* -------------------------------------------------------
Cargador:   
    carga yamls a drupal 8 mediante la gracia de Spyc.

drush9 -v scr a.php

------------------------------------------------------- */
$debug = 1;
$drupal = 1;


use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

require_once "./d8-charger-yaml/y.php";

$archivos_ruta = './d8-charger-yaml/yamls/';
$archivos_yaml = file_scan_directory($archivos_ruta, '/[.*.y.?ml]$/');

foreach ($archivos_yaml as $y){
        $Coso = spyc_load_file($y->uri);
        if ($debug){
            drush_log( $Coso,'ok');
        }

    if ($drupal){
        $t = array_keys($Coso);
        $tt = $t[0];
        $node = Node::create([
            'language'             => 'LANGUAGE_NONE',
            'type'                 => 'producto',
            'title'                => $tt,
            'field_codigo_interno' => $Coso[$tt]['CODIGO-INTERNO'],
            'field_descripcion'    => $Coso[$tt]['DESCRIPCION'],
            'field_nombre'         => $Coso[$tt]['NOMBRE'],
            'field_presentacion'   => $Coso[$tt]['PRESENTACION'],
            // ---------------------------------------------
            'field_tipo'           => $Coso[$tt]['TIPO'],
            'field_tags'           => $Coso[$tt]['ETIQUETAS'],
            'field_linea'          => $Coso[$tt]['LINEA'],
            // ---------------------------------------------
            'field_imagen'         => $Coso[$tt]['IMAGEN'],
        ]);
        $node->save();
    }
}


?>

