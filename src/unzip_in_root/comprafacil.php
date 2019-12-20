<?php
// Version
define('VERSION', '1.5.3.1');

// Configuration
require_once('config.php');
   
// Install 
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Application Classes
require_once(DIR_SYSTEM . 'library/customer.php');
require_once(DIR_SYSTEM . 'library/affiliate.php');
require_once(DIR_SYSTEM . 'library/currency.php');
require_once(DIR_SYSTEM . 'library/tax.php');
require_once(DIR_SYSTEM . 'library/weight.php');
require_once(DIR_SYSTEM . 'library/length.php');
require_once(DIR_SYSTEM . 'library/cart.php');

require_once(DIR_APPLICATION . 'controller/payment/comprafacil.php');


$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

// Database 
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0' OR store_id = '" . (int)$config->get('config_store_id') . "' ORDER BY store_id ASC");

foreach ($query->rows as $setting) {
	if (!$setting['serialized']) {
		$config->set($setting['key'], $setting['value']);
	} else {
		$config->set($setting['key'], unserialize($setting['value']));
	}
}

if (!$_REQUEST['order']){
	echo 'error';
	die();
}

$cfLog = $db->query("SELECT * FROM comprafacil WHERE `key` = '" . $_REQUEST['order'] . "'");

if ($cfLog->row['status'] == 1){
    echo 'error';
    die();
}
if (!$cfLog->row['reference']){
    echo 'error';
    die();
}

//VERIFY PAY - BEGIN
    $username = $config->get('cf_username');
    $password = $config->get('cf_password');
    $debugMode = $config->get('cf_mode');

    if (!class_exists('soapclient')){
    	 require_once DIR_CATALOG.'\includes\nusoap\lib\nusoap.php';
         $action='http://hm.comprafacil.pt/SIBSClick2/webservice/getInfoCompra';
         if($debugMode == true){
         $serverpath ='https://hm.comprafacil.pt/SIBSClick2TESTE/webservice/clicksmsV4.asmx';
         }else{
         $serverpath ='https://hm.comprafacil.pt/SIBSClick2/webservice/clicksmsV4.asmx';
         }

         $client = new soapclient($serverpath);

         $msg=$client->serializeEnvelope('<getInfoCompra xmlns="http://hm.comprafacil.pt/SIBSClick2/webservice/"><IDCliente>'.$username.'</IDCliente><password>'.$password.'</password><referencia>'.$cfLog->row["reference"].'</referencia></getInfoCompra>','',array(),'document', 'literal');

         $response = $client->send($msg,$action);

         if ($client->fault) {
             echo '<p>Fault</p><pre>';
             print_r($response);
             echo '</pre>';
         }
         
         $result=$response['getInfoCompraResult'];
         
         if($result == "true"){
             if($response['pago'] == true){
                 echo 'pago';
             }else{
                echo 'error';
			    die();
             }
         }
         else{
            echo 'error';
		    die();
         }
    }else{
       
        try 
         {
             if($debugMode == true){
             $wsURL = "https://hm.comprafacil.pt/SIBSClick2TESTE/webservice/ClicksmsV4.asmx?WSDL";
             }else{
             $wsURL = "https://hm.comprafacil.pt/SIBSClick2/webservice/ClicksmsV4.asmx?WSDL";
             }

             $parameters = array(
              
                 "IDCliente" => $username,
                 "password" => $password,
                 "referencia" => $cfLog->row["reference"]
                 );
             
             $client = new SoapClient($wsURL);
             $res = $client->getInfoCompra($parameters); 
             if ($res->getInfoCompraResult)
             {
                 if($res->pago == true){
                     echo 'pago';
                 }else{
                    echo 'error';
				    die();
                 }
             }
             else
             {
                echo 'error';
			    die();
             }
         }
         catch (Exception $e){
            echo 'error';
		    die();
         }
        
    }  
//VERIFY PAY - END

$db->query("UPDATE comprafacil SET status = 1 WHERE `key` = '" . $_REQUEST['order'] . "'");

$order_status_id = 2;
$order_id = $cfLog->row['orderID'];

$db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

$db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', date_added = NOW()");

?>