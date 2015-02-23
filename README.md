#mymodule
mymodule is a class for handling image uploads in the Anax-MVC framework (see https://github.com/mosbth/Anax-MVC)

#License

This software is free software and carries a MIT license.

#Description and test guide

**mymodule** contains a php class that you can use to upload images within the Anax-MVC framework. Future versions will also allow downloads from secure folders on or above root level using a simple proxy script. In this first version there is no support for downloads and uploaded files are stored in the default folder Anax-MVC/webroot/img (where you will need to create a new writable folder named "upload").

The uploaded file is passed through a try and catch method where it is checked for errors, invalid parameters, file size defined in the upload form (MAX_FILE_SIZE) and also defined in the script itself. The filename length is limited to 100 characters and only JPG, PNG and GIF images are allowed. If the file pass the "valid upload file" check performed by "move_uploaded_file()" it is stored on disk with a secure filename obtained by the php function *sha1_file()*.

If the uploaded file fail to pass any of these checks it will be handled by the Anax-MVC Exception handler and an error message will be shown.

In order to test this package you will first need to download the framework 'Anax-MVC' from Github. In this description I will presume that you use GIT bash on your computer. I will also presume that you have Composer installed and that you can use Composer on the command line.

Start GIT Bash and change the directory so that your command line prompt is present in the working directory you want to use for this download.
After the "$" prompt you may want to type the following line:
> `git clone https://github.com/mosbth/Anax-MVC.git`

After this download you may want to open your text editor (like JEdit or other) and open the file *composer.json* in the folder Anax-MVC. Add these lines to the end of that file, just before the final curly brace:

>           "require": {
        "tommy001/mymodule": "dev-master"
    }
    
Now use your command line interface and change the directory into Anax-MVC. Type "Composer validate" to check out that your composer.json file is valid. If it checks out, you then type "Composer install --no dev" on the command line.

This will download the package **Mymodule** into a folder named "vendor" in Anax-MVC.
    
In the folder Anax-MVC/vendor/Tommy001/mymodule/webroot there is a file named index.php. Copy that file to the folder Anax-MVC/webroot.
Then copy the files from the folder Anax-MVC/vendor/Tommy001/mymodule/view to a new folder named Anax-MVC/app/view/**mymodule**.
And finally copy the files from the folder Anax-MVC/vendor/Tommy001/mymodule/src to a new folder named Anax-MVC/app/src/**Mymodule**.
As mentioned above you will also need to create a writable folder named Anax-MVC/webroot/img/upload.

In order for simple testing of the upload class there is a rudimentary database connection in the Upload class constructor. You also need a MySQL database table to test the script. Create it with the SQL query below using e.g. phpMyAdmin. 

> `DROP TABLE IF EXISTS test_upload;
CREATE TABLE IF NOT EXISTS test_upload (
  id int(11) NOT NULL AUTO_INCREMENT,
  san_filename varchar(100) NOT NULL,
  path varchar(200) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;`

In the constructor of the class Upload you will also need to change the connection details to fit your MySQL database.

When you are done with this setup you can point your web browser to index.php, mentioned above and upload a valid image.

A sanitized version of the original filename is stored in the database, and can e.g. be used to represent the image in a download list. You may wish to add language specific characters and their replacements to the pattern found in line 180 in the class UploadCOntroller.

The relative disk path of the image is stored with a secure filename in the database, like for example "img/upload/6beb8c7b6305b58cb834161d5a4a383b22101b58.jpg".
