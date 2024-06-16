<?php
/**
 * @author haimerb <hbarbetti.ing@gmail.com>
 * @see https://github.com/haimerb* 
 **/
require "../vendor/autoload.php";
include_once '../files-handler-core/templates/file.php';

include_once '../core/model/certificate.php';
// include_once './config/database.php';
// $configs=include('./config/config.php');


use Mpdf\HTMLParserMode;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;



function uploadFile($file_name, $file_type, $file_size, $file_tmp_name, $file_error)
{

    $dir_subida = "C:/Users/hbarb/OneDrive/fuentes/atg/back/apext-api/api/tmp/";
    $fichero_subido = $dir_subida . basename($file_name);

    //print_r("\n El Archivo_ " . $fichero_subido . "\n");

    if (move_uploaded_file($file_tmp_name, $fichero_subido)) {
        echo json_encode(
            array(
                "result" => "El fichero es válido y se subió con éxito.",
                "code" => "200"
            )
        );
    } else {
        echo json_encode(
            array(
                "result" => "¡Posible ataque de subida de ficheros!"
            )
        );
    }
}

function readFileXlsx($nameFile,$conn,$whitOutSave,$ini,$end) {

    $salida =array();
  

    $rutaArchivo = 'C:/Users/hbarb/OneDrive/fuentes/atg/back/apext-api/api/tmp/' . $nameFile;
    $spreadsheet = IOFactory::load($rutaArchivo);
    $hoja = $spreadsheet->getActiveSheet();

    

    foreach ($hoja->getRowIterator() as $fila) {

        foreach ($fila->getCellIterator() as $celda) {

            array_push($salida, $celda->getCalculatedValue());
            //Aqui se graba la data en la base de datos
            // if($whitOutSave!==null&&$whitOutSave===true) {

            // }

            //echo $celda->getCalculatedValue() . " \n "; // Imprime el contenido de la celda
        }
        //echo "<br>";
        //print_r($salida);
    }
    $toSave=array();
    $tmpTransformed=array();
    $cont=0;
    $tipoRet="";
    $nit="";
    $razonSocial="";
    $nombreConcepto="";
    $base="";
    $valorRetenido="";
    $porcentaje="";
    for ($i=8; $i<sizeof($salida) ;$i++) {
        if($cont===1){
            $tipoRet=$salida[$i];
        }elseif($cont===2){
            $nit=$salida[$i];
        }elseif($cont===3){ 
            $razonSocial=$salida[$i];
        }elseif($cont===4){
            $nombreConcepto=$salida[$i];
        }elseif($cont===5){
            $base=$salida[$i];
        }elseif($cont===6){
            $valorRetenido=$salida[$i];
        }elseif($cont===7){
            $porcentaje=$salida[$i];
        }elseif($cont===8){
            array_push($toSave,array(
                "tipoRetencion"=>$tipoRet,
                "nit"=>$nit,
                "razonSocial"=>$razonSocial,
                "nombreConcepto"=>$nombreConcepto,
                "base"=>$base,
                "valorRetenido"=>$valorRetenido,
                "porcentaje"=>$porcentaje
            ));
            $cont=-1;
        }
        $cont+=1;
    }
    //print_r($toSave);
    $workArray=array();
    $iterator=0;
    //print_r($whitOutSave." WITHOUT");
    if($whitOutSave===true){

        foreach($toSave as $save){
            $workArray=$save;
            if($iterator>8){
                saveArray($workArray,$ini,$end,$conn);
            }
            $iterator+=$iterator+1;
        }

        
    }
    
}


function saveArray($arr,$ini,$end,$conn){
      $query = "INSERT INTO certificate_data_ica
                SET tipo_retencion = :tipo_retencion,
                    nit = :nit,
                    razon_social = :razon_social,
                    nombre_concepto = :nombre_concepto,
                    base = :base,
                    valor_retenido = :valor_retenido,
                    porcentaje = :porcentaje,
                    year_tribute = '2023'
                    
                    ";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':tipo_retencion', $arr["tipoRetencion"]);
    $stmt->bindParam(':nit', $arr["nit"]);
    $stmt->bindParam(':razon_social', $arr["razonSocial"]);
    $stmt->bindParam(':nombre_concepto', $arr["nombreConcepto"]);
    $stmt->bindParam(':base', $arr["base"]);
    $stmt->bindParam(':valor_retenido', $arr["valorRetenido"]);
    $stmt->bindParam(':porcentaje', $arr["porcentaje"]);
    // $stmt->bindParam(':year_tribute',  "2023");
    //$stmt->bindParam(':range_ini', $arr["range_ini"]);
    //$stmt->bindParam(':range_end', $arr["range_end"]);

    //PDO::PARAM_INT
    
    if($stmt->execute()){
        //http_response_code(200);
        echo json_encode(array("message" => "registro was successfully create."));
    }
    else{
        //http_response_code(400);
    
        echo json_encode(array("message" => "Unable to register the certificate."));
    }
}


