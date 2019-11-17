<?php

namespace v20100t\PlantumlGraph;

use GuzzleHttp\Exception\ClientException;

class Tools
{
    public static function getUrls($baseUrl, $encodedGraph)
    {
        return $urls = [
            'debug' => $baseUrl.'uml/'.$encodedGraph,
            'svg' => $baseUrl.'svg/'.$encodedGraph,
            'txt' => $baseUrl.'txt/'.$encodedGraph,
            'png' => $baseUrl.'png/'.$encodedGraph,
        ];
    }

    public static function getCurlDatas($url)
    {
        $clientDatas = new \GuzzleHttp\Client();

        try {
            // $this->logger->info(__CLASS__.__METHOD__.' > URL : '.$url);
            $responseDatas = $clientDatas->request('GET', $url);
            //code...
        } catch (ClientException $e) {
            $mess = 'ERROR : ';
            if (
                $e->getResponse()->getHeader('X-PlantUML-Diagram-Error')
                && ($e->getResponse()->getHeader('X-PlantUML-Diagram-Error-Line'))
            ) {
                $mess = 'Plantuml ERROR : '.$e->getResponse()->getHeader('X-PlantUML-Diagram-Error')[0].' on Line : '.$e->getResponse()->getHeader('X-PlantUML-Diagram-Error-Line')[0];
            }

            // $this->logger->error($mess.' - ERROR > getDatas > '.json_encode($e));
            //throw $th;
            throw new Exception($mess);
        }
        //$this->logger->warning($url.' : '.$responseDatas->getStatusCode());

        if (200 != $responseDatas->getStatusCode()) {
            // $this->logger->error($url.' : '.$responseDatas->getStatusCode());
            // die('superman');
        }

        // echo $responseDatas->getStatusCode(); // 200
        // echo $responseDatas->getHeaderLine('content-type'); // 'application/json; charset=utf8'
        $datas = $responseDatas->getBody();
        // $this->logger->debug($responseDatas->getBody());

        return $datas;
    }

    public static function saveInFile($filePath, $datas, $ext)
    {
        return file_put_contents($filePath.'.'.$ext, $datas);
    }
}
