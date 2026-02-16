if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/service-worker.js')
	  .then(function(registration) {
	    console.log('Registration PWA successful, scope is:', registration.scope);
	  })
	  .catch(function(error) {
	    console.log('Service worker PWA registration failed, error:', error);
  });
}

// PWA Script
let deferredPrompt;
var swalClick = false;
const addContainer = $('#a2hs-container');
const addBtn = $('#a2hs-button');

addContainer.css("display", "none");

window.addEventListener('beforeinstallprompt', (e) => {
  // Prevent the mini-infobar from appearing on mobile
  e.preventDefault();
  // Stash the event so it can be triggered later.
  deferredPrompt = e;
  // Update UI notify the user they can install the PWA
  // addContainer.slideToggle();
  if( swalClick == false ){
  	swalClick = true;
	  swal.fire({
	    title: "Konfirmasi",
	    text: "Apakah Anda akan memasang aplikasi SIEAP ?",
	    icon: "question",
	    showCancelButton: !0,
	    confirmButtonText: "Ya",
	    cancelButtonText: "Tidak",
	    reverseButtons: !0
	  }).then(function(e) {
	  	if(e.value) {
	  		addToHomeScreen();
	  		swal.close();
	  	}
	  });
  }
});

addBtn.click(function(){
	addToHomeScreen();
});

function addToHomeScreen() {
	deferredPrompt.prompt();
  window.promptEvent.userChoice.then(function(choiceResult) {
  	// addContainer.slideToggle();
  	swalClick = false;
    if (choiceResult.outcome === 'accepted') {
      console.log('mm User accepted the A2HS prompt');
    } else {
      console.log('mm User dismissed the A2HS prompt');
    }
    window.promptEvent = null;
  }); 
}

window.addEventListener('appinstalled', (evt) => {
  // Log install to analytics
  console.log('INSTALL: Success');
});
