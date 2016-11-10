#Documentation
==============
Ini adalah file readme sekaligus dokumentasi pengembangan dan penggunakan framework.

#About This Framework

Framework ini dikembangkan dengan gampang-simpel-cepat-aman. *Gampang* berarti mudah untuk menggunakannya, sehingga mempercepat proses develop sebuah aplikasi web. *Simpel* berarti tidak pakai ribet, file-file khusus back-end tidak banyak sehingga deploy aplikasi juga tidak ribet. Ini berarti juga framework mudah dipelajari sehingga untuk mempelajari dan memahaminya tidak susah-susah amat. *Cepat* berarti bahwa aplikasi ini dapat berjalan dengan cepat dan tentu saja tidak boros memori. *Aman* berarti aplikasi yang dikembangkan dengan framework ini aman dari serangan hack.

Penginnya sih seperti itu... Hehe...

#Features

Berikut adalah fitur-fitur yang dikembangkan untuk framework minimalis ini.

##Sudah
- MVC architecture: sudah menggunakan 
- Database: Pilihan database dengan mysql, mariadb
- Database CRUD
- SQL builder: where, select all, limit, order
- menggunakan PDO
- Helper HTML: link, table, grid, form
- view bisa di dalam folder
- debugging, dengan trait Util.php

##Future features
- pagination
- SQL builder: relasi pada database
- nodb options: Opsi tanpa database, bisa dengan markdown atau plain-text biasa
- internet connection options: jika ada internet, menggunakan aset CDN. Jika tidak, menggunakan aset lokal
- generator: generator model, controller, dan view
- user management: login, register, lupa password, profile user
- session: manajemen session
- flash: notifikasi
- markdown support: merender file markdown
- security token untuk mencegah XSS attack
- middleware
- cache

#Getting Started

First thing you must do is setting properly file `index.php` in folder `html`. Folder `html` tersebut yang nantinya menjadi root aplikasi Anda.

##Instalasi

Untuk menginstalnya, silakan salin semua file framework aplikasi. Berikut adalah struktur file pada aplikasi:

|-- M/              => berisi model
|-- V/              => berisi view
|-- C/              => berisi controller
|-- fw/             => berisi kelas-kelas utama framework
|-- html/           => aplikasi root yang akan diakses oleh pengguna
|-- serve.sh        => file untuk menjalankan built-in server php
|-- readthis.md     => file dokumentasi
|-- config.php      => file konfigurasi
|-- autoload.php    => file autoload kelas-kelas

Selanjutnya, jika Anda menggunakan sistem operasi Linux, dapat menjalankan file `serve` untuk menggunakan php built-in server.

#Controller

#View
Tidak ada kelas khusus untuk menghandel view. View di sini merupakan kepanjangan tangan dari Controller. Variabel `$this` pada file-file view merujuk pada controller yang sedang aktif.

#Model
Model is a class which is used to work with database. Here are the functionalities of the model:

##Create Model

Create a file with name same with class name. Example:

```
Class Project extends Modeldb
{
    protected $table = 'training';
}
```

The variable `$table` above is needed when the table of model is not the same with the name of model class.

##Get all records

```
$data = (new Project)->all();
```

The returned output is array.

##Get record by primary key

```
$data = (new Books)->find($id);
```
returned output is array

#Third Parties
Thanks for all third parties which is used in this minimal framework:
- 
