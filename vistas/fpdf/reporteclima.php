<?php

require('./fpdf.php');

// Incluir el archivo de conexión
require '../../modelo/conexion.php'; 

class PDF extends FPDF
{
    private $isFirstPage = true;

    // Cabecera de página
    function Header()
    {
        if ($this->isFirstPage) {
            $this->isFirstPage = false;

            $this->Image('../../img/logo_proyecto.png', 230, 5, 70);
            $this->SetFont('Arial', 'B', 24);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(95);
            $this->Cell(90, 20, utf8_decode('SENSORWATCH'), 1, 1, 'C', 0);
            $this->Ln(5);

            $this->SetTextColor(228, 100, 0);
            $this->Cell(100);
            $this->SetFont('Arial', 'B', 18);
            $this->Cell(80, 20, utf8_decode("REPORTE DEL SENSOR METEOROLOGICO"), 0, 1, 'C', 0);
            $this->Ln(10);

            $this->SetFillColor(228, 100, 0);
            $this->SetTextColor(255, 255, 255);
            $this->SetDrawColor(163, 163, 163);
            $this->SetFont('Arial', 'B', 11);
            $this->SetX(30);  
            $this->Cell(45, 10, utf8_decode('TEMPERATURA'), 1, 0, 'C', 1);
            $this->Cell(45, 10, utf8_decode('PRESION'), 1, 0, 'C', 1);
            $this->Cell(45, 10, utf8_decode('HUMEDAD'), 1, 0, 'C', 1);
            $this->Cell(45, 10, utf8_decode('VELOCIDAD DE VIENTO'), 1, 0, 'C', 1);
            $this->Cell(50, 10, utf8_decode('FECHA'), 1, 1, 'C', 1);
        }
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $hoy = date('d/m/Y');
        $this->Cell(540, 10, utf8_decode($hoy), 0, 0, 'C');
    }
}

// Consulta a la tabla datos_meteorologicos para obtener registros agrupados por hora
$sql = "SELECT 
      TO_CHAR(date, 'YYYY-MM-DD HH24:00:00') AS hora,
      AVG(temp) AS temp,
      AVG(pressure) AS pressure,
      AVG(humidity) AS humidity,
      AVG(wind_speed) AS wind_speed
    FROM 
        datos_meteorologicos
    GROUP BY 
        hora
    ORDER BY 
        hora;";

try {
    // Ejecutar la consulta
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$result) {
        die("No se encontraron resultados.");
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}

// Crear el PDF
$pdf = new PDF();
$pdf->AddPage("landscape");
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 12);
$pdf->SetDrawColor(163, 163, 163);

// Generar el reporte
foreach ($result as $row) {
    // Redondear las variables a 2 decimales
    $humedad = number_format($row['humidity'], 2);
    $temperatura = number_format($row['temp'], 2);
    $presion = number_format($row['pressure'], 2);
    $viento = number_format($row['wind_speed'], 2);
    $pdf->SetX(30);  
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(45, 10, utf8_decode($temperatura), 1, 0, 'C', 0);
    $pdf->Cell(45, 10, utf8_decode($presion), 1, 0, 'C', 0);
    $pdf->Cell(45, 10, utf8_decode($humedad), 1, 0, 'C', 0);
    $pdf->Cell(45, 10, utf8_decode($viento), 1, 0, 'C', 0);
    $pdf->Cell(50, 10, utf8_decode($row['hora']), 1, 1, 'C', 0);
}

// Salida del archivo PDF
$pdf->Output('Reporte_Clima.pdf', 'I');

// Cerrar la conexión
$pdo = null;

?>