function  getCertificatesByOrg($conn):array {
    $query ='select * 
               from certificados_generados cg 
               inner join certificates_x_values cxv ON cxv.id_certificados_generado =cg.id_certificados_generado 
               inner join values_certificates vc on vc.id_values =cxv.id_values
             ';

    $stmt = $conn->prepare( $query );
    //$stmt->bindParam(1, $email);
    $stmt->execute();
    $num = $stmt->rowCount();

    
    $certificates=[];
    if($num>0) {
        //echo "Ingresó";
        $row = $stmt->fetchAll();
        $object = new stdClass();
        //echo json_encode($row);
        for ($i=0; $i<$num;$i++)
        {
            array_push($certificates,array(
                "id_certificados_generado"=>$row[$i]["id_certificados_generado"],
                "tipo_certificado"=>$row[$i]["tipo_certificado"],
                "nombre"=>$row[$i]["nombre"],
                "organization_asociate"=>$row[$i]["organization_asociate"],
                "ret_razon_social"=>$row[$i]["ret_razon_social"],
                "ret_nit"=>$row[$i]["ret_nit"],
                "ret_concepto_retencion"=>$row[$i]["ret_concepto_retencion"],
                "anio_gravable"=>$row[$i]["anio_gravable"],
                "id_certificate_value"=>$row[$i]["id_certificate_value"],
                "id_values"=>$row[$i]["id_values"],
                "concepto"=>$row[$i]["concepto"],
                "base_retencion"=>$row[$i]["base_retencion"],
                "valor_retenido"=>$row[$i]["valor_retenido"]
            ));
        }
        //echo $certificate->getId_certificados_generado();
        //return json_encode($certificates);

        //echo json_encode($certificates);
        return $certificates;
    }         
 
}

