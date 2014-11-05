Install Bot Web API
===================

This guide describes how to install the Bot Web API Server on Microsoft Windows. The installation is currently tested with
* Microsoft Windows 8.1

## 1 Manual Installation

### 1.2 Install missing applications

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