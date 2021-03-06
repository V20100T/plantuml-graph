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

    /**
     * Proxy setting :
     *   guzzle proxy
     *    http://docs.guzzlephp.org/en/latest/request-options.html#proxy.
     */
    public static function getCurlDatas($url, $proxy = [])
    {
        // Handle proxy settings
        $proxySettings = [];
        if ($proxy && is_array($proxy)) {
            $proxySettings = [
                'proxy' => $proxy,
            ];
        }

        $clientDatas = new \GuzzleHttp\Client();

        try {
            $responseDatas = $clientDatas->request('GET', $url, $proxySettings);
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
