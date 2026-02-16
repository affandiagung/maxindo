<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stockopname extends CI_Controller{
	
	private $params = array();
	
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->getparams();
	}
	
	function index(){
		$this->browse();
	}
	
	function getparams(){
		$this->params['command'] = "browse,add,edit,delete,deleteall,detail,search,process";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['table'] = "stockopnames";
		$this->params['sql'] = "SELECT STOCKOPNAMEID,
		stockopnames.NAME, STARTDATE, ENDDATE, 
		STOCKOPNAMESTATUS,
		A.NAME as STOCKOPNAMEBY,
		B.NAME as VALIDATEBY
		FROM stockopnames
		LEFT JOIN employees A ON STOCKOPNAMEBY=A.EMPLOYEEID 
		LEFT JOIN employees B ON VALIDATEBY=B.EMPLOYEEID 
		";
		$this->params['order'] = "STOCKOPNAMEID DESC";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();
		$this->params['cmd'] = array(
			/*'process' => array(
				'url' => "javascript:loadcontent(\"".site_url( $this->router->fetch_directory() . $this->router->fetch_class() ."/process/[urlparams]" ) . "\"".");",
				'icon' => "far fa-arrow-alt-circle-right text-danger",
			),*/
		);
		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "javascript:loadcontent(\"engine-content\",\"".site_url( $this->router->fetch_directory() . $this->router->fetch_class() ."/browse_detail/[urlparams]" ) . "\"".");";
		$this->params['sqlmaster'] = $this->params['sql'];
		// Slave Params
		$this->params['table-slave'] ="stockopnamedetails";
		$this->params['slavecommand'] ="browse,add,delete";
		$this->params['slavename'] = $this->lang->line("stockopnamedetail");
		$this->params['sqlslave']="SELECT STOCKOPNAMEDETAILID, STOCKOPNAME, CONCAT(inventories.NAME,'<br><i class=''fa fa-barcode''></i> : ',stockopnamedetails.BARCODE) AS INVENTORYDETAIL,
		  inventoryconditions.NAME INVENTORYCONDITION,
			SYSTEMQTY, ACTUALQTY, stockopnamedetails.DESCRIPTION, DIFFERENCE
			FROM stockopnamedetails
			LEFT JOIN inventorydetails ON INVENTORYDETAILID=stockopnamedetails.INVENTORYDETAIL
			LEFT JOIN inventories ON INVENTORYID=inventorydetails.INVENTORY
			LEFT JOIN stockopnames ON STOCKOPNAMEID=STOCKOPNAME
			LEFT JOIN inventoryconditions ON INVENTORYCONDITIONID = stockopnamedetails.INVENTORYCONDITION
			";
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'STOCKOPNAMEID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'NAME' => array(
				'class' => "sorting",
			),
			'STARTDATE' => array(
				'class' => "sorting",
				'type' => "date"
			),
			'ENDDATE' => array(
				'class' => "sorting",
				'type' => "date"
			),
			'STOCKOPNAMEBY' => array(
				'class' => "sorting",
			),
			'STOCKOPNAMESTATUS' => array(
				'class' => "sorting",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getStockopnameStatus(),
			),
			'VALIDATEBY' => array(
				'class' => "sorting",
			),
			'DESCRIPTION' => array(
				'class' => "sorting",
			),
			
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'STOCKOPNAMEID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),
			'STARTDATE' => array(
				'validation' => "required",
				'class' => "col-md-3",
				'type' => "date",
				'value' => date('Y-m-d'),
			),
			'ENDDATE' => array(
				'validation' => "required",
				'class' => "col-md-3",
				'type' => "date",
			),
			'STOCKOPNAMEBY' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getEmployee())
			),
			'VALIDATEBY' => array(
				'validation' => "required",
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getEmployee())
			),
			'DESCRIPTION' => array(
				// 'validation' => "required",
				'class' => "col-md-6 select2",
				'type' => "textarea",
			),
		);
	}
	
	function getData(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}


	function add(){
		if (!empty($_POST)) {
			// $_POST['STOCKOPNAMEBYY'] = $this->logged['IDREF'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		if (!empty($_POST)) {
			// $_POST['STOCKOPNAMEBYY'] = $this->logged['IDREF'];
		}
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
	function delete(){
		$delete=$this->db->delete($this->params['table'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
		if($delete){
			echo "<script>
				loadcontent('main-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."');
			</script>";
		}
	}

	function fieldselectmaster(){
		$this->params['fieldselectedmaster']=array(
			'NAME' => array(),	
			'STOCKOPNAMEBY' => array(),
			'STARTDATE' => array(),			
			'ENDDATE' => array(),			
		);
	}

	function fieldselectslave(){
		$this->params['fieldselectslave']=array(
			'SEQ' => array(

			),
			'STOCKOPNAMEDETAILID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'STOCKOPNAME' => array(
				'type' => "foreignkey",
				'hidden' => true
			),
			/*'INVENTORY' => array(
				'class' => "sorting",
				'width' => "150px",
			),*/
			'INVENTORYDETAIL' => array(
				'class' => "sorting",
				'width' => "150px",
			),
			'SYSTEMQTY' => array(
				'class' => "sorting",
				'type' => "number",
				'width' => "70px",
			),
			'ACTUALQTY' => array(
				'class' => "sorting",
				'type' => "number",
				'width' => "70px",
			),
			'DIFFERENCE' => array(
				'class' => "sorting",
				'type' => 'number',
			),
			'DESCRIPTION' => array(
				'class' => "sorting",
			),
			'INVENTORYCONDITION' => array(
				'class' => "sorting",
			),
			/*'STOCKOPNAMESTATUS' => array(
				'class' => "sorting",
			),*/
		);
	}
	function fieldeditslave(){
		$this->jsinclude();
		$this->params['fieldeditslave']=array(
			'STOCKOPNAMEDETAILID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'STOCKOPNAME' => array(
				'type' => "foreignkey",
				'hidden' => true,
				'value' => $this->urisegments['valpk']
			),
			'INVENTORY' => array(
				'class' => "col-md-3",
				'readonly' => true,
				// 'help' => "F1",
			),
			'BARCODE' => array(
				'class' => "col-md-3",
				'validation' => "required",
				// 'help' => "F2",
			),
			'INVENTORYNAME' => array(
				'class' => "col-md-3",
				'value' => "",
				'readonly' => true,
			),
			'INVENTORYDETAIL' => array(
				'class' => "col-md-3",
				'width' => "150px",
				'hidden' => true,
			),
			'SYSTEMQTY' => array(
				'class' => "col-md-3",
				'type' => "number",
				'value' => "0",
				'readonly' => true,
			),
			'ACTUALQTY' => array(
				'class' => "col-md-3",
				'validation' => "",
				'type' => "number",
				'value' => "1",
				// 'help' => "F3",
			),
			'DESCRIPTION' => array(
				'class' => "col-md-3",
				'type' => "textarea",
				// 'help' => "F4",
			),
			'INVENTORYCONDITION' => array(
				'class' => "col-md-3",
				'type' => "dropdownquery",
				'sourcequery' => $this->Mmasterdata->getInventoryCondition(),
			),
			'STOCKOPNAMESTATUS' => array(
				'class' => "col-md-3",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getStockopnameStatus(),
			),	
		);
	}

	function process(){
		$check_proses = $this->db->where($this->urisegments['pk'],$this->urisegments['valpk'])->get("stockopnames")->row();
		// dd($check_proses);exit;
		if ($check_proses->STOCKOPNAMESTATUS == "0") {
				$so_detail = $this->db->where('STOCKOPNAME',$check_proses->STOCKOPNAMEID)->get('stockopnamedetails')->result();
				foreach ($so_detail as $key => $value) {
					$this->db->set('QTY',$value->ACTUALQTY)->where('INVENTORYDETAILID',$value->INVENTORYDETAIL)->update('inventorydetails');
				}
				$this->db->set('STOCKOPNAMESTATUS',1)->where($this->urisegments['pk'],$this->urisegments['valpk'])->update('stockopnames');
			echo "
				<script>
				showToast('Berhasil');
				</script>
			";
				$this->browse();
		}
		else{
			$this->browse();
			echo "
				<script>
				showToast('Data sudah diproses',false);
				</script>
			";
		}
	}

	function browse_detail(){
		$this->params['primarykeymaster']=$this->urisegments['pk'];
		$this->params['valprimarykeymaster']=$this->urisegments['valpk'];
		$this->fieldselectmaster();
		$this->fieldselectslave();
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse_detail();
	}
	function add_detail(){
		$this->params['primarykeymaster']=$this->urisegments['pk'];
		$this->params['valprimarykeymaster']=$this->urisegments['valpk'];
		$this->fieldselectmaster();
		$this->fieldeditslave();
		unset($this->params['fieldeditslave']['STOCKOPNAMESTATUS']);
		unset($this->params['fieldeditslave']['INVENTORYNAME']);
		$post = $this->input->post();
		if (count($post)>0) {

			// $inventories = $this->db->where("BARCODE",$post['BARCODE'])->get('inventories');
			// $this->db->set("QTY",$_POST['ACTUALQTY'])->where("INVENTORYID",$_POST['INVENTORY'])->update('inventories');
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add_detail();
	}

	function edit_detail(){
		$this->params['primarykeymaster']= $this->urisegments['fk'] . "ID";
		$this->params['valprimarykeymaster']=$this->urisegments['valfk'];
		$this->fieldselectmaster();
		$this->fieldeditslave();
		unset($this->params['fieldeditslave']['BARCODE']);
		unset($this->params['fieldeditslave']['INVENTORYNAME']);
		$this->params['fieldeditslave']['INVENTORY'] = array(
											'class' => "col-md-3",
											'type' => "dropdownquery",
											'sourcequery' => $this->Mmasterdata->getProduct(),
											'disabled' => true,
										);
		$this->params['command']="browse";
		/*$this->params['jsinclude'] .= "<script type='text/javascript'>
			$(function(){
                $('#BARCODE').remove();
			});
		</script>";*/
		$post = $this->input->post();
		if (count($post)>0) {
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->edit_detail();
	}

	function delete_detail(){
		$delete=$this->db->delete($this->params['table-slave'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
		if($delete){
			echo "<script>
				loadcontent('main-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse_detail/pk/".$this->urisegments['fk']."ID/valpk/".urldecode($this->urisegments['valfk'])."/');
			</script>";
		}
	}

	function showInv(){
		$post = $this->input->post();
		if (count($post)>0) {
			$inventories = $this->db->select("NAME,QTY,INVENTORYDETAILID")
								->where("inventorydetails.BARCODE",$post['BARCODE'])
								->join('inventories','INVENTORYID=INVENTORY','LEFT')
								->get('inventorydetails')->row();
			header("Content-Type: application/json");
			echo json_encode($inventories);
		}
		else{
			show_404();
		}
	}
	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			$(function(){
              /*$.get('".site_url($this->router->fetch_directory() . $this->router->fetch_class() . '/listInventories')."', function(data){
                var input = $('#INVENTORY');
                input.typeahead({
                  source: data,
                  autoSelect: true,
                  updater: function(item) {
                    $('#BARCODE').val(item.id);
                    return item;
                  }
                });
                input.change(function() {
                  var current = input.typeahead('getActive');
                  if (current) {
                    if (current.name == input.val()) {
                    } else {;
                    }
                  } else {
                  }
                });
              },'json');*/

            $('#BARCODE').keypress(function(e){
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if(keycode == '13'){
                		$(this).change();
                		$('#ACTUALQTY').focus();
                    e.preventDefault();
										return false;
                }
            });
			  $('#BARCODE').change(function(){
	                var barcode = $(this).val();
	                $.post('".site_url($this->router->fetch_directory() . $this->router->fetch_class() . '/showInv')."',{BARCODE : barcode},function(result){
	                    if (result !== null) {
	                        $('#INVENTORY').val(result.NAME);
	                        $('#INVENTORYDETAIL').val(result.INVENTORYDETAILID);
	                        $('#SYSTEMQTY').val(result.QTY);
	                        // $('button[type=\"submit\"]').click();
	                    }
	                    else{
				            swal.fire({
						        icon: 'error',
						        title: 'Tidak ditemukan',
						        text: 'Barcode tidak ditemukan',
						        showConfirmButton: !1,
						        timer: 1500
					        });
	                        $('#BARCODE').val('');
	                        $('#INVENTORY').val('');
	                        $('#INVENTORYDETAIL').val('');
	                        $('#BARCODE').focus();
	                    }
	                },'json');
	            });
	             $('#INVENTORY').change(function(){
	                var barcode = $('#BARCODE').val();
	                if (barcode==''){
	                	return false;
	                }
	                $.post('".site_url($this->router->fetch_directory() . $this->router->fetch_class() . '/showInv')."',{BARCODE : barcode},function(result){
	                    if (result !== null) {
	                        $('#INVENTORYNAME').val(result.NAME);
	                        $('#SYSTEMQTY').val(result.QTY);
	                    }
	                    else{
	                        swal.fire({
						        icon: 'error',
						        title: 'Tidak ditemukan',
						        text: 'Inventori tidak ditemukan',
						        showConfirmButton: !1,
						        timer: 1500
					        });
	                        $('#BARCODE').val('');
	                    }
	                },'json');
	            });
	            $('#BARCODE').focus();
	            $('#BARCODE').attr('autocomplete', 'off');
 							$('#SYSTEMQTY').attr('tabindex','-1');
	            $(document).unbind('keydown');
                $(document).keydown(function(event){
                  if(event.keyCode == 112) {
                    event.preventDefault();
                    $('#INVENTORY').focus();
                    return false;
                  }
                });
                $(document).keydown(function(event){
                  if(event.keyCode == 113) {
                    event.preventDefault();
                    $('#BARCODE').focus();
                    return false;
                  }
                });
                $(document).keydown(function(event){
                  if(event.keyCode == 114) {
                    event.preventDefault();
                    $('#ACTUALQTY').focus();
                    return false;
                  }
                });
                $(document).keydown(function(event){
                  if(event.keyCode == 116) {
                    event.preventDefault();
                    return false;
                  }
                });
                $(document).keydown(function(event){
                  if(event.keyCode == 115) {
                    event.preventDefault();
                    $('#DESCRIPTION').focus();
                    return false;
                  }
                });
                $(document).keydown(function(event){
                  if(event.keyCode == 123) {
                    event.preventDefault();
                    $('button[type=\"submit\"]').click();
                    return false;
                  }
                });
                $(document).keydown(function(event){
                  if(event.keyCode == 122) {
                    event.preventDefault();
                    return false;
                  }
                });
                $(document).keydown(function(event){
                  if(event.keyCode == 121) {
                    event.preventDefault();
                    $('button[type=\"reset\"]').click();
                    return false;
                  }
                });

			});
		</script>";
	}


	
}
