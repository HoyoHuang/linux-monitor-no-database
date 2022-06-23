<?php
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

require( __DIR__ .'/../config.php');


class index
{
    function __construct()
    {
        $uri = $_SERVER['REQUEST_URI'];

        if ( $uri == '/' ) {
            $this->Home();
        }

        else{
            $function = trim($uri,'/');
            if ( method_exists(__CLASS__, $function) ) {
                header('Content-Type: application/json; charset=utf-8');
                $this->$function();
            }
            else{
                $this->Page404();
            }
        }
    }

    function Home()
    {
        // cpu
        // https://askubuntu.com/questions/988440/how-do-i-get-the-model-name-of-my-processor CLI 正常，網頁無法取得
        $command = "grep -m1 'model name' /proc/cpuinfo";
        $cpu_name = explode(':', shell_exec($command))[1];

        // 記憶體大小
        $command = " free -m | grep Mem: | awk '{print $2}' ";
        $ram_usage_total = shell_exec($command);

        // 硬碟大小
        $command = ' cat /sys/block/'. HddCode .'/size ';
        $hdd_size = number_format((float)shell_exec($command) /1024/1024, 2, '.', '');

        $page = file_get_contents(__DIR__ .'/../view/index.html');
        $page = str_replace('{cpu}', $cpu_name, $page);
        $page = str_replace('{ram}', $ram_usage_total, $page);
        $page = str_replace('{hdd}', $hdd_size, $page);
        $page = str_replace('{network_data_rate}', NetDataRate, $page);
        echo $page;
    }

    function Page404()
    {
        echo '404';
    }

    //
    public function Get()
    {
        // 網站
        $command = ' ss -tan | grep ":80 " | grep -v grep | wc -l ';
        $netstat_80 = trim(shell_exec($command));

        $command = ' ss -tan | grep ":443 " | grep -v grep | wc -l ';
        $netstat_443 = trim(shell_exec($command));

        $command = ' ss -tan | grep ":3306 " | grep -v grep | wc -l ';
        $netstat_3306 = trim(shell_exec($command));

        // time wait
        $command = ' ss -tan | grep TIME-WAIT | grep -v grep | wc -l ';
        $netstat_time_wait = trim(shell_exec($command));

        // 網卡流量
        // https://blog.51cto.com/u_15127607/3980215
        // RX
        $command = ' cat /proc/net/dev | grep '. NetCode .' | tr : " " | awk \'{print $2}\' ';
        $net_rx = number_format(((int)shell_exec($command) / 1024/1024), 2, '.', '');

        $command = ' cat /proc/net/dev | grep '. NetCode .' | tr : " " | awk \'{print $10}\' ';
        $net_tx = number_format(((int)shell_exec($command) / 1024/1024), 2, '.', '');

        $a = exec('df /');
        $a = preg_replace('/\s(?=\s)/', '', $a);
        $b = explode(' ', $a);
        $hdd_usage = $b[4];

        $command = "grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage \"%\"}'";
        $cpu_usage = number_format((float)shell_exec($command), 2, '.', '').'%';

        $command = " free -m | grep Mem: | awk '{print $2}' ";
        $ram_usage_total = shell_exec($command);
        $command = " free -m | grep Mem: | awk '{print $4}' ";
        $ram_usage_free = shell_exec($command);
        $ram_usage = number_format((1 - ((int)$ram_usage_free/(int)$ram_usage_total))*100, 2, '.', '').'%';

        $data = [
            'result' => true,
        ];

        //
        $data['monitor']['80 Port Connections'] = $netstat_80;
        $data['monitor']['443 Port Connections'] = $netstat_443;
        $data['monitor']['3306 Port Connections'] = $netstat_3306;
        $data['monitor']['Time Wait Connections'] = $netstat_time_wait;
        $data['net_rx'] = $net_rx;
        $data['net_tx'] = $net_tx;
        $data['usage']['cpu'] = $cpu_usage;
        $data['usage']['ram'] = $ram_usage;
        $data['usage']['hdd'] = $hdd_usage;

        echo json_encode($data);
    }

    //
    public function ReleaseCache()
    {
        $data = [
            'result' => true,
        ];

        $command = "sudo sync && echo 3 > /proc/sys/vm/drop_caches";
        shell_exec($command);

        echo json_encode($data);
    }

}

new index();
