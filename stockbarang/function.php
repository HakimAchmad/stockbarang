<?php
session_start();

//Membuat Koneksi ke database
$conn = mysqli_connect("localhost","root","","stockbarang");


//Menambah Barang Baru
if(isset($_POST['addnewbarang'])){
   $namabarang = $_POST['namabarang'];
   $kategori = $_POST['kategori'];
   $stock = $_POST['stock'];
 
   //tambah gambar
      $allowed_extension = array('png','jpg');
      $nama = $_FILES['file']['name']; //ngambil nama gambar
      $dot = explode('.',$nama);
      $ektensi = strtolower(end($dot)); //mengambil extensi
      $ukuran = $_FILES['file']['size'];  //mengambil size file
      $file_tmp = $_FILES['file']['tmp_name']; //mengambil lokasi file

      //penamaan file -> enkripsi
      $image = md5(uniqid($nama,true) . time()).'.'.$ektensi; //menggabungkan nama file yg dienkripsi dengan ektensi

      //validasi udh ada atau belum
      $cek = mysqli_query($conn,"select * from stock where namabarang='$namabarang'");
      $hitung = mysqli_num_rows($cek);

      if($hitung<1){
         //jika belum ada
  
         //proses upload gambar
         if(in_array($ektensi, $allowed_extension) === true){
            //validasi ukuran file nya
            if($ukuran < 15000000){
               move_uploaded_file($file_tmp, 'images/'.$image);
               
               $addtotable = mysqli_query($conn,"insert into stock (namabarang, kategori, stock, image) values('$namabarang','$kategori','$stock','$image')");
               if($addtotable){
                   header('location:index.php');
              } else {
                  echo 'gagal';
                  header('location:index.php');
              }
            } else {
               //kalau file nya lebih dari 15 mb
               echo '
               <script>
                  alert("Ukuran File Terlalu Besar ");
                  window.location.href="index.php";
               </script>
               ';
            }
         } else {
            //kalau file nya tidak png / jpg
            echo '
            <script>
               alert("File Harus png/jpg ");
               window.location.href="index.php";
            </script>
            ';
         }
         
      } else { 
         //jika sudah ada
         echo '
         <script>
            alert("Nama Barang Sudah Terdaftar ");
            window.location.href="index.php";
         </script>
         ';
      }      
  
 };

 
