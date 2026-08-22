# BestLife Matrimony — PHP + Apache (Docker)
FROM php:8.2-apache

# Enable Apache rewrite and headers/expires/deflate
RUN a2enmod rewrite headers expires deflate mime

# Configure Apache to allow .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Set ServerName to suppress warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy project files
COPY . /var/www/html/

# Fix .htaccess for Docker root (remove XAMPP subfolder base)
RUN sed -i 's|RewriteBase /BestLife_Matrimony/|RewriteBase /|g' /var/www/html/.htaccess && \
    sed -i 's|ErrorDocument 404 /BestLife_Matrimony/404.php|ErrorDocument 404 /404.php|g' /var/www/html/.htaccess

# Permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80
