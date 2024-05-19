<?php

require "../vendor/autoload.php";
//$strHtml=include('../files-handler-core/templates/file.php');
include_once '../files-handler-core/templates/file.php';

use Mpdf\HTMLParserMode;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

   function uploadFile($file_name,$file_type,$file_size,$file_tmp_name,$file_error){

    $dir_subida="C:/Users/hbarb/OneDrive/fuentes/atg/back/apext-api/api/tmp/";
    $fichero_subido = $dir_subida . basename($file_name);
    
    print_r("\n El Archivo_ ".$fichero_subido."\n");

    if (move_uploaded_file( $file_tmp_name,$fichero_subido)) {
        echo json_encode(
            array(
                "result" =>"El fichero es válido y se subió con éxito."               
            ));        
    }else {
        echo json_encode(
            array(
                "result" =>"¡Posible ataque de subida de ficheros!"               
            ));        
    }
   } 
 
   function readFileXlsx($nameFile){

    $rutaArchivo = 'C:/Users/hbarb/OneDrive/fuentes/atg/back/apext-api/api/tmp/'.$nameFile;
    $spreadsheet = IOFactory::load($rutaArchivo);
    $hoja = $spreadsheet->getActiveSheet();
    
    $salida=array();

    foreach ($hoja->getRowIterator() as $fila) {

        foreach ($fila->getCellIterator() as $celda) {
            
               array_push($salida,$celda->getCalculatedValue());
              
            
            //echo $celda->getCalculatedValue() . " \n "; // Imprime el contenido de la celda
        }
        //echo "<br>";
        print_r($salida);
    }
} 

function generateDocPdf(){
    header("Content-Type:  application/pdf; charset=utf-8");
    $strHtml=include('../files-handler-core/templates/file.php');
    $mpdf = new \Mpdf\Mpdf();
    //--function WriteHTML($html, $mode = HTMLParserMode::DEFAULT_MODE, $init = true, $close = true)
    $mpdf->WriteHTML($templateHTML,4,true,false );
    $mpdf->WriteHTML($templateHTML,4,false,true );
    $mpdf->Output();
}

?>