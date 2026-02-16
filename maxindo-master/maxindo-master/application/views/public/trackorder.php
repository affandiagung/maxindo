<?php
	$config = $this->Mmasterdata->getConfiguration();
?>
<!DOCTYPE html>
<!--
Product Name: Metronic - #1 Selling Bootstrap 5 HTML Multi-demo Admin Dashboard ThemeAuthor: KeenThemes
Purchase: https://1.envato.market/EA4JPWebsite: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.-->
<html lang="en">
	<!--begin::Head-->
	<head><base href="../../../">
		<meta charset="utf-8" />
		<title>Track Order - <?php echo $config->APP_NAME; ?></title>
		<meta name="description" content="Metronic admin dashboard live demo. Check out all the features of the admin panel. A large number of settings, additional services and widgets." />
		<meta name="keywords" content="Metronic, bootstrap, bootstrap 5, Angular 11, VueJs, React, Laravel, admin themes, web design, figma, web development, ree admin themes, bootstrap admin, bootstrap dashboard" />
		<link rel="canonical" href="Https://preview.keenthemes.com/metronic8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="shortcut icon" href="<?php echo base_url("uploads/" . $config->APP_LOGO_HEADER); ?>" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="<?php echo base_url("assets/admin13"); ?>/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url("assets/admin13"); ?>/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
		<style type="text/css">
			
			.font-weight-bolder{
				font-weight: 600 !important;
			}
		</style>
		<script type="text/javascript">
			var base_url = "<?php echo base_url(); ?>";
			var site_url = "<?php echo site_url(); ?>";
		</script>
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="bg-dark">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url(<?php echo base_url(); ?>assets/admin13/media/illustrations/progress-hd.png)">
				<!--begin::Content-->
				<div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
					<!--begin::Logo-->
					<a href="<?php echo base_url(); ?>" class="mb-12">
						<img alt="Logo" src="<?php echo base_url("uploads/" . $config->APP_LOGO_HEADER); ?>" class="" />
					</a>
					<!--end::Logo-->
					<!--begin::Wrapper-->
					<?php if ($order): ?>
						<div class="card card-custom gutter-b">
							<div class="card-body p-0">
								<!-- begin: Invoice-->
								<!-- begin: Invoice header-->
								<div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
									<div class="col-md-10">
										<div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
											<h1 class="display-4 font-weight-boldest mb-10">Track Order</h1>
											<div class="d-flex flex-column align-items-md-end px-0">
												<!--begin::Logo-->
												<a href="#" class="mb-5">
													<img alt="Logo" src="<?php echo base_url("uploads/" . $config->APP_LOGO_HEADER); ?>" class="" />
												</a>
												<!--end::Logo-->
												<span class="d-flex flex-column align-items-md-end opacity-70">
													<span><?php echo $config->OFFICE_ADDRESS ?></span>
												</span>
											</div>
										</div>
										<div class="border-bottom w-100"></div>
										<div class="d-flex justify-content-between pt-6">
											<div class="d-flex flex-column flex-root">
												<span class="font-weight-bolder mb-2">Tanggal Mulai</span>
												<span class="opacity-70"><?php echo $order['PROJECTSTART'] ?></span>
											</div>
											<div class="d-flex flex-column flex-root">
												<span class="font-weight-bolder mb-2">Tanggal Selesai</span>
												<span class="opacity-70"><?php echo $order['PROJECTEND'] ?></span>
											</div>
											<div class="d-flex flex-column flex-root">
												<span class="font-weight-bolder mb-2">Kode Order</span>
												<span class="opacity-70"><?php echo $order['PROJECTORDERCODE'] ?></span>
											</div>
										</div>
										<div class="d-flex justify-content-between pt-6">
											<div class="d-flex flex-column flex-root">
												<span class="font-weight-bolder mb-2">Kostumer</span>
												<span class="opacity-70"><?php echo $order['CUSTOMERNAME'] ?></span>
											</div>
											<div class="d-flex flex-column flex-root">
												<span class="font-weight-bolder mb-2">Tahap Proyek</span>
												<span class="opacity-70"><?php echo $order['PROJECTSTAGENAME'] ?></span>
											</div>
											<div class="d-flex flex-column flex-root">
												<span class="font-weight-bolder mb-2">Lokasi</span>
												<span class="opacity-70"><?php echo $order['PROJECTLOCATION'] ?></span>
											</div>
										</div>
									</div>
								</div>
								<!-- end: Invoice header-->
								<!-- begin: Invoice body-->
								<div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
									<div class="col-md-10">
										<div class="table-responsive">
											<table class="table">
												<thead>
													<tr>
														<th class="pl-0 font-weight-bold text-muted text-uppercase">Nama Inventori</th>
														<th class="text-right font-weight-bold text-muted text-uppercase">Qty</th>
														<th class="text-right font-weight-bold text-muted text-uppercase">Harga</th>
														<th class="text-right pr-0 font-weight-bold text-muted text-uppercase">Total</th>
													</tr>
												</thead>
												<tbody>
													<tr class="font-weight-boldest">
														<td class="border-0 pl-0 pt-7 d-flex align-items-center">
														<!--begin::Symbol-->
														<div class="symbol symbol-40 flex-shrink-0 mr-4 bg-light">
															<div class="symbol-label" style="background-image: url('/metronic/theme/html/demo4/dist/assets/media/products/11.png')"></div>
														</div>
														<!--end::Symbol-->
														Street Sneakers</td>
														<td class="text-right pt-7 align-middle">2</td>
														<td class="text-right pt-7 align-middle">$90.00</td>
														<td class="text-primary pr-0 pt-7 text-right align-middle">$180.00</td>
													</tr>
													<tr class="font-weight-boldest border-bottom-0">
														<td class="border-top-0 pl-0 py-4 d-flex align-items-center">
														<!--begin::Symbol-->
														<div class="symbol symbol-40 flex-shrink-0 mr-4 bg-light">
															<div class="symbol-label" style="background-image: url('/metronic/theme/html/demo4/dist/assets/media/products/2.png')"></div>
														</div>
														<!--end::Symbol-->
														Headphones</td>
														<td class="border-top-0 text-right py-4 align-middle">1</td>
														<td class="border-top-0 text-right py-4 align-middle">$449.00</td>
														<td class="text-primary border-top-0 pr-0 py-4 text-right align-middle">$449.00</td>
													</tr>
													<tr class="font-weight-boldest border-bottom-0">
														<td class="border-top-0 pl-0 py-4 d-flex align-items-center">
														<!--begin::Symbol-->
														<div class="symbol symbol-40 flex-shrink-0 mr-4 bg-light">
															<div class="symbol-label" style="background-image: url('/metronic/theme/html/demo4/dist/assets/media/products/1.png')"></div>
														</div>
														<!--end::Symbol-->
														Smartwatch</td>
														<td class="border-top-0 text-right py-4 align-middle">1</td>
														<td class="border-top-0 text-right py-4 align-middle">$160.00</td>
														<td class="text-primary border-top-0 pr-0 py-4 text-right align-middle">$160.00</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<!-- end: Invoice body-->
								<?php /*<!-- begin: Invoice footer-->
								<div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0 mx-0">
									<div class="col-md-10">
										<div class="table-responsive">
											<table class="table">
												<thead>
													<tr>
														<th class="font-weight-bold text-muted text-uppercase">PAYMENT TYPE</th>
														<th class="font-weight-bold text-muted text-uppercase">PAYMENT STATUS</th>
														<th class="font-weight-bold text-muted text-uppercase">PAYMENT DATE</th>
														<th class="font-weight-bold text-muted text-uppercase text-right">TOTAL PAID</th>
													</tr>
												</thead>
												<tbody>
													<tr class="font-weight-bolder">
														<td>Credit Card</td>
														<td>Success</td>
														<td>Jan 07, 2020</td>
														<td class="text-primary font-size-h3 font-weight-boldest text-right">$789.00</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<!-- end: Invoice footer-->*/ ?>
								<!-- begin: Invoice action-->
								<div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
									<div class="col-md-10">
										<div class="d-flex justify-content-between">
											<button type="button" class="btn btn-primary font-weight-bold" onclick="window.print();">Cetak Order</button>
										</div>
									</div>
								</div>
								<!-- end: Invoice action-->
								<!-- end: Invoice-->
							</div>
						</div>
					<?php else: ?>
					<div class="w-lg-500px bg-white rounded shadow-sm p-10 p-lg-15 mx-auto">
						<!--begin::Form-->
						<form class="form w-100" novalidate="novalidate" id="kt_track_order" action="#">
							<!--begin::Heading-->
							<div class="text-center mb-10">
								<!--begin::Title-->
								<h1 class="text-dark mb-3"><?php echo $config->APP_SHORTNAME; ?> </h1>
								<!--end::Title-->
								<!--begin::Link-->
								<div class="text-gray-400 fw-bold fs-4"><?php echo $config->APP_NAME; ?></div>
								<!--end::Link-->
							</div>
							<!--begin::Heading-->
							<!--begin::Input group-->
							<div class="fv-row mb-10">
								<!--begin::Label-->
								<label class="form-label fs-6 fw-bolder text-dark">Kode Order</label>
								<!--end::Label-->
								<!--begin::Input-->
								<input class="form-control form-control-lg form-control-solid" type="text" name="ordercode" id="ordercode" autocomplete="off" />
								<!--end::Input-->
							</div>
							<!--end::Input group-->
							<!--begin::Actions-->
							<div class="text-center">
								<!--begin::Submit button-->
								<button type="submit" id="kt_track_order_submit" class="btn btn-lg btn-primary w-100 mb-5">
									<span class="indicator-label">Submit</span>
									<span class="indicator-progress">Please wait...
									<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
								</button>
								<!--end::Submit button-->
							</div>
							<!--end::Actions-->
						</form>
						<!--end::Form-->
					</div>
						
					<?php endif ?>
					<!--end::Wrapper-->
				</div>
				<!--end::Content-->
				<!--begin::Footer-->
				<div class="d-flex flex-center flex-column-auto p-10">
					<!--begin::Links-->
					<div class="d-flex align-items-center fw-bold fs-6">
						<a href="https://keenthemes.com/faqs" class="text-muted text-hover-primary px-2">About</a>
						<a href="mailto:support@keenthemes.com" class="text-muted text-hover-primary px-2">Contact</a>
						<a href="https://1.envato.market/EA4JP" class="text-muted text-hover-primary px-2">Contact Us</a>
					</div>
					<!--end::Links-->
				</div>
				<!--end::Footer-->
			</div>
			<!--end::Authentication - Sign-in-->
		</div>
		<!--end::Main-->
		<!--begin::Javascript-->
		<!--begin::Global Javascript Bundle(used by all pages)-->
		<script src="<?php echo base_url("assets/admin13"); ?>/plugins/global/plugins.bundle.js"></script>
		<script src="<?php echo base_url("assets/admin13"); ?>/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Page Custom Javascript(used by this page)-->
		<script src="<?php echo base_url("assets/admin13"); ?>/js/trackorder.js"></script>
		<!--end::Page Custom Javascript-->
		<!--end::Javascript-->
		<script type="text/javascript">
			function loginCheck(){
				return false;
			}
		</script>
	</body>
	<!--end::Body-->
</html>