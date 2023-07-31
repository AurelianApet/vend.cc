				<div class="box-login">
						<div class="box-title" style="margin:auto;">
									<span class="error">
											<?php
											$loginError = $user->login_error;
												switch ($loginError) {
													/*case -1:
														echo "Sorry, you have provided an invalid security code.";
														break;*/
													case 1:
														echo "This username / password doesn't exist.";
														break;

													case 3:
														echo "You don't have permission to log in to this page.";
														break;
													case 4:
														echo "You aren't a seller to log in to this page.";
														break;
													case 5:
														echo "You account haven't confirmed yet.<br />Please confirm your email address to use our services.<br /><a href='./confirm.php'>Click here</a> to resend the confirmation code.";
														break;
												}
											?>
									</span>
						</div>
						
						<form name="login" method="post" action="" class="form-login">
								<fieldset>
									<div class="form-group">
										<span class="input-icon">
											<input type="text" class="form-control" name="txtUser" placeholder="Username" value="<?php echo $_POST['txtUser']?>">
											<i class="fa fa-user"></i> </span>
									</div>
									<div class="form-group form-actions">
										<span class="input-icon">
											<input type="password" class="form-control password" name="txtPass" placeholder="Password">
											<i class="fa fa-lock"></i>
											<a class="forgot" href="./forgot.php">
												I forgot my password
											</a> </span>
									</div>
									<!--
									<div class="form-group">
										<img src="./captcha.php?width=100&height=40&characters=5" width="100px" height="40px" />
										<span class="input-icon" style="width:200px; float: right;">
											<input type="text" class="form-control" name="security_code" maxlength="5" autocomplete="off" >
											<i class="fa fa-key"></i> </span>
									</div>
									-->
									<div class="form-actions"><div class="slideExpandUp">
										<button type="submit" class="btn btn-default btn-primary btn-login pull-right" name="btnLogin" id="btnLogin" value="Login">
											Login <i class="fa fa-arrow-circle-right"></i>
										</button></div>
									</div>
									<div class="new-account">
										Don't have an account yet?
										<a href="./register.php" class="register">
											Create an account
										</a>
									</div>
								</fieldset>
    				</form>
				</div>
		        </div>

	 	        <!--  end login-inner -->
		        <div class="clear"></div><br>
		        
</div>
<!--<table border="0" width="80%">
	<tr>
		<td><img class="footer-west" src="http://westsiders.net/demo/demo64/images/credit-cards-caae.png"></td>
		<td>
		<p align="left"><a href="ymsgr:sendIM?ug.ghost"><img class="footer-west" src="http://www.onlinehomeworksite.com/images/yahoo_chat.png"></a></td>
	</tr>
</table><br> -->

 	<!--  end loginbox -->
</div>
<!-- End: login-holder -->				