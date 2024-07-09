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
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;



function uploadFile($file_name, $file_type, $file_size, $file_tmp_name, $file_error, $conn)
{
    $outArry = array();
    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

    $dir_subida = "../api/tmp/";
    $fichero_subido = $dir_subida . basename($file_name);


    if (move_uploaded_file($file_tmp_name, $fichero_subido)) {
        $logger->info("El fichero es válido y se subió con éxito. Archivo: " . $file_name);
        // echo json_encode(
        //     array(
        //         "validatorExist" => validateFileExist('../api/tmp/' . $file_name),
        //         "result" => "El fichero es válido y se subió con éxito.",
        //         "code" => "200"
        //     ),
        //     JSON_INVALID_UTF8_IGNORE
        // );
        $outArry=
            array(
                "validatorExist" => validateFileExist('../api/tmp/' . $file_name),
                "result" => "El fichero es válido y se subió con éxito.",
                "code" => "200"
            );
        /**
         * fiele exist validations
         */
        $rutaArchivo = '../api/tmp/' . $file_name;
        $file_exist = validateFileExist($rutaArchivo);
        if ($file_exist) {
            //removedLastColumn($rutaArchivo);
            readFileXlsx($file_name, $conn, true,$outArry);
        }
    } else {

        $logger->error("Error: Error al cargar los arhivos");
        echo json_encode(
            array(
                "result" => "Error al cargar los arhivos"
            ),
            JSON_INVALID_UTF8_IGNORE
        );
    }
}

function removedLastColumn($nameFile) {
    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

    $logger->info("Seleccionado archivo " . $nameFile);

    $spreadsheet = IOFactory::load($nameFile);
    $hoja = $spreadsheet->getSheet(0);
    $hoja->removeColumn('I');
    //$cell = $hoja->getCell('A2');
    //$value = explode("A", $cell->getValue());
    
    $spreadsheet-> disconnectWorksheets();   
    
}

function validateFileExist($file)
{
    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

    if (file_exists($file)) {
        $logger->info("El archivo " . $file . " existe");
        return true;
    } else {
        $logger->info("El archivo  " . $file . " no existe..");
        return false;
    }
}


function getFromFilePeriod($nameFile, $indexSheet)
{
    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

    $logger->info("Seleccionado archivo " . $nameFile);

    $spreadsheet = IOFactory::load($nameFile);
    $hoja = $spreadsheet->getSheet($indexSheet);
    $cell = $hoja->getCell('A2');
    $value = explode("A", $cell->getValue());
 
    
    $spreadsheet-> disconnectWorksheets();   
    
    return
        json_encode(
            array(
                "since" => str_replace(' ', '', str_replace('De ', '', $value[0])),
                "until" => str_replace(' ', '', $value[1])
            ),
            JSON_INVALID_UTF8_IGNORE
        );
}


function getFromFileDataType($nameFile, $indexSheet)
{
    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

    $logger->info("Seleccionado archivo " . $nameFile);

    $spreadsheet = IOFactory::load($nameFile);
    $hoja = $spreadsheet->getSheet($indexSheet);
    $cell = $hoja->getCell('A1');
    $value = explode(" ", $cell->getValue());


    if ($value[1] == "ICA") {
        $value = 1;
    } else if ($value[1] == "IVA") {
        $value = 2;
    } else if ($value[1] == "Industria y Comenrcio") {
        $value = 3;
    } else {
        $value = 0;
    }
    
    $spreadsheet-> disconnectWorksheets();   
    
    return
        json_encode(
            array(
                "dataType" => $value
            ),
            JSON_INVALID_UTF8_IGNORE
        );
}

function getFromFileYearTribute($nameFile, $indexSheet)
{
    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

    $logger->info("Seleccionado archivo " . $nameFile);

    $spreadsheet = IOFactory::load($nameFile);
    $hoja = $spreadsheet->getSheet($indexSheet);
    $cell = $hoja->getCell('A2');
    $value = explode("/", $cell->getValue());
    $value = explode("A", $value[2]);
    
    
    
    $spreadsheet-> disconnectWorksheets();   
    
    return
        json_encode(
            array(
                "year_tribute" => $value[0]
            ),
            JSON_INVALID_UTF8_IGNORE
        );
}



function readIca()
{
    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

}

