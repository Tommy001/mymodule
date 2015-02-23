<br><br>
<h2><a>Upload test page</a></h2>

<form method='post' enctype='multipart/form-data'>
    <input type="hidden" name="MAX_FILE_SIZE" value="2000000" /> 
    <input type='file' name='img' />
    <input type='submit' value='Upload image' onClick="this.form.action = '<?=$this->url->create('upload/entry/')?>'"/>    
</form>


