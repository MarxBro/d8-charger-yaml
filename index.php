<?php

/* -------------------------------------------------------
Cargador:   
    carga yamls a nodos de drupal 8 mediante la gracia 
    de Spyc.
    
    Taxonomiza la cosa un poco y agrega imágenes...
    
    Usar así, desde el root de D8:
        
        drush9 -v scr carpete/index.php

------------------------------------------------------- */
$debug = 0;
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
        if ($debug){
            drush_log( print_r($archivos_imgs) ,'ok');
        }

    if ($drupal){
        $t = array_keys($Coso);
        $tt = $t[0];

        // Taxonomias
        $f_linea = taxonomizame_la_nutria($Coso[$tt]['LINEA'], 'lineas'); // Jerarquía
        $f_tags = taxonomizame_la_nutria($Coso[$tt]['ETIQUETAS'], 'tags');
        $f_tipo = taxonomizame_la_nutria($Coso[$tt]['TIPO'], 'tipo');
        if ($debug){
            drush_log(print_r($f_linea),'ok');    
        }

        //Copiar el archivo al public, registrarlo drupalezcamente y agregarlo al nodo
        $nombre_yaml_img= $Coso[$tt]['IMAGEN'];
        $desde          = $archivos_ruta_imgs . $nombre_yaml_img;
        $al_public_papu = 'public://' . $nombre_yaml_img;
        $uranio         = file_unmanaged_copy($desde, $al_public_papu, FILE_EXISTS_REPLACE);

        $filex = File::Create([
          'uri' => $uranio,
        ]);
        $filex->save();
        
        
        //NODO : Esto es lo mas importante.
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
            'field_imagen'         => array('target_id'=>$filex->id()),
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

?>

