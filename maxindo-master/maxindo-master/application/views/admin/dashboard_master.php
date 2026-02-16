<?php
	$status = $this->Mmasterdata->getStatus();
?>
<?php 
	$header = $this->config->item("header_container");
	$header = str_replace("[home]", site_url(), $header);
	$header = str_replace("[name]", $params['name'], $header);
	$footer = $this->config->item("footer_container");

	echo $header;
?>
<style type="text/css" media="screen">
	.kt-widget17__item{
		border:  1px solid #ddd;
	}
	.kt-portlet__head{
		background: #4245fddb;
		color: #fff;
	}
	.kt-portlet.kt-portlet--skin-solid .kt-portlet__body{
		color: inherit !important;
	}
	.kt-widget12 .kt-widget12__content .kt-widget12__item .kt-widget12__info .kt-widget12__desc{
		color: #000;
	}
	.kt-portlet.kt-portlet--height-fluid{
		height: 200px;
	}
</style>
<div class="kt-portlet__body kt-portlet__body--fit mt-5">
	<div class="kt-widget17">
		<div class="kt-widget17__stats" style="width:100%;">
			<div class="kt-widget17__items">
				<div class="kt-widget17__item">
					<a title="<?php echo $this->lang->line("back"); ?>" role="button" href="javascript:loadcontent('main-content','<?php echo site_url( $this->router->fetch_directory() . "transaksi_idk/") ?>');" class="btn btn-brand btn-elevate btn-circle float-right ml-2"><i class="la la-arrow-left"></i> Kembali</a> &nbsp;&nbsp;
					<a title="<?php echo $this->lang->line("refresh"); ?>" role="button" href="javascript:loadcontent('main-content','<?php echo site_url( $this->router->fetch_directory() . "dashboard_master/index/pk/id/valpk/" . $this->session->userdata("idk")) ?>');" class="btn btn-brand btn-elevate btn-circle float-right"><i class="la la-refresh"></i> Segarkan</a>
					<h4><?php echo $this->lang->line("dashboard_rincian"); ?></h4>
					<?php echo $satker . " - " . $tahun; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row mt-3">
	<?php 
		$isOdd = false;
		$background = array(
			'SWAKELOLA' => "bl.png",
			'PENYEDIA' => "btl.png",
		);
		foreach($jenisPaket as $jp){ 
			$dataurl = "data-url='" . site_url( $this->router->fetch_directory() . "transaksi_master/index/type/" . $jp->ID) . "'";
	?>
	<div class="col-md-6 dashboard-item" <?php echo $dataurl; ?>>
		<!--begin:: Widgets/Order Statistics-->
		<div class="kt-portlet kt-portlet--height-fluid"  style="position:relative;overflow:hidden;color:#000;">
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<h3 class="kt-portlet__head-title text-white">
						<img src="<?php echo base_url("assets/img/icon/" . $background[$jp->ID]); ?>" style="width: 30px;" > &nbsp; <?php echo $jp->NAMA; ?>
					</h3>
				</div>
			</div>
			<div class="kt-portlet__body kt-portlet__body--fluid"  style="background: url('<?php echo base_url("assets/img/background.jpg") ?>'); background-size: 100%">
				<div class="kt-widget12">
					<div class="kt-widget12__content">
						<div class="kt-widget12__item">
							<div class="kt-widget12__info">
								<!-- <span class="kt-widget12__desc">Total Laporan Kecamatan</span> -->
								<span class="kt-widget12__value text-right"><h2 class=""><?php echo number_format($total[$jp->ID], 2, ",","."); ?></h2></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end:: Widgets/Order Statistics-->
	</div>
<?php } ?>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$(".dashboard-item").each(function(e){
			if( $(this).attr("data-url") != undefined ){
				$(this).css("cursor", "pointer");
			}
		});
		$(".dashboard-item").click(function(){
			var url = $(this).attr("data-url");
			if( url != undefined ){
				loadcontent("main-content", url);
			}
		});
	});
</script>
<?php echo $footer; ?>