<?php
//Enable loading of classes under Folder/File.php if class-name is Folder_File
function __autoload($class)
{
    require_once str_replace("_", "/", $class) . ".php";
}

//Enable the Aspects (subscribe)
Demo_Logging::enableLogging();
Demo_Promotions::enablePromotion();
Demo_Security::disableLargeMoneyOperations();

//Load a Demo_Account, the logger should match newInstance and wrapInstance
$a = Aspect_Wrapper::load('Demo_Account', 32, 200);
$a->debit(100);

//Big Money Operations should return false
var_dump($a->debit(10000));
var_dump($a->credit(6000));

echo "\n";
$a->printInfo();	//No Changes in Account for big
echo "\n";

//Disable one aspect (unsubscribe)
Demo_Security::enableLargeMoneyOperations();
var_dump($a->debit(10000));
var_dump($a->credit(6000));

echo "\n";
$a->printInfo();	//Changes in account for big
echo "\n";
