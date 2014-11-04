Bot Web API
===========

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

### 1.2 Messages

The Bot Web API uses JSON as message format.

#### Error Message

**JSON schema:**
```JSON
{
  "title" : "Error Message",
  "type" : "object",
  "required" : true,
  "properties" : {
    "Error" : {
      "type" : "object",
      "required" : true,
      "properties" : {
        "class" : {
          "type" : "string",
          "required" : true
        },
        "sub_class": {
          "type":"string",
          "required":false
        },
        "details" : {
          "type" : "object",
          "required":false
        }
      }
    }
  }
}
```

**Example:**
```JSON
{
  "Error" : {
    "class" : "Server Error",
    "sub_class" : "Internal Server Error",
    "details" : "Internal error: INCLUDE_PATH does not name a directory!"
  }
}
```

#### /api
#### GET
Returns a list describing all available resources.

**JSON schema:**
```JSON
{
	"type" : "object",
	"required" : true,
	"properties" : {
		"resources" : {
			"type" : "array",
			"required" : true,
			"items" : {
				"type" : "object",
				"required" : false,
				"properties" : {
					"name" : {
						"type" : "string",
						"required" : true
					},
					"uri" : {
						"type" : "string",
						"required" : true
					},
					"about" : {
						"type":"object",
						"required":false,
						"properties":{
							"version": {
								"type":"string",
								"required":false
							},
							"homepage": {
								"type":"string",
								"required":false
							}
						}
					}
				}
			}
		}
	}
}
```

**Example:**
```JSON
{
  "resources": [
    {
      "name": "api",
      "uri": "/api",
      "about": {
        "version": "1.0",
        "homepage": "https://github.com/kipr/botwebapi"
      }
    },
    {
      "name": "projects",
      "uri": "/api/projects",
      "about": {
        "version": "0.0",
        "homepage": "https://github.com/kipr/botwebapi"
      }
    },
    {
      "name": "sessions",
      "uri": "/api/sessions",
      "about": {
        "version": "0.0",
        "homepage": "https://github.com/kipr/botwebapi"
      }
    }
  ]
}
```

