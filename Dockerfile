FROM php:8.2-apache

# Instalar dependencias del sistema y controladores de PostgreSQL y MySQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql pgsql

# Configurar Apache para escuchar en el puerto 10000 requerido por Render
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html/
EXPOSE 10000