function readIva()
{

}

function readFileXlsx($nameFile, $conn, $whitOutSave,$outArry)
{
    $timeForSince = 0;
    $timeForUntil = 0;

    $dateFormat = "";
    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

    $salida = array();
    $rutaArchivo = '../api/tmp/' . $nameFile;
    $periodFromFile = array();
    $peridoSince = "";
    $periodUntil = "";
    $dataType = 0;
    $year_tribute = 0;

    //removedLastColumn($rutaArchivo);

    if (validateFileExist($rutaArchivo)) {

        removedLastColumn($rutaArchivo);

        $logger->info("Archivo: " . $nameFile . " Existe en: ../api/tmp/");

        $periodFromFile = getFromFilePeriod($rutaArchivo, 3);

        $logger->info("Periodo[ Desde: " . json_decode($periodFromFile)->since . " Hasta: " . json_decode($periodFromFile)->until . " ]");

        $timeForSince = strtotime(json_decode($periodFromFile)->since);
        $timeForUntil = strtotime(json_decode($periodFromFile)->until);

        $peridoSince = date('Y-m-d', $timeForSince);
        $periodUntil = date('Y-m-d', $timeForUntil);

        $logger->info("Periodo Transform[ Desde: " . $peridoSince . " Hasta: " . $periodUntil . " ]");

        $dataType = json_decode(getFromFileDataType($rutaArchivo, 3))->dataType;

        $logger->info("Data Type: " . $dataType);

        $year_tribute = json_decode(getFromFileYearTribute($rutaArchivo, 3))->year_tribute;

        $logger->info("Year_tribute: " . $year_tribute);

        $spreadsheet = IOFactory::load($rutaArchivo);
        $hoja = $spreadsheet->getSheet(0);
        //$hoja->removeColumn('I',1);
    
        $rowCount = $hoja->getHighestRow();

        foreach ($hoja->getRowIterator(1,$rowCount+1) as $fila) {

            foreach ($fila->getCellIterator('A','H') as $celda) {

                
                //$logger->info("getCalculatedValue: " . $celda->getCalculatedValue());
                array_push($salida, $celda->getCalculatedValue());

                //Aqui se graba la data en la base de datos
                // if($whitOutSave!==null&&$whitOutSave===true) {

                // }

                //echo $celda->getCalculatedValue() . " \n "; // Imprime el contenido de la celda
            }
            //echo "<br>";
            //print_r($salida);
        }

        $toSave = array();
        $tmpTransformed = array();
        $cont = 0;
        $tipoRet = "";
        $nit = "";
        $dv = "";
        $razonSocial = "";
        $nombreConcepto = "";
        $base = "";
        $valorRetenido = "";
        $porcentaje = "";

        $logger->info("salida: " . json_encode($salida, JSON_INVALID_UTF8_IGNORE) . " Cantidad: " . sizeof($salida));

        // if($dataType === 1){

        //     for ($i = 8; $i < sizeof($salida); $i++) {
        //         if ($cont === 1) {
        //             $tipoRet = $salida[$i];
        //         } elseif ($cont === 2) {
        //             $nit = $salida[$i];
        //         } elseif ($cont === 3) {
        //             $dv = $salida[$i];
        //         } elseif ($cont === 4) {
        //             $razonSocial = $salida[$i];
        //         } elseif ($cont === 5) {
        //             $nombreConcepto = $salida[$i];
        //         } elseif ($cont === 6) {
        //             $base = $salida[$i];
        //         } elseif ($cont === 7) {
        //             $valorRetenido = $salida[$i];
        //         } elseif ($cont === 8) {
        //             $porcentaje = $salida[$i];
        //             array_push(
        //                 $toSave,
        //                 array(
        //                     "tipoRetencion" => $tipoRet,
        //                     "nit" => $nit,
        //                     "razonSocial" => $razonSocial,
        //                     "nombreConcepto" => $nombreConcepto,
        //                     "base" => $base,
        //                     "valorRetenido" => $valorRetenido,
        //                     "porcentaje" => $porcentaje
        //                 )
        //             );
        //             $cont = -1;
        //         }
        //     }

        // }elseif($dataType === 2){

        //     for ($i = 9; $i < sizeof($salida); $i++) {
        //         if ($cont === 1) {
        //             $tipoRet = $salida[$i];
        //         } elseif ($cont === 2) {
        //             $nit = $salida[$i];
        //         } elseif ($cont === 3) {
        //             $dv = $salida[$i];
        //         } elseif ($cont === 4) {
        //             $razonSocial = $salida[$i];
        //         } elseif ($cont === 5) {
        //             $nombreConcepto = $salida[$i];
        //         } elseif ($cont === 6) {
        //             $base = $salida[$i];
        //         } elseif ($cont === 7) {
        //             $valorRetenido = $salida[$i];
        //         } elseif ($cont === 8) {
        //             $porcentaje = $salida[$i];
        //         }elseif($cont === 9){
        //             array_push(
        //                 $toSave,
        //                 array(
        //                     "tipoRetencion" => $tipoRet,
        //                     "nit" => $nit,
        //                     "razonSocial" => $razonSocial,
        //                     "nombreConcepto" => $nombreConcepto,
        //                     "base" => $base,
        //                     "valorRetenido" => $valorRetenido,
        //                     "porcentaje" => $porcentaje
        //                 )
        //             );
        //             $cont = -1;
        //         }
                    
                    
        //     }

        // }

        

        for ($i = 8; $i < sizeof($salida); $i++) {

            if ($dataType === 1) {

                if ($cont === 0) {
                    $tipoRet = $salida[$i];
                } elseif ($cont === 1) {
                    $nit = $salida[$i];
                } elseif ($cont === 2) {
                    $dv = $salida[$i];
                } elseif ($cont === 3) {
                    $razonSocial = $salida[$i];
                } elseif ($cont === 4) {
                    $nombreConcepto = $salida[$i];
                } elseif ($cont === 5) {
                    $base = $salida[$i];
                } elseif ($cont === 6) {
                    $valorRetenido = $salida[$i];
                } elseif ($cont === 7) {
                    $porcentaje = $salida[$i];
                }elseif ($cont === 8) {
                    array_push(
                        $toSave,
                        array(
                            "tipoRetencion" => $tipoRet,
                            "nit" => $nit,
                            "razonSocial" => $razonSocial,
                            "nombreConcepto" => $nombreConcepto,
                            "base" => $base,
                            "valorRetenido" => $valorRetenido,
                            "porcentaje" => $porcentaje
                        )
                    );
                    $cont = 0;
                }

            } elseif ($dataType === 2) {

                if ($cont === 0) {
                    $tipoRet = $salida[$i];
                } elseif ($cont === 1) {
                    $nit = $salida[$i];
                } elseif ($cont === 2) {
                    $dv = $salida[$i];
                } elseif ($cont === 3) {
                    $razonSocial = $salida[$i];
                } elseif ($cont === 4) {
                    $nombreConcepto = $salida[$i];
                } elseif ($cont === 5) {
                    $base = $salida[$i];
                } elseif ($cont === 6) {
                    $valorRetenido = $salida[$i];
                }elseif($cont === 7){
                    $porcentaje = $salida[$i];
                } elseif ($cont === 8) {    
                array_push(
                        $toSave,
                        array(
                            "tipoRetencion" => $tipoRet,
                            "nit" => $nit,
                            "razonSocial" => $razonSocial,
                            "nombreConcepto" => $nombreConcepto,
                            "base" => $base,
                            "valorRetenido" => $valorRetenido,
                            "porcentaje" => $porcentaje
                        )
                    );
                    $cont = 0;
                }
            }

            $cont += 1;
        }

        
        $workArray = array();
        $iterator = 0;
        $rowsSave = array();

        if ($whitOutSave === true) {

            $logger->info("CANTIDAD: " . sizeof($toSave));

            foreach ($toSave as $save) {
                $workArray = $save;
                $logger->info("workArray: " . json_encode($workArray, JSON_INVALID_UTF8_IGNORE) . " " . sizeof($workArray) . " iterator: " . $iterator);
                // if ($iterator > 0) {
                /**
                 * datayType {1=ICA,2=IVA,3=Otros}
                 */
                $logger->info("save: " . json_encode($save, JSON_INVALID_UTF8_IGNORE) . " " . sizeof($save) . " iterator: " . $iterator);
                $objForSave = saveArray($workArray, $peridoSince, $periodUntil, $conn, $dataType, $year_tribute);
                array_push($rowsSave, $objForSave);
                // }
                $iterator += $iterator + 1;
            }

            echo json_encode(
                array("creation"=>$outArry,
                    "code" => 200,
                    "rows_proocessed" => $rowsSave,
                    "num_rows_proocessed" => count($rowsSave)
                ),
                JSON_INVALID_UTF8_IGNORE
            );
        }
    } else {
        $periodFromFile = getFromFilePeriod($rutaArchivo, 4);
        echo json_encode($periodFromFile, JSON_INVALID_UTF8_IGNORE);
        echo json_encode(array("message" => "El archivo ingresado no existe.", "code" => 401), JSON_INVALID_UTF8_IGNORE);
    }



}

