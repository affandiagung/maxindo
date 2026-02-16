"use strict";
var KTtrackorder = function() {
	var e, t, i;
	return {
		init: function() {
			e = document.querySelector("#kt_track_order"), t = document.querySelector("#kt_track_order_submit"), i = FormValidation.formValidation(e, {
				fields: {
					ordercode: {
						validators: {
							notEmpty: {
								message: "Kode order tidak boleh kosong"
							},
						}
					},
			},
			plugins: {
				trigger: new FormValidation.plugins.Trigger,
				bootstrap: new FormValidation.plugins.Bootstrap5({
					rowSelector: ".fv-row",
					eleInvalidClass: "",
					eleValidClass: ""
				})
			}
		}), t.addEventListener("click", (function(n) {
			n.preventDefault(), i.validate().then((function(i) {
				t.setAttribute("data-kt-indicator", "on");
				t.disabled = !0;
				if( i == "Valid" ){
					t.removeAttribute("data-kt-indicator"); 
					t.disabled = !1;
					var target = site_url + "trackorder/doTrackorder";
					var datapost = {
						ordercode: $("#ordercode").val(),
					}
					$.post(target, datapost, function(r){
						if( r == "success"){
							Swal.fire({
								text: "Kode order ditemukan!",
								icon: "success",
								buttonsStyling: !1,
								showConfirmButton: !1,
		        		timer: 1500
							}).then(function(){
								location.replace(site_url + "trackorder/index/" + datapost.ordercode);
							});
						} else {
							Swal.fire({
								text: "Kode Order tidak ditemukan.",
								icon: "error",
								buttonsStyling: !1,
								showConfirmButton: !1,
		        		timer: 150000
							});
						}
					});
				} else {
					t.removeAttribute("data-kt-indicator"); 
					t.disabled = !1;
					Swal.fire({
						text: "Informasi Kode order belum sesuai.",
						icon: "error",
						buttonsStyling: !1,
						showConfirmButton: !1,
        		timer: 1500
					});
				}
			}))
		}))
	}
}
}();
KTUtil.onDOMContentLoaded((function() {
	KTtrackorder.init()
}));
