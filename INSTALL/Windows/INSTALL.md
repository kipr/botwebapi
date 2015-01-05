Install Bot Web API
===================

This guide describes how to install the Bot Web API Server on Microsoft Windows. The installation is currently tested with
* Microsoft Windows 8.1

## 1 Manual Installation

### 1.1 Install missing applications

#### Install PHP
1. Download the latest version from [here](http://windows.php.net/download/).
2. Extract the archive content into `C:\php`. If you choose another path (this path cannot have any white spaces), you have adapt `botwebapi\INSTALL\Windows\lighttpd\lighttpd.conf`.

#### Install Lighttpd
We will use [Lighttpd](http://redmine.lighttpd.net/) as web server as it does not require an installation.

1. Download [version 1.4.35](http://lighttpd.dtech.hu/LightTPD-1.4.35-1-IPv6-Win32-SSL.zip) or the latest version from [here](http://redmine.lighttpd.net/projects/1/wiki/tutoriallighttpdandphp#Windows).
2. Extract the archive content into `C:\LightTPD`. If you choose another path you have to adapt the following steps.

#### Install git
If not already done, install your favorite git tool (e.g. [git](http://git-scm.com/downloads), [SourceTree](http://www.sourcetreeapp.com/), [GitHub for Windows](https://windows.github.com/))

### 1.2 Prepare the development environment

#### Clone the project
Clone `https://github.com/kipr/botwebapi.git` with your git tool. The subsequent steps will assume that you cloned it into `C:\Users\stefan\Documents\Projects\botwebapi`

#### Patch the config files and link Bot Web API into the system
Open a Command Prompt as administrator and type:

```
C:\WINDOWS\system32>del C:\LightTPD\conf\lighttpd.conf
C:\WINDOWS\system32>mklink C:\LightTPD\conf\lighttpd.conf C:\Users\stefan\Documents\Projects\botwebapi\INSTALL\Windows\lighttpd\lighttpd.conf
C:\WINDOWS\system32>mklink /D C:\LightTPD\htdocs\api C:\Users\stefan\Documents\Projects\botwebapi\botwebapi
```

### Edit the fixed paths
This manual installation assumes that your

1. Bot Web API source (`<botwebapi>`) is located at `C:\Users\stefa_000\Documents\Projects\botwebapi`
2. Windows system 32 directory is located at `C:\windows\system32`
3. KISS Platform 5 is located at `C:\Program Files (x86)\KISS Platform 5.1.2\KISS`

Please adjust the following files if this is not the case:

1. Open `<botwebapi>\botwebapi\botwebapi.php` and verify:
  1. `CMD_PATH` has to point to your cmd.exe (usually `C:\windows\system32\cmd.exe`)
  2. `COMPILE_HELPER_PATH` has to point to your compile.bat (located at `<botwebapi>\INSTALL\Windows\compile.bat`)
2. Open `<botwebapi>\INSTALL\Windows\compile.bat` and verify that the path to your KISS Platform 5 is correct (usually `C:\Program Files (x86)\KISS Platform 5.1.2\KISS`)

## 2 Launch the LightTPD server
If not already launched, navigate to `C:\LightTPD` and launch (double click) on `LightTPD.exe`

## 3 Check if the Bot Web API is ready
Open http://127.0.0.1/api in a browser. Your browser should display a JSON response like the following one:
```
{
   "about": {
        "resource_class": "botwebapi\\resources\\api\\Api",
        "resource_version": "1.0",
        "resource_homepage": "https://github.com/kipr/botwebapi"
    },
    "links": {
        "self": {
            "href": "http://127.0.0.1/api",
            "type": "application/vnd.KIPR.BotWebApi; charset=utf-8"
        },
        "fs": {
            "href": "http://127.0.0.1/api/fs",
            "type": "application/vnd.KIPR.BotWebApi; charset=utf-8"
        },
        "workspaces": {
            "href": "http://127.0.0.1/api/workspaces",
            "type": "application/vnd.KIPR.BotWebApi; charset=utf-8"
        }
    }
}
```
