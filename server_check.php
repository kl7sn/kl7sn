<?php


// include("Check.class.php");
// include("SoapDiscovery.class.php");
// //第一个参数是类名（生成的wsdl文件就是以它来命名的），即person类，第二个参数是服务的名字（这个可以随便写）。
// $disco = new SoapDiscovery('check','Check');
// $disco->getWSDL();



    include("check.class.php");
    $objSoapServer = new SoapServer("check.wsdl");//person.wsdl是刚创建的wsdl文件
    //$objSoapServer = new SoapServer("server.php?wsdl");//这样也行
    $objSoapServer->setClass("Check");//注册person类的所有方法
    $objSoapServer->handle();//处理请求