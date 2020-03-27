<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php include "config/koneksi.php"; include "dina_titel.php"; ?></title>
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="index, follow">
<meta name="description" content="<?php include "dina_meta1.php"; ?>">
<meta name="keywords" content="<?php include "dina_meta2.php"; ?>">
<meta http-equiv="Copyright" content="lokomedia">
<meta name="author" content="Lukmanul Hakim">
<meta http-equiv="imagetoolbar" content="no">
<meta name="language" content="Indonesia">
<meta name="revisit-after" content="7">
<meta name="webcrawlers" content="all">
<meta name="rating" content="general">
<meta name="spiders" content="all">

<link rel="shortcut icon" href="favicon.ico" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://localhost/detikcom/rss.xml" />
<link href="style.css" rel="stylesheet" type="text/css" />

</head>

<body>
<div id="wrapper">
  <div id="header">
    <div id="menuutama">
    	<ul>
		<li><a href="index.php"><span>Home</span></a></li>
        
        <?php
		$menu=mysqli_query($conn,"SELECT * FROM modul WHERE publish='Y' ORDER BY urutan");
		while($m=mysqli_fetch_array($menu)){
			echo "<li><a href=$m[link_seo]><span>$m[nama_modul]</span></a></li>";
		}
		?>
		
	</ul>
    </div>
  </div>
  <div id="leftcontent">
    <p>
      <?php include "kiri.php"; ?>
    </p>
  </div>
  <div id="middlecontent">
    <p>
      <?php include "tengah.php"; ?>
    </p>
  </div>
  <div id="rightcontent">
    <p>
      <?php include "kanan.php"; ?>
    </p>
  </div>
  <div id="clearer"></div>
  <div id="footer">Copyright &copy; 2009 by detik.com. All Rights Reserved.</div>
</div>
</body>
</html>
