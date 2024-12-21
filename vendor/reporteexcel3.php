<?php

// Incluye los archivos necesarios para PhpSpreadsheet
require 'autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Incluir el archivo de conexión
require '../modelo/conexion.php'; 

// Consulta a la tabla datos_meteorologicos
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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Reporte de Sensor Meteorologico');

// Configuración del título del reporte
$sheet->mergeCells('A1:F1');
$sheet->setCellValue('A1', 'Reporte del Sensor Meteorologico');
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
$headers = ['Temperatura', 'Presión', 'Humedad', 'Velocidad De Viento', 'Fecha'];
$columnLetters = range('A', 'E');
foreach ($headers as $index => $header) {
    $sheet->setCellValue($columnLetters[$index] . '6', $header);
}

// Aplicar estilo a los encabezados
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0000FF']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
];
$sheet->getStyle('A6:E6')->applyFromArray($headerStyle);

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
$row_num = 7; 
foreach ($result as $row) {
    $sheet->setCellValue('A' . $row_num, $row['temp']); 
    $sheet->setCellValue('B' . $row_num, $row['pressure']); 
    $sheet->setCellValue('C' . $row_num, $row['humidity']); 
    $sheet->setCellValue('D' . $row_num, $row['wind_speed']); 
    $sheet->setCellValue('E' . $row_num, $row['hora']); 
    $row_num++;
}

// Aplicar bordes a todas las celdas con datos
$sheet->getStyle('A6:E' . ($row_num - 1))->applyFromArray($borderStyle);

// Configurar ancho de columnas
foreach ($columnLetters as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Agregar una imagen en la parte derecha
$drawing = new Drawing();
$drawing->setPath('../img/logo_proyecto.png'); 
$drawing->setCoordinates('F1'); 
$drawing->setWidth(150); 
$drawing->setHeight(150); 
$drawing->setWorksheet($sheet);

// Establecer nombre del archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte_Meteorologico.xlsx"');
header('Cache-Control: max-age=0');

// Guardar el archivo
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Cerrar la conexión
$pdo = null;

?>
