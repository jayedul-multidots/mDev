<?php
// phpcs:ignoreFile
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
$header_logo = get_field( 'header_logo', 'option' );
?>
	<main id="primary" class="site-main vaultrix-main">
		<div class="countdown-container common-page">
			<div class="container">
				<div class="wp-block-columns">
					<div class="wp-block-column">
						<div class="site-logo">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php
								$footer_logo  = get_field( 'footer_logo', 'option' );
									if ( ! empty( $footer_logo ) ) {
										$args = array(
											'width'  => '150',
											'height' => '150',
											'alt'    => 'Footer logo',
										);
										echo wp_get_attachment_image( $footer_logo, 'full', false, $args );
									}
								?>
							</a>
						</div>
						<h1>COMING SOON...</h1>
						<h2>You Won't Want To Miss This</h2>
						<!-- <div id="countdown"></div> -->
					</div>
					<div class="wp-block-column">
						<div class="coming-soon-form">
							<!-- <h2>Don't Miss Out - Be The First To Be Notified When We Launch</h2> -->
							<?php echo do_shortcode( '[contact-form-7 id="b0a5eeb" title="Coming Soon Form"]' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			// Adjust the target date and time
			const targetDate = new Date("May 29, 2024 12:00:00 EST");

			function updateCountdown() {
				const currentDate = new Date();
				const timeDifference = targetDate - currentDate;

				if (timeDifference > 0) {
					const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
					const hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
					const minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
					const seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

					document.getElementById("countdown").innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
					// document.getElementById("countdown").innerHTML = `${days} d : ${hours} h : ${minutes} m : ${seconds} s`;
				} else {
					document.getElementById("countdown").innerHTML = "Event has started!";
				}
			}

			// Update the countdown every second
			setInterval(updateCountdown, 1000);

			// Initial call to display the countdown immediately
			updateCountdown();
		</script>
		<style>
			.countdown-container {
				background-color:#001e4e;
				background-image:url("/wp-content/themes/vaultrix/assets/src/images/top-left-wave.webp");
				background-repeat: no-repeat;
				background-size: contain;
				background-position: top left;
				min-height: 100vh;
				display: flex;
				align-items: center;
				position: relative;
				color:#fff;
				padding: 100px 0;
			}
			.container{
				color: #fff;
				z-index: 1;
				width: 100%;
			}
			img{
				max-width: max-content;
				width: 100%;
				height: 100%;
				margin-bottom: 20px;
			}
			.countdown-container h1{
				line-height: normal;
				color: #fff;
				font-size: 40px;
				margin-bottom: 20px;
			}
			#countdown{
				font-size: 30px;
				font-style: normal;
				font-weight: 700;
				line-height: 27px;
				margin-bottom: 12px;
			}
			.wp-block-columns{
				display: flex;
				align-items: center;
				gap: 40px;
				/* flex-direction: column; */
			}
			.wp-block-column{
				width:100%;
				max-width:50%;
			}
			.wp-block-column:first-child{
				text-align: left;
			}
			.wp-block-column:first-child h2 {
				color: #fff;
				margin:0;
			}
			.coming-soon-form .screen-reader-response {
				display: none;
			}
			.coming-soon-form {
				padding: 40px;
				border-radius: 24px;
				border: 1px solid rgba(255, 255, 255, 0.1);
				background: #0a0c1c;
				backdrop-filter: blur(20px);

				padding: 48px;
				border-radius: 16px;
				border: none;
				background-color: #07101d;
				background-image: url(https://deeppockets.go-vip.net/wp-content/themes/vaultrix/assets/build/images/contact-form-bg.01e80c2b.webp);
				background-position: center bottom;
				background-repeat: no-repeat;
				background-size:cover;
			}

			.vaultrix-main {
				position: relative;
				background-repeat: no-repeat;
				background-image: url(https://deeppockets.go-vip.net/wp-content/themes/vaultrix/assets/build/images/top-left-wave.7c610664.webp);
				background-repeat: no-repeat;
				background-size: contain;
				background-position: top left
			}
			.coming-soon-form h2 {
				font-size: 30px;
				line-height: normal;
				color: #fff;
				margin-bottom: 20px;
				text-align: center;
			}
			.coming-soon-form form p {
				color: #fff;
				margin-bottom: 0;
			}
			.coming-soon-form form label {
				width: 100%;
				font-family: "Eudoxus Sans", sans-serif;
				font-size: 16px;
				font-style: normal;
				font-weight: 400;
				line-height: 24px;
				color: rgba(255, 255, 255, .6);
				margin-bottom: 12px;
				display: block;
			}
			.coming-soon-form form label > span {
				display: flex;
				flex-direction: column;
				margin: 12px 0 25px;
			}
			.coming-soon-form form label span.wpcf7-not-valid-tip {
				font-size: 14px;
				line-height: 24px;
			}
			.coming-soon-form form input, .coming-soon-form form textarea {
				width: 100%;
				margin: 0;
				padding: 16px 24px 18px;
				font-family: "Eudoxus Sans", sans-serif;
				font-size: 16px;
				font-style: normal;
				font-weight: 400;
				line-height: 24px;
				border-radius: 8px;
				background: rgba(255, 255, 255, .1);
				border: none;
				outline: none;
				color: #fff;
			}
			.coming-soon-form form input:active, .coming-soon-form form textarea:active, .coming-soon-form form input:focus, .coming-soon-form form textarea:focus, .coming-soon-form form input:focus-visible, .coming-soon-form form textarea:focus-visible {
				outline: 0;
				border: 1px solid var(--primary-main, #6533ee);
			}
			.coming-soon-form form input::placeholder, .coming-soon-form form textarea::placeholder {
				color: rgba(255, 255, 255, 0.16);
				font-size: 14px;
				font-style: normal;
				font-weight: 500;
				line-height: 22.4px;
				letter-spacing: 0.14px;
			}
			.coming-soon-form form input{
				background-repeat: no-repeat;
				background-position: 20px center;
				background-size: 24px;
				padding-left: 55px;
				position:relative;
				background-image:url("/wp-content/themes/vaultrix/assets/src/images/contact-form-mail.png")
			}
			.coming-soon-form form input[type=text]{
				background-image:url("/wp-content/themes/vaultrix/assets/src/images/contact-form-user.png")
			}
			.coming-soon-form form input:focus,
			.coming-soon-form form input:hover{
				outline:none;
				border:none;
			}
			.coming-soon-form form .submit-btn {
				display: flex;
				position: relative;
				margin-top: 30px;
			}
			.coming-soon-form form .submit-btn .btn-main {
				text-align: center;
				background: linear-gradient(98deg, #D81425 0%, #FFC371 130.01%);
				color: #fff;
				display: inline-block;
				font-family: "Eudoxus Sans", sans-serif;
				font-size: 16px;
				font-style: normal;
				font-weight: 600;
				line-height: 24.01px;
				letter-spacing: -0.02px;
				cursor: pointer;
				word-break: break-word;
				text-decoration: none;
				position: relative;
				padding: 12px 32px;
				margin: 0;
				border-radius: 32px;
				transition: 150ms ease-out;
			}
			.coming-soon-form form .submit-btn .btn-main:hover {
				background-color: #3d1f8f;
				border:none;
			}
			.coming-soon-form form .submit-btn .btn-main:focus{
				outline:none;
			}
			.coming-soon-form form .submit-btn .wpcf7-spinner {
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				margin: 0;
			}
			.coming-soon-form form .wpcf7-response-output {
				font-size: 14px;
				border: none;
				padding: 0;
				margin: 20px 5px 0;
				text-align: center;
			}
			.coming-soon-form form.submitting .submit-btn .btn-main {
				font-size: 0;
			}
			.coming-soon-form form.sent .wpcf7-response-output {
				color: green;
			}
			.coming-soon-form form.invalid .wpcf7-response-output, .coming-soon-form form.failed .wpcf7-response-output, .coming-soon-form form.aborted .wpcf7-response-output {
				color: #dc3232;
			}
			.coming-soon-form form.spam, .coming-soon-form form.unaccepted, .coming-soon-form form.payment-required {
				color: #ffb900;
			}

			body .site-footer{
				padding: 0 !important;
			}
			
			@media screen and (max-width: 1200px) {
				.container{
					padding: 0 50px;
				}
			}
			@media screen and (max-width: 1100px) {
				h1{
					font-size: 40px;
				}
				h2 {
					font-size: 28px;
				}
				.container{
					padding: 0 56px;
				}
				.wp-block-columns{
					flex-direction:column;
				}
				.wp-block-column{
					max-width: 100%;
					/* text-align: center; */
				}
				.wp-block-column:first-child{
					text-align: center;
				}
				.coming-soon-form{
					max-width: 767px;
					width: 100%;
					margin: auto;
				}
			}
			@media screen and (max-width: 1024px) {
				.coming-soon-form form input, .coming-soon-form form textarea{
					padding: 16px 24px;
				}
				.coming-soon-form form input{
					padding-left:55px;
				}
				.countdown-container h1{
					font-size:40px;
				}
				#countdown{
					font-size:25px;
				}
			}
			@media screen and (max-width: 767px) {
				h1{
					font-size: 36px;
				}
				h2 {
					font-size: 25px;
				}
				.container{
					padding: 0 24px;
				}
				.wp-block-columns{
					gap:30px;
				}
				.coming-soon-form {
					padding: 40px 24px 30px;
				}
				.coming-soon-form form input, .coming-soon-form form textarea{
					padding: 12px 24px;
				}
				.coming-soon-form form input{
					padding-left:55px;
				}
			}
			@media screen and (max-width: 480px) {
				.countdown-container h1{
					font-size:35px;
				}
			}
			

		</style>
	</main><!-- #main -->
	<footer id="colophon" class="site-footer">
		<link rel="preload" href="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/vaultrix/assets/library/css/site-font.min.css" as="style" onload="this.onload=null;this.rel='stylesheet';">
	</footer><!-- #colophon -->
</body>
</html>
