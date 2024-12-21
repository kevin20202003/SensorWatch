<?php

require('./fpdf.php');

// Incluir el archivo de conexión
require '../../modelo/conexion.php'; 

class PDF extends FPDF
{
    private $isFirstPage = true; // Agrega una bandera para controlar si es la primera página

    // Cabecera de página
    function Header()
    {
        if ($this->isFirstPage) {
            $this->isFirstPage = false;

            $this->Image('../../img/logo_proyecto.png', 230, 5, 70);
            $this->SetFont('Arial', 'B', 24); 
            $this->SetTextColor(0, 0, 0);
            $this->Cell(95);
            $this->Cell(110, 20, utf8_decode('SENSORWATCH'), 1, 1, 'C', 0); 
            $this->Ln(5);

            $this->SetTextColor(228, 100, 0);
            $this->Cell(100);
            $this->SetFont('Arial', 'B', 18); 
            $this->Cell(100, 20, utf8_decode("REPORTE DEL SENSOR AMBIENTE"), 0, 1, 'C', 0); 
            $this->Ln(10); 

            $this->SetFillColor(228, 100, 0);
            $this->SetTextColor(255, 255, 255);
            $this->SetDrawColor(163, 163, 163);
            $this->SetFont('Arial', 'B', 11);
            $this->SetX(40);  
            $this->Cell(55, 10, utf8_decode('HUMEDAD'), 1, 0, 'C', 1);
            $this->Cell(55, 10, utf8_decode('TEMPERATURA'), 1, 0, 'C', 1);
            $this->Cell(55, 10, utf8_decode('LUZ'), 1, 0, 'C', 1);
            $this->Cell(55, 10, utf8_decode('FECHA'), 1, 1, 'C', 1);
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

// Consulta a la tabla datos_ambiente para obtener registros agrupados por hora
$sql = "SELECT 
      TO_CHAR(created_at, 'YYYY-MM-DD HH24:00:00') AS hora,
      AVG(humedad_amb) AS humedad_amb,
      AVG(temperatura_amb) AS temperatura_amb,
      AVG(lux) AS lux
    FROM 
        datos_ambiente
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

$pdf->Header();

foreach ($result as $row) {
    $pdf->SetTextColor(0, 0, 0); 

    // Redondear las variables a 2 decimales
    $humedad = number_format($row['humedad_amb'], 2);
    $temperatura = number_format($row['temperatura_amb'], 2);
    $lux = number_format($row['lux'], 2);

    // Formatear la fecha (hora) antes de mostrarla
    $fecha = date('Y-m-d H:i:s', strtotime($row['hora']));

    $pdf->SetX(40); 
    $pdf->Cell(55, 10, utf8_decode($humedad), 1, 0, 'C', 0);
    $pdf->Cell(55, 10, utf8_decode($temperatura), 1, 0, 'C', 0);
    $pdf->Cell(55, 10, utf8_decode($lux), 1, 0, 'C', 0);
    $pdf->Cell(55, 10, utf8_decode($fecha), 1, 1, 'C', 0);
}

// Pie de página
$pdf->Footer();
$pdf->Output('Reporte_Ambiente.pdf', 'I');

// Cerrar la conexión
$pdo = null;

?>
