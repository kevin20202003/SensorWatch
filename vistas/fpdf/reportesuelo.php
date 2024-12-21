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
         $this->Cell(80, 20, utf8_decode('SENSORWATCH'), 1, 1, 'C', 0);
         $this->Ln(5);

         $this->SetTextColor(228, 100, 0);
         $this->SetFont('Arial', 'B', 18);
         $this->Cell(100);
         $this->Cell(80, 20, utf8_decode("REPORTE DEL SENSOR SUELO"), 0, 1, 'C', 0);
         $this->Ln(10);

         $this->SetFillColor(228, 100, 0);
         $this->SetTextColor(255, 255, 255);
         $this->SetDrawColor(163, 163, 163);
         $this->SetFont('Arial', 'B', 11);

         $this->SetX(80); 
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
      $hoy = date('d/m/Y');
      $this->Cell(540, 10, utf8_decode($hoy), 0, 0, 'C');
   }
}

try {
   // Consulta a la base de datos
   $sql = "SELECT 
               TO_CHAR(created_at, 'YYYY-MM-DD HH24:00:00') AS hora,
               AVG(humedad) AS humedad,
               AVG(temperatura) AS temperatura,
               AVG(ph) AS ph
           FROM 
               datos_suelo
           GROUP BY 
               hora
           ORDER BY 
               hora";

   $stmt = $pdo->prepare($sql);
   $stmt->execute();
   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

   if (empty($result)) {
      throw new Exception("No se encontraron datos para generar el reporte.");
   }

   // Crear el objeto PDF
   $pdf = new PDF();
   $pdf->AddPage("landscape");
   $pdf->AliasNbPages();
   $pdf->SetFont('Arial', '', 12);
   $pdf->SetDrawColor(163, 163, 163);

   // Mostrar los registros en el PDF
   foreach ($result as $row) {
      $pdf->SetTextColor(0, 0, 0);

      $humedad = number_format($row['humedad'], 2);
      $temperatura = number_format($row['temperatura'], 2);
      $ph = number_format($row['ph'], 2);

      $pdf->SetX(80); 
      $pdf->Cell(30, 10, utf8_decode($humedad), 1, 0, 'C', 0);
      $pdf->Cell(35, 10, utf8_decode($temperatura), 1, 0, 'C', 0);
      $pdf->Cell(30, 10, utf8_decode($ph), 1, 0, 'C', 0);
      $pdf->Cell(43, 10, utf8_decode($row['hora']), 1, 1, 'C', 0);
   }

   // Generar el PDF
   $pdf->Output('Reporte_Suelo.pdf', 'I');
} catch (Exception $e) {
   echo "Error: " . $e->getMessage();
} finally {
   // Cerrar conexión a la base de datos
   $pdo = null;
}
