FROM php:7.4-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Configure Apache to allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Configure PHP include_path
RUN echo 'include_path = ".:/var/www/html"' > /usr/local/etc/php/conf.d/zz-custom.ini

# Expose port 80 to the outside world
RUN docker-php-ext-install pdo_mysql

# Copy the application files to the web server's document root
COPY . /var/www/html/

# Set the working directory
WORKDIR /var/www/html/

# Set permissions for the web server to write to the files
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

EXPOSE 80
