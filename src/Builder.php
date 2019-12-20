<?php

namespace v20100t\PlantumlGraph;

use function Jawira\PlantUml\encodep;

class Builder
{
    public $header = "@startuml \n";
    public $close = "} \n";
    public $body;
    public $graph;
    public $footer = '@enduml'."\n";
    public $config = [];
    public $encoded = false;

    public function __construct($graphHeader, $graphSkinParam, $graphFooter, $graphIconsBaseUrl = 'https://raw.githubusercontent.com/tupadr3/plantuml-icon-font-sprites/v2.1.0')
    {
        $this->config['graphHeader'] = $graphHeader;
        $this->config['graphFooter'] = $graphFooter;
        $this->config['graphSkinParam'] = $graphSkinParam;
        $this->config['graphIconsBaseUrl'] = $graphIconsBaseUrl;
    }

    public function setHeader()
    {
        $this->header .= "header \n";
        $this->header .= $this->config['graphHeader']."\n";
        $this->header .= "endheader \n";

        self::addSkinParams();

        self::addIconOffice();
    }

    public function addSkinParams()
    {
        if ($this->config['graphSkinParam']) {
            $this->header .= $this->config['graphSkinParam'];

            return;
        }
        //Default skin param :

        $skinParams = "\n

        skinparam titleBorderRoundCorner 15
        skinparam titleBorderThickness 2
        skinparam titleBorderColor #de2768
        skinparam titleBackgroundColor #15bacf


        skinparam database{
            FontColor          white
            AttributeFontColor white
            FontSize           17
            AttributeFontSize  15
            AttributeFontname  Droid Sans Mono
            BackgroundColor    #949ba2
            BorderColor        black
            ArrowColor         #222266
        }

        skinparam component{
            FontColor          white
            AttributeFontColor white
            FontSize           17
            AttributeFontSize  15
            AttributeFontname  Droid Sans Mono
            BackgroundColor    #949ba2
            BorderColor        black
            ArrowColor         #222266
            LinkColor           #222266
        }

        skinparam package{
            FontColor          white
            AttributeFontColor white
            FontSize           17
            AttributeFontSize  15
            AttributeFontname  Droid Sans Mono
            BackgroundColor    #3c8dbc
            BorderColor        black
            ArrowColor         #222266
        }
        
        
        skinparam node{
            FontColor          #15bacf
            AttributeFontColor #15bacf
            FontSize           17
            AttributeFontSize  15
            AttributeFontname  Droid Sans Mono
            BackgroundColor    #efefef
            BorderColor        black
            ArrowColor         #222266
        } 
        
        \n";

        $this->header .= $skinParams;
    }

    public function addIconOffice()
    {
        $this->header .= '!define ICONURL '.$this->config['graphIconsBaseUrl'].'
        !includeurl ICONURL/common.puml
        !includeurl ICONURL/devicons/mysql.puml
        !includeurl ICONURL/font-awesome/database.puml'."\n";
    }

    public function addIco($toolType, $url)
    {
        $this->graph .= '!includeurl ICONURL/'.$toolType."/$url \n";
    }

    public function addLegend($txt, $position = 'top left')
    {
        $this->graph .= 'legend '.$position."\n";
        $this->graph .= $txt."\n";
        $this->graph .= "endlegend \n";
    }

    public function addNote($txt, $position = 'left', $cibleAlias = false)
    {
        $this->graph .= 'note '.$position;
        if ($cibleAlias) {
            $this->graph .= " of $cibleAlias";
        }
        $this->graph .= "\n";
        $this->graph .= '"'.$txt."\"\n";
        $this->graph .= "end note \n";
    }

    public function addTitle($title)
    {
        $this->graph .= 'title '.$title."\n";
    }

    /**
     * $shape :
     *     package.
            node
            folder
            frame
            cloud
            database
            storage
     */
    public function addGroup($title, $shape = 'package', $color = false)
    {
        if (!$shape) {
            $shape = 'package';
            //  Error is handled : Empty shape < $shape > for $title with color : $color ");
        }
        $this->graph .= $shape.' "'.$title.'"'." $color { \n";
    }

    /**
     * ex : https://github.com/tupadr3/plantuml-icon-font-sprites/blob/master/examples/complex-example.puml.
     *
     *
     * DEV_LINUX(debian,Linux,node){.
     *  FA_CLOCK_O(crond,crond) #White
     *  FA_FIRE(iptables,iptables) #White.
     *
     *  DEV_DOCKER(docker,docker,node)  {
     *      DEV_NGINX(nginx,nginx,node) #White
     */
    public function addGroupMacro($macro, $alias, $label = false, $shape = 'node', $color = false)
    {
        $macro = $macro.'('.$alias.'';
        if ($label) {
            $macro .= ','.$label.'';
        }
        if ($shape) {
            $macro .= ','.$shape.'';
        }

        $macro .= ' ) ';
        if ($color) {
            $macro .= $color;
        }

        $this->graph .= $macro." { \n";
    }

    //<prefix>_<name>(alias,label,shape,color)
    public function addMacro($macro, $alias, $label = '', $shape = false, $color = false)
    {
        $macro = $macro.'("'.$alias.'"';
        if ($label) {
            $macro .= ',"'.$label.'"';
        }
        if ($shape) {
            $macro .= ',"'.$shape.'"';
        }
        if ($color) {
            $macro .= ',"'.$color.'"';
        }

        $this->graph .= $macro." ) \n";
    }

    /**
     * actor actor.
        agent agent
        artifact artifact
        boundary boundary
        card card
        cloud cloud
        component component
        control control
        database database
        entity entity
        file file
        folder folder
        frame frame
        interface  interface
        node node
        package package
        queue queue
        stack stack
        rectangle rectangle
        storage storage
        usecase usecase
     */
    public function addItem($slug, $title, $ico = false, $shape = 'component', $color = '')
    {
        if (!$ico) {
            $this->graph .= $shape.' "'.$title.'" as '.$slug."\n";
        } else {
            //<prefix>_<name>(alias,label,shape,color)
            //@todo color
            $this->graph .= $ico.'("'.$slug.'", '.$title.', '.$shape.', '.$color.')'."\n";
        }
    }

    public function addFlow($slugTo, $slugFrom, $comment = '', $link = '<-->')
    {
        $this->graph .= $slugTo.' '.$link.' '.$slugFrom.' : '.$comment."\n";
    }

    public function addClose()
    {
        $this->graph .= $this->close;
    }

    public function addFooter()
    {
        $this->graph .= 'center footer '.$this->config['graphFooter'].' - '.date('Y-m-d H:i:s')."\n";
    }

    public function build()
    {
        self::addFooter();
        $this->graph = $this->header.$this->graph.$this->footer;
    }

    public function encode()
    {
        return $this->encoded = encodep($this->graph);
    }
}
