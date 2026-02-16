<?php 
  $config = $this->Mmasterdata->getConfiguration();
?>
<table style="margin-bottom:24px;">
  <tr>
    <td style="width:100px;"><img src="<?php echo base_url("uploads/" . $config->APP_LOGO_HEADER); ?>" style='width:75px;' /> </td>
    <td>
      <h2 style="font-weight:bold;"><?php echo $config->OFFICE_NAME; ?></h2>
      <p><?php echo $config->OFFICE_ADDRESS; ?></p>
    </td>
  </tr>
</table>
<hr style="border-color: #a1a1a1;border-width: 2px" />
<div class='content'>
  <?php echo $msg ?>
</div>