Install Bot Web API
===================

This guide describes how to install the Bot Web API Server on Microsoft Windows. The installation is currently tested with
* Microsoft Windows 8.1

## 1 Manual Installation

### 1.1 Install PHP
1. Download the latest version from [here](http://windows.php.net/download/).
2. Extract the archive content into `C:\php` or another folder (the folder path cannot have any spaces).
3. Rename `C:\php\php.ini-production` to `C:\php\php.ini`

### 1.2 Install Lighttpd
We will use [Lighttpd](http://redmine.lighttpd.net/) as web server as it does not require an installation.

1. Download [version 1.4.35](http://lighttpd.dtech.hu/LightTPD-1.4.35-1-IPv6-Win32-SSL.zip) or the latest version from [here](http://redmine.lighttpd.net/projects/1/wiki/tutoriallighttpdandphp#Windows).
2. Extract the archive content into `C:\LightTPD` or another folder.

### 1.3 Clone the Bot Web API git repository
1. If not already done, install your favorite git tool (e.g. [git](http://git-scm.com/downloads), [SourceTree](http://www.sourcetreeapp.com/), [GitHub for Windows](https://windows.github.com/))
2. Clone `https://github.com/kipr/botwebapi.git`
