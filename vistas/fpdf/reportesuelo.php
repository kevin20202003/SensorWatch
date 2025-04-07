<?php

require('./fpdf.php');

require '../../modelo/conexion.php';

class PDF extends FPDF
{
   private $isFirstPage = true;

   // Recibe la información del usuario
   function __construct()
   {
      parent::__construct();
   }

   // Cabecera de página
   function Header()
   {
      if ($this->isFirstPage) {
         $this->isFirstPage = false;

         $this->Image('../../img/logo_proyecto.png', 230, 5, 70);
         $this->SetFont('Arial', 'B', 24);
         $this->SetTextColor(0, 0, 0);
         $this->Cell(95);
         $this->Cell(80, 20, utf8_decode('SENSORWATCH'), 1, 1, 'C', 0);
         $this->Ln(5);

         $this->SetTextColor(103);
         $this->Cell(180);
         $this->SetFont('Arial', 'B', 14);
         $this->Ln(7);

         $this->SetTextColor(228, 100, 0);
         $this->Cell(100);
         $this->SetFont('Arial', 'B', 18);
         $this->Cell(80, 20, utf8_decode("REPORTE DEL SENSOR SUELO"), 0, 1, 'C', 0);
         $this->Ln(10);

         $this->SetFillColor(228, 100, 0);
         $this->SetTextColor(255, 255, 255);
         $this->SetDrawColor(163, 163, 163);
         $this->SetFont('Arial', 'B', 11);

         // Asegurarse de que la tabla esté centrada en la página
         $this->SetX(80);  // Ajuste horizontal para centrar la tabla
         $this->Cell(30, 10, utf8_decode('HUMEDAD'), 1, 0, 'C', 1);
         $this->Cell(35, 10, utf8_decode('TEMPERATURA'), 1, 0, 'C', 1);
         $this->Cell(30, 10, utf8_decode('PH'), 1, 0, 'C', 1);
         $this->Cell(43, 10, utf8_decode('FECHA'), 1, 1, 'C', 1);
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

// Consulta a la tabla datos_suelo para obtener registros agrupados por hora
$sql = "SELECT 
      DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') AS hora,
      AVG(humedad) AS humedad,
      AVG(temperatura) AS temperatura,
      AVG(PH) AS PH
    FROM 
        datos_suelo
    GROUP BY 
        hora
    ORDER BY 
        hora;";

// Prepara y ejecuta la consulta
$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
   die("Error en la consulta de datos_suelo: " . $conexion->error);
}

// Crea el objeto PDF
$pdf = new PDF();
$pdf->AddPage("landscape");
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 12);
$pdf->SetDrawColor(163, 163, 163);

// Incluir la información del usuario en el encabezado
$pdf->Header();

// Mostrar los registros agrupados por hora
while ($row = $result->fetch_assoc()) {
   $pdf->SetTextColor(0, 0, 0); // Establece el color de texto a negro (RGB: 0, 0, 0)

   // Redondear las variables a 2 decimales
   $humedad = number_format($row['humedad'], 2) . " %";
   $temperatura = number_format($row['temperatura'], 2) . " °C";
   $ph = number_format($row['PH'], 2);

   $pdf->SetX(80);  // Ajuste horizontal para centrar la tabla
   $pdf->Cell(30, 10, utf8_decode($humedad), 1, 0, 'C', 0);
   $pdf->Cell(35, 10, utf8_decode($temperatura), 1, 0, 'C', 0);
   $pdf->Cell(30, 10, utf8_decode($ph), 1, 0, 'C', 0);
   $pdf->Cell(43, 10, utf8_decode($row['hora']), 1, 1, 'C', 0);
}

// Agregar pie de página
$pdf->Footer();
$pdf->Output('Reporte_Suelo.pdf', 'I');

// Cerrar la conexión a la base de datos
$conexion->close();
?>
