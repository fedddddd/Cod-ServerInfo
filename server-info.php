<?php
namespace Server {
    class ServerInfo {
        private $ip;
        private $port;
        private $gamename;
        private $map;
        private $password;
        private $externalIP;
        public $out = array();
        private $rconString = "\xff\xff\xff\xffrcon %rcon_pwd%";
        function __construct($ip, $port, $password) {
            $this->ip = $ip;
            $this->port = $port;
            $this->password = $password;
            $this->externalIP = $this->getExternalIP();
            $this->connectRcon();
        }
        private function sendCommand($RconConnection, $command) {
            $send = str_replace('%rcon_pwd%', $this->password, $this->rconString)." ".$command;
            fwrite($RconConnection, $send);
            $result = fread($RconConnection, 2048);
            return $result;
        }
        private function connectRcon() {
            $RconConnection = stream_socket_client("udp://{$this->ip}:{$this->port}", $errno, $errstr, 5);
            if (!$RconConnection) { 
                $this->out = array("success" => false);
                return;
            }
            $this->gamename = $this->parseDVAR($this->sendCommand($RconConnection, "get gamename"));
            $this->out["success"] = true;
            $this->getMaps();
            $this->map = $this->getMap($this->parseDVAR($this->sendCommand($RconConnection, "get mapname")));
            $this->out["Clients"] = $this->parsePlayerList($this->sendCommand($RconConnection, "status"));
            $this->out["Gamename"] = $this->gamename;
            $this->out["Hostname"] = $this->parseDVAR($this->sendCommand($RconConnection, "get sv_hostname"));
            $this->out["Dvars"]["Map"] = $this->map["Alias"];
            $this->out["Dvars"]["Gametype"] = $this->getGamemode($this->parseDVAR($this->sendCommand($RconConnection, "get g_gametype")));
            $this->out["Dvars"]["External IP"] = $this->externalIP;
            $this->out["Dvars"]["Latency"] = $this->ping("8.8.8.8");
            $this->out["Dvars"]["Maxclients"] = $this->parseDVAR($this->sendCommand($RconConnection, "get sv_maxclients"));
        }
        private function ping($host, $port = 53, $timeout = 3) { 
            $tB = microtime(true); 
            $fP = fSockOpen($host, $port, $errno, $errstr, $timeout); 
            if (!$fP) { return "down"; } 
            $tA = microtime(true); 
            return round((($tA - $tB) * 1000), 0)." ms"; 
        }
        private function getGamemode($mode) {
            $gamemodes = array(
                'infect' => 'Infected',
                'war' => 'Team Deathmatch',
                'sd' => 'Search & Destroy',
                'dm' => 'Free for All',
                'koth' => 'Headquarters',
                'sab' => 'Sabotage',
                'ctf' => 'Capture The Flag',
                'gun' => 'Gun Game',
                'dd' => 'Demolition',
                'hlnd' => 'Stick & Stones',
                'oic' => 'One in the Chamber',
                'shrp' => 'Sharp Shooter',
                'tdef' => 'Team Defender',
                'jugg' => 'Juggernaut',
                'tjugg' => 'Team Juggernaut',
                'conf' => 'Kill Confirmed',
                'grnd' => 'Drop Zone',
                'dom' => 'Domination'
            );
            return $gamemodes[$mode];
        }
        private function getExternalIP() {
            $externalContent = file_get_contents('http://checkip.dyndns.com/');
            preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
            return $m[1];
        }
        private function parseDVAR($dvar) {
            preg_match('/"([^"]+)"/', $dvar, $m);
            return $m[1];
        }
        private function getMap($mapname) {
            foreach ($this->maps as $maplist) {
                if ($maplist['Game'] == $this->gamename) {
                    foreach ($maplist['Maps'] as $map) {
                        if (strtolower($map['Name']) == strtolower($mapname) || strtolower($map['Alias']) == strtolower($mapname)) return $map;
                    }
                }
            }
        }
        private function parsePlayerList($raw) {
            $lines = explode("\n", $raw);
            $lines = array_splice($lines, 4);
            $players = array();
            foreach($lines as $line) {
                if (empty($line)) continue;
                $line = preg_replace('/\s+/', ' ', $line);
                $player = explode(' ', trim($line));
                $player = array(
                    "ClientSlot" => $player[0],
                    "Score" => $player[1],
                    "Bot" => $player[2],
                    "Ping" => $player[3],
                    "Name" => $player[5]
                );
                $players[] = $player;
            }
            return $players;
        }
        private function getMaps() {
            $maplist = fopen(dirname(__FILE__)."/maps.json", "r");
            $this->maps = json_decode(fread($maplist, filesize(dirname(__FILE__)."/maps.json")), true);
            fclose($maplist);
        }
    }
}
?>