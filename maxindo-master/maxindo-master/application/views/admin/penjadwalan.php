<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title"><i class="fa fa-list me-5"></i>Penjadwalan</h3>
		<div class="card-toolbar">
			<div class="example-tools justify-content-center">
				<div class="btn-no-group">
					<a title="Segarkan" role="button" href="javascript:loadcontent('main-content','<?php echo site_url(); ?>/admin/penjadwalan/');" class="btn  btn-light-primary btn-browse me-2"><i class="fa fa-sync-alt"></i> Segarkan</a>
				</div>
			</div>
		</div>
	</div>
	<div class="card-body px-5">
		<!-- <form action="" class="form-inline" method="POST" id="dsb_form">
			<div class="row mb-5">
				<div class="col-md-4">
  				<div class="form-group row">
  					<label class="col-md-4 col-form-label" for="TGLAWAL">Dari Tanggal</label>
						<div class="col-md-8">
  						<div class="input-group">
  							<input type="text" class="date-picker form-control" name="TGLAWAL" id="TGLAWAL" value="<?php echo $TGLAWAL; ?>" />
								<span class="input-group-text"><i class="fa fa-calendar"></i></span>
  						</div>
  					</div>
  				</div>
				</div>
				<div class="col-md-4">
  				<div class="form-group row">
  					<label class="col-md-4 col-form-label" for="TGLAKHIR">s/d Tanggal</label>
						<div class="col-md-8">
  						<div class="input-group">
  							<input type="text" class="date-picker form-control" name="TGLAKHIR" id="TGLAKHIR" value="<?php echo $TGLAKHIR; ?>" />
								<span class="input-group-text"><i class="fa fa-calendar"></i></span>
  						</div>
  					</div>
  				</div>
				</div>
				<div class="col-md-4">
					<button type="submit" class="btn btn-primary">Tampilkan</button>
				</div>
			</div>
		</form> -->
		<div class="row">
			<div class="container">
				<div class="table-responsive">
					<table class="table">
						
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		$(".date-picker").flatpickr();
		$("#dsb_form").submit(function(){
			let target = site_url + "admin/dashboard/";
			let datapost = $(this).serialize();
			loadpost("main-content",target, datapost);
			return false;
		});
	});
</script>