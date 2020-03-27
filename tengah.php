<?php
// Form Pencarian
echo "<img src=images/pencarian.jpg><br />
      <form method=POST action='hasil-pencarian.html'>    
        <input name=kata type=text size=17 />
        <input type=submit value=Go />
      </form>
      <hr color=#FCEDC7 noshade=noshade />";

// Menu Kategori
echo "<br /><img src='images/mainmenu.jpg'><br /><br />";

$kategori=mysql_query("select nama_kategori, kategori.id_kategori, kategori_seo,  
                       count(berita.id_kategori) as jml 
                       from kategori left join berita 
                       on berita.id_kategori=kategori.id_kategori 
                       group by nama_kategori");
while($k=mysql_fetch_array($kategori)){
  echo "<span class=kategori>&bull; <a href=kategori-$k[id_kategori]-$k[kategori_seo].html> $k[nama_kategori] ($k[jml])</a></span><br />";
}
echo "<br /><hr color=#FCEDC7 noshade=noshade /><br />";

// Berita Teratas
echo "<img src=images/populer.jpg><br /><ul>";
$populer=mysql_query("SELECT * FROM berita ORDER BY dibaca DESC LIMIT 6");
while($p=mysql_fetch_array($populer)){
  echo "<p><li><a href=berita-$p[id_berita]-$p[judul_seo].html>$p[judul]</a> ($p[dibaca])</li></p>";
}
echo "</ul><br /><hr color=#FCEDC7 noshade=noshade /><br />";

// Agenda
echo "<img src=images/agenda.jpg /><br /><br />";
$agenda=mysql_query("SELECT * FROM agenda ORDER BY id_agenda DESC LIMIT 3");

while($a=mysql_fetch_array($agenda)){
	$tgl_agenda = tgl_indo($a[tgl_mulai]);
  echo "<span class=date>&bull; $tgl_agenda </a></span><br />";
  echo "<span class=agenda><a href=agenda-$a[id_agenda]-$a[tema_seo].html> $a[tema]</a></span><br /><br />";
}
echo "<br />";

?>
