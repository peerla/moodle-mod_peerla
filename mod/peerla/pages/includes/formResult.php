<?php
if (count($errors) > 0){
?>
<div class="alert alert-danger" role="alert">
<?php
	$messageFound = false;
	foreach($errors as $error){
		if (is_string($error)){
			$messageFound = true;
?>
	<div class="errorBoxMsg">
		<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
		<?=$error?>
	</div>
<?php
		}
	}
		
	if (!$messageFound){
?>
	<div class="errorBoxMsg">
		<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
		<?=get_string('error_default_error_found_msg','peerla')?>
	</div>
<?php
	}
?>
</div>
<?php
}
