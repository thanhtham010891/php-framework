FROM ubuntu:18.04
MAINTAINER thamtt@nal.vn

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update && apt-get -y install nano curl git composer
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN apt-get -y update
RUN apt-get install -y apache2

# php libraries
RUN apt-get update && apt-get install -y php7.2-mbstring \
    php7.2-mysql \
    php7.2-zip \
    php7.2-opcache \
    php7.2-json \
    php7.2-gd \
    php7.2-curl \
    php7.2-zip \
    php7.2-xml \
    libapache2-mod-php7.2

RUN a2enmod rewrite ssl
RUN rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

EXPOSE 80 443

ENTRYPOINT ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]