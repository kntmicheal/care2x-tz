<script  language="javascript">
<!-- 

<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

function popRecordHistory(table,pid) {
	urlholder="./record_history.php<?php echo URL_REDIRECT_APPEND; ?>&table="+table+"&pid="+pid;
	HISTWIN<?php echo $sid ?>=window.open(urlholder,"histwin<?php echo $sid ?>","menubar=no,width=400,height=550,resizable=yes,scrollbars=yes");
}


function popDrugSheet(table,pid) {
	urlholder="./nursechart.php<?php echo URL_REDIRECT_APPEND; ?>&table="+table+"&pid="+pid;
	window.open(urlholder);
}

function show_patient_history(table,pid) {
	urlholder="./show_history.php<?php echo URL_REDIRECT_APPEND; ?>&table="+table+"&pid="+pid;
	window.open(urlholder);
}
-->
</script>
