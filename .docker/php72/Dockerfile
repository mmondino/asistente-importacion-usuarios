FROM php:7.2.8-apache

ENV http_proxy="http://10.1.30.219:3128"
ENV https_proxy="http://10.1.30.219:3128"
ENV no_proxy=""

RUN apt-get update \
    && apt-get install -y unzip libaio1 \
    && apt-get clean -y

RUN a2enmod rewrite

ADD ./instantclient/instantclient-basiclite-linux.x64-12.2.0.1.0.zip /tmp/ 
ADD ./instantclient/instantclient-sdk-linux.x64-12.2.0.1.0.zip /tmp/

RUN unzip /tmp/instantclient-basiclite-linux.x64-12.2.0.1.0.zip -d /usr/local/ \
    && unzip /tmp/instantclient-sdk-linux.x64-12.2.0.1.0.zip -d /usr/local/ 

RUN pear config-set http_proxy http://10.1.30.219:3128

# install oci8
RUN ln -s /usr/local/instantclient_12_2 /usr/local/instantclient \
    && ln -s /usr/local/instantclient/libclntsh.so.12.1 /usr/local/instantclient/libclntsh.so \
    && echo 'instantclient,/usr/local/instantclient' | pecl install oci8

ENV LD_LIBRARY_PATH /usr/local/instantclient_12_2/

COPY ./conf/php.ini /usr/local/etc/php/

EXPOSE 8507
EXPOSE 8508