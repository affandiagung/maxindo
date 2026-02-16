<style>
  .vis-itemset{
    height: ;
  }
</style>
<h1 class="text-center bg-secondary text-dark p-2">Confirmed / Deal Inventory</h1>
<div class="table-responsive pb-10">
  <div id="kt_timeline_widget_1" class="vis-timeline-custom min-w-700px" data-kt-timeline-widget-1-image-root="assets/media/" data-kt-timeline-widget-1-blockui="true"></div>
</div>

<script type="text/javascript">
  // Detect element
  $(function(){
    const element = document.querySelector('#kt_timeline_widget_1');
    if (!element) {
      return;
    }

    if(element.innerHTML){
      return;
    }

    // Set variables
    // var now = Date.now();
    var now = <?php echo strtotime($TGLINVAWAL); ?>;
        // Build vis-timeline datasets
    var groups = new vis.DataSet(<?php echo $inventory; ?>);


    var items = new vis.DataSet(<?php echo $availability; ?>);

        // Set vis-timeline options
    var options = {
      zoomable: false,
      moveable: false,
      selectable: true,
      min: "<?php echo $TGLINVAWAL; ?>",
      max: "<?php echo $TGLINVAKHIR; ?>",

      // More options https://visjs.github.io/vis-timeline/docs/timeline/#Configuration_Options
      margin: {
        item: {
          horizontal: 10,
          vertical: 35
        }
      },

      // Remove current time line --- more info: https://visjs.github.io/vis-timeline/docs/timeline/#Configuration_Options
      showCurrentTime: false,

      // Whitelist specified tags and attributes from template --- more info: https://visjs.github.io/vis-timeline/docs/timeline/#Configuration_Options
      xss: {
        disabled: false,
        filterOptions: {
          whiteList: {
            div: ['class', 'style'],
            img: ['data-kt-timeline-avatar-src', 'alt'],
            a: ['href', 'class']
          },
        },
      },
            // specify a template for the items
      template: function (item) {

        return `<div class="bg-light-${item.color} d-flex align-items-center position-relative h-40px w-100 p-2 overflow-hidden">
        <div class="position-absolute d-block bg-${item.color} start-0 top-0 h-100 z-index-1" style="width: ${item.progress};"></div>
        
        <div class="d-flex align-items-center position-relative z-index-2" onclick="alert('ooo');">
        
        <a href="#" class="fw-bold text-white text-hover-dark">${item.content}</a>
        </div>
        </div>        
        `;
      },

            // Remove block ui on initial draw
      onInitialDrawComplete: function () {

        /*const target = element.closest('[data-kt-timeline-widget-1-blockui="true"]');
        const blockUI = KTBlockUI.getInstance(target);

        if (blockUI.isBlocked()) {
          setTimeout(() => {
            blockUI.release();
          }, 1000);      
        }*/
      }
    };

        // Init vis-timeline
    const timeline = new vis.Timeline(element, items, groups, options);

        // Prevent infinite loop draws
    timeline.on("currentTimeTick", () => {            
            // After fired the first time we un-subscribed
      timeline.off("currentTimeTick");
    });

    timeline.on('click', function (properties) {
      let item = properties.item;
      let availability = <?php echo $availability; ?>;
      let arr = [
          { name:"string 1", value:"this", other: "that" },
          { name:"string 2", value:"this", other: "that" }
      ];

      let obj = availability.find(o => o.name === 'string 1');
      if(properties.item){
        const data = items.get(properties.item);
        // console.log(encodeURI('<?php echo site_url( "admin/inventoryusage/index" ); ?>/' + properties.group + "/" + data.content + "/" + data.start + "/" + data.end));
        loadpopup(encodeURI('<?php echo site_url( "admin/cekstok/detail" ); ?>/' + properties.group + "/" + data.content + "/" + data.start + "/" + data.end));
      }
    });
  });
</script>

<!--- Booked Inventory / Not Deal -->

<h1 class="text-center bg-light-danger text-danger p-2">Booked / Lead Inventory</h1>
<div class="table-responsive pb-10">
  <div id="kt_timeline_widget_2" class="vis-timeline-custom min-w-700px" data-kt-timeline-widget-1-image-root="assets/media/" data-kt-timeline-widget-1-blockui="true"></div>
</div>

<script type="text/javascript">
  // Detect element
  $(function(){
    const element2 = document.querySelector('#kt_timeline_widget_2');
    if (!element2) {
      return;
    }

    if(element2.innerHTML){
      return;
    }

    // Set variables
    // var now = Date.now();
    var now = <?php echo strtotime($TGLINVAWAL); ?>;
        // Build vis-timeline datasets
    var groups = new vis.DataSet(<?php echo $bookedinv; ?>);


    var items = new vis.DataSet(<?php echo $booked; ?>);

        // Set vis-timeline options
    var options = {
      zoomable: false,
      moveable: false,
      selectable: true,
      min: "<?php echo $TGLINVAWAL; ?>",
      max: "<?php echo $TGLINVAKHIR; ?>",

      // More options https://visjs.github.io/vis-timeline/docs/timeline/#Configuration_Options
      margin: {
        item: {
          horizontal: 10,
          vertical: 35
        }
      },

      // Remove current time line --- more info: https://visjs.github.io/vis-timeline/docs/timeline/#Configuration_Options
      showCurrentTime: false,

      // Whitelist specified tags and attributes from template --- more info: https://visjs.github.io/vis-timeline/docs/timeline/#Configuration_Options
      xss: {
        disabled: false,
        filterOptions: {
          whiteList: {
            div: ['class', 'style'],
            img: ['data-kt-timeline-avatar-src', 'alt'],
            a: ['href', 'class']
          },
        },
      },
            // specify a template for the items
      template: function (item) {

        return `<div class="bg-light-${item.color} d-flex align-items-center position-relative h-40px w-100 p-2 overflow-hidden">
        <div class="position-absolute d-block bg-${item.color} start-0 top-0 h-100 z-index-1" style="width: ${item.progress};"></div>
        
        <div class="d-flex align-items-center position-relative z-index-2" onclick="alert('ooo');">
        
        <a href="#" class="fw-bold text-white text-hover-dark">${item.content}</a>
        </div>
        </div>        
        `;
      },

            // Remove block ui on initial draw
      onInitialDrawComplete: function () {

        /*const target = element2.closest('[data-kt-timeline-widget-1-blockui="true"]');
        const blockUI = KTBlockUI.getInstance(target);

        if (blockUI.isBlocked()) {
          setTimeout(() => {
            blockUI.release();
          }, 1000);      
        }*/
      }
    };

        // Init vis-timeline
    const timeline = new vis.Timeline(element2, items, groups, options);

        // Prevent infinite loop draws
    timeline.on("currentTimeTick", () => {            
            // After fired the first time we un-subscribed
      timeline.off("currentTimeTick");
    });

    timeline.on('click', function (properties) {
      let item = properties.item;
      let availability = <?php echo $availability; ?>;
      let arr = [
          { name:"string 1", value:"this", other: "that" },
          { name:"string 2", value:"this", other: "that" }
      ];

      let obj = availability.find(o => o.name === 'string 1');
      if(properties.item){
        const data = items.get(properties.item);
        // console.log(encodeURI('<?php echo site_url( "admin/inventoryusage/index" ); ?>/' + properties.group + "/" + data.content + "/" + data.start + "/" + data.end));
        loadpopup(encodeURI('<?php echo site_url( "admin/cekstok/detailbooked" ); ?>/' + properties.group + "/" + data.content + "/" + data.start + "/" + data.end));
      }
    });
  });
</script>