function confirmDelete() {
	return confirm(adminpraise.confirmDelete); 
}

Joomla.submitbutton = function(pressbutton) {
	if (pressbutton) {
		document.adminForm.task.value=pressbutton;
	}
	if(pressbutton == 'reset') {
		if (!confirm(adminpraise.resetToDefaultWarning)) {
			return false;
		};
	}
	document.adminForm.submit();
}
		
