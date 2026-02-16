$(function(){
  $(".main-menu").click(function(){
    $(".main-menu").removeClass("active");
    if( $(this).prop("target") == "_blank" ) {

    } else {
      // Delete LocalStorage
      var arr = [];
      for (var i = 0; i < localStorage.length; i++){
          if (localStorage.key(i).substring(0,11) == 'DataTables_') {
              arr.push(localStorage.key(i));
          }
      }
      for (var i = 0; i < arr.length; i++) {
          localStorage.removeItem(arr[i]);
      }

      var targeturl = $(this).attr("href");
      if(targeturl != "#"){
        $(".main-menu").parent("li").removeClass("menu-item-open").removeClass("menu-item-here").removeClass("menu-item-active");
        $("#main-content").html("<div class='text-center spin-loading'><img src='" + base_url + "assets/img/spin.svg' /></div>");
        $("#main-content").load(targeturl, function(response,status,xhr){
          if (xhr.status!="200") {
            if(xhr.status=="404"){
              $("#main-content").load(site_url + "/welcome/error404");
            } 
            else {
              var msg = "Halaman Gagal Dimuat : ";
              bootbox.alert( "Pesan : " + msg + "<br />Status : " + xhr.status + "<br />Status Text : " + xhr.statusText + "<br />Response : " + response);
            }
          }
          getNotif();
        });
        // $("#main-menu li").removeClass("active");
        $(this).addClass("active");
        $(this).parent("li").addClass("menu-item-active");
        // $(".aside-toggle").click();
      }
      return false;
    }
  });
  
  $(".profile-side").click(function(){
    var targeturl = $(this).attr("href");
    if(targeturl != "#"){
      $(".main-menu").parent("li").removeClass("menu-item-active");
      $("#main-content").html("<div class='text-center spin-loading'><img src='" + base_url + "assets/img/spin.svg' /></div>");
      $("#main-content").load(targeturl, function(response,status,xhr){
        if (xhr.status!="200") {
          if(xhr.status=="404"){
            $("#main-content").load(site_url + "/welcome/error404");
          } 
          else {
            var msg = "Halaman Gagal Dimuat : ";
            bootbox.alert( "Pesan : " + msg + "<br />Status : " + xhr.status + "<br />Status Text : " + xhr.statusText + "<br />Response : " + response);
          }
        }
      });
      // $("#main-menu li").removeClass("active");
      $(this).parent("li").addClass("menu-item-active");
      $("#kt_quick_user_toggle").click();
    }
    return false;
  });
});

function showInlineAdd( container ){
  $("#row--"+ container +"--add").removeClass("hidden");
}

function subscribeTokenToTopic(token, topic) {
  fetch('https://iid.googleapis.com/iid/v1/'+token+'/rel/topics/'+topic, {
    method: 'POST',
    headers: new Headers({
      'Authorization': 'key='+fcm_server_key
    })
  }).then(response => {
    if (response.status < 200 || response.status >= 400) {
      throw 'Error subscribing to topic: '+response.status + ' - ' + response.text();
    }
    console.log('Subscribed to "'+topic+'"');
  }).catch(error => {
    console.error(error);
  })
}


function selectRow(id, content){
  var elem = $("#row--"+ content +"--content--" + id); //.find(".col-checkbox .form-check-input").click();
  console.log( elem );
} 

function loadcontent(container, page_url, toast = false) {
  $('#jquery_ui').remove();
  $("#" + container).html("<div class='text-center spin-loading'><img src='" + base_url + "assets/img/spin.svg' /></div>");
  $("#" + container).load(page_url,function(response,status,xhr){
    if (xhr.status!="200") {
      var msg = "Halaman Gagal Dimuat : ";
      bootbox.alert({
        message: xhr.responseText,
        size: "large"
      });
      showToast('Halaman gagal dimuat',false);
    } else {
      if( toast ){
        showToast();
      }
    }
  });
  getNotif();
}

