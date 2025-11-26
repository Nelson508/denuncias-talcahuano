# Imagen base oficial de PHP con servidor embebido y extensiones
FROM php:8.2-apache

# Activar mod_rewrite si lo necesitas (opcional)
RUN a2enmod rewrite

# Copiar el proyecto al contenedor
COPY . /var/www/html/

# Dar permisos al archivo JSON (porque tu "BD" es un archivo)
RUN chmod -R 777 /var/www/html/data

# Exponer el puerto donde correrá el servidor
EXPOSE 80
