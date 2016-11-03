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

require_once "./d8-charger-yaml/y.php";

$archivos_ruta = './d8-charger-yaml/yamls/';
$archivos_yaml = file_scan_directory($archivos_ruta, '/[.*.y.?ml]$/');

foreach ($archivos_yaml as $y){
        $Coso = spyc_load_file($y->uri);
        if ($debug){
            drush_log( print_r($Coso) ,'ok');
        }

    if ($drupal){
        $t = array_keys($Coso);
        $tt = $t[0];

        // Taxonomias
        $f_tags = taxonomizame_la_nutria($Coso[$tt]['ETIQUETAS'], 'tags');
        $f_tipo = taxonomizame_la_nutria($Coso[$tt]['TIPO'], 'tipo');
        $f_linea = taxonomizame_la_nutria($Coso[$tt]['LINEA'], 'lineas');

        $node = Node::create([
            'language'             => 'LANGUAGE_NONE',
            'type'                 => 'producto',
            'title'                => $tt,
            'field_codigo_interno' => array('value'=>$Coso[$tt]['CODIGO-INTERNO']),
            'field_descripcion'    => array('value'=>$Coso[$tt]['DESCRIPCION']),
            'field_nombre'         => array('value'=>$Coso[$tt]['NOMBRE']),
            'field_presentacion'   => array('value'=>$Coso[$tt]['PRESENTACION']),
            //// ---------------------------------------------
            //'field_tipo'           => array('term_id'=> $f_tipo),
            //'field_tags'           => array('term_id'=> $f_tags),
            //'field_linea'          => array('term_id'=> $f_linea),
            // ---------------------------------------------
            //'field_imagen'         => array('value'=>$Coso[$tt]['IMAGEN']),
        ]);
        $node->save();
    }
}

function taxonomizame_la_nutria($palabra, $vocabulario){
    if ($vocabulario == "lineas"){
        //Linea tiene jerarquia
    } else {
        $debug = 1;
        $g = "";//$palabra;
        if(is_array($palabra)){
            foreach($palabra as $a){
                if (is_array($a)){
                    $g .= implode("-",$a);
                } else {
                    $g .= "-" . $a;
                }
            }
        } else {
            $g = "-" . $palabra;
        }
        if ($debug){
            //drush_log(print_r($palabra));
            //drush_log(print_r($vocabulario));
            drush_log(print_r($g));
        }

        //if ($terms = taxonomy_term_load_multiple_by_name($g,$vocabulario)){
        //$term = reset($terms);
        //} else {
            //$term = Term::create([
                //'name' => $palabra,
                //'vid' => $vocabulario,
            //])->save();
            //if($debug){ 
            //drush_log(print $term->id(), 'ok');
            //}
        }
        //return $term->id();
        //}
}


?>

