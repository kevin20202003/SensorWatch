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

// Consulta a la tabla datos_meteorologicos
$sql = "SELECT 
      DATE_FORMAT(date, '%Y-%m-%d %H:00:00') AS hora,
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

$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Error en la consulta de datos_meteorologicos: " . $conexion->error);
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Reporte de Sensor Meteorologico');

// Configuración del título del reporte
$sheet->mergeCells('A1:E1');
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
$row_num = 7; // Empezamos en la fila 7
while ($row = $result->fetch_assoc()) {
    // Redondear las variables a 2 decimales
    $temp = number_format($row['temp'], 2) . " °C";
    $pressure = number_format($row['pressure'], 2) . " hPa";
    $humidity = number_format($row['humidity'], 2) . " %";
    $wind_speed = number_format($row['wind_speed'], 2) . " m/s";
    
    $sheet->setCellValue('A' . $row_num, $temp);
    $sheet->setCellValue('B' . $row_num, $pressure);
    $sheet->setCellValue('C' . $row_num, $humidity); 
    $sheet->setCellValue('D' . $row_num, $wind_speed); 
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
$drawing->setPath('../img/logo_proyecto.png'); // Reemplaza con la ruta a tu imagen
$drawing->setCoordinates('F1'); // Coloca la imagen en la celda F1
$drawing->setWidth(150); // Ajusta el ancho de la imagen
$drawing->setHeight(150); // Ajusta la altura de la imagen
$drawing->setWorksheet($sheet);

// Establecer nombre del archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte_Meteorologico.xlsx"');
header('Cache-Control: max-age=0');

// Guardar el archivo
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Cerrar la base de datos
$conexion->close();
?>
