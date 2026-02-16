<?php 
$config = $this->Mmasterdata->getConfiguration( $module );
$logged = $this->session->userdata("admin");
$dir = $this->Mmasterdata->getHomePage($logged['PRIVILEGE']);
$user = $this->Mmasterdata->getLoggedUser($logged['USERID']);
$website = (substr($config->OFFICE_WEBSITE,0,4) != "http") ? "http://" . $config->OFFICE_WEBSITE : $config->OFFICE_WEBSITE;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Monitoring <?php echo $config->APP_NAME; ?></title>
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
<body>

</body>
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
</html>