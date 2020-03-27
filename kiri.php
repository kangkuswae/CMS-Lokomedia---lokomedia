<script language="javascript">
function validasi(form){
  if (form.nama_komentar.value == ""){
    alert("Anda belum mengisikan Nama.");
    form.nama_komentar.focus();
    return (false);
  }
     
  if (form.isi_komentar.value == ""){
    alert("Anda belum mengisikan komentar.");
    form.isi_komentar.focus();
    return (false);
  }
  return (true);
}
</script>


<?php
// Skrip javascript diatas untuk melakukan validasi data untuk pengisi komentar agar tidak mengosongkan nama dan isi komentar.

include "config/fungsi_indotgl.php";
include "config/class_paging.php";
include "config/fungsi_combobox.php";
include "config/library.php";

// Halaman utama (Home)
if ($_GET[module]=='home'){
  // Tampilkan 5 headline berita terbaru dan hitung jumlah komentar masing-masing berita
  $terkini=mysql_query("select count(komentar.id_komentar) as jml, judul, judul_seo, jam, 
                       berita.id_berita, hari, tanggal, gambar, isi_berita    
                       from berita left join komentar 
                       on berita.id_berita=komentar.id_berita
                       and aktif='Y' 
                       group by berita.id_berita DESC LIMIT 4");

// 	$terkini= mysql_query("SELECT * FROM berita ORDER BY id_berita DESC LIMIT 4");		 
	while($t=mysql_fetch_array($terkini)){
		$tgl = tgl_indo($t[tanggal]);
		echo "<span class=date><img src=images/clock.gif> $t[hari], $tgl - $t[jam] WIB</span><br />";
		echo "<span class=judul><a href=berita-$t[id_berita]-$t[judul_seo].html>$t[judul]</a></span><br />";
 		// Apabila ada gambar dalam berita, tampilkan
    if ($t[gambar]!=''){
			echo "<span class=image><img src='foto_berita/small_$t[gambar]' width=110 border=0></span>";
		}
    // Tampilkan hanya sebagian isi berita
    $isi_berita = htmlentities(strip_tags($t[isi_berita])); // mengabaikan tag html
    $isi = substr($isi_berita,0,220); // ambil sebanyak 220 karakter
    $isi = substr($isi_berita,0,strrpos($isi," ")); // potong per spasi kalimat

    echo "$isi ... <a href=berita-$t[id_berita]-$t[judul_seo].html>Selengkapnya</a> (<b>$t[jml] komentar</b>)
          <br /><hr color=#e0cb91 noshade=noshade />";
	}
  
  // Tampilkan 7 judul berita sebelumnya 
  echo "<img src=images/berita_sebelumnya.jpg><br /><ul>";
  $sebelum=mysql_query("SELECT * FROM berita 
                        ORDER BY id_berita DESC LIMIT 4,7");		 
	while($s=mysql_fetch_array($sebelum)){
	   echo "<li><a href=berita-$s[id_berita]-$s[judul_seo].html>$s[judul]</a></li>";
	}
	echo "</ul>";
}


// Modul detail berita
elseif ($_GET[module]=='detailberita'){
	$detail=mysql_query("SELECT * FROM berita,users,kategori    
                      WHERE users.username=berita.username 
                      AND kategori.id_kategori=berita.id_kategori 
                      AND id_berita='$_GET[id]'");
	$d   = mysql_fetch_array($detail);
	$tgl = tgl_indo($d[tanggal]);
	$baca = $d[dibaca]+1;
	echo "<span class=date>$d[hari], $tgl - $d[jam] WIB</span><br />";
	echo "<span class=judul>$d[judul]</span><br />";
	echo "<span class=posting>Diposting oleh : <b>$d[nama_lengkap]</b><br /> 
        Kategori: <a href=kategori-$d[id_kategori]-$d[kategori_seo].html><b>$d[nama_kategori]</b></a> 
        - Dibaca: <b>$baca</b> kali</span><br /><br />";
  // Apabila ada gambar dalam berita, tampilkan   
 	if ($d[gambar]!=''){
		echo "<span class=image><img src='foto_berita/$d[gambar]' border=0></span>";
	}
	echo "$d[isi_berita] <br />";	 		  
  
  // Tampilkan judul berita yang terkait (maks: 5) 
  echo "<img src=images/berita_terkait.jpg><br /><ul>";
  // pisahkan kata per kalimat lalu hitung jumlah kata
  $pisah_kata  = explode(",",$d[tag]);
  $jml_katakan = (integer)count($pisah_kata);

  $jml_kata = $jml_katakan-1; 
  $ambil_id = substr($_GET[id],0,2);

  // Looping query sebanyak jml_kata
  $cari = "SELECT * FROM berita WHERE (id_berita<'$ambil_id') and (id_berita!='$ambil_id') and (" ;
    for ($i=0; $i<=$jml_kata; $i++){
      $cari .= "tag LIKE '%$pisah_kata[$i]%'";
      if ($i < $jml_kata ){
        $cari .= " OR ";
      }
    }
   $cari .= ") ORDER BY id_berita DESC LIMIT 5";
 
  $hasil  = mysql_query($cari);
  while($h=mysql_fetch_array($hasil)){
  		echo "<li><a href=berita-$h[id_berita]-$h[judul_seo].html>$h[judul]</a></li>";
  }      
	echo "</ul>";

  // Apabila detail berita dilihat, maka tambahkan berapa kali dibacanya
  mysql_query("UPDATE berita SET dibaca=$d[dibaca]+1 
              WHERE id_berita='$_GET[id]'"); 


  // Hitung jumlah komentar
  $idnya=substr("$_GET[id]",0,2);
  $komentar = mysql_query("select count(komentar.id_komentar) as jml from komentar WHERE id_berita='$idnya' AND aktif='Y'");
  $k = mysql_fetch_array($komentar); 
  echo "<span class=judul><b>$k[jml]</b> Komentar : </span><br /><hr color=#e0cb91 noshade=noshade />";

  // Komentar berita
  $sql = mysql_query("SELECT * FROM komentar WHERE id_berita='$idnya' AND aktif='Y'");
	$jml = mysql_num_rows($sql);
  // Apabila sudah ada komentar, tampilkan 
  if ($jml > 0){
    while ($s = mysql_fetch_array($sql)){
      $tanggal = tgl_indo($s[tgl]);
      // Apabila ada link website diisi, tampilkan dalam bentuk link   
 	    if ($s[url]!=''){
        echo "<span class=komentar><a href='http://$s[url]' target='_blank'>$s[nama_komentar]</a></span><br />";  
	    }
	    else{
        echo "<span class=komentar>$s[nama_komentar]</span><br />";  
      }

  		echo "<span class=date>$tanggal - $s[jam_komentar] WIB</span><br /><br />";
      $isian=nl2br($s[isi_komentar]); // membuat paragraf pada isi komentar
	    echo "$isian <hr color=#e0cb91 noshade=noshade />";	 		  
    }
  }
  // Form komentar
  echo "<b>Isi Komentar :</b>
        <table width=100% style='border: 1pt dashed #0000CC;padding: 10px;'>
        <form action=simpankomentar.php method=POST onSubmit=\"return validasi(this)\">
        <input type=hidden name=id_berita value=$idnya>
        <input type=hidden name=id value=$_GET[id]>
        <tr><td>Nama</td><td> : <input type=text name=nama_komentar size=40></td></tr>
        <tr><td>Website</td><td> : <input type=text name=url size=40></td></tr>
        <tr><td valign=top>Komentar</td><td> <textarea name='isi_komentar' style='width: 315px; height: 100px;'></textarea></td></tr>
        <tr><td>&nbsp;</td><td><img src='captcha.php'></td></tr>
        <tr><td>&nbsp;</td><td>(Masukkan 6 kode diatas)<br /><input type=text name=kode size=6><br /></td></tr>
        <tr><td>&nbsp;</td><td><input type=submit name=submit value=Kirim></td></tr>
        </form></table><br />";
}


// Modul berita per kategori
elseif ($_GET[module]=='detailkategori'){
  // Tampilkan nama kategori
  $sq = mysql_query("SELECT nama_kategori from kategori where id_kategori='$_GET[id]'");
  $n = mysql_fetch_array($sq);
  echo "<span class=judul_head>&#187; Kategori : <b>$n[nama_kategori]</b></span><br /><br />";
  
  $p      = new Paging;
  $batas  = 5;
  $posisi = $p->cariPosisi($batas);
  
  // Tampilkan daftar berita sesuai dengan kategori yang dipilih
 	$sql   = "SELECT * FROM berita WHERE id_kategori='$_GET[id]' 
            ORDER BY id_berita DESC LIMIT $posisi,$batas";		 

	$hasil = mysql_query($sql);
	$jumlah = mysql_num_rows($hasil);
	// Apabila ditemukan berita dalam kategori
	if ($jumlah > 0){
   while($r=mysql_fetch_array($hasil)){
		$tgl = tgl_indo($r[tanggal]);
		echo "<span class=date><img src=images/clock.gif> $r[hari], $tgl - $r[jam] WIB</span><br />";
		echo "<span class=judul><a href=berita-$r[id_berita]-$r[judul_seo].html>$r[judul]</a></span><br />";
 		// Apabila ada gambar dalam berita, tampilkan
    if ($r[gambar]!=''){
			echo "<span class=image><img src='foto_berita/small_$r[gambar]' width=110 border=0></span>";
		}
    // Tampilkan hanya sebagian isi berita
    $isi_berita = nl2br($r[isi_berita]); // membuat paragraf pada isi berita
    $isi = substr($isi_berita,0,220); // ambil sebanyak 220 karakter
    $isi = substr($isi_berita,0,strrpos($isi," ")); // potong per spasi kalimat
    echo "$isi ... <a href=berita-$r[id_berita]-$r[judul_seo].html>Selengkapnya</a>
          <br /><hr color=#e0cb91 noshade=noshade />";
	 }
	
  $jmldata     = mysql_num_rows(mysql_query("SELECT * FROM berita WHERE id_kategori='$_GET[id]'"));
  $jmlhalaman  = $p->jumlahHalaman($jmldata, $batas);
  $linkHalaman = $p->navHalaman2($_GET[halaman], $jmlhalaman);

  echo "Hal: $linkHalaman";
  }
  else{
    echo "Belum ada berita pada kategori <b>$_GET[nama_kat]</b>";
  }
}


// Modul detail agenda
elseif ($_GET[module]=='detailagenda'){
	$detail=mysql_query("SELECT * FROM agenda 
                      WHERE id_agenda='$_GET[id]'");
	$d   = mysql_fetch_array($detail);
  $tgl_posting   = tgl_indo($d[tgl_posting]);
  $tgl_mulai   = tgl_indo($d[tgl_mulai]);
  $tgl_selesai = tgl_indo($d[tgl_selesai]);
  $isi_agenda=nl2br($d[isi_agenda]);
	
  echo "<span class=judul>$d[tema]</span><br />";
  echo "<span class=date>Diposting tanggal: $tgl_posting</span><br /><br />";
	echo "<b>Topik</b>  : $isi_agenda <br />";
 	echo "<b>Tanggal</b> : $tgl_mulai s/d $tgl_selesai <br /><br />";
 	echo "<b>Tempat</b> : $d[tempat] <br /><br />";
 	echo "<b>Pengirim (Contact Person)</b> : $d[pengirim] <br />";
}


// Modul hasil pencarian berita 
elseif ($_GET[module]=='hasilcari'){
  echo "<span class=judul_head>&#187; <b>Hasil Pencarian</b></span><br />";
  // menghilangkan spasi di kiri dan kanannya
  $kata = trim($_POST[kata]);

  // pisahkan kata per kalimat lalu hitung jumlah kata
  $pisah_kata = explode(" ",$kata);
  $jml_katakan = (integer)count($pisah_kata);
  $jml_kata = $jml_katakan-1;

  $cari = "SELECT * FROM berita WHERE " ;
    for ($i=0; $i<=$jml_kata; $i++){
      $cari .= "isi_berita LIKE '%$pisah_kata[$i]%'";
      if ($i < $jml_kata ){
        $cari .= " OR ";
      }
    }
  $cari .= " ORDER BY id_berita DESC LIMIT 7";
  $hasil  = mysql_query($cari);
  $ketemu = mysql_num_rows($hasil);

  if ($ketemu > 0){
    echo "<p>Ditemukan <b>$ketemu</b> berita dengan kata <font style='background-color:#00FFFF'><b>$kata</b></font> : </p>"; 
    while($t=mysql_fetch_array($hasil)){
  		echo "<span class=judul><a href=berita-$t[id_berita]-$t[judul_seo].html>$t[judul]</a></span><br />";
      // Tampilkan hanya sebagian isi berita
      $isi_berita = nl2br($t[isi_berita]); // membuat paragraf pada isi berita
      $isi = substr($isi_berita,0,150); // ambil sebanyak 150 karakter
      $isi = substr($isi_berita,0,strrpos($isi," ")); // potong per spasi kalimat

      echo "$isi ... <a href=berita-$t[id_berita]-$t[judul_seo].html>Selengkapnya</a>
            <br /><hr color=#e0cb91 noshade=noshade />";
    }      
  }
  else{
    echo "Tidak ditemukan berita dengan kata <b>$kata</b>";
  }
}


// Modul indeks berita
elseif ($_GET[module]=='indeksberita'){
   echo "<span class=judul_head>&#187; <b>Hasil Indeks Berita</b></span><br />";
   
   $format_mysql = $_POST[tahun].'-'.$_POST[bulan].'-'.$_POST[tanggal]; 
	 $format_indo = tgl_indo($_POST[tahun].'-'.$_POST[bulan].'-'.$_POST[tanggal]);

  // Hanya mengindeks berita, apabila diperlukan bisa ditambahkan utk menngindeks agenda, dll
	$cari   = mysql_query("SELECT * FROM berita WHERE tanggal='$format_mysql'");
	$jumlah = mysql_num_rows($cari);
  // Apabila berita ditemukan sesuai dengan tanggal yang diinginkan 
  if ($jumlah > 0){
    echo "<br />Ditemukan <b>$jumlah</b> berita pada tanggal <b>$format_indo</b> : <ul>";   
    while($r=mysql_fetch_array($cari)){
      echo "<p><li><a href=berita-$r[id_berita]-$r[judul_seo].html>$r[judul]</a></li></p>";
    }      
    echo "</ul>";
  }
  else{
    echo "<br />Tidak ada berita pada tanggal <b>$format_indo</b>";
  }
}


// Modul hasil poling
elseif ($_GET[module]=='hasilpoling'){
   echo "<span class=posting>&#187; <b>Hasil Poling</b></span><br /><br />";

  $u=mysql_query("UPDATE poling SET rating=rating+1 WHERE id_poling='$_POST[pilihan]'");

  echo "<p align=center>Terimakasih atas partisipasi Anda mengikuti poling kami<br /><br />
        Hasil poling saat ini: </p><br />";
  
  echo "<table width=100% style='border: 1pt dashed #0000CC;padding: 10px;'>";

  $jml=mysql_query("SELECT SUM(rating) as jml_vote FROM poling WHERE aktif='Y'");
  $j=mysql_fetch_array($jml);
  
  $jml_vote=$j[jml_vote];
  
  $sql=mysql_query("SELECT * FROM poling WHERE aktif='Y'");
  
  while ($s=mysql_fetch_array($sql)){
  	
  	$prosentase = sprintf("%2.1f",(($s[rating]/$jml_vote)*100));
  	$gbr_vote   = $prosentase * 3;

    echo "<tr><td width=120>$s[pilihan] ($s[rating]) </td><td> 
          <img src=images/blue.png width=$gbr_vote height=18 border=0> $prosentase % 
          </td></tr>";  
  }
  echo "</table>
        <p>Jumlah Voting: <b>$jml_vote</b></p>";
}


// Menu utama di header

// Modul semua berita
elseif ($_GET[module]=='semuaberita'){
  echo "<span class=judul_head>&#187; <b>Berita</b></span><br /><br />"; 
  $p      = new Paging;
  $batas  = 8;
  $posisi = $p->cariPosisi($batas);
  // Tampilkan semua berita
  $sql=mysql_query("select count(komentar.id_komentar) as jml, judul, judul_seo, jam, 
                       berita.id_berita, hari, tanggal, gambar, isi_berita    
                       from berita left join komentar 
                       on berita.id_berita=komentar.id_berita
                       and aktif='Y' 
                       group by berita.id_berita DESC LIMIT $posisi,$batas");


  while($r=mysql_fetch_array($sql)){
		$tgl = tgl_indo($r[tanggal]);
		echo "<span class=date>$r[hari], $tgl - $r[jam] WIB</span><br />";
 		echo "<span class=judul><a href=berita-$r[id_berita]-$r[judul_seo].html>$r[judul]</a></span><br />";
      // Tampilkan hanya sebagian isi berita
      $isi_berita = nl2br($r[isi_berita]); // membuat paragraf pada isi berita
      $isi = substr($isi_berita,0,150); // ambil sebanyak 150 karakter
      $isi = substr($isi_berita,0,strrpos($isi," ")); // potong per spasi kalimat

      echo "$isi ... <a href=berita-$r[id_berita]-$r[judul_seo].html>Selengkapnya</a> (<b>$r[jml] komentar</b>)
            <br /><hr color=#e0cb91 noshade=noshade />";
	 }
	
  $jmldata     = mysql_num_rows(mysql_query("SELECT * FROM berita"));
  $jmlhalaman  = $p->jumlahHalaman($jmldata, $batas);
  $linkHalaman = $p->navHalaman2($_GET[halaman], $jmlhalaman);

  echo "Hal: $linkHalaman <br /><br />";
}


// Modul semua agenda
elseif ($_GET[module]=='semuaagenda'){
  echo "<span class=judul_head>&#187; <b>Agenda</b></span><br /><br />"; 
  $p      = new Paging;
  $batas  = 5;
  $posisi = $p->cariPosisi($batas); 
  // Tampilkan semua agenda
 	$sql = mysql_query("SELECT * FROM agenda  
                      ORDER BY id_agenda DESC LIMIT $posisi,$batas");		 
  while($d=mysql_fetch_array($sql)){
    $tgl_posting = tgl_indo($d[tgl_posting]);
    $tgl_mulai   = tgl_indo($d[tgl_mulai]);
    $tgl_selesai = tgl_indo($d[tgl_selesai]);
    $isi_agenda  = nl2br($d[isi_agenda]);
	
    echo "<span class=date>$tgl_posting</span><br />";
    echo "<span class=judul>$d[tema]</span><br />";
	  echo "<b>Topik</b>  : $isi_agenda ";
 	  echo "<b>Tanggal</b> : $tgl_mulai s/d $tgl_selesai <br />";
 	  echo "<b>Tempat</b> : $d[tempat] <br />";
 	  echo "<b>Pengirim (Contact Person)</b> : $d[pengirim] 
          <br /><hr color=#e0cb91 noshade=noshade />";
	 }
	
  $jmldata     = mysql_num_rows(mysql_query("SELECT * FROM agenda"));
  $jmlhalaman  = $p->jumlahHalaman($jmldata, $batas);
  $linkHalaman = $p->navHalaman2($_GET[halaman], $jmlhalaman);

  echo "Hal: $linkHalaman <br /><br />";
}


// Modul semua download
elseif ($_GET[module]=='semuadownload'){
  echo "<span class=judul_head>&#187; <b>Download</b></span><br />"; 
  $p      = new Paging;
  $batas  = 20;
  $posisi = $p->cariPosisi($batas);
  // Tampilkan semua download
 	$sql = mysql_query("SELECT * FROM download  
                      ORDER BY id_download DESC LIMIT $posisi,$batas");		 

  echo "<ul>";   
   while($d=mysql_fetch_array($sql)){
      echo "<li><a href='downlot.php?file=files/$d[nama_file]'>$d[judul]</a></li>";
	 }
  echo "</ul><hr color=#e0cb91 noshade=noshade />";
	
  $jmldata     = mysql_num_rows(mysql_query("SELECT * FROM download"));
  $jmlhalaman  = $p->jumlahHalaman($jmldata, $batas);
  $linkHalaman = $p->navHalaman2($_GET[halaman], $jmlhalaman);

  echo "Hal: $linkHalaman <br /><br />";
}


// Modul profil
elseif ($_GET[module]=='profilkami'){
  echo "<span class=judul_head>&#187; <b>Profil</b></span><br /><br />"; 

	$profil = mysql_query("SELECT * FROM modul WHERE id_modul='37'");
	$r      = mysql_fetch_array($profil);

  echo "<tr><td class=isi>";
  if ($r[gambar]!=''){
		echo "<span class=image><img src='foto_banner/$r[gambar]'></span>";
	}
	$isi_profil=nl2br($r[static_content]);
  echo "$isi_profil";  
}


// Modul hubungi kami
elseif ($_GET[module]=='hubungikami'){
  echo "<span class=judul_head>&#187; <b>Hubungi Kami</b></span><br /><br />"; 
  echo "<b>Hubungi kami secara online dengan mengisi form dibawah ini:</b>
        <table width=100% style='border: 1pt dashed #0000CC;padding: 10px;'>
        <form action=hubungi-aksi.html method=POST>
        <tr><td>Nama</td><td> : <input type=text name=nama size=40></td></tr>
        <tr><td>Email</td><td> : <input type=text name=email size=40></td></tr>
        <tr><td>Subjek</td><td> : <input type=text name=subjek size=55></td></tr>
        <tr><td valign=top>Pesan</td><td> <textarea name=pesan  style='width: 315px; height: 100px;'></textarea></td></tr>
        </td><td colspan=2><input type=submit name=submit value=Kirim></td></tr>
        </form></table><br />";
}


// Modul hubungi aksi
elseif ($_GET[module]=='hubungiaksi'){
  mysql_query("INSERT INTO hubungi(nama,
                                   email,
                                   subjek,
                                   pesan,
                                   tanggal) 
                        VALUES('$_POST[nama]',
                               '$_POST[email]',
                               '$_POST[subjek]',
                               '$_POST[pesan]',
                               '$tgl_sekarang')");
  echo "<span class=posting>&#187; <b>Hubungi Kami</b></span><br /><br />"; 
  echo "<p align=center><b>Terimakasih telah menghubungi kami. <br /> Kami akan segera meresponnya.</b></p>";
}
?>
