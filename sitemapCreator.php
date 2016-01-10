
<?php
function getTitle($Url){
$str = file_get_contents($Url);
if(strlen($str)>0){
    preg_match("/\<title\>(.*)\<\/title\>/",$str,$title);
    return $title[1];
}
}
// Timothy Sitemap Creator - Copyright 2013 - All Rights Reserved.
// Visit www.timothyseo.net for more great SEO Tools and Resources

// Please edit these values before running your script.
//////////////////////////////////// Options ////////////////////////////////////
$url = "http://www.mecaautoupholstery.com/"; //The Url of the site - the last '/' is needed

$root_dir = '.'; //Where the root of the site is with relation to this file.

$file_mask = array();
$file_mask[] = '*.html'; //Or *.html or whatever - Any pattern that can be used in the glob() php function can be used here.
$file_mask[] = "*.htm";


//The file to which the result is written to - must be writable. The file name is relative from root.
$xml_sitemap_file = 'sitemap.xml'; 

$html_sitemap_file = "sitemap.html";

$txt_sitemap_file = "urllist.txt";

// Stuff to be ignored...
//Ignore the file/folder if these words appear in the name
$always_ignore = array();

//These files will not be linked in the sitemap.
$ignore_files = array();

//The script will not enter these folders
$ignore_folders = array();

//The default priority for all pages - the priority of all pages will increase/decrease with respect to this.
$starting_priority = ($_REQUEST['starting_priority']) ? $_REQUEST['starting_priority'] : 70;
$changefrequency = ($_REQUEST['changefreq']) ? $_REQUEST['changefreq'] : "";
/////////////////////////// Stop editing now - Configurations are over ////////////////////////////


///////////////////////////////////////////////////////////////////////////////////////////////////
function generateSiteMap() {
	global $url, $file_mask, $root_dir, $xml_sitemap_file, $html_sitemap_file, $txt_sitemap_file, $starting_priority, $changefrequency;
	global $always_ignore, $ignore_files, $ignore_folders;
	global $total_file_count,$average, $lowest_priority_page, $lowest_priority;

	/////////////////////////////////////// Code ////////////////////////////////////
	chdir($root_dir);
	$all_pages = getFiles('');
	
	$xml_string = '<?xml version="1.0" encoding="UTF-8"?>
<urlset
  xmlns="http://www.google.com/schemas/sitemap/0.84"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84
                      http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">

';

$html_string = '
<!DOCTYPE HTML>
<html>
<head>
     <title>HTML Sitemap</title>
     <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
</head>
<body>
     <div>
          <h1>HTML Sitemap</h1>
          <h3>Last updated: '.date('Y-m-d').'</h3>
          <ul>
';

$txt_string = '';
	
	$modified_priority = array();
	for ($i=30;$i>0;$i--) array_push($modified_priority,$i);
	
	$lowest_priority = 100;
	$lowest_priority_page = "";
	//Process the files
	foreach ($all_pages as $link) {
		//Find the modified time.
		$handle = fopen($link,'r');
		$info = fstat($handle);
		fclose($handle);
		$modified_at = date('Y-m-d\Th:i:s\Z',$info['mtime']);
		$modified_before = ceil((time() - $info['mtime']) / (60 * 60 * 24));
	
		$priority = $starting_priority; //Starting priority
		
		//If the file was modified recently, increase the importance
		if($modified_before < 30) {
			$priority += $modified_priority[$modified_before];
		}
		
		if(preg_match('/index\.\w{3,4}$/',$link)) {
			$link = preg_replace('/index\.\w{3,4}$/',"",$link);
			$priority += 20;
		}
		
		//These priority detectors should be different for different sites :TODO:
		if(strpos($link,'example')) $priority -= 30; //If the page is an example page
		elseif(strpos($link,'demo')) $priority -= 30;
		if(strpos($link,'tuorial')) $priority += 10;
		if(strpos($link,'script')) $priority += 5;
		if(strpos($link,'other') !== false) $priority -= 20;
	
		//Priority based on depth
		$depth = substr_count($link,'/');
		if($depth < 2) $priority += 10; // Yes, I know this is flawed.
		if($depth > 2) $priority += $depth * 5;	// But the results are better.
		
		if($priority > 100) $priority = 100;
		$loc = $url . $link;
		if(substr($loc,-1,1) == '/') $loc = substr($loc,0,-1);//Remove the last '/' char.
		
	
		$total_priority += $priority;
		if($lowest_priority > $priority) {
			$lowest_priority = $priority;//Find the file with the lowest priority.	
			$lowest_priority_page = $loc;
		}

		$priority = $priority / 100; //The priority is given in decimals

		$xml_string .= " <url>
  <loc>$loc</loc>
  <lastmod>$modified_at</lastmod>
  <priority>$priority</priority>
  <changefreq>$changefrequency</changefreq>
 </url>\n";
 
 
 $txt_string .= $loc . "\r\n";
 
$title = getTitle($loc);
 
 $html_string .= '<li><a href="'.$loc.'">'.$title.'</a></li>
 ';
 
	}
	
	$xml_string .= "</urlset>";
	$html_string .= '</ul>     
     </div>
</body>
</html>';
	
	
	if(!$xml_hndl = fopen($xml_sitemap_file,'w')) {
		//header("Content-type:text/plain");
		print "Can't open xml sitemap file - '$xml_sitemap_file'.<br />Dumping result to screen...<br /><br />";
		print '<textarea rows="25" cols="70" style="width:100%">'.$xml_string.'</textarea>';
	} else {
		print '<p>Sitemap was written to <a href="' . $url.$xml_sitemap_file .'">'. $url.$xml_sitemap_file .'></a></p>';

		fputs($xml_hndl,$xml_string);
		fclose($xml_hndl);
	}
	
	
	
	if(!$txt_hndl = fopen($txt_sitemap_file,'w')) {
		//header("Content-type:text/plain");
		print "Can't open txt sitemap file - '$txt_sitemap_file'.<br />Dumping result to screen...<br /><br />";
		print '<textarea rows="25" cols="70" style="width:100%">'.$txt_string.'</textarea>';
	} else {
		print '<p>Sitemap was written to <a href="' . $url.$txt_sitemap_file .'">'. $url.$txt_sitemap_file .'></a></p>';

		fputs($txt_hndl,$txt_string);
		fclose($txt_hndl);
	}
	
	
	
	
	if(!$html_hndl = fopen($html_sitemap_file,'w')) {
		//header("Content-type:text/plain");
		print "Can't open html sitemap file - '$html_sitemap_file'.<br />Dumping result to screen...<br /><br />";
		print '<textarea rows="25" cols="70" style="width:100%">'.$html_string.'</textarea>';
	} else {
		print '<p>Sitemap was written to <a href="' . $url.$html_sitemap_file .'">'. $url.$html_sitemap_file .'></a></p>';

		fputs($html_hndl,$html_string);
		fclose($html_hndl);
	}
	
	
	$total_file_count = count($all_pages);
	$average = round(($total_priority/$total_file_count),2);
}

