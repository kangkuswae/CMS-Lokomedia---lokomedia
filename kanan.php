<?php
// RSS
echo "<p align=center><a href=rss.xml target=_blank><img src=images/rssku.jpg border=0 /></a><br />
      <a href=rss.xml target=_blank>Langganan RSS</a></p>
      <hr color=#e0cb91 noshade=noshade /><br />";

// Form indeks berita
echo "<img src=images/indeksberita.jpg /><br /><br />
      <form method=POST action='indeks-berita.html'>";    
      combotgl(1,31,'tanggal',$tgl_skrg);
echo " / ";
      combobln(1,12,'bulan',$bln_sekarang);
echo " / ";
      combothn(2000,$thn_sekarang,'tahun',$thn_sekarang);
echo "<br /><input type=submit value=Go />
      </form>
      <hr color=#e0cb91 noshade=noshade /><br />";

// Polling
echo "<img src='images/polling.jpg' /><br /><br />";
echo "<b>Pilih Browser Favorit Anda?</b> <br /><br />";

echo "<form method=POST action='hasil-poling.html'>";

$poling=mysqli_query($conn,"SELECT * FROM poling WHERE aktif='Y'");
while ($p=mysqli_fetch_array($poling)){
  echo "<input type=radio name=pilihan value='$p[id_poling]' />$p[pilihan]<br />";
}
echo "<p align=center><input type=submit value=Vote /></p>
      </form>
      <p align=center><a href=hasil-poling.html>Hasil Poling</a></p>
      <hr color=#e0cb91 noshade=noshade /><br />";



// Download
echo "<img src='images/download.jpg' /><br /><ul>";
$download=mysqli_query($conn,"SELECT * FROM download 
                    ORDER BY id_download DESC LIMIT 5");
while($d=mysqli_fetch_array($download)){
  echo "<p><li><a href='downlot.php?file=files/$d[nama_file]'>$d[judul]</a></li></p>";
}
echo "</ul><hr color=#e0cb91 noshade=noshade />";


// Banner
$banner=mysqli_query($conn,"SELECT * FROM banner 
                    ORDER BY id_banner DESC LIMIT 4");
while($b=mysqli_fetch_array($banner)){
  echo "<p align=center><a href=$b[url] target='_blank' title='$b[judul]'><img src='foto_banner/$b[gambar]' border=0></a></p>";
}

?>
