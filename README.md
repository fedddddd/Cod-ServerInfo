# Cod-ServerInfo
Server Status for Cod servers
# Usage
```php
    require_once $_SERVER['DOCUMENT_ROOT']."FILELOCATION/server-info.php";
    $info = new Server\ServerInfo('127.0.0.1', 27016, 'password');
    /*
      DVARS -> $info->out['Dvars']
      CLIENTS -> $info->out['Clients']
      ...
    */
    echo json_encode($info->out);
```
* JSON Output
```json
  {"success":true,
  "Clients":[
    {"ClientSlot":"0","Score":"0","Bot":"0","Ping":"50","Name":"player"}
   ],
   "Gamename":"IW5",
   "Hostname":"^5fed ^7| ^6GunGame^7 | ^:$help",
   "Dvars":{
      "Map":"Rust",
      "Gametype":"Free for All",
      "External IP":"X.X.X.X",
      "Latency":"3 ms",
      "Maxclients":"18"
      }
    }
```
# Example frontend
![ServerStatus](https://i.imgur.com/PBhMsRS.png)
