import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
import joblib
from sqlalchemy import create_engine, text
import time

# Configuración de conexión con SQLAlchemy
db_uri = "mysql+mysqlconnector://root:@localhost/invernadero"
engine = create_engine(db_uri)

def obtener_datos(tabla):
    """Obtiene los datos históricos desde la base de datos."""
    query = f"SELECT * FROM {tabla}"
    data = pd.read_sql(query, engine)
    return data

def entrenar_y_predecir(tabla, variables, horizonte, columna_fecha):
    """Entrena un modelo y predice valores futuros."""
    # Obtener datos históricos
    datos = obtener_datos(tabla)

    # Validación de columnas necesarias
    for columna in variables + [columna_fecha]:
        if columna not in datos.columns:
            raise ValueError(f"La columna '{columna}' no existe en la tabla '{tabla}'")

    # Preparar datos para el modelo
    datos[columna_fecha] = pd.to_datetime(datos[columna_fecha])
    datos = datos.sort_values(columna_fecha)

    X = datos[variables].values
    y = datos[variables].shift(-1).fillna(0).values  # Etiquetas (valor siguiente)

    # Dividir datos en entrenamiento y prueba
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

    # Entrenar modelo
    modelo = RandomForestRegressor(n_estimators=100, random_state=42)
    modelo.fit(X_train, y_train)

    # Guardar modelo entrenado
    joblib.dump(modelo, f"modelo_{tabla}.pkl")

    # Predicción de datos futuros
    ultimos_datos = datos[variables].iloc[-horizonte:].values
    predicciones = modelo.predict(ultimos_datos)

    # Generar fechas futuras sin microsegundos
    fechas_futuras = [pd.Timestamp.now().replace(microsecond=0) + pd.Timedelta(days=i) for i in range(1, horizonte + 1)]

    # Crear un DataFrame con las predicciones
    predicciones_df = pd.DataFrame(predicciones, columns=variables)
    predicciones_df[columna_fecha] = fechas_futuras

    return predicciones_df

def guardar_predicciones(tabla, predicciones, columna_fecha):
    """Guarda las predicciones en la base de datos sin verificar existencia."""
    tabla_predicciones = f"{tabla}_predicciones"

    with engine.connect() as conn:  # Abre la conexión
        for _, fila in predicciones.iterrows():
            # Insertar nuevo registro
            try:
                # Crear un DataFrame con la fecha incluida para la inserción
                fila_con_fecha = fila.copy()
                fila_con_fecha[columna_fecha] = fila[columna_fecha]  # Asegura que la fecha se incluya
                predicciones_sin_fecha = fila_con_fecha.drop(columns=[columna_fecha])
                predicciones_sin_fecha = pd.DataFrame([predicciones_sin_fecha])

                # Insertar los valores, asegurando que la columna de fecha esté incluida
                predicciones_sin_fecha.to_sql(
                    tabla_predicciones, con=conn, if_exists='append', index=False
                )
                print(f"Insertado nuevo registro para la fecha: {fila[columna_fecha]}")
            except Exception as e:
                print(f"Error al insertar registro para la fecha {fila[columna_fecha]}: {e}")

# Bucle para la ejecución recurrente
while True:
    for tabla, variables, horizonte, columna_fecha in [
        ('datos_suelo', ['temperatura', 'humedad', 'PH'], 7, 'created_at'),
        ('datos_ambiente', ['temperatura_amb', 'humedad_amb', 'lux'], 7, 'created_at'),
        ('datos_meteorologicos', ['temp', 'humidity', 'pressure', 'wind_speed'], 30, 'date')
    ]:
        try:
            predicciones = entrenar_y_predecir(tabla, variables, horizonte, columna_fecha)
            guardar_predicciones(tabla, predicciones, columna_fecha)
        except Exception as e:
            print(f"Error procesando la tabla '{tabla}': {e}")

    print("Esperando 1 hora y 30 minutos para la próxima ejecución...")
    time.sleep(5400)