function saveArray($arr, $ini, $end, $conn, $dataType, $year_tribute)
{
    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

    $newRow = array();

    $logger->info("arr: " . json_encode($arr, JSON_INVALID_UTF8_IGNORE) . " " . sizeof($arr));

    $query = "INSERT INTO certificate_data
                SET tipo_retencion = :tipo_retencion,
                    nit = :nit,
                    razon_social = :razon_social,
                    nombre_concepto = :nombre_concepto,
                    base = :base,
                    valor_retenido = :valor_retenido,
                    porcentaje = :porcentaje,
                    year_tribute =:year_tribute,
                    range_ini=:range_ini,
                    range_end=:range_end,
                    dataType=:dataType                  
                    ";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':tipo_retencion', $arr["tipoRetencion"]);
    $stmt->bindParam(':nit', $arr["nit"]);
    $stmt->bindParam(':razon_social', $arr["razonSocial"]);
    $stmt->bindParam(':nombre_concepto', $arr["nombreConcepto"]);
    $stmt->bindParam(':base', $arr["base"]);
    $stmt->bindParam(':valor_retenido', $arr["valorRetenido"]);
    $stmt->bindParam(':porcentaje', $arr["porcentaje"]);
    $stmt->bindParam(':dataType', $dataType);
    $stmt->bindParam(':year_tribute', $year_tribute);
    $stmt->bindParam(':range_ini', $ini);
    $stmt->bindParam(':range_end', $end);

    //PDO::PARAM_INT
    $execute = $stmt->execute();
    $last = $conn->lastInsertId();

    if ($execute > 0) {
        //http_response_code(200);
        array_push(
            $newRow,
            array(
                "code" => 200,
                "id_certificate_data" => $last,
                "nit" => $arr["nit"],
                "razonSocial" => $arr["razonSocial"],
                "nombreConcepto" => $arr["nombreConcepto"]
            )
        );
        //echo json_encode(array("message" => "registro was successfully create."), JSON_INVALID_UTF8_IGNORE);
    } else {
        //http_response_code(400);
        echo json_encode(array("message" => "Unable to register the certificate."), JSON_INVALID_UTF8_IGNORE);
    }

    if (count($newRow) > 0) {
        return $newRow;
    } else {
        return array("code" => "401", "message" => "Registro no pudo ser insertado. [ Nit: " . $arr["nit"] . " Concepto: " . $arr["nombreConcepto"] . "]");
    }
}
/**
 * @param $conn Connection
 */
