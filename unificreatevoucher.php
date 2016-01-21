<?php
/*------------------------------------------------------------------------
January 2016
To suit Version 4.x.x of Unifi Controller, will NOT work on 3.x.x i dont think, not tried.
Version 4.0 of Unifi Easy Voucher Creator, written by Wozzzzz from UBNT UniFi Forums.
Change these 3 variables below, line 33-40, to suit your unifi setup
That is it, all you have to do, then run the script, dont rename it else it wont work.
This is an easy to use script for any front desk staff to creat vouchers instantly for any guests who want to use the internet.
No passwords needed to login, not good for security though but easy and safe in my setup any way. i have added security in a way that
if you set it up on your local server if anyone else tries to acess it the script will not work, lines 19-22
Logo escposlogo.png at top of voucher is maximum 384 pixels wide 
------------------------------------------------------------------------
*/
	
//This line will allow access to this script ONLY from the IP address specified else it will not work.
//Change this IP address if you will use this script from another computer and not the local machine the webserver is installed on.
//Uncomment these 4 lines to implement the security function

//if($_SERVER["REMOTE_ADDR"] != "127.0.0.1"){
//	echo"You do not have access to this script.";
// 	exit();
//}

	require("unifi/phpapi/class.unifi.php");

	session_start();
	$time_start = microtime(true);
	
	/*
	assign variables required later on  together with their default values
	*/

	$siteid = 'default'; 										//The name of your site
	$username = 'username';								//username for controller
	$password = 'password';								//Password for controller
	$baseurl = 'https://127.0.0.1:8443';	//URL to access controller
	$controllerversion = "4.7.6";						//Version of controller
	$ssid = "SSID";													//SSID you want people to connect to with this ticket
	$wpapsk = "WPA";												//Security password for the SSID above
	$printername = "VoucherPrinter";				//Name of the printer from PC
	


	$unifidata = new unifiapi($username, $password, $baseurl, $siteid, $controllerversion);
	$loginresults = $unifidata->login();
	if($loginresults === 400) {
	    echo '<div class="alert alert-danger" role="alert">HTTP response status: 400<br>This is probably caused by a Unifi controller login failure, please check your credentials in config.php</div>';
	}


////////////////////////////////////
//
//You shouldn't need to change anything under this line unless you want to customize the script.
//
////////////////////////////////////

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>UniFi Easy Voucher Creator</title>
<style type="text/css">
body {
	background-color: #D3D3D3;
	font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", serif;
	text-align: center;
	font-size: 13px;
}
</style>
</head>

<body>

<table width="588" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center"><span style="text-align: center; color: #0814EC; font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif; font-size: 25px; font-weight: bolder; text-shadow: 3px 3px #8E8E8E; -webkit-box-shadow: 5px 5px 3px 5px #8A8A8A; box-shadow: 5px 5px 3px 5px #8A8A8A;">UniFi Easy Voucher Creator V4.0 by Wozzzzz</span></td>
    </tr>
  </tbody>
</table>

