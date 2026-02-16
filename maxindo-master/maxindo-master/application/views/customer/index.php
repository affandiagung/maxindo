<?php 
$config = $this->Mmasterdata->getConfiguration( $module );
$logged = $this->session->userdata("admin");
$dir = $this->Mmasterdata->getHomePage($logged['PRIVILEGE']);
$user = $this->Mmasterdata->getLoggedUser($logged['USERID']);
$website = (substr($config->OFFICE_WEBSITE,0,4) != "http") ? "http://" . $config->OFFICE_WEBSITE : $config->OFFICE_WEBSITE;
?>
<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: Metronic - Bootstrap 5 HTML, VueJS, React, Angular & Laravel Admin Dashboard Theme
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
	<!--begin::Head-->
	<head><base href="">
		<title>Admin - <?php echo $config->APP_NAME; ?></title>
		<meta charset="utf-8" />
		<meta name="description" content="<?php echo $config->APP_NAME; ?>" />
		<meta name="keywords" content="Metronic, bootstrap, bootstrap 5, Angular, VueJs, React, Laravel, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="<?php echo $config->APP_NAME; ?>" />
		<meta property="og:url" content="<?php echo base_url(); ?>" />
		<meta property="og:site_name" content="<?php echo $config->APP_SHORTNAME; ?>" />
		<link rel="canonical" href="<?php echo base_url(); ?>" />
		<link rel="shortcut icon" href="<?php echo base_url("uploads/" . $config->APP_LOGO_HEADER); ?>" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Page Vendor Stylesheets(used by this page)-->
		<link href="<?php echo base_url('assets/admin13'); ?>/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url('assets/admin13'); ?>/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Page Vendor Stylesheets-->
		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="<?php echo base_url('assets/admin13'); ?>/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url('assets/admin13'); ?>/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url("assets"); ?>/admin13/plugins/custom/jquery.calculator/jquery.calculator.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url("bower_components"); ?>/typeahead.js/dist/typeaheadjs.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url("assets"); ?>/admin13/css/engine.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
		<script type="text/javascript">
			var base_url = "<?php echo base_url(); ?>";
			var site_url = "<?php echo site_url(); ?>";
		</script>
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed toolbar-tablet-and-mobile-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
		<!--begin::Main-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Page-->
			<div class="page d-flex flex-row flex-column-fluid">
				<!--begin::Aside-->
				<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
					<!--begin::Brand-->
					<div class="aside-logo flex-column-auto" id="kt_aside_logo">
						<!--begin::Logo-->
						<a href="<?php echo site_url( $this->router->fetch_directory() ); ?>">
							<img alt="Logo" src="<?php echo base_url("uploads/" . $config->APP_LOGO_HEADER); ?>" class="h-50px logo float-start" />
						</a>
						<!--end::Logo-->
						<!--begin::Aside toggler-->
						<div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
							<!--begin::Svg Icon | path: icons/duotune/arrows/arr074.svg-->
							<span class="svg-icon svg-icon-1 rotate-180">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M11.2657 11.4343L15.45 7.25C15.8642 6.83579 15.8642 6.16421 15.45 5.75C15.0358 5.33579 14.3642 5.33579 13.95 5.75L8.40712 11.2929C8.01659 11.6834 8.01659 12.3166 8.40712 12.7071L13.95 18.25C14.3642 18.6642 15.0358 18.6642 15.45 18.25C15.8642 17.8358 15.8642 17.1642 15.45 16.75L11.2657 12.5657C10.9533 12.2533 10.9533 11.7467 11.2657 11.4343Z" fill="black" />
								</svg>
							</span>
							<!--end::Svg Icon-->
						</div>
						<!--end::Aside toggler-->
					</div>
					<!--end::Brand-->
					<!--begin::Aside menu-->
					<div class="aside-menu flex-column-fluid">
						<!--begin::Aside Menu-->
						<div class="hover-scroll-overlay-y my-2 py-5 py-lg-8" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
							<!--begin::Menu-->
							<div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true">
								<?php
								$logged = $this->session->userdata("admin");
								$homepage = $this->Mmasterdata->getAccessPrivilege($logged['PRIVILEGE']);
								$menus = $this->Mmasterdata->getMenu($logged['PRIVILEGE'], true);
								$renderMenu = "";
								foreach( $menus as $menu ){
									// Check  if has Child
									$child1 = $this->Mmasterdata->getChildMenu( $menu->MENUID, $logged['PRIVILEGE'] );
									if( count($child1) == 0 ){
										$renderMenu .= '<div class="menu-item">
										<a class="menu-link main-menu" href="'.site_url( $homepage ."/" . $menu->URL).'">
										<span class="menu-icon">
										<i class="'.$menu->ICON.'"></i>
										</span>
										<span class="menu-title">'.$menu->NAME.'</span>
										<span class="menu-label badge badge-light-danger" id="menu-notif-'.text2slug($menu->URL).'"></span>
										</a>
										</div>';
									} else {
										$renderMenu .= '<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
										<span class="menu-link">
										<span class="menu-icon">
										<i class="'.$menu->ICON.'"></i>
										</span>
										<span class="menu-title">'.$menu->NAME.'</span>
										<span class="menu-label badge badge-light-danger" id="menu-notif-'.text2slug($menu->URL).'"></span>
										<span class="menu-arrow"></span>
										</span>
										<div class="menu-sub menu-sub-accordion menu-active-bg">';
										foreach($child1 as $ch1){
											$child2 = $this->Mmasterdata->getChildMenu( $ch1->MENUID, $logged['PRIVILEGE'] );
											if( count($child2) == 0 ){
												$renderMenu .= '<div class="menu-item">
												<a class="menu-link main-menu" href="'.site_url( $homepage ."/" . $ch1->URL).'">
												<span class="menu-bullet">
												<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">'.$ch1->NAME.'</span>
												<span class="menu-label badge badge-light-danger" id="menu-notif-'.text2slug($ch1->URL).'"></span>
												</a>
												</div>';
											} else {
												$renderMenu .= '<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
												<span class="menu-link">
												<span class="menu-bullet">
												<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">'.$ch1->NAME.'</span>
												<span class="menu-label badge badge-light-danger" id="menu-notif-'.text2slug($ch1->URL).'"></span>
												<span class="menu-arrow"></span>
												</span>
												<div class="menu-sub menu-sub-accordion menu-active-bg">';
												foreach($child2 as $ch2){
													$renderMenu .= '<div class="menu-item">
													<a class="menu-link main-menu" href="'.site_url( $homepage ."/" . $ch2->URL).'">
													<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
													</span>
													<span class="menu-title">'.$ch2->NAME.'</span>
													<span class="menu-label badge badge-light-danger" id="menu-notif-'.text2slug($ch2->URL).'"></span>
													</a>
													</div>';
												}
												$renderMenu .= '</div></div>';
											}
										}
										$renderMenu .= '</div></div>';
									}
								}

								echo $renderMenu;
								?>
							</div>
							<!--end::Menu-->
						</div>
					</div>
					<!--end::Aside menu-->
					<!--begin::Footer-->
					<div class="aside-footer flex-column-auto" id="kt_aside_footer">
						<div id="countdown-timer" class="w-100"></div>
					</div>
					<!--end::Footer-->
				</div>
				<!--end::Aside-->
				<!--begin::Wrapper-->
				<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
					<!--begin::Header-->
					<div id="kt_header" style="" class="header align-items-stretch">
						<!--begin::Container-->
						<div class="container-fluid d-flex align-items-stretch justify-content-between">
							<!--begin::Aside mobile toggle-->
							<div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
								<div class="btn btn-icon btn-active-color-white" id="kt_aside_mobile_toggle">
									<i class="bi bi-list fs-1"></i>
								</div>
							</div>
							<!--end::Aside mobile toggle-->
							<!--begin::Mobile logo-->
							<div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
								<a href="<?php echo site_url(); ?>" class="d-lg-none">
									<img alt="Logo" src="<?php echo base_url("uploads/" . $config->APP_LOGO_HEADER); ?>" class="h-50px" />
								</a>
							</div>
							<!--end::Mobile logo-->
							<!--begin::Wrapper-->
							<div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
								<!--begin::Navbar-->
								<div class="d-flex align-items-stretch" id="kt_header_nav">
									<!--begin::Menu wrapper-->
									<div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-place="true" data-kt-place-mode="prepend" data-kt-place-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
										<h2 class="text-white d-sm-none d-lg-block" style="line-height: 70px;"><?php echo $config->APP_NAME; ?></h2>
									</div>
									<!--end::Menu wrapper-->
								</div>
								<!--end::Navbar-->
								<!--begin::Toolbar wrapper-->
								<div class="topbar d-flex align-items-stretch flex-shrink-0">
									<!--begin::User-->
									<div class="d-flex align-items-stretch" id="kt_header_user_menu_toggle">
										<!--begin::Menu wrapper-->
										<div class="topbar-item cursor-pointer symbol px-3 px-lg-5 me-n3 me-lg-n5 symbol-30px symbol-md-35px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" data-kt-menu-flip="bottom">
											<?php echo imageload($user->PHOTO, $user->NAME, "uploads"); ?>
										</div>
										<!--begin::User account menu-->
										<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<div class="menu-content d-flex align-items-center px-3">
													<!--begin::Avatar-->
													<div class="symbol symbol-50px me-5">
														<?php echo imageload($user->PHOTO, $user->NAME, "uploads"); ?>
													</div>
													<!--end::Avatar-->
													<!--begin::Username-->
														<div class="d-flex flex-column">
															<div class="fw-bolder d-flex align-items-center fs-5"><?php echo $user->NAME; ?>
															<?php /* <span class="badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2"><?php echo $user->PRIVILEGE; ?></span> */ ?>
														</div>
														<a href="#" class="fw-bold text-muted text-hover-primary fs-7"><?php echo $user->EMAIL; ?></a>
													</div>
													<!--end::Username-->
												</div>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu separator-->
											<div class="separator my-2"></div>
											<!--end::Menu separator-->
											<!--begin::Menu item-->
											<div class="menu-item px-5">
												<a href="<?php echo $dir . "/userprofile"; ?>" class="menu-link main-menu px-5"><?php echo $this->lang->line("userprofile"); ?></a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-5">
												<a href="<?php echo $dir . "/change_password"; ?>" class="menu-link main-menu px-5"><?php echo $this->lang->line("change_password"); ?></a>
											</div>
											<!--end::Menu item-->
											<?php if($config->MANUALBOOK != "" && file_exists("./uploads/" . $config->MANUALBOOK) ){ ?>
												<!--begin::Menu item-->
												<div class="menu-item px-5">
													<a target="_blank" href="<?php echo base_url() . "uploads/" . $config->MANUALBOOK; ?>" class="menu-link px-5"><?php echo $this->lang->line("manualbook"); ?></a>
												</div>
												<!--end::Menu item-->
											<?php } ?>
											<!--begin::Menu separator-->
											<div class="separator my-2"></div>
											<!--end::Menu separator-->

											<!--begin::Menu item-->
											<div class="menu-item px-5">
												<a href="<?php echo site_url("logout"); ?>" class="menu-link px-5">Sign Out</a>
											</div>
											<!--end::Menu item-->
										</div>
										<!--end::User account menu-->
										<!--end::Menu wrapper-->
									</div>
									<!--end::User -->
									<!--begin::Heaeder menu toggle-->
									<div class="d-flex align-items-stretch d-lg-none px-3 me-n3" title="Show header menu">
										<div class="topbar-item" id="kt_header_menu_mobile_toggle">
											<i class="bi bi-text-left fs-1"></i>
										</div>
									</div>
									<!--end::Heaeder menu toggle-->
								</div>
								<!--end::Toolbar wrapper-->
							</div>
							<!--end::Wrapper-->
						</div>
						<!--end::Container-->
					</div>
					<!--end::Header-->
					<!--begin::Content-->
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
						<!--begin::Post-->
						<div class="post d-flex flex-column-fluid">
							<!--begin::Container-->
							<div class="container">
								<div class="row">
									<div class="col-md-12">
										<div id="top-content">

										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div id="main-content">

										</div>
									</div>
								</div>
							</div>
							<!--end::Container-->
						</div>
						<!--end::Post-->
					</div>
					<!--end::Content-->
					<!--begin::Footer-->
					<div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
						<!--begin::Container-->
						<div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
							<!--begin::Copyright-->
							<div class="text-dark order-2 order-md-1">
								<span class="text-muted fw-bold me-1"><?php echo date("Y"); ?> © </span>
								<a href="<?php echo $config->OFFICE_WEBSITE; ?>" target="_blank" class="text-gray-800 text-hover-primary"><?php echo $config->OFFICE_NAME; ?></a>
							</div>
							<!--end::Copyright-->
						</div>
						<!--end::Container-->
					</div>
					<!--end::Footer-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::Root-->
		<!--begin::Scrolltop-->
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
			<span class="svg-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black" />
					<path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black" />
				</svg>
			</span>
			<!--end::Svg Icon-->
		</div>
		<!--end::Scrolltop-->
		<!-- begin:: Modal -->
		<div class="modal fade" id="engineModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-xl">
				<div class="modal-content">
					<div class="modal-body" id="modalContainer">

					</div>
					<div class="modal-footer" id="modalFooter">
						<button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-times"></i> Close</button>
						<button type="button" style="display:none;" onclick="checkout();" id="btn-checkout-modal" class="btn btn-success"><i class="fa fa-check"></i> Checkout</button>
						<button type="button" style="display:none;" onclick="print('modalContainer');" id="btn-print-modal" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
						<button type="button" style="display:none;" id="btn-save-modal" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
					</div>
				</div>
			</div>
		</div>
		<!-- end:: Modal -->
		<!-- begin:: Modal Popup -->
		<div class="modal fade" id="popupModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-xl">
				<div class="modal-content">
					<div class="modal-body" id="modalPopupContainer">

					</div>
					<div class="modal-footer" id="modalPopupFooter">
						<button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-times"></i> Close</button>
						<button type="button" style="display:none;" onclick="print('modalPopupContainer');" id="btn-print-popup" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
						<button type="button" style="display:none;" id="btn-save-popup" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
					</div>
				</div>
			</div>
		</div>

		<!-- end:: Modal Popup -->
		<!--end::Modals-->
		<!--begin::Javascript-->
		<script>var hostUrl = "<?php echo base_url('assets/admin13'); ?>/";</script>
		<!--begin::Global Javascript Bundle(used by all pages)-->
		<script src="<?php echo base_url('assets/admin13'); ?>/plugins/global/plugins.bundle.js"></script>
		<script src="<?php echo base_url('assets/admin13'); ?>/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Page Vendors Javascript(used by this page)-->
		<script src="<?php echo base_url('assets/admin13'); ?>/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
		<script src="<?php echo base_url('assets/admin13'); ?>/plugins/custom/datatables/datatables.bundle.js"></script>

		<script src="https://maps.google.com/maps/api/js?key=<?php echo $config->GOOGLE_MAP_API; ?>&libraries=places" type="text/javascript"></script>
		<script src="<?php echo base_url("assets/admin13"); ?>/plugins/custom/gmaps/gmaps.js" type="text/javascript"></script>

		<script type="text/javascript" src="<?php echo base_url("assets/admin13"); ?>/js/jquery.ajaxfileupload.js"></script>
		<script type="text/javascript" src="<?php echo base_url("bower_components"); ?>/tinymce/jquery.tinymce.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url("bower_components"); ?>/tinymce/tinymce.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url("assets/admin13"); ?>/js/bootbox.js"></script>
		<script type="text/javascript" src="<?php echo base_url("assets/admin13"); ?>/js/moment.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url("assets/admin13"); ?>/js/jquery.countdown.min.js"></script>
		
		<script type="text/javascript" src="<?php echo base_url("assets/admin13"); ?>/js/jquery.PrintArea.js"></script>
		<script type="text/javascript" src="<?php echo base_url("bower_components"); ?>/js-xlsx/dist/xlsx.core.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url("bower_components"); ?>/blobjs/Blob.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url("bower_components"); ?>/file-saverjs/FileSaver.js"></script>
		<script type="text/javascript" src="<?php echo base_url("bower_components"); ?>/tableexport.js/dist/js/tableexport.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url("bower_components"); ?>/typeahead.js/dist/typeahead.bundle.min.js"></script>

		<script type="text/javascript" src="<?php echo base_url("assets/admin13/js/polyfiller.js") ?>"></script>
		<script type="text/javascript" src="<?php echo base_url("assets/admin13/plugins/custom/jquery.calculator/jquery.plugin.js") ?>"></script>
		<script type="text/javascript" src="<?php echo base_url("assets/admin13/plugins/custom/jquery.calculator/jquery.calculator.js") ?>"></script>
		<script type="text/javascript" src="<?php echo base_url("assets/admin13/plugins/custom/jquery.calculator/jquery.calculator-id.js") ?>"></script>	
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.12/jquery.mousewheel.min.js"></script>

		<!--end::Page Vendors Javascript-->
		<!--begin::Page Custom Javascript(used by this page)-->
		<script src="<?php echo base_url('assets/admin13'); ?>/js/widgets.bundle.js"></script>
		<script src="<?php echo base_url('assets/admin13'); ?>/js/custom/widgets.js"></script>
		<script src="<?php echo base_url('assets/admin13'); ?>/js/custom/apps/chat/chat.js"></script>
		<script src="<?php echo base_url('assets/admin13'); ?>/js/custom/utilities/modals/upgrade-plan.js"></script>
		<script src="<?php echo base_url('assets/admin13'); ?>/js/custom/utilities/modals/create-app.js"></script>
		<script src="<?php echo base_url('assets/admin13'); ?>/js/custom/utilities/modals/users-search.js"></script>
		<!--end::Page Custom Javascript-->
		<script src="<?php echo base_url("assets"); ?>/admin13/js/engine.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
		<!--end::Page Custom Javascript-->

		<script type="text/javascript">
			tinymce.init({
				plugins: [
				'advlist autolink lists link image charmap print preview hr anchor pagebreak',
				'searchreplace wordcount visualblocks visualchars code fullscreen',
				'insertdatetime media nonbreaking save table contextmenu directionality',
				'emoticons paste textcolor colorpicker textpattern imagetools'
				],
				toolbar1: 'insertfile undo redo | styleselect | fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify',
				toolbar2: 'bullist numlist outdent indent | link image code | print preview media | forecolor backcolor emoticons | table',			
		    relative_urls : false,
		    remove_script_host : false,
		    convert_urls : true,
		    width: '100%',
		    menubar : false,
		    image_title: true, 
		    image_dimensions: true,
		    automatic_uploads: true,
		    images_upload_url: './upload/tinyMCEUpload/',
		    images_upload_credentials: true,
		    setup: function(ed){
			    ed.on('init', function() 
			    {
			        this.execCommand("fontName", false, "tahoma");
			        this.execCommand("fontSize", false, "9pt");
			    });
			  }
		  });

			function tinyMCEUpload(fileid){
				$.ajaxFileUpload({
					url:"<?php echo site_url(); ?>/upload/tinyMCEUpload/",
					secureuri:false,
					fileElementId:fileid,
					dataType: 'json',
					data:{name:'logan', id:'id'},
					success: function (data, status){
						console.log(data);
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								bootbox.alert(data.error);
							} else {
								return data.msg;
							}
						}
					},
					error: function (data, status, e)
					{
						bootbox.alert(e);
					}
				})
				return false;
			}

			function ajaxupload(fileid, dir){
				$.ajaxFileUpload({
					url:"<?php echo site_url(); ?>upload/uploadfile/dir/" + dir +"/fileName/FILE_" + fileid,
					secureuri:false,
					fileElementId:'FILE_'+fileid,
					dataType: 'json',
					data:{name:'logan', id:'id'},
					success: function (data, status){
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								bootbox.alert(data.error);
							} else {
								$("#"+fileid).val(data.msg);
							}
						}
					},
					error: function (data, status, e)
					{
						bootbox.alert(e);
					}
				})
				return false;
			}

			webshims.activeLang("fr");
			webshims.setOptions("debug", false);
			webshims.setOptions("forms-ext", {
				replaceUI: "auto",
				types: "number"
			});
			webshims.polyfill("forms forms-ext");

			$(function(){
				$("#main-content").load( site_url + "/<?php echo $logged['HOMEDIR']; ?>/dashboard/");
				$("#top-content").load( site_url + "/<?php echo $logged['HOMEDIR']; ?>/welcome/topContent");
				$("#countdown-timer").load( site_url + "/<?php echo $logged['HOMEDIR']; ?>/welcome/countdown_timer");
			});
			getNotif();
		</script>
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>