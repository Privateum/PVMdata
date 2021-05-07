<?php
/*
 * This file is part of the PRIVATEUM INITIATIVE open source scripts package.
 *
 * (c) Arsen Khachatryan <info@privateum.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


    header("Content-type: text/plain; charset=utf-8");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);




if (isset($_REQUEST["query"]))

{

	$apikey=""; //Before deploying your own copy, get your own API token from Binance Smart Chain and update the $apikey value accordingly.

	$sss=file_get_contents("https://www.privateum.org/richlist.txt");
	$sss=explode("\n",$sss);	

$ts=json_decode(file_get_contents("https://api.bscscan.com/api?module=stats&action=tokensupply&contractaddress=0x71aff23750db1f4edbe32c942157a478349035b2&apikey=".$apikey),true);
$ts["name"]="Total Supply Amount";
$ts["contract_address"]="0x71aff23750db1f4edbe32c942157a478349035b2";

$csr=gmp_init($ts["result"]);

$i=0;
$rl=array();
$reply[]=$ts;
$lsss1=array();

foreach ($sss as $value)

	{
	$lsss=explode(":",$value);
	
	$rl[$i]["name"]=$lsss[0];
	$rl[$i]["address"]=$lsss[1];
	

	$lsss1[$i]=json_decode(file_get_contents("https://api.bscscan.com/api?module=account&action=tokenbalance&contractaddress=0x71aff23750db1f4edbe32c942157a478349035b2&address=".$lsss[1]."&tag=latest&apikey=".$apikey),true);
	
	foreach ($lsss1[$i] as $key =>$data)
	{
		$rl[$i][$key]=$data;
	};
	
	$csr=gmp_sub($csr,gmp_init($rl[$i]["result"]));
	
	$reply[]=$rl[$i];
	
	$i++;
	};

$cs= array ("status" =>"1", "message" => "OK");
$cs["result"]=gmp_strval($csr);
$cs["name"] ="PVM Market Circulation Amount";

$reply[]=$cs;

$shortform=0;

if (isset($_REQUEST["shortform"])) 
{
	$shortform=1;
};

$query=strtoupper($_REQUEST["query"]);

	switch ($query)
	{

		case "TOTALSUPPLY": 
			if ($shortform==0)
				{echo json_encode($ts);}
			else
				{echo substr($ts["result"],0,strlen($ts["result"])-18 ).".".substr($ts["result"],-18);};
			break;

		case "RICHLIST":
			
			if ($shortform==0)
				{echo json_encode($rl);}
			else
				{
				
				echo json_encode(array($rl[0]["address"],$rl[1]["address"],$rl[2]["address"]));
				};
			break;

		case "CIRCULATION":

			if ($shortform==0)
				{echo json_encode($cs);}
			else
				{echo substr($cs["result"],0,strlen($cs["result"])-18 ).".".substr($cs["result"],-18);};
			break;

		default: 
			echo json_encode($reply);

	};



}
else
{

echo "Use switches '?query=' to access appropriate data:\r\n";
echo "TOTALSUPPLY switch for total supply amount;\r\n";
echo "RICHLIST switch for all the wallets currently belonging to PRIVATEUM(PVM);\r\n";
echo "CIRCULATION switch for total amount, circulating in open market at the momentum of request.\r\n";
echo "\r\nAdding \"&shortform=on\" after the query switch will provide only numeric/address data.\r\n\r\n";
echo "Full form amounts are provided in Binance Smart Chain native format, e.g. as big integer, denoting upmost right 18 characters the fractional part.\r\n\r\n";
echo "Circulation amount = Total Supply - Amount of tokens from Rich List Addresses\r\n\r\n\r\n";
echo "Full list of currently supported call options:\r\n\r\n";
echo $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?query=TOTALSUPPLY\r\n";
echo $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?query=TOTALSUPPLY&shortform=on\r\n";
echo $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?query=RICHLIST\r\n";
echo $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?query=RICHLIST&shortform=on\r\n";
echo $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?query=CIRCULATION\r\n";
echo $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?query=CIRCULATION&shortform=on\r\n";
echo $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?query=default\r\n";

};


?>