<?php $logged = $this->session->userdata("admin"); ?>
<div class="card card-custom gutter-bs card-stretch mb-3">
	<div class="card-body">
		<h3 class="text-primary"><span class='badge badge-<?php echo $project->CLASS ?>'><?php echo $project->PROJECTSTAGENAME; ?></span> <?php echo "[ ".$project->PROJECTORDERCODE." ] ".$project->NAME; ?></h3>
		<span class='badge badge-danger'><?php echo datetime_to_ID($project->PROJECTSTART) . " - " . datetime_to_ID($project->PROJECTEND); ?></span>
		<span style="cursor:pointer; "id="CUSTOMERDATA" data-value="<?php echo $project->CUSTOMER;?>" class='badge badge-success'><?php echo $project->CUSTOMERNAME; ?></span>
		<span class='badge badge-info'>Marketing : <?php echo $project->EMPLOYEENAME; ?></span>
		<?php if( ($PRIVILEGE == "MRK" && $project->PROJECTSTAGE < 4) || $PRIVILEGE == "ADM") { ?>
		<div class="form-group row mt-5">
			<label for="PROJECTSTAGE" class="col-form-label"><?php echo $this->lang->line("PROJECTSTAGE");?></label>
			<div class="input-group">
				<div class="col-md-3">
					<select name="projectstage-select" id="projectstage-select" class="form-select">
						<?php foreach($projectstages as $ps){
						$selected = ($project->PROJECTSTAGE == $ps['keydt']) ? "selected" : null;
						echo "<option ".$selected." value='".$ps['keydt']."'>".$ps['valuedt']."</option>";
						} ?>
					</select>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<div class="card card-custom gutter-bs card-stretch card-shadowless">
	<!--begin::Header-->
	<div class="card-header card-header-tabs-line">
		<div class="card-toolbar">
			<ul id="project_tab" class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
			<?php if( $PRIVILEGE == "EMP" ){ ?>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_wo_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-list fa-2x text-primary"></i>
						</span>
						<span class="nav-text font-weight-bold">Work Order / WO</span>
					</a>
				</li>
				<li class="nav-item me-3">
					<a class="nav-link active" data-bs-toggle="tab" href="#project_activity_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-credit-card fa-2x text-primary"></i>
						</span>
						<span class="nav-text font-weight-bold">Activity</span>
					</a>
				</li>
				<?php if (!$project->DRYRENT): ?>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_member_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-users fa-2x text-danger"></i>
						</span>
						<span class="nav-text font-weight-bold">Member</span>
					</a>
				</li>
				<?php endif ?>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_inventory_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-box fa-2x text-info"></i>
						</span>
						<span class="nav-text font-weight-bold">Inventory</span>
					</a>
				</li>
			<?php } elseif($PRIVILEGE == "MRK") { ?>
				<li class="nav-item me-3">
					<a class="nav-link active" data-bs-toggle="tab" href="#project_master_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-exchange-alt fa-2x text-success"></i>
						</span>
						<span class="nav-text font-weight-bold">Project</span>
					</a>
				</li>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_followup_tab">
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
				<?php if (!$project->DRYRENT): ?>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_member_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-users fa-2x text-danger"></i>
						</span>
						<span class="nav-text font-weight-bold">Member</span>
					</a>
				</li>
				<?php endif ?>
			<?php } else { ?>
				<li class="nav-item me-3">
					<a class="nav-link active" data-bs-toggle="tab" href="#project_master_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-exchange-alt fa-2x text-success"></i>
						</span>
						<span class="nav-text font-weight-bold">Project</span>
					</a>
				</li>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_followup_tab">
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
				<?php if (!$project->DRYRENT): ?>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_member_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-users fa-2x text-danger"></i>
						</span>
						<span class="nav-text font-weight-bold">Member</span>
					</a>
				</li>
				<?php endif ?>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_inventory_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-box fa-2x text-info"></i>
						</span>
						<span class="nav-text font-weight-bold">Inventory</span>
					</a>
				</li>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_wo_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-list fa-2x text-primary"></i>
						</span>
						<span class="nav-text font-weight-bold">Work Order / WO</span>
					</a>
				</li>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_activity_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-credit-card fa-2x text-primary"></i>
						</span>
						<span class="nav-text font-weight-bold">Activity</span>
					</a>
				</li>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_cost_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-dollar-sign fa-2x text-dark"></i>
						</span>
						<span class="nav-text font-weight-bold">Cost</span>
					</a>
				</li>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_invoice_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-file fa-2x text-secondary"></i>
						</span>
						<span class="nav-text font-weight-bold">Invoice</span>
					</a>
				</li>
				<li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_subrent_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-code-branch fa-2x text-warning"></i>
						</span>
						<span class="nav-text font-weight-bold">PO / Subrent</span>
					</a>
				</li>
				<!-- <li class="nav-item me-3">
					<a class="nav-link" data-bs-toggle="tab" href="#project_rating_tab">
						<span class="nav-icon me-1">
							<i class="fa fa-star fa-2x text-primary"></i>
						</span>
						<span class="nav-text font-weight-bold">Rating</span>
					</a>
				</li> -->
			<?php } ?>
				
			</ul>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body">
		<div class="tab-content">
			<?php if( $PRIVILEGE == "EMP" ){ ?>
			<div class="tab-pane active" id="project_activity_tab" role="tabpanel">
				<div id="quick_action"></div>
				<div id="project_activity"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_activity").load(site_url + "/admin/project_activity");
						$("#quick_action").load(site_url + "/admin/project_activity/quick_action");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_wo_tab" role="tabpanel">
				<div id="project_workorder"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_workorder").load(site_url + "/admin/project_workorder");
					});
				</script>
			</div>
			<?php if (!$project->DRYRENT): ?>
			<div class="tab-pane" id="project_member_tab" role="tabpanel">
				<div id="project_member"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_member").load(site_url + "/admin/project_member");
					});
				</script>
			</div>
			<?php endif ?>
			<div class="tab-pane" id="project_inventory_tab" role="tabpanel">
				<div id="project_inventory"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_inventory").load(site_url + "/admin/project_inventory");
					});
				</script>
			</div>
			<?php } elseif($PRIVILEGE == "MRK") { ?>
			<div class="tab-pane active" id="project_master_tab" role="tabpanel">
				<div id="project_master"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_master").load(site_url + "/admin/project/edit/pk/PROJECTID/valpk/<?php echo $project->PROJECTID; ?>");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_followup_tab" role="tabpanel">
				<div id="project_detail_followup"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_detail_followup").load(site_url + "/admin/project_detail_followup");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_quotation_tab" role="tabpanel">
				<div id="project_quotation"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_quotation").load(site_url + "/admin/project_quotation");
					});
				</script>
			</div>
			<?php if (!$project->DRYRENT): ?>
			<div class="tab-pane" id="project_member_tab" role="tabpanel">
				<div id="project_member"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_member").load(site_url + "/admin/project_member");
					});
				</script>
			</div>
			<?php endif ?>
			<?php } else { ?>
			<div class="tab-pane active" id="project_master_tab" role="tabpanel">
				<div id="project_master"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_master").load(site_url + "/admin/project/edit/pk/PROJECTID/valpk/<?php echo $project->PROJECTID; ?>");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_followup_tab" role="tabpanel">
				<div id="project_detail_followup"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_detail_followup").load(site_url + "/admin/project_detail_followup");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_quotation_tab" role="tabpanel">
				<div id="project_quotation"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_quotation").load(site_url + "/admin/project_quotation");
					});
				</script>
			</div>
			<?php if (!$project->DRYRENT): ?>
			<div class="tab-pane" id="project_member_tab" role="tabpanel">
				<div id="project_member"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_member").load(site_url + "/admin/project_member");
					});
				</script>
			</div>
			<?php endif ?>
			<div class="tab-pane" id="project_inventory_tab" role="tabpanel">
				<div id="project_inventory"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_inventory").load(site_url + "/admin/project_inventory");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_wo_tab" role="tabpanel">
				<div id="project_workorder"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_workorder").load(site_url + "/admin/project_workorder");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_activity_tab" role="tabpanel">
				<div id="project_activity"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_activity").load(site_url + "/admin/project_activity");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_cost_tab" role="tabpanel">
				<div id="project_cost"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_cost").load(site_url + "/admin/project_cost");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_invoice_tab" role="tabpanel">
				<div id="project_invoice_master"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_invoice_master").load(site_url + "/admin/project_invoice_master");
					});
				</script>
			</div>
			<div class="tab-pane" id="project_subrent_tab" role="tabpanel">
				<div id="project_subrent"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_subrent").load(site_url + "/admin/project_subrent");
					});
				</script>
			</div>
			<!-- <div class="tab-pane" id="project_rating_tab" role="tabpanel">
				<div id="project_rating"></div>
				<script type="text/javascript">
					$(function(){
						$("#project_rating").load(site_url + "/admin/project_rating");
					});
				</script>
			</div> -->
		</div>
		<?php } ?>
	</div>
	<!--end::Body-->
