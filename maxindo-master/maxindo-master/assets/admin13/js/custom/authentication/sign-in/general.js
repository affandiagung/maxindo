"use strict";
var KTSigninGeneral = function() {
	var e, t, i;
	return {
		init: function() {
			
			e = document.querySelector("#kt_sign_in_form"), t = document.querySelector("#kt_sign_in_submit"), i = FormValidation.formValidation(e, {
				fields: {
					username: {
						validators: {
							notEmpty: {
								message: "Username tidak boleh kosong"
							},
						}
					},
					password: {
						validators: {
							notEmpty: {
								message: "Password tidak boleh kosong"
							},
							callback: {
								message: "Masukkan password yang benar",
								callback: function(e) {
									if (e.value.length > 0) return _validatePassword()
								}
						}
					}
				}
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
					var target = site_url + "/adminlogin/doLogin";
					var datapost = {
						username: $("#username").val(),
						password: $("#password").val(),
					}
					$.post(target, datapost, function(r){
						if( r == "success"){
							Swal.fire({
								text: "Login Berhasil!",
								icon: "success",
								buttonsStyling: !1,
								showConfirmButton: !1,
		        		timer: 1500
							}).then(function(){
								location.reload();
							});
						} else {
							Swal.fire({
								text: "Username atau password tidak cocok.",
								icon: "error",
								buttonsStyling: !1,
								showConfirmButton: !1,
		        		timer: 200000
							});
						}
					});
				} else {
					t.removeAttribute("data-kt-indicator"); 
					t.disabled = !1;
					Swal.fire({
						text: "Informasi login belum sesuai.",
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
	KTSigninGeneral.init()
}));