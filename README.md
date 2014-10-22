Bot Web API
===========

Bot Web API is a web-based interface for KIPR robot controllers. It allows user to, amongst others, create, edit and run programs, get sensor readings or control motors and servos.

**Note:** This project is currently under development!

## 1 API

**Note:** [RFC 7231](http://tools.ietf.org/html/rfc7231#section-4.3) describes the HTTP methods.

### 1.1 Overview

**Note:** Not all resources may be available on your platform!

Resource                                               | GET           | POST          | PUT           | DELETE 
:------------------------------------------------------|:--------------|:--------------|:--------------|:--------------
/api                                                   | list of top-level resources | - | - | -
/api/sessions                                          | list of logged in users | login | - | logout all
/api/sessions/&lt;id&gt;                               | user information | - | - | logout
/api/projects                                          | list of projects | create project | - | delete all projects
/api/projects/&lt;project&gt;                          | list of project resources | - | - | delete &lt;project&gt;
/api/projects/&lt;project&gt;/files                    | list of project files | add file | - | delete all files
/api/projects/&lt;project&gt;/files/&lt;file&gt;       | get file content | - | update &lt;file&gt; | delete &lt;file&gt;
/api/projects/&lt;project&gt;/options                  | list of project options | - | - | -
/api/projects/&lt;project&gt;/options/&lt;option&gt;   | get option value | - | set option value | -
/api/projects/&lt;project&gt;/binaries                 | list of binaries | compile binary | - | delete all binaries
/api/projects/&lt;project&gt;/binaries/&lt;binary&gt;  | execute binary | - | recompile binary | delete binary
/api/sensors                                           | list of sensors | - | - | -
/api/sensors/&lt;sensor&gt;                            | sensor readings | - | - | - 
/api/actuators                                         | list of actuators | - | - | -
/api/actuators/motors                                  | list of motors | - | - | -
/api/actuators/motors/&lt;motor&gt;                    | get motor position / velocity ... | - | set position / velocity ... | - 
/api/actuators/servos                                  | list of servos | - | - | -
/api/actuators/servos/&lt;servo&gt;                    | get servo position | - | set position | - 
/api/files                                             | listing of the root directory | add file/directory | - | remove /*
/api/files/&lt;path&gt;/&lt;directory&gt;              | listing of /&lt;path&gt;/&lt;directory&gt; | add file/directory | - | remove /&lt;path&gt;/&lt;directory&gt;
/api/files/&lt;path&gt;/&lt;file&gt;                   | content of /&lt;path&gt;/&lt;file&gt; | - | set/update file content | remove file
/api/connections                                       | list of available connections types | - | - | -
/api/connections/SSH                                   | - | establish new connection | - | close all ssh connections
/api/connections/SSH/&lt;connection&gt;                | - | - | execute remote command | close connection
/api/connections/botui                                 | - | establish new connection | - | close all botui connections
/api/connections/botui/&lt;connection&gt;              | get screenshot | - | send mouse events | close connection

## 1.2 Manual installation / installation for developer or contributor

**Note:** This is the only way to install Bot Web API on a Link with firmware 2.0.3 or below.

### 1.2.1 Create swap file
Create the swap file
```Shell
root@kovan:~# dd if=/dev/zero of=/swapfile bs=1024 count=262144
root@kovan:~# mkswap /swapfile
root@kovan:~# swapon /swapfile
```

Add the following line to `/etc/fstab`
```
/swapfile            none                 swap       defaults              0  0
```

### 1.2.2 Install missing packets

#### Install wget
```Shell
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/wget_1.13.4-r13.1_armv5te.ipk
```

#### Install curl
```Shell
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/libcurl5_7.23.1-r0_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/curl_7.23.1-r0_armv5te.ipk
```

#### Install CA certificates
```Shell
root@kovan:~# mkdir -p /etc/ssl/certs
```

Add the following to the end of file */etc/profile*
```Shell
export SSL_CERT_DIR=/etc/ssl/certs
```

```Shell
root@kovan:~# source /etc/profile
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/openssl-misc_1.0.0g-r15.0_armv5te.ipk
root@kovan:~# cd /etc/ssl/certs
root@kovan:/etc/ssl/certs# curl http://curl.haxx.se/ca/cacert.pem -o cacert.pem
root@kovan:/etc/ssl/certs# awk 'split_after==1{n++;split_after=0} /-----END CERTIFICATE-----/ {split_after=1} {print > "cert" n ".pem"}' cacert.pem 
root@kovan:/etc/ssl/certs# for file in *.pem; do ln -s $file `openssl x509 -hash -noout -in $file`.0; done
root@kovan:/etc/ssl/certs# cd ~
```

#### Install git
```Shell
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/git_1.7.7-r2_armv5te.ipk
root@kovan:~# git config --global http.sslcainfo /etc/ssl/certs/cacert.pem
```

#### Install lighttpd

```Shell
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/libpcre0_8.21-r0_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-indexfile_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-access_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-dirlisting_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-accesslog_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-staticfile_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-fastcgi_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-rewrite_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd_1.4.30-r2_armv5te.ipk
```

#### Install PHP
```Shell
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/libmysqlclient16_5.1.40-r7_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/php-cgi_5.3.6-r0.0_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/php_5.3.6-r0.0_armv5te.ipk
```

### 1.2.3 Prepare the development environment

#### Clone the project
```Shell
root@kovan:~# git clone https://github.com/kipr/botwebapi.git
```

#### Use our lighttpd configuration file
```Shell
root@kovan:~# cd botwebapi/
root@kovan:~/botwebapi# rm /etc/lighttpd.conf
root@kovan:~/botwebapi# ln -s ~/botwebapi/lighttpd/lighttpd.conf /etc/
root@kovan:~/botwebapi# ln -s ~/botwebapi/botwebapi /www/pages/api
```

#### Restart lighttpd
```Shell
root@kovan:~/botwebapi# /etc/init.d/lighttpd restart
```
