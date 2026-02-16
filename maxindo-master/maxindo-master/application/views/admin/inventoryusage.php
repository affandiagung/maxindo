<style type="text/css">
	#usagetable th,
	#usagetable td
	{	
		border-bottom: 1px solid #ccc;
  	border-top: 1px solid #ccc;
	}
	#usagetable th{
		font-weight: bold;
	}
</style>
<h2><?php echo $inventory->NAME; ?></h2>
<h5><?php echo datetime_to_ID($start) . " s/d " . datetime_to_ID($end); ?></h5>
<div class="table-container">
	<table class="table table-bordered table-striped" id="usagetable">
		<thead>
			<tr>
				<th>No</th>
				<th>Project</th>
				<th>Jumlah</th>
				<th>SQM</th>
				<th>Account Manager</th>
				<th>Stage</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$no=1;
			$total=0;
			foreach($usage as $use){ ?>
			<tr>
				<td><?php echo $no ?>.</td>
				<td><?php echo "<strong>" . $use->CUSTOMER . "</strong><br />" . $use->PROJECTLOCATION; ?></td>
				<td><?php echo $use->USEDCOUNT ?></td>
				<td><?php echo round($use->SQM,1) ?></td>
				<td><?php echo $use->EMPLOYEE ?></td>
				<td><?php echo $use->PROJECTSTAGE ?></td>
			</tr>
			<?php 
			$total += $use->USEDCOUNT;
			$no++; 
			}?>
		</tbody>
		<tfoot>
			<tr>
				<th></th>
				<th>Total Penggunaan</th>
				<th><?php echo number_format($total,0,",","."); ?></th>
				<th></th>
				<th></th>
			</tr>
			<tr>
				<th></th>
				<th>Total Item</th>
				<th><?php echo number_format($inventory->TOTITEM,0,",","."); ?></th>
				<th></th>
				<th></th>
			</tr>
			<tr>
				<th></th>
				<th>Sisa / Kekurangan</th>
				<th><?php echo number_format(($inventory->TOTITEM - $total),0,",","."); ?></th>
				<th></th>
				<th></th>
			</tr>
		</tfoot>
	</table>
</div>