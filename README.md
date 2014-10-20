Bot Web API
===========

Bot web API is a web-based interface for KIPR robot controllers. It allows user to, amongst others, create, edit and run programs, get sensor readings or control motors and servos.

**Note:** This project is currently under development!

Installation
------------

### KIPR Link 2.0.3 (only manual installation possible)

#### Install lighttpd

```Shell
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/libpcre0_8.21-r0_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-indexfile_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-access_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-dirlisting_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-accesslog_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-staticfile_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd-module-fastcgi_1.4.30-r2_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/lighttpd_1.4.30-r2_armv5te.ipk
```

#### Install PHP
```Shell
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/libmysqlclient16_5.1.40-r7_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/php-cgi_5.3.6-r0.0_armv5te.ipk
root@kovan:~# opkg install http://netv.bunnie-bar.com/build/kovan-debug/LATEST/armv5te/php_5.3.6-r0.0_armv5te.ipk
```

#### Enable the lighttpd FastCGI module
1. Open `/etc/lighttpd.conf`
2. Add the FastCGI module to the modules to load:

        #                               "mod_setenv",                                  
                                        "mod_fastcgi",  
3. Uncomment the configuration for the FastCGI module and adjust the path:

        #### fastcgi module                                                
        ## read fastcgi.txt for more info                          
        ## for PHP don't forget to set cgi.fix_pathinfo = 1 in the php.ini
        fastcgi.server             = ( ".php" =>                                     
                                       ( "localhost" =>   
                                         (                
                                           "socket" => "/tmp/php-fastcgi.socket",
                                           "bin-path" => "/usr/bin/php-cgi"
                                         )                             
                                       )                               
                                    )

#### Test the server
1. Create `/www/pages/info.php` with the following content:

        <?php
        phpinfo();
        ?>
2. (Re-)start the web server with `/etc/init.d/lighttpd restart`
3. You should now be able to open `<IP of KIPR Link>/info.php` with a web browser and see information about your PHP installation.