function getCertificatesByOrg($conn, $idCertificate): array
{
    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

    $query = 'select
                *
            from
                certificados_generados cg
            inner join certificates_x_values cxv on cxv.id_certificados_generado = cg.id_certificados_generado
            inner join values_certificates vc on vc.id_values = cxv.id_values
            where cg.id_certificados_generado =?;
             ';

    $logger->info("Ejecuted Select Query");

    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $idCertificate);
    $stmt->execute();
    $num = $stmt->rowCount();
    $certificates = [];

    $logger->info("Result count certificados_generados: " . $num);

    if ($num > 0) {
        $row = $stmt->fetchAll();
        $object = new stdClass();
        for ($i = 0; $i < $num; $i++) {
            array_push(
                $certificates,
                array(
                    "id_certificados_generado" => $row[$i]["id_certificados_generado"],
                    "tipo_certificado" => $row[$i]["tipo_certificado"],
                    "nombre" => $row[$i]["nombre"],
                    "organization_asociate" => $row[$i]["organization_asociate"],
                    "ret_razon_social" => $row[$i]["ret_razon_social"],
                    "ret_nit" => $row[$i]["ret_nit"],
                    "ret_concepto_retencion" => $row[$i]["ret_concepto_retencion"],
                    "anio_gravable" => $row[$i]["anio_gravable"],
                    "id_certificate_value" => $row[$i]["id_certificate_value"],
                    "id_values" => $row[$i]["id_values"],
                    "concepto" => $row[$i]["concepto"],
                    "base_retencion" => (float) $row[$i]["base_retencion"],
                    "valor_retenido" => (float) $row[$i]["valor_retenido"]
                )
            );
        }
        return $certificates;
    }

}

