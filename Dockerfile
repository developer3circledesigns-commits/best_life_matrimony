# BestLife Matrimony — PHP + Apache (Docker)
FROM php:8.2-apache

# Enable Apache rewrite and headers/expires/deflate
RUN a2enmod rewrite headers expires deflate mime

# Install PHP MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql

# Configure Apache to allow .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Set ServerName to suppress warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy project files
COPY . /var/www/html/

# Fix .htaccess for Docker root (remove XAMPP subfolder base)
RUN sed -i 's|RewriteBase /BestLife_Matrimony/|RewriteBase /|g' /var/www/html/.htaccess && \
    sed -i 's|ErrorDocument 404 /BestLife_Matrimony/404.php|ErrorDocument 404 /404.php|g' /var/www/html/.htaccess

# PHP upload limits — fix 2MB vs 5MB miscalculation (was default 2M, code allows 5M)
RUN echo "upload_max_filesize = 10M\npost_max_size = 12M\nmemory_limit = 256M\nmax_file_uploads = 20\n" > /usr/local/etc/php/conf.d/uploads.ini

# Permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80
