<!-- @component('mail::message')
# Introduction

The body of your message.

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent -->

<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta property="og:title" content="[CAMPAIGN_SUBJECT]"><meta property="og:type" content="website"><meta name="x-apple-disable-message-reformatting">
	<style type="text/css">td>p{margin: 0; padding-top: 1rem; padding-bottom: 1rem;} a{color: black; text-decoration:none;}
	</style>
	<title>[CAMPAIGN_SUBJECT]</title>
</head>
<body style="text-align: -webkit-center; font-family: sans-serif;">
<table style="margin: 0px auto;" width="100%">
	<tbody>
		<tr>
			<td>
			<div class="table-block-resizer react-draggable hide" style="touch-action: none; transform: translate(0px, 0px);">
			<div class="block-resizer-circle hide" draggable="true" style="top: 20px; display: none;"> </div>
			</div>

			<table class="header-table" data-bg-size="cover" height="60px" style="background-color: rgb(143, 36, 173); padding-top: 40px; padding-bottom: 40px;" width="100%">
				<tbody>
					<tr style="text-align: center;">
						<td id="template-header">
						<table style="margin: auto;" width="600">
							<tbody>
								<tr id="table-tr-withoutDisplay" style="min-height: 1px; text-align: center; padding: 2px 0px; width: 600px; margin-bottom: 0px; transition: all 0.2s ease 0s; top: 0px;">
								</tr>
								<tr id="table-tr-withoutDisplay" style="min-height: 1px; text-align: center; padding: 0px; width: 600px; margin-bottom: 0px; transition: all 0.2s ease 0s; top: 0px; background-color: transparent; font-weight: normal; font-style: normal;">
									<td draggable="true" id="exist-template-img-caption-1619522640026" style="display: block; text-align: center;" width="600">
									<table data-image-block-container="1" style="width: 100%">
										<tbody>
											<tr>
												<td align="center" data-image-block="1" style="background-color: transparent;"><img alt="Image Caption" src="https://getbonus.ca/mwiz/frontend/assets/files/customer/hk175k58kcb1e/logo_27-04-2021.png" style="width: 264px;" width="264" /></td>
											</tr>
										</tbody>
									</table>

									<table data-caption-block-container="1" style="width: 100%">
										<tbody>
											<tr>
												<td data-caption-block="1" width="264px"> </td>
											</tr>
										</tbody>
									</table>
									</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="table-block-resizer react-draggable hide" style="touch-action: none; transform: translate(0px, 0px);">
			<div class="block-resizer-circle hide" draggable="true" style="top: 3px; display: none;"> </div>
			</div>

			<table class="preview-table" data-bg-size="cover" height="60px" style="background-color: rgb(255, 255, 255); padding-top: 23px; padding-bottom: 23px;" width="100%">
				<tbody>
					<tr style="text-align: center;">
						<td id="template-body">
						<table style="margin: auto;" width="600">
							<tbody>
								<tr id="table-tr-withoutDisplay" style="min-height: 1px; text-align: center; padding: 2px 0px; width: 600px; margin-bottom: 0px; transition: all 0.2s ease 0s; top: 0px;">
								</tr>
								<tr id="table-tr-withoutDisplay" style="min-height: 1px; text-align: center; padding: 0px; width: 600px; margin-bottom: 0px; transition: all 0.2s ease 0s; top: 0px; background-color: transparent; font-weight: normal; font-style: normal;">
									<td draggable="true" id="exist-template-img-1619523349322" style="display: block; text-align: center; margin-bottom: 0px;">
									<table cellspacing="15" classname="image-block-wrap" style="width: 100%; border-spacing: 15px; pointer-events: none;">
										<tbody>
											<tr style="margin-bottom: 0px;">
												<td style="background-color: transparent; padding: 0px; margin-bottom: 0px;"><img alt="img" src="https://getbonus.ca/mwiz/frontend/assets/files/customer/hk175k58kcb1e/email.jpg" style="max-width: 560px;" width="560" /></td>
											</tr>
										</tbody>
									</table>
									</td>
								</tr>
								<tr id="table-tr-withoutDisplay" style="min-height: 1px; text-align: center; padding: 0px; width: 600px; margin-bottom: 0px; transition: all 0.2s ease 0s; top: 0px; background-color: rgb(255, 255, 255); font-weight: normal; font-style: normal; outline: rgb(164, 0, 247) none 1px;">
									<td draggable="true" id="exist-template-text-boxed-1619523376797" style="display: block; text-align: center; color: rgb(0, 0, 0); padding-left: 8px; padding-right: 8px; background-color: rgb(255, 255, 255); outline: rgb(164, 0, 247) none 1px; margin-bottom: 0px;">
									<p style="pointer-events: none;"><strong><span style="font-size:28px;"><span style="margin-bottom: 0px;">HI {{$username}} !</user></span></span></strong><br />
									<br />
									<br />
									<span style="font-size: 18px; margin-bottom: 0px;">There has been an unsuccessful attempt to make a deposit to your account. The deposit has been declined. To find out the details, please get in touch with our customer support.</span><br />
									<br />
									<br />
									 </p>
									</td>
								</tr>
								<tr id="table-tr-withoutDisplay" style="min-height: 1px; text-align: center; padding: 0px; width: 600px; margin-bottom: 0px; transition: all 0.2s ease 0s; top: 0px; background-color: transparent; font-weight: bold; font-style: normal;">
									<td draggable="true" id="exist-template-button-1619523575856" style="display: inline-block; text-align: center; color: rgb(255, 255, 255); font-weight: bold; width: 100%; font-size: 24px; margin-bottom: 0px;">
									<table align="center" cellpadding="0" cellspacing="0" data-button-type="web" style="margin: 5px auto; width: initial;">
										<tbody>
											<tr>
												<td bgcolor="#10a329" style="color: rgb(255, 255, 255); text-align: center; padding: 15px; background-color: rgb(16, 163, 41); border-radius: 3px; display: block; border: none;"><a href="https://www.canada777.com" style="color: rgb(255, 255, 255); line-height: 100%; width: 100%; display: inline-block; text-decoration: none !important; pointer-events: auto;" target="_blank">GO TO WEBSITE</a></td>
											</tr>
										</tbody>
									</table>
									</td>
								</tr>
								<tr id="table-tr-withoutDisplay" style="min-height: 1px; text-align: center; padding: 0px; width: 600px; margin-bottom: 0px; transition: all 0.2s ease 0s; top: 0px; background-color: transparent; font-weight: normal; font-style: normal;">
									<td draggable="true" id="exist-template-footer-1619523858026" style="display: block; text-align: center; color: rgb(255, 255, 255);">
									<p style="text-align: center; pointer-events: none;"> </p>

									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
										<tbody>
											<tr style="margin-bottom: 0px;">
												<td align="center" style="margin-bottom: 0px;"><a href="mailto:support@Playamo.com" target="_blank">support@canada777.com</a></td>
											</tr>
											<tr style="margin-bottom: 0px;">
												<td align="center" style="margin-bottom: 0px;"><a data-saferedirecturl="https://www.google.com/url?q=https://email.playamomails.com/e/c/eyJlbWFpbF9pZCI6ImRnUGo4d0xqOHdJQkFBRjNzczljZHRQMmNaNTNJd1JJODE0PSIsImhyZWYiOiJodHRwczovL3d3dy5yZWdpc3RlcmFtby5jb20vIiwibGlua19pZCI6NjUwNDUwNTcsInBvc2l0aW9uIjozfQ/fdd051d32764d7daf75e1cbe3a2ea3f6ece5639f7f19fbd8c36fb2c77e46175a&source=gmail&ust=1619608950529000&usg=AFQjCNEqJkvPG_CvgIE5YzlyC9Nkj8ct8g" href="https://email.playamomails.com/e/c/eyJlbWFpbF9pZCI6ImRnUGo4d0xqOHdJQkFBRjNzczljZHRQMmNaNTNJd1JJODE0PSIsImhyZWYiOiJodHRwczovL3d3dy5yZWdpc3RlcmFtby5jb20vIiwibGlua19pZCI6NjUwNDUwNTcsInBvc2l0aW9uIjozfQ/fdd051d32764d7daf75e1cbe3a2ea3f6ece5639f7f19fbd8c36fb2c77e46175a" target="_blank">live chat 24/7</a></td>
											</tr>
										</tbody>
									</table>
									</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="table-block-resizer hide react-draggable" style="touch-action: none; transform: translate(0px, 0px);">
			<div class="block-resizer-circle hide" draggable="true" style="top: 4px; display: none;"> </div>
			</div>

			<table class="footer-table" data-bg-size="cover" height="70px" style="background-color: rgb(0, 0, 0); padding-top: 24px; padding-bottom: 24px;" width="100%">
				<tbody>
					<tr style="text-align: center; margin: auto;">
						<td class="template-footer">
						<table style="margin: auto;" width="600">
							<tbody>
								<tr id="table-tr-withoutDisplay" style="min-height: 1px; text-align: center; padding: 2px 0px; width: 600px; margin-bottom: 0px; transition: all 0.2s ease 0s; top: 0px;">
								</tr>
								<tr id="table-tr-withoutDisplay" style="min-height: 1px; text-align: center; padding: 0px; width: 600px; margin-bottom: 0px; transition: all 0.2s ease 0s; top: 0px; background-color: transparent; font-weight: normal; font-style: normal;">
									<td draggable="true" id="exist-template-img-1619523989561" style="display: block; text-align: center; margin-bottom: 0px;">
									<table cellspacing="15" classname="image-block-wrap" style="width: 100%; text-align: center; border-spacing: 15px; pointer-events: none;">
										<tbody>
											<tr>
												<td style="background-color: transparent; padding: 0px;"><img alt="img" src="https://getbonus.ca/mwiz/frontend/assets/files/customer/hk175k58kcb1e/logo_27-04-2021.png" style="max-width: 560px; width: 250px; pointer-events: none;" width="250" /></td>
											</tr>
										</tbody>
									</table>
									</td>
								</tr>
								<tr id="table-tr-withoutDisplay" style="min-height: 1px; text-align: center; padding: 0px; width: 600px; margin-bottom: 0px; transition: all 0.2s ease 0s; top: 0px; background-color: transparent; font-weight: normal; font-style: normal;">
									<td draggable="true" id="exist-template-text-1619524038058" style="display: block; text-align: center;">
									<p><span style="color:#ffffff;"><span style="font-size:18px;">www.canada777.com</span></span></p>
									</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
</body>
</html>
