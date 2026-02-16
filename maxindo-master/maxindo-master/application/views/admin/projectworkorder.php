<div class="card card-custom gutter-b example example-compact">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-list me-5"></i>Work Order / WO</h3>
    <div class="card-toolbar">
      <div class="example-tools justify-content-center">
        <div class="btn-no-group">
          <a title="Segarkan" role="button" href="javascript:loadcontent('project_workorder','<?php echo site_url(); ?>/admin/project_workorder/');" class="btn  btn-light-primary btn-browse me-2"><i class="fa fa-sync-alt"></i> Segarkan</a>
        </div>
      </div>
    </div>
  </div>
  <div class="card-body px-5">
		<strong>New Wo (MR)</strong><br />
		<br />
		Nama Perusahaan : <?php echo $projects->CUSTOMERNAME; ?><br />
		Nama Penyewa : <?php echo $projects->CUSTOMERCONTACTNAME; ?><br />
		No Hp : <?php echo $projects->CUSTOMERCONTACTPHONE; ?><br />
		<br />
		Barang yg diorder :<br />
		<?php echo $barangs; ?><br />
		<br />
		<?php echo $subrent; ?><br />
		<br />
		Lokasi, Ruangan : <?php echo $this->Mmasterdata->splitLokasi($projects->PROJECTLOCATION) . ", " .$projects->PROJECTROOM; ?><br />
		Tgl & Lama sewa : <?php echo datetime_to_ID($projects->PROJECTSTART); ?> s/d <?php echo datetime_to_ID($projects->PROJECTEND); ?><br />
		Tgl & Jam Setup : <?php echo datetime_to_ID($projects->SETUPDATE); ?><br />
		Tgl & Jam Clear Area : <?php echo $projects->CLEARDATE; ?><br />
		Tgl & Jam GR : <?php echo $projects->GRDATE; ?><br />
		Tgl & Jam Bongkar : <?php echo datetime_to_ID($projects->DISPLACEDATE); ?><br />
		<br />
		Jenis Acara : <?php echo $projects->EVENTTYPE; ?><br />
		Source Listrik : <?php echo $projects->ELECTRICITYSOURCE; ?><br />
		Area Acara (PxL) :<?php echo $projects->AREASIZE; ?><br />
		<br />
		PIC Lapangan (Client)<br />
		Nama 1 : <?php echo $projects->CLIENTPICNAME1; ?><br />
		No HP 1: <?php echo $projects->CLIENTPICNUMBER1; ?><br />
		Nama 2 : <?php echo $projects->CLIENTPICNAME2; ?><br />
		No Hp 2: <?php echo $projects->CLIENTPICNUMBER2; ?><br />
		<br />
		Note : <?php echo $projects->PROJECTNOTES; ?><br />
	</div>
</div>
