# MIV
MIV is the plugin developed in Moodle 3.6. This is the mod plugin which when while creating/editing this course activity need to upload the .vtx image (i.e. 3D image eg. skull.vtk) <br>

Once activity is created opening this activity will load the image

## Installation
1. Go to Site Administrator --> Plugins --> Install medicalimageviewer_mod_plugin_moodle.zip Plugin <br>
2. Drag and Drop the plugin in upload file and install plugin. Click a few next and save button. <br>
3. Go to the course you want to add the activity i.e. project/course or Site Administrator --> Course --> Select course where you want to add the activity. <br>
4.Turn Editing On --> Add an activity --> select miv (i.e. with X icon). Now fill the Name upload the vtx image eg. hello.vtx, skull.vtx or any 3D image you want to view. Few images are there in sample_3D_image directory <br>
5. For this X-masters package is used. For those of you want to want to develop in PHP or any other framework can extract X-masters_PHP zip and run test.php to view it. <br>
6. This uses JavaScript package for 3D Visualization

## Snippets

STEP1: Add the following library for 3D Visualization
```
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<script type="text/javascript" src="http://get.goXTK.com/xtk_edge.js"></script>
<script type="text/javascript" src="x-master/visualization/renderer3D.js"></script>
<!-- <script type="text/javascript" src="demo.js"></script> -->
<link rel="stylesheet" type="text/css" href="demo.css">

```

STEP2:  Add the HTML content and a div which will trigger the JavaScript file
```

<body>
    <?php 
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        list($baselink, $otherlink) = explode('/mod', $actual_link);
        $cmid = $_GET['cmid'];
    ?>
	<div>
		<a href="<?php echo $baselink . '/mod/medicalimageviewer/view.php?id=' . $cmid . '&mode=list' ?>" class="btn btn-primary" style="margin-top: 2%; margin-left: 2%;">Return Back</a>
	</div>
	
	<div id='jsfiddle' style='position:absolute;bottom:10px;left:10px;'><a href='http://jsfiddle.net/gh/get/toolkit/edge/xtk/lessons/tree/master/00/#run' target=_blank><img src='http://xtk.github.com/fiddlelogo.png' border=0></a></div>
</body>

```
STEP3: JavaScript file which will render the image uploaded:

```

<!-- ================= Added Script for parsing 3D Visualization File: Start =============== -->
<script type="text/javascript">
	window.onload = function() {
	  let imgdata = '<?php echo $currenturl ?>';            // Receive the uploaded image

	  // create and initialize a 3D renderer
	  var r = new X.renderer3D();
	  r.init();
	  
	  // create a new X.mesh
	  var skull = new X.mesh();
	  // .. and associate the .vtk file to it
	  // skull.file = 'http://x.babymri.org/?skull.vtk';    // Online Link
	  skull.file = imgdata;                                 // Downloaded Image - upload your VTK file and pass it here
	  //skull.file = 'http://people.sc.fsu.edu/~lb13f/projects/finite_difference/three_js/cavity_test_7.vtk';
	  // .. make it transparent
	  skull.opacity = 0.7;
	  
	  // .. add the mesh
	  r.add(skull);
	  
	  // re-position the camera to face the skull
	  r.camera.position = [0, 400, 0];
	  
	  // animate..
	  r.onRender = function() {

	    // rotate the skull around the Z axis
	    // since we moved the camera, it is Z not X
	    skull.transform.rotateZ(1);
	    
	    // we could also rotate the camera instead which is better in case
	    // we have a lot of objects and want to rotate them all:
	    //
	    // r.camera.rotate([1,0]);
	    
	  };
	  
	  r.render();
	  
	};
</script>
<!-- ================= Added Script for parsing 3D Visualization File: End =============== -->

```

STEP4: (A) Core Changes:

Path -  moodle project/course/modeedit.php
Go to line 150 in modedit.php enter and paste below code: I have also shared the modedit.php page for easier comparison or

Search this line: else if ($fromform = $mform->get_data()) and press enter and paste below code

```
//  ============= Added for handelling image upload: Start ==============
   // receiving the image and updating in db.      
   $idnumberk           = $fromform->mivimage;
   $coursek             = $fromform->course;
   $time                = time();
   $imagekk             = $mform->get_new_filename('mivimage');
  
   // for edit case course module id
   $coursemodulek       = $fromform->coursemodule;
   // For insert case course module id
   if($coursemodulek == 0) {
       $coursemodulefreshSql = $DB->get_record_sql("
           SELECT id FROM {course_modules} ORDER BY 1 DESC LIMIT 1
       ");
      
       // if db has no value set coursemodule to 1, otherwise new course module id will be (last id + 1)
       if(empty($coursemodulefreshSql)) {
           $coursemodulek = 1;
       } else {
           $coursemodulelast = $coursemodulefreshSql->id;
           $coursemodulek = $coursemodulelast + 1;
       }
   }
$DB->execute("INSERT INTO mdl_medicalimageviewer_images(itemid, course, coursemodule, image,  time_created) VALUES($idnumberk, $coursek, $coursemodulek,'$imagekk', $time)");
//  =========== Added for handelling image upload: End ==================

```
## Video
Here I have recorded a video for better understanding.<br>
https://www.loom.com/share/5b173e41e3da4650a06c41411e311fee  <br>
https://www.loom.com/share/8726353100b34f9aabfaa9d239bb782d

## Screenshot
![Screenshot of Moodle Course Page](https://user-images.githubusercontent.com/15896579/56870819-d2429a00-6a32-11e9-9f0d-2eaecd767d66.PNG?raw=true "Screenshot of Moodle Course Page")

![Screenshot of  different MIV activities installed](https://user-images.githubusercontent.com/15896579/56870821-d7074e00-6a32-11e9-8b34-297061d785e4.PNG?raw=true "Screenshot of different MIV activities installed")

![Screenshot of Sample 3D Image](https://user-images.githubusercontent.com/15896579/56870822-db336b80-6a32-11e9-9b0a-ad561f08e742.png?raw=true "Screenshot of Sample 3D Image")

![Screenshot of Sample 3D Image](https://user-images.githubusercontent.com/15896579/56870826-e25a7980-6a32-11e9-9a05-f71411e294c1.PNG?raw=true "Screenshot of Sample 3D Image")