function generateDocPdf($conn, $idCertificate)
{
    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

    ini_set("memory_limit", "-1");
    //header("Content-Type:  application/pdf; charset=utf-8");

    $certificatesData = array();
    $certificatesData = getCertificatesByOrg($conn, $idCertificate);

    $servicios = "";
    $compras = "";
    $valor_ret_servicios = "";
    $valor_ret_compra = "";
    $regxServiciosICA = "Servicios";
    $regxCompraICA = "Comercial";


    $regxServiciosIVA = "servicios";
    $regxMercanciaIVA = "mercancías";

    $razonSocialRet = $certificatesData[0]["ret_razon_social"];
    $nitRet = $certificatesData[0]["ret_nit"];
    $porcentRetServicios = 0;

    for ($i = 0; $i < count($certificatesData); $i++) {
        if (strpos($certificatesData[$i]["concepto"], $regxServiciosICA) !== false) {
            $servicios .= '<td>$ ' . $certificatesData[$i]["base_retencion"] . '</td>';
            $valor_ret_servicios .= '<td>$ ' . $certificatesData[$i]["valor_retenido"] . '</td>';
        }

        if (strpos($certificatesData[$i]["concepto"], $regxCompraICA) !== false) {
            $compras .= '<td>$ ' . $certificatesData[$i]["base_retencion"] . '</td>';
            $valor_ret_compra .= '<td>$ ' . $certificatesData[$i]["valor_retenido"] . '</td>';
        }

        if (strpos($certificatesData[$i]["concepto"], $regxServiciosIVA) !== false) {
            $servicios .= '<td>$ ' . $certificatesData[$i]["base_retencion"] . '</td>';
            $valor_ret_servicios .= '<td>$ ' . $certificatesData[$i]["valor_retenido"] . '</td>';
        }

        if (strpos($certificatesData[$i]["concepto"], $regxMercanciaIVA) !== false) {
            $compras .= '<td>$ ' . $certificatesData[$i]["base_retencion"] . '</td>';
            $valor_ret_compra .= '<td>$ ' . $certificatesData[$i]["valor_retenido"] . '</td>';
        }
    }

    //$strHtml = include ('../files-handler-core/templates/file.php');
    $logger = new Logger('logger');
    $logger->pushHandler(new StreamHandler('../api/tmp/logs/pdf_logger.log', Logger::DEBUG));

    $mpdf = new \Mpdf\Mpdf([
        'allow_charset_conversion' => true,
        'mode' => 'utf-8',
        'tempDir' => '../api/tmp/',
        'orientation' => 'P'
    ]);
    $mpdf->setLogger($logger);
    //$mpdf->allow_charset_conversion=true;
    //$mpdf->charset_in='utf8';
    //$mpdf->autoScriptToLang = true;
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->useSubstitutions = true;
    $mpdf->text_input_as_HTML = true;


    //$mpdf->simpleTables = true;  
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
        \Mpdf\HTMLParserMode::HEADER_CSS,
        true,
        false
    );
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
                        <p> &nbsp; Apellidos y Nombres o Razón Social: .........: ' . $razonSocialRet . '</p>
                        <p> &nbsp; NIT...............................................................: ' . $nitRet . '</p>
                        <p> &nbsp; Concepto de la retenciónn .......................................: COMPRAS Y/O SERVICIOS</p>
                    </div>
                    <table class="table-middel" border="1">
                        <tr>
                            <th>CONCEPTO</th>
                            <th>BASE DE RETENCION</th>
                            <th>VALOR RETENIDO</th>
                        </tr>

                        <tr>
                            <td>SERVICIOS 4%</td>
                            <label>'
        . $servicios
        . '</label>'
        . $valor_ret_servicios .
        '
                            
                        </tr>
                        <tr>
                            <td>COMPRAS 2,5%</td>$ '
        . $compras
        . $valor_ret_compra
        . '
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
        . date("Y-m-d") . ' 
                    </strong>
                    </p>
                </div>',
        \Mpdf\HTMLParserMode::HTML_BODY,
        true,
        true
    );
    //$mpdf->Output();
    //http_response_code(200);
    //$mpdf->OutputHttpDownload('download.pdf');

    $outPutNameFile = "A" . time() . '.pdf';
    $outPutDirFile = 'tmp/mpdf/outfiles/' . $outPutNameFile;
    updateCertificate($conn, $idCertificate, $outPutNameFile);

    //$mpdf->Output();
    $mpdf->Output($outPutDirFile, \Mpdf\Output\Destination::FILE);
}