///////////////////////////////////////// Functions /////////////////////////////////
// File finding function.
function getFiles($cd) {
	$links = array();
	$directory = ($cd) ? $cd . '/' : '';//Add the slash only if we are in a valid folder

	//$files = glob($directory . $GLOBALS['file_mask']);
	$files = array();
	
	foreach($GLOBALS['file_mask'] as $globmask) {
	$file_search = glob($directory . $globmask);
	foreach($file_search as $indifile) {
	$files[] = $indifile;
	}
	}
	
	foreach($files as $link) {
		//Use this only if it is NOT on our ignore lists
		if(in_array($link,$GLOBALS['ignore_files'])) continue; 
		if(in_array(basename($link),$GLOBALS['always_ignore'])) continue;
		
		$filecontents = file_get_contents($link);
		
		if(strpos($filecontents, '<META name="robots" content="NOINDEX, NOFOLLOW, NOYDIR, NOODP, NOARCHIVE" />') !== false) continue;
		if(strpos($filecontents, '<META name="googlebot" content="NOARCHIVE, NOODP, NOSNIPPET" />') !== false) continue;
		if(strpos($filecontents, '<META name="slurp" content="NOARCHIVE, NOYDIR, NOSNIPPET" />') !== false) continue;
		
		
		array_push($links, $link);
	}
	//asort($links);//Sort 'em - to get the index at top.

	//Get All folders.	
	$folders = glob($directory . '*',GLOB_ONLYDIR);//GLOB_ONLYDIR not avalilabe on windows.
	foreach($folders as $dir) {
		//Use this only if it is NOT on our ignore lists
		$name = basename($dir);
		if(in_array($name,$GLOBALS['always_ignore'])) continue;
		if(in_array($dir,$GLOBALS['ignore_folders'])) continue; 
		
		$more_pages = getFiles($dir); // :RECURSION: 
		if(count($more_pages)) $links = array_merge($links,$more_pages);//We need all thing in 1 single dimentional array.
	}
	
	return $links;
}

//////////////////////////////// Display /////////////////////////////


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.1 Transitional//EN">
<html>
<head>
<title>Sitemap Creator</title>
<style type="text/css">
a {color:blue;text-decoration:none;}
a:hover {color:red;}
</style>
</head>
<body>
<h1>Sitemap Creator Script</h1>

<?php
if($_POST['action'] == 'Generate Sitemap') {
	generateSiteMap();
?>
<h2>Sitemap Created...</h2>

<h2>Statistics</h2>

<p><strong><?php echo $total_file_count; ?></strong> files were found and indexed.<br />
Lowest priority of <strong><?php echo $lowest_priority; ?></strong> was 
given to <a href='<?php echo $lowest_priority_page; ?>'><?php echo $lowest_priority_page; ?></a></p>

Average Priority : <strong><?php echo $average; ?></strong><br />

<h2>Redo</h2>
<?php } else { ?>

<p>You can use this script to create the sitemap for your site automatically. The script will recursively visit all files on your site and create a sitemap XML file in the format needed by Google. </p>

<p>You can customize the result by changing the starting priorities.</p>
 
<h2>Set Starting Priority</h2>
<?php } ?>

<form action="sitemapCreator.php" method="post">
Starting Priority : <input type="text" name="starting_priority" size="3" value="<?php echo $starting_priority; ?>" /><br />
Change Frequency : <select name="changefreq"><option value="daily" <?php if(isset($_POST['changefreq']) && $_POST['changefreq'] == "daily") { echo "SELECTED"; } ?>>Daily</option><option value="weekly" <?php if(isset($_POST['changefreq']) && $_POST['changefreq'] == "weekly") { echo "SELECTED"; } ?>>Weekly</option><option value="monthly" <?php if(isset($_POST['changefreq']) && $_POST['changefreq'] == "monthly") { echo "SELECTED"; } ?>>Monthly</option></select>
<br /><input type="submit" name="action" value="Generate Sitemap" />
</form>

</body>
</html>