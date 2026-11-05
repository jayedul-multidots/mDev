<?php
// phpcs:ignoreFile
/**
 * Reset Password Email template.
 *
 * @package vaultrix
 */

function vaultrix_retrieve_password_mail_template( $message, $key, $user_login ) {
	$reset_link = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );

	$formatted_message = '
		<!DOCTYPE html>
		<html>
			<head>
				<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
				<title>' . esc_html( get_bloginfo( 'name', 'display' ) ) . '</title>
				<link rel="preconnect" href="https://fonts.googleapis.com">
				<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
				<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"> '; // phpcs:ignore 

		$formatted_message .= '</head>
			<body style="margin:0;">
				<table width="100%" id="outer_wrapper" style="font-family:\'Montserrat\',\'Avenir\',sans-serif;background:#f7f7f7">
					<tr>
						<td>
							<div id="wrapper" style="max-width:640px;width:100%;border-spacing:0;margin:auto">
								<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_container" style="border:0!important; border-radius:0!important;">
									<tr>
										<td align="center" valign="top">
											<div id="template_header_image" style="width:100%;height:auto;background-color:#001e4e;background-repeat:no-repeat;background-blend-mode:screen;background-image:url(' . esc_url( VAULTRIX_IMAGES_DIR_PATH . 'top-left-wave.webp' ) . ');background-position: top left;background-size: cover;">';
												$formatted_message .= '<a target="_blank" href="' . esc_url( home_url( '/' ) ) . '" style="padding: 100px 0;display:block">';
												$img                = get_field( 'header_logo' );
	if ( $img ) {
		$formatted_message .= '<p style="margin:0;"><img src="' . esc_url( $img ) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" /></p>';
	} else {
		$formatted_message .= '<p style="margin:0;"><img src="' . esc_url( VAULTRIX_IMAGES_DIR_PATH . 'Vaultrix_Logo_White.png' ) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" width="40" height="40" /></p>';
	}

												$formatted_message .= '</a>
											</div>
										</td>
									</tr>
									<tr>
										<td align="center" valign="top">
											<!-- Body -->
											<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body" style="font-family: \'Montserrat\',\'Avenir\', sans-serif;">
												<tr>
													<td valign="top" id="body_content" style="background:#fff;">
														<!-- Content -->
														<table border="0" cellpadding="20" cellspacing="0" width="100%">
															<tr>
																<td valign="top" style="padding:0 !important">
																	<div id="body_content_inner" style="font-family:\'Montserrat\',\'Avenir\', sans-serif;font-size:14px;font-weight: 400;line-height: 24px;padding: 40px">
																		<p style="margin:0 0 16px">' . __( 'Someone has requested a password reset for the following account:', 'vaultrix' ) . '</p>
																		<p style="margin:0 0 16px"><strong>Site Name: </strong>' . network_home_url( '/' ) . '</p>
																		<p style="margin:0 0 16px"><strong>Username: </strong>' . $user_login . '</p>
																		<p style="margin:0 0 16px">' . __( 'If this was a mistake, just ignore this email and nothing will happen.', 'vaultrix' ) . '</p>
																		<strong style="margin:0 0 16px">' . __( 'To reset your password, visit the following address:', 'vaultrix' ) . '</strong>
																		<p style="margin:0 0 16px">' . $reset_link . '</p>
																	</div>
																</td>
															</tr>
														</table>
														<!-- End Content -->
													</td>
												</tr>
											</table>
											<!-- End Body -->
										</td>
									</tr>
									<tr>
										<td align="center" valign="top">
											<!-- Footer -->
											<table border="0" cellpadding="10" cellspacing="0" width="100%" id="template_footer">
											<tbody>
											<tr>
												<td valign="top" style="padding:0;border-radius:6px">
													<table border="0" cellpadding="10" cellspacing="0" width="100%">
														<tbody>
															<tr>
																<td align="center" valign="middle" style="padding:0;border-radius:6px">
																	<table bgcolor="#ffffff" align="center" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;width:100%;">
																		<tbody>
																			<tr>
																				<td style="padding:0;border-radius:6px">
																					<table width="100%" border="0" cellspacing="0" cellpadding="0">
																						<tbody>
																							<tr>
																								<td style="padding:0;border-radius:6px">
																									<table bgcolor="#001e4e" width="100%" border="0" cellspacing="0" cellpadding="0">
																										<tbody>
																											<tr>
																												<td style="padding:0;border-radius:6px;width:40px" width="40">&nbsp;</td>
																												<td valign="top" style="padding:0;border-radius:6px">
																													<table width="100%" border="0" cellspacing="0" cellpadding="0">
																														<tbody>
																															<tr>
																																<td bgcolor="#ffffff" style="padding:0;border-radius:0">
																																	<table width="100%" border="0" cellspacing="0" cellpadding="0">
																																		<tbody>
																																			<tr>
																																				<td style="padding:0;border-radius:6px;width:40px" width="40">&nbsp;</td>
																																				<td valign="top" style="padding:0;border-radius:0">
																																					<table width="100%" border="0" cellspacing="0" cellpadding="0">
																																						<tbody>
																																							<tr>
																																								<td height="1" bgcolor="#e6e8e9" style="padding:0;border-radius:6px;line-height:1px;font-size:1px">&nbsp;</td>
																																							</tr>
																																							<tr>
																																								<td height="25" style="padding:0;border-radius:6px;line-height:1px;font-size:1px">&nbsp;</td>
																																							</tr>
																																							<tr>
																																								<td style="padding:0;border-radius:6px">
																																									<table width="100%" border="0" cellspacing="0" cellpadding="0">
																																										<tbody>
																																											<tr>
																																												<td valign="top" width="46%" style="padding:0;border-radius:6px">
																																													<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
																																														<tbody>
																																															<tr>
																																																<td style="padding:0;border-radius:6px">
																																																	<table width="100%" border="0" cellspacing="0" cellpadding="0">
																																																		<tbody>
																																																			<tr>
																																																				<td style="font-family:\'Montserrat\',\'Avenir\',sans-serif;font-size:12px;text-align:left;color:#ffffff99;line-height:30px;font-weight:400">
																																																					<a style="text-decoration:none;color:#636363" href="' . esc_url( get_site_url() . '/about/' ) . '" target="_blank" data-saferedirecturl="">About </a>
																																																					<br>
																																																					<a style="text-decoration:none;color:#636363;" href="' . esc_url( get_site_url() . '/contact-us/' ) . '" target="_blank" data-saferedirecturl="">Contact Us </a>
																																																					<br>
																																																					<a style="text-decoration:none;color:#636363;" href="' . esc_url( get_site_url() . '/faqs/' ) . '" target="_blank" data-saferedirecturl="">FAQ </a>
																																																				</td>
																																																			</tr>
																																																		</tbody>
																																																	</table>
																																																</td>
																																															</tr>
																																														</tbody>
																																													</table>
																																												</td>
																																												<td width="6%" style="padding:0;border-radius:6px">&nbsp;</td>
																																												<td valign="top" width="46%" style="padding:0;border-radius:6px">
																																													<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
																																														<tbody>
																																															<tr>
																																																<td style="padding:0;border-radius:6px">
																																																	<table width="100%" border="0" cellspacing="0" cellpadding="0">
																																																		<tbody>
																																																			<tr>
																																																				<td style="font-family:\'Montserrat\',\'Avenir\',sans-serif;font-size:12px;text-align:left;color:#ffffff99;line-height:30px;font-weight:400">
																																																					<a style="text-decoration:none;color:#636363" href="' . esc_url( get_site_url() . '/disk-disclaimer/' ) . '" target="_blank" data-saferedirecturl="">Disk Disclaimer </a>
																																																					<br>
																																																					<a style="text-decoration:none;color:#636363;" href="' . esc_url( get_site_url() . '/privacy-policy/' ) . '" target="_blank" data-saferedirecturl="">Term of Support </a>
																																																					<br>
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
																																								</td>
																																							</tr>
																																							<tr>
																																								<td height="30" style="padding:0;border-radius:6px;line-height:1px;font-size:1px">&nbsp;</td>
																																							</tr>
																																						</tbody>
																																					</table>
																																				</td>
																																				<td style="padding:0;border-radius:6px;width:40px" width="40">&nbsp;</td>
																																			</tr>
																																		</tbody>
																																	</table>
																																</td>
																															</tr>
																															<tr>
																																<td style="padding:0;border-radius:6px">
																																	<table width="100%" border="0" cellspacing="0" cellpadding="0">
																																		<tbody>
																																			<tr>
																																				<td height="40" style="padding:0;border-radius:6px;line-height:1px;font-size:1px">&nbsp;</td>
																																			</tr>
																																			<tr>
																																				<td style="font-family:\'Montserrat\',\'Avenir\',sans-serif;font-size:14px;text-align:center;color:#b8b8b8;line-height:24px">Follow us on Social Media</td>
																																			</tr>
																																			<tr>
																																				<td height="20" style="padding:0;border-radius:6px;line-height:1px;font-size:1px">&nbsp;</td>
																																			</tr>
																																			<tr>
																																				<td style="padding:0;border-radius:6px">
																																					<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:140px" width="140">
																																						<tbody>
																																							<tr>';

	while ( have_rows( 'social_icons_group', 'options' ) ) :
		the_row();
		if ( have_rows( 'footer_social_icons' ) ) {
			while ( have_rows( 'footer_social_icons' ) ) {
				the_row();
				$social_type = get_sub_field( 'social_type' );
				$social_link = get_sub_field( 'social_link' );
				$link_url    = ! empty( $social_link['url'] ) ? $social_link['url'] : 'javascript:void(0)';

				if ( $social_type === 'instagram' ) {
					$formatted_message .= '<td height="21" style="width:21px"><a href="' . esc_url( $link_url ) . '" style="color:#ffffff" target="_blank" data-saferedirecturl=""><img src="' . esc_url( VAULTRIX_IMAGES_DIR_PATH . 'instagram.png' ) . '" width="21" height="21" style="max-width:21px;display:block;border:none;margin:auto" border="0" alt="in" class="CToWUd" data-bit="iit"></a></td>';
				} elseif ( $social_type === 'facebook' ) {
					$formatted_message .= '<td height="21" style="width:21px"><a href="' . esc_url( $link_url ) . '" style="color:#ffffff" target="_blank" data-saferedirecturl=""><img src="' . esc_url( VAULTRIX_IMAGES_DIR_PATH . 'facebook.png' ) . '" width="21" height="21" style="max-width:21px;display:block;border:none;margin:auto" border="0" alt="fb" class="CToWUd" data-bit="iit"></a></td>';
				} elseif ( $social_type === 'twitter' ) {
					$formatted_message .= '<td height="21" style="width:21px"><a href="' . esc_url( $link_url ) . '" style="color:#ffffff" target="_blank" data-saferedirecturl=""><img src="' . esc_url( VAULTRIX_IMAGES_DIR_PATH . 'twitter.png' ) . '" width="21" height="21" style="max-width:21px;display:block;border:none;margin:auto" border="0" alt="tw" class="CToWUd" data-bit="iit"></a></td>';
				}
			}
		}
																																							endwhile;
																																						$formatted_message .= '</tr>
																																						</tbody>
																																					</table>
																																				</td>
																																			</tr>
																																			<tr>
																																				<td height="40" style="padding:0;border-radius:6px;line-height:1px;font-size:1px">&nbsp;</td>
																																			</tr>
																																			<tr>
																																				<td height="1" bgcolor="#333333" style="padding:0;border-radius:6px;line-height:1px;font-size:1px">&nbsp;</td>
																																			</tr>
																																		</tbody>
																																	</table>
																																</td>
																															</tr>
																															<tr>
																																<td style="padding:0;border-radius:6px">
																																	<table width="100%" border="0" cellspacing="0" cellpadding="0">
																																		<tbody>
																																			<tr>
																																				<td height="40" style="padding:0;border-radius:6px;line-height:1px;font-size:1px">&nbsp;</td>
																																			</tr>
																																			<tr>
																																				<td style="padding:0;border-radius:6px;font-family:\'Montserrat\',\'Avenir\',sans-serif;font-size:12px;text-align:left;color:#b8b8b8;line-height:24px" align="left">
																																					<p style="text-align:center" align="center">Copyright 2024 Vaultrix, All rights reserved.<br></p>
																																				</td>
																																			</tr>
																																			<tr>
																																				<td height="40" style="padding:0;border-radius:6px;line-height:1px;font-size:1px">&nbsp;</td>
																																			</tr>
																																		</tbody>
																																	</table>
																																</td>
																															</tr>
																														</tbody>
																													</table>
																												</td>
																												<td style="padding:0;border-radius:6px;width:40px" width="40">&nbsp;</td>
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
																</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
											</table>
											<!-- End Footer -->
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</body>
		</html>';

	return $formatted_message;
}
add_filter( 'retrieve_password_message', 'vaultrix_retrieve_password_mail_template', 20, 3 );

function vaultrix_reset_password_message( $defaults ) {
	$defaults['subject'] = __( 'Password Reset Request', 'vaultrix' );
	return $defaults;
}
add_filter( 'retrieve_password_notification_email', 'vaultrix_reset_password_message' );
