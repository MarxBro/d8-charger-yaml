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
use Drupal\taxonomy\Entity;
use Drupal\taxonomy\Entity\Term;
//use Drupal\taxonomy\Entity\File;

require_once "./d8-charger-yaml/y.php";

$archivos_ruta = './d8-charger-yaml/yamls/';
$archivos_ruta_imgs = './d8-charger-yaml/yamls/';
$archivos_yaml = file_scan_directory($archivos_ruta, '/[.*.y.?ml]$/');
$archivos_imgs = file_scan_directory($archivos_ruta_imgs, '/[.*.jp.?g]$|[.*.png]$|[.*.gif]$/');

foreach ($archivos_yaml as $y){
        $Coso = spyc_load_file($y->uri);
        //if ($debug){
            //drush_log( print_r($Coso) ,'ok');
            //}

    if ($drupal){
        $t = array_keys($Coso);
        $tt = $t[0];

        // Taxonomias
        $f_linea = taxonomizame_la_nutria($Coso[$tt]['LINEA'], 'lineas'); // Jerarquía
        $f_tags = taxonomizame_la_nutria($Coso[$tt]['ETIQUETAS'], 'tags');
        $f_tipo = taxonomizame_la_nutria($Coso[$tt]['TIPO'], 'tipo');
        if ($debug){
            //drush_log($f_tags,'ok');    
            //drush_log($f_tipo,'ok');    
            drush_log($f_linea,'ok');    
        }

        $node = Node::create([
            'language'             => 'LANGUAGE_NONE',
            'type'                 => 'producto',
            'title'                => $tt,
            'field_codigo_interno' => array('value'=>$Coso[$tt]['CODIGO-INTERNO']),
            'field_descripcion'    => array('value'=>$Coso[$tt]['DESCRIPCION']),
            'field_nombre'         => array('value'=>$Coso[$tt]['NOMBRE']),
            'field_presentacion'   => array('value'=>$Coso[$tt]['PRESENTACION']),
            //// ---------------------------------------------
            'field_tipo'           => $f_tipo,
            'field_tags'           => $f_tags,
            'field_linea'          => $f_linea,
            // ---------------------------------------------
            //'field_imagen'         => array('value'=>$Coso[$tt]['IMAGEN']),
        ]);
        $node->save();
    }
}

function taxonomizame_la_nutria($palabra, $vocabulario){
    $ids = [];
        $debug = 1;
        $g = [];//$palabra;
        if(is_array($palabra)){
            foreach($palabra as $a){
                if (is_array($a)){
                    array_push($g,$a);
                } else {
                    $g[] = $a;
                }
            }
        } else {
            $g[] = $palabra;
        }
        
        foreach($g as $tt){
            if ($terms = taxonomy_term_load_multiple_by_name($tt,$vocabulario)){
                $term = reset($terms);
                if ($vocabulario == 'lineas'){
                    if ($term->id() > 10 ){
                        array_push($ids,array('target_id' => $term->id()));
                    }
                } else{
                    array_push($ids,array('target_id' => $term->id()));
                }
            } else {
                $term = Term::create([
                        'name' => $tt,
                        'vid' => $vocabulario,
                ]);
                $term->save();    
                array_push($ids,array('target_id' => $term->id()));
            }
        }
        if(!empty($ids)){
            return $ids;
        }
}

//Para el archivo
/*
// Create file object from a locally copied file.
$uri  = file_unmanaged_copy('public://source.jpg', 'public://destination.jpg', FILE_EXISTS_REPLACE);
$file = File::Create([
  'uri' => $uri,
]);
$file->save();
 
// Load existing node and attach file.
$node = Node::load(1);
$node->field_image->setValue([
  'target_id' => $file->id(),
]);
$node->save();
*/
?>

