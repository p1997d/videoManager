# Use the official PHP image as the base image
FROM php:8.1-cli

# Install system dependencies 
RUN apt-get update && apt-get install -y \
	build-essential\
	libpng-dev\
	libjpeg62-turbo-dev\
	libfreetype6-dev
	locales \
	libzip-dev
	libonig-dev\
	zip\
	jpegoptim optipng pngquant gifsicle \
	vim \
	unzip \
	curl \
	git
	
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install composer 
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory 
WORKDIR /var/www

# Remove the default nginx index page 
RUN rm -rf /var/www/html