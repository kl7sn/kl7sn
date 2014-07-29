<?php  
  
/** 
 * @author mixmore	 
 * @copyright 2012 
 * php连接sql server数据库
 */  
  
$server   ='';  
$username ='';  
$password ='';  
$database =''; 
$connstr  = "Driver={SQL Server};Server=$server;Database=$database";
$conn     =odbc_connect($connstr,$username,$password,SQL_CUR_USE_ODBC);
if(!$conn)
{
    echo "connect failed";
}
