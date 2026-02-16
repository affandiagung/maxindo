<div class="card card-custom gutter-bs card-stretch card-shadowless">
	<!--begin::Header-->
	<div class="card-header card-header-tabs-line">
		<table class="table table-condensed table-border slave-header">
		    <tbody>
		        <tr>
		            <td style="width: 150px;"><strong>Nama</strong></td>
		            <td style="width: 2px;"><strong>:</strong></td>
		            <td><?php echo $customer->NAME ?></td>
		        </tr>
		        <tr>
		            <td style="width: 150px;"><strong>Alamat</strong></td>
		            <td style="width: 2px;"><strong>:</strong></td>
		            <td><?php echo $customer->ADDRESS ?></td>
		        </tr>
		        <tr>
		            <td style="width: 150px;"><strong>Jenis Kostumer</strong></td>
		            <td style="width: 2px;"><strong>:</strong></td>
		            <td><?php echo $customer->CUSTOMERTYPE ?></td>
		        </tr>
		        <tr>
		            <td style="width: 150px;"><strong>Tgl. Daftar</strong></td>
		            <td style="width: 2px;"><strong>:</strong></td>
		            <td><?php echo $customer->REGISTERDATE ?></td>
		        </tr>
		    </tbody>
		</table>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body" id="customer_detail">
		
	</div>
	<!--end::Body-->
</div>

<script>
$(function(){
	$("#customer_detail").load(site_url + "admin/customer_detail/browse");
});
</script>