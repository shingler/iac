FROM centos:7
MAINTAINER singlerwong@qq.com
RUN yum update -y
RUN yum install -y vim \
    && ln -snf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
    && echo 'Asia/Shanghai' > /etc/timezone
RUN yum install -y php php-mysql php-pdo
RUN yum install -y httpd
RUN curl -sS https://getcomposer.org/installer | php
VOLUME /var/www
WORKDIR /var/www
COPY apache2.conf /etc/httpd/conf.d/iac.conf
EXPOSE 8200
CMD /etc/init.d/httpd start & php -v & /bin/bash