function loadprocess(container, page_url, message = "Apakah Anda akan memproses Data ini ?", toast = false) {
  swal.fire({
    title: "Konfirmasi",
    text: message,
    icon: "warning",
    showCancelButton: !0,
    confirmButtonText: "Ya",
    cancelButtonText: "Tidak",
    cancelButtonColor: '#d33',
    reverseButtons: !0
  }).then(function(e) {
    if(e.value) {
      $("#" + container).html("<div class='text-center spin-loading'><img src='" + base_url + "assets/img/spin.svg' /></div>");
      $("#" + container).load(page_url,function(response,status,xhr){
        if (xhr.status!="200") {
          var msg = "Halaman Gagal Dimuat : ";
          bootbox.alert({
            message: xhr.responseText,
            size: "large"
          });
          showToast('Halaman gagal dimuat',false);
        } else {
          if( toast ){
            showToast();
          }
        }
      });
    } else {
      e.dismiss();
    }
  });
}

function loadtab(tabId, container, page_url){
  $('#jquery_ui').remove();
  $("a[href='#" + tabId + "']").parent("li").show();
  $("a[href='#" + tabId + "']").tab("show");
  loadcontent(container, page_url);
  getNotif();
}

function hidetab(tabId, container){
  $('#jquery_ui').remove();
  $("a[href='#" + tabId + "']").parent("li").hide();
  $("a[href='#" + tabId + "']").tab("dispose");
  $("#" + container).html("");
}

function backtab( openTab, closeTab = null ){
  $("[href='#" + openTab + "']").tab("show");
  // $("a[href='#" + closeTab+"'").parent("li").hide();
}

function loadmodal( page_url, type = "none"){
  $("#modalContainer").load(page_url,function(response,status,xhr){
    if (xhr.status!="200") {
      var msg = "Halaman Gagal Dimuat : ";
      bootbox.alert({
        message: xhr.responseText,
        size: "large"
      });
      showToast('Halaman gagal dimuat',false);
    }
  });
  $("#modalFooter #btn-print-modal").hide();
  $("#modalFooter #btn-save-modal").hide();
  
  if( type == "print" ){
    $("#modalFooter #btn-print-modal").show();
  }
  if( type == "checkout" ){
  $("#modalFooter #btn-checkout-modal").show();
  }
  if( type == "save" ){
    $("#modalFooter #btn-save-modal").show();
  }
  var engineModal = new bootstrap.Modal(document.getElementById('engineModal'), {
    backdrop: 'static', 
    keyboard: false
  });
  engineModal.show();
}

function loadpopup( page_url, type="none"){
  $("#modalPopupContainer").load(page_url,function(response,status,xhr){
    if (xhr.status!="200") {
      var msg = "Halaman Gagal Dimuat : ";
      bootbox.alert({
        message: xhr.responseText,
        size: "large"
      });
      showToast('Halaman gagal dimuat',false);
    }
  });
  $("#modalPopupFooter #btn-print-popup").hide();
  $("#modalPopupFooter #btn-save-popup").hide();
  if( type == "print" ){
    $("#modalPopupFooter #btn-print-popup").show();
  }
  if( type == "save" ){
    $("#modalPopupFooter #btn-save-popup").show();
  }
  var popupModal = new bootstrap.Modal(document.getElementById('popupModal'), {
    keyboard: false
  });
  popupModal.show();
}

function loadinputmodal( page_url, type = "none"){
  $("#modalInputContainer").load(page_url,function(response,status,xhr){
    if (xhr.status!="200") {
      var msg = "Halaman Gagal Dimuat : ";
      bootbox.alert({
        message: xhr.responseText,
        size: "large"
      });
      showToast('Halaman gagal dimuat',false);
    }
  });
  $("#modalInputFooter #btn-print-modal").hide();
  $("#modalInputFooter #btn-save-modal").hide();
  
  if( type == "print" ){
    $("#modalInputFooter #btn-print-modal").show();
  }
  if( type == "checkout" ){
  $("#modalInputFooter #btn-checkout-modal").show();
  }
  if( type == "save" ){
    $("#modalInputFooter #btn-save-modal").show();
  }
  var inputModal = new bootstrap.Modal(document.getElementById('inputModal'), {
    backdrop: 'static', 
    keyboard: false
  });
  inputModal.show();
}