//Menambah Barang Masuk
if(isset($_POST['barangmasuk'])){
    $barangnya = $_POST['barangnya'];
    $pengirim = $_POST['pengirim'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang+$qty;

    $addtomasuk = mysqli_query($conn,"insert into masuk (idbarang, pengirim, qty) values('$barangnya','$pengirim','$qty')");
    $updatestockmasuk = mysqli_query($conn,"update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
   if($addtomasuk&&$updatestockmasuk){
        header('location:masuk.php');
   } else {
       echo 'gagal';
       header('location:masuk.php');
   }
 }


//Menambah Barang Keluar
if (isset($_POST['addbarangkeluar'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
      
   if($stocksekarang >= $qty){
       //kalau stock barang tersedia
      $tambahkanstocksekarangdenganquantity = $stocksekarang-$qty;

      $addtokeluar = mysqli_query($conn,"insert into keluar (idbarang, penerima, qty) values('$barangnya','$penerima','$qty')");
      $updatestockmasuk = mysqli_query($conn,"update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
      if($addtokeluar&&$updatestockmasuk){
         header('location:keluar.php');
      } else {
         echo 'gagal';
         header('location:keluar.php');
      }
   } else {
      //kalau stock barang kurang
      echo '
      <script>
         alert("Stock saat ini tidak mencukupi");
         window.location.href="keluar.php";
       </script>
      ';
   }
}



//Update Info Barang
if(isset($_POST['updatebarang'])){
   $idb = $_POST['idb'];
   $namabarang = $_POST['namabarang'];
   $kategori = $_POST['kategori'];

   //tambah gambar
   $allowed_extension = array('png','jpg');
   $nama = $_FILES['file']['name']; //ngambil nama gambar
   $dot = explode('.',$nama);
   $ektensi = strtolower(end($dot)); //mengambil extensi
   $ukuran = $_FILES['file']['size'];  //mengambil size file
   $file_tmp = $_FILES['file']['tmp_name']; //mengambil lokasi file

   //penamaan file -> enkripsi
   $image = md5(uniqid($nama,true) . time()).'.'.$ektensi; //menggabungkan nama file yg dienkripsi dengan ektensi

   if($ukuran==0){
      //jika tidak ingin mengupload
      $update = mysqli_query($conn,"update stock set namabarang='$namabarang', kategori='$kategori' where idbarang ='$idb'");
      if($update){
         header('location:index.php');
      } else {
         echo 'Gagal';
         header('location:index.php');
      }
   } else {
      //jika ingin mengupload
      move_uploaded_file($file_tmp, 'images/'.$image);
      $update = mysqli_query($conn,"update stock set namabarang='$namabarang', kategori='$kategori', image='$image' where idbarang ='$idb'");
      if($update){
         header('location:index.php');
      } else {
         echo 'Gagal';
         header('location:index.php');
      }
   }
}


//Menghapus Barang Dari Stock
if(isset($_POST['hapusbarang'])){
   $idb = $_POST['idb']; //id barang

   $gambar = mysqli_query($conn, "select * from stock where idbarang='$idb'");
   $get = mysqli_fetch_array($gambar);
   $img = 'images/'.$get['image'];
   unlink($img);

   $hapus = mysqli_query($conn,"delete from stock where idbarang ='$idb'");
   if($hapus){
	   header('location:index.php');
   } else {
      echo 'Gagal';
      header('location:index.php');
   }
};



//Mengubah Data barang Masuk
if(isset($_POST['updatebarangmasuk'])){
   $idb = $_POST['idb'];
   $idm = $_POST['idm'];
   $pengirim = $_POST['pengirim'];
   $qty = $_POST['qty'];

   $lihatstock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
   $stocknya = mysqli_fetch_array($lihatstock);
   $stockskrg = $stocknya['stock'];

   $qtyskrg = mysqli_query($conn,"select * from masuk where idmasuk='$idm'");
   $qtynya = mysqli_fetch_array($qtyskrg);
   $qtyskrg = $qtynya['qty'];

   if($qty>$qtyskrg){
      $selisih = $qty-$qtyskrg;
      $kurangin = $stockskrg + $selisih;
      $kuranginstocknya = mysqli_query($conn,"update stock set stock='$kurangin' where idbarang='$idb'");
      $updatenya = mysqli_query($conn,"update masuk set qty='$qty', pengirim='$pengirim' where idmasuk='$idm'");
         if($kuranginstocknya&&$updatenya){
            header('location:masuk.php');
            } else {
               echo 'Gagal';
               header('location:masuk.php');
         }
   } else { 
      $selisih = $qtyskrg-$qty;
      $kurangin = $stockskrg - $selisih;
      $kuranginstocknya = mysqli_query($conn,"update stock set stock='$kurangin' where idbarang='$idb'");
      $updatenya = mysqli_query($conn,"update masuk set qty='$qty', pengirim='$pengirim' where idmasuk='$idm'");
         if($kuranginstocknya&&$updatenya){
            header('location:masuk.php');
            } else {
               echo 'Gagal';
               header('location:masuk.php'); 
	      }
   }
}




//Menghapus Barang Masuk
if(isset($_POST['hapusbarangmasuk'])){
   $idb = $_POST['idb'];
   $qty = $_POST['qty'];
   $idm = $_POST['idm'];

   $getdatastock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
   $data = mysqli_fetch_array($getdatastock);
   $stok = $data['stock'];

   $selisih = $stok-$qty;

   $update = mysqli_query($conn,"update stock set stock='$selisih' where idbarang='$idb'");
   $hapusdata = mysqli_query($conn,"delete from masuk where idmasuk='$idm'");

   if($update&&$hapusdata){
	   header('location:masuk.php');
   } else {
	   header('location:masuk.php');
   }

}



//Mengubah Data Barang Keluar
if(isset($_POST['updatebarangkeluar'])){
   $idb = $_POST['idb'];
   $idk = $_POST['idk'];
   $penerima = $_POST['penerima'];
   $qty = $_POST['qty']; //Qty terbaru inputan user

   //Mengambil stock barang saat ini
   $lihatstock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
   $stocknya = mysqli_fetch_array($lihatstock);
   $stockskrg = $stocknya['stock'];

   //Qty barang keluar saat ini
   $qtyskrg = mysqli_query($conn,"select * from keluar where idkeluar='$idk'");
   $qtynya = mysqli_fetch_array($qtyskrg);
   $qtyskrg = $qtynya['qty'];

   if($qty>$qtyskrg){
      $selisih = $qty-$qtyskrg;
      $kurangin = $stockskrg - $selisih;

      if($selisih <= $stockskrg){
         $kuranginstocknya = mysqli_query($conn,"update stock set stock='$kurangin' where idbarang='$idb'");
         $updatenya = mysqli_query($conn,"update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
         if($kuranginstocknya&&$updatenya){
            header('location:keluar.php');
            } else {
               echo 'Gagal';
               header('location:keluar.php');
      }
      } else {
         echo '
         <script>alert("Stock Tidak Mencukupi");
         window.location.href="keluar.php"
         </script>
         ';
      }

      
   } else { 
      $selisih = $qtyskrg-$qty;
      $kurangin = $stockskrg + $selisih;
      $kuranginstocknya = mysqli_query($conn,"update stock set stock='$kurangin' where idbarang='$idb'");
      $updatenya = mysqli_query($conn,"update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
         if($kuranginstocknya&&$updatenya){
            header('location:keluar.php');
            } else {
               echo 'Gagal';
               header('location:keluar.php'); 
	      }
   }
}




//Menghapus Barang Keluar
if(isset($_POST['hapusbarangkeluar'])){
   $idb = $_POST['idb'];
   $qty = $_POST['qty'];
   $idk = $_POST['idk'];

   $getdatastock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
   $data = mysqli_fetch_array($getdatastock);
   $stok = $data['stock'];

   $selisih = $stok+$qty;

   $update = mysqli_query($conn,"update stock set stock='$selisih' where idbarang='$idb'");
   $hapusdata = mysqli_query($conn,"delete from keluar where idkeluar='$idk'");

   if($update&&$hapusdata){
	   header('location:keluar.php');
   } else {
	   header('location:keluar.php');
   }

}

 

 

?>