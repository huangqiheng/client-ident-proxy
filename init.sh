#!/bin/sh

current_path=`dirname $(readlink -f $0)`


wwwroot=$current_path

echo ""
echo "=================== INSTALLATION NGINX ====================="
apt-get update
apt-get install -y build-essential
apt-get install -y libpcre3-dev libssl-dev
apt-get install -y git
adduser --system --no-create-home --disabled-login --disabled-password --group nginx

cd $current_path
mkdir temp
cd temp

git clone https://github.com/huangqiheng/nginx-gunzip.git
wget http://nginx.org/download/nginx-1.4.2.tar.gz
tar xvzf nginx-1.4.2.tar.gz && cd nginx-1.4.2
./configure \
	--prefix=/opt/nginx \
	--user=nginx \
	--group=nginx \
	--with-http_ssl_module \
	--without-http_scgi_module \
	--without-http_uwsgi_module \
	--with-http_sub_module \
	--add-module=../nginx-gunzip

make && make install

cd $current_path
rm -rf ./temp

echo ""
echo "================ INSTALL PHP environment =================="

apt-get install -y php5-cli php5-cgi php5-fpm php5-curl php5-mcrypt
apt-get install -y php5-memcache php5-memcached

echo ""
echo "================ INSTALL client-ident-proxy =================="

wwwroot=$current_path

if [ ! -f $current_path/run ]; then
	wget https://github.com/huangqiheng/client-ident-proxy/archive/master.tar.gz
	tar xzvf master.tar.gz
	mv client-ident-proxy-master client-ident-proxy
	rm master.tar.gz

	wwwroot=$current_path/client-ident-proxy
fi

echo ""
echo "================ INSTALL jsondb =================="

jsondb=$wwwroot/json-db

if [ ! -f $jsondb ]; then
	git clone https://github.com/huangqiheng/json-db.git

	cd $jsondb

	mkdir -p ./databases/jsondb.gw/gateway/proxy
	cp $wwwroot/schema-db.json $jsondb/databases/jsondb.gw/gateway/schema.json
	cp $wwwroot/schema-tb.json $jsondb/databases/jsondb.gw/gateway/proxy/schema.json

	chown www-data:www-data ./databases -R
	chown www-data:www-data ./cache -R
	chown www-data:www-data ./admin/uploads -R
	chown www-data:www-data ./service/queue/cache -R
fi

echo ""
echo "================ START web application firwall =================="

nginx_conf=$wwwroot/nginx.conf
sed -i "s|root.*client-ident-proxy;$|root $wwwroot;|g" $nginx_conf
sed -i "s|root.*json-db;$|root $jsondb;|g" $nginx_conf

cd $wwwroot
sh run

