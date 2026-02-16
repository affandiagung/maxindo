<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Engine {

	private $str="";
	private $params=array();
	private $content="";
	private $filename="";
	private $urisegments="";
	private $namesplit=array();
	private $iscrypt=false;
	private $maincontent="engine-content";
	private $formid="myform";
	private $configurations = array();
	

	public function __construct($parameters){
		$this->params = $parameters;
		$this->ci = &get_instance();
		$this->filename = site_url($this->ci->router->fetch_directory().$this->ci->router->fetch_class());
		$this->urisegments=$this->ci->uri->uri_to_assoc(4);
		$this->configurations = $this->ci->db->get("configurations")->row_array();
		if( isset($this->params['urisegments']) ){
			$this->urisegments = $this->params['urisegments']; 
		}
		$this->iscrypt = $this->ci->config->item("encrypt_url");
		if( isset($this->params["maincontent"]) ){
			$this->maincontent = $this->params["maincontent"] . "-content";
		}
		if( isset($this->params["filename"]) ){
			$this->filename = $this->params["filename"];
		}
		if( isset($this->params["formid"]) ){
			$this->formid = "form_" . $this->params["formid"];
		} else {
			$this->formid = "form_" . $this->ci->router->fetch_class();
		}
	}

	public function mainfunction(){
		$header = $this->ci->config->item("header_container");
		$header = str_replace("[home]", site_url(), $header);
		$header = str_replace("[name]", $this->params['name'], $header);
		if( @$this->params['simpleform'] == false ){
			$this->str = $header
		  . $this->params["content"]
			. @$this->params['jsinclude']
			. $this->ci->config->item("footer_container");
		} else {
			$this->str = $this->params["content"] . @$this->params['jsinclude'];
		}
		$this->str = "<div id='".$this->maincontent."' class='h-100'>".$this->str."</div>";
	}

	public function browse(){
		$p_search = isset($_POST["p_search"]) ? $_POST["p_search"] : "";
		$this->params['content'] = "";
		$_primary="";
		foreach(array_keys($this->params['fieldselect']) as $_cols){
			if(@$this->params['fieldselect'][$_cols]['type']=="primarykey"){
				$_primary=$_cols;
				break;
			}
		}
		$command=explode(",",$this->params['command']);
		$_cmdtext="<div class='btn-no-group'>";
		if( isset($this->params['buttontitle']) && $this->params['buttontitle'] == false ){
			$_cmdtext.=(in_array("browse",$command)) ? "<a title='".$this->ci->lang->line("refresh")."' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn  btn-light-primary btn-browse btn-icon me-2'><i class='fa fa-sync-alt'></i></a>" : '' ;
			$_cmdtext.=(in_array("add",$command)) ? "<a title=''".$this->ci->lang->line("add")."'' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/add/\");' class='btn  btn-primary btn-add btn-icon me-2'><i class='fa fa-plus'></i></a>" : '' ;
			$_cmdtext.=(in_array("inlineadd",$command)) ? "<a title=''".$this->ci->lang->line("inlineadd")."'' role='button' href='javascript:showInlineAdd(\"".$this->maincontent."\");' class='btn  btn-primary btn-add btn-icon me-2'><i class='fa fa-plus'></i></a>" : '' ;
			$_cmdtext.=(in_array("search",$command)) ? "<a title=''".$this->ci->lang->line("search")."'' role='button' href='javascript:opensearch()' class='btn  btn-light-primary btn-search btn-icon me-2'><i class='fa fa-filter'></i></a>" : '' ;
			$_cmdtext.=(in_array("generate",$command)) ? "<a title=''".$this->ci->lang->line("generate")."'' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/generate/\");' class='btn  btn-light-primary btn-generate btn-icon me-2'><i class='fa fa-cog'></i></a>" : '' ;
			$_cmdtext.=(in_array("import",$command)) ? "<form method='post' id='importForm_".$this->maincontent."' class='float-start'><input type='hidden' name='truncate' id='truncate' class='d-none' value='0'><input type='file' name='importFile' id='importFile_".$this->maincontent."' class='d-none' /></form><a title='".(isset($this->params['cmd']['import']['title']) ? $this->params['cmd']['import']['title'] : $this->ci->lang->line("import"))."' role='button' href='#javascript:void();' onclick='$(\"#importFile_".$this->maincontent."\").click();' class='btn  btn-light-primary btn-import btn-icon me-2'> ".(isset($this->params['cmd']['import']['icon']) ? "<i class='".$this->params['cmd']['import']['icon']."'></i>" : "<i class='fa fa-upload'></i> " )."</a>" : '';
		} else {
			$_cmdtext.=(in_array("browse",$command)) ? "<a title='".$this->ci->lang->line("refresh")."' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn  btn-light-primary btn-browse me-2'><i class='fa fa-sync-alt'></i> ".$this->ci->lang->line("refresh")."</a>" : '' ;
			$_cmdtext.=(in_array("add",$command)) ? "<a title=''".$this->ci->lang->line("add")."'' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/add/\");' class='btn  btn-primary btn-add me-2'><i class='fa fa-plus'></i> ".$this->ci->lang->line("add")."</a>" : '' ;
			$_cmdtext.=(in_array("inlineadd",$command)) ? "<a title=''".$this->ci->lang->line("inlineadd")."'' role='button' href='javascript:showInlineAdd(\"".$this->maincontent."\");' class='btn  btn-primary btn-add me-2'><i class='fa fa-plus'></i> ".$this->ci->lang->line("inlineadd")."</a>" : '' ;
			$_cmdtext.=(in_array("search",$command)) ? "<a title=''".$this->ci->lang->line("search")."'' role='button' href='javascript:opensearch()' class='btn  btn-light-primary btn-search me-2'><i class='fa fa-filter'></i> ".$this->ci->lang->line("search")."</a>" : '' ;
			$_cmdtext.=(in_array("generate",$command)) ? "<a title=''".$this->ci->lang->line("generate")."'' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/generate/\");' class='btn  btn-light-primary btn-generate me-2'><i class='fa fa-cog'></i> ".$this->ci->lang->line("generate")."</a>" : '' ;
			$_cmdtext.=(in_array("import",$command)) ? "<form method='post' id='importForm_".$this->maincontent."' class='float-start'><input type='hidden' name='truncate' id='truncate' class='d-none' value='0'><input type='file' name='importFile' id='importFile_".$this->maincontent."' class='d-none' /></form><a title='".(isset($this->params['cmd']['import']['title']) ? $this->params['cmd']['import']['title'] : $this->ci->lang->line("import"))."' role='button' href='javascript:void();' onclick='$(\"#importFile_".$this->maincontent."\").click();' class='btn  btn-light-primary btn-import me-2'> ".(isset($this->params['cmd']['import']['icon']) ? "<i class='".$this->params['cmd']['import']['icon']."'></i>" : "<i class='fa fa-upload'></i> " )." ".(isset($this->params['cmd']['import']['title']) ? $this->params['cmd']['import']['title'] : $this->ci->lang->line("import"))."</a>" : '' ;
		}

		// if(
		// 	in_array("generate",$command)
		// 	|| in_array("search",$command)
		// ){
		// 	$_cmdtext .= "<div class='dropdown'>";
		// 	$_cmdtext .= "<button type='button' class='btn btn-light-primary dropdown-toggle' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
		// 								<i class='fa fa-cog'></i> Aksi
		// 							</button>";	
		// 	$_cmdtext.= "<div class='dropdown-menu dropdown-menu-right' x-placement='top-end' style='position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-66px, -228px, 0px);'>
		// 								<ul class='kt-nav'>
		// 									<li class='kt-nav__section kt-nav__section--first'>
		// 										<span class='kt-nav__section-text'>Pilih Aksi Lainnya</span>
		// 									</li>";
		// 	$_cmdtext.=(in_array("generate",$command)) ? "<li class='kt-nav__item'><a title='".$this->ci->lang->line("generate")."' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/generate/\");' class='navi-link'><span class='navi-icon'><i class='fa fa-cog'></i></span> <span class='navi-text'>".$this->ci->lang->line("generate")."</span></a></li>" : '' ;
		// 	$_cmdtext.=(in_array("search",$command)) ? "<li class='kt-nav__item'><a title='".$this->ci->lang->line("search")."' title='search' href='javascript:opensearch();' class='navi-link btn-search'><span clas='navi-icon'><i class='fa fa-search'></i></span> <span class='navi-text'> Filter</span></a></li>" : '' ;
		// 	$_cmdtext .= "</ul></div></div>";
		// }
		
		$_cmdtext.=(in_array("back",$command)) ? "<a title='".$this->ci->lang->line("back")."' title='back' href='".$this->params['cmd']['back']['url']."' class='btn btn-light-primary'><i class='fa fa-arrow-left'></i> Kembali</a>" : '' ;
		
		$customHead = false;
		$customHeadCommand = array();
		foreach($command as $cmd){
			if(substr($cmd,-5) == "_head"){
				$customHead = true;
				$customHeadCommand[$cmd] = $cmd;
			}
		}

		$customHeadDropdown = false;
		$customHeadDropdownCommand = array();
		foreach($command as $cmd){
			if(substr($cmd,-9) == "_dropdown"){
				$customHeadDropdown = true;
				$customHeadDropdownCommand[$cmd] = $this->params['cmd'][$cmd];
			}
		}

		if($customHead == true){
			foreach($customHeadCommand as $ch){
				$class = "btn-light-primary";
				if( isset($this->params['cmd'][$ch]['class']) ){
					$class = $this->params['cmd'][$ch]['class'];
				}
				if(isset($this->params['buttontitle']) && $this->params['buttontitle'] == false){
					$_cmdtext.="<a title='".$this->ci->lang->line($ch)."' title='back' href='".$this->params['cmd'][$ch]['url']."' class='btn ".$class." btn-icon me-2'><i class='".$this->params['cmd'][$ch]['icon']."'></i></a>";
				} else {
					$_cmdtext.="<a title='".$this->ci->lang->line($ch)."' title='back' href='".$this->params['cmd'][$ch]['url']."' class='btn ".$class." me-2'><i class='".$this->params['cmd'][$ch]['icon']."'></i> ".$this->ci->lang->line($ch)."</a>";
				}
			}
		}

		if($customHeadDropdown == true){
			foreach($customHeadDropdownCommand as $k => $ch){
				$buttontitle = ""; 
				if(isset($this->params['buttontitle']) && $this->params['buttontitle'] == false){
					$buttontitle = ""; 
				} else {
					$buttontitle = $this->ci->lang->line($k);
				}
				$_cmdtext .= "<div class='dropdown dropdown-inline'>";
				$_cmdtext .= "<button type='button' class='btn btn-light-primary dropdown-toggle ' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
											<i class='fa fa-list'></i> ".$buttontitle."
										</button>";	
				$_cmdtext .= "<div class='dropdown-menu dropdown-menu-sm dropdown-menu-right' x-placement='top-end' style='position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-66px, -228px, 0px);'>
											<ul class='navi flex-column navi-hover py-2'>";
				foreach( $ch as $k => $cmd ){
					if( isset($cmd['items']) ){ // ada submenu
						$_cmdtext .= "<li class='navi-item dropdown-submenu'>
														<a href='javascript:void();' class='dropdown-toggle' data-bs-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'> <span class='nav-label'>".$this->ci->lang->line($k)."</span><span class='caret'></span></a>
                    					<ul class='dropdown-menu'>";

						$_cmdtext	.= "</li>";
					} else {
						$_cmdtext .= "<li class='navi-item'><a role='button' href='".$cmd['url']."' class='navi-link'><span class='navi-icon'><i class='".$cmd['icon']."'></i></span> <span class='navi-text'>".$this->ci->lang->line($k)."</span></a></li>";
					}
					
				}
				$_cmdtext .= "</ul></div></div>";
			}
		}

		$allCommand = false;
		foreach($command as $cmd){
			if(substr($cmd,-3) == "all"){
				$allCommand = true;
			}
		}
		if($allCommand = true || in_array("copy", $command) || in_array("paste", $command) ){
			$_cmdtext .= '<div class="dropdown float-end" id="bulk_'.$this->maincontent.'">
			  <button class="btn btn-light-primary dropdown-toggle" type="button" id="mass_action_'.$this->maincontent.'" data-bs-toggle="dropdown" aria-expanded="false">
			    '.$this->ci->lang->line("mass_action").'
			  </button>
			  <ul class="dropdown-menu mass-action" aria-labelledby="mass_action_'.$this->maincontent.'">';
			$_cmdtext.=(in_array("updateall",$command)) ? "<li><a role='button' href='javascript:updateall(\"".$this->maincontent."\",\"".$this->filename."/updateall/\",$(\".checkbox-data--".$this->maincontent."\"));' class='dropdown-item p-3'><i class='me-3 far fa-edit'></i>".(isset($this->params['updateall']['caption']) ? $this->params['updateall']['caption'] : "Update Terpilih")."</a></li>" : '' ;
			$_cmdtext.=(in_array("duplicateall",$command)) ? "<li><a role='button' href='javascript:updateall(\"".$this->maincontent."\",\"".$this->filename."/duplicateall/\",$(\".checkbox-data--".$this->maincontent."\"));' class='dropdown-item p-3'><i class='me-3 fa fa-copy'></i>".(isset($this->params['duplicateall']['caption']) ? $this->params['duplicateall']['caption'] : "Duplicate Terpilih")."</a></li>" : '' ;
			$_cmdtext.=(in_array("deleteall",$command)) ? "<li><a role='button' href='javascript:deleteall(\"".$this->maincontent."\",\"".$this->filename."/deleteall/\",$(\".checkbox-data--".$this->maincontent."\"));' class='dropdown-item p-3 text-danger'><i class='me-3 fa fa-trash text-danger'></i>".(isset($this->params['deleteall']['caption']) ? $this->params['deleteall']['caption'] : "Hapus Terpilih")."</a></li>" : '' ;
			$_cmdtext.=(in_array("printall",$command)) ? "<li><a role='button' href='javascript:printall(\"".$this->maincontent."\",\"".$this->filename."/printall/\",$(\".checkbox-data--".$this->maincontent."\"));' class='dropdown-item p-3'><i class='me-3 fa fa-print'></i>Print Terpilih</a></li>" : '' ;
			$_cmdtext.=(in_array("copy",$command)) ? "<li><a role='button' title='".$this->ci->lang->line("copy")."' href='javascript:copy(\"".$this->maincontent."\",\"".$this->filename."/copy/\",$(\".checkbox-data--".$this->maincontent."\"));' class='dropdown-item p-3 btn-copy'><i class='me-3 fa fa-copy text-info'></i>Copy Terpilih</a></li>" : '' ;
			$_cmdtext.=(in_array("paste",$command)) ? "<li><a role='button' title='".$this->ci->lang->line("paste")."' href='javascript:paste(\"".$this->maincontent."\",\"".$this->filename."/paste/\");' class='dropdown-item p-3 btn-paste'><i class='me-3 fa fa-paste text-info'></i>Paste Terpilih</a></li>" : '' ;
			
			// echo "<pre>";print_r($this->params['cmd']);echo "</pre>";
			foreach($command as $cmd){
				if(substr($cmd,-3) == "all" && 
					$cmd != "updateall" &&
					$cmd != "duplicateall" &&
					$cmd != "deleteall" &&
					$cmd != "printall"
				){	
					// echo $cmd . " | ";
					$_cmdtext.= "<li><a role='button' href='".$this->params['cmd'][$cmd]['url']."' class='dropdown-item p-3'><i class='me-3 ".$this->params['cmd'][$cmd]['icon']."'></i>".$this->ci->lang->line($cmd)."</a></li>";
				}
			}
			$_cmdtext .= "</ul></div>";
			// $this->params['content'].= '<div class="btn btn-no-group">'.$_cmdbuttom.'</div>';
		}
		$_cmdtext.= '</div>';

		if(in_array("import",$command)){
			$message = isset($this->params['cmd']['import']['warning_text']) ? $this->params['cmd']['import']['warning_text'] : "Pastikan File dan format import sudah sesuai, Apakah Anda yakin ?";
			$this->params['content'] .="
				<script type='text/javascript'>
					$(function(){
						$('#importFile_".$this->maincontent."').on('change',function(){
							swal.fire({
						    title: 'Konfirmasi',
						    text: '".$message."',
						    icon: 'warning',
						    showCancelButton: !0,
						    confirmButtonText: 'Ya',
						    cancelButtonText: 'Tidak',
						    reverseButtons: !0
						  }).then(function(e) {
						    if(e.value) {
						    	$('#importForm_".$this->maincontent."').submit();
						    } 
						    else {
						      e.dismiss;
						    }
						  });
						});

						$('#importForm_".$this->maincontent."').on('submit', function(event){
							event.preventDefault();
							$('#".$this->maincontent."').html('<div class=\"text-center\"><img src=\"' + base_url + 'assets/img/spin.svg\" /></div>');
							$.ajax({  
                url:'".$this->filename."/import',  
                method:'POST',  
                data:new FormData(this), 
                contentType:false,  
                processData:false,  
                success:function( e ){
                	if( e.status == true ){
	                	swal.fire({
							        icon: 'success',
							        title: e.message,
							        showConfirmButton: !1,
							        timer: 1500
							      });
                	} else {
                		swal.fire({
							        icon: 'error',
							        title: 'Import Gagal : ' + e.message,
							        showConfirmButton: !1,
							        timer: 1500
							      });
                	}
                	loadcontent('".$this->maincontent."','".$this->filename."');
									".@$this->params['inlineeditcallback']."
                },
           		}); 
						});
					});
				</script>		
			";
		}
		$alert = "";
		if(isset($this->params['alert'])){
			$alert = '<div class="alert alert-'.@$this->params["alert"]["type"].'">
				<div class="alert-text">
					'.@$this->params['alert']['message'].'
				</div>
			</div>';
		}

		$search = "";
		if(isset($this->params['search']) && count($this->params['search'])>0){
			$search.="<div class='searchpanel'><form class='form search-form' id='searchform'><div class='row'>";
			$counter=1;
			foreach(array_keys($this->params['search']) as $_search){
				$addon = "";
				if(isset($this->params['search'][$_search]['help'])){
					$addon = "<span class='input-group-text'>".$this->params['fieldadd'][$_keyadd]['help']."</span>";
				}
				$_type=(!isset($this->params['search'][$_search]['type'])) ? "text" : $this->params['search'][$_search]['type'] ;
				$dateclass="";
				$append="";
				$value="";
				if(isset($_POST[$_search])){
					$value = $_POST[$_search];
				}
				else {
					if(isset($this->params['search'][$_search]['value'])){
						$value = $this->params['search'][$_search]['value'];
					}
				}

				if($_type=="dropdownquery"){
					$option="";
					$selected="";
					$optgroup="";
					foreach($this->params['search'][$_search]['sourcequery'] as $dropdown){
						if(isset($dropdown['labeldt'])){
							if($optgroup != $dropdown['labeldt']){
								$option.="<optgroup label='".$dropdown['labeldt']."'>";
								$optgroup = $dropdown['labeldt'];
							}
						}
						$selected = ($value==$dropdown['keydt']) ? "selected" : null;
						$option.="<option ".$selected." value='".$dropdown['keydt']."'>".$dropdown['valuedt']."</option>";
					}
					$search.="<div class='form-group col-md-4 mb-3'>
						<label for='".$_search."' class='col-form-label'>".$this->ci->lang->line($_search)."</label>
						<div class='".@$this->params['search'][$_search]['class']."'>
							<div class='input-group'>
								<select name='".$_search."' id='".$_search."' class='form-select'>
									".$option."
								</select>
								".$addon."
							</div>
						</div>
					</div>";
				}
				elseif($_type=="dropdownarray"){
					$option="";
					foreach(array_keys($this->params['search'][$_search]['sourcearray']) as $opdata){
						$selected=($opdata==$value) ? "selected" : null ;
						$option.="<option ".$selected." value='".$opdata."'>".$this->params['search'][$_search]['sourcearray'][$opdata]."</option>";
					}
					$search.="<div class='form-group col-md-4 mb-3'>
						<label for='".$_search."' class='col-form-label'>".$this->ci->lang->line($_search)."</label>
						<div class='".@$this->params['search'][$_search]['class']."'>
							<div class='input-group'>
								<select name='".$_search."' id='".$_search."' class='form-select'>
									".$option."
								</select>
								".$addon."
							</div>
						</div>
					</div>";
				}
				else {
					// $dateclass = "";
					if($_type=="date"){
						$_type="text";
						$dateclass="date-picker";
						$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
					}
					elseif($_type=="month"){
						$_type="text";
						$dateclass="month-picker";
						$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
					}
					elseif($_type=="time"){
						$dateclass="time-picker";
						$append="<span class='input-group-text'><i class='fas fa-clock'></i></span>";
					}
					$search.="<div class='form-group col-md-4 mb-3'>
							<label for='".$_search."' class='col-form-label'>".$this->ci->lang->line($_search)." : </label>
							<div class='".$dateclass." ".@$this->params['search'][$_search]['class']."'>
								<div class='input-group'>
									<input value='".$value."' type='".$_type."' name='".$_search."' id='".$_search."' placeholder='".@$this->params['search'][$_search]['placeholder']."' ".@$this->params['search'][$_search]['is_required']." class='form-control' maxlength='".@$this->params['search'][$_search]['maxlength']."' />".$append."
									".$addon."
								</div>
							</div>
						</div>";
				}

			}
			$search.="<div class='form-group col-md-3 mt-5'><button type='submit' class='btn btn-light-primary btn-sm'> ".$this->ci->lang->line("apply_filter")." </button> <button type='button' onclick='loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn btn-danger btn-sm'>".$this->ci->lang->line("reset")."</button></div>";
			$search.='</div></form></div>
			<script type="text/javascript">
				$(function(){
					$(".select2 > .input-group > select").select2({
						width: "100%",
						placeholder: $(this).attr("placeholder"),
						allowClear: Boolean($(this).data("allow-clear")),
					});
					$(".date-picker input").flatpickr({
						dateFormat: "Y-m-d",
						// disableMobile: true
						
					});
					$(".month-picker input").flatpickr({
						dateFormat: "Y-m",
					  minViewMode: 1,
					});
					$("#searchform").submit(function(){
						$("#'.$this->maincontent.'").html("<div class=\"text-center spin-loading\"><img src=\"" + base_url + "assets/img/spin.svg\" /></div>");
						var datapost=$(this).serialize();
						loadpost("'.$this->maincontent.'","'.$this->filename.'",datapost);
						return false;
					});
				});
			</script>
			';

		}

		if(isset($this->params['formheader']) && $this->params['formheader'] == false){
			$this->params['content'] .='
			<div class="card card-custom gutter-b example example-compact h-100">
				<div class="card-body px-1">';
		} else {
			$this->params['content'] .='
      <div class="card card-custom gutter-b example example-compact h-100">
        <div class="card-header">
          <h3 class="card-title"><i class="fa fa-list me-5"></i>' . $this->params['name'].'</h3>
          <div class="card-toolbar">
            <div class="example-tools justify-content-center">
            	'.$_cmdtext.'
            </div>
          </div>
        </div>
        <div class="card-body px-1">
	    	';
		}

		$this->params['content'] .= $search.'
	    	<div id="alert">'.$alert.'</div>
	      <div class="table-responsive h-100">';

    if( isset($this->params['view']) ){
    	$this->params['content'] .= $this->ci->load->view( $this->params['view'], $this->params, true);
    } else {
	    $this->params['content'] .='
					<table id="dataTable_'.$this->maincontent.'" class="table align-middle table-row-dashed no-footer table-striped table-bordered '.@$this->params['table-class'].'">';
			$this->params['content'].='<thead>';
			$columns = "";
			$sum = false;
			$exportCommand = (in_array("export",$command)) ? true : false;
			if( isset($this->params['customheader']) ){
				$this->params['content'].= $this->params['customheader'];
				foreach(array_keys($this->params['fieldselect']) as $keys){
					if(!@$this->params['fieldselect'][$keys]['hidden']){
						if( @$this->params['fieldselect'][$keys]['sum'] == true ){
							$sum = true;
						}
						$class = "";
						if(@$this->params['fieldselect'][$keys]['type'] == "number" || @$this->params['fieldselect'][$keys]['type'] == "decimal"){
							$class = @$this->params['fieldselect'][$keys]['class'] . " text-end";
						} else {
							$class = @$this->params['fieldselect'][$keys]['class'];
						}
						$classes = explode(" ", $class);
						$sorting = in_array("sorting", $classes) ? "" : "sorting_disabled";
						$orderable = in_array("sorting", $classes) ? '"orderable" : true, ' : '"orderable": false, ';

						if($keys=="SEQ"){
							$columns.='{ "data": "'.$keys.'", "orderable": false, "searchable": false, "class" : "'.$class.' col-seq", "width" : "20px" },
							';
						} elseif($keys=="#"){
							$columns.='{ "data": "'.$keys.'", "orderable": false, "searchable": false, "class" : "col-checkbox '.$class.'", "width" : "20px" },
							';
						} elseif(@$this->params['fieldselect'][$keys]['hidden'] != true) {
							$columns.='{ "data": "'.$keys.'", '.$orderable.' "class" : "'.$class.' '.$sorting.'", "width" : "'.@$this->params['fieldselect'][$keys]['width'].'" },
							';
						}
					}
				}
				
			} else {
				$this->params['content'] .= "<tr>";
				foreach(array_keys($this->params['fieldselect']) as $keys){
					if(!@$this->params['fieldselect'][$keys]['hidden']){
						if( @$this->params['fieldselect'][$keys]['sum'] == true ){
							$sum = true;
						}
						$class = "";
						if(@$this->params['fieldselect'][$keys]['type'] == "number" || @$this->params['fieldselect'][$keys]['type'] == "decimal"){
							$class = @$this->params['fieldselect'][$keys]['class'] . " text-end";
						} else {
							$class = @$this->params['fieldselect'][$keys]['class'];
						}
						$classes = explode(" ", $class);
						$sorting = in_array("sorting", $classes) ? "" : "sorting_disabled";
						$orderable = in_array("sorting", $classes) ? '"orderable" : true, ' : '"orderable": false, ';

						$description = "";
						
						if( isset($this->params['fieldselect'][$keys]['description']) ){
							$description = "<span><a href='javascript:return false;' class='popover' data-container='body' data-bs-toggle='popover' data-placement='top' data-title='".$this->ci->lang->line($keys)."' data-content='".$this->params['fieldselect'][$keys]['description']."'>
							  <span class='fa fa-question-circle'></span>
							</a></span>
							";
						}

						if($keys=="SEQ"){
							$this->params['content'].='<th class="text-center sorting_disabled col-seq  border-light-success" style="width:20px;">No</th>';
							$columns.='{ "data": "'.$keys.'", "orderable": false, "searchable": false, "class" : "'.$class.' col-seq  border-light-success", "width" : "20px" },
							';
						}
						elseif($keys=="#"){
							$this->params['content'].='<th class="text-center sorting_disabled" style="width:20px;">
									<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
									<input type="checkbox" value="" class="form-check-input checkbox-header--'.$this->maincontent.'" id="checkbox-header--'.$this->maincontent.'">
									<span></span></label>
							</th>';
							$columns.='{ "data": "'.$keys.'", "orderable": false, "searchable": false, "class" : "col-checkbox '.$class.'", "width" : "20px" },
							';
						}
						elseif(@$this->params['fieldselect'][$keys]['hidden'] != true) {
							$icon=isset($this->params['fieldselect'][$keys]['icon']) ? '<i class="'.$this->params['fieldselect'][$keys]['icon'].'"></i>' : null ;
							$title = isset($this->params['fieldselect'][$keys]['title']) ? $this->params['fieldselect'][$keys]['title'] : $this->ci->lang->line($keys);
							$width = isset($this->params['fieldselect'][$keys]['width']) ? 'style="width:'.$this->params['fieldselect'][$keys]['width'].';"' : '';
							$this->params['content'].='<th class="text-center '.@$this->params['fieldselect'][$keys]['class'].' '.$sorting.'" '.$width.' >'.$icon.$title.$description.'</th>';
							$columns.='{ "data": "'.$keys.'", '.$orderable.' "class" : "'.$class.' '.$sorting.'", "width" : "'.@$this->params['fieldselect'][$keys]['width'].'" },
							';
						}
					}
				}
				
				foreach( $command as $key => $value ){
					if(
						$value == "browse" || 
						$value == "search" || 
						$value == "upload" || 
						$value == "import" || 
						$value == "generate" || 
						$value == "copy" || 
						$value == "paste" || 
						$value == "export" || 
						substr($value,-3) == "all" || 
						substr($value,-5) == "_head" || 
						$value == "back"
					){
						unset($command[$key]);
					}
				}

				if(count($command) > 0){
					$this->params['content'].='<th class="sorting_disabled text-center">Action</th>';
					$columns.='{ "data": "action", "orderable": false, "searchable": false, "class" : "action","width" : "80px" }';
				}
				$this->params['content'].='</tr>';
			}


			$this->params['content'].='</thead><tbody>';
			$foot = "";
			if( $sum == true ){
				// Get Total
				$sqlTotal = $this->params['query-total'] . $p_search;
				$queryTotal = $this->ci->db->query($sqlTotal)->row();
				$blankAction = "";
				if(count($command) > 0){
					$blankAction = "<th style='text-align:right'></th>";
				}

				/*$foot = "<tfoot>
	            <tr>
	                <th colspan='".$count."' style='text-align:right'>Subtotal :</th>
	                <th style='text-align:right'></th>
	                ".$blankAction."
	            </tr>
	            <tr>
	                <th colspan='".$count."' style='text-align:right'>Total : </th>
	                <th style='text-align:right'>".number_format($total,2,',','.')."</th>
	                ".$blankAction."
	            </tr>
	        </tfoot>";*/
	      $foot = "<tfoot>";
	      $foot .= "<tr>";
	      foreach(array_keys($this->params['fieldselect']) as $keys){
					if(!@$this->params['fieldselect'][$keys]['hidden']){
						if(@$this->params['fieldselect'][$keys]['sumtitle'] == true){
							$foot .= "<th style='text-align:right'>Subtotal</th>";
						} else {
	          	$foot .= "<th style='text-align:right'></th>";
						}
					}
				}     
	      $foot .= $blankAction . "</tr>";
	      $foot .= "<tr>";
	      // print_r($queryTotal);exit;
	      foreach(array_keys($this->params['fieldselect']) as $keys){
					if(!@$this->params['fieldselect'][$keys]['hidden']){
						$value = "";
						if(@$this->params['fieldselect'][$keys]['sum'] == true){
							$value = isset($queryTotal->$keys) ? number_format($queryTotal->$keys,2,",",".") : number_format(0,2,",",".");
						}
						if(@$this->params['fieldselect'][$keys]['sumtitle'] == true){
							$foot .= "<th style='text-align:right'>Total</th>";
						} else {
	          	$foot .= "<th style='text-align:right' class='".@$this->params['fieldselect'][$keys]['class']."'>".$value."</th>";
						}
					}
				}     
	      $foot .= $blankAction . "</tr>";

	      $foot .= "</tfoot>";
			}
			$this->params['content'].='</tbody>'.$foot.'</table>';
		}

		if( !isset($this->params['view']) ){
			$export = "";
			$footer = "";
			if( $exportCommand ){
				$export = '"dom": "Bflrtip",
        "buttons": [
	        {
            extend: "copyHtml5",
            footer: true,
            exportOptions: {
		          columns: "th:not(.action, .col-checkbox)"
		        },
		        filename: "'.$this->params['name'].'_'.date('Y-m-d H:i:s').'",
		        className: "btn-warning"
          },
        	{
            extend: "csv",
            exportOptions: {
		          columns: "th:not(.action, .col-checkbox)"
		        },
		        filename: "'.$this->params['name'].'_'.date('Y-m-d H:i:s').'",
		        className: "btn-dark"
          },
          {
            extend: "excelHtml5",
            footer: true,
            exportOptions: {
		          columns: "th:not(.action, .col-checkbox)"
		        },
		        filename: "'.$this->params['name'].'_'.date('Y-m-d H:i:s').'",
		        className: "btn-success"
          },
        	{
          	extend: "pdfHtml5",
            orientation: "landscape",
            pageSize: "LEGAL",
            exportOptions: {
		          columns: "th:not(.action, .col-checkbox)"
		        },
		        footer: true,
		        filename: "'.$this->params['name'].'_'.date('Y-m-d H:i:s').'",
		        className: "btn-danger"
        	}, 
        	{
          	extend: "print",
          	footer: true,
            exportOptions: {
		          columns: "th:not(.action, .col-checkbox)"
		        },
		        filename: "'.$this->params['name'].'_'.date('Y-m-d H:i:s').'",
		        className: "btn-info"
        	}, 
        	"colvis", 
    		],';
			}
			if( $sum == true ){
				$footer = '"footerCallback": function ( row, data, start, end, display ) {
	            var api = this.api(), data;
	 
	            // Remove the formatting to get integer data for summation
	            var intVal = function ( i ) {

	            	if( i === "" ){
	            		i = 0;
	            	}
            		
            		if( typeof i === "string" && i.includes("<") ){
									var obj = $.parseHTML( i );
									var elem = obj[0];
									i = eval( elem.value );
            		}

            		if( typeof i === "string" && i.includes("%") ){
									i = i.replace("%","");
            		}

            		var result = 0;
            		if( typeof i === "string" ){
            			result = eval( i.split(".").join("").split(",").join(".") );
            		} else if( typeof i === "number" ) {
									result = i;
            		}
            		
            		return result;

	            };
							
							var total = [];
							var pageTotal = [];
	
	 			';
	 			$colNum = 0;
	 			foreach(array_keys($this->params['fieldselect']) as $keys){
					if(!@$this->params['fieldselect'][$keys]['hidden']){
						if(@$this->params['fieldselect'][$keys]['sum'] == true){
							$footer .= '
								// Total over all pages
		            total['.$colNum.'] = api
		                .column( '.$colNum.' )
		                .data()
		                .reduce( function (a, b) {
		                    return intVal(a) + intVal(b);
		                }, 0 );
		 
		            // Total over this page
		            pageTotal['.$colNum.'] = api
		                .column( '.$colNum.', { page: "current"} )
		                .data()
		                .reduce( function (a, b) {
		                    return intVal(a) + intVal(b);
		                }, 0 );

								// Update footer
		            $( api.column( '.$colNum.' ).footer() ).html(
		                pageTotal['.$colNum.'].toLocaleString("id", { minimumFractionDigits: 2 })
		            );
	            ';
						}
						$colNum++;
					}
				}
	            
	 
	            
	        $footer .= '}';
			}

			// Grouping Table
			$grouping = "drawCallback: function ( settings ) {
				var api = this.api();

    		if (localStorage.getItem( 'DataTables_".$this->maincontent."_selected' ) != undefined){
	        var selected =  localStorage.getItem( 'DataTables_".$this->maincontent."_selected' ).split(',');
			    selected.forEach(function(s) {
			      api.row(s).select();
			      var trid = $('tr.selected').attr('id');
			    	/* goToByScroll( trid ); */
			    });
		    }
			";
			$groupcol = array();
			
			if( isset($this->params['groupcol']) ){
				$grouping .= "
				var rows = api.rows( {page:'current'} ).nodes();
    		var last = null;";
				$order = array();
				$arrCol = explode(",", $this->params['groupcol']);
				foreach($arrCol as $gcol){
					if( is_numeric($gcol) ){
						$groupcol[] = $gcol;
						array_push($order, array($gcol, 'asc'));
					} else {
						$col = 0;
						foreach(array_keys($this->params['fieldselect']) as $keys){
							if(@$this->params['fieldselect'][$keys]['hidden'] != true){
								if( $keys == $gcol){
									$groupcol[] = $col;
									array_push($order, array($col, 'asc'));
									break;
								}
								$col++;
							}
						}
					}
				}
				$totlevel = count($groupcol);
	      for($i = 0; $i < $totlevel; $i++){
	      	$grouping .= "
	      	api.column(".$groupcol[$i].", {page:'current'} ).data().each( function ( group, i ) {
            if ( last !== group ) {
              $(rows).eq( i ).before(
                  '<tr class=\"dtrg-group dtrg-start dtrg-level-".$i."\"><td colspan=\"".count($this->params['fieldselect'])."\">' + group + '</td></tr>'
              );
              last = group;
            }
          });";
        }
        if( @$this->params['inlinesearch'] != true  ){
		      $grouping .= "          
		        api.columns(".json_encode($groupcol).").visible( false );
	        ";
        }
			}
			$grouping .= "},";

			// Row Call Back
			$rowCallback = "";
			$clickCallback = "";
			$compare = "";
			$addClass = "";
			if( isset($this->params['compare']) ){
				$compare = "if( data.".$this->params['compare']['col1']." ".$this->params['compare']['operation']." data.".$this->params['compare']['col2']." ){
						$(row).addClass('".$this->params['compare']['class']."');
					}";
				if( in_array("inlineadd", $command) ){
					$compare = "if( data.".$this->params['compare']['col1']." ".$this->params['compare']['operation']." data.".$this->params['compare']['col2']." ){
						if( !$(row).hasClass('inlineadd') ){
							$(row).addClass('".$this->params['compare']['class']."');
						}
					}";
				}
			}

			if( @$this->params['rowselect'] == true ){
				$_primary = "";
				foreach(array_keys($this->params['fieldselect']) as $_cols){
					if(@$this->params['fieldselect'][$_cols]['type']=="primarykey"){
						$_primary=$_cols;
						break;
					}
				}

				$addClass = "if( row.id == rawId ){
					// $(row).addClass('table-success');
					$(row).addClass('selected');
				}
        // For Selected Data
        if ( $.inArray(data.DT_RowId, selected) !== -1 ) {
      		rawId = $(row).attr('id');
          $(row).addClass('selected');
        }";

				$urlparams = "pk/".$_primary."/valpk/";
					$clickCallback = "
					$('#dataTable_".$this->maincontent."').addClass('table-hover');
					$('#dataTable_".$this->maincontent." tbody').css('cursor','pointer');

		      $('#dataTable_".$this->maincontent." tbody').on('click', 'tr td:not(.action,.col-checkbox,.col-seq,.input)', function (e) {
		        // $('#dataTable_".$this->maincontent." tbody').children('tr').removeClass('table-success');
		        // $(this).parents('tr').addClass('table-success');
		        $('#dataTable_".$this->maincontent." tbody').children('tr').removeClass('selected');
		        $(this).parents('tr').addClass('selected');
		        rawId = $(this).parents('tr').attr('id');
		        if( rawId != undefined ){
		        	if( rawId.includes('add') ){
								return false;
		      		} else { ";
					if( isset($this->params['rowcallback']) ){
					$clickCallback .= "
								var ids = rawId.split('--');
								var id = ids[2];
				        var urlparams = '".$urlparams."' + id;
				        var callback = '".$this->params['rowcallback']."';
				        var url = callback.replace('[urlparams]', urlparams);
				        url = url.replace('[id]', id);
				        url = url.replace('[nama]', $('tr#'+ rawId + ' td:nth-child(3)').html() );
								eval( url );";
					}
					$clickCallback .= "
		      		}
		        }
		  		} );";

			}

			// if( isset($this->params['compare']) || @$this->params['rowselect'] == true ){
				$contextMenu = "";
		    // Context Menu
		    if( isset($this->params['context-menu']) ) {
		    	$buttonCMArr = array();
		    	$actionCM = "";
		    	$urlparams = "pk/".$_primary."/valpk/[id]";
		    	foreach( $this->params['context-menu'] as $cmk => $cmv){
		    		if( isset($cmv['items']) ){ // Ada Sub Menu
		    			foreach( $cmv['items'] as $cmk2 => $cmv2){
		    				$actionCM .= "
				    			case '".$cmk2."' :
				    				var urlparams = '".$urlparams."';
				    				var callback = '".$cmv2['url']."';
			            	var url = callback.replace('[urlparams]', urlparams);
					        	url = url.replace('[id]', id);
				    				eval(url); 
				    				break;
				    		";	
		    			}
		    		} else {
			    		$actionCM .= "
			    			case '".$cmk."' :
			    				var urlparams = '".$urlparams."';
			    				var callback = '".$cmv['url']."';
		            	var url = callback.replace('[urlparams]', urlparams);
				        	url = url.replace('[id]', id);
			    				eval(url); 
			    				break;
			    		";
		    		}
		    	}
		    	$buttonCM = json_encode($this->params['context-menu']);

		    	$contextMenu = "
		    	$(row).on('mousedown', function(e){
			   		if( e.button == 2 ) {
			   			var urlparams = '".$urlparams."';
			   			$('#dataTable_".$this->maincontent." tbody').children('tr').removeClass('selected');
			   			$(row).addClass('selected');
	            var ids = row.id.split('--');
	            var id = ids[2];
		    			$.contextMenu( 'destroy' );
			        $.contextMenu({
				        selector: 'tbody tr', 
				        callback: function(key, options) {
			            switch (key) {
			            	".$actionCM."
			            } 
				        },
				        items: ".$buttonCM."
					    });
			        return false;
			      }
					});";
		    }
				$rowCallback = "
				'rowCallback': function( row, data ) {
					".$compare."
					".$addClass."
					".@$this->params['customrowcallback']."
					".$contextMenu."
					// Calculator Callback
					$(row).find('td.calculator').find('input').calculator({
						onClose: function(){
							$(this).blur();
						},
						showFormula: true
					});
					$(row).find('td.calculator').find('input').calculator({
						onClose: function(){
							$(this).blur();
						},
						showFormula: true
					});
	      },";
	    // }
	      $noControl = "";
	      if( isset($this->params['datatable-control']) && $this->params['datatable-control'] == false ){
	      	 $noControl .= '
							"searching": false,
							"lengthChange": false,
							"info":     false,
	      	 ';
	      }

				if( isset($this->params['datatable-paging']) && $this->params['datatable-paging'] == false ){
	      	 $noControl .= '
							"paging": false,
	      	 ';
	      }

	    $minLength = isset($this->params['per_page']) ? $this->params['per_page'] : $this->ci->config->item("per_page");

	    
	    $inlinesearch = "";
	    if( @$this->params['inlinesearch'] == true ){
	    	$inlinesearch = '$("#dataTable_'.$this->maincontent.' thead tr").clone(true).appendTo( "#dataTable_'.$this->maincontent.' thead" );
		    $("#dataTable_'.$this->maincontent.' thead tr:eq(1) th").each( function (i) {
	        var title = $(this).text();
	        if( title != "No" && title != "Action" && title.trim() != "" ){
		        $(this).html( "<input type=\"text\" class=\"form-control\" placeholder=\"Cari "+title+"\" />" );
		        
		        $( "input", this ).on( "keyup change", function () {
	            if ( tbl_'.str_replace("-","_",$this->maincontent).'.column(i).search() !== this.value ) {
                tbl_'.str_replace("-","_",$this->maincontent).'
                  .column(i)
                  .search( this.value )
                  .draw();
	            }
		        });
		      	
		      } else {
		      	$(this).html( "" );
		      }
		    });';
	    }
	    $dataTableParam = isset( $this->params['dataTableParam'] ) ? $this->params['dataTableParam'] : "" ;
			$this->params['content'].='</div>
			</div>

			<script type="text/javascript">
				'.$inlinesearch.'
				var selected = [];
				var rawId = "";
		    var tbl_'.str_replace("-","_",$this->maincontent).' = $("#dataTable_'.$this->maincontent.'").DataTable( {
					'.$this->ci->config->item("config_data_table").'
					'.@$this->params['config_data_table'].'
					"autoWidth": false,
					"scrollCollapse": true,
					"orderCellsTop": true,
					"responsive": false,
	        "processing": true,
	        "serverSide": true,
	        "stateSave": true,
	        "select": {
	          style:    "single",
	          selector: "tr :not(.action)"
	        },
	        "dom": "lfrtip",
	        "lengthMenu": [['.$minLength.', 50, 100, -1], ['.$minLength.', 50, 100, "All"]],
	        "aaSorting": [],
	        '.$noControl.'
	        "ajax": {
	        	"url" : "'.site_url( $this->ci->router->fetch_directory() . $this->ci->router->fetch_class() ).'/getData/",
	        	"type" : "POST",
	        	"data" : {
	        		filter : "'.$p_search.'",
	        		postdata : "'.str_replace('"','\"',json_encode($_POST)).'"
	        	},
	        	"error": function (xhr, error, thrown) {
              bootbox.alert(xhr.responseText);
            }
	        },
	        "columns": [
	            '.$columns.'
	        ],
	        "oLanguage": {
  					"sProcessing": "<img style=\"width: 50px;\" src=\"'.base_url().'assets/img/spin.svg\" /><br />Memuat Data ....",
						"sLengthMenu":   "Tampilan _MENU_ entri",
						"sZeroRecords":  "Tidak ada data yang ditampilkan",
						"sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
						"sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
						"sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
						"sInfoPostFix":  "",
						"sSearch":       "Cari:",
						"sUrl":          "",
						"oPaginate": {
						   "sFirst":    "Pertama",
						   "sPrevious": "Sebelumnya",
						   "sNext":     "Selanjutnya",
						   "sLast":     "Terakhir"
						},
					},
					'.$dataTableParam.'
					'.$rowCallback.'
					'.$export.'
					'.$grouping.'
					'.$footer.'
		    });

		    tbl_'.str_replace("-","_",$this->maincontent).'.on("select.dt deselect.dt", function() {
				  localStorage.setItem( "DataTables_'.$this->maincontent.'_selected", tbl_'.str_replace("-","_",$this->maincontent).'.rows( { selected: true }).toArray())   
				});

		    '.$clickCallback.'

		    $("#bulk_'.$this->maincontent.'").hide();

				$("#checkbox-header--'.$this->maincontent.'").on("click",function(){
					if($("#checkbox-header--'.$this->maincontent.'").is(":checked")){
						$(".checkbox-data--'.$this->maincontent.'").prop("checked",true);
						$("#bulk_'.$this->maincontent.'").show();
					}
					else{
						$(".checkbox-data--'.$this->maincontent.'").prop("checked",false);
						$("#bulk_'.$this->maincontent.'").hide();
					}
				});

				$(".popover").popover({
						html: true,
						trigger: "hover"
				});
				
				tbl_'.str_replace("-","_",$this->maincontent).'.on("draw", function(){
					$(".checkbox-data--'.$this->maincontent.'").on("click",function(){
						var checked = 0;
						$(".checkbox-data--'.$this->maincontent.'").each(function( e, item ){
							if( $(item).is(":checked") ){
								checked = 1;
							}
						});
						
						if( checked == 1 ){
							$("#bulk_'.$this->maincontent.'").show();
						} else {
							$("#bulk_'.$this->maincontent.'").hide();
						}
					});

					$(".select2 select").select2();
					
				});

				$(this).updatePolyfill();
				$(function(){
					$(".table .select2 > select").select2();
				});
			</script>
			';
		}
		$this->mainfunction();
		return $this->str;
		// echo $this->str;
	}

	public function getData(){
		$months = $this->ci->config->item("bulan");
		$_primary = "";
		foreach(array_keys($this->params['fieldselect']) as $_cols){
			if(@$this->params['fieldselect'][$_cols]['type']=="primarykey"){
				$_primary=$_cols;

				break;
			}
		}
		$sql = "";
		

		$filter = @$_POST["filter"];
		if( $_POST['postdata'] ){
			$postdata = json_decode($_POST['postdata']);
			foreach($postdata as $key => $value ){
				$_POST[$key] = "'".$value."'";
			}
		}
		// dd($_POST);
		if( !isset($this->params['sql']) ){
			$field = "";
			foreach(array_keys($this->params['fieldselect']) as $keys){
				if( $keys != "SEQ" && $keys != "#"){
					$field .= $keys . ",";
				}
			}

			$field = substr($field,0, strlen($field)-1);
			$sql = "SELECT ".$field." FROM ".$this->params['table'] . " " . $filter;
			$this->params['sql'] = $sql;
		} else {
			// dd($this->params['sql']);
			$sql = "SELECT * FROM ( ".$this->params['sql']. " " . $filter ." ) as A";
		}
		

		$whereParam = "";
		// Check if Where Exist in Query
		if (strpos($sql, strtoupper('WHERE')) !== false) {
		   // Get WHERE Params
			$pos = strpos($sql, strtoupper('WHERE'));
			$whereParam = substr($sql, $pos);
			$whereParam = substr($whereParam, 0, strlen($whereParam) - 6);
		}

		$sqlcount = "SELECT ".$_primary." FROM (" . $this->params['sql']  . $filter . ") as A ";
		if( isset($this->params['dbconn'] ) ){
			$this->ci->load->database($this->params['dbconn'], FALSE, TRUE);
		}
		$count_all = $this->ci->db->query( $sqlcount )->num_rows();


		$post = $this->ci->input->post();
		// Add Filter data
		$value = @$post['search']['value'];
		if($value != ""){
			$sql .= " WHERE ";
			foreach(array_keys($this->params['fieldselect']) as $s_keys){
				if( $s_keys != 'SEQ' && $s_keys != '#' && @$this->params['fieldselect'][$s_keys]['type'] != "function" ){
					$sql .= $s_keys . " LIKE '%".$this->ci->db->escape_like_str($value)."%' OR ";
				}
			}
			$sql = substr($sql,0, (strlen($sql) - 3) );

			if( isset($this->params['dbconn'] ) ){
				$this->ci->load->database($this->params['dbconn'], FALSE, TRUE);
			}
			$count_all = $this->ci->db->query( $sql )->num_rows();
		}

		// Individual Search
		$addWhere = false;
		if($value != ""){
			$addWhere = true;
		}
		foreach($post['columns'] as $key => $value){
			$s_keys = $value['data'];
			if( $value['search']['value'] != "" ){
				if( $addWhere	== false ){
					$sql .= " WHERE ";
					$addWhere	= true;
				} else{
					$sql .= " AND ";
				}
				$sql .= $s_keys . " LIKE '%".$this->ci->db->escape_like_str($value['search']['value'])."%'";
			}
		}
		// dd($sql);
		// $sql = substr($sql,0, (strlen($sql) - 3) );
		if( isset($this->params['dbconn'] ) ){
			$this->ci->load->database($this->params['dbconn'], FALSE, TRUE);
		}
		$count_all = $this->ci->db->query( $sql )->num_rows();

		// Order Data
		$ordercol = 1;
		if( @$post['order'][0]['column'] ){
			// Get Column Name
			$fieldselect = array();
			$groupcol = array();
			foreach($this->params['fieldselect'] as $key => $value){
				if( @$value['hidden'] != true ){
					$fieldselect[] = $key;
				}
			}
			if( isset($this->params['groupcol']) ){
				$groupcol = explode(",",$this->params['groupcol']);
				foreach($groupcol as $gc){
					unset($fieldselect[$gc]);
				}
			}
			// $orderColumn = array_keys(array_slice($fieldselect, $post['order'][0]['column'] - 1,1));
			$orderColumn = $fieldselect[$post['order'][0]['column']];
			$ordercol = $orderColumn;
			$sql .= " ORDER BY ".$ordercol. " " . $post['order'][0]['dir'] . " ";
		} else {
			if( isset($this->params['order']) ){
				$sql .= " ORDER BY " . $this->params['order'];
			}
		}
		// else {
		// 	$sql .= " ORDER BY ".$_primary." ASC";
		// }
		// Add Limit Data
		if( !isset($post['start']) || !isset($post['length']) ){
			$post['start'] = 1;
			$post['length'] = isset($this->params['per_page']) ? $this->params['per_page'] : 20;
			$post['draw'] = TRUE;
		}

		$limit = "";
		if( !isset($this->params['datatable-paging']) || @$this->params['datatable-paging'] == true ){
			if($post['length'] != "-1"){
				if( $this->ci->db->platform() == "mssql" || $this->ci->db->platform() == "sqlsrv"){
					if( @$post['order'][0]['column'] || isset($this->params['order']) ){
						$limit = " OFFSET ".$post['start']." ROWS FETCH NEXT ".$post['length']." ROWS ONLY";
					} else {
						$limit = "ORDER BY 1 OFFSET ".$post['start']." ROWS FETCH NEXT ".$post['length']." ROWS ONLY";
					}
				} else {
					$limit = "LIMIT " . $post['start'] . "," . $post['length'];
				}
				if( isset($this->params['dblimit']) ){
					$limit = strtolower($this->params['dblimit']);
					if( strpos($limit, "order by") !== false ){
						$limit = str_replace("order by [order]", "", $limit);
					}
					$limit = str_replace("[order]", $ordercol, $limit);
					$limit = str_replace("[start]", $post['start'], $limit);
					$limit = str_replace("[length]", $post['length'], $limit);
					$limit = str_replace("[end]", ($post['start'] + $post['length']) - 1, $limit);
				}
			}
    }
    $sql .= " " . $limit;
		
		if( isset($this->params['dbconn'] ) ){
			$this->ci->load->database($this->params['dbconn'], FALSE, TRUE);
		}
		$query = $this->ci->db->query( $sql )->result_array();

		$count_filtered = $count_all;

		$seq = $post['start'];
		$datalist = array();
		$command=explode(",",$this->params['command']);

		// Start the Loop
		if( in_array("inlineadd", $command) ){
			$mylist["DT_RowId"] = "row--" . $this->maincontent . "--add";
			$mylist["DT_RowClass"] = "inlineadd hidden";
			foreach(array_keys($this->params['fieldselect']) as $keys){
				if($keys=="SEQ"){
					$mylist[$keys] = "";
				} elseif($keys=="#"){
					$mylist[$keys] = "";
				}
				elseif(!@$this->params['fieldadd'][$keys]['hidden']){
					// $type = isset($this->params['fieldadd'][$keys]['type']) ? $this->params['fieldadd'][$keys]['type'] : "text" ;
					$type = @$this->params['fieldadd'][$keys]['type'];
					//Several type config
					$isdisabled = (@$this->params['fieldadd'][$keys]['disabled'] == true ) ? "disabled" : "";
					$isreadonly = (@$this->params['fieldadd'][$keys]['readonly'] == true ) ? "readonly" : "";
					switch($type){
						case "text":
							$_value = "<input type='text' ".$isdisabled." ".$isreadonly." class='form-control' id='input-".$keys."' name='input-".$keys."' value='".@$this->params['fieldadd'][$keys]['value']."' />";
							break;
						case "textarea":
							$_value = "<textarea ".$isdisabled." ".$isreadonly." class='form-control' id='input-".$keys."' name='input-".$keys."'>".@$this->params['fieldadd'][$keys]['value']."</textarea>";
							break;
						case "number":
							$_value = "<input type='number' ".$isdisabled." ".$isreadonly." style='text-align: right;' class='form-control' id='input-".$keys."' name='input-".$keys."' value='".@$this->params['fieldadd'][$keys]['value']."' />";
							$_value .= "<script type='text/javascript'>
								$(document).ready(function(){
									$(this).updatePolyfill();
								});
							</script>";
							break;
						case "checkbox":
							$_value = '<label class="checkbox checkbox-single"><input type="checkbox" value="0" class="checkable checkbox-input--'.$this->maincontent.'" id="input-'.$keys.'" name="input-'.$keys.'"><span></span></label>';
							$_value .= "<script type='text/javascript'>
								$(function(){
									$('#input-".$keys."').on('change', function(){
										if( $(this).is(':checked') == true ){
											$(this).val('1');
										} else {
											$(this).val('0');
										}
									});
								});";
							break;
						case "dropdownquery":
							$option="";
							$optgroup="";
							$multiple = "";
							$addButton = "";
							foreach($this->params['fieldadd'][$keys]['sourcequery'] as $opdata){
								if(isset($opdata['labeldt'])){
									if($optgroup!=$opdata['labeldt']){
										$option.="<optgroup label='".$opdata['labeldt']."'>";
										$optgroup=$opdata['labeldt'];
									}
								}
								$selected = ($opdata['keydt']==@$this->params['fieldadd'][$keys]['value']) ? "selected" : null ;
								$option.="<option ".$selected." value='".$opdata['keydt']."'>".$opdata['valuedt']."</option>";
							}
							$name = "name='input-".$keys."'";

							$_value ="
							<select ".$isreadonly." ".$isdisabled." ".$name." id='input-".$keys."' class='form-select' />
								".$option."
							</select>";
							break;

						case "popup":
							$url = $this->params['fieldadd'][$keys]['popup_url'];
							$_value = "<div class='input-group'><input readonly type='text' style='width:1%;' readonly class='form-control' id='input-".$keys."' name='input-".$keys."' value='".@$this->params['fieldadd'][$keys]['value']."' />";
							$_value .= "
								<button class='btn btn-primary  btn-popup' style='display:none;' type='button' id='btn-popup-".$keys."'><i class='fa fa-search' style='font-size: 12px;color: white;padding: 0;margin: 0;'></i></button>
							</div>";
							$_value .= "<script type='text/javascript'>
								$(function(){
									$('#btn-popup-".$keys."').click(function(){
										loadinputmodal('".$url."/trigger/".$keys."');
									});
								});
							</script>";
							break;
						default:
							$_value = "";
							break;
					}
					$mylist[$keys] = $_value;
				}
				$addAction = "<button type='button' class='btn btn-light-primary btn-inlineadd' style='min-width:100px;' id='btn-inlineadd-".$this->maincontent."'><i class='fa fa-plus'></i> ".$this->ci->lang->line("add")."</button>
					<button type='button' onclick='$(this).parents(\"tr\").addClass(\"hidden\");' class='btn btn-light-danger btn-cancel-inlineadd' style='min-width:100px;' id='btn-cancel-inlineadd-".$this->maincontent."'><i class='fa fa-times'></i> ".$this->ci->lang->line("cancel")."</button>
					<script type='text/javascript'>
						$(function(){
							$('#btn-inlineadd-".$this->maincontent."').click(function(){
								var target = '".$this->filename."/inlineadd/';
								var datapost = $('#row--" . $this->maincontent . "--add input, #row--" . $this->maincontent . "--add select, #row--" . $this->maincontent . "--add textarea').serialize();
								$.post(target, datapost, function( e ){
									if( e == 1 ){
										".@$this->params['inlineaddcallback']."
										loadcontent('".$this->maincontent."', '".$this->filename."/');
									} else {
										swal.fire({
							        type: 'warning',
							        title: e,
							        showConfirmButton: !1,
							        timer: 1500
							      });
									}
								});
								return false;
							});
						});
					</script>
				";
				$mylist['action'] = $addAction;
			}
			$datalist[] = (object) $mylist;
		}

		foreach($query as $data){
			$seq++;
			$mylist = array();
			$sum = array();
			foreach(array_keys($this->params['fieldselect']) as $keys){
				$mylist["DT_RowId"] = "row--" . $this->maincontent . "--" . $data[$_primary];
				if($keys=="SEQ"){
					$mylist[$keys] = number_format($seq,0) . ".";
				} elseif($keys=="#"){
					$mylist[$keys] = '<div class="form-check form-check-sm form-check-custom form-check-solid"><input type="checkbox" value="" class="form-check-input checkbox-data--'.$this->maincontent.'" id="check-'.$_primary."-".$data[$_primary].'"></div>';
				} else {
					$_value=@$data[$keys];
					$isdisabled = (@$this->params['fieldselect'][$keys]['disabled'] == true ) ? "disabled" : "";
					$isreadonly = (@$this->params['fieldselect'][$keys]['readonly'] == true ) ? "readonly" : "";
					switch(@$this->params['fieldselect'][$keys]['type']){
						case "date":
							if( @$data[$keys] != "" ){
								if( $_value != "0000-00-00" && $_value != "0000-00-00 00:00:00" && $_value != "" ){
									$_value=date("d",strtotime(@$data[$keys])) . " " . $months[ date("m",strtotime(@$data[$keys])) ] . " " . date("Y",strtotime(@$data[$keys]));
								} else {
									$_value = "";
								}
							}
							break;
						case "color":
							if( @$data[$keys] != "" ){
								$_value="<div style='float:left;width:20px;height:20px;background:".$data[$keys].";border:none;border-radius:50%;'></div> &nbsp; " . $data[$keys];
							}
							break;
						case "month":
							if( @$data[$keys] != "" ){
								$ex = explode("-",$data[$keys]);
								if( strlen($ex[0]) == "4" ){
									$_value = $months[intval($ex[1])] . " " . $ex[0];
								} else {
									$_value = $months[intval($ex[0])] . " " . $ex[1];
								}
							}
							break;
						case "datetime":
							if( $_value != "0000-00-00 00:00:00" && $_value != "" ){
								$_value=date("d M y - H:i",strtotime(@$data[$keys]));
							}
							break;
						case "number":
							$_value=number_format(@$data[$keys],0,",",".");
							break;
						case "decimal":
							$decimalPlace = isset($this->params['fieldselect'][$keys]['decimalplace']) ?  $this->params['fieldselect'][$keys]['decimalplace'] : 2;
							$_value=number_format(@$data[$keys],$decimalPlace,",",".");
							break;
						case "dropdownarray":
							$_value=@$this->params['fieldselect'][$keys]['sourcearray'][$data[$keys]];
							break;
						case "image":
							if(@$data[$keys] == "") $data[$keys] = "noimage.png";
							$_value="<img src='".base_url()."uploads/".@$data[$keys]."' class='thumbnail' style='width:100%;' />";
							break;
						case "blob":
							$_value="<img src='".$_value."' class='thumbnail' style='width:100%;' />";
							break;
						case "download":
							$dir = isset($this->params['fieldselect'][$keys]['dir']) ? $this->params['fieldselect'][$keys]['dir'] : "uploads" ;
							if(@$data[$keys] != "" && file_exists("./".$dir ."/".$data[$keys]) != "") {
								$_value="<a href='".base_url(). $dir . "/".@$data[$keys]."' target='_blank' class='btn w-100 btn-light-primary' title='download'><i class='fa fa-download'></i> Download</a>";
							} else {
								$_value = "<span class='btn btn-danger'>No File Available</span>";
							}
							break;
						case "function":
							$func = isset($this->params['fieldselect'][$keys]['method']) ? $this->params['fieldselect'][$keys]['method'] : $this->params['fieldselect'][$keys]['func'];
							$model = $this->params['fieldselect'][$keys]['model'];
							if( !isset($this->params['fieldselect'][$keys]['params']) ){
								$_value = $this->ci->$model->$func( $data[$_primary] );
							} else {
								$params = $this->params['fieldselect'][$keys]['params'];
								$exp = explode(",",$params);
								$prm = "";
								$myParams = array();
								foreach( $exp as $e ){
									$myParams[] = $data[$e];
								}
								$_value = $this->ci->$model->$func( $myParams );
							}
							break;
						case "input_checkbox":
							$action = $this->params['fieldselect'][$keys]['action'];
							$checked = ($data[$keys] == 1) ? "checked" : null ;
							$_value = '<label class="checkbox checkbox-input"><input type="checkbox" '.$checked.' value="" class="checkable checkbox-input--'.$this->maincontent.'" id="input-'.$keys."-".$data[$_primary].'"><span></span></label>';
							$_value .= "<script type='text/javascript'>
								$(function(){
									$('#input-".$keys."-".$data[$_primary]."').on('change', function(){
										var datapost = {
											'id' : '".base64_encode($keys."-".$data[$_primary])."',
											'value' : ($(this).is(':checked') == true) ? 1 : 0,
										}
										var target = '".$action."';
										$.post(target, datapost, function( e ){
											if( e != '' ){
												swal.fire({
									        type: 'warning',
									        title: e,
									        showConfirmButton: !1,
									        timer: 1500
									      });
											} else {
												".@$this->params['inlineeditcallback']."
											}
										});
									});

								});
							</script>";
							break;
						case "input_text":
							$action = $this->params['fieldselect'][$keys]['action'];
							$_value = "<input ".$isdisabled." ".$isreadonly." type='text' class='form-control' id='input-".$keys."-".$data[$_primary]."' name='input-".$keys."-".$data[$_primary]."' value='".$data[$keys]."' />";
							$_value .= "<script type='text/javascript'>
								$(function(){
									$('#input-".$keys."-".$data[$_primary]."').on('blur', function(){
										var datapost = {
											'id' : '".base64_encode($keys."-".$data[$_primary])."',
											'value' : $(this).val(),
										}
										var target = '".$action."';
										$.post(target, datapost, function( e ){
											if( e != '' ){
												swal.fire({
									        type: 'warning',
									        title: e,
									        showConfirmButton: !1,
									        timer: 1500
									      });
											} else {
												".@$this->params['inlineeditcallback']."
											}
										});
									});

								});
							</script>";
							break;
						case "input_checkbox":
							$_value = "<label class='checkbox checkbox-single'><input ".$isdisabled." ".$isreadonly." type='checkbox' class='checkable' id='input-".$keys."-".$data[$_primary]."' name='input-".$keys."-".$data[$_primary]."' value='".$data[$keys]."' /><span></span></label>";
							if( isset($this->params['fieldselect'][$keys]['action']) ){
								$action = $this->params['fieldselect'][$keys]['action'];
								$_value .= "<script type='text/javascript'>
									$(function(){
										$('#input-".$keys."-".$data[$_primary]."').on('change', function(){
											var datapost = {
												'id' : '".base64_encode($keys."-".$data[$_primary])."',
												'value' : $(this).val(),
											}
											var target = '".$action."';
											$.post(target, datapost, function( e ){
												if( e != '' ){
													swal.fire({
										        type: 'warning',
										        title: e,
										        showConfirmButton: !1,
										        timer: 1500
										      });
												} else {
													".@$this->params['inlineeditcallback']."
												}
											});
										});

									});
								</script>";
							}
							break;
						case "input_textarea":
							$action = $this->params['fieldselect'][$keys]['action'];
							$_value = "<textarea ".$isdisabled." ".$isreadonly." class='form-control' id='input-".$keys."-".$data[$_primary]."' name='input-".$keys."-".$data[$_primary]."'>".$data[$keys]."</textarea>";
							$_value .= "<script type='text/javascript'>
								$(function(){
									$('#input-".$keys."-".$data[$_primary]."').on('blur', function(){
										var datapost = {
											'id' : '".base64_encode($keys."-".$data[$_primary])."',
											'value' : $(this).val(),
										}
										var target = '".$action."';
										$.post(target, datapost, function( e ){
											if( e != '' ){
												swal.fire({
									        type: 'warning',
									        title: e,
									        showConfirmButton: !1,
									        timer: 1500
									      });
											} else {
												".@$this->params['inlineeditcallback']."
											}
										});
									});
								});
							</script>";
							break;
						case "input_number":
							// $reload = (isset($this->params['sum-column'])) ? "tbl_".str_replace($this->params['maincontent'],"-","_").".draw();" : null;
							$reload = "";
							$class = $this->params['fieldselect'][$keys]['class'];
							$classes = explode(" ", $class);
							$action = $this->params['fieldselect'][$keys]['action'];
							$_value = "<input step='0' type='number' ".$isdisabled." ".$isreadonly." style='text-align: right; position: relative;' class='form-control' id='input-".$keys."-".$data[$_primary]."' name='input-".$keys."-".$data[$_primary]."' value='".$data[$keys]."' />";
							$_value .= "<script type='text/javascript'>
								$(document).ready(function(){
									$('#input-".$keys."-".$data[$_primary]."').on('blur', function( e ){
										var datapost = {
											'id' : '".base64_encode($keys."-".$data[$_primary])."',
											'value' : $(this).val(),
										}
										var target = '".$action."';
										$.post(target, datapost, function( e ){
											if( e != '' ){
												swal.fire({
									        type: 'warning',
									        title: e,
									        showConfirmButton: !1,
									        timer: 1500
									      });
											} else {
												".@$this->params['inlineeditcallback']."
											}
											".$reload."
										});
									});

									$('.ws-number').on('keydown',function(e) {
								    var key = e.charCode || e.keyCode;
								    if(key == 38 || key == 40 ) {
											e.preventDefault();
								    	return false;
								    } else {
											return true;
									  }
									});

									$('.ws-number').css('position', 'relative');
									$('.ws-number').unbind('mousewheel');
									$('.ws-number').unbind('mwheeintent');
									$('.ws-number').unbind('wheel');
									$('.ws-number').unbind('keydown');
								});
							</script>";
							break;
						case "input_dropdownquery":
							$action = $this->params['fieldselect'][$keys]['action'];
							$option="";
							$optgroup="";
							$multiple = "";
							$addButton = "";
							foreach($this->params['fieldselect'][$keys]['sourcequery'] as $opdata){
								if(isset($opdata['labeldt'])){
									if($optgroup!=$opdata['labeldt']){
										$option.="<optgroup label='".$opdata['labeldt']."'>";
										$optgroup=$opdata['labeldt'];
									}
								}
								$selected = ($opdata['keydt']==$data[$keys]) ? "selected" : null ;
								$option.="<option ".$selected." value='".$opdata['keydt']."'>".$opdata['valuedt']."</option>";
							}
							$name = "name='input-".$keys."'";

							$_value ="
							<select ".$isreadonly." ".$isdisabled." ".$name." id='input-".$keys."-".$data[$_primary]."' class='form-select' />
								".$option."
							</select>";
							
							$_value .= "<script type='text/javascript'>
								$(document).ready(function(){
									$('.select2 > select').select2({
										placeholder: $(this).attr('placeholder'),
										allowClear: Boolean($(this).data('allow-clear')),
									});

									$('#input-".$keys."-".$data[$_primary]."').on('change', function( e ){
										var datapost = {
											'id' : '".base64_encode($keys."-".$data[$_primary])."',
											'value' : $(this).val(),
										}
										var target = '".$action."';
										$.post(target, datapost, function( e ){
											if( e != '' ){
												swal.fire({
									        type: 'warning',
									        title: e,
									        showConfirmButton: !1,
									        timer: 1500
									      });
											} else {
												".@$this->params['inlineeditcallback']."
											}

										});
									});
								});
							</script>";
							break;

						case "input_popup":
							$action = $this->params['fieldselect'][$keys]['action'];
							$url = $this->params['fieldselect'][$keys]['popup_url'];
							$_value = "<div class='input-group'><input readonly type='text' ".$isdisabled." ".$isreadonly." style='width:1%;' disabled class='form-control' id='input-".$keys."-".$data[$_primary]."' name='input-".$keys."-".$data[$_primary]."' value='".$data[$keys]."' />";
							$_value .= "
								<button class='btn btn-primary  btn-popup' style='display:none;' type='button' id='btn-popup-".$keys."-".$data[$_primary]."'><i class='fa fa-search' style='font-size: 12px;color: white;padding: 0;margin: 0;'></i></button>
							</div>";
							$_value .= "<script type='text/javascript'>
								$(function(){
									$('#btn-popup-".$keys."-".$data[$_primary]."').click(function( e ){
										e.preventDefault();
										loadinputmodal('".$url."/trigger/".$keys."-".$data[$_primary]."');
									});
									$('#input-".$keys."-".$data[$_primary]."').on('blur', function(){
										var datapost = {
											'id' : '".base64_encode($keys."-".$data[$_primary])."',
											'value' : $(this).val(),
										}
										var target = '".$action."';
										$.post(target, datapost, function( e ){
											if( e != '' ){
												swal.fire({
									        type: 'warning',
									        title: e,
									        showConfirmButton: !1,
									        timer: 1500
									      });
											} else {
												".@$this->params['inlineeditcallback']."
											}
										});
									});
								});
							</script>";
							break;
					}
					// $_value = nl2br($_value);
					$mylist[$keys] = @$this->params['fieldselect'][$keys]['ltag'].$_value.@$this->params['fieldselect'][$keys]['rtag'];
				}

				$urlparams = "pk/".$_primary."/valpk/".urlencode($data[$_primary]);
				if( $this->iscrypt == TRUE ){
					$urlparams = $this->ci->encryption->encrypt($urlparams);
					$urlparams = base64_encode($urlparams);
				}

				$_cmddetail='';
				$_cmddetail.=(in_array("preview",$command)) ? "<li><a class='dropdown-item btn btn-clean btn-preview' data-rel='tooltip' href='".$this->params['previewurl'] . $data[$this->params['previewfield']] ."' target='blank'><i class='fa fa-search fa-info'></i> ".$this->ci->lang->line("preview")."</a></l>" : '' ;
				$_cmddetail.=(in_array("view",$command)) ? "<li><a class='dropdown-item btn btn-clean btn-view' data-rel='tooltip' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/view/".$urlparams."\");' title='".$this->ci->lang->line('view')."'><i class='fa fa-eye text-warning'></i> ".$this->ci->lang->line("view")."</a></l>" : '' ;
				$_cmddetail.=(in_array("edit",$command)) ? "<li><a class='dropdown-item btn btn-clean btn-edit' data-rel='tooltip' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/edit/".$urlparams."\");' title='".$this->ci->lang->line('edit')."'><i class='far fa-edit text-success'></i> ".$this->ci->lang->line("edit")."</a></l>" : '' ;
				$_cmddetail.=(in_array("printreceipt",$command)) ? "<li><a class='dropdown-item btn btn-clean btn-printreceipt' data-rel='tooltip' href='javascript:loadinputmodal(\"".$this->filename."/printreceipt/pk/".$_primary."/valpk/".urlencode($data[$_primary])."\", \"print\");' title='".$this->ci->lang->line('print')."'><i class='fa fa-print text-success'></i> ".$this->ci->lang->line("print")."</a></l>" : '' ;
				$_cmddetail.=(in_array("instantprint",$command)) ? "<li><a class='dropdown-item btn btn-clean btn-printreceipt' data-rel='tooltip' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/print/pk/".$_primary."/valpk/".urlencode($data[$_primary])."\");' title='".$this->ci->lang->line('print')."'><i class='fa fa-print text-success'></i> ".$this->ci->lang->line("print")."</a></l>" : '' ;
				$_cmddetail.=(in_array("stimulsoft",$command)) ? "<li><a class='dropdown-item btn btn-clean btn-stimulsoft' data-rel='tooltip' href='javascript:loadmodal(\"".$this->filename."/stimulsoft/pk/".$_primary."/valpk/".urlencode($data[$_primary])."\", \"none\");' title='".$this->ci->lang->line('print')."'><i class='fa fa-print text-success'></i> ".$this->ci->lang->line("print")."</a></l>" : '' ;
				$_cmddetail.=(in_array("detail",$command)) ? "<li><a class='dropdown-item btn btn-clean btn-detail' data-rel='tooltip' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/browse_detail/pk/".$_primary."/valpk/".urlencode($data[$_primary])."\");' title='".$this->ci->lang->line('detail')."'><i class='fa fa-list text-warning'></i> ".$this->ci->lang->line("detail")."</a></l>" : '' ;
				$_cmddetail.=(in_array("process",$command)) ? "<li><a class='dropdown-item btn btn-clean btn-process' data-rel='tooltip' href='javascript:loadprocess(\"".$this->maincontent."\",\"".$this->filename."/process/pk/".$_primary."/valpk/".urlencode($data[$_primary])."\");' title='".$this->ci->lang->line('process')."'><i class='fa fa-cog text-primary'></i> ".$this->ci->lang->line("process")."</a></li>" : '' ;
				
				$custom = false;
				foreach( $command as $k => $cmd ){
					if( $cmd != "preview" && 
							$cmd != "edit" && 
							$cmd != "view" && 
							$cmd != "delete" && 
							$cmd != "printreceipt" &&
							$cmd != "instantprint" &&
							$cmd != "stimulsoft" &&
							$cmd != "detail" &&
							$cmd != "process" &&

							$cmd != "browse" &&
							$cmd != "back" &&
							$cmd != "add" &&
							$cmd != "inlineadd" &&
							$cmd != "import" &&
							$cmd != "generate" &&
							$cmd != "search" &&
							$cmd != "copy" &&
							$cmd != "paste" &&
							$cmd != "export" &&
							substr($cmd,-3) != "all" &&
							substr($cmd,-5) != "_head" &&
							substr($cmd,-9) != "_dropdown"
					){
						$target = "target='".(isset($this->params['cmd'][$cmd]['target']) ? $this->params['cmd'][$cmd]['target'] : "" )."'";
						$_cmddetail .= "<li><a class='dropdown-item btn btn-clean btn-".$cmd."' ".$target." data-rel='tooltip' href='".(isset($this->params['cmd'][$cmd]['url']) ? str_replace("[urlparams]",$urlparams,$this->params['cmd'][$cmd]['url']) : "javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/".$cmd."/".$urlparams."\")" )."' title='".$this->ci->lang->line($cmd)."'><i class='".$this->params['cmd'][$cmd]['icon']."'></i> ".$this->ci->lang->line($cmd)."</a></li>";
					}
				}
				$_cmddetail.=(in_array("delete",$command)) ? "<li><a class='dropdown-item btn btn-clean btn-delete text-danger' data-rel='tooltip' href='javascript:deletedata(\"".$this->maincontent."\",\"".$this->filename."/delete/".$urlparams."\");' title='".$this->ci->lang->line('delete')."'><i class='fa fa-trash text-danger'></i>".$this->ci->lang->line("delete")."</a></li>" : '' ;
				
				$mylist['action'] = '';
				
				if( $_cmddetail != "" ){
					// $mylist['action'] = '<div class="btn-group" role="group">'.$_cmddetail.'</div>';
					$mylist['action'] = '
					<div class="dropdown table-dropdown">
					  <a class="btn btn-sm btn-success dropdown-toggle" href="javascript:void();" role="button" id="dd-'.$this->maincontent.'-'.$data[$_primary].'" data-bs-toggle="dropdown" aria-expanded="false">
					    '.$this->ci->lang->line("action").'
					  </a>

					  <ul class="dropdown-menu" aria-labelledby="dd-'.$this->maincontent.'"-'.$data[$_primary].'>
					    '.$_cmddetail.'
					  </ul>
					</div>';
				}
			}
			$datalist[] = (object) $mylist;
		}

		$output = (object) array(
			"draw" => $post['draw'],
			"recordsTotal" => $count_all,
			"recordsFiltered" => $count_filtered,
			"data" => $datalist
			// "error" => $sql
		);
		if( isset($this->params['dbconn']) ){
			$this->ci->load->database("default", FALSE, TRUE);
		}
		header("Content-type: application/json");
		return json_encode($output);
	}

	public function add(){
		$post = $this->ci->input->post();
		$command=explode(",",$this->params['command']);
		$_cmdtext="";
		$_cmdtext.=(in_array("browse",$command)) ? "<a title='".$this->ci->lang->line("back")."' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn btn-light-primary '><i class='fa fa-arrow-left'></i> ".$this->ci->lang->line("back")."</a>" : '' ;
		$alert = "";
		$valid = true;

		$primary = "";
		foreach($this->params['fieldadd'] as $key => $value){
			if(@$value['type'] == "primarykey" ){
				$primary = $key;
			}
		}

		if(isset($this->params['alert'])){
			$alert = '<div class="alert alert-'.@$this->params["alert"]["type"].'">
				<div class="alert-text">
					'.@$this->params['alert']['message'].'
				</div>
			</div>';
		}

		if(isset($this->params['formheader']) && $this->params['formheader'] == false){
			$this->params['content']='
			<div class="card card-custom gutter-b example example-compact">
				<div class="card-body">';
		} else {
			$this->params['content']='
			<div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
          <h3 class="card-title"><i class="fa fa-plus me-5"></i>'.$this->ci->lang->line("add") . ' ' . $this->params['name'].'</h3>
          <div class="card-toolbar">
            <div class="example-tools justify-content-center">
            	'.$_cmdtext.'
            </div>
          </div>
        </div>
        <div class="card-body">';
		}
		$this->params['content'].= "<div id='alert'>" . $alert . "</div>";
		if( isset($this->params['view']) ){
			$this->params['data'] = (isset($this->params['data'])) ? $this->params['data'] : array();
			$this->params['content'].= $this->ci->load->view($this->params['view'], $this->params['data'], TRUE);
		} else {
			$this->params['content'].= "<form novalidate class='form-horizontal' role='form' id='".$this->formid."' enctype='multipart/formdata'>";
			foreach(array_keys($this->params['fieldadd']) as $_keyadd){
				$addon = "";
				$rsign = "";
				$disabled = "";
				$description = "";
				$caption = isset($this->params['fieldadd'][$_keyadd]['caption']) ? $this->params['fieldadd'][$_keyadd]['caption'] : $this->ci->lang->line($_keyadd);
				$validation_message = (isset($this->params['fieldadd'][$_keyadd]['validation_message'])) ? $this->params['fieldadd'][$_keyadd]['validation_message'] : "";
				if( isset($this->params['fieldadd'][$_keyadd]['description']) ){
					$description = "<a href='javascript:return false;' class='popover' data-container='body' data-bs-toggle='popover' data-placement='top' data-title='".$this->ci->lang->line($_keyadd)."' data-content='".$this->params['fieldadd'][$_keyadd]['description']."'>
					  <span class='fa fa-question-circle'></span>
					</a>
					";
				}
				// $description = @$this->params['fieldadd'][$_keyadd]['description'];
				if(isset($this->params['fieldadd'][$_keyadd]['help'])){
					$addon = "<span class='input-group-text'>".$this->params['fieldadd'][$_keyadd]['help']."</span>";
				}

				if(isset($this->params['fieldadd'][$_keyadd]['button'])){
					$addon = "<button type='button' class='btn btn-light-primary' id='btn-".$_keyadd."'>".$this->params['fieldadd'][$_keyadd]['button']."</button>";
				}

				$isreadonly=(@$this->params['fieldadd'][$_keyadd]['readonly']==true) ? "readonly" : null;
				$isdisabled=(@$this->params['fieldadd'][$_keyadd]['disabled']==true) ? "disabled" : null;

				if( @$this->params['disabled'] == true ){
					$isdisabled ="disabled";
				}

				if( @$this->params['readonly'] == true ){
					$isreadonly ="readonly";
				}
				
				// Form Validation
				if( count($post) > 0){
					$this->params['fieldadd'][$_keyadd]['value'] = set_value($_keyadd);
				}
				if( isset($this->params['fieldadd'][$_keyadd]['validation']) ){
					$this->ci->form_validation->set_rules( $_keyadd, $this->ci->lang->line($_keyadd), $this->params['fieldadd'][$_keyadd]['validation'] );
					if(@$this->params['fieldadd'][$_keyadd]['type'] == "primarykey"){
						if( @$this->params['fieldadd'][$_keyadd]['hidden'] == true ){
							$this->ci->form_validation->set_rules( $_keyadd, $this->ci->lang->line($_keyadd), "is_unique[".$this->params['table'].".".$_keyadd."]" );
						} else {
							$this->ci->form_validation->set_rules( $_keyadd, $this->ci->lang->line($_keyadd), "required|is_unique[".$this->params['table'].".".$_keyadd."]" );
						}
					}
					$arr_validation = explode("|", $this->params['fieldadd'][$_keyadd]['validation']);
					if( in_array("required", $arr_validation)) {
						$this->params['fieldadd'][$_keyadd]['is_required'] = "required";
					}
					if( !$this->ci->form_validation->run() ){ // Validation Error
						$validation_message = form_error($_keyadd);
						$valid = false;
					}

					if($validation_message != ""){
						$valid = false;
					}
				}

				if(@$this->params['fieldadd'][$_keyadd]['is_required'] == true){
					$rsign = "<span class='requiredsign'>*</span>";
				}

				if(@$this->params['fieldadd'][$_keyadd]['hidden']==true){
					$this->params['content'].="<input type='hidden' value='".@$this->params['fieldadd'][$_keyadd]['value']."' name='".$_keyadd."' id='".$_keyadd."'/>";
				}
				else {
					$_type=(!isset($this->params['fieldadd'][$_keyadd]['type'])) ? "text" : $this->params['fieldadd'][$_keyadd]['type'] ;
					if($_type=="primarykey"){
						$_type="text";
					}

					if($_type=="textarea"){
						$this->params['content'].="
						<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<textarea ".$isreadonly." ".$isdisabled." name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldadd'][$_keyadd]['placeholder']."' ".@$this->params['fieldadd'][$_keyadd]['is_required']." class='form-control' maxlength='".@$this->params['fieldadd'][$_keyadd]['maxlength']."'>".@$this->params['fieldadd'][$_keyadd]['value']."</textarea>
									".$addon."
									".$description."
								</div>
								".$validation_message."
							</div>
						</div>";
					}
					elseif( $_type == "separator"){
						$this->params['content'].="
							<div class='form-separator text-primary'>".$caption."</div>
						";
					}
					elseif($_type=="wysiwyg"){
						$this->params['content'].="
						<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<textarea ".$isreadonly." ".$isdisabled." name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldadd'][$_keyadd]['placeholder']."' ".@$this->params['fieldadd'][$_keyadd]['is_required']." class='form-control' maxlength='".@$this->params['fieldadd'][$_keyadd]['maxlength']."' novalidate>".@$this->params['fieldadd'][$_keyadd]['value']."</textarea>
									".$addon."
									".$description."
								</div>
								".$validation_message."
							</div>
						</div>
						<script type='text/javascript'>
							$(document).ready(function(){
								var editor_id = '".$_keyadd."';
								tinymce.EditorManager.execCommand('mceRemoveEditor',true, editor_id);
								tinymce.EditorManager.execCommand('mceAddEditor',true, editor_id);
							});
						</script>";
					}
					elseif($_type=="dropdownquery"){
						$option="";
						$optgroup="";
						$multiple = "";
						$addButton = "";
						foreach($this->params['fieldadd'][$_keyadd]['sourcequery'] as $opdata){
							if(isset($opdata['labeldt'])){
								if($optgroup!=$opdata['labeldt']){
									$option.="<optgroup label='".$opdata['labeldt']."'>";
									$optgroup=$opdata['labeldt'];
								}
							}
							$selected = ($opdata['keydt']==@$this->params['fieldadd'][$_keyadd]['value']) ? "selected" : null ;
							$option.="<option ".$selected." value='".$opdata['keydt']."'>".$opdata['valuedt']."</option>";
						}
						$name = "name='".$_keyadd."'";
						if( @$this->params['fieldadd'][$_keyadd]['multiple'] ){
							$multiple = "multiple='multiple'";
							$name = "name='".$_keyadd."[]'";
						}

						if( @$this->params['fieldadd'][$_keyadd]['allowadd'] == true || @$this->params['fieldadd'][$_keyadd]['allowedit'] == true){
							$addButton = "";
							$titleType = "add";
							if( @$this->params['fieldadd'][$_keyadd]['allowadd'] == true ){
								$titleType = "add";
								$addButton .= "
									<button class='btn btn-light-primary' type='button' id='add_".$_keyadd."'  data-bs-toggle='modal' data-target='#modal_".$_keyadd."'><i class='fa fa-plus'></i> </button>
									<script type='text/javascript'>
										$(function(){
											$('#add_".$_keyadd."').click(function(){
												$('#modal_body_".$_keyadd."').load('".$this->params['fieldadd'][$_keyadd]['addurl']."');
											});
										});
						      </script>
									";
							}
							if( @$this->params['fieldadd'][$_keyadd]['allowedit'] == true ){
								$titleType = "edit";
								$addButton .= "
									<button class='btn btn-light-primary' type='button' id='edit_".$_keyadd."'  data-bs-toggle='modal' data-target='#modal_".$_keyadd."'><i class='far fa-edit'></i> </button>
									<script type='text/javascript'>
										$(function(){
											$('#edit_".$_keyadd."').click(function(){
												$('#modal_body_".$_keyadd."').load('".$this->params['fieldadd'][$_keyadd]['editurl']."/edit/pk/".$_keyadd."ID/valpk/' + $('#".$_keyadd."').val());
											});
										});
						      </script>
									";
							}
							$addButton .= "
							<!-- Modal -->
							<div class='modal fade' id='modal_".$_keyadd."' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
							  <div class='modal-dialog modal-lg' role='document'>
							    <div class='modal-content'>
							      <div class='modal-header'>
							        <h5 class='modal-title' id='exampleModalLabel'>".$this->ci->lang->line($titleType)." ".$caption."</h5>
							        <button type='button' class='close' data-bs-dismiss='modal' aria-label='Close'>
							          <span aria-hidden='true'>&times;</span>
							        </button>
							      </div>
							      <div class='modal-body' id='modal_body_".$_keyadd."'>
							        
							      </div>
							      <div class='modal-footer'>
							        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
							      </div>
							    </div>
							  </div>
							</div>
							";
						}

						$this->params['content'].="
						<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<select  ".$isreadonly." ".$isdisabled." ".$multiple." ".$name." id='".$_keyadd."' class='form-select' ".@$this->params['fieldadd'][$_keyadd]['is_required'].">
										".$option."
									</select>
									".$addon." ".$addButton." ".$description."
								</div>
								".$validation_message."
							</div>
						</div>";
						// if( $multiple != "" ){
						// 	$this->params['content'] .= "
						// 		<script type='text/javascript'>
						// 		$(function() {
						//         $('#".$_keyadd."').multipleSelect({
						//             width: '100%'
						//         });
						//     });
						// 	</script>";
						// }
					}
					elseif($_type=="dropdownarray"){
						$option="";
						$multiple="";
						foreach(array_keys($this->params['fieldadd'][$_keyadd]['sourcearray']) as $opdata){
							$selected=($opdata==@$this->params['fieldadd'][$_keyadd]['value']) ? "selected" : null ;
							$option.="<option ".$selected." value='".$opdata."'>".$this->params['fieldadd'][$_keyadd]['sourcearray'][$opdata]."</option>";
						}
						$name = "name='".$_keyadd."'";
						if( @$this->params['fieldadd'][$_keyadd]['multiple'] ){
							$multiple = "multiple='multiple'";
							$name = "name='".$_keyadd."[]'";
						}
						$this->params['content'].="
						<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<select  ".$isreadonly." ".$isdisabled." ".$multiple." ".$name." id='".$_keyadd."' class='form-select' ".@$this->params['fieldadd'][$_keyadd]['is_required'].">
										".$option."
									</select>
									".$addon."
									".$description."
								</div>
								".$validation_message."
							</div>
						</div>";
						/*if( $multiple != "" ){
							$this->params['content'] .= "
								<script type='text/javascript'>
								$(function() {
						        $('#".$_keyadd."').multipleSelect({
						            width: '100%'
						        });
						    });
							</script>";
						}*/
					}
					elseif($_type=="popup") {
							$name = "name='".$_keyadd."'";
							$url = $this->params['fieldadd'][$_keyadd]['popup_url'];
							$this->params['content'].= "
							<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<input readonly type='text' ".$isdisabled." ".$isreadonly." class='form-control' ".$name." id='".$_keyadd."' />";
							if( $isdisabled == null && $isreadonly == null ){
								$this->params['content'].= "
									<button class='btn btn-light-primary  btn-popup' type='button'  id='btn-popup-".$_keyadd."'> <i class='fa fa-search'></i> </button>
								";
							}
							$this->params['content'].= "</div>".$validation_message."</div></div>";
							$this->params['content'].= "<script type='text/javascript'>
								$(function(){
									$('#btn-popup-".$_keyadd."').click(function( e ){
										e.preventDefault();
										loadinputmodal('".$url."/trigger/".$_keyadd."');
									});
								});
							</script>";
					}
					elseif($_type=="checkgroup"){
						$option="";
						foreach($this->params['fieldadd'][$_keyadd]['sourcequery'] as $opdata){
							$selected=($opdata['keydt']==@$this->params['fieldadd'][$_keyadd]['value']) ? "checked" : null ;
							$option.="<label><input class='form-control' type='checkbox' name='CHECK_".$opdata['keydt']."' ".$selected.">
							<span class='lbl' style='margin-left:5px;'> ".$opdata['valuedt']." </span></label>";
						}
						$this->params['content'].="
						<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									".$option."
									".$addon."
									".$description."
								</div>
								".$validation_message."
							</div>
						</div>";
					}
					elseif($_type=="checkbox"){
						$name = "name='".$_keyadd."'";
						$this->params['content'].="
						<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<span class='switch switch-sm'>
										<label>
											<input type='checkbox' ".$name." id='".$_keyadd."' class='form-control' />
											<span></span>
										</label>
									</span>
									".$addon."
									".$description."
								</div>
								".$validation_message."
							</div>
						</div>";
					}
					elseif($_type=="file"){
						$dir = isset($this->params['fieldadd'][$_keyadd]['dir']) ? $this->params['fieldadd'][$_keyadd]['dir'] : "uploads";
						$img = "<img src='' class='thumbnail' style='width:100%;' />";
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group custom-file'>
									<input class='form-control' ".@$this->params['fieldadd'][$_keyadd]['required']." type='file' id='FILE_".$_keyadd."' name='FILE_".$_keyadd."' value='' />".$addon."
									<input type='hidden' id='".$_keyadd."' name='".$_keyadd."' value='' />
									".$description."
								</div>
								".$validation_message."
								<div class='img-container' id='IMG_".$_keyadd."'>".$img."</div>
							</div>
							</div>
							<script type='text/javascript'>
								$('#FILE_".$_keyadd."').on('click', function() {
									$(this).val('');
								});
								$('#FILE_".$_keyadd."').on('change', function() {
						      var fileName = $(this).val();
						      $(this).next('.custom-file-label').addClass('selected').html(fileName);
							  });
								$('#FILE_".$_keyadd."').on('change', function(){
									// $('#IMG_".$_keyadd."').remove();

									var reader = new FileReader();
							    reader.onload = function (e) {
							    	if( e.target.result.substring(5,14) == 'video/mp4' || e.target.result.substring(5,14) == 'video/avi' ){
							    		var loaded = '<video width=\"100%\" height=\"400px\" controls><source src=\"' + e.target.result + '\" type=\"' + e.target.result.substring(5,14) + '\">Browser Anda tidak support dengan pemutar video.</video>';
							    		$('#IMG_".$_keyadd."').html( loaded );
							    	} else if(e.target.result.substring(5,20) == 'application/pdf') {
							    		var loaded = '<object data=\"' + e.target.result + '\" type=\"application/pdf\" width=\"100%\" height=\"600px\"><p><b>Gagal Memuat PDF</b>: Browser Ini tidak mendukung penampil PDF.</p></object>';
											$('#IMG_".$_keyadd."').html( loaded );
							    	} else {
						    			$('#IMG_".$_keyadd."').html('<img class=\"thumbnail\" src=\"' + e.target.result + '\" style=\"width:100%;\" />');
							    	}
							    };

							    // read the image file as a data URL.
							    reader.readAsDataURL(this.files[0]);

									ajaxupload('".$_keyadd."', '".$dir."');
									return false;
								});
							</script>
							";
					}
					elseif($_type=="blob"){
						$img = "<img src='' class='thumbnail' style='width:100%;' />";
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group custom-file'>
									<input class='form-control' ".@$this->params['fieldadd'][$_keyadd]['required']." type='file' id='FILE_".$_keyadd."' name='FILE_".$_keyadd."' value='' />".$addon."
									<input type='hidden' id='".$_keyadd."' name='".$_keyadd."' value='' />
									".$description."
								</div>
								".$validation_message."
								<div class='img-container' id='IMG_".$_keyadd."'>".$img."</div>
							</div>
							</div>
							<script type='text/javascript'>
								// generate Blob String
								$('#FILE_".$_keyadd."').on('click', function() {
									$(this).val('');
								});
								$('#FILE_".$_keyadd."').on('change', function() {
						      var fileName = $(this).val();
						      $(this).next('.custom-file-label').addClass('selected').html(fileName);
							  });
								$('#FILE_".$_keyadd."').on('change', function(){
									// $('#IMG_".$_keyadd."').remove();

									var img = new Image();
									img.crossOrigin = 'Anonymous';

									var reader = new FileReader();
							    reader.onload = function (e) {
							    	if( e.target.result.substring(5,14) == 'video/mp4' || e.target.result.substring(5,14) == 'video/avi' ){
							    		var loaded = '<video width=\"100%\" height=\"400px\" controls><source src=\"' + e.target.result + '\" type=\"' + e.target.result.substring(5,14) + '\">Browser Anda tidak support dengan pemutar video.</video>';
							    		$('#IMG_".$_keyadd."').html( loaded );
							    	} else if(e.target.result.substring(5,20) == 'application/pdf') {
							    		var loaded = '<object data=\"' + e.target.result + '\" type=\"application/pdf\" width=\"100%\" height=\"600px\"><p><b>Gagal Memuat PDF</b>: Browser Ini tidak mendukung penampil PDF.</p></object>';
											$('#IMG_".$_keyadd."').html( loaded );
							    	} else {
						    			$('#IMG_".$_keyadd."').html('<img class=\"thumbnail\" src=\"' + e.target.result + '\" style=\"width:100%;\" />');
							    	}
							    };
							    reader.readAsDataURL(this.files[0]);

							    img.onload = function () {
								    // read the image file as a data URL.
								    var canvas = document.createElement('canvas'),
									    ctx = canvas.getContext('2d');

									  canvas.height = img.naturalHeight;
									  canvas.width = img.naturalWidth;
									  ctx.drawImage(img, 0, 0);

									  // Unfortunately, we cannot keep the original image type, so all images will be converted to PNG
									  // For this reason, we cannot get the original Base64 string
									  var uri = canvas.toDataURL('image/jpeg'),
									    b64 = uri.replace(/^data:image.+;base64,/, '');

										$('#".$_keyadd."').val( b64 );
									}
							    
									return false;
								});
							</script>
							";
					}
					elseif($_type=="multiplefile"){
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								
							</div>
						";
					}
					elseif($_type == "map"){
						$default_position = $this->configurations['MAP_DEFAULT_LOCATION'];
						$latlng = explode(",", $default_position);
						$default_lat = $latlng[0];
						$default_lng = $latlng[1];
						$addressChain = "";
						if( isset($this->params['fieldadd'][$_keyadd]['address_id'] )){
							$id = $this->params['fieldadd'][$_keyadd]['address_id'];
							$addressChain = "
							var input = document.getElementById('".$id."');
							var autocomplete = new google.maps.places.Autocomplete(input);
							autocomplete.bindTo('bounds', map);
							autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);
							
							/*var infowindow = new google.maps.InfoWindow();
			        var infowindowContent = document.getElementById('infowindow-content');
			        infowindow.setContent(infowindowContent);
			        var marker = new google.maps.Marker({
			          map: map,
			          anchorPoint: new google.maps.Point(0, -29)
			        });*/

			        autocomplete.addListener('place_changed', function() {
		          
		          /*infowindow.close();*/

			          marker.setVisible(false);
			          var place = autocomplete.getPlace();
			          if (!place.geometry) {
			            // User entered the name of a Place that was not suggested and
			            // pressed the Enter key, or the Place Details request failed.
			            window.alert('No details available for input: ' + place.name);
			            return;
			          }

		          // If the place has a geometry, then present it on a map.

		          if (place.geometry.viewport) {
		            map.fitBounds(place.geometry.viewport);
		          } else {
		            map.setCenter(place.geometry.location);
		            map.setZoom(17);  // Why 17? Because it looks good.
		          }

		          marker.setPosition(place.geometry.location);
		          marker.setVisible(true);
		          var lat = place.geometry.location.lat();
		          var lng = place.geometry.location.lng();
							$('#".$_keyadd."').val(eval(lat) + ',' + eval(lng));
							$('#".$_keyadd."').change();

		          var address = '';
		          if (place.address_components) {
		            address = [
		              (place.address_components[0] && place.address_components[0].short_name || ''),
		              (place.address_components[1] && place.address_components[1].short_name || ''),
		              (place.address_components[2] && place.address_components[2].short_name || '')
		            ].join(' ');
		          }

		          /*
		          infowindowContent.children['place-icon'].src = place.icon;
		          infowindowContent.children['place-name'].textContent = place.name;
		          infowindowContent.children['place-address'].textContent = address;
		          infowindow.open(map, marker);
		          */
		        });
							";
						}
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<input type='text' value='".$default_lat.",".$default_lng."' class='form-control' id='".$_keyadd."' name='".$_keyadd."' value='' />
									".$addon."
									".$description."
								</div>
								".$validation_message."
								<div class='map-container' id='MAP_".$_keyadd."' style='height:300px;'></div>
								<p class='text-primary mt-2 text-center'>Geser Titik Merah untuk Mendapatkan lokasi Koordinat</p>
							</div>
							</div>
							<script type='text/javascript'>
								var map, infoWindow;
								".$_keyadd."_initMap();
								function ".$_keyadd."_initMap() {
									var location = { lat: ".$default_lat.", lng: ".$default_lng." };
							    var map = new google.maps.Map(document.getElementById('MAP_".$_keyadd."'), {
							      zoom: 18,
							      center: location
							    });

							    ".$addressChain."

							    // Add a marker at the center of the map.
							    var marker = new google.maps.Marker({
							      position: location,
							      title: 'Pilih Lokasi',
							      map: map,
							      draggable: true,
							   		animation: google.maps.Animation.DROP
							    });
							    google.maps.event.addListener(marker, 'dragend', function (event) {
							    	var lat = this.getPosition().lat();
							    	var lng = this.getPosition().lng();
							    	map.setCenter({lat: eval(lat),lng: eval(lng)});
								    $('#".$_keyadd."').val(eval(lat) + ',' + eval(lng));
										$('#".$_keyadd."').change();
									});

									$('#".$_keyadd."').on('blur', function(){
										var gpsloc = $(this).val().split(',');
										var lat = eval(gpsloc[0]);
										var lng = eval(gpsloc[1]);
										map.setCenter({ lat: lat, lng: lng });
			            	map.setZoom(18);
					          marker.setPosition({ lat: lat, lng: lng });
					          $('#".$_keyadd."').change();
									});
							  }
							</script>";
					}
					elseif($_type == "table"){
						$this->params['content'].="
						<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div id='".$_keyadd."' class='table-responsive'>
									
								</div>
								<script type='text/javascript'>
									$(function(){
										$('#".$_keyadd."').load('".$this->params['fieldadd'][$_keyadd]["url"]."', function(response,status,xhr){
											if( xhr.status != 200){
												$('#".$_keyadd."').html(xhr.responseText);
											}
										});
									});
								</script>
								".$validation_message."
							</div>
						</div>";
					}
					else {
						$step = "";
						$dateclass="";
						$append="";
						$js="";
						$min= isset($this->params['fieldadd'][$_keyadd]['min']) ? " min='".$this->params['fieldadd'][$_keyadd]['min']."' " : null;
						$max= isset($this->params['fieldadd'][$_keyadd]['max']) ? " max='".$this->params['fieldadd'][$_keyadd]['max']."' " : null;
						$currencyAdd = "";
						$currencyClass = "";
						if($_type=="date"){
							$_type="text";
							$dateclass="date-picker";
							$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
						}
						elseif($_type=="month"){
							$_type="text";
							$dateclass="month-picker";
							$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
						}
						elseif($_type=="time"){
							$_type="text";
							$dateclass="time-picker";
							$append="<span class='input-group-text'><i class='fas fa-clock'></i></span>
								<script type='text/javascript'>
									$('#".$_keyadd.", #".$_keyadd."_modal').flatpickr({
				            enableTime: true,
								    noCalendar: true,
								    dateFormat: 'H:i',
					        });
								</script>
							";
						}
						elseif($_type=="datetime"){
							$_type="text";
							$dateclass="datetime-picker";
							$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
						}
						elseif($_type=="decimal"){
							$step = "step='0.01'";
							$_type="number";
							if( @$this->params['fieldadd'][$_keyadd]['decimalplace'] == 1){
								$step = "step='0.1'";
							}
							if( @$this->params['fieldadd'][$_keyadd]['decimalplace'] == 3){
								$step = "step='0.001'";
							}
						}
						elseif($_type=="number"){
							$_type="number";
							$_value = round($value,0);
						}
						elseif($_type=="currency"){
							$_type="number";
							$step = "step='0.01'";
							$currencyAdd = " data-number-to-fixed='2' data-number-stepfactor='100'";
							$currencyClass = " currency";
						}
						elseif($_type=="tagsinput"){
							$js .= "<script type='text/javascript'>
								$('#".$_keyadd."').tagsinput();
							</script>";
							$_type = "text";
						}

						if( @$this->params['fieldadd'][$_keyadd]['typeahead'] != "" ){
							$js .= "<script type='text/javascript'>
				        var sources = new Bloodhound({
				            datumTokenizer: Bloodhound.tokenizers.whitespace,
				            queryTokenizer: Bloodhound.tokenizers.whitespace,
				            prefetch: '".$this->params['fieldadd'][$_keyadd]['typeahead']."'
				        });

				        $('#".$_keyadd.", #".$_keyadd."_modal').typeahead(null, {
				            name: 'sources',
				            source: sources
				        });
							</script>";
						}

						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".$dateclass." ".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<input ".$min." ".$max." ".$isreadonly." ".$isdisabled." value='".@$this->params['fieldadd'][$_keyadd]['value']."' type='".$_type."' ".$currencyAdd." ".$step." name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldadd'][$_keyadd]['placeholder']."' ".@$this->params['fieldadd'][$_keyadd]['is_required']." ".$isreadonly." class='form-control ".$currencyClass."' maxlength='".@$this->params['fieldadd'][$_keyadd]['maxlength']."' />".$append."
									".$addon."
									".$description."
								</div>
								".$validation_message."
							</div>
						</div>" . $js;
					}
				}
			}
			if( !isset($this->params['showcontrol']) || $this->params['showcontrol'] != false ){
				$this->params['content'].="<div class='form-group row mb-3'>
					<div class='offset-md-2 col-md-6'>
						<button class='btn btn-primary' type='submit'><i class='fa fa-save'></i>".$this->ci->lang->line("submit")."</button>
						<button class='btn btn-danger' type='reset'><i class='fa fa-sync-alt'></i>".$this->ci->lang->line("reset")."</button>
					</div>
				</div>";
			}
			$this->params['content'].="</form>";
			$this->params['content'].='</div>';
			

		}

		$target_post = $this->filename."/add/";
		if( isset($this->params['target_post']) ){
			$target_post = $this->filename . "/" . $this->params['target_post'] ."/";
		}
		$this->params['content'].='<script type="text/javascript">
			$(document).ready(function(){

				$(this).updatePolyfill();

				$(".ws-number").unbind("mousewheel");
				$(".ws-number").unbind("wheel");
				

				let parentModal = $(".select2 > .input-group > select").parents("#modalContainer");
				let parentPopup = $(".select2 > .input-group > select").parents("#modalPopupContainer");
				let parentInput = $(".select2 > .input-group > select").parents("#modalInputContainer");

				if( parentModal.length > 0 ) {
					var select2_parent = $("#modalContainer");
				} 
				if( parentPopup.length > 0 ){
					var select2_parent = $("#modalPopupContainer");
				} 
				if( parentInput.length > 0 ){
					var select2_parent = $("#modalInputContainer");
				} 
				if( parentPopup.length == 0 && parentModal.length == 0 && parentInput.length == 0){
					var select2_parent = $(document.body);
				}

				$(".select2 > .input-group > select").select2({
					placeholder: $(this).attr("placeholder"),
					allowClear: Boolean($(this).data("allow-clear")),
					dropdownParent: select2_parent
				});

				$(".date-picker input").flatpickr({
					dateFormat: "Y-m-d",
					// disableMobile: true
					
				});
				$(".month-picker input").flatpickr({
					dateFormat: "Y-m",
				  minViewMode: 1,
				});
				$(".datetime-picker input").flatpickr({
					enableTime: true,
					dateFormat: "Y-m-d H:i",
					time_24hr: true
				});
				$(".time-picker input").flatpickr({
					enableTime: true,
					noCalendar: true,
					dateFormat: "H:i:S",
					enableSeconds: true,
					time_24hr: true
				});
				$(".popover").popover({
						html: true,
						trigger: "hover"
				});
				$("#'.$this->formid.'").submit(function(){
					$("#'.$this->maincontent.'").html("<div class=\"text-center spin-loading\"><img src=\"" + base_url + "assets/img/spin.svg\" /></div>");
  				var datapost=$(this).serialize();
					var target="'.$target_post.'";
					loadpost("'.$this->maincontent.'", target, datapost);
					return false;
				});

				$(".date-picker input, .datetime-picker input, .time-picker input").after("<button type=\"button\" class=\"btn btn-clear btn-flatpicker-clear\"><i class=\"fas fa-times\"></i></button>");
				$(".btn-flatpicker-clear").click(function(){
					// $(this).parents(".input-group").find("input").val("");
					$(this).parents(".input-group").find("input").flatpickr().clear();
				});
			});
		</script>
		';
		// Inserting Data POst
		if( count($post) > 0 && $valid == true){
			$primarykey = "";
			foreach($this->params['fieldadd'] as $_keyadd => $_params){
				if(@$_params['type'] == "primarykey" ){
					$primarykey = $_keyadd;
				}
				if(@$_params['type'] == "primarykey" && @$_params['hidden'] == true ){
					unset($post[$_keyadd]);
				}
				if(@$_params['type'] == "number" && @$post[$_keyadd] == ""){
					unset($post[$_keyadd]);
				}
				if(@$_params['type'] == "decimal" && @$post[$_keyadd] == ""){
					unset($post[$_keyadd]);
				}
				if(@$_params['type'] == "separator"){
					unset($post[$_keyadd]);
				}
				if(@$_params['type'] == "table"){
					unset($post[$_keyadd]);
				}
				// if(@$_params['disabled'] == true){
				// 	unset($post[$_keyadd]);
				// }
				if(@$_params['type'] == "blob"){
					$ex = explode(",", $post[$_keyadd]);
					if( $this->ci->db->platform() == "mssql" || $this->ci->db->platform() == "sqlsrv"){
						$this->ci->db->set($_keyadd, "cast('' as xml).value('xs:base64Binary(\"".$ex[1]."\")', 'varbinary(max)')", FALSE);
					} elseif($this->ci->db->platform() == "mysqli") {
						$this->ci->db->set($_keyadd, "from_base64('".$ex[1]."')", FALSE);
					}
					unset($post[$_keyadd]);
				}
			}
			if( isset($this->params['unset_post']) ){
				$this->params['unset_post'] = explode(",", $this->params['unset_post']);
				foreach( $this->params['unset_post'] as $unset){
					unset($post[$unset]);
				}
			}
			
			if( isset($this->params['dbconn'] ) ){
				$this->ci->load->database($this->params['dbconn'], FALSE, TRUE);
			}

			if( @$this->params['skipinsert'] == true ){
				return 1;
			} else {
				$ins = $this->ci->db->insert($this->params['table'],$post);
				if( $ins ){
					if( @$this->params['wizard'] == true ){
						return 1;
						exit;
					} else {
						// $_POST = array();
						// $next = isset($this->params['nextmethod']) ? $this->params['nextmethod'] : "add" ;
						$next = isset($this->params['nextmethod']) ? $this->params['nextmethod'] : "browse" ;
						$this->params['content'] = '<script type="text/javascript">
							loadcontent("'.$this->maincontent.'","'.$this->filename.'/'.$next.'/", true);
						</script>';
					}
				} else {
					$error = $this->ci->db->error();
					$this->params['content'] .= '<script type="text/javascript">
						showToast("Gagal Insert karena : <br />'.$error['message'].'" );
					</script>';
				}
			}


			if( isset($this->params['dbconn'] ) ){
				$this->ci->load->database("default", FALSE, TRUE);
			}
		}
		$this->mainfunction($this->params);
		return $this->str;
	}

	public function edit(){
		$post = $this->ci->input->post();
		if( $this->iscrypt == TRUE  && count($post) == 0 ){
			$urlparams = base64_decode($this->ci->uri->segment(4));
			$urlparams = $this->ci->encryption->decrypt($urlparams);
			$exp = explode("/", $urlparams);

			$this->urisegments['pk'] = $exp[1];
			$this->urisegments['valpk'] = $exp[3];
		}
		if(isset($this->params['urisegments']['pk'])){
			$this->urisegments['pk'] = $this->params['urisegments']['pk'];
			$this->urisegments['valpk'] = $this->params['urisegments']['valpk'];
		}
		$command=explode(",",$this->params['command']);
		$_cmdtext="";
		$_cmdtext.=(in_array("browse",$command)) ? "<a title='".$this->ci->lang->line("back")."' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn btn-light-primary '><i class='fa fa-arrow-left'></i> ".$this->ci->lang->line("back")."</a>" : '' ;
		
		$valid = true;

		$alert = "";
		if(isset($this->params['alert'])){
			$alert = '<div class="alert alert-'.@$this->params["alert"]["type"].'">
				<div class="alert-text">
					'.@$this->params['alert']['message'].'
				</div>
			</div>';
		}
		$keySelect = array();
		foreach(array_keys($this->params['fieldadd']) as $_keyadd){
			if(@$this->params['fieldadd'][$_keyadd]['type'] != "separator"){
				$keySelect[$_keyadd] = $this->params['fieldadd'][$_keyadd];
			}
		}

		$query="SELECT ".implode(", ",array_keys($keySelect))." FROM ".$this->params['table'];
		if(isset($this->urisegments['pk']) && isset($this->urisegments['valpk'])){
			$query.=" WHERE ".$this->urisegments['pk']."='".urldecode($this->urisegments['valpk'])."'";
		}
		if(isset($this->params['sqlupdate'])){
			$query = $this->params['sqlupdate'];
			// check apakah ada Where
			$w = " WHERE ";
			if( strpos( $query, "WHERE"  ) == true ){
				$w = " AND ";
			}
			$query.= $w . " ".$this->params['table'].".".$this->urisegments['pk']."='".urldecode($this->urisegments['valpk'])."'";
		}

		if( isset($this->params['dbconn'] ) ){
			$this->ci->load->database($this->params['dbconn'], FALSE, TRUE);
		}

		$query=$this->ci->db->query($query)->result_array();
		
		$data=$query[0];
		if(isset($this->params['formheader']) && $this->params['formheader'] == false){
			$this->params['content']='
			<div class="card card-custom gutter-b example example-compact">
				<div class="card-body">';
		} else {
			$this->params['content']='
			<div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
          <h3 class="card-title"><i class="far fa-edit me-5"></i>'.$this->ci->lang->line("edit") . ' ' . $this->params['name'].'</h3>
          <div class="card-toolbar">
            <div class="example-tools justify-content-center">
            	'.$_cmdtext.'
            </div>
          </div>
        </div>
        <div class="card-body">';
		}
		$this->params['content'].= "<div id='alert'>" . $alert . "</div>";
		if( isset($this->params['view']) ){
			$this->params['data'] = (isset($this->params['data'])) ? $this->params['data'] : array();
			$this->params['content'].= $this->ci->load->view($this->params['view'], $this->params['data'], TRUE);
		} else {
			$this->params['content'].= "<form novalidate class='form-horizontal' id='".$this->formid."'>";
			foreach(array_keys($this->params['fieldadd']) as $_keyadd){
				$addon = "";
				$rsign = "";
				$description = "";
				$caption = isset($this->params['fieldadd'][$_keyadd]['caption']) ? $this->params['fieldadd'][$_keyadd]['caption'] : $this->ci->lang->line($_keyadd);
				$validation_message = (isset($this->params['fieldadd'][$_keyadd]['validation_message'])) ? $this->params['fieldadd'][$_keyadd]['validation_message'] : "";
				if( isset($this->params['fieldadd'][$_keyadd]['description']) ){
					$description = "<a href='javascript:void();' class='popover' data-container='body' data-bs-toggle='popover' data-placement='top' data-title='".$this->ci->lang->line($_keyadd)."' data-content='".$this->params['fieldadd'][$_keyadd]['description']."'>
					  <span class='fa fa-question-circle'></span>
					</a>
					";
				}
				// Form Validation
				if( count($post) > 0){
					$data[$_keyadd] = set_value($_keyadd);
				}
				if( isset($this->params['fieldadd'][$_keyadd]['validation']) ){
					$this->ci->form_validation->set_rules( $_keyadd, $this->ci->lang->line($_keyadd), $this->params['fieldadd'][$_keyadd]['validation'] );
					$arr_validation = explode("|", $this->params['fieldadd'][$_keyadd]['validation']);
					if( in_array("required", $arr_validation)) {
						$this->params['fieldadd'][$_keyadd]['is_required'] = "required";
					}
					if( !$this->ci->form_validation->run() ){ // Validation Error
						$validation_message = form_error($_keyadd);
						$valid = false;
					}

					if($validation_message != ""){
						$valid = false;
					}

				}

				if(@$this->params['fieldadd'][$_keyadd]['is_required'] == true){
					$rsign = "<span class='requiredsign'>*</span>";
				}

				if(isset($this->params['fieldadd'][$_keyadd]['help'])){
					$addon = "<span class='input-group-text'>".$this->params['fieldadd'][$_keyadd]['help']."</span>";
				}
				
				if(isset($this->params['fieldadd'][$_keyadd]['button'])){
					$addon = "<button type='button' class='btn btn-light-primary' id='btn-".$_keyadd."'>".$this->params['fieldadd'][$_keyadd]['button']."</button>";
				}

				$isreadonly=(@$this->params['fieldadd'][$_keyadd]['readonly']==true) ? "readonly" : null;
				$isdisabled=(@$this->params['fieldadd'][$_keyadd]['disabled']==true) ? "disabled" : null;
				
				if( @$this->params['disabled'] == true ){
					$isdisabled ="disabled";
				}

				if( @$this->params['readonly'] == true ){
					$isreadonly ="readonly";
				}

				if(@$this->params['fieldadd'][$_keyadd]['hidden']==true){
					$this->params['content'].="<input type='hidden' name='".$_keyadd."' id='".$_keyadd."' value='".$data[$_keyadd]."' />";
				}
				else {
					$_type=(!isset($this->params['fieldadd'][$_keyadd]['type'])) ? "text" : $this->params['fieldadd'][$_keyadd]['type'] ;
					$isdisabled=($_type=="primarykey") ? "readonly" : $isdisabled;
					if($_type=="primarykey"){
						$_type="text";
					}
					if($_type=="dropdown"){

					}
					elseif($_type=="textarea"){
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<textarea ".$isreadonly." ".$isdisabled."  name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldadd'][$_keyadd]['placeholder']."' ".@$this->params['fieldadd'][$_keyadd]['is_required']." class='form-control' maxlength='".@$this->params['fieldadd'][$_keyadd]['maxlength']."'>".htmlentities($data[$_keyadd], ENT_QUOTES)."</textarea>
									".$addon."
									".$description."
								</div>
								".$validation_message."
							</div>
						</div>";
					}
					elseif( $_type == "separator"){
						$this->params['content'].="
							<div class='form-separator text-primary'>".$caption."</div>
						";
					}
					elseif($_type=="wysiwyg"){
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<textarea ".$isreadonly." ".$isdisabled."  name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldadd'][$_keyadd]['placeholder']."' ".@$this->params['fieldadd'][$_keyadd]['is_required']." class='form-control' maxlength='".@$this->params['fieldadd'][$_keyadd]['maxlength']."' novalidate>".htmlentities($data[$_keyadd], ENT_QUOTES)."</textarea>
									".$addon."
									".$description."
								</div>
								".$validation_message."
							</div>
						</div>
						<script type='text/javascript'>
							$(document).ready(function(){
								var editor_id = '".$_keyadd."';
								tinymce.EditorManager.execCommand('mceRemoveEditor',true, editor_id);
								tinymce.EditorManager.execCommand('mceAddEditor',true, editor_id);
							});
						</script>
						";
					}
					elseif($_type=="dropdownquery"){
						$option="";
						$selected="";
						$optgroup="";
						$multiple = "";

						$multiplevalues = array();
						$addButton = "";
						$name = "name='".$_keyadd."'";
						if( @$this->params['fieldadd'][$_keyadd]['multiple'] ){
							$multiple = "multiple='multiple'";
							$name = "name='".$_keyadd."[]'";
							// $multiplevalues = json_decode($data[$_keyadd]);
							$multiplevalues = explode(",",$data[$_keyadd]);
						}

						foreach($this->params['fieldadd'][$_keyadd]['sourcequery'] as $opdata){

							if(isset($opdata['labeldt'])){
								if($optgroup!=$opdata['labeldt']){
									$option.="<optgroup label='".$opdata['labeldt']."'>";
									$optgroup=$opdata['labeldt'];
								}
							}
							if($multiple != ""){
								$selected = @in_array($opdata['keydt'], $multiplevalues) ? "selected" : null;
							} else {
								$selected = ($data[$_keyadd]==$opdata['keydt']) ? "selected" : null;
							}
							$option.="<option ".$selected." value='".$opdata['keydt']."'>".$opdata['valuedt']."</option>";

						}

						if( @$this->params['fieldadd'][$_keyadd]['allowadd'] == true || @$this->params['fieldadd'][$_keyadd]['allowedit'] == true){
							$addButton = "";
							$titleType = "add";
							if( @$this->params['fieldadd'][$_keyadd]['allowadd'] == true ){
								$titleType = "add";
								$addButton .= "
									<button class='btn btn-light-primary' type='button' id='add_".$_keyadd."'  data-bs-toggle='modal' data-target='#modal_".$_keyadd."'><i class='fa fa-plus'></i> </button>
									<script type='text/javascript'>
										$(function(){
											$('#add_".$_keyadd."').click(function(){
												$('#modal_body_".$_keyadd."').load('".$this->params['fieldadd'][$_keyadd]['addurl']."');
											});
										});
						      </script>
									";
							}
							if( @$this->params['fieldadd'][$_keyadd]['allowedit'] == true ){
								$titleType = "edit";
								$addButton .= "
									<button class='btn btn-light-primary' type='button' id='edit_".$_keyadd."'  data-bs-toggle='modal' data-target='#modal_".$_keyadd."'><i class='far fa-edit'></i> </button>
									<script type='text/javascript'>
										$(function(){
											$('#edit_".$_keyadd."').click(function(){
												$('#modal_body_".$_keyadd."').load('".$this->params['fieldadd'][$_keyadd]['editurl']."/edit/pk/".$_keyadd."ID/valpk/' + $('#".$_keyadd."').val());
											});
										});
						      </script>
									";
							}
							$addButton .= "
							<!-- Modal -->
							<div class='modal fade' id='modal_".$_keyadd."' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
							  <div class='modal-dialog modal-lg' role='document'>
							    <div class='modal-content'>
							      <div class='modal-header'>
							        <h5 class='modal-title' id='exampleModalLabel'>".$this->ci->lang->line($titleType)." ".$caption."</h5>
							        <button type='button' class='close' data-bs-dismiss='modal' aria-label='Close'>
							          <span aria-hidden='true'>&times;</span>
							        </button>
							      </div>
							      <div class='modal-body' id='modal_body_".$_keyadd."'>
							        
							      </div>
							      <div class='modal-footer'>
							        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
							      </div>
							    </div>
							  </div>
							</div>
							";
						}
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<select data-value='".$data[$_keyadd]."' ".$isreadonly." ".$isdisabled."  ".$multiple." ".$name." id='".$_keyadd."' class='form-select' ".@$this->params['fieldadd'][$_keyadd]['is_required'].">
										".$option."
									</select>
									".$addon." ".$addButton." ".$description."
								</div>
								".$validation_message."
							</div>
						</div>";
						// if( $multiple != "" ){
						// 	$this->params['content'] .= "
						// 		<script type='text/javascript'>
						// 		$(function() {
						//         $('#".$_keyadd."').multipleSelect({
						//             width: '100%'
						//         });
						//     });
						// 	</script>";
						// }
					}
					elseif($_type=="dropdownarray"){
						$option="";
						$multiple="";

						$multiplevalues = array();
						$name = "name='".$_keyadd."'";
						if( @$this->params['fieldadd'][$_keyadd]['multiple'] ){
							$multiple = "multiple='multiple'";
							$name = "name='".$_keyadd."[]'";
							// $multiplevalues = json_decode($data[$_keyadd]);
							$multiplevalues = explode(",",$data[$_keyadd]);
						}
						foreach(array_keys($this->params['fieldadd'][$_keyadd]['sourcearray']) as $opdata){
							if($multiple != ""){
								$selected = @in_array($opdata, $multiplevalues) ? "selected" : null;
							} else {
								$selected = ($data[$_keyadd]==$opdata) ? "selected" : null;
							}
							$option.="<option ".$selected." value='".$opdata."'>".$this->params['fieldadd'][$_keyadd]['sourcearray'][$opdata]."</option>";

						}
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<select data-value='".$data[$_keyadd]."' ".$isreadonly." ".$isdisabled."  name='".$_keyadd."' id='".$_keyadd."' class='form-select' ".@$this->params['fieldadd'][$_keyadd]['is_required'].">
										".$option."
									</select>
									".$addon."
									".$description."
								</div>
							</div>
						</div>";
						// if( $multiple != "" ){
						// 	$this->params['content'] .= "
						// 		<script type='text/javascript'>
						// 		$(function() {
						//         $('#".$_keyadd."').multipleSelect({
						//             width: '100%'
						//         });
						//     });
						// 	</script>";
						// }
					}
					elseif($_type=="popup") {
							$name = "name='".$_keyadd."'";
							$url = $this->params['fieldadd'][$_keyadd]['popup_url'];
							$this->params['content'].= "
							<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<input readonly type='text' ".$isdisabled." ".$isreadonly." class='form-control' ".$name." id='".$_keyadd."' value='".$data[$_keyadd]."' />";
							if( $isdisabled == null && $isreadonly == null ){
								$this->params['content'].= "
									<button class='btn btn-light-primary  btn-popup' type='button'  id='btn-popup-".$_keyadd."'> <i class='fa fa-search'></i> </button>
								";
							}
							$this->params['content'].= "</div>".$validation_message."</div></div>";
							$this->params['content'].= "<script type='text/javascript'>
								$(function(){
									$('#btn-popup-".$_keyadd."').click(function( e ){
										e.preventDefault();
										loadinputmodal('".$url."/trigger/".$_keyadd."');
									});
								});
							</script>";
					}
					elseif($_type=="checkgroup"){
						$option="";
						$arrdata=explode(",",$data[$_keyadd]);
						foreach($this->params['fieldadd'][$_keyadd]['sourcequery'] as $opdata){
							$selected=(array_search($opdata['keydt'],$arrdata)) ? "checked" : null ;
							$option.="<label><input type='checkbox' class='form-control' name='CHECK_".$opdata['keydt']."' ".$selected.">
							<span class='lbl' style='margin-left:5px;'> ".$opdata['valuedt']." </span></label>";
						}
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									".$option."
									".$addon."
									".$description."
								</div>
								".$validation_message."
							</div>
						</div>";
					}
					elseif($_type=="checkbox"){
						$name = "name='".$_keyadd."'";
						$this->params['content'].="
						<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<span class='switch switch-sm'>
										<label>
											<input ".($data[$_keyadd] == "1" ? "checked" : null )." type='checkbox' ".$name." id='".$_keyadd."' class='form-control' />
											<span></span>
										</label>
									</span>
									".$addon."
									".$description."
								</div>
								".$validation_message."
							</div>
						</div>";
					}
					elseif($_type=="file"){
						$dir = isset($this->params['fieldadd'][$_keyadd]['dir']) ? $this->params['fieldadd'][$_keyadd]['dir'] : "uploads";
						$download_url = "";
						$img = "<img src='".base_url($dir."/noimage.png")."' class='thumbnail' style='width:100%;' />";
						if($data[$_keyadd] <> ""){
							$img = "<img src='".base_url($dir."/" . $data[$_keyadd])."' class='thumbnail' style='width:100%;' />";
							$download_url = "<a class='btn btn-secondary btn-sm w-100' target='_blank' href='".base_url($dir."/" . $data[$_keyadd])."'><i class='fa fa-download'></i> Download</a>";
						}
						$extension = substr($data[$_keyadd],-3);
						if( $extension == "pdf"){
							$img = '<object data="'.base_url($dir."/" . $data[$_keyadd]).'" type="application/pdf" width="100%" height="100%">
										   <p><b>Gagal Memuat PDF</b>: Browser Ini tidak mendukung penampil PDF. Silahkan Download File Untuk melihatnya : <a target="_blank" href="'.base_url($dir."/" . $data[$_keyadd]).'">Download PDF</a>.</p>
										</object><style>.img-container{height: 300px}</style>';
						}
						if( $extension == "mp4" || $extension == "avi"){
							$img = '<video width="100%" height="400px" controls>
										  <source src="'.base_url($dir."/" . $data[$_keyadd]).'" type="video/'.$extension.'">
											Browser Anda tidak support dengan pemutar video.
										</video>';
						}
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<input class='form-control' ".@$this->params['fieldadd'][$_keyadd]['required']." ".$isdisabled." ".$isreadonly." type='file' id='FILE_".$_keyadd."' name='FILE_".$_keyadd."' value='".$data[$_keyadd]."' />".$addon."
									<input type='hidden' id='".$_keyadd."' name='".$_keyadd."' value='".$data[$_keyadd]."'/>
									".$description."
								</div>
								".$validation_message."
								<div class='img-container' id='IMG_".$_keyadd."'>".$img."</div>
								".$download_url."
							</div>
							</div>
							<script type='text/javascript'>
								$('#FILE_".$_keyadd."').on('change', function(){
									// $('#IMG_".$_keyadd."').remove();
									var reader = new FileReader();
							    reader.onload = function (e) {
							    	if( e.target.result.substring(5,14) == 'video/mp4' || e.target.result.substring(5,14) == 'video/avi' ){
							    		var loaded = '<video width=\"100%\" height=\"400px\" controls><source src=\"' + e.target.result + '\" type=\"' + e.target.result.substring(5,14) + '\">Browser Anda tidak support dengan pemutar video.</video>';
							    		$('#IMG_".$_keyadd."').html( loaded );
							    	} else if(e.target.result.substring(5,20) == 'application/pdf') {
							    		var loaded = '<object data=\"' + e.target.result + '\" type=\"application/pdf\" width=\"100%\" height=\"600px\"><p><b>Gagal Memuat PDF</b>: Browser Ini tidak mendukung penampil PDF.</p></object>';
											$('#IMG_".$_keyadd."').html( loaded );
							    	} else {
						    			$('#IMG_".$_keyadd."').html('<img class=\"thumbnail\" src=\"' + e.target.result + '\" style=\"width:100%;\" />');
							    	}
							    };

							    // read the image file as a data URL.
							    reader.readAsDataURL(this.files[0]);

									ajaxupload('".$_keyadd."', '".$dir."');
									return false;
								});
							</script>
							";
					}
					elseif($_type=="blob"){
						$img = "<img src='".base_url("uploads/noimage.png")."' class='thumbnail' style='width:100%;' />";
						if($data[$_keyadd] <> ""){
							$img = "<img src='data:image/jpeg;base64, ".base64_encode($data[$_keyadd])."' class='thumbnail' style='width:100%;' />";
						}
						/*$extension = substr($data[$_keyadd],-3);
						if( $extension == "pdf"){
							$img = '<object data="data:application/pdf;base64, '.$data[$_keyadd]).'" type="application/pdf" width="100%" height="100%">
										   <p><b>Gagal Memuat PDF</b>: Browser Ini tidak mendukung penampil PDF. Silahkan Download File Untuk melihatnya : <a target="_blank" href="'.base_url("uploads/" . $data[$_keyadd]).'">Download PDF</a>.</p>
										</object><style>.img-container{height: 300px}</style>';
						}
						if( $extension == "mp4" || $extension == "avi"){
							$img = '<video width="100%" height="400px" controls>
										  <source src="'.base_url("uploads/" . $data[$_keyadd]).'" type="video/'.$extension.'">
											Browser Anda tidak support dengan pemutar video.
										</video>';
						}*/
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<input class='form-control' ".@$this->params['fieldadd'][$_keyadd]['required']." type='file' id='FILE_".$_keyadd."' name='FILE_".$_keyadd."' value='' />".$addon."
									<input type='hidden' id='".$_keyadd."' name='".$_keyadd."' value='' />
									".$description."
								</div>
								".$validation_message."
								<div class='img-container' id='IMG_".$_keyadd."'>".$img."</div>
							</div>
							</div>
							<script type='text/javascript'>
								$('#FILE_".$_keyadd."').on('change', function(){
									// $('#IMG_".$_keyadd."').remove();

									var reader = new FileReader();
							    reader.onload = function (e) {

							    	if( e.target.result.substring(5,14) == 'video/mp4' || e.target.result.substring(5,14) == 'video/avi' ){
							    		var loaded = '<video width=\"100%\" height=\"400px\" controls><source src=\"' + e.target.result + '\" type=\"' + e.target.result.substring(5,14) + '\">Browser Anda tidak support dengan pemutar video.</video>';
							    		$('#IMG_".$_keyadd."').html( loaded );
							    	} else if(e.target.result.substring(5,20) == 'application/pdf') {
							    		var loaded = '<object data=\"' + e.target.result + '\" type=\"application/pdf\" width=\"100%\" height=\"600px\"><p><b>Gagal Memuat PDF</b>: Browser Ini tidak mendukung penampil PDF.</p></object>';
											$('#IMG_".$_keyadd."').html( loaded );
							    	} else {
						    			$('#IMG_".$_keyadd."').html('<img class=\"thumbnail\" src=\"' + e.target.result + '\" style=\"width:100%;\" />');
							    	}

							    	var dataURL = reader.result;
							    	$('#".$_keyadd."').val( dataURL );

							    };
							    reader.readAsDataURL(this.files[0]);
							    
									return false;
								});
							</script>
							";
					}
					elseif($_type == "map"){
						$default_position = ($data[$_keyadd] == "") ? $this->configurations['MAP_DEFAULT_LOCATION'] : $data[$_keyadd];
						$latlng = explode(",", $default_position);
						$default_lat = $latlng[0];
						$default_lng = $latlng[1];
						$addressChain = "";
						if( isset($this->params['fieldadd'][$_keyadd]['address_id'] )){
							$id = $this->params['fieldadd'][$_keyadd]['address_id'];
							$addressChain = "
							var input = document.getElementById('".$id."');
							var autocomplete = new google.maps.places.Autocomplete(input);
							autocomplete.bindTo('bounds', map);
							autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);
							
							/*var infowindow = new google.maps.InfoWindow();
			        var infowindowContent = document.getElementById('infowindow-content');
			        infowindow.setContent(infowindowContent);
			        var marker = new google.maps.Marker({
			          map: map,
			          anchorPoint: new google.maps.Point(0, -29)
			        });*/

			        autocomplete.addListener('place_changed', function() {
		          
		          /*infowindow.close();*/

			          marker.setVisible(false);
			          var place = autocomplete.getPlace();
			          if (!place.geometry) {
			            // User entered the name of a Place that was not suggested and
			            // pressed the Enter key, or the Place Details request failed.
			            window.alert('No details available for input: ' + place.name);
			            return;
			          }

		          // If the place has a geometry, then present it on a map.

		          if (place.geometry.viewport) {
		            map.fitBounds(place.geometry.viewport);
		          } else {
		            map.setCenter(place.geometry.location);
		            map.setZoom(17);  // Why 17? Because it looks good.
		          }

		          marker.setPosition(place.geometry.location);
		          marker.setVisible(true);
		          var lat = place.geometry.location.lat();
		          var lng = place.geometry.location.lng();
							$('#".$_keyadd."').val(eval(lat) + ',' + eval(lng));
							$('#".$_keyadd."').change();

		          var address = '';
		          if (place.address_components) {
		            address = [
		              (place.address_components[0] && place.address_components[0].short_name || ''),
		              (place.address_components[1] && place.address_components[1].short_name || ''),
		              (place.address_components[2] && place.address_components[2].short_name || '')
		            ].join(' ');
		          }

		          /*
		          infowindowContent.children['place-icon'].src = place.icon;
		          infowindowContent.children['place-name'].textContent = place.name;
		          infowindowContent.children['place-address'].textContent = address;
		          infowindow.open(map, marker);
		          */
		        });
							";
						}
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<input type='text' class='form-control' id='".$_keyadd."' name='".$_keyadd."' value='".$default_position."' />
									".$addon."
									".$description."
								</div>
								".$validation_message."
								<div class='map-container' id='MAP_".$_keyadd."' style='height:300px;'></div>
							</div>
							</div>
							<script type='text/javascript'>
								var map, infoWindow, marker;
								".$_keyadd."_initMap();
								function ".$_keyadd."_initMap() {
									var location = { lat: ".$default_lat.", lng: ".$default_lng." };
							    var map = new google.maps.Map(document.getElementById('MAP_".$_keyadd."'), {
							      zoom: 18,
							      center: location
							    });

							    ".$addressChain."

							    // Add a marker at the center of the map.
							    var marker = new google.maps.Marker({
							      position: location,
							      title: 'Pilih Lokasi',
							      map: map,
							      draggable: true,
							   		animation: google.maps.Animation.DROP
							    });
							    google.maps.event.addListener(marker, 'dragend', function (event) {
							    	var lat = this.getPosition().lat();
							    	var lng = this.getPosition().lng();
								    $('#".$_keyadd."').val(eval(lat) + ',' + eval(lng));
								    $('#".$_keyadd."').change();
									});

									$('#".$_keyadd."').on('blur', function(){
										var gpsloc = $(this).val().split(',');
										var lat = eval(gpsloc[0]);
										var lng = eval(gpsloc[1]);
										map.setCenter({ lat: lat, lng: lng });
			            	map.setZoom(18);
					          marker.setPosition({ lat: lat, lng: lng });
					          $('#".$_keyadd."').change();
									});
							  }

							</script>";
					}
					elseif($_type == "table"){
						$this->params['content'].="
						<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
							<div class='".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div id='".$_keyadd."' class='table-responsive'>
									
								</div>
								<script type='text/javascript'>
									$(function(){
										$('#".$_keyadd."').load('".$this->params['fieldadd'][$_keyadd]["url"]."', function(response,status,xhr){
											if( xhr.status != 200){
												$('#".$_keyadd."').html(xhr.responseText);
											}
										});
									});
								</script>
								".$validation_message."
							</div>
						</div>";
					}
					else {
						$step = "";
						$dateclass="";
						$append="";
						$value=htmlentities($data[$_keyadd], ENT_QUOTES);
						$js="";
						$min= isset($this->params['fieldadd'][$_keyadd]['min']) ? " min='".$this->params['fieldadd'][$_keyadd]['min']."' " : null;
						$max= isset($this->params['fieldadd'][$_keyadd]['max']) ? " max='".$this->params['fieldadd'][$_keyadd]['max']."' " : null;
						$currencyAdd = "";
						$currencyClass = "";
						if($_type=="date"){
							$_type="text";
							// $value = date("Y-m-d", strtotime($value));
							$dateclass="date-picker";
							$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
							if(count($post) > 0){
								if(@$post[$_keyadd] == ""){
									unset($post[$_keyadd]);
								}
							}
						}
						elseif($_type=="month"){
							$_type="text";
							$dateclass="month-picker";
							$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
							if(count($post) > 0){
								if(@$post[$_keyadd] == ""){
									unset($post[$_keyadd]);
								}
							}
						}
						elseif($_type=="time"){
							$_type="text";
							$dateclass="time-picker";
							$append="<span class='input-group-text'><i class='fas fa-clock'></i></span>
									<script type='text/javascript'>
										$('#".$_keyadd.", #".$_keyadd."_modal').flatpickr({
					            enableTime: true,
									    noCalendar: true,
									    dateFormat: 'H:i',
									</script>
							";
						}
						elseif($_type=="datetime"){
							$_type="text";
							$dateclass="datetime-picker";
							$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
						}
						elseif($_type=="password"){
							$value="";
						}
						elseif($_type=="decimal"){
							$step = "step='0.01'";
							$_type="number";
							if( @$this->params['fieldadd'][$_keyadd]['decimalplace'] == 1){
								$step = "step='0.1'";
							}
							if( @$this->params['fieldadd'][$_keyadd]['decimalplace'] == 3){
								$step = "step='0.001'";
							}
						}
						elseif($_type=="number"){
							$_type = "number";
							$value = round($value,0);
						}
						elseif($_type=="currency"){
							$_type="number";
							$step = "step='0.01'";
							$currencyAdd = " data-number-to-fixed='2' data-number-stepfactor='100'";
							$currencyClass = " currency";
						}
						elseif($_type=="tagsinput"){
							$js = "<script type='text/javascript'>
								$('#".$_keyadd."').tagsinput();
							</script>";
							$_type = "text";
						}
						if( @$this->params['fieldadd'][$_keyadd]['typeahead'] != "" ){
							$js .= "<script type='text/javascript'>
				        var sources = new Bloodhound({
				            datumTokenizer: Bloodhound.tokenizers.whitespace,
				            queryTokenizer: Bloodhound.tokenizers.whitespace,
				            prefetch: '".$this->params['fieldadd'][$_keyadd]['typeahead']."'
				        });

				        $('#".$_keyadd.", #".$_keyadd."_modal').typeahead(null, {
				            name: 'sources',
				            source: sources
				        });
							</script>";
						}
						$this->params['content'].="<div class='form-group row mb-3'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
							<div class='".$dateclass." ".@$this->params['fieldadd'][$_keyadd]['class']."'>
								<div class='input-group'>
									<input ".$min." ".$max." ".$isreadonly." ".$isdisabled." value='".$value."' type='".$_type."' ".$step." name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldadd'][$_keyadd]['placeholder']."' ".@$this->params['fieldadd'][$_keyadd]['is_required']." class='form-control ".$currencyClass."' ".$currencyAdd." maxlength='".@$this->params['fieldadd'][$_keyadd]['maxlength']."' />".$append."
									".$addon."
									".$description."
								</div>
								".$validation_message."
							</div>
						</div>" . $js;
					}
				}
			}
			if( !isset($this->params['showcontrol']) || $this->params['showcontrol'] != false ){
			$this->params['content'].="<div class='form-group row mb-3'>
				<div class='offset-md-2 col-md-6'>
					<button class='btn btn-primary' type='submit'><i class='fa fa-save'></i>".$this->ci->lang->line("submit")."</button>
						<button class='btn btn-danger' type='reset'><i class='fa fa-sync-alt'></i>".$this->ci->lang->line("reset")."</button>
				</div>
			</div>";
			}
			$this->params['content'].="</form>";
		}
		$targetPost = $this->ci->router->fetch_method();
		if( isset($this->params['target_post']) ){
			$targetPost = $this->params['target_post'];
		}
		$this->params['content'].='</div>
		<script type="text/javascript">
			$(document).ready(function(){
				$(this).updatePolyfill();
				$(".ws-number").unbind("mousewheel");
				$(".ws-number").unbind("wheel");
				$(".popover").popover({
						html: true,
						trigger: "hover"
				});
				let parentModal = $(".select2 > .input-group > select").parents("#modalContainer");
				let parentPopup = $(".select2 > .input-group > select").parents("#modalPopupContainer");
				let parentInput = $(".select2 > .input-group > select").parents("#modalInputContainer");

				if( parentModal.length > 0 ) {
					var select2_parent = $("#modalContainer");
				} 
				if( parentPopup.length > 0 ){
					var select2_parent = $("#modalPopupContainer");
				} 
				if( parentInput.length > 0 ){
					var select2_parent = $("#modalInputContainer");
				} 
				if( parentPopup.length == 0 && parentModal.length == 0 && parentInput.length == 0){
					var select2_parent = $(document.body);
				}

				$(".select2 > .input-group > select").select2({
					placeholder: $(this).attr("placeholder"),
					allowClear: Boolean($(this).data("allow-clear")),
					dropdownParent: select2_parent
				});

				$(".date-picker input").flatpickr({
					dateFormat: "Y-m-d",
					// disableMobile: true
					
				});
				$(".month-picker input").flatpickr({
					dateFormat: "Y-m",
				  minViewMode: 1,
				});

				$(".datetime-picker input").flatpickr({
					enableTime: true,
					dateFormat: "Y-m-d H:i",
					time_24hr: true
				});

				$(".time-picker input").flatpickr({
					enableTime: true,
					noCalendar: true,
					dateFormat: "H:i:S",
					enableSeconds: true,
					time_24hr: true
				});

				$("#'.$this->formid.'").submit(function(){
					$("#'.$this->maincontent.'").html("<div class=\"text-center spin-loading\"><img src=\"" + base_url + "assets/img/spin.svg\" /></div>");
  				var datapost=$(this).serialize();
					var target="'.$this->filename.'/'.$targetPost.'/pk/'.@$this->urisegments['pk'].'/valpk/'.@urlencode($this->urisegments['valpk']).'";
					loadpost("'.$this->maincontent.'", target, datapost);
					return false;
				});

				$(".date-picker input, .datetime-picker input, .time-picker input").after("<button type=\"button\" class=\"btn btn-clear btn-flatpicker-clear\"><i class=\"fas fa-times\"></i></button>");
				$(".btn-flatpicker-clear").click(function(){
					// $(this).parents(".input-group").find("input").val("");
					$(this).parents(".input-group").find("input").flatpickr().clear();
				});
			});
		</script>
		';
		// Updating Data POst
		if( count($post) > 0 && $valid == true){
			foreach($this->params['fieldadd'] as $_keyadd => $_params){
				if(@$_params['type'] == "primarykey"){
					$primarykey = $_keyadd;
					unset($post[$_keyadd]);
				}
				if(@$_params['type'] == "number" && @$post[$_keyadd] == ""){
					unset($post[$_keyadd]);
				}
				if(@$_params['type'] == "decimal" && @$post[$_keyadd] == ""){
					unset($post[$_keyadd]);
				}
				if(@$_params['type'] == "separator"){
					unset($post[$_keyadd]);
				}
				// if(@$_params['disabled'] == true){
				// 	unset($post[$_keyadd]);
				// }
				if(@$_params['type'] == "blob"){
					$ex = explode(",", $post[$_keyadd]);
					if( $this->ci->db->platform() == "mssql" || $this->ci->db->platform() == "sqlsrv"){
						$this->ci->db->set($_keyadd, "cast('' as xml).value('xs:base64Binary(\"".$ex[1]."\")', 'varbinary(max)')", FALSE);
					} elseif($this->ci->db->platform() == "mysqli") {
						$this->ci->db->set($_keyadd, "from_base64('".$ex[1]."')", FALSE);
					}
					unset($post[$_keyadd]);
				}
				if( isset($post[$_keyadd]) && is_array($post[$_keyadd]) ){
					$post[$_keyadd] = implode(",", $post[$_keyadd]);
				}
			}
			if( isset($this->params['unset_post']) ){
				$this->params['unset_post'] = explode(",", $this->params['unset_post']);
				foreach( $this->params['unset_post'] as $unset){
					unset($post[$unset]);
				}
			}
			$upd = $this->ci->db->update($this->params['table'], $post, array($primarykey => urldecode($this->urisegments['valpk'])));
			if( $upd == 1 ){
				if( @$this->params['wizard'] == true ){
					return 1;
					exit;
				} else {
					// $_POST = array();
					$this->params['content'] = '<script type="text/javascript">
						loadcontent("'.$this->maincontent.'","'.$this->filename.'/", true);
					</script>';
				}
			}
		}
		$this->mainfunction($this->params);
		if( isset($this->params['dbconn'] ) ){
			$this->ci->load->database("default", FALSE, TRUE);
		}
		return $this->str;
	}

	public function report(){
		$months = $this->ci->config->item("bulan");
		$_primary="";
		foreach(array_keys($this->params['fieldselect']) as $_cols){
			if(@$this->params['fieldselect'][$_cols]['type']=="primarykey"){
				$_primary=$_cols;
				break;
			}
		}
		$command=explode(",",$this->params['command']);
		$_cmdtext="";
		$_cmdtext.=(in_array("browse",$command)) ? "<a title='Refresh' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn btn-light-primary btn-browse'><i class='fa fa-sync-alt'></i> Refresh</a>" : '' ;
		$_cmdtext.=(in_array("search",$command)) ? "<a title='Search' href='javascript:opensearch();' class='btn btn-light-primary btn-search'><i class='fa fa-search'></i> Filter</a>" : '' ;
		$_cmdtext.=(in_array("print",$command)) ? "<a title='Print' id='btn-print' href='javascript:print(\"printarea\");' class='btn btn-light-primary btn-print'><i class='fa fa-print'></i> Print</a>" : '' ;
		$_cmdtext.=(in_array("excel",$command)) ? "<a title='Excel' id='btn-excel' href='javascript:void();' class='btn btn-light-primary btn-excel'><i class='fas fa-file-excel'></i> Excel</a>" : '' ;


		$search="";
		if(isset($this->params['search']) && count($this->params['search'])>0){
			$search.="<div class='searchpanel'><form class='form search-form' id='searchform'><div class='row'>";
			$counter=1;
			foreach(array_keys($this->params['search']) as $_search){
				$addon = "";
				if(isset($this->params['search'][$_search]['help'])){
					$addon = "<span class='input-group-text'>".$this->params['fieldadd'][$_keyadd]['help']."</span>";
				}
				$_type=(!isset($this->params['search'][$_search]['type'])) ? "text" : $this->params['search'][$_search]['type'] ;
				$dateclass="";
				$append="";
				$value="";
				if(isset($_POST[$_search])){
					$value = $_POST[$_search];
				}
				else {
					if(isset($this->params['search'][$_search]['value'])){
						$value = $this->params['search'][$_search]['value'];
					}
				}

				if($_type=="dropdownquery"){
					$option="";
					$selected="";
					$optgroup="";
					foreach($this->params['search'][$_search]['sourcequery'] as $dropdown){
						if(isset($opdata['labeldt'])){
							if($optgroup!=$opdata['labeldt']){
								$option.="<optgroup label='".$opdata['labeldt']."'>";
								$optgroup=$opdata['labeldt'];
							}
						}
						$selected = ($value==$dropdown['keydt']) ? "selected" : null;
						$option.="<option ".$selected." value='".$dropdown['keydt']."'>".$dropdown['valuedt']."</option>";
					}
					$search.="<div class='form-group col-md-4 mb-3'>
						<label for='".$_search."'>".$this->ci->lang->line($_search)."</label>
						<div class='".@$this->params['search'][$_search]['class']."'>
							<div class='input-group'>
								<select name='".$_search."' id='".$_search."' class='form-select'>
									".$option."
								</select>
								".$addon."
							</div>
						</div>
					</div>";
				}
				elseif($_type=="dropdownarray"){
					$option="";
					foreach(array_keys($this->params['search'][$_search]['sourcearray']) as $opdata){
						$selected=($opdata==$value) ? "selected" : null ;
						$option.="<option ".$selected." value='".$opdata."'>".$this->params['search'][$_search]['sourcearray'][$opdata]."</option>";
					}
					$search.="<div class='form-group col-md-4 mb-3'>
						<label for='".$_search."'>".$this->ci->lang->line($_search)."</label>
						<div class='".@$this->params['search'][$_search]['class']."'>
							<div class='input-group'>
								<select name='".$_search."' id='".$_search."' class='form-select'>
									".$option."
								</select>
								".$addon."
							</div>
						</div>
					</div>";
				}
				else {
					// $dateclass = "";
					if($_type=="date"){
						$_type="text";
						$dateclass="date-picker";
						$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
					}
					elseif($_type=="time"){
						$dateclass="time-picker";
						$append="<span class='input-group-text'><i class='fas fa-clock'></i></span>";
					}
					$search.="<div class='form-group col-md-4 mb-3'>
							<label for='".$_search."'>".$this->ci->lang->line($_search)." : </label>
							<div class='".$dateclass." ".@$this->params['search'][$_search]['class']."'>
								<div class='input-group'>
									<input value='".$value."' type='".$_type."' name='".$_search."' id='".$_search."' placeholder='".@$this->params['search'][$_search]['placeholder']."' ".@$this->params['search'][$_search]['is_required']." class='form-control' maxlength='".@$this->params['search'][$_search]['maxlength']."' />".$append."
									".$addon."
								</div>
							</div>
						</div>";
				}

			}
			$search.="<div class='form-group col-md-2'><button type='submit' class='btn btn-light-primary btn-sm'> Cari </button> <button type='button' onclick='loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn btn-danger btn-sm'>".$this->ci->lang->line("reset")."</button></div>";
			$search.='</div></form></div>
			<script type="text/javascript">
				$(function(){
					$(".select2 > .input-group > select").select2({
						width: "100%",
						placeholder: $(this).attr("placeholder"),
						allowClear: Boolean($(this).data("allow-clear")),
					});
					$(".date-picker input").flatpickr({
						dateFormat: "Y-m-d",
						// disableMobile: true
						
					});
					$("#searchform").submit(function(){
						$("#'.$this->maincontent.'").html("<div class=\"text-center spin-loading\"><img src=\"" + base_url + "assets/img/spin.svg\" /></div>");
						var datapost=$(this).serialize();
						loadpost("'.$this->maincontent.'","'.$this->filename.'",datapost);
						return false;
					});
				});
			</script>
			';

		}
		$this->params['content']='
			<div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
          <h3 class="card-title">'.$this->params['name'].'</h3>
          <div class="card-toolbar">
            <div class="example-tools justify-content-center">
            	'.$_cmdtext.'
            </div>
          </div>
        </div>
        <div class="card-body">
		'.$search.'
		<div class="table-responsive">
		<div id="printarea">';

			$query=$this->ci->db->query($this->params['sql'])->result_array();

			if(isset($this->params['view'])){
				$data['params'] = $this->params;
				$data['query'] = $query;
				$this->params['content'].= $this->ci->load->view( $this->params['view'], $data, true);
			} else {
				$logo = (isset($this->params['logo']) ? "<img src='".$this->params['logo']."' style='width:60px' />" : null);
				$this->params['content'].= '
				<table id="table-header" class="table no-border" border="0" cellpadding="5" cellspacing="0">
					<tr>
						<td style="width:100px;">
							'.$logo.'
						</td>
						<td>
							<h4>'.@$this->params["title"].' '.@$this->params["titleaddon"].'</h4>
							<h4 style="margin:1px;padding:1px;line-height:inherit;">'.@$this->params["kop1"].'</h4>
							<h3 style="margin:1px;padding:1px;line-height:inherit;">'.@$this->params["kop2"].'</h3>
							<p style="margin:1px;padding:1px;line-height:inherit;">'.nl2br(@$this->params["kopaddress"]).'</p>
						</td>
					</tr>
				</table>
				<hr style="border:1px solid #666;" />' . @$this->params['prependhtml'];

				$this->params['table'] = '<table id="table-print" class="table-print table table-striped table-bordered"><thead>';
				$this->params['excel'] = '<table id="table-excel" class="d-none"><thead>';

				if(!isset($this->params['head'])){
					$this->params['table'].= '<tr>';
					$this->params['excel'].= '<tr>';
					foreach(array_keys($this->params['fieldselect']) as $keys){

						if($keys=="SEQ"){
							$this->params['table'].='<th class="center sorting_disabled" style="width:20px;">No</th>';
							$this->params['excel'].='<th class="center sorting_disabled" style="width:20px;">No</th>';
						}
						elseif(!@$this->params['fieldselect'][$keys]['hidden']){
							$icon=isset($this->params['fieldselect'][$keys]['icon']) ? '<i class="'.$this->params['fieldselect'][$keys]['icon'].'"></i>' : null ;
							$width=isset($this->params['fieldselect'][$keys]['width']) ? "width:".$this->params['fieldselect'][$keys]['width'].";" : null ;
							$title = isset($this->params['fieldselect'][$keys]['title']) ? $this->params['fieldselect'][$keys]['title'] : $this->ci->lang->line($keys) ;
							$this->params['table'].='<th style="font-size:10pt;'.$width.'" class="'.@$this->params['fieldselect'][$keys]['class'].'">'.$icon." ".$title.'</th>';
							$this->params['excel'].='<th style="font-size:10pt;">'.$title.'</th>';
						}
					}
					$this->params['table'].='</tr>';
					$this->params['excel'].='</tr>';
				} else {
					$this->params['table'].= $this->params['head'];
					$this->params['excel'].= $this->params['head'];
				}

				if( @$this->params['colnumber']==true ){
					$colnumber = 1;
					foreach(array_keys($this->params['fieldselect']) as $keys){
						if(!@$this->params['fieldselect'][$keys]['hidden']){
							$this->params['table'].='<th style="text-align:center;">'.$colnumber.'</th>';
							$this->params['excel'].='<th style="text-align:center;">'.$colnumber.'</th>';
							$colnumber++;
						}
					}
				}

				$this->params['table'].='</thead><tbody>';
				$this->params['excel'].='</thead><tbody>';

				if(count($query) > 0){
					$seq=0;
					$seq_avg=array();
					$sum = array();
					$avg = array();
					foreach($query as $data){
						$seq++;
						$this->params['table'].="<tr>";
						$this->params['excel'].="<tr>";
						foreach(array_keys($this->params['fieldselect']) as $keys){
							if($keys=="SEQ"){
								$this->params['table'].='<td class="center '.@$this->params['fieldselect'][$keys]['class'].'">'.number_format($seq,0).'.</th>';
								$this->params['excel'].='<td class="center '.@$this->params['fieldselect'][$keys]['class'].'">'.number_format($seq,0).'.</th>';
							}
							elseif(!@$this->params['fieldselect'][$keys]['hidden']){
								$_value=@$data[$keys];
								$_excelValue=@$data[$keys];
								switch(@$this->params['fieldselect'][$keys]['type']){
									case "image":
										$_value="<img style='width:100%;' src='".base_url("uploads")."/".@$data[$keys]."'/>";
										break;
									case "date":
										if(@$data[$keys] != ""){
											$_value=date("d",strtotime(@$data[$keys])) . " " . $months[ date("m",strtotime(@$data[$keys])) ] . " " . date("Y",strtotime(@$data[$keys]));
										}
										break;
									case "datetime":
										if($_value != ""){
											$_value=date("d",strtotime(@$data[$keys])) . " " . $months[ date("m",strtotime(@$data[$keys])) ] . " " . date("Y",strtotime(@$data[$keys])) . date("H:i:s", strtotime(@$data[$keys]));
										}
										break;
									case "number":
										$this->params['fieldselect'][$keys]['class'] .= " text-end";
										$_value=number_format(@$data[$keys],0,",",".");
										break;
									case "decimal":
										$this->params['fieldselect'][$keys]['class'] .= " text-end";
										$_value=number_format(@$data[$keys],2,",",".");
										break;
									case "dropdownarray":
										$_value=@$this->params['fieldselect'][$keys]['sourcearray'][$data[$keys]];
										break;
									case "function":
										$model = $this->params['fieldselect'][$keys]['model'];
										$function = $this->params['fieldselect'][$keys]['func'];
										$_value= $this->ci->$model->$function( $_value );
										break;
								}
								$_value = nl2br($_value);

								if(isset($this->params['groupcol'])){
									if(in_array($keys,$this->params['groupcol'])){
										if(@$groupcol[$keys]['data']!=$_value){
											$this->params['table'].='<td class="'.@$this->params['fieldselect'][$keys]['class'].'" width="'.@$this->params['fieldselect'][$keys]['width'].'" align="'.@$this->params['fieldselect'][$keys]['align'].'">'.@$this->params['fieldselect'][$keys]['ltag'].$_value.@$this->params['fieldselect'][$keys]['rtag'].'</td>';
											$this->params['excel'].='<td>'.$_excelValue.'</td>';
											$groupcol[$keys]['data']=$_value;
										}
										else {
											$this->params['table'].='<td class="'.@$this->params['fieldselect'][$keys]['class'].'" width="'.@$this->params['fieldselect'][$keys]['width'].'" align="'.@$this->params['fieldselect'][$keys]['align'].'"></td>';
											$this->params['excel'].='<td></td>';
										}
									}
									else {
										$this->params['table'].='<td class="'.@$this->params['fieldselect'][$keys]['class'].'" width="'.@$this->params['fieldselect'][$keys]['width'].'" align="'.@$this->params['fieldselect'][$keys]['align'].'">'.@$this->params['fieldselect'][$keys]['ltag'].$_value.@$this->params['fieldselect'][$keys]['rtag'].'</td>';
										$this->params['excel'].='<td>'.$_value.'</td>';
									}
								}
								else {
									$this->params['table'].='<td class="'.@$this->params['fieldselect'][$keys]['class'].'" width="'.@$this->params['fieldselect'][$keys]['width'].'" align="'.@$this->params['fieldselect'][$keys]['align'].'">'.@$this->params['fieldselect'][$keys]['ltag'].$_value.@$this->params['fieldselect'][$keys]['rtag'].'</td>';
									$this->params['excel'].='<td>'.$_value.'</td>';
								}

								if(@$this->params['fieldselect'][$keys]['sum'] == true || @$this->params['fieldselect'][$keys]['avg'] == true){
									if(!isset($sum[$keys]['value'])){
										$sum[$keys]['value'] = $data[$keys];
										$sum[$keys]['type'] = isset($this->params['fieldselect'][$keys]['type']) ? $this->params['fieldselect'][$keys]['type'] : "number";
									} else {
										$sum[$keys]['value'] += $data[$keys];
									}
								}
							}

							if(@$this->params['fieldselect'][$keys]['avg'] == true){
								if( isset($seq_avg[$keys]) ){
									if( $data[$keys] != 0 ){
										$seq_avg[$keys]++;
									} 
								} else {
										$seq_avg[$keys] = 1;										
								}
							}

							if(@$this->params['fieldselect'][$keys]['avg']){
								$avg[$keys]['value'] = (($seq_avg[$keys] == 0) ? 0 : $sum[$keys]['value'] / $seq_avg[$keys]);
								// $avg[$keys]['value'] = $seq_avg[$keys];
							}
						}
						$this->params['table'].='</tr>';
						$this->params['excel'].='</tr>';

					}

					// echo "<pre>";print_r($avg);echo "</pre>";

					if( count($sum) > 0 ){
						$this->params['table'].='<tr>';
						$this->params['excel'].='<tr>';
						foreach(array_keys($this->params['fieldselect']) as $keys){
							$finalvalue = array();
							if(!@$this->params['fieldselect'][$keys]['hidden']){
								if(@$this->params['fieldselect'][$keys]['sum'] == true){
									if($sum[$keys]['type'] == "number"){
										$finalvalue[$keys] = number_format($sum[$keys]['value'], 0,",",".");
									} elseif($sum[$keys]['type'] == "decimal"){
										$finalvalue[$keys] = number_format($sum[$keys]['value'], 2,",",".");
									}
									$this->params['table'].='<td class="bolder '.@$this->params['fieldselect'][$keys]['class'].'" width="'.@$this->params['fieldselect'][$keys]['width'].'" align="'.@$this->params['fieldselect'][$keys]['align'].'">'.$finalvalue[$keys].'</td>';
									$this->params['excel'].='<td>'.$finalvalue[$keys].'</td>';
								} elseif(@$this->params['fieldselect'][$keys]['avg'] == true) {
									if($sum[$keys]['type'] == "number"){
										$finalvalue[$keys] = number_format($avg[$keys]['value'], 0,",",".");
									} elseif($sum[$keys]['type'] == "decimal"){
										$finalvalue[$keys] = number_format($avg[$keys]['value'], 2,",",".");
									}
									$this->params['table'].='<td class="bolder '.@$this->params['fieldselect'][$keys]['class'].'" width="'.@$this->params['fieldselect'][$keys]['width'].'" align="'.@$this->params['fieldselect'][$keys]['align'].'">'.$finalvalue[$keys].'</td>';
									$this->params['excel'].='<td>'.$finalvalue[$keys].'</td>';
								} else {
									$this->params['table'].='<td class="bolder '.@$this->params['fieldselect'][$keys]['class'].'" width="'.@$this->params['fieldselect'][$keys]['width'].'" align="'.@$this->params['fieldselect'][$keys]['align'].'"></td>';
									$this->params['excel'].='<td></td>';
								}
							}
						}
						$this->params['table'].='</tr>';
						$this->params['excel'].='</tr>';
					}


				} else {
					$this->params['table'].='<table class="table"><tr><td colspan="'.count($this->params['fieldselect']).'">Tidak ada data</td></tr>';
				}

				$this->params['table'].='</table>';
				$this->params['content'] .= $this->params['table'];


				if( isset($this->params['ttd1jabatan']) || isset($this->params['ttd2jabatan']) || isset($this->params['ttdcity']) ){
					$this->params['content'].='
					<br />
					<table class="table table-borderless text-center">
						<tr>
							<td style="width:50%"></td>
							<td style="width:50%">'.(isset($this->params['ttdcity']) ? $this->params['ttdcity'] . "," : null).' '.@$this->params['ttddate'].'</td>
						</td>
						<tr>
							<td style="width:50%">'.(@$this->params['ttd1jabatan'] != "" ? $this->params['ttd1jabatan'] . "," : null).'<br /><br /><br /><br /></td>
							<td style="width:50%">'.(@$this->params['ttd2jabatan'] != "" ? $this->params['ttd2jabatan'] . "," : null).'<br /><br /><br /><br /></td>
						</tr>
						<tr>
							<td style="width:50%"><strong><u>'.@$this->params['ttd1name'].'</u></strong><br />'.(isset($this->params['ttd1nip']) ? 'NIP. '.@$this->params['ttd1nip'] : "") . '</td>
							<td style="width:50%"><strong><u>'.@$this->params['ttd2name'].'</u></strong><br />'.(isset($this->params['ttd2nip']) ? 'NIP. '.@$this->params['ttd2nip'] : "").'</td>
						</tr>
					</table>';
				}
				$this->params['content'].='</div></div>';
				
				$this->params['content'] .= $this->params['excel'];
			}
			

			if( in_array("excel",$command) ){
				$this->params['content'] .= "<script type='text/javascript'>
					var ExportButtons = document.getElementById( 'table-excel' );
				  var instance = new TableExport(ExportButtons, {
				    formats: ['xlsx'],
				    exportButtons: false,
				    filename: '".$this->params['name']."',
				  });
				  var exportData = instance.getExportData()['table-excel']['xlsx'];
				  var XLSbutton = document.getElementById('btn-excel');
				  XLSbutton.addEventListener('click', function (e) {
				      instance.export2file(exportData.data, exportData.mimeType, exportData.filename, exportData.fileExtension);
				  });
				</script>";
			}
			if( isset($this->params['jsinclude']) ){
				$this->params['content'] .= $this->params['jsinclude'];
			}

			$this->mainfunction($this->params);
			return $this->str;
	}

	public function chart(){
		$command=explode(",",$this->params['command']);
		$_cmdtext="";
		$_cmdtext.=(in_array("browse",$command)) ? "<a title='Refresh' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn btn-light-primary'><i class='icon-white icon icon-refresh'></i></a></li>" : '' ;
		$_cmdtext.=(in_array("print",$command)) ? "<a title='Print' href='javascript:print(\"printarea\");' class='btn btn-light-primary'><i class='icon-white icon icon-print'></i></a></li>" : '' ;
		$_cmdtext.=(in_array("search",$command)) ? "<a title='Search' href='javascript:opensearch();' class='btn btn-light-primary'><i class='icon-white icon icon-search'></i></a></li>" : '' ;

		$search="";
		if(count(@$this->params['search'])>0){
			$search.="<form class='form-horizontal' id='searchform' style='margin-top:10px;'>
				<div class='row'>";
			$counter=1;
			foreach(array_keys($this->params['search']) as $_search){
				$_type=(!isset($this->params['search'][$_search]['type'])) ? "text" : $this->params['search'][$_search]['type'] ;
				$dateclass="";
				$append="";
				$value=@$_POST[$_search];
				if($_type=="dropdownquery"){
					$option="";
					$selected="";
					$optgroup="";
					foreach($this->params['search'][$_search]['sourcequery'] as $dropdown){
						if(isset($opdata['labeldt'])){
							if($optgroup!=$opdata['labeldt']){
								$option.="<optgroup label='".$opdata['labeldt']."'>";
								$optgroup=$opdata['labeldt'];
							}
						}
						$selected = (@$_POST[$_search]==$dropdown['keydt']) ? "selected" : null;
						$option.="<option ".$selected." value='".$dropdown['keydt']."'>".$dropdown['valuedt']."</option>";
					}
					$search.="<div class='form-group col-md-6'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_search."'>".$this->ci->lang->line($_search)."</label>
						<div class='controls'>
							<select name='".$_search."' id='".$_search."' class='form-select ".@$this->params['search'][$_search]['class']."'>
								".$option."
							</select>
							<span class='help-inline'>".@$this->params['search'][$_search]['help']."</span>
						</div>
					</div>";
				}
				elseif($_type=="dropdownarray"){
					$option="";
					foreach(array_keys($this->params['search'][$_search]['sourcearray']) as $opdata){
						$selected=($opdata==@$_POST[$_search]) ? "selected" : null ;
						$option.="<option ".$selected." value='".$opdata."'>".$this->params['search'][$_search]['sourcearray'][$opdata]."</option>";
					}
					$search.="<div class='form-group col-md-6'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_search."'>".$this->ci->lang->line($_search)."</label>
						<div class='controls'>
							<select name='".$_search."' id='".$_search."' class='form-select ".@$this->params['search'][$_search]['class']."'>
								".$option."
							</select>
							<span class='help-inline'>".@$this->params['search'][$_search]['help']."</span>
						</div>
					</div>";
				}
				else {
					if($_type=="date"){
					$_type="text";
					$dateclass="date-picker";
					$append="<span class='add-on'><span class='input-group-text'><i class='fa fa-calendar'></i></span></span>";
					}
					elseif($_type=="time"){
						$dateclass="time-picker";
						$append="<span class='add-on'><span class='input-group-text'><i class='fas fa-clock'></i></span></span>";
					}
					$value = (@$_POST[$_search]!="" ) ? $_POST[$_search] : $value ;
					$search.="<div class='form-group col-md-6'>
							<label class='col-md-2 col-xs-12 col-form-label' for='".$_search."'>".$this->ci->lang->line($_search)."</label>
							<div class='controls'>
								<input value='".$value."' type='".$_type."' name='".$_search."' id='".$_search."' placeholder='".@$this->params['search'][$_search]['placeholder']."' ".@$this->params['search'][$_search]['is_required']." class='input ".$dateclass." ".@$this->params['search'][$_search]['class']."' maxlength='".@$this->params['search'][$_search]['maxlength']."' />".$append."
								<span class='help-inline'>".@$this->params['search'][$_search]['help']."</span>
							</div>
						</div>";
				}
				if($counter==2){
					$search.="</div><div class='row'>";
					$counter=0;
				}
				$counter++;
			}
			$search.="<div class='form-group col-md-2' style='float:right;'><button type='submit' class='btn btn-light-primary all'> Cari </button> <button type='button' onclick='loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn btn-danger all'>".$this->ci->lang->line("reset")."</button></div>";
			$search.='</div></form>
			<script type="text/javascript">
				$(function(){
					$(".select2 > .input-group > select").select2({
						width: "100%",
						placeholder: $(this).attr("placeholder"),
						allowClear: Boolean($(this).data("allow-clear")),
					});
					$(".date-picker").flatpickr({
						dateFormat: "Y-m-d",
						// disableMobile: true
					}).next().on(ace.click_event, function(){
						$(this).prev().focus();
					});
					$("#searchform").submit(function(){
						$("#'.$this->maincontent.'").html("<div class=\'loader\'><img src=\''.base_url().'assets/img/spin.svg\' align=\'center\' /></div> <h3 style=\'text-align:center;color:#799d23;\'> Mencari ...</h3>");
						var datapost=$(this).serialize();
						loadpost("'.$this->maincontent.'","'.$this->filename.'",datapost);
						return false;
					});
				});
			</script>
			';

		}
		$this->params['content']='
		<div class="row">
			<div class="table-header" style="overflow:hidden;height:auto;">
				<div class="col-md-9">Data '.$this->params["name"].'</div>
				<div class="col-md-3">
					<ul class="command">
						'.$_cmdtext.'
					</ul>
				</div>
			</div>
			<div class="searchpanel" style="overflow:hidden;height:auto;">
				'.$search.'
			</div>
			<div id="printarea">
				<h4>'.@$this->params["title"].' '.@$this->params["titleaddon"].'</h3>
				<h4 style="text-align:center;margin:1px;padding:1px;line-height:inherit;">'.@$this->params["kop1"].'</h3><h3 style="text-align:center;margin:1px;padding:1px;line-height:inherit;">'.@$this->params["kop2"].'</h3>
				<h6 style="text-align:center;margin:1px;padding:1px;line-height:inherit;">'.nl2br(@$this->params["kopaddress"]).'</h5>
				<hr style="border:1px solid #666;" />';
				$this->params['content'].='<script type="text/javascript">
					var chart;
					var chartData;
					$(function(){
						$.getJSON("'.$this->params["chart"]["chartdata"].'",function(chartData){
							// SERIAL CHART
							chart = new AmCharts.AmSerialChart();
							chart.dataProvider = chartData;
							chart.categoryField = "'.$this->params["chart"]["x"].'";
							chart.startDuration = 1;

							// AXES
							// category
							var categoryAxis = chart.categoryAxis;
							categoryAxis.labelRotation = '.$this->params['chart']['labelrotation'].';
							categoryAxis.gridPosition = "start";

							// value
							var valueAxis = new AmCharts.ValueAxis();
							valueAxis.title = "'.$this->ci->lang->line($this->params["chart"]["y"]).'";
							valueAxis.dashLength = 5;
							chart.addValueAxis(valueAxis);

							// GRAPH
							var graph = new AmCharts.AmGraph();
							graph.valueField = "'.$this->params["chart"]["y"].'";
							graph.balloonText = "[[category]]: <b>[[value]]</b>";
							graph.type = "'.$this->params['chart']['type'].'";
							graph.lineAlpha = 0;
							graph.fillAlphas = 0.8;
							chart.addGraph(graph);

							// CURSOR
							var chartCursor = new AmCharts.ChartCursor();
							chartCursor.cursorAlpha = 0;
							chartCursor.zoomable = false;
							chartCursor.categoryBalloonEnabled = false;
							chart.addChartCursor(chartCursor);
							chart.creditsPosition = "top-right";
							chart.write("chartdiv");
						});
					});
				</script>
				<style media="printer">
					#chartdiv{
						width : 100%;
						margin-right : 20px;
					}
				</style>
				<div id="chartdiv" style="height: 400px;"></div>
			</div>';
		$this->mainfunction($this->params);
		return $this->str;
	}

	public function header_slave(){
		$header="";
		$query= "SELECT * FROM (".$this->params['sqlmaster'].") as ".$this->params['table'];
		$query.=" WHERE ".$this->params['table'].".".$this->params['primarykeymaster']."='".$this->params['valprimarykeymaster']."'";
		$qry=$this->ci->db->query($query)->result_array();
		$qry=$qry[0];
		foreach(array_keys($this->params['fieldselectedmaster']) as $_keys ){
			if(@$this->params['fieldselectedmaster'][$_keys]['type']=="number"){
				$qry[$_keys]=number_format($qry[$_keys],0,",",".");
			}
			elseif(@$this->params['fieldselectedmaster'][$_keys]['type']=="decimal"){
				$qry[$_keys]=number_format($qry[$_keys],2,",",".");
			}
			elseif(@$this->params['fieldselectedmaster'][$_keys]['type']=="date"){
				$qry[$_keys]=date("d M Y",strtotime($qry[$_keys]));
			}
			elseif(@$this->params['fieldselectedmaster'][$_keys]['type']=="image"){
				$qry[$_keys]="<img src='".base_url("uploads/" . $qry[$_keys])."' />";
			}
			elseif(@$this->params['fieldselectedmaster'][$_keys]['type']=="dropdownarray"){
				$array = $this->params['fieldselectedmaster'][$_keys]['sourcearray'];
				$qry[$_keys]=$array[$qry[$_keys]];
			}
			$header.="<tr>
				<td style='width:150px;'><strong>".@$this->ci->lang->line($_keys)."</strong></td>
				<td style='width:2px;'><strong>:</strong></td>
				<td>".@$this->params['fieldselectedmaster'][$_keys]['ltag'].$qry[$_keys].@$this->params['fieldselectedmaster'][$_keys]['rtag']."</td>
			</tr>";
		}
		return $header;
	}

	public function browse_detail(){
		$command=explode(",",$this->params['command']);
		$_cmdtext="";
		$_cmdtext.=(in_array("browse",$command)) ? "<a title='Refresh' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn btn-light-primary '><i class='fa fa-arrow-left'></i>".$this->ci->lang->line("back")."</a>" : '' ;
		$orderFalse = "";

		$slaveheader=$this->header_slave();
		$this->params['content']='
		<div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
          <h3 class="card-title"><i class="fa fa-th me-2"></i>'.$this->ci->lang->line("master").' '.$this->params['name'].'</h3>
          <div class="card-toolbar">
            <div class="example-tools justify-content-center">
            	'.$_cmdtext.'
            </div>
          </div>
        </div>
        <div class="card-body">
    	<div class="table-responsive">
			<table class="table table-condensed table-border slave-header">'.$slaveheader.'</table>';
		$_primary="";
		$_foreign="";
		foreach(array_keys($this->params['fieldselectslave']) as $_cols){
			if(@$this->params['fieldselectslave'][$_cols]['type']=="primarykey"){
				$_primary=$_cols;
			}
			if(@$this->params['fieldselectslave'][$_cols]['type']=="foreignkey"){
				$_foreign=$_cols;
			}
		}
		$command=explode(",",$this->params['slavecommand']);
		$_cmdtext="";
		$_cmdtext.=(in_array("browse",$command)) ? "<a title='Refresh' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/browse_detail/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."\");' class='btn btn-light-warning  me-2'><i class='fa fa-sync-alt'></i> Segarkan</a>" : '' ;
		$_cmdtext.=(in_array("add",$command)) ? "<a title='Tambah' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/add_detail/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."\");' class='btn btn-warning  me-2'><i class='fa fa-plus'></i> Tambah</a>" : '' ;
		$_cmdtext.=(in_array("print",$command)) ? "<a title='Print' role='button' href='javascript:print(\"printarea\");' class='btn btn-warning  me-2'><i class='fa fa-print'></i> Cetak</a>" : '' ;
		$allCommand = false;
		foreach($command as $cmd){
			if( substr( $cmd,-3 ) == "all" ){
				$allCommand = true;
			}
		}
		if( $allCommand ){
			$_cmdtext .= '<div class="dropdown float-end" id="bulk_slave_'.$this->maincontent.'">
			  <button class="btn btn-light-primary dropdown-toggle" type="button" id="mass_action_'.$this->maincontent.'" data-bs-toggle="dropdown" aria-expanded="false">
			    '.$this->ci->lang->line("mass_action").'
			  </button>
			  <ul class="dropdown-menu mass-action" aria-labelledby="mass_action_'.$this->maincontent.'">';
			// $_cmdbuttom.=(in_array("updateall",$command)) ? "<a role='button' href='javascript:updateall(\"".$this->maincontent."\",\"".$this->filename."/updateall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='btn btn-light-primary'><i class='far fa-edit'></i> ".(isset($this->params['updateall']['caption']) ? $this->params['updateall']['caption'] : "Update Terpilih")."</a>" : '' ;
			// $_cmdbuttom.=(in_array("duplicateall",$command)) ? "<a role='button' href='javascript:updateall(\"".$this->maincontent."\",\"".$this->filename."/duplicateall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='btn btn-light-primary'><i class='far fa-edit'></i> ".(isset($this->params['duplicateall']['caption']) ? $this->params['duplicateall']['caption'] : "Duplicate Terpilih")."</a>" : '' ;
			// $_cmdbuttom.=(in_array("deleteall",$command)) ? "<a role='button' href='javascript:deleteall(\"".$this->maincontent."\",\"".$this->filename."/deleteall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='btn btn-danger btn-light-primary'><i class='fa fa-trash text-white'></i> ".(isset($this->params['deleteall']['caption']) ? $this->params['deleteall']['caption'] : "Hapus Terpilih")."</a>" : '' ;
			// $_cmdbuttom.=(in_array("printall",$command)) ? "<a role='button' href='javascript:printall(\"".$this->maincontent."\",\"".$this->filename."/printall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='btn btn-light-primary'><i class='fa fa-print text-white'></i> Print Terpilih</a>" : '' ;

			$_cmdtext.=(in_array("updateall",$command)) ? "<li><a role='button' href='javascript:updateall(\"".$this->maincontent."\",\"".$this->filename."/updateall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='dropdown-item p-3'><i class='me-3 far fa-edit'></i>".(isset($this->params['updateall']['caption']) ? $this->params['updateall']['caption'] : "Update Terpilih")."</a></li>" : '' ;
			$_cmdtext.=(in_array("duplicateall",$command)) ? "<li><a role='button' href='javascript:updateall(\"".$this->maincontent."\",\"".$this->filename."/duplicateall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='dropdown-item p-3'><i class='me-3 fa fa-copy'></i>".(isset($this->params['duplicateall']['caption']) ? $this->params['duplicateall']['caption'] : "Duplicate Terpilih")."</a></li>" : '' ;
			$_cmdtext.=(in_array("deleteall",$command)) ? "<li><a role='button' href='javascript:deleteall(\"".$this->maincontent."\",\"".$this->filename."/deleteall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='dropdown-item p-3 text-danger'><i class='me-3 fa fa-trash text-danger'></i>".(isset($this->params['deleteall']['caption']) ? $this->params['deleteall']['caption'] : "Hapus Terpilih")."</a></li>" : '' ;
			$_cmdtext.=(in_array("printall",$command)) ? "<li><a role='button' href='javascript:printall(\"".$this->maincontent."\",\"".$this->filename."/printall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='dropdown-item p-3'><i class='me-3 fa fa-print'></i>Print Terpilih</a></li>" : '' ;
			// $_cmdtext.=(in_array("copy",$command)) ? "<li><a role='button' title='".$this->ci->lang->line("copy")."' href='javascript:copy(\"".$this->maincontent."\",\"".$this->filename."/copy/\",$(\".checkbox-data--".$this->maincontent."\"));' class='dropdown-item p-3 btn-copy'><i class='me-3 fa fa-copy text-info'></i>Copy Terpilih</a></li>" : '' ;
			// $_cmdtext.=(in_array("paste",$command)) ? "<li><a role='button' title='".$this->ci->lang->line("paste")."' href='javascript:paste(\"".$this->maincontent."\",\"".$this->filename."/paste/\");' class='dropdown-item p-3 btn-paste'><i class='me-3 fa fa-paste text-info'></i>Paste Terpilih</a></li>" : '' ;
			
			// echo "<pre>";print_r($this->params['cmd']);echo "</pre>";
			foreach($command as $cmd){
				if(substr($cmd,-3) == "all" && 
					$cmd != "updateall" &&
					// $cmd != "duplicateall" &&
					$cmd != "deleteall" &&
					$cmd != "printall"
				){	
					// echo $cmd . " | ";
					$txtcmd = "<li><a role='button' href='".$this->params['cmd_slave'][$cmd]['url']."' class='dropdown-item p-3'><i class='me-3 ".$this->params['cmd_slave'][$cmd]['icon']."'></i>".$this->ci->lang->line($cmd)."</a></li>";
					$_cmdtext .= str_replace("[slaveparams]", "pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/" , $txtcmd);
				}
			}
			$_cmdtext .= "</ul></div>";
		}

		$this->params['content'].='
			<div class="card card-custom gutter-b example example-compact">
        <div class="card-header p-0">
          <h3 class="card-title"><i class="fa fa-list me-2"></i>'.$this->params['slavename'].'</h3>
          <div class="card-toolbar">
            <div class="example-tools justify-content-center">
            	'.$_cmdtext.'
            </div>
          </div>
        </div>
        <div class="card-body p-0">';
			
			$this->params['sqlslave']= "SELECT * FROM (".$this->params['sqlslave'].") as ".$this->params['table-slave']."  WHERE ".$this->params['table-slave'].".".$_foreign."='".$this->params['valprimarykeymaster']."'";
			$query=$this->ci->db->query($this->params['sqlslave'])->result_array();
    	
    	if( isset($this->params['view']) ){
    		$this->data['query'] = $query;
    		$this->params['content'].= $this->ci->load->view($this->params['view'], $this->data, TRUE);
    	} else {
    		$this->params['content'].='<div class="table-responsive" id="printarea">
				<table id="dataTable_slave_'.$this->maincontent.'" class="table align-middle table-row-dashed no-footer table-bordered">';
				$this->params['content'].='<thead><tr>';
				$colIndex = 0;
				foreach(array_keys($this->params['fieldselectslave']) as $keys){
					$title = isset( $this->params['fieldselectslave'][$keys]['title'] ) ? $this->params['fieldselectslave'][$keys]['title'] : $this->ci->lang->line($keys);
					$class = "";
					if(
						@$this->params['fieldselectslave'][$keys]['type'] == "number" || 
						@$this->params['fieldselectslave'][$keys]['type'] == "decimal"
					){
						$class = "text-end";
					} else {
						$class = @$this->params['fieldselectslave'][$keys]['class'];
					}
					if($keys=="SEQ"){
						$orderFalse .= "{ orderable: false, targets: ".$colIndex." },";
						$this->params['content'].='<th class="center sorting_disabled " style="width:20px;">No</th>';
					}
					elseif( $keys == "#" ){
						$orderFalse .= "{ orderable: false, targets: ".$colIndex." }";
						$this->params['content'].='<th class="center sorting_disabled" style="width:20px;">
								<div class="form-check form-check-sm form-check-custom form-check-solid me-3 ms-3"><input type="checkbox" value="" class="group-checkable form-check-input checkbox-header--'.$this->maincontent.'" id="checkbox-header--'.$this->maincontent.'" /></div>
						</th>';
					}
					elseif(@$this->params['fieldselectslave'][$keys]['type']=="checkbox"){
						$this->params['content'].='<th class="center sorting_disabled" style="width:20px;">
							<label>
								<input type="checkbox" class="group-checkable" />
								<span class="lbl"></span>
							</label>
						</th>';
					}
					elseif(!@$this->params['fieldselectslave'][$keys]['hidden']){
						$icon=isset($this->params['fieldselectslave'][$keys]['icon']) ? '<i class="'.$this->params['fieldselectslave'][$keys]['icon'].'"></i>' : null ;
						$this->params['content'].='<th class="'.@$this->params['fieldselectslave'][$keys]['class'].'" style="width:'.@$this->params['fieldselectslave'][$keys]['width'].';">'.$icon.$title.'</th>';
					}
					$colIndex++;
				}
				if((in_array("preview",$command)) || (in_array("edit",$command)) || (in_array("delete",$command)) || (in_array("printreceipt",$command)) || (in_array("detail",$command)) || (in_array("process",$command))){
					$this->params['content'].='<th class="sorting_disabled" style="width: 80px;">Action</th>';
				}
				$this->params['content'].='</tr></thead><tbody>';
				$seq=0;
				foreach($query as $data){
					$seq++;
					$this->params['content'].="<tr>";
					foreach(array_keys($this->params['fieldselectslave']) as $keys){
						if($keys=="SEQ"){
							$this->params['content'].= '<td class="center '.@$this->params['fieldselectslave'][$keys]['class'].'">'.$seq.'.</th>';
						}
						elseif($keys =="#" ){
							$this->params['content'].= '<td class="center"><div class="form-check form-check-sm form-check-custom form-check-solid me-3 ms-3"><input type="checkbox" value="" class="form-check-input checkable checkbox-data--'.$this->maincontent.'" id="check-'.$_primary."-".$data[$_primary].'"></div></td>';
						}
						elseif(@$this->params['fieldselectslave'][$keys]['type']=="checkbox"){
							$this->params['content'].='<td class="center">
								<label>
									<input type="checkbox" />
									<span class="lbl"></span>
								</label>
							</td>';
						}
						elseif(!@$this->params['fieldselectslave'][$keys]['hidden']){
							$_value=@$data[$keys];
							switch(@$this->params['fieldselectslave'][$keys]['type']){
								case "date":
									if( @$data[$keys] != "" ){
										$_value=date("d M Y",strtotime(@$data[$keys]));
									}
									break;
								case "month":
									if( @$data[$keys] != "" ){
										$ex = explode("-",$data[$keys]);
										if( strlen($ex[0]) == "4" ){
											$_value = $months[intval($ex[1])] . " " . $ex[0];
										} else {
											$_value = $months[intval($ex[0])] . " " . $ex[1];
										}
									}
									break;
								case "datetime":
									if($_value != ""){
										$_value=date("d M Y - H:i:s",strtotime(@$data[$keys]));
									}
									break;
								case "number":
									$_value=number_format(@$data[$keys],0,",",".");
									$this->params['fieldselectslave'][$keys]['align'] = isset($this->params['fieldselectslave'][$keys]['align']) ? $this->params['fieldselectslave'][$keys]['align'] : "right";
									break;
								case "decimal":
									$_value=number_format(@$data[$keys],2,",",".");
									$this->params['fieldselectslave'][$keys]['align'] = isset($this->params['fieldselectslave'][$keys]['align']) ? $this->params['fieldselectslave'][$keys]['align'] : "right";
									break;
								case "dropdownarray":
									$_value=@$this->params['fieldselectslave'][$keys]['sourcearray'][$data[$keys]];
									break;
								case "input_dropdownarray":
									$option = "<select name='".$keys."_".$data[$_primary]."' id='".$keys."_".$data[$_primary]."' class='form-select'>";
									foreach($this->params['fieldselectslave'][$keys]['sourcearray'] as $key => $value){
										$selected = ($key == $data[$keys]) ? "selected" : null;
										$option .= "<option ".$selected." value='".$key."'>".$value."</option>";
									}
									$option .= "</select>
									<script type='text/javascript'>
										$('#".$keys."_".$data[$_primary]."').change(function(){
											var target = '".$this->params['fieldselectslave'][$keys]['action']."';
											var datapost = {
												id: $(this).attr('id'),
												value: $(this).val()
											}
											$.post(target, datapost);
										});
									</script>";
									$_value = $option;
									break;
								case "image":
									if(@$data[$keys] == "") $data[$keys] = "noimage.png";
									$_value="<img src='".base_url()."uploads/".@$data[$keys]."' class='thumbnail' style='width:100%;' />";
									break;
								case "download":
									$dir = isset($this->params['fieldselectslave'][$keys]['dir']) ? $this->params['fieldselectslave'][$keys]['dir'] : "uploads" ;
									if(@$data[$keys] != "" && file_exists("./".$dir."/".$data[$keys]) != "") {
										$_value="<a href='".base_url(). $dir . "/".@$data[$keys]."' target='_blank' class='btn w-100 btn-light-primary' title='download'><i class='fa fa-download'></i> Download</a>";
									} else {
										$_value = "<span class='m-badge m-badge--danger m-badge--dot'></span> <span class='m--font-bold m--font-danger'>No File Available</span>";
									}
									break;
							}
							$_value = @$this->params['fieldselectslave'][$keys]['ltag'] . $_value . @$this->params['fieldselectslave'][$keys]['rtag'];
							$this->params['content'].='<td class="'.@$this->params['fieldselectslave'][$keys]['class'].'" width="'.@$this->params['fieldselectslave'][$keys]['width'].'" align="'.@$this->params['fieldselectslave'][$keys]['align'].'">'.$_value.'</td>';
						}
					}
					$_cmddetail="";
					$_cmddetail.=(in_array("preview",$command)) ? "<a title='Preview' class='btn  btn-clean btn-icon btn-icon-md' href='uploads/".$data["FILENAME"]."' target='blank'><i class='fa fa-zoom-in'></i></a>" : '' ;
					$_cmddetail.=(in_array("edit",$command)) ? "<a title='Edit' class='btn  btn-clean btn-icon btn-icon-md' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/edit_detail/pk/".$_primary."/valpk/".urlencode($data[$_primary])."/fk/".$_foreign."/valfk/".urlencode($data[$_foreign])."\");'><i class='far fa-edit text-success'></i></a>" : '' ;
					$_cmddetail.=(in_array("process",$command)) ? "<a title='Proses' class='btn  btn-clean btn-icon btn-icon-md' href='javascript:loadprocess(\"".$this->maincontent."\",\"".$this->filename."/edit_detail/pk/".$_primary."/valpk/".urlencode($data[$_primary])."/fk/".$_foreign."/valfk/".urlencode($data[$_foreign])."\");'><i class='fa fa-gear text-info'></i></a>" : '' ;
					$_cmddetail.=(in_array("delete",$command)) ? "<a title='Hapus' class='btn  btn-clean btn-icon btn-icon-md m-btn--label-danger' href='javascript:deletedata(\"".$this->maincontent."\",\"".$this->filename."/delete_detail/pk/".$_primary."/valpk/".urlencode($data[$_primary])."/fk/".$_foreign."/valfk/".urlencode($data[$_foreign])."\");'><i class='fa fa-trash text-danger'></i></a>" : '' ;
					$_cmddetail.=(in_array("printreceipt",$command)) ? "<a title='Print' class='btn  btn-clean btn-icon btn-icon-md' href='javascript:loadinputmodal(\"".$this->filename."/print_detail/pk/".$_primary."/valpk/".urlencode($data[$_primary])."\",\"print\");'><i class='fa fa-print'></i></a>" : '' ;
					$_cmddetail.=(in_array("instantprint",$command)) ? "<a title='Print' class='btn  btn-clean btn-icon btn-icon-md' href='javascript:loadcontent(\"".$this->filename."/print_detail/pk/".$_primary."/valpk/".urlencode($data[$_primary])."\",\"print\");'><i class='fa fa-print'></i></a>" : '' ;

					if((in_array("preview",$command)) || (in_array("edit",$command)) || (in_array("delete",$command)) || (in_array("print",$command)) || (in_array("detail",$command)) || (in_array("process",$command))){
						$this->params['content'].='
							<td class="td-actions" style="width:100px;">
								<div class="action-buttons">
									<div class="btn-no-group">
									'.$_cmddetail.'
									</div>
								</div>

							</td>';
						}
						$this->params['content'].='</tr>';
				}
				$this->params['content'].='</table></div></div></div>';

		}

		// $_cmdbuttom="";
		// $_cmdbuttom.=(in_array("updateall",$command)) ? "<a role='button' href='javascript:updateall(\"".$this->maincontent."\",\"".$this->filename."/updateall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='btn btn-light-primary'><i class='far fa-edit'></i> ".(isset($this->params['updateall']['caption']) ? $this->params['updateall']['caption'] : "Update Terpilih")."</a>" : '' ;
		// $_cmdbuttom.=(in_array("duplicateall",$command)) ? "<a role='button' href='javascript:updateall(\"".$this->maincontent."\",\"".$this->filename."/duplicateall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='btn btn-light-primary'><i class='far fa-edit'></i> ".(isset($this->params['duplicateall']['caption']) ? $this->params['duplicateall']['caption'] : "Duplicate Terpilih")."</a>" : '' ;
		// $_cmdbuttom.=(in_array("deleteall",$command)) ? "<a role='button' href='javascript:deleteall(\"".$this->maincontent."\",\"".$this->filename."/deleteall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='btn btn-danger btn-light-primary'><i class='fa fa-trash text-white'></i> ".(isset($this->params['deleteall']['caption']) ? $this->params['deleteall']['caption'] : "Hapus Terpilih")."</a>" : '' ;
		// $_cmdbuttom.=(in_array("printall",$command)) ? "<a role='button' href='javascript:printall(\"".$this->maincontent."\",\"".$this->filename."/printall_slave/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."/\",$(\".checkbox-data--".$this->maincontent."\"));' class='btn btn-light-primary'><i class='fa fa-print text-white'></i> Print Terpilih</a>" : '' ;

		// $this->params['content'].= '<div class="btn btn-no-group">'.$_cmdbuttom.'</div>';

		$this->params['content'].='</div><script type="text/javascript">
			$(function(){
				var tbl_slave_'.str_replace("-","_",$this->maincontent).' = $("#dataTable_slave_'.$this->maincontent.'").DataTable( {
					"processing": true,
					"oLanguage": {
  					"sSearch": "",
  					"sSearchPlaceholder": "Cari",
    				"sLengthMenu": "<span>_MENU_</span>",
    				"sProcessing": "<img style=\"width: 50px;\" src=\"'.base_url().'assets/img/spin.svg\" /><br />Memuat Data ...."
					},
					"dom": "lfrtip",
					"autoWidth": false,
					"columnDefs": [
    				'.$orderFalse.'
  				]
				});

				$(".checkbox-data--'.$this->maincontent.'").on("click",function(){
					var checked = 0;
					$(".checkbox-data--'.$this->maincontent.'").each(function( e, item ){
						if( $(item).is(":checked") ){
							checked = 1;
						}
					});
					
					if( checked == 1 ){
						$("#bulk_slave_'.$this->maincontent.'").show();
					} else {
						$("#bulk_slave_'.$this->maincontent.'").hide();
					}
				});

				$("#bulk_slave_'.$this->maincontent.'").hide();

				$("#checkbox-header--'.$this->maincontent.'").on("click",function(){
					if($("#checkbox-header--'.$this->maincontent.'").is(":checked")){
						$(".checkbox-data--'.$this->maincontent.'").prop("checked",true);
						$("#bulk_slave_'.$this->maincontent.'").show();
					}
					else{
						$(".checkbox-data--'.$this->maincontent.'").prop("checked",false);
						$("#bulk_slave_'.$this->maincontent.'").hide();
					}
				});
			});
		</script>
		';
		$this->mainfunction($this->params);
		return $this->str;
	}

	public function add_detail(){
		$post = $this->ci->input->post();
		$command=explode(",",$this->params['command']);
		$_cmdtext="";
		$_cmdtext.=(in_array("browse",$command)) ? "<a title='Refresh' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn btn-light-primary '><i class='fa fa-arrow-left'></i>".$this->ci->lang->line("back")."</a></li>" : '' ;
		$valid = true;
		$slaveheader=$this->header_slave();
		$this->params['content']='
			<div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
          <h3 class="card-title"><i class="fa fa-th me-2"></i>'.$this->ci->lang->line("master").' '.$this->params['name'].'</h3>
          <div class="card-toolbar">
            <div class="example-tools justify-content-center">
            	'.$_cmdtext.'
            </div>
          </div>
        </div>
        <div class="card-body">
				<table class="table table-condensed table-border slave-header">'.$slaveheader.'</table>';
		$_primary="";
		$_foreign="";
		foreach(array_keys($this->params['fieldeditslave']) as $_cols){
			if(@$this->params['fieldeditslave'][$_cols]['type']=="primarykey"){
				$_primary=$_cols;
			}
			if(@$this->params['fieldeditslave'][$_cols]['type']=="foreignkey"){
				$_foreign=$_cols;
			}
		}
		$command=explode(",",$this->params['slavecommand']);
		$_cmdtext="";
		$_cmdtext.=(in_array("browse",$command)) ? "<a title='Refresh' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/browse_detail/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."\");' class='btn btn-light-warning '><i class='fa fa-sync-alt'></i> Segarkan</a>" : '' ;
		//$_cmdtext.=(in_array("add",$command)) ? "<li class='command-list'><a href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/add_detail/\");' class='btn btn-light-primary'><i class='icon-white icon icon-plus'></i></a></li>" : '' ;

		$this->params['content'].='
			<div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
          <h3 class="card-title"><i class="fa fa-plus me-2"></i>'.$this->ci->lang->line("add_detail").' '.$this->params['slavename'].'</h3>
          <div class="card-toolbar">
            <div class="example-tools justify-content-center">
            	'.$_cmdtext.'
            </div>
          </div>
        </div>
        <div class="card-body">
				<form class="form-horizontal" id="'.$this->formid.'">';
		foreach(array_keys($this->params['fieldeditslave']) as $_keyadd){
			$validation_message = "";
			$rsign = "";
			$caption = isset($this->params['fieldeditslave'][$_keyadd]['caption']) ? $this->params['fieldeditslave'][$_keyadd]['caption'] : $this->ci->lang->line($_keyadd);
			// Form Validation
			if( count($post) > 0){
				$this->params['fieldeditslave'][$_keyadd]['value'] = set_value($_keyadd);
			}
			if( isset($this->params['fieldeditslave'][$_keyadd]['validation']) ){
				$this->ci->form_validation->set_rules( $_keyadd, $this->ci->lang->line($_keyadd), $this->params['fieldeditslave'][$_keyadd]['validation'] );
				$arr_validation = explode("|", $this->params['fieldeditslave'][$_keyadd]['validation']);
				if( in_array("required", $arr_validation)) {
					$this->params['fieldeditslave'][$_keyadd]['is_required'] = "required";
				}
				if( !$this->ci->form_validation->run() ){ // Validation Error
					$validation_message = form_error($_keyadd);
					$valid = false;
				}
			}

			if(@$this->params['fieldeditslave'][$_keyadd]['is_required'] == true){
				$rsign = "<span class='requiredsign'>*</span>";
			}

			if(@$this->params['fieldeditslave'][$_keyadd]['hidden']==true){
				$this->params['content'].="<input type='hidden' value='".@$this->params['fieldeditslave'][$_keyadd]['value']."' name='".$_keyadd."' id='".$_keyadd."'/>";
			} else {
				$addon = "";
				if(isset($this->params['fieldeditslave'][$_keyadd]['help'])){
					// $addon = "<span class='input-group-append'>".$this->params['fieldeditslave'][$_keyadd]['help']."</span>";
					$textappend = "";
					$textprepend = "";
					if( substr($this->params['fieldeditslave'][$_keyadd]['help'],0,8) != "<button>"){
						$textprepend = "<span class='input-group-text'>";
						$textappend = "</span>";
					}
					$addon = "".$textprepend.$this->params['fieldeditslave'][$_keyadd]['help'].$textappend."";
				}
				$isreadonly=(@$this->params['fieldeditslave'][$_keyadd]['readonly']==true) ? "readonly" : null;
				$isdisabled=(@$this->params['fieldeditslave'][$_keyadd]['disabled']==true) ? "disabled" : null;
				if( @$this->params['slavedisabled'] == true ){
					$isdisabled = "disabled";
				}
				$_type=(!isset($this->params['fieldeditslave'][$_keyadd]['type'])) ? "text" : $this->params['fieldeditslave'][$_keyadd]['type'] ;
				if($_type=="primarykey"){
					$_type="text";
				}

				if($_type=="textarea"){
					$this->params['content'].="
					<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<textarea ".$isreadonly." ".$isdisabled."  name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldeditslave'][$_keyadd]['placeholder']."' ".@$this->params['fieldeditslave'][$_keyadd]['is_required']." class='form-control' maxlength='".@$this->params['fieldeditslave'][$_keyadd]['maxlength']."'>".@$this->params['fieldeditslave'][$_keyadd]['value']."</textarea>
								".$addon."
							</div>
							".$validation_message."
						</div>
					</div>";
				}
				elseif( $_type == "separator"){
					$this->params['content'].="
						<div class='form-separator text-primary'>".$caption."</div>
					";
				}
				elseif($_type=="wysiwyg"){
					$this->params['content'].="
					<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign . "</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<textarea ".$isreadonly." ".$isdisabled."  name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldeditslave'][$_keyadd]['placeholder']."' ".@$this->params['fieldeditslave'][$_keyadd]['is_required']." class='form-control' maxlength='".@$this->params['fieldeditslave'][$_keyadd]['maxlength']."' novalidate>".@$this->params['fieldeditslave'][$_keyadd]['value']."</textarea>
								".$addon."
							</div>
							".$validation_message."
						</div>
					</div>
					<script type='text/javascript'>
						$(document).ready(function(){
							var editor_id = '".$_keyadd."';
							tinymce.EditorManager.execCommand('mceRemoveEditor',true, editor_id);
							tinymce.EditorManager.execCommand('mceAddEditor',true, editor_id);
						});
					</script>";
				}
				elseif($_type=="dropdownquery"){
					$option="";
					$optgroup="";
					$multiple="";
					foreach($this->params['fieldeditslave'][$_keyadd]['sourcequery'] as $opdata){
						if(isset($opdata['labeldt'])){
							if($optgroup!=$opdata['labeldt']){
								$option.="<optgroup label='".$opdata['labeldt']."'>";
								$optgroup=$opdata['labeldt'];
							}
						}
						$selected=($opdata['keydt']==@$this->params['fieldeditslave'][$_keyadd]['value']) ? "selected" : null ;
						$option.="<option ".$selected." value='".$opdata['keydt']."'>".$opdata['valuedt']."</option>";
					}
					$name = "name='".$_keyadd."'";
					if( @$this->params['fieldeditslave'][$_keyadd]['multiple'] ){
						$multiple = "multiple='multiple'";
						$name = "name='".$_keyadd."[]'";
					}
					$this->params['content'].="
					<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<select ".$isreadonly." ".$isdisabled."  ".$multiple." ".$name." id='".$_keyadd."' class='form-select' ".@$this->params['fieldeditslave'][$_keyadd]['is_required'].">
									".$option."
								</select>
								".$addon."
							</div>
							".$validation_message."
						</div>
					</div>";
					// if( $multiple != "" ){
					// 	$this->params['content'] .= "
					// 		<script type='text/javascript'>
					// 		$(function() {
					//         $('#".$_keyadd."').multipleSelect({
					//             width: '100%'
					//         });
					//     });
					// 	</script>";
					// }

				}
				elseif($_type=="dropdownarray"){
					$option="";
					$multiple="";
					foreach(array_keys($this->params['fieldeditslave'][$_keyadd]['sourcearray']) as $opdata){
						$selected=($opdata==@$this->params['fieldeditslave'][$_keyadd]['value']) ? "selected" : null ;
						$option.="<option ".$selected." value='".$opdata."'>".$this->params['fieldeditslave'][$_keyadd]['sourcearray'][$opdata]."</option>";
					}
					$name = "name='".$_keyadd."'";
					if( @$this->params['fieldeditslave'][$_keyadd]['multiple'] ){
						$multiple = "multiple='multiple'";
						$name = "name='".$_keyadd."[]'";
					}
					$this->params['content'].="
					<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign . "</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<select ".$isreadonly." ".$isdisabled."  ".$multiple." ".$name." id='".$_keyadd."' class='form-select' ".@$this->params['fieldeditslave'][$_keyadd]['is_required'].">
									".$option."
								</select>
								".$addon."
							</div>
							".$validation_message."
						</div>
					</div>";
					// if( $multiple != "" ){
					// 	$this->params['content'] .= "
					// 		<script type='text/javascript'>
					// 		$(function() {
					//         $('#".$_keyadd."').multipleSelect({
					//             width: '100%'
					//         });
					//     });
					// 	</script>";
					// }
				}
				elseif($_type=="checkgroup"){
					$option="";
					foreach($this->params['fieldeditslave'][$_keyadd]['sourcequery'] as $opdata){
						$selected=($opdata['keydt']==@$this->params['fieldeditslave'][$_keyadd]['value']) ? "checked" : null ;
						$option.="<label><input class='form-control' type='checkbox' name='CHECK_".$opdata['keydt']."' ".$selected.">
						<span class='lbl' style='margin-left:5px;'> ".$opdata['valuedt']." </span></label>";
					}
					$this->params['content'].="
					<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								".$option."
								".$addon."
							</div>
							".$validation_message."
						<?div>
					</div>";
				}
				elseif($_type=="file"){
					$dir = isset($this->params['fieldadd'][$_keyadd]['dir']) ? $this->params['fieldadd'][$_keyadd]['dir'] : "uploads";
					$img = "<img src='' class='thumbnail' style='width:100%;' />";
					$this->params['content'].="<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign . "</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<input class='form-control' ".@$this->params['fieldeditslave'][$_keyadd]['required']." type='file' id='FILE_".$_keyadd."' name='FILE_".$_keyadd."' value='' />".$addon."
								<input type='hidden' id='".$_keyadd."' name='".$_keyadd."' value='' />
							</div>
							<div class='img-container' id='IMG_".$_keyadd."'>".$img."</div>
						</div>
						</div>
						<script type='text/javascript'>
							$('#FILE_".$_keyadd."').on('change', function(){
								// $('#IMG_".$_keyadd."').remove();
								var reader = new FileReader();
						    reader.onload = function (e) {
						    	if( e.target.result.substring(5,14) == 'video/mp4' || e.target.result.substring(5,14) == 'video/avi' ){
						    		var loaded = '<video width=\"100%\" height=\"400px\" controls><source src=\"' + e.target.result + '\" type=\"' + e.target.result.substring(5,14) + '\">Browser Anda tidak support dengan pemutar video.</video>';
						    		$('#IMG_".$_keyadd."').html( loaded );
						    	} else if(e.target.result.substring(5,20) == 'application/pdf') {
						    		var loaded = '<object data=\"' + e.target.result + '\" type=\"application/pdf\" width=\"100%\" height=\"600px\"><p><b>Gagal Memuat PDF</b>: Browser Ini tidak mendukung penampil PDF.</p></object>';
										$('#IMG_".$_keyadd."').html( loaded );
						    	} else {
					    			$('#IMG_".$_keyadd."').html('<img class=\"thumbnail\" src=\"' + e.target.result + '\" style=\"width:100%;\" />');
						    	}
						    };

						    // read the image file as a data URL.
						    reader.readAsDataURL(this.files[0]);

								ajaxupload('".$_keyadd."', '".$dir."');
								return false;
							});
						</script>
						";
				}
				elseif($_type == "map"){
					$default_position = $this->configurations['MAP_DEFAULT_LOCATION'];
					$latlng = explode(",", $default_position);
					$default_lat = $latlng[0];
					$default_lng = $latlng[1];
					$addressChain = "";
						if( isset($this->params['fieldadd'][$_keyadd]['address_id'] )){
							$id = $this->params['fieldadd'][$_keyadd]['address_id'];
							$addressChain = "
							var input = document.getElementById('".$id."');
							var autocomplete = new google.maps.places.Autocomplete(input);
							autocomplete.bindTo('bounds', map);
							autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);
							
							/*var infowindow = new google.maps.InfoWindow();
			        var infowindowContent = document.getElementById('infowindow-content');
			        infowindow.setContent(infowindowContent);
			        var marker = new google.maps.Marker({
			          map: map,
			          anchorPoint: new google.maps.Point(0, -29)
			        });*/

			        autocomplete.addListener('place_changed', function() {
		          
		          /*infowindow.close();*/

			          marker.setVisible(false);
			          var place = autocomplete.getPlace();
			          if (!place.geometry) {
			            // User entered the name of a Place that was not suggested and
			            // pressed the Enter key, or the Place Details request failed.
			            window.alert('No details available for input: ' + place.name);
			            return;
			          }

		          // If the place has a geometry, then present it on a map.

		          if (place.geometry.viewport) {
		            map.fitBounds(place.geometry.viewport);
		          } else {
		            map.setCenter(place.geometry.location);
		            map.setZoom(17);  // Why 17? Because it looks good.
		          }

		          marker.setPosition(place.geometry.location);
		          marker.setVisible(true);
		          var lat = place.geometry.location.lat();
		          var lng = place.geometry.location.lng();
							$('#".$_keyadd."').val(eval(lat) + ',' + eval(lng));
							$('#".$_keyadd."').change();

		          var address = '';
		          if (place.address_components) {
		            address = [
		              (place.address_components[0] && place.address_components[0].short_name || ''),
		              (place.address_components[1] && place.address_components[1].short_name || ''),
		              (place.address_components[2] && place.address_components[2].short_name || '')
		            ].join(' ');
		          }

		          /*
		          infowindowContent.children['place-icon'].src = place.icon;
		          infowindowContent.children['place-name'].textContent = place.name;
		          infowindowContent.children['place-address'].textContent = address;
		          infowindow.open(map, marker);
		          */
		        });
							";
						}
					$this->params['content'].="<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<input type='text' value='".$default_lat.",".$default_lng."' class='form-control' id='".$_keyadd."' name='".$_keyadd."' value='' />
							</div>
							".$validation_message."
							<div class='map-container' id='MAP_".$_keyadd."' style='height:300px;'></div>
							<p class='text-primary mt-2 text-center'>Geser Titik Merah untuk Mendapatkan lokasi Koordinat</p>
						</div>
						</div>
						<script type='text/javascript'>
							var map, infoWindow;
							".$_keyadd."_initMap();
							function ".$_keyadd."_initMap() {
								var location = { lat: ".$default_lat.", lng: ".$default_lng." };
						    var map = new google.maps.Map(document.getElementById('MAP_".$_keyadd."'), {
						      zoom: 18,
						      center: location
						    });

						    ".$addressChain."

						    // Add a marker at the center of the map.
						    var marker = new google.maps.Marker({
						      position: location,
						      title: 'Pilih Lokasi',
						      map: map,
						      draggable: true,
						   		animation: google.maps.Animation.DROP
						    });
						    google.maps.event.addListener(marker, 'dragend', function (event) {
						    	var lat = this.getPosition().lat();
						    	var lng = this.getPosition().lng();
							    $('#".$_keyadd."').val(eval(lat) + ',' + eval(lng));
							    $('#".$_keyadd."').change();
								});

								$('#".$_keyadd."').on('blur', function(){
									var gpsloc = $(this).val().split(',');
									var lat = eval(gpsloc[0]);
									var lng = eval(gpsloc[1]);
									map.setCenter({ lat: lat, lng: lng });
		            	map.setZoom(18);
				          marker.setPosition({ lat: lat, lng: lng });
				          $('#".$_keyadd."').change();
								});
						  }
						</script>";
				}
				elseif($_type=="popup") {
						$name = "name='".$_keyadd."'";
						$url = $this->params['fieldeditslave'][$_keyadd]['popup_url'];
						$this->params['content'].= "
						<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<input readonly type='text' ".$isdisabled." ".$isreadonly." class='form-control' ".$name." id='".$_keyadd."' />";
						if( $isdisabled == null && $isreadonly == null ){
							$this->params['content'].= "
								<button class='btn btn-light-primary  btn-popup' type='button'  id='btn-popup-".$_keyadd."'> <i class='fa fa-search'></i> </button>
							";
						}
						$this->params['content'].= "</div></div></div>";
						$this->params['content'].= "<script type='text/javascript'>
							$(function(){
								$('#btn-popup-".$_keyadd."').click(function( e ){
									e.preventDefault();
									loadinputmodal('".$url."/trigger/".$_keyadd."');
								});
							});
						</script>";
				}
				else {
					$step = "";
					$dateclass="";
					$append="";
					$js="";
					$min= isset($this->params['fieldeditslave'][$_keyadd]['min']) ? " min='".$this->params['fieldeditslave'][$_keyadd]['min']."' " : null;
					$max= isset($this->params['fieldeditslave'][$_keyadd]['max']) ? " max='".$this->params['fieldeditslave'][$_keyadd]['max']."' " : null;
					$currencyAdd = "";
					$currencyClass = "";
					if($_type=="date"){
						$_type="text";
						$dateclass="date-picker";
						$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
						if(count($post) > 0){
							if($post[$_keyadd] == ""){
								unset($post[$_keyadd]);
							}
						}
					}
					elseif($_type=="month"){
						$_type="text";
						$dateclass="month-picker";
						$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
						if(count($post) > 0){
							if($post[$_keyadd] == ""){
								unset($post[$_keyadd]);
							}
						}
					}
					elseif($_type=="time"){
						$_type="text";
						$dateclass="time-picker";
						$append="<span class='input-group-text'><i class='fas fa-clock'></i></span>
							<script type='text/javascript'>
								$('#".$_keyadd.", #".$_keyadd."_modal').flatpickr({
				           enableTime: true,
							    noCalendar: true,
							    dateFormat: 'H:i',
				        });
							</script>
						";
					}
					elseif($_type=="datetime"){
						$_type="text";
						$dateclass="datetime-picker";
						$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
					}
					elseif($_type=="decimal"){
						$_type="number";
						$step = "step='0.01'";
					}
					elseif($_type=="currency"){
						$_type="number";
						$step = "step='0.01'";
						$currencyAdd = " data-number-to-fixed='2' data-number-stepfactor='100'";
						$currencyClass = " currency";
					}
					elseif($_type=="tagsinput"){
						$js = "<script type='text/javascript'>
							$('#".$_keyadd."').tagsinput();
						</script>";
						$_type = "text";
					}
					$this->params['content'].="<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign . "</label>
						<div class='".$dateclass." ".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<input ".$currencyAdd." ".$min." ".$max." ".$isreadonly." ".$isdisabled."  value='".@$this->params['fieldeditslave'][$_keyadd]['value']."' type='".$_type."' ".$step." name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldeditslave'][$_keyadd]['placeholder']."' ".@$this->params['fieldeditslave'][$_keyadd]['is_required']." ".$isreadonly." class='form-control ".$currencyClass."' maxlength='".@$this->params['fieldeditslave'][$_keyadd]['maxlength']."' />".$append."
								".$addon."
							</div>
							".$validation_message."
						</div>
					</div>" . $js;
				}
			}
		}
		if( !isset($this->params['slaveshowcontrol']) || $this->params['slaveshowcontrol'] != false ){
			$this->params['content'].="<div class='form-group row mb-3'>
				<div class='offset-md-2 col-md-6'>
					<button class='btn btn-primary' type='submit'><i class='fa fa-save'></i>".$this->ci->lang->line("submit")."</button>
						<button class='btn btn-danger' type='reset'><i class='fa fa-sync-alt'></i>".$this->ci->lang->line("reset")."</button>
				</div>
			</div>";
		}

		$this->params['content'].="</form></div></div>";
		$this->params['content'].='</table>';
		$this->params['content'].='</div></div>
		<script type="text/javascript">
			$(document).ready(function(){
				$(this).updatePolyfill();

				$(".select2 > .input-group > select").select2({
					width: "100%",
					placeholder: $(this).attr("placeholder"),
					allowClear: Boolean($(this).data("allow-clear")),
				});
				$(".date-picker input").flatpickr({
					dateFormat: "Y-m-d",
					// disableMobile: true
					
				});
				$("month-picker input").flatpickr({
					dateFormat: "Y-m",
				});
				$(".datetime-picker input").flatpickr({
					enableTime: true,
					dateFormat: "Y-m-d H:i",
					time_24hr: true
				});

				$(".time-picker input").flatpickr({
					enableTime: true,
					noCalendar: true,
					dateFormat: "H:i:S",
					enableSeconds: true,
					time_24hr: true
				});

				$("#'.$this->formid.'").submit(function(){
					$("#'.$this->maincontent.'").html("<div class=\"text-center spin-loading\"><img src=\"" + base_url + "assets/img/spin.svg\" /></div>");
  				var datapost=$(this).serialize();
					var target="'.$this->filename.'/add_detail/pk/'.$this->params['primarykeymaster'].'/valpk/'.$this->params['valprimarykeymaster'].'/";
					loadpost("'.$this->maincontent.'", target, datapost);
					return false;
				});

				$(".date-picker input, .datetime-picker input, .time-picker input").after("<button type=\"button\" class=\"btn btn-clear btn-flatpicker-clear\"><i class=\"fas fa-times\"></i></button>");
				$(".btn-flatpicker-clear").click(function(){
					// $(this).parents(".input-group").find("input").val("");
					$(this).parents(".input-group").find("input").flatpickr().clear();
				});
			});
		</script>
		';
		// Inserting Data POst
		if( count($post) > 0 && $valid == true){
			foreach($this->params['fieldeditslave'] as $_keyadd => $_params){
				if(@$_params['type'] == "primarykey" && @$_params['hidden'] == true ){
					unset($post[$_keyadd]);
				}
				if(@$_params['type'] == "separator"){
					unset($post[$_keyadd]);
				}
				if(@$_params['disabled'] == true){
					unset($post[$_keyadd]);
				}
			}
			if( isset($this->params['unset_post']) ){
				$this->params['unset_post'] = explode(",", $this->params['unset_post']);
				foreach( $this->params['unset_post'] as $unset){
					unset($post[$unset]);
				}
			}
			if( isset($this->params['dbconn'] ) ){
				$this->ci->load->database($this->params['dbconn'], FALSE, TRUE);
			}
			$ins = $this->ci->db->insert($this->params['table-slave'],$post);
			if( $ins == 1 ){
				$this->params['content'] = '<script type="text/javascript">
					loadcontent("'.$this->maincontent.'","'.$this->filename.'/add_detail/pk/'.$this->params['primarykeymaster'].'/valpk/'.$this->params['valprimarykeymaster'].'/", true);
				</script>';
			}
			if( isset($this->params['dbconn'] ) ){
				$this->ci->load->database("default", FALSE, TRUE);
			}
		}
		$this->mainfunction($this->params);
		return $this->str;
	}

	public function edit_detail(){
		$post = $this->ci->input->post();
		$valid = true;
		$command=explode(",",$this->params['command']);
		$_cmdtext="";
		$_cmdtext.=(in_array("browse",$command)) ? "<a title='Refresh' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."\");' class='btn btn-light-primary '><i class='fa fa-arrow-left'></i>".$this->ci->lang->line("back")."</a>" : '' ;
		$slaveheader=$this->header_slave();
		$this->params['content']='
		<div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
          <h3 class="card-title"><i class="fa fa-th me-2"></i>'.$this->ci->lang->line("master").' </h3>
          <div class="card-toolbar">
            <div class="example-tools justify-content-center">
            	'.$_cmdtext.'
            </div>
          </div>
        </div>
        <div class="card-body">
			<table class="table table-condensed table-border slave-header">'.$slaveheader.'</table>';
		$_primary="";
		$_foreign="";
		foreach(array_keys($this->params['fieldeditslave']) as $_cols){
			if(@$this->params['fieldeditslave'][$_cols]['type']=="primarykey"){
				$_primary=$_cols;
			}
			if(@$this->params['fieldeditslave'][$_cols]['type']=="foreignkey"){
				$_foreign=$_cols;
			}
		}
		$command=explode(",",$this->params['slavecommand']);
		$_cmdtext="";
		$_cmdtext.=(in_array("browse",$command)) ? "<a title='Refresh' role='button' href='javascript:loadcontent(\"".$this->maincontent."\",\"".$this->filename."/browse_detail/pk/".$this->params['primarykeymaster']."/valpk/".$this->params['valprimarykeymaster']."\");' class='btn btn-light-warning '><i class='fa fa-sync-alt'></i> Segarkan</a>" : '' ;
		$keySelect = array();
		foreach(array_keys($this->params['fieldeditslave']) as $_keyadd){
			if( @$this->params['fieldeditslave'][$_keyadd]['type'] != "separator"){
				$keySelect[$_keyadd] = $this->params['fieldeditslave'][$_keyadd];
			}
		}
		$query="SELECT ".implode(", ",array_keys($keySelect))." FROM ".$this->params['table-slave'];
		if( isset($this->params['sqlslaveupdate']) ){
			$query = $this->params['sqlslaveupdate'];
		}
		if(isset($this->urisegments['pk']) && isset($this->urisegments['valpk'])){
			$query.=" WHERE ".$this->urisegments['pk']."='".urldecode($this->urisegments['valpk'])."'";
		}
		$query=$this->ci->db->query($query)->result_array();
		$data=$query[0];
		$this->params['content'].='
		<div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
          <h3 class="card-title"><i class="fa fa-edit me-2"></i>'.$this->ci->lang->line("edit_detail").' '.$this->params['slavename'].'</h3>
          <div class="card-toolbar">
            <div class="example-tools justify-content-center">
            	'.$_cmdtext.'
            </div>
          </div>
        </div>
        <div class="card-body">
    <form class="form-horizontal" id="'.$this->formid.'">';
		foreach(array_keys($this->params['fieldeditslave']) as $_keyadd){
			$addon = "";
			$rsign = "";
			$validation_message = "";
			$caption = isset($this->params['fieldeditslave'][$_keyadd]['caption']) ? $this->params['fieldeditslave'][$_keyadd]['caption'] : $this->ci->lang->line($_keyadd);
			// Form Validation
			if( count($post) > 0){
				$data[$_keyadd] = set_value($_keyadd);
			}
			if( isset($this->params['fieldeditslave'][$_keyadd]['validation']) ){
				$this->ci->form_validation->set_rules( $_keyadd, $this->ci->lang->line($_keyadd), $this->params['fieldeditslave'][$_keyadd]['validation'] );
				$arr_validation = explode("|", $this->params['fieldeditslave'][$_keyadd]['validation']);
				if( in_array("required", $arr_validation)) {
					$this->params['fieldeditslave'][$_keyadd]['is_required'] = "required";
				}
				if( !$this->ci->form_validation->run() ){ // Validation Error
					$validation_message = form_error($_keyadd);
					$valid = false;
				}
			}
			if(isset($this->params['fieldeditslave'][$_keyadd]['help'])){
				// $addon = "<span class='input-group-append'>".$this->params['fieldeditslave'][$_keyadd]['help']."</span>";
				$textappend = "";
				$textprepend = "";
				if( substr($this->params['fieldeditslave'][$_keyadd]['help'],0,8) != "<button>"){
					$textprepend = "<span class='input-group-text'>";
					$textappend = "</span>";
				}
				$addon = "".$textprepend.$this->params['fieldeditslave'][$_keyadd]['help'].$textappend."";
			}
			if(@$this->params['fieldeditslave'][$_keyadd]['is_required'] == true){
				$rsign = "<span class='requiredsign'>*</span>";
			}
			$isreadonly=(@$this->params['fieldeditslave'][$_keyadd]['readonly']==true) ? "readonly" : null;
			$isdisabled=(@$this->params['fieldeditslave'][$_keyadd]['disabled']==true) ? "disabled" : null;

			if( @$this->params['slavedisabled'] == true ){
				$isdisabled = "disabled";
			}

			if(@$this->params['fieldeditslave'][$_keyadd]['hidden']==true){
				$this->params['content'].="<input type='hidden' name='".$_keyadd."' id='".$_keyadd."' value='".$data[$_keyadd]."' />";
			}
			else {
				$_type=(!isset($this->params['fieldeditslave'][$_keyadd]['type'])) ? "text" : $this->params['fieldeditslave'][$_keyadd]['type'] ;
				$isreadonly=($_type=="primarykey") ? "readonly" : $isreadonly;
				if($_type=="primarykey"){
					$_type="text";
				}
				if($_type=="dropdown"){

				}
				elseif( $_type == "separator"){
					$this->params['content'].="
						<div class='form-separator text-primary'>".$this->ci->lang->line($_keyadd)."</div>
					";
				}
				elseif($_type=="textarea"){
					$this->params['content'].="<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<textarea ".$isreadonly." ".$isdisabled."  name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldeditslave'][$_keyadd]['placeholder']."' ".@$this->params['fieldeditslave'][$_keyadd]['is_required']." class='form-control' maxlength='".@$this->params['fieldeditslave'][$_keyadd]['maxlength']."'>".htmlentities($data[$_keyadd], ENT_QUOTES)."</textarea>
								".$addon."
							</div>
						</div>
					</div>";
				}
				elseif($_type=="wysiwyg"){
					$this->params['content'].="<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign."</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<textarea ".$isreadonly." ".$isdisabled."  name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldeditslave'][$_keyadd]['placeholder']."' ".@$this->params['fieldeditslave'][$_keyadd]['is_required']." class='form-control' maxlength='".@$this->params['fieldeditslave'][$_keyadd]['maxlength']."' novalidate>".htmlentities($data[$_keyadd], ENT_QUOTES)."</textarea>
								".$addon."
							</div>
						</div>
					</div>
					<script type='text/javascript'>
						$(document).ready(function(){
							var editor_id = '".$_keyadd."';
							tinymce.EditorManager.execCommand('mceRemoveEditor',true, editor_id);
							tinymce.EditorManager.execCommand('mceAddEditor',true, editor_id);
						});
					</script>
					";
				}
				elseif($_type=="dropdownquery"){
					$option="";
					$selected="";
					$optgroup="";
					$multiple="";
					
					$multiplevalues = array();
					$name = "name='".$_keyadd."'";
					if( @$this->params['fieldeditslave'][$_keyadd]['multiple'] ){
						$multiple = "multiple='multiple'";
						$name = "name='".$_keyadd."[]'";
						// $multiplevalues = json_decode($data[$_keyadd]);
						$multiplevalues = explode(",",$data[$_keyadd]);
					}
					foreach($this->params['fieldeditslave'][$_keyadd]['sourcequery'] as $opdata){
						
						if(isset($opdata['labeldt'])){
							if($optgroup!=$opdata['labeldt']){
								$option.="<optgroup label='".$opdata['labeldt']."'>";
								$optgroup=$opdata['labeldt'];
							}
						}
						if($multiple != ""){
							$selected = @in_array($opdata['keydt'], $multiplevalues) ? "selected" : null;
						} else {
							$selected = ($data[$_keyadd]==$opdata['keydt']) ? "selected" : null;
						}
						$option.="<option ".$selected." value='".$opdata['keydt']."'>".$opdata['valuedt']."</option>";
					}

					$this->params['content'].="<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<select  data-value='".$data[$_keyadd]."' ".$isreadonly." ".$isdisabled."  name='".$_keyadd."' id='".$_keyadd."' class='form-select' ".@$this->params['fieldeditslave'][$_keyadd]['is_required'].">
									".$option."
								</select>
								".$addon."
							</div>
						</div>
					</div>";
					// if( $multiple != "" ){
					// 	$this->params['content'] .= "
					// 		<script type='text/javascript'>
					// 		$(function() {
					//         $('#".$_keyadd."').multipleSelect({
					//             width: '100%'
					//         });
					//     });
					// 	</script>";
					// }
				}
				elseif($_type=="dropdownarray"){
					$option="";
					$multiple="";
					$multiplevalues = array();
					$name = "name='".$_keyadd."'";
					if( @$this->params['fieldeditslave'][$_keyadd]['multiple'] ){
						$multiple = "multiple='multiple'";
						$name = "name='".$_keyadd."[]'";
						// $multiplevalues = json_decode($data[$_keyadd]);
						$multiplevalues = explode(",",$data[$_keyadd]);
					}
					foreach(array_keys($this->params['fieldeditslave'][$_keyadd]['sourcearray']) as $opdata){
						if($multiple != ""){
							$selected = @in_array($opdata, $multiplevalues) ? "selected" : null;
						} else {
							$selected = ($data[$_keyadd]==$opdata) ? "selected" : null;
						}
						$option.="<option ".$selected." value='".$opdata."'>".$this->params['fieldeditslave'][$_keyadd]['sourcearray'][$opdata]."</option>";
					}
					$this->params['content'].="<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<select data-value='".$data[$_keyadd]."' ".$isreadonly." ".$isdisabled."  ".$multiple." ".$name." id='".$_keyadd."' class='form-select' ".@$this->params['fieldeditslave'][$_keyadd]['is_required'].">
									".$option."
								</select>
								".$addon."
							</div>
						</div>
					</div>";
					if( $multiple != "" ){
						$this->params['content'] .= "
							<script type='text/javascript'>
							$(function() {
					        $('#".$_keyadd."').multipleSelect({
					            width: '100%'
					        });
					    });
						</script>";
					}
				}
				elseif($_type=="checkgroup"){
					$option="";
					$arrdata=explode(",",$data[$_keyadd]);
					foreach($this->params['fieldeditslave'][$_keyadd]['sourcequery'] as $opdata){
						$selected=(array_search($opdata['keydt'],$arrdata)) ? "checked" : null ;
						$option.="<label><input type='checkbox' class='form-control' name='CHECK_".$opdata['keydt']."' ".$selected.">
						<span class='lbl' style='margin-left:5px;'> ".$opdata['valuedt']." </span></label>";
					}
					$this->params['content'].="<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								".$option."
								".$addon."
							</div>
						</div>
					</div>";
				}
				elseif($_type=="file"){
					$dir = isset($this->params['fieldadd'][$_keyadd]['dir']) ? $this->params['fieldadd'][$_keyadd]['dir'] : "uploads";
					$img = "<img src='".base_url("uploads/noimage.png")."' class='thumbnail' style='width:100%;' />";
					if($data[$_keyadd] <> ""){
						$img = "<img src='".base_url("uploads/" . $data[$_keyadd])."' class='thumbnail' style='width:100%;' />";
					}
					$extension = substr($data[$_keyadd],-3);
					if( $extension == "pdf"){
						$img = '<object data="'.base_url("uploads/" . $data[$_keyadd]).'" type="application/pdf" width="100%" height="100%">
									   <p><b>Gagal Memuat PDF</b>: Browser Ini tidak mendukung penampil PDF. Silahkan Download File Untuk melihatnya : <a target="_blank" href="'.base_url("uploads/" . $data[$_keyadd]).'">Download PDF</a>.</p>
									</object><style>.img-container{height: 300px}</style>';
					}
					if( $extension == "mp4" || $extension == "avi"){
						$img = '<video width="100%" height="400px" controls>
									  <source src="'.base_url("uploads/" . $data[$_keyadd]).'" type="video/'.$extension.'">
										Browser Anda tidak support dengan pemutar video.
									</video>';
					}
					$this->params['content'].="<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<input class='form-control' ".@$this->params['fieldeditslave'][$_keyadd]['required']." type='file' id='FILE_".$_keyadd."' name='FILE_".$_keyadd."' value='".$data[$_keyadd]."' />".$addon."
								<input type='hidden' id='".$_keyadd."' name='".$_keyadd."' value='".$data[$_keyadd]."' />
							</div>
							<div class='img-container' id='IMG_".$_keyadd."'>".$img."</div>
						</div>
						</div>
						<script type='text/javascript'>
							$('#FILE_".$_keyadd."').on('change', function(){
								// $('#IMG_".$_keyadd."').remove();
								var reader = new FileReader();
						    reader.onload = function (e) {
						    	if( e.target.result.substring(5,14) == 'video/mp4' || e.target.result.substring(5,14) == 'video/avi' ){
						    		var loaded = '<video width=\"100%\" height=\"400px\" controls><source src=\"' + e.target.result + '\" type=\"' + e.target.result.substring(5,14) + '\">Browser Anda tidak support dengan pemutar video.</video>';
						    		$('#IMG_".$_keyadd."').html( loaded );
						    	} else if(e.target.result.substring(5,20) == 'application/pdf') {
						    		var loaded = '<object data=\"' + e.target.result + '\" type=\"application/pdf\" width=\"100%\" height=\"600px\"><p><b>Gagal Memuat PDF</b>: Browser Ini tidak mendukung penampil PDF.</p></object>';
										$('#IMG_".$_keyadd."').html( loaded );
						    	} else {
					    			$('#IMG_".$_keyadd."').html('<img class=\"thumbnail\" src=\"' + e.target.result + '\" style=\"width:100%;\" />');
						    	}
						    };

						    // read the image file as a data URL.
						    reader.readAsDataURL(this.files[0]);

								ajaxupload('".$_keyadd."', '".$dir."');
								return false;
							});
						</script>
						";
				}
				elseif($_type == "map"){
					$default_position = ($data[$_keyadd] == "") ? $this->configurations['MAP_DEFAULT_LOCATION'] : $data[$_keyadd];
					$latlng = explode(",", $default_position);
					$default_lat = $latlng[0];
					$default_lng = $latlng[1];
					$addressChain = "";
						if( isset($this->params['fieldadd'][$_keyadd]['address_id'] )){
							$id = $this->params['fieldadd'][$_keyadd]['address_id'];
							$addressChain = "
							var input = document.getElementById('".$id."');
							var autocomplete = new google.maps.places.Autocomplete(input);
							autocomplete.bindTo('bounds', map);
							autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);
							
							/*var infowindow = new google.maps.InfoWindow();
			        var infowindowContent = document.getElementById('infowindow-content');
			        infowindow.setContent(infowindowContent);
			        var marker = new google.maps.Marker({
			          map: map,
			          anchorPoint: new google.maps.Point(0, -29)
			        });*/

			        autocomplete.addListener('place_changed', function() {
		          
		          /*infowindow.close();*/

			          marker.setVisible(false);
			          var place = autocomplete.getPlace();
			          if (!place.geometry) {
			            // User entered the name of a Place that was not suggested and
			            // pressed the Enter key, or the Place Details request failed.
			            window.alert('No details available for input: ' + place.name);
			            return;
			          }

		          // If the place has a geometry, then present it on a map.

		          if (place.geometry.viewport) {
		            map.fitBounds(place.geometry.viewport);
		          } else {
		            map.setCenter(place.geometry.location);
		            map.setZoom(17);  // Why 17? Because it looks good.
		          }

		          marker.setPosition(place.geometry.location);
		          marker.setVisible(true);
		          var lat = place.geometry.location.lat();
		          var lng = place.geometry.location.lng();
							$('#".$_keyadd."').val(eval(lat) + ',' + eval(lng));
							$('#".$_keyadd."').change();

		          var address = '';
		          if (place.address_components) {
		            address = [
		              (place.address_components[0] && place.address_components[0].short_name || ''),
		              (place.address_components[1] && place.address_components[1].short_name || ''),
		              (place.address_components[2] && place.address_components[2].short_name || '')
		            ].join(' ');
		          }

		          /*
		          infowindowContent.children['place-icon'].src = place.icon;
		          infowindowContent.children['place-name'].textContent = place.name;
		          infowindowContent.children['place-address'].textContent = address;
		          infowindow.open(map, marker);
		          */
		        });
							";
						}
					$this->params['content'].="<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption. $rsign . "</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<input type='text' class='form-control' id='".$_keyadd."' name='".$_keyadd."' value='".$default_position."' />
							</div>
							".$validation_message."
							<div class='map-container' id='MAP_".$_keyadd."' style='height:300px;'></div>
						</div>
						</div>
						<script type='text/javascript'>
							var map, infoWindow;
							".$_keyadd."_initMap();
							function ".$_keyadd."_initMap() {
								var location = { lat: ".$default_lat.", lng: ".$default_lng." };
						    var map = new google.maps.Map(document.getElementById('MAP_".$_keyadd."'), {
						      zoom: 18,
						      center: location
						    });

						    ".$addressChain."

						    // Add a marker at the center of the map.
						    var marker = new google.maps.Marker({
						      position: location,
						      title: 'Pilih Lokasi',
						      map: map,
						      draggable: true,
						   		animation: google.maps.Animation.DROP
						    });
						    google.maps.event.addListener(marker, 'dragend', function (event) {
						    	var lat = this.getPosition().lat();
						    	var lng = this.getPosition().lng();
							    $('#".$_keyadd."').val(eval(lat) + ',' + eval(lng));
							    $('#".$_keyadd."').change();
								});

								$('#".$_keyadd."').on('blur', function(){
									var gpsloc = $(this).val().split(',');
									var lat = eval(gpsloc[0]);
									var lng = eval(gpsloc[1]);
									map.setCenter({ lat: lat, lng: lng });
		            	map.setZoom(18);
				          marker.setPosition({ lat: lat, lng: lng });
				          $('#".$_keyadd."').change();
								});
						  }
						</script>";
				}
				elseif($_type=="popup") {
						$name = "name='".$_keyadd."'";
						$url = $this->params['fieldeditslave'][$_keyadd]['popup_url'];
						$this->params['content'].= "
						<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
						<div class='".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<input readonly type='text' ".$isdisabled." ".$isreadonly." class='form-control' ".$name." id='".$_keyadd."' value='".$data[$_keyadd]."' />";
						if( $isdisabled == null && $isreadonly == null ){
							$this->params['content'].= "
								<button class='btn btn-light-primary  btn-popup' type='button'  id='btn-popup-".$_keyadd."'> <i class='fa fa-search'></i> </button>
							";
						}
						$this->params['content'].= "</div></div></div>";
						$this->params['content'].= "<script type='text/javascript'>
							$(function(){
								$('#btn-popup-".$_keyadd."').click(function( e ){
									e.preventDefault();
									loadinputmodal('".$url."/trigger/".$_keyadd."');
								});
							});
						</script>";
				}
				else {
					$step = "";
					$dateclass="";
					$append="";
					$js="";
					$min= isset($this->params['fieldeditslave'][$_keyadd]['min']) ? " min='".$this->params['fieldeditslave'][$_keyadd]['min']."' " : null;
					$max= isset($this->params['fieldeditslave'][$_keyadd]['max']) ? " max='".$this->params['fieldeditslave'][$_keyadd]['max']."' " : null;
					$currencyAdd = "";
					$currencyClass = "";
					$value=$data[$_keyadd];
					if($_type=="date"){
						$_type="text";
						$dateclass="date-picker";
						$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
						if(count($post) > 0){
							if($post[$_keyadd] == ""){
								unset($post[$_keyadd]);
							}
						}
					}
					elseif($_type=="month"){
						$_type="text";
						$dateclass="month-picker";
						$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
						if(count($post) > 0){
							if($post[$_keyadd] == ""){
								unset($post[$_keyadd]);
							}
						}
					}
					elseif($_type=="time"){
						$_type="text";
						$dateclass="time-picker";
						$append="<span class='input-group-text'><i class='fas fa-clock'></i></span>
						<script type='text/javascript'>
							$('#".$_keyadd.", #".$_keyadd."_modal').flatpickr({
		            enableTime: true,
						    noCalendar: true,
						    dateFormat: 'H:i',
			        });
						</script>
						";
					}
					elseif($_type=="datetime"){
						$_type="text";
						$dateclass="datetime-picker";
						$append="<span class='input-group-text'><i class='fa fa-calendar'></i></span>";
					}
					elseif($_type=="password"){
						$value="";
					}
					elseif($_type=="decimal"){
						$_type="number";
						$step = "step='0.01'";
					}
					elseif($_type=="currency"){
						$_type="number";
						$step = "step='0.01'";
						$currencyAdd = " data-number-to-fixed='2' data-number-stepfactor='100'";
						$currencyClass = " currency";
					}
					elseif($_type=="tagsinput"){
						$js = "<script type='text/javascript'>
							$('#".$_keyadd."').tagsinput();
						</script>";
						$_type = "text";
					}
					$this->params['content'].="<div class='form-group row mb-3'>
						<label class='col-md-2 col-xs-12 col-form-label' for='".$_keyadd."'>".$caption . $rsign ."</label>
						<div class='".$dateclass." ".@$this->params['fieldeditslave'][$_keyadd]['class']."'>
							<div class='input-group'>
								<input ".$currencyAdd." ".$min." ".$max." ".$isreadonly." ".$isdisabled." value='".$value."' type='".$_type."' ".$step." name='".$_keyadd."' id='".$_keyadd."' placeholder='".@$this->params['fieldeditslave'][$_keyadd]['placeholder']."' ".@$this->params['fieldeditslave'][$_keyadd]['is_required']." class='form-control ".$currencyClass."' maxlength='".@$this->params['fieldeditslave'][$_keyadd]['maxlength']."' />".$append."
								".$addon."
							</div>
							".$validation_message."
						</div>
					</div>" . $js;
				}
			}
		}
		if( !isset($this->params['slaveshowcontrol']) || $this->params['slaveshowcontrol'] != false ){
			$this->params['content'].="<div class='form-group row mb-3'>
				<div class='offset-md-2 col-md-6'>
					<button class='btn btn-primary' type='submit'><i class='fa fa-save'></i>".$this->ci->lang->line("submit")."</button>
						<button class='btn btn-danger' type='reset'><i class='fa fa-sync-alt'></i>".$this->ci->lang->line("reset")."</button>
				</div>
			</div>";
		}
		$this->params['content'].="</form></div></div>";
		$this->params['content'].='</table>';
		$this->params['content'].='</div></div>
		<script type="text/javascript">
			$(document).ready(function(){
				$(this).updatePolyfill();

				$(".select2 > .input-group > select").select2({
					width: "100%",
					placeholder: $(this).attr("placeholder"),
					allowClear: Boolean($(this).data("allow-clear")),
				});
				$(".date-picker input").flatpickr({
					dateFormat: "Y-m-d",
					// disableMobile: true
					
				});
				$(".month-picker input").flatpickr({
					dateFormat: "Y-m",
				  minViewMode: 1,
				});
				
				$(".datetime-picker input").flatpickr({
					enableTime: true,
					dateFormat: "Y-m-d H:i",
					time_24hr: true
				});

				$(".time-picker input").flatpickr({
					enableTime: true,
					noCalendar: true,
					dateFormat: "H:i:S",
					enableSeconds: true,
					time_24hr: true
				});

				$("#'.$this->formid.'").submit(function(){
					$("#'.$this->maincontent.'").html("<div class=\"text-center spin-loading\"><img src=\"" + base_url + "assets/img/spin.svg\" /></div>");
					var datapost=$(this).serialize();
					var target="'.$this->filename.'/edit_detail/pk/'.@$this->urisegments['pk'].'/valpk/'.@urlencode($this->urisegments['valpk']).'/fk/'.@$this->urisegments['fk'].'/valfk/'.@urlencode($this->urisegments['valfk']).'";
					loadpost("'.$this->maincontent.'", target, datapost);
					return false;
				});

				$(".date-picker input, .datetime-picker input, .time-picker input").after("<button type=\"button\" class=\"btn btn-clear btn-flatpicker-clear\"><i class=\"fas fa-times\"></i></button>");
				$(".btn-flatpicker-clear").click(function(){
					// $(this).parents(".input-group").find("input").val("");
					$(this).parents(".input-group").find("input").flatpickr().clear();
				});

			});
		</script>
		';
		// Updating Data POst
		if( count($post) > 0 && $valid == true){
			foreach($this->params['fieldeditslave'] as $_keyadd => $_params){
				if(@$_params['type'] == "primarykey" && @$_params['hidden'] == true ){
					unset($post[$_keyadd]);
				}
				
				if(@$_params['type'] == "separator"){
					unset($post[$_keyadd]);
				}
				if(@$_params['disabled'] == true){
					unset($post[$_keyadd]);
				}
			}
			if( isset($this->params['unset_post']) ){
				$this->params['unset_post'] = explode(",", $this->params['unset_post']);
				foreach( $this->params['unset_post'] as $unset){
					unset($post[$unset]);
				}
			}
			if( isset($this->params['dbconn'] ) ){
				$this->ci->load->database($this->params['dbconn'], FALSE, TRUE);
			}
			$upd=$this->ci->db->update($this->params['table-slave'],$post,array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
			if( isset($this->params['dbconn'] ) ){
				$this->ci->load->database("default", FALSE, TRUE);
			}
			if( !isset($this->params['primarykeymaster']) ){
				$this->params['primarykeymaster'] = $this->urisegments['fk'] . "ID";
			}
			if( $upd == 1 ){
				$this->params['content'] = '<script type="text/javascript">
					loadcontent("'.$this->maincontent.'","'.$this->filename.'/browse_detail/pk/'.@$this->params['primarykeymaster'].'/valpk/'.@urlencode($this->urisegments['valfk']).'/", true);
				</script>';
			}
		}
		$this->mainfunction($this->params);
		return $this->str;
	}
}
