{ pkgs, ... }:

{

  scripts.run-server.exec = ''
    php -S localhost:8000 kirby/router.php
  '';

  languages.php.enable = true;
  languages.php.version = "8.1";
  languages.php.extensions = [
    "ctype"
    "curl"
    "dom"
    "filter"
    "iconv"
    "mbstring"
    "openssl"
    "simplexml"
    "exif"
    "fileinfo"
    "intl"
    "apcu"
    "memcached"
    "pdo"
    "zip"
    "zlib"
    "gd"
  ];

}
