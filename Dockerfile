# Imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias para Slim
RUN docker-php-ext-install pdo pdo_mysql

# Copiar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar todo el proyecto al contenedor
COPY . /var/www/html

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias de PHP (Slim Framework)
RUN composer install

# Cambiar el DocumentRoot a /var/www/html/public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess (activar AllowOverride All)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Activar mod_rewrite para Slim
RUN a2enmod rewrite

# Exponer el puerto 80
EXPOSE 80

# ✅ Dar permisos al archivo y carpeta de datos para que PHP lo modifique
RUN chown -R www-data:www-data /var/www/html/data \
    && chmod -R 775 /var/www/html/data