function updateCertificate($conn, $idCertificate, $outPutNameFile)
{
    $query = 'update certificados_generados 
                set url_assoc_file =:url_assoc_file 
                where id_certificados_generado =:id_certificados_generado;
            ';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':url_assoc_file', $outPutNameFile);
    $stmt->bindParam(':id_certificados_generado', $idCertificate);
    $stmt->execute();
    $num = $stmt->rowCount();
}

function generarBase($conn, $nit, $tipo_retencion, $year, $idOrganizacion,$sinceRange,$untilRange)
{
    echo $sinceRange,$untilRange;
    $insertRow = 0;
    $certificatesData = [];
    $nombreCert = "";
    $dateNow = date("Y-m-d-h:i:s");
    $concepto = "COMPRAS Y/O SERVICIOS";

    $querySelect = 'select * from certificate_data 
                    where dataType=:dataType
                    and nit=:nit
                    and year_tribute=:year_tribute and range_ini >=:range_ini and range_end <= :range_end';

    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));
    $logger->info("Select Query" . $querySelect);
    // try {
    $stmt = $conn->prepare($querySelect);
    $stmt->bindParam(':dataType', $tipo_retencion);
    $stmt->bindParam(':nit', $nit);
    $stmt->bindParam(':year_tribute', $year);
    $stmt->bindParam(':range_ini', $sinceRange);
    $stmt->bindParam(':range_end', $untilRange);
    
    $stmt->execute();
    $num = $stmt->rowCount();
    // } catch (PDOException $e) {
    // echo json_encode(array("message" => "Error!" . $e->getMessage()),JSON_INVALID_UTF8_IGNORE);
    // }    
    $logger->info("Result count certificate_data: " . $num);

    if ($num > 0) {
        $row = $stmt->fetchAll();

        for ($i = 0; $i < $num; $i++) {
            array_push(
                $certificatesData,
                array(
                    "id_certificate_data_ica" => $row[$i]["id_certificate_data_ica"],
                    "tipo_retencion" => $row[$i]["tipo_retencion"],
                    "nit" => $row[$i]["nit"],
                    "razon_social" => $row[$i]["razon_social"],
                    "nombre_concepto" => $row[$i]["nombre_concepto"],
                    "base" => $row[$i]["base"],
                    "valor_retenido" => $row[$i]["valor_retenido"],
                    "porcentaje" => $row[$i]["porcentaje"],
                    "year_tribute" => $row[$i]["year_tribute"],
                    "range_ini" => $row[$i]["range_ini"],
                    "range_end" => $row[$i]["range_end"],
                    "dataType" => (float) $row[$i]["dataType"]
                )
            );
        }
        $logger->info("OBJ certificatesData: " . json_encode($certificatesData, JSON_INVALID_UTF8_IGNORE));
        $lastCertificateGen = 0;
        $lastCertificateValue = 0;

        $logger->info("Result count certificatesData: " . count($certificatesData));

        $insertQuery = 'INSERT INTO certificados_generados (tipo_certificado,
                                           nombre,
                                           organization_asociate,
                                           ret_razon_social,
                                           ret_nit,
                                           ret_concepto_retencion,
                                           anio_gravable,
                                           createat,
                                           url_assoc_file) 
                        VALUES(:tipo_certificado,:nombre,:organization_asociate,:ret_razon_social,:ret_nit,:ret_concepto_retencion,:anio_gravable,:createat,"")';

        $logger->info("Insert Query: " . $insertQuery);

        $nombreCert = ($tipo_retencion == 1) ? $nit . "-ICA" : $nit . "-IVA";

        $stmt = $conn->prepare($insertQuery);
        $stmt->bindParam(':tipo_certificado', $tipo_retencion);
        $stmt->bindParam(':nombre', $nombreCert);
        $stmt->bindParam(':organization_asociate', $idOrganizacion);
        $stmt->bindParam(':ret_razon_social', $certificatesData[0]["razon_social"]);
        $stmt->bindParam(':ret_nit', $certificatesData[0]["nit"]);
        $stmt->bindParam(':ret_concepto_retencion', $concepto);
        $stmt->bindParam(':anio_gravable', $year);
        $stmt->bindParam(':createat', $dateNow);

        $logger->info("EJECUTO! O NO?: " . $stmt->execute());
        $ejecute = $stmt->execute();

        $logger->info("Insert Query: Inserta Registro!");

        $lastCertificateGen = $conn->lastInsertId();
        $num = $stmt->rowCount();

        $logger->info("Inserted? : " . $ejecute . " lastCertificateGen: " . $lastCertificateGen);

        if ($ejecute == 1) {

            for ($i = 0; $i < count($certificatesData); $i++) {

                $insertValuesQuery = 'INSERT INTO values_certificates (concepto,base_retencion,valor_retenido) VALUES (?,?,?) ';

                $logger->info("Insert Query: " . $insertValuesQuery);

                $stmt = $conn->prepare($insertValuesQuery);
                $stmt->bindParam(1, $certificatesData[$i]["nombre_concepto"]);
                $stmt->bindParam(2, $certificatesData[$i]["base"]);
                $stmt->bindParam(3, $certificatesData[$i]["valor_retenido"]);

                $execute = $stmt->execute();
                $logger->info("Insert Query: Inserta Registro!");
                $lastCertificateValue = $conn->lastInsertId();
                $num = $stmt->rowCount();

                $logger->info("Result count values_certificates: " . $num);
                $logger->info("Result count certificatesData: " . json_encode($certificatesData[$i], JSON_INVALID_UTF8_IGNORE));

                if ($execute == 1) {
                    $insertRelations = 'INSERT INTO certificates_x_values (id_values,id_certificados_generado) VALUES (?,?); ';

                    $logger->info("Insert Query: " . $insertRelations . " Data: lastCertificateGen" . $lastCertificateGen . " " . $lastCertificateValue);

                    $logger->info("Result count lastCertificateGen - lastCertificateValue: " . $lastCertificateGen . " " . $lastCertificateValue);

                    $stmt = $conn->prepare($insertRelations);

                    $stmt->bindParam(1, $lastCertificateValue);
                    $stmt->bindParam(2, $lastCertificateGen);


                    $insertRow = $stmt->execute();
                    //$num = $stmt->rowCount();
                    //http_response_code(200);

                    // if($stmt->execute()){
                    //     echo json_encode(
                    //         array(
                    //             "message" => "Cetificado creado con exito",
                    //             "code" => "200",
                    //             "length" => $num." ".$lastCertificateGen
                    //         ),JSON_INVALID_UTF8_IGNORE
                    //     );
                    // }

                }
                //    generateDocPdf($conn,$lastCertificateGen);

            }

            if ($insertRow > 0) {
                echo json_encode(
                    array(
                        "message" => "Cetificado creado con exito",
                        "code" => "200",
                        "length" => $num . " " . $lastCertificateGen
                    ),
                    JSON_INVALID_UTF8_IGNORE
                );
            }

            generateDocPdf($conn, $lastCertificateGen);
            // }


        } else {
            echo json_encode(array("message" => "No se encontraron registros", "code" => "401", "length" => 0, "data" => "" . $tipo_retencion . " " . $nit . " " . $year . " \n " . $querySelect, JSON_INVALID_UTF8_IGNORE));
        }


    } else {
        echo json_encode(array("message" => "No se encontraron registros", "code" => "401", "length" => 0, "data" => "" . $tipo_retencion . " " . $nit . " " . $year . " \n " . $querySelect, JSON_INVALID_UTF8_IGNORE));
    }
    // return "";
}

?>