function loadpost(container, page_url, datapost) {
  $("#" + container).html("<div class='text-center spin-loading'><img src='" + base_url + "assets/img/spin.svg' /></div>");
  $.post(page_url, datapost, function (e) {
    $("#" + container).html(e, function(response,status,xhr){
      if (xhr.status!="200") {
        var msg = "Halaman Gagal Dimuat : ";
        $("#" + container).html( response );
        showToast('Halaman gagal dimuat',false);
        return false;
      }
      else {
        showToast();
        return true;
      }
    });
  }).fail(function(err){
    bootbox.alert({
      message: err.responseText,
      size: "large"
    });
  });
}

function print(container) {
  $("#" + container).printArea({
    "popup" : "close"
  });
}

function deletedata(container, page_url) {
  swal.fire({
    title: "Konfirmasi",
    text: "Apakah Anda akan menghapus Data ini ?",
    icon: "warning",
    showCancelButton: !0,
    confirmButtonText: "Ya",
    cancelButtonText: "Tidak",
    cancelButtonColor: '#d33',
    reverseButtons: !0
  }).then(function(e) {
    if(e.value) {
      $("#" + container).html("<div class='text-center spin-loading'><img src='" + base_url + "assets/img/spin.svg' /></div>");
      $("#" + container).load(page_url);
      swal.fire({
        icon: "success",
        title: "Data berhasil dihapus",
        showConfirmButton: !1,
        timer: 1500
      });
    } 
    else {
      e.dismiss;
    }
  });
}

function printall(container, page_url, datapost){
  var myData = "";
  $.each(datapost, function(){
    if( $(this).prop("checked") == true ){
      var id = $(this).attr("id");
      myData += id+"=true&";
    }
  });
  $.post(page_url, myData, function (e) {
    $("#modalPopupContainer").html(e, function(response,status,xhr){
      if (xhr.status!="200") {
        var msg = "Halaman Gagal Dimuat : ";
        $("#" + container).html( response );
        showToast('Halaman gagal dimuat',false);
      }
      else {
        showToast();
      }
    });
  }).fail(function(err){
    bootbox.alert({
      message: err.responseText,
      size: "large"
    });
  });

  $("#modalPopupFooter #btn-print-popup").hide();
  $("#modalPopupFooter #btn-save-popup").hide();
  
  $("#modalPopupFooter #btn-print-popup").show();
  var popupModal = new bootstrap.Modal(document.getElementById('popupModal'), {
    keyboard: false
  });
  popupModal.show();
}

function popupall(page_url, datapost, newtab = false){
  var myData = "";
  $.each(datapost, function(){
    if( $(this).prop("checked") == true ){
      var id = $(this).attr("id");
      myData += id+"=true&";
    }
  });
  $.post(page_url, myData, function (e) {
    $("#modalContainer").html(e, function(){
      if (xhr.status!="200") {
        var msg = "Halaman Gagal Dimuat : ";
        bootbox.alert({
          message: xhr.responseText,
          size: "large"
        });
        showToast('Halaman gagal dimuat',false);
      }
    });
  }).fail(function(err){
    bootbox.alert({
      message: err.responseText,
      size: "large"
    });
  });
  var engineModal = new bootstrap.Modal(document.getElementById('engineModal'), {
    keyboard: false
  });
  engineModal.show();
}

function updateall(container, page_url, datapost, newtab = false){
  $("#" + container).html("<div class='text-center spin-loading'><img src='" + base_url + "assets/img/spin.svg' /></div>");
  var myData = "";
  $.each(datapost, function(){
    if( $(this).prop("checked") == true ){
      var id = $(this).attr("id");
      myData += id+"=true&";
    }
  });
  if( newtab != false ){
    var nw = window.open("", "resultWindow");
    nw.document.open();
    nw.document.write('Ini Contoh');
    nw.document.close();
  } else {
    $.post(page_url, myData, function (e) {
      $("#" + container).html(e, function(response,status,xhr){
        if (xhr.status!="200") {
          var msg = "Halaman Gagal Dimuat : ";
          $("#" + container).html( response );
          showToast('Halaman gagal dimuat',false);
        }
        else {
          showToast();
        }
      });
    }).fail(function(err){
      bootbox.alert({
        message: err.responseText,
        size: "large"
      });
    });
  }
}

