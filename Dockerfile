# Usa una imagen base de PHP
FROM php:8.0-apache

# Instala Python
RUN apt-get update && apt-get install -y python3 python3-pip

# Copia el código PHP
COPY ./ /var/www/html/

# Configura el directorio de trabajo y las dependencias de Python
WORKDIR /var/www/html
RUN pip3 install -r requirements.txt

# Expone el puerto adecuado
EXPOSE 80
