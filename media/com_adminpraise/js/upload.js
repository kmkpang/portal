function startUpload(){
	if(!checkTypeFile($(prefix+'adminpraise-image').value)){
		$(prefix+'error-myfile').innerHTML = '<span class="err" style="color:red">Support png, jpeg, gif, ico type only.</span>';
		return false;
	}	
	var form = document.adminForm;

	form.setAttribute( "autocomplete","off" );
	$(form).setProperty('encoding' , 'multipart/form-data');
	$(form).setProperty('enctype' , 'multipart/form-data');
	$(form).task.set('value', 'upload');
	adminpraiseImage = $(prefix+'adminpraise-image').value;
	form.action = adminpraise.root+"administrator/index.php?option=com_adminpraise&view=settings&prefix="+prefix+"&format=raw";		
	form.target = prefix+"upload-target";
	
	$(prefix+'upload-process').style.display='block';
	form.submit();
}
function stopUpload(message){
	document.adminForm.target = '_self';
	$(prefix+'upload-process').setStyle('display', 'none');
	$(prefix+'error-myfile').innerHTML = '';
	$(prefix+'adminpraise-image').value = '';
	document.adminForm.set('value', '');
	
	$(prefix+'error-myfile').innerHTML = message;

	updatePath();
	updateImage();
}
function errorUpload(text){
	$(prefix+'error-myfile').innerHTML = text;
	$(prefix+'upload-process').setStyle('display', 'none');
	$(prefix+'adminpraise-image').value = '';
}

function checkTypeFile(value){		
	var pos = value.lastIndexOf('.');
	var type = value.substr(pos+1, value.length).toLowerCase();
	if((type!='png') && (type!='jpeg') && (type!='gif') && (type!='ico')){					
		return false;
	}	
	return true;
}

function updatePath() {
	$('params'+prefix).set('value', '/media/com_adminpraise/images/adminpraise3/'+adminpraiseImage);
	$('params'+prefix).setStyle('background', 'red');
}

function updateImage() {
	var src = adminpraise.root+'/media/com_adminpraise/images/adminpraise3/'+adminpraiseImage;
	$(prefix+'-image').set('src', src);
}