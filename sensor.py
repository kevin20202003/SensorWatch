import serial
import mysql.connector
import json

# Configuración de la base de datos
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",  # Cambiar si tu contraseña de MySQL no está vacía
    "database": "invernadero",
}

# Configuración del puerto serie
serial_port = "COM3"  # Cambiar según el puerto COM de tu dispositivo
baud_rate = 115200

# Función para insertar datos en la tabla de datos ambientales
def insertar_datos_ambientales(cursor, temperatura, humedad, luz):
    try:
        query = "INSERT INTO datos_ambiente (temperatura_amb, humedad_amb, lux) VALUES (%s, %s, %s)"
        cursor.execute(query, (temperatura, humedad, luz))
        print("Datos ambientales insertados.")
    except mysql.connector.Error as err:
        print(f"Error al insertar en datos_ambientales: {err}")

# Función para insertar datos en la tabla de datos de suelo
def insertar_datos_suelo(cursor, temperatura, humedad, ph):
    try:
        query = "INSERT INTO datos_suelo (temperatura, humedad, PH) VALUES (%s, %s, %s)"
        cursor.execute(query, (temperatura, humedad, ph))
        print("Datos de suelo insertados.")
    except mysql.connector.Error as err:
        print(f"Error al insertar en datos_suelo: {err}")

# Conexión a la base de datos
try:
    db_connection = mysql.connector.connect(**db_config)
    db_cursor = db_connection.cursor()
    print("Conexión a la base de datos establecida.")
except mysql.connector.Error as err:
    print(f"Error al conectar a la base de datos: {err}")
    exit()

# Conexión al puerto serie
try:
    ser = serial.Serial(serial_port, baud_rate, timeout=1)
    print(f"Conectado al puerto {serial_port}")
except serial.SerialException as err:
    print(f"Error con el puerto serie: {err}")
    db_connection.close()
    exit()

# Lectura y procesamiento de datos
try:
    while True:
        data = ser.readline()  # Leer línea desde el puerto serie
        try:
            data_json = json.loads(data.decode("utf-8"))
            print(f"Datos recibidos: {data_json}")

            # Extraer datos para las tablas
            temperatura_amb = data_json.get("temperaturaDHT")
            humedad_amb = data_json.get("humedadDHT")
            luz = data_json.get("luz")
            temperatura_suelo = data_json.get("temperaturaDS18B20")
            humedad_suelo = data_json.get("humedadSuelo")
            ph = data_json.get("ph")

            # Insertar en la base de datos
            insertar_datos_ambientales(db_cursor, temperatura_amb, humedad_amb, luz)
            insertar_datos_suelo(db_cursor, temperatura_suelo, humedad_suelo, ph)

            # Confirmar cambios en la base de datos
            db_connection.commit()

        except json.JSONDecodeError:
            if data.strip():  # Imprime los datos no procesados si no están vacíos
                print(f"Datos no procesados: {data}")

except KeyboardInterrupt:
    print("\nPrograma detenido por el usuario.")

finally:
    # Cerrar conexiones
    ser.close()
    db_connection.close()
    print("Conexión a la base de datos cerrada. Puerto serie cerrado.")
