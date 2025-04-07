<?php

// Incluye los archivos necesarios para PhpSpreadsheet
require 'autoload.php'; // Asegúrate de tener Composer instalado y usar autoload.php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

require '../modelo/conexion.php';

// Consulta a la tabla datos_suelo
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

$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Error en la consulta de datos_suelo: " . $conexion->error);
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Reporte de Sensor Suelo');

// Configuración del título del reporte
$sheet->mergeCells('A1:E1');
$sheet->setCellValue('A1', 'Reporte del Sensor Suelo');
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 28,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
]);

// Encabezado de columnas
$headers = ['Humedad', 'Temperatura', 'PH', 'Fecha'];
$columnLetters = ['A', 'B', 'C', 'D'];
foreach ($headers as $index => $header) {
    $sheet->setCellValue($columnLetters[$index] . '6', $header);
}

// Aplicar estilo a los encabezados
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0000FF']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
];
$sheet->getStyle('A6:D6')->applyFromArray($headerStyle);

// Agregar bordes a las celdas
$borderStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
];

// Agregar datos
$row_num = 7; // Empezamos en la fila 7
while ($row = $result->fetch_assoc()) {
    // Redondear las variables a 2 decimales
    $humedad = number_format($row['humedad'], 2) . " %";
    $temperatura = number_format($row['temperatura'], 2) . " °C";
    $ph = number_format($row['PH'], 2);

    // Establecer los valores en las celdas
    $sheet->setCellValue('A' . $row_num, $humedad);
    $sheet->setCellValue('B' . $row_num, $temperatura);
    $sheet->setCellValue('C' . $row_num, $ph);
    $sheet->setCellValue('D' . $row_num, Date::PHPToExcel($row['hora']));
    $sheet->getStyle('D' . $row_num)->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
    $row_num++;
}

// Aplicar bordes a todas las celdas con datos
$sheet->getStyle('A6:D' . ($row_num - 1))->applyFromArray($borderStyle);

// Configurar ancho de columnas
foreach ($columnLetters as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Agregar una imagen en la parte derecha
$drawing = new Drawing();
$drawing->setPath('../img/logo_proyecto.png'); // Reemplaza con la ruta a tu imagen
$drawing->setCoordinates('E1'); // Coloca la imagen en la celda E1
$drawing->setWidth(150); // Ajusta el ancho de la imagen
$drawing->setHeight(150); // Ajusta la altura de la imagen
$drawing->setWorksheet($sheet);

// Establecer nombre del archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte_Suelo.xlsx"');
header('Cache-Control: max-age=0');

// Guardar el archivo
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

$conexion->close();
?>