</div>
<script type="text/javascript">
	
	$(document).ready(function(){
		<?php if($PRIVILEGE == "MRK" || $PRIVILEGE == "ADM") { ?>

		$("#projectstage-select").on('focusin', function(){
		    $(this).data('val', $(this).val());
		}).on('change' , function(){
			var old_state = $(this).data('val');
			var target = site_url + 'admin/project_detail/updateStage/';
			var datapost = {
				stage: $(this).val()
			}
			$.post( target, datapost, function(e){
				console.log(e);
				swal.fire({
					icon: e.type,
					title: e.message,
					showConfirmButton: !1,
					timer: 1500
				});
				if (!e.status) {
					$('#projectstage-select').val(old_state);
				}
			},'json');
		});
		<?php } ?>
		$('#popupModal .modal-dialog').addClass('modal-fullscreen');
		$('#popupModal .modal-dialog').removeClass('modal-xl');
		$('#popupModal').on('hidden.bs.modal', function(){
			var select2_parent = $(document.body);
			$('#popupModal .modal-dialog').addClass('modal-xl');
			$('#popupModal .modal-dialog').removeClass('modal-fullscreen');
		});

		$("#CUSTOMERDATA").on('click',function(){
			var customerid = $(this).attr('data-value');
			loadinputmodal(site_url + 'admin/customer_detail/index/pk/CUSTOMERID/valpk/' + customerid);
			
		});
	});
</script>