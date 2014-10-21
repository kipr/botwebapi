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

API
---

**Note:** [RFC 7231](http://tools.ietf.org/html/rfc7231#section-4.3) describes the HTTP methods.

### Overview

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

### 



