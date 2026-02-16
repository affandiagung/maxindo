<div class="card-body">
  <div id="alert"></div>
  <form novalidate="novalidate" class="form-horizontal" id="form_checkAvail">
    <div class="form-group row mb-3">
      <label class="col-md-2 col-xs-12 col-form-label" for="INVENTORY">Iventory</label>
      <div class="col-md-6 select2">
        <div class="input-group">
          <select name="INVENTORY" id="INVENTORY" class="form-select" required="required">
            <?php foreach ($INVENTORY as $key => $value): ?>
              <option value="<?php echo $value['keydt'] ?>"><?php echo $value['valuedt'] ?></option>
            <?php endforeach ?>
          </select>
          <!-- <span class="input-group-text"><i class="fa fa-list"></i></span> -->
        </div>
      </div>
    </div>
    <div class="form-group row mb-3">
      <label class="col-md-2 col-xs-12 col-form-label" for="JOBDESCRIPTION">Dari Tanggal</label>
      <div class="col-md-6">
        <div class="input-group">
          <input type="text" class="datetime-picker form-control" name="TGLAWAL" id="TGLAWAL" value="<?php echo $TGLAWAL; ?>" />
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
      </div>
    </div>
    <div class="form-group row mb-3">
      <label class="col-md-2 col-xs-12 col-form-label" for="QUALIFICATION">Sampai Tanggal</label>
      <div class="col-md-6">
        <div class="input-group">
          <input type="text" class="datetime-picker form-control" name="TGLAKHIR" id="TGLAKHIR" value="<?php echo $TGLAKHIR; ?>" />
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
      </div>
    </div>
    <div class="form-group row mb-3">
      <div class="offset-md-2 col-md-6">
        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i>Cari</button>
      </div>
    </div>
    <div class="form-group row mb-3">
      <div class="offset-md-2 col-md-6 form_result">

      </div>
    </div>
  </form>
</div>

<script>
  $(document).ready(function(){

    $("#form_checkAvail").submit(function(){
      $(".form_result").html("<div class=\"text-center spin-loading\"><img src=\"" + base_url + "assets/img/spin.svg\" /></div>");
      var datapost=$(this).serialize();
      var target=site_url+'admin/dashboard/checkAvailability';
      $.post(target, datapost , function(r){
       $(".form_result").html(r);
     })
      return false;
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