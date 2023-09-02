<html>
<head>
	<title><?=$_lang->val( 'user_registration_subject' )?></title>
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins" />
</head>
<body style="background-color: #FFF8F5">
	<table align="center" width="700" style="padding: 50px 50px 20px 50px">
		<tr>
			<td align="center">
				<!--<a href="<?//=$_app_domain?>/verify.php?code=<?//=$_code?>&lang=<?//=$_user_lang?>" target="_blank">	
				    <img src="<?//=$_email_manager->getHeaderImage( )?>" style="display: block; max-width:450px;" alt="<?//=$_email_manager->getCompanyName( )?>" />
				</a>-->
				&nbsp;
			</td>
		</tr>
	</table>
	<table bgcolor="#FFFFFF" align="center" width="700" height="400" style="padding: 50px 50px 50px 50px; border-radius: 10px">
		<tr>
			<td>
				<p style="text-align: center; font-size: 15px;color: #000938; font-family:'Poppins', 'Arial' , 'Serif';" > 
					<?=$_lang->val( 'pass_reset_txt_1' )?>
					<br><br>
					<?=$_lang->val( 'pass_reset_txt_2' )?>
				</p> 
			</td>
		</tr>
		<tr>
			<td align="center">
				<a href="<?=$_app_domain?>/reset-password/<?=$_code?>/<?=$_user_lang?>/"> 
					<button type="button" name="Verificar" value="<?=$_lang->val( 'reg_btn_txt' )?>" style="background-color: #6B75FF; border-radius: 5px; color: white; padding: 8px 20px 8px 20px; font-size:16px; border: none; text-align: center; cursor: pointer; font-family: 'Poppins', 'Arial' , 'Serif';">
						<?=$_lang->val( 'pass_reset_btn_txt' )?>
					</button> 
				</a>
			</td>
		</tr>
		<tr>
			<td align="center">
				<p style="line-height: 30px;text-align: center; font-size: 15px ; color:#9BABC5; font-family: 'Poppins', 'Arial' , 'Serif';">  
					<?=$_lang->val( 'email_user_registration_verify_3' )?>
					<br> 
					<a href="<?=$_app_domain?>/reset-password/<?=$_code?>/<?=$_user_lang?>/" target="_blank" style="text-align: center; text-decoration: underline; font-size: 15px ; color:#6B75FF ; a:active color:#1155CC; a:hover cursor: pointer; a:visited color:#6611CC; font-family:'Poppins', 'Arial' , 'Serif';"> 
						<?=$_app_domain?>/reset-password/<?=$_code?>/<?=$_user_lang?>/
					</a> 
				</p>
			</td>
		</tr>
	</table>
	<table align="center" width="700" style="padding: 20px 50px 10px 50px">
		<!--<tr>
			<td align="center">
				<a href="<?=$_app_domain?>" target="_blank">	
					<p style="text-align: center"> 
						<img width="200px" src="<?//=$_email_manager->getHeaderImage( )?>" alt="<?=$_company_name?>" /> 
					</p> 
				</a>
			</td>
		</tr>-->
		<tr>
			<td align="center">
				<p style="font-size:13px; line-height: 20px; color: #000938; font-family: 'Poppins', 'Arial' , 'Serif';"> 
					This email was sent to <?=$_user_email?><br>
					Copyright © <?=date( 'Y' )?> <?=$_company_name?>. All rights reserved.
				</p>
			</td>
		</tr>
	</table>
</body>
</html>