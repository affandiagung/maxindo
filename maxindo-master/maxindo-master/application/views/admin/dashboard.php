<div class="card card-custom gutter-b example example-compact">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-list me-5"></i>Dashboard</h3>
    <div class="card-toolbar">
      <div class="example-tools justify-content-center">
        <div class="btn-no-group">
          <a title="Segarkan" role="button" href="javascript:loadcontent('main-content','<?php echo site_url(); ?>/admin/dashboard/');" class="btn  btn-light-primary btn-browse me-2"><i class="fa fa-sync-alt"></i> Segarkan</a>
        </div>
      </div>
    </div>
  </div>
  <div class="card-body px-5">
    <form action="" class="form-inline" method="POST" id="dsb_form">
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
          <!-- <button type="button" id="checkAvailability" class="btn btn-success">Check Ketersediaan</button> -->
        </div>
      </div>
    </form>
    <div class="row">
      <?php 
      $bg = array(
        'primary', 'success', 'warning', 'danger','info', 'dark'
      );
      $rand = 0;
      $total = 0;
      foreach($dashboard as $dash){
        $bgclass = $bg[$rand];
        echo '
        <div class="col-md-4 mb-5">';
        if(isset($dash["controller"])){
          echo '<div onclick="loadcontent(\'main-content\',site_url + \'admin/'.$dash["controller"].'\')" class="card bg-'.$bgclass.' hoverable card-xl-stretch mb-xl-8">';
        } else {
          echo '<div class="card bg-'.$bgclass.' hoverable card-xl-stretch mb-xl-8">';
        }
        echo '<!--begin::Body-->
        <div class="card-body">
        <i class="'.$dash["icon"].' text-white fa-3x"></i>
        <!--end::Svg Icon-->
        <div class="text-white fw-bolder fs-1 mb-2 mt-5">'.number_format( $dash["total"],0,",",".").'</div>
        <div class="fw-bold text-white fs-5">'.$dash["nama"].'</div>
        </div>
        <!--end::Body-->
        </div>
        </div>';
        if( $rand == count($bg) - 1){
          $rand = 0;
        } else {
          $rand++;
        }
      } ?>
    </div>
    <div class="row mt-5">
      <div class="col-md-12">
        <div id="table-detail"></div>
      </div>
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
    $('#checkAvailability').on('click',function(){
      loadpopup(site_url+'/admin/dashboard/checkAvailability');
    });
    $('#debug').on('click',function(){
      loadpopup(site_url+'/admin/dashboard/debug');
    });

    $(this).updatePolyfill();

    $(".ws-number").unbind("mousewheel");
    $(".ws-number").unbind("wheel");
    
    let parentModal = $(".select2 > .input-group > select").parents("#modalContainer");
    let parentPopup = $(".select2 > .input-group > select").parents("#modalPopupContainer");

    if( parentModal.length > 0 ) {
      var select2_parent = $("#modalContainer");
    } 
    if( parentPopup.length > 0 ){
      var select2_parent = $("#modalPopupContainer");
    } 
    if( parentPopup.length == 0 && parentModal.length == 0){
      var select2_parent = $(document.body);
    }

    $(".select2 > .input-group > select").select2({
      placeholder: $(this).attr("placeholder"),
      allowClear: Boolean($(this).data("allow-clear")),
      dropdownParent: select2_parent
    });

    $(".date-picker").flatpickr({
      dateFormat: "Y-m-d",
        // disableMobile: true
    });
    $(".month-picker").flatpickr({
      dateFormat: "Y-m",
      minViewMode: 1,
    });
    $(".datetime-picker").flatpickr({
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      time_24hr: true
    });
    $(".time-picker").flatpickr({
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
    
  });


</script>


