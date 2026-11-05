<?php
/**
 * Enqueue theme assets
 *
 * @package vaultrix
 */

namespace VAULTRIX\Inc;

use VAULTRIX\Inc\Traits\Singleton;

/**
 * Class Assets
 */
class Register_Shortcodes {
	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		// load class.
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function setup_hooks() {

		add_shortcode( 'dp-contact-form', array( $this, 'dp_contact_form_callback_function' ) );

		/**
		 * Actions.
		 */
		add_action( 'wp_ajax_dp_contact_form_submission', array( $this, 'dp_contact_form_submission' ) );
		add_action( 'wp_ajax_nopriv_dp_contact_form_submission', array( $this, 'dp_contact_form_submission' ) );

	}

	/**
	 * Callback function of contact form shortcode.
	 */
	public function dp_contact_form_callback_function( $atts = array(), $content = null ) {

		wp_enqueue_script( 'vaultrix-jquery-validation' );
		ob_start();
		?>
		<div class="vaultrix-contact-form-main">
			<form id="vaultrix-contact-form" class="vaultrix-contact-form" method="post" action="javascript:void(0)" enctype="multipart/form-data">
				<div class="vaultrix-contact-form__inner-wrapper">
					<div class="dp-form-field">
						<span class="dp-form-field-wrapper"> 
							<label for="name">First Name</label>
							<input 
								id="dp-contact-form-name"
								placeholder="First Name"
								class="dp-form-cmn-field"
								name="name" 
								type="text"
								aria-label="Name"
								autocomplete="off"
							/>
						</span>
					</div>

					<div class="dp-form-field">
						<span class="dp-form-field-wrapper">
							<label for="email">Email</label>
							<input 
								id="dp-contact-form-email"
								placeholder="Email"
								class="dp-form-cmn-field" 
								name="email"
								type="email" 
								aria-label="Email"
								autocomplete="off"
							/>
						</span>
					</div>
					
					<div class="dp-form-field">
						<span class="dp-form-field-wrapper">
							<label for="message">Message</label>
							<textarea 
								id="dp-contact-form-message"
								placeholder="Enter Your Message Here"
								class="dp-form-cmn-field" 
								name="message"
								aria-label="Message" 
								cols="40"
								rows="6.5"
							></textarea>
						</span>
					</div>

					<div class="dp-form-field">
						<span class="dp-form-field-wrapper form-submit submit-btn">
							<button 
								id="dp-contact-form-submit" 
								class="btn-main"
								name="submit"
								   type="submit"
							>Send Message</button>
							<input 
								id="dp-contact-form-recaptcha-secret-key"
								name="recaptcha-secret-key" 
								type="hidden"
								value = <?php echo esc_attr( VAULTRIX_RECAPTCHA_V3_SECRET_KEY ); ?>
							/>
							<input 
								id="dp-contact-form-token"
								name="token" 
								type="hidden"
							/>
							<input 
								type="hidden" 
								name="nonce_dp_contact_form"
								value="<?php echo esc_attr( wp_create_nonce( 'dp-contact-form-submission-nonce' ) ); ?>"
							/>
						</span>
					</div>

				</div>
			</form>
		</div>
		<?php
		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * Callback function of contact form submission.
	 */
	public function dp_contact_form_submission() {
		$result            = array();
		$result['success'] = 0;
		$result['message'] = '<div class="dp-contact-form-ajax-error">' . __( '', 'vaultrix' ) . '</div>';
		$result['data']    = '';
		$nonce             = filter_input( INPUT_POST, 'nonce_dp_contact_form', FILTER_SANITIZE_STRING );
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'dp-contact-form-submission-nonce' ) ) {
			$result['message'] = '<div class="dp-contact-form-ajax-error">' . __( 'Security check failed', 'vaultrix' ) . '</div>';
			echo wp_json_encode( $result );
			wp_die();
		}
		$name = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_STRING );
		if ( empty( $name ) ) {
			$result['message'] = '<div class="dp-contact-form-ajax-error">' . __( 'Please enter your name', 'vaultrix' ) . '</div>';
			echo wp_json_encode( $result );
			wp_die();
		}
		$email = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
		if ( empty( $email ) ) {
			$result['message'] = '<div class="dp-contact-form-ajax-error">' . __( 'Please enter your email', 'vaultrix' ) . '</div>';
			echo wp_json_encode( $result );
			wp_die();
		}
		$token = filter_input( INPUT_POST, 'token', FILTER_SANITIZE_STRING );
		if ( empty( $token ) ) {
			$result['message'] = '<div class="dp-contact-form-ajax-error">' . __( 'Human verification failed.', 'vaultrix' ) . '</div>';
			echo wp_json_encode( $result );
			wp_die();
		}
		$message = filter_input( INPUT_POST, 'message', FILTER_SANITIZE_STRING );

		$response      = wp_safe_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'body' => array(
					'secret'   => VAULTRIX_RECAPTCHA_V3_SECRET_KEY,
					'response' => $token,
				),
			)
		);
		$response_body = json_decode( $response['body'] );

		if ( $response_body->success && $response_body->score < 0.5 ) {
			$result['message'] = '<div class="dp-contact-form-ajax-error">' . __( 'Human verification failed.', 'vaultrix' ) . '</div>';
			echo wp_json_encode( $result );
			wp_die();
		}

		// Create post object.
		$form_post = array(
			'post_title'   => sanitize_text_field( $name ),
			'post_content' => '',
			'post_status'  => 'publish',
			'post_type'    => VAULTRIX_CONTACT_FORM_ENTRY_POST_TYPE,
		);
		// Insert the post into the database.
		$form_id = wp_insert_post( $form_post );
		if ( ! empty( $form_id ) && ! is_wp_error( $form_id ) ) {

			update_post_meta( $form_id, 'cf_email_address', $email );
			update_post_meta( $form_id, 'cf_message', $message );

			$form_data = array(
				'name'  => $name,
				'email' => $email,
			);

			// Send Confirmation message to user
			$this->dp_send_confirmation_message_to_user( $form_data );

			$result['success'] = 1;
			ob_start();

			$cf_success_message = get_field( 'cf_success_message', 'option' );
			$success_message    = ! empty( $cf_success_message ) ? $cf_success_message : 'Thank you for contacting us.';

			$result['message'] = '<div class="dp-ajax-success">' . __( $success_message, 'vaultrix' ) . '</div>';
		} else {
			$result['message'] = '<div class="dp-contact-form-ajax-error">' . __( '', 'vaultrix' ) . '</div>';
		}
		echo wp_json_encode( $result );
		wp_die();
	}

	/**
	 * Callback function to send confirmation message to user.
	 */
	public function dp_send_confirmation_message_to_user( $form_data = array() ) {
		$result            = array();
		$result['success'] = 0;

		if ( empty( $form_data ) ) {
			return $result;
		}

		$name              = ( ! empty( $form_data['name'] ) ) ? $form_data['name'] : '';
		$email             = ( ! empty( $form_data['email'] ) ) ? $form_data['email'] : '';
		$subject           = 'Thank You for Contacting Us !';
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
																		<p style="margin:0 0 16px">Hello ' . $name . ',</p>
																		<p style="margin:0 0 16px">Thanks for Contacting Us. We will Contact you soon.</p>
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

		$to_email = $email;

		$headers = array(
			'Content-Type: text/html; charset=ISO-8859-1\r\n',
			'MIME-Version: 1.0\r\n',
			'Reply-To: Vaultrix <support.@deeppockets.com>',
			'From: Vaultrix <support@deeppockets.com>',
		);

		// phpcs:ignore
		$mail_sent = wp_mail( $to_email, $subject, $formatted_message, $headers );

		if ( $mail_sent ) {
			$result['success'] = 1;
		}

		return $result;
	}
}
