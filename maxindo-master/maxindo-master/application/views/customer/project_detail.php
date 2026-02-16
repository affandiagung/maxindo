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
					<a class="nav-link" data-bs-toggle="tab" href="#project_quotation_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-list fa-2x text-success"></i>
						</span>
						<span class="nav-text font-weight-bold">Quotation</span>
					</a>
				</li>
				<?php /*
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_member_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-users fa-2x text-danger"></i>
						</span>
						<span class="nav-text font-weight-bold">Member</span>
					</a>
				</li>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_inventory_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-box fa-2x text-info"></i>
						</span>
						<span class="nav-text font-weight-bold">Inventory</span>
					</a>
				</li>
				*/ ?>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_activity_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-credit-card fa-2x text-primary"></i>
						</span>
						<span class="nav-text font-weight-bold">Activity</span>
					</a>
				</li>
				<?php /*
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_cost_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-dollar-sign fa-2x text-dark"></i>
						</span>
						<span class="nav-text font-weight-bold">Cost</span>
					</a>
				</li>
				*/ ?>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_invoice_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-file fa-2x text-secondary"></i>
						</span>
						<span class="nav-text font-weight-bold">Invoice & Payment</span>
					</a>
				</li>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_rating_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-star fa-2x text-primary"></i>
						</span>
						<span class="nav-text font-weight-bold">Rating & Review</span>
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
				<div id="project_detail_followup"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_detail_followup").load(site_url + "/customer/project_detail_followup");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_quotation_tab" role="tabpanel">
				<div id="project_quotation"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_quotation").load(site_url + "/customer/project_quotation");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_activity_tab" role="tabpanel">
				<div id="project_activity"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_activity").load(site_url + "/customer/project_activity");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_invoice_tab" role="tabpanel">
				<div id="project_invoice"></div>
			</div>
			<div class="tab-pane" id="project_rating_tab" role="tabpanel">
				<div id="project_rating"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_rating").load(site_url + "/customer/project_rating");
					});
				</script>
			</div>
		</div>
	</div>
	<!--end::Body-->
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("#restra_tab li a[href='#project_program_master_tab']").tab("dispose");
	});
</script>