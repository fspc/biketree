#############
# biketree  #
#############
# Password is password for  & 

# docker run -d --name bikebike --env VIRTUAL_HOST=biketree.bikelover.org --expose 80 -v /somewhere/public_html/bikebike:/usr/share/nginx/html nginx:1.11-alpine

FROM nginx:1.11-alpine

MAINTAINER Jonathan Rosenbaum <gnuser@gmail.com>

RUN rm -rf /usr/share/nginx/html; apk update; apk add git; apk add php5-mysql
RUN git clone https://github.com/fspc/biketree.git /usr/share/nginx/html
COPY  settings.php /usr/share/nginx/html
COPY  default.conf /etc/nginx/conf.d
RUN 	chown -R nginx:nginx /usr/share/nginx/html; \
	mkdir /var/www; ln -sf /usr/share/nginx/html/ /var/www/html; \ 
	chmod 0004 /usr/share/nginx/html/settings.php; \ 
	chmod 0004 /usr/share/nginx/html/install;

VOLUME /usr/share/nginx/html 

