<?php
if (!empty($_SERVER["HTTPS"]))
  if ($_SERVER["HTTPS"] !== "off")
    $https = true; //https
  else
    $https = false; //http
else
  $https = false; //http

if (!$https) {
  $httpsUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  header("Location: $httpsUrl");
  exit;
}