function generateDocPdf($conn)
{   
    ini_set("memory_limit","-1");
    header("Content-Type:  application/pdf; charset=utf-8");

    $certificatesData=array();
    $certificatesData=getCertificatesByOrg($conn);

    $servicios="";
    $compras="";
    $valor_ret_servicios="";
    $valor_ret_compra="";
    $regxServicios="Servicios";
    $regxCompra="Comercial";

    $razonSocialRet=$certificatesData[0]["ret_razon_social"];
    $nitRet=$certificatesData[0]["ret_nit"];
    $porcentRetServicios=0;

    for($i=0;$i<count($certificatesData);$i++){
        if(strpos($certificatesData[$i]["concepto"], $regxServicios)!==false ){
            $servicios.='<td>$ '.$certificatesData[$i]["base_retencion"].'</td>';
            $valor_ret_servicios.='<td>$ '.$certificatesData[$i]["valor_retenido"].'</td>';
        }

        if(strpos($certificatesData[$i]["concepto"], $regxCompra)!==false ){
            $compras.='<td>$ '.$certificatesData[$i]["base_retencion"].'</td>';
            $valor_ret_compra.='<td>$ '.$certificatesData[$i]["valor_retenido"].'</td>';
        }
        
    }
    
    $strHtml = include ('../files-handler-core/templates/file.php');
    

    $logger = new Logger('logger');
    $logger->pushHandler(new StreamHandler('logger.log', Logger::DEBUG));


    $mpdf = new \Mpdf\Mpdf(['orientation' => 'P',__DIR__ . '/../tmp']);
    $mpdf->setLogger($logger);
    $mpdf->allow_charset_conversion=true;
    //$mpdf->charset_in='utf-8';
    $mpdf->useSubstitutions = false; 
    $mpdf->simpleTables = true;
    //$mpdf->text_input_as_HTML = true;
    //$mpdf->useDefaultCSS2 = true;

    //--function WriteHTML($html, $mode = HTMLParserMode::DEFAULT_MODE, $init = true, $close = true)

    //$stylesheet = file_get_contents('style.css');

    //$stylesheet=include('/style.css');
    //print_r($stylesheet);
    
    $mpdf->WriteHTML(
        'body {
            font-family: Arial, sans-serif;
            border-style: solid;
            font-size: 8.4pt;
        } 
        .header {
            text-align: center;
            border-bottom: 1.4px solid;
            border-top: 1.4px solid;
            border-left: 1.4px solid;
            border-right: 1.4px solid;
        }  
        
        .content {
            //margin: 20px;
            align-items: center;
            justify-content: center;

             border-bottom: 1.4px solid;
            //border-top: 1.4px solid;
            border-left: 1.4px solid;
            border-right: 1.4px solid;
            
            
        }
        
        .section {
            //margin-bottom: 20px;      
            border-top: 1.4px solid;      
        }
        
        .section-title {
            font-weight: bold;
            align-items: center;
            justify-content: center;
            display: flex;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            border-style: solid;
            border-bottom: 1.4px solid;
        }
        
        .table th,
        .table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            border-style: solid;
            border-bottom: 1.4px solid;
        }
        
        .footer {
            text-align: left;
            border-style: solid;                    
        }    
        .logo{
            width: 354px;
            height: 86px;
        }
        .middle{
            border-style: solid;
        }        
        .table-middel{
            justify-content:center; 
            align-items: center;
            justify-content: center;
            display: flex;
            width: 100%;
            border-collapse: collapse;             
        }        
        .table-middel th,
        .table-middel td {
            padding: 6px; 
            text-align: left;
            border-style: solid;            
        }
        body p{
            font-size: large;
        }
        body.disclamer p{
            font-size: small;
        }',
         \Mpdf\HTMLParserMode::HEADER_CSS,true,true);

    $mpdf->WriteHTML( 
            '            
            <div class="header" style="text-align: center; border-bottom: 2px solid;" align="center">
                <img src="../logoapex.png" alt="logo" class="logo"/>        
             </div>
                <div class="content">
                    <div class="section" style="margin-bottom: 20px;">
                        <div class="section-title" style="font-weight: bold; align-items: center; justify-content: center; display: flex;">
                            <h1>CERTIFICADO DE RETENCIÓN EN LA FUENTE</h1>
                        </div>
                        <div class="section-title">
                            <h2>
                                IDENTIFICACIÓN DEL RETENEDOR
                            </h2>                
                        </div>
                        <p> &nbsp; &nbsp;Razón Social: ..........................................: APEX TOOL GROUP S.A.S</p>
                        <p> &nbsp; &nbsp;NIT: ..........................................................: 00311300</p>
                        <p> &nbsp; &nbsp;Dirección: .................................................: AVCL 26 69 D 91 TO 1 OF 406 BOGOTA</p>
                        <p> &nbsp; &nbsp;Año Gravable: ..........................................: 2023</p>
                    </div>
                    <div class="section middle">
                        <div class="section-title middle">
                            <h2>
                                &nbsp; IDENTIFICACIÓN DE LA PERSONA O ENTIDAD A QUIEN SE PRACTICÓ LA RETENCIÓN
                            </h2>
                        </div>
                        <p> &nbsp; Apellidos y Nombres o Razón Social: .........: '.$razonSocialRet.'</p>
                        <p> &nbsp; NIT...............................................................: '.$nitRet.'</p>
                        <p> &nbsp; Concepto de la retenciónn .......................................: COMPRAS Y/O SERVICIOS</p>
                    </div>
                    <table class="table-middel" border="1">
                        <tr>
                            <th>CONCEPTO</th>
                            <th>BASE DE RETENCION</th>
                            <th>VALOR RETENIDO</th>
                        </tr>

                        <tr>
                            <td>SERVICIOS 4%</td>'
                            .$servicios
                            .$valor_ret_servicios.
                            '
                        </tr>
                        <tr>
                            <td>COMPRAS 2,5%</td>$ '
                            .$compras
                            .$valor_ret_compra 
                            .'
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td>$34.557.990</td>
                            <td>$1.284.133</td>
                        </tr>

                    </table>
                </div>    
                <div class="footer">
                    <div class="disclamer">
                        <p align="justify">
                           Este documento no requiere para su validez firma autógrafa de acuerdo con el articulo 10 del Decreto 836 de 1991, recopilado en el articulo 1.6.1.12.12 del DUT 1625 de Octubre 11 de 2016, que regula el contenido del certificado de retenciones a título de Renta. 
                        </p>
                    </div>
                    <p>FECHA DE EXPEDICIÓN: 
                    <strong>'
                    .date("Y-m-d").' 
                    </strong>
                    </p>
                </div>', \Mpdf\HTMLParserMode::HTML_BODY,true,true);
    $mpdf->Output();
}

?>