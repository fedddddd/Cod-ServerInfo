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
*JSON Output
```json
  {"success":true,
  "Clients":[
    {"ClientSlot":"0","Score":"0","Bot":"0","Ping":"594","Name":"Lolman"}
   ],
   "Gamename":"IW5",
   "Hostname":"^5fed ^7| ^6GunGame^7 | ^:$help",
   "Dvars":{
      "Map":"Rust",
      "Gametype":"Free for All",
      "External IP":"45.9.188.76",
      "Latency":"3 ms",
      "Maxclients":"18"
      }
    }
```
