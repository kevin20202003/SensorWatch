# Usa una imagen base de PHP
FROM php:8.0-apache

# Instala Python
RUN apt-get update && apt-get install -y python3 python3-pip

# Copia el código PHP y el archivo requirements.txt al contenedor
COPY ./ /var/www/html/

# Asegúrate de que el archivo requirements.txt esté en el directorio correcto
WORKDIR /var/www/html

# Instala las dependencias de Python
RUN pip3 install -r requirements.txt

# Expone el puerto adecuado
EXPOSE 80