<?php
if(($_POST["posted"] == "posted") && ($_POST['length'] > 0)){


	 //work out stuff
	 $voucherlength = $_POST['length'] * $_POST['units'];

		switch($_POST['units']){
			
			case 1 : 
				if($_POST['length'] > 1){ 
					$unitlength = "Minutes"; 
				} else {
					$unitlength = "Minute";
				}
				break;
			case 60 :
				if($_POST['length'] > 1){ 
					$unitlength = "Hours"; 
				} else {
					$unitlength = "Hour";
				}
				break;
			case 1440 :
				if($_POST['length'] > 1){ 
					$unitlength = "Days"; 
				} else {
					$unitlength = "Day";
				}
				break;			
		}

				if(($_POST['download'] == 0)||($_POST['download'] == "")){
					$downloadspeed = "No Limit";
					$down = "";
				}else{
					$downloadspeed = $_POST['download'];
					$down = $_POST['download'];
				}
				if(($_POST['upload'] == 0)||($_POST['upload'] == "")){
					$uploadspeed = "No Limit";
					$up = "";
				}else{
					$uploadspeed = $_POST['upload'];
					$up = $_POST['upload'];
				}
				if(($_POST['quota'] == 0)||($_POST['quota'] == "")){
					$quota = "No Limit";
					$mb = "";
				}else{
					$quota = $_POST['quota'];
					$mb = $_POST['quota'];
				}

	 $vouchers = $unifidata->create_voucher($voucherlength, $_POST['number'], $_POST['notes'], $up, $down, $mb);


?>
				
				<br><br><br><br>
<table width="896" border="1" align="center" cellpadding="5">
				  <tbody>
				    <tr>
				      <td width="166" align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Code</td>
				      <td width="160" align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Create Time</td>
				      <td width="225" align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Note</td>
				      <td width="105" align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Duration</td>
							<td width="80" align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Download</td>
							<td width="80" align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Upload</td>
							<td width="80" align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Quota</td>
				    </tr>
				    
		
		<?php
			
			$voucherstring = "";
			foreach($vouchers as $data){	
				$voucherstring .= $data."\n";

		?>
						
						<tr>
				      <td align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;"><?php echo $data ?></td>
				      <td align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;"><?php echo date('d-m-Y H:i:s',time()) ?></td>
				      <td align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;"><?php echo $_POST['notes'] ?></td>
				      <td align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;"><?php echo $_POST['length']." ".$unitlength ?></td>
				      <td align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;"><?php echo $downloadspeed ?> kbps</td>
				      <td align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;"><?php echo $uploadspeed ?> kbps</td>
				      <td align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;"><?php echo $quota ?> MB</td>
						</tr>
						
		<?php
		}
	
		//send info to printer for printing vouchers.
		
		require_once(dirname(__FILE__) . "/escpos/Escpos.php");
		$connector = null;
		$connector = new WindowsPrintConnector($printername);
		
		$printer = new Escpos($connector);
		
		/* Initialize Printer */
		$printer -> initialize();
		$printer -> setJustification(Escpos::JUSTIFY_CENTER);		
		
		$printer -> setTextSize(2, 2);
		$printer -> text("WIFI HOTSPOT\n");

		$logo = new EscposImage("escpos/escposlogo.png");
		$printer -> bitImage($logo);
	
		$printer -> setTextSize(1, 1);
		$printer -> text("\nNOTE: Each code valid for one\n");
		$printer -> text("device only.\n\n");
		$printer -> text("Download Speed ".$downloadspeed." kbps\n");
		$printer -> text("Upload Speed ".$uploadspeed." kbps\n");
		$printer -> text("Quota ".$quota." MB\n");
		$printer -> text("\n");
		
		$printer -> setTextSize(2, 2);
		$printer -> text($voucherstring."\n");
		$printer -> text("Usable One Time\n\n");
		
		$printer -> setTextSize(1, 2);
		$printer -> text("Valid For ".$_POST['length']." ".$unitlength." From Login\n\n");

		$printer -> setTextSize(1, 1);
		$printer -> text("Connect to Hotspot\n");
		$printer -> text("'".$ssid."'\n");
		$printer -> text("WPA-PSK Password: ".$wpapsk."\n");
		
		$printer -> text("\n\n\n\n\n");
		
		$printer -> cut();
		$printer -> close();		
		
		
		?>
				
				    
				    <tr>
				      <td colspan="7" align="center"><a href="<?php echo basename($_SERVER['SCRIPT_FILENAME']) ?>">Create Another Voucher</a></td>
				    </tr>
				  </tbody>
				</table>
				
<?php }else{ ?>

<form id="form1" name="form1" method="post">
<br>

<br>
<br><br>
<table width="780" border="1" align="center" cellpadding="5">
  <tbody>
    <tr>
      <td width="194" align="right" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Create how many vouchers?</td>
      <td width="554" align="left"><select name="number" id="number">
        <option value="1" selected>1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>        
      </select></td>
    </tr>
    <tr>
      <td align="right" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">For how long?</td>
      <td align="left"><input name="length" type="text" id="length" size="9" maxlength="5">
        <select name="units" id="units">
          <option value="1440">Day</option>
          <option value="60" selected>Hour</option>
          <option value="1">Minute</option>
      </select></td>
    </tr>
    <tr>
      <td align="right" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Notes</td>
      <td align="left"><input name="notes" type="text" id="notes" size="30"></td>
    </tr>
    <tr>
      <td align="right" valign="top" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Bandwidth Limit (Download)</td>
      <td align="left">
      	<input name="download" type="text" id="download" size="5">kbps, 0 = Maximum
</td>

    </tr>
        <tr>
      <td align="right" valign="top" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Bandwidth Limit (Upload)</td>
      <td align="left">
      	<input name="upload" type="text" id="upload" size="5">kbps, 0 = Maximum
</td>

    </tr>
            <tr>
      <td align="right" valign="top" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">Byte Quota</td>
      <td align="left">
      	<input name="quota" type="text" id="quota" size="5">MBytes, 0 = No Limit
</td>

    </tr>
    <tr>
      <td colspan="2" align="center" style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif;">
      	<input type="hidden" name="posted" value="posted">
      	<input name="Submit" type="submit" id="Submit" formaction="<?php echo basename($_SERVER['SCRIPT_FILENAME']) ?>" formmethod="POST" formtarget="_self" value="Create Voucher"></td>
    </tr>
 <?php
 if((($_POST["posted"] == "posted") && ($_POST['length'] == ""))||(($_POST["posted"] == "posted")&&($_POST['length'] <= 0))){
 	?>
     <tr>
      <td colspan="2" align="center">
      <strong style="color: #FF0004;">ERROR:</strong> Enter a length for the voucher. </td>
    </tr>	

<?php } ?>
  </tbody>
</table>
</form>

<?php } 


?>

</body>
</html>
