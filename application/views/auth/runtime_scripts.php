<?php
$runtimeSuccess = '';
$runtimeError = '';

$successKeys = array('SUCCESSMSG', 'success', 'successR');
$errorKeys = array('error', 'error1', 'errormsg', 'errormsg1', 'errormsg2', 'errorR', 'errorR1');

foreach ($successKeys as $key) {
    $value = $this->session->flashdata($key);
    if (!$runtimeSuccess && $value) {
        $runtimeSuccess = (string) $value;
    }
}
foreach ($errorKeys as $key) {
    $value = $this->session->flashdata($key);
    if (!$runtimeError && $value) {
        if ($key === 'errorR') {
            $runtimeError = 'Your security question or answer is incorrect.';
        } elseif ($key === 'errorR1') {
            $runtimeError = 'The email address is not valid for account recovery.';
        } else {
            $runtimeError = (string) $value;
        }
    }
}
?>
<script src="<?=base_url()?>assets/bower_components/select2/dist/js/select2.full.min.js"></script>
<script src="<?=base_url()?>js/cams-select2.js?v=<?=@filemtime(FCPATH.'js/cams-select2.js')?>"></script>
<script src="<?=base_url()?>js/cams-datepicker.js?v=<?=@filemtime(FCPATH.'js/cams-datepicker.js')?>"></script>
<script src="<?=base_url()?>assets/plugins/toastr/toastr.min.js"></script>
<script src="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="<?=base_url()?>js/cams-ui.js?v=<?=@filemtime(FCPATH.'js/cams-ui.js')?>"></script>
<script src="<?=base_url()?>js/cams-tailwind-ui.js?v=<?=@filemtime(FCPATH.'js/cams-tailwind-ui.js')?>"></script>
<script src="<?=base_url()?>js/cams-alpine-modals.js?v=<?=@filemtime(FCPATH.'js/cams-alpine-modals.js')?>"></script>
<script>
  jQuery(function ($) {
    $('.alert-success, .alert-danger, .alert-error').hide();

    <?php if ($runtimeError): ?>
      if (window.CamsUI) CamsUI.notify('error', 'Unable to continue', <?=json_encode($runtimeError)?>);
    <?php elseif ($runtimeSuccess): ?>
      if (window.CamsUI) CamsUI.notify('success', 'Success', <?=json_encode($runtimeSuccess)?>);
    <?php endif; ?>
  });
</script>
