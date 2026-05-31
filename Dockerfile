# 1. Usar la imagen base oficial con PHP 8.3 y Apache
FROM chialab/php:8.3-apache

# 2. Instalar utilidades del sistema necesarias para producción si hicieran falta
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Configurar el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# 4. Copiar los archivos del proyecto al contenedor
COPY . /var/www/html

# 5. Instalar dependencias de Composer en modo producción (Rápido y sin herramientas de desarrollo)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 6. Configurar Apache de raíz para evitar el error "Forbidden" apuntando a /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# 7. Habilitar el módulo rewrite de Apache (Crucial para las rutas URL de Laravel)
RUN a2enmod rewrite

# 8. Asignar los permisos correctos al usuario 'www-data' de Apache en todo el directorio
RUN chown -R www-data:www-data /var/www/html

# 9. Exponer el puerto estándar HTTP
EXPOSE 80

# 10. Comando por defecto para iniciar el servidor web
CMD ["apache2-foreground"]
