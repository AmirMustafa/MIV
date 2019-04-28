
<!-- 
/*
 * 
 *                  xxxxxxx      xxxxxxx
 *                   x:::::x    x:::::x 
 *                    x:::::x  x:::::x  
 *                     x:::::xx:::::x   
 *                      x::::::::::x    
 *                       x::::::::x     
 *                       x::::::::x     
 *                      x::::::::::x    
 *                     x:::::xx:::::x   
 *                    x:::::x  x:::::x  
 *                   x:::::x    x:::::x 
 *              THE xxxxxxx      xxxxxxx TOOLKIT
 *                    
 *                  http://www.goXTK.com
 *                   
 * Copyright (c) 2012 The X Toolkit Developers <dev@goXTK.com>
 *                   
 *    The X Toolkit (XTK) is licensed under the MIT License:
 *      http://www.opensource.org/licenses/mit-license.php
 *
 * LESSON 00 - Hello cube!
 */
-->

<html>
<head>
<title>XTK LESSON 00</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<script type="text/javascript" src="http://get.goXTK.com/xtk_edge.js"></script>
<script type="text/javascript" src="x-master/visualization/renderer3D.js"></script>
<!-- <script type="text/javascript" src="demo.js"></script> -->
<link rel="stylesheet" type="text/css" href="demo.css">
</head>

<?php 
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	list($baselink, $otherlink) = explode('/mod', $actual_link);
	$cmid = $_GET['cmid'];
?>

<body>
	<div>
		<a href="<?php echo $baselink . '/mod/medicalimageviewer/view.php?id=' . $cmid . '&mode=list' ?>" class="btn btn-primary" style="margin-top: 2%; margin-left: 2%;">Return Back</a>
	</div>
	
	<div id='jsfiddle' style='position:absolute;bottom:10px;left:10px;'><a href='http://jsfiddle.net/gh/get/toolkit/edge/xtk/lessons/tree/master/00/#run' target=_blank><img src='http://xtk.github.com/fiddlelogo.png' border=0></a></div>
</body>
</html>

 <!-- ================= Added Script for parsing 3D Visualization File: Start =============== -->
<script type="text/javascript">
	window.onload = function() {
	  let imgdata = '<?php echo $currenturl ?>';

	  // create and initialize a 3D renderer
	  var r = new X.renderer3D();
	  r.init();
	  
	  // create a new X.mesh
	  var skull = new X.mesh();
	  // .. and associate the .vtk file to it
	  // skull.file = 'http://x.babymri.org/?skull.vtk';
	  skull.file = imgdata;
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