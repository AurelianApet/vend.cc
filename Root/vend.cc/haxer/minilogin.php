				<div id="cards">
					<div class="section_title">USER LOGIN</div>
					<div class="section_content">
						<table class="content_table" style="border:none;">
							<tbody>
								<form name="login" method="post" action="./login.php">
									<tr>
										<td align="center">
											<table class="borderstyle">
												<tbody>
													<tr>
														<td class="centered">
															Username:
														</td>
														<td class="centered">
															<input name="txtUser" type="text" class="formstyle" id="txtUser" tabindex="1">
														</td>
														<td class="centered" rowspan="2">
															<input name="remember" type="checkbox" class="formstyle" id="remember" <?php if ($remember) echo "checked ";?> tabindex="4"/> Remember
														</td>
													</tr>
													<tr>
														<td class="centered">
															Password:
														</td>
														<td class="centered">
															<input name="txtPass" type="password" class="formstyle" id="txtPass" tabindex="2">
														</td>
													</tr>
													<tr>
														<td class="centered">
															<img src="../captcha.php?width=100&height=40&characters=5" width="100px" height="40px" />
														</td>
														<td class="centered">
															<input name="security_code" type="text" class="formstyle" id="security_code" maxlength="5" autocomplete="off" tabindex="3">
														</td>
														<td class="centered">
															<input name="btnLogin" type="submit" class="formstyle" id="btnLogin" value="Login" style="width:80px;" tabindex="5"/>
														</td>
													</tr>
													<tr>
														<td colspan="3" class="error">
															<?php
																switch ($loginError) {
																	case -1:
																		echo "Sorry, you have provided an invalid security code.";
																		break;
																	case 1:
																		echo "This username doesn't exist.";
																		break;
																	case 2:
																		echo "Wrong password.";
																		break;
																	case 3:
																		echo "You don't have permission to log in to this page.";
																		break;
																	case 4:
																		echo "You aren't a seller to log in to this page.";
																		break;
																}
															?>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>