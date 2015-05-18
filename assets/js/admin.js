//REF:
//
// https://github.com/weixiyen/jquery-filedrop

function init_droparea()
{


$('.uploadarea').filedrop({
	//fallback_id: 'upload_button',    // an identifier of a standard file input element
	url: '/admin/upload',              // upload handler, handles each file separately
	paramname: 'uploadfile',          // POST parameter name used on serverside to reference file
	data: {

	},
	/*headers: {          // Send additional request headers
		'header': 'value'
	},*/
	error: function(err, file) {
		switch(err) {
			case 'BrowserNotSupported':
				alert('browser does not support html5 drag and drop');
				break;
			case 'TooManyFiles':
				alert('too many files, max '+this.maxfiles);
				break;
			case 'FileTooLarge':
				// program encountered a file whose size is greater than 'maxfilesize'
				alert('file too large, max '+this.maxfilesize+' MB');
				break;
			default:
				break;
		}
	},
	maxfiles: 10,
	maxfilesize: 5,    // max file size in MBs
	drop: function() {

	},
	uploadStarted: function(i, file, len){
		return true;
	},
	uploadFinished: function(i, file, response, time) {
		var u = $('.uploadarea');

		if(response['ok'] == true)
		{
			if ( response['thumb'] == 0 )
			{
				u.val(u.val()+"\n"+'![](/assets/blog/'+response.filename+')'+"\n");
			}
			else
			{
				u.val(u.val()+"\n"+'[![](/assets/blog/thumbs/'+response.filename+')](/assets/blog/'+response.filename+')'+"\n");
			}
		}
		else{
			alert(response['error']);
		}
	},
	afterAll: function() {
		$('.loading_corner').remove();
	},
	beforeEach: function(file) {
		if(file.type == "image/png" || file.type == "image/jpeg" || file.type == "image/gif" )
		{
			$('.loading_corner').remove();
			$('.admin-editor').append("<div class='loading_corner'></div>");
			return true;
		}
		else
		{
			alert('could not upload '+file.name+' - invalid file type: '+file.type);
			return false;
		}
	}
});
}


