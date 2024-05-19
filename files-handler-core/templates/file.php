<?php 
$templateHTML ='<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Retención en la Fuente</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            border-style: solid;            
        }

        .header {
            text-align: center;
            border-bottom: 2px solid;
        }  

        .content {
            margin: 20px;
            align-items: center;
            justify-content: center;
            border-width: 100%;        
        }

        .section {
            margin-bottom: 20px;            
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
            border-bottom: 2px solid;
        }

        .table th,
        .table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            border-style: solid;
            border-bottom: 2px solid;
        }

        .footer {
            text-align: left;
            border-style: solid;                    
        }    
        .logo{
            width: 30%;
            height: 40%;
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
        body .disclamer p{
            font-size: small;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="logoapex.png" alt="logo" class="logo"/>        
    </div>
    <div class="content">
        <div class="section">
            <div class="section-title">
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
                    &nbsp; IDENTIFICACIÓN DE LA PERSONA O ENTIDAD A QUIEN SE PRACTICÓ LA RETENCION
                </h2>
            </div>
            <p> &nbsp; Apellidos y Nombres o Razón Social: .........: Tl SOLUCIONES DE OCCIDENTE S.A.S.</p>
            <p> &nbsp; NIT.........................................................: Tl SOLUCIONES DE OCCIDENTE S.A.S.</p>
            <p> &nbsp; Concepto de la retenciónn .......................................: COMPRAS Y/O SERVICIOS</p>
        </div>
        <table class="table-middel" >
            <tr>
                <th>CONCEPTO</th>
                <th>BASE DE RETENCION</th>
                <th>VALOR RETENIDO</th>
            </tr>
            <tr>
                <td>SERVICIOS 4%</td>
                <td>$28.012.190</td>
                <td>$1.120.488</td>
            </tr>
            <tr>
                <td>COMPRAS 2,5%</td>
                <td>$6.545.800</td>
                <td>$163.645</td>
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
            <p>
                &nbsp; Este documento no requiere para su validez firma autógrafa de acuerdo con el articulo 10 del Decreto 836 de 1991, recopilado en el articulo 1.6.1.12.12 del DUT 1625 de Octubre 11 de 2016, que regula el contenido del certificado de retenciones a título de Renta. 
            </p>
        </div>
        <p>FECHA DE EXPEDICIÓN: <strong> 26/01/2023 </strong></p>
    </div>
</body>
</html>';

?>