function deleteall(container, page_url, datapost) {
  swal.fire({
    title: "Konfirmasi",
    text: "Anda akan menghapus SEMUA DATA TERPILIH ?",
    icon: "warning",
    showCancelButton: !0,
    confirmButtonText: "Ya",
    cancelButtonText: "Tidak",
    cancelButtonColor: '#d33',
    reverseButtons: !0
  }).then(function(e) {
    if(e.value) {
      $("#" + container).html("<div class='text-center spin-loading'><img src='" + base_url + "assets/img/spin.svg' /></div>");
      var myData = "";
      $.each(datapost, function(){
        if( $(this).prop("checked") == true ){
          var id = $(this).attr("id");
          myData += id+"=true&";
        }
      });
      $.post(page_url, myData, function (e) {
        $("#" + container).html(e, function(response,status,xhr){
          if (xhr.status!="200") {
            var msg = "Halaman Gagal Dimuat : ";
            $("#" + container).html( response );
            showToast('Halaman gagal dimuat',false);
          }
          else {
            showToast();
          }
        });
      }).fail(function(err){
        bootbox.alert({
          message: err.responseText,
          size: "large"
        });
      });
      swal.fire({
        icon: "success",
        title: "Data berhasil dihapus",
        showConfirmButton: !1,
        timer: 1500
      });
    } else {
      e.dismiss;
    }
  });
}

function opensearch(){
  $(".searchpanel").slideToggle("fast");
}

function showToast(message = "", success = true){
  if( message == "" ){
    message = "Data Berhasil Disimpan";
  }
  if( success  == true ){
    swal.fire({
      icon: "success",
      title: message,
      showConfirmButton: !1,
      timer: 1500
    });
  } else {
    swal.fire({
      icon: "error",
      title: message,
      showConfirmButton: !1,
      timer: 1500
    });
  }
  /*
  toastr.options = {
    "debug": false,
    "newestOnTop": false,
    "positionClass": "toast-bottom-right",
    "closeButton": true,
    "progressBar": true
  };
  if( success ){
    toastr.success(message, 'Sukses');
  } else {
    toastr.error(message, 'Error');
  }
  */
}

function formatInputNumber(){
  webshims.setOptions("forms-ext", {
    replaceUI: "auto",
    types: "number"
  });
  webshims.polyfill("forms forms-ext");
}

function getNotif(){
  var target = site_url + "admin/welcome/getNotif";
  var datapost = {};
  $.post(target, datapost, function(e){
    $.each(e, function(k, v){
      $('#menu-notif-'+k).html('<span class="label label-danger label-inline">'+v+'</span>');
    });
  },"json");
}

function goToByScroll(id) {
  if( $("#" + id).offset() != undefined ){
    $('html,body').animate({
      scrollTop: $("#" + id).offset().top
    }, 'slow');
  }
}

function getAllValues( id ) {
  var inputValues = $('#'+id+' :input').map(function() {
    var type = $(this).prop("type");

    // checked radios/checkboxes
    if ((type == "checkbox" || type == "radio") && this.checked) { 
      return $(this).val();
    }
    // all other fields, except buttons
    else if (type != "button" && type != "submit") {
      return $(this).val();
    }
  });
  return inputValues.join(',');
}

function copy(container, page_url, datapost) {
  var myData = "";
  $.each(datapost, function(){
    if( $(this).prop("checked") == true ){
      var id = $(this).attr("id");
      myData += id+"=true&";
    }
  });
  $.post(page_url, myData, function (e) {
    showToast("Data berhasil dicopy");
  }).fail(function(err){
    bootbox.alert({
      message: err.responseText,
      size: "large"
    });
  });
}

function paste(container, page_url) {
  swal.fire({
    title: "Konfirmasi",
    text: "Apakah Anda akan menempel data yang di-copy ?",
    icon: "warning",
    showCancelButton: !0,
    confirmButtonText: "Ya",
    cancelButtonText: "Tidak",
    cancelButtonColor: '#d33',
    reverseButtons: !0
  }).then(function(e) {
    if(e.value) {
      $("#" + container).load( page_url, function( e ){

      });
    } 
    else {
      e.dismiss;
    }
  });
}
