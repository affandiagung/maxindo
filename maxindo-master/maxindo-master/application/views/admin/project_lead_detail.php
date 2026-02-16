<?php $logged = $this->session->userdata("admin"); ?>
<div class="card card-custom gutter-bs card-stretch mb-3">
	<div class="card-body">
		<h3 class="text-primary"><span class='badge badge-<?php echo $project->CLASS ?>'><?php echo $project->PROJECTSTAGENAME; ?></span> <?php echo "[ ".$project->PROJECTORDERCODE." ] ".$project->NAME; ?></h3>
		<span class='badge badge-danger'><?php echo datetime_to_ID($project->PROJECTSTART) . " - " . datetime_to_ID($project->PROJECTEND); ?></span>
		<span class='badge badge-success'><?php echo $project->CUSTOMERNAME; ?></span>
		<span class='badge badge-warning'>Marketing : <?php echo $project->EMPLOYEENAME; ?></span>
	</div>
</div>
<div class="card card-custom gutter-bs card-stretch card-shadowless">
	<!--begin::Header-->
	<div class="card-header card-header-tabs-line">
		<div class="card-toolbar">
			<ul id="project_tab" class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
				<li class="nav-item me-3">
					<a class="nav-link active" data-bs-toggle="tab" href="#project_followup_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-th fa-2x text-warning"></i>
						</span>
						<span class="nav-text font-weight-bold">Followup</span>
					</a>
				</li>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_lead_quotation_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-list fa-2x text-success"></i>
						</span>
						<span class="nav-text font-weight-bold">Quotation</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body">
		<div class="tab-content">
			<div class="tab-pane active" id="project_followup_tab" role="tabpanel">
				<div id="project_followup"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_followup").load(site_url + "/admin/project_followup");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_lead_quotation_tab" role="tabpanel">
				<div id="project_lead_quotation"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_lead_quotation").load(site_url + "/admin/project_quotation");
					});
				</script>
			</div>
		</div>
	</div>
	<!--end::Body-->
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('.modal-dialog').addClass('modal-fullscreen');
		$('.modal-dialog').removeClass('modal-xl');
		$('#engineModal').on('hidden.bs.modal', function(){
			$('.modal-dialog').addClass('modal-xl');
			$('.modal-dialog').removeClass('modal-fullscreen');
		});
	});
</script>