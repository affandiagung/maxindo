<div class="card card-custom gutter-b example example-compact">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-list me-5"></i>Inventory Calendar</h3>
    <div class="card-toolbar">
      <div class="example-tools justify-content-center">
        <div class="btn-no-group">
          <a title="Segarkan" role="button" href="javascript:loadcontent('main-content','<?php echo site_url(); ?>/admin/cekstok/');" class="btn  btn-light-primary btn-browse me-2"><i class="fa fa-sync-alt"></i> Segarkan</a>
        </div>
      </div>
    </div>
  </div>
  <div class="card-body px-5">
    <div class="row mt-5">
      <div class="col-md-12">
        <form novalidate="novalidate" class="form-horizontal" id="formCalendar">
          <div class="form-group row mb-3">
            <label class="col-md-2 col-xs-12 col-form-label" for="INVENTORY">Iventory</label>
            <div class="col-md-6 select2">
              <div class="input-group">
                <select name="INVENTORY[]" id="INVENTORY[]" class="form-select" required="required" multiple="multiple">
                  <?php foreach ($INVENTORY as $key => $value){ ?>
                    <option value="<?php echo $value['keydt'] ?>"><?php echo $value['valuedt'] ?></option>
                  <?php } ?>
                </select>
                <!-- <span class="input-group-text"><i class="fa fa-list"></i></span> -->
              </div>
            </div>
          </div>
          <div class="form-group row mb-3">
            <label class="col-md-2 col-xs-12 col-form-label" for="TGLINVAWAL">Dari Tanggal</label>
            <div class="col-md-6">
              <div class="input-group datetime-picker">
                <input type="text" class="form-control" name="TGLINVAWAL" id="TGLINVAWAL" value="<?php echo $TGLINVAWAL; ?>" />
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <div class="form-group row mb-3">
            <label class="col-md-2 col-xs-12 col-form-label" for="TGLINVAKHIR">Sampai Tanggal</label>
            <div class="col-md-6">
              <div class="input-group datetime-picker">
                <input type="text" class="form-control" name="TGLINVAKHIR" id="TGLINVAKHIR" value="<?php echo $TGLINVAKHIR; ?>" />
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <div class="form-group row mb-3">
            <div class="offset-md-2 col-md-6">
              <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i>Cari</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="row mt-5">
      <div class="col-md-12" id="resultCalendar">

      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(function(){
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
    
    $("#formCalendar").submit(function(){
      $("#resultCalendar").html("<div class=\"text-center spin-loading\"><img src=\"" + base_url + "assets/img/spin.svg\" /></div>");
      var datapost=$(this).serialize();
      var target=site_url+'admin/cekstok/loadCalendar';
      $.post(target, datapost , function(r){
       $("#resultCalendar").html(r);
     })
      return false;
    });

    // $(this).updatePolyfill();
  });
</script>