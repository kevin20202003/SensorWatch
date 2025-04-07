<?php

require('./fpdf.php');

require '../../modelo/conexion.php';

class PDF extends FPDF
{
   private $isFirstPage = true; // Agrega una bandera para controlar si es la primera página


   // Cabecera de página
   function Header()
   {
      if ($this->isFirstPage) {
         // Solo se ejecuta el código si es la primera página
         $this->isFirstPage = false;

         $this->Image('../../img/logo_proyecto.png', 230, 5, 70);
         $this->SetFont('Arial', 'B', 24); // Aumenta el tamaño de la fuente
         $this->SetTextColor(0, 0, 0);
         $this->Cell(95);
         $this->Cell(110, 20, utf8_decode('SENSORWATCH'), 1, 1, 'C', 0); // Aumenta el alto de la celda
         $this->Ln(5); // Ajusta el espacio entre líneas

         $this->SetTextColor(228, 100, 0);
         $this->Cell(100);
         $this->SetFont('Arial', 'B', 18); // Aumenta el tamaño de la fuente
         $this->Cell(100, 20, utf8_decode("REPORTE DEL SENSOR AMBIENTE"), 0, 1, 'C', 0); // Aumenta el alto de la celda
         $this->Ln(10); // Ajusta el espacio entre líneas

         $this->SetFillColor(228, 100, 0);
         $this->SetTextColor(255, 255, 255);
         $this->SetDrawColor(163, 163, 163);
         $this->SetFont('Arial', 'B', 11);
         $this->SetX(40);  // Ajuste horizontal para centrar la tabla
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
      DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') AS hora,
      AVG(humedad_amb) AS humedad_amb,
      AVG(temperatura_amb) AS temperatura_amb,
      AVG(lux) AS lux
    FROM 
        datos_ambiente
    GROUP BY 
        hora
    ORDER BY 
        hora;";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
   die("Error en la consulta de datos_suelo: " . $conexion->error);
}

$pdf = new PDF();
$pdf->AddPage("landscape");
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 12);
$pdf->SetDrawColor(163, 163, 163);

// Incluir la información del usuario y la ubicación en el encabezado
$pdf->Header();

while ($row = $result->fetch_assoc()) {
   $pdf->SetTextColor(0, 0, 0); // Establece el color de texto a negro (RGB: 0, 0, 0)
   
   // Redondear las variables a 2 decimales
   $humedad = number_format($row['humedad_amb'], 2) . " %"; 
   $temperatura = number_format($row['temperatura_amb'], 2) . " °C"; 
   $lux = number_format($row['lux'], 2) . " lux";

   // Formatear la fecha (hora) antes de mostrarla
   $fecha = date('Y-m-d H:i:s', strtotime($row['hora']));

   $pdf->SetX(40);  // Ajuste horizontal para centrar la tabla
   $pdf->Cell(55, 10, utf8_decode($humedad), 1, 0, 'C', 0);
   $pdf->Cell(55, 10, utf8_decode($temperatura), 1, 0, 'C', 0);
   $pdf->Cell(55, 10, utf8_decode($lux), 1, 0, 'C', 0);
   $pdf->Cell(55, 10, utf8_decode($fecha), 1, 1, 'C', 0);
}

// Pie de página
$pdf->Footer();
$pdf->Output('Reporte_Ambiente.pdf', 'I');

$conexion->close();
?>
