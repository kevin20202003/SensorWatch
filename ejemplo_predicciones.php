<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predicciones en Tiempo Real</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Predicciones de Datos</h1>
    <select onchange="cargarDatos()">
        <option value="suelo">Datos del Suelo</option>
        <option value="ambiente">Datos del Ambiente</option>
        <option value="meteorologico">Datos Meteorológicos</option>
    </select>
    <canvas id="chart" width="400" height="200"></canvas>

    <script>
        let grafico = null; // Variable global para almacenar la instancia del gráfico

        async function cargarDatos() {
            const tabla = document.querySelector('select').value;  // Obtiene la opción seleccionada
            const response = await fetch(`api.php?tabla=${tabla}`);
            const data = await response.json();

            if (data.status === "success") {
                const fechas = data.data.map(d => d.created_at || d.date); // Manejar las columnas de fecha dinámicamente
                const columnas = Object.keys(data.data[0]).filter(key => key !== 'created_at' && key !== 'date'); // Filtrar fecha de las columnas

                // Crear un dataset para cada columna
                const datasets = columnas.map(columna => {
                    return {
                        label: columna,
                        data: data.data.map(d => parseFloat(d[columna] || 0)),
                        borderColor: getRandomColor(),
                        borderWidth: 2
                    };
                });

                mostrarGrafico(fechas, datasets);
            } else {
                alert(data.message || "Error al cargar los datos");
            }
        }

        // Función para generar colores aleatorios para cada columna
        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        function mostrarGrafico(fechas, datasets) {
            const ctx = document.getElementById('chart').getContext('2d');

            // Destruir el gráfico existente si existe
            if (grafico) {
                grafico.destroy();
            }

            // Crear un nuevo gráfico
            grafico = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: fechas,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Valor'
                            }
                        }
                    }
                }
            });
        }

        // Cargar datos iniciales
        cargarDatos();
    </script>
</body>
</html>
