client-ident-proxy
==================

这是一个http代理，经过的通讯会被识别出关键信息。用于服务器可以用来采集分析POST的数据，用于网关可以用来采集微信、淘宝、或什么其他非ssl加密的数据。哦，淘宝现在是全SSL加密了，废了。

在全新ubuntu里安装
```
sudo su
cd /srv
wget https://raw.githubusercontent.com/huangqiheng/client-ident-proxy/master/init.sh -O - | sh
```

在VM开发环境下，取消烦人的开机登录：
```
vim /etc/init/tty1.conf
--------------
exec /sbin/getty -8 38400 tty1
to:
exec /bin/login -f USERNAME < /dev/tty1 > /dev/tty1 2>&1
--------------
```

如果见不到tty1.conf这个文件：
```
sudo apt-get install mingetty
vim /etc/event.d/tty1
--------------
exec /sbin/getty 38400 tty1
to:
exec /sbin/mingetty --autologin USERNAME tty1
--------------
```

如果是ubuntu server 1510，添加autologin配置
```
vim /etc/systemd/system/getty.target.wants/getty@tty1.service
-------------------------
ExecStart=/sbin/agetty --noclear %I $TERM
to:
ExecStart=/sbin/agetty --noclear --autologin <username> %I $TERM
-------------------------
```

设置成开机启动nginx正向代理：
```
vim /etc/rc.local
----------------
sh /srv/client-ident-proxy/run
exit 0
----------------
```
