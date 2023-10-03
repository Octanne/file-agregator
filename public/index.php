<?php
ini_set('display_errors', 1);

$nameWebsite = getenv("NAME");
$titleWebsite = getenv("TITLE");
$descWebsite = getenv("DESC");
$footerText = "Files agregator | Made with <i class='bi bi-heart-fill'></i> by Octanne | 2020-2023";
$rootShareFolder = getcwd()."/files/";

$folderIcon = <<<HTML
<i class="bi bi-folder"></i>
HTML;

$dlIcon = <<<HTML
<i class="bi bi-box-arrow-down"></i>
HTML;

$zipIcon = <<<HTML
<i class="bi bi-file-zip"></i>
HTML;

$visitIcon = <<<HTML
<i class="bi bi-globe"></i>
HTML;

function icon_file($file) {
  $extKnow = ["aac", "ai", "bmp", "cs", "css", "csv", "txt", "doc", " docx ", "exe ", "gif ", "heic", " html ",
              "java ", "jpg ", "js ", "json", "jsx", "key", "m4p", "md", "mov", "mp3", "mp4", "otf", "pdf", "php",
              "png", "ppt", "pptx", "psd", "py", "raw", "rb", "sass", "scss", "sh", "sql", "svg", "tiff", "tsx",
              "txt", "wav", "woff", "xls", "xlsx", "xml", "yml"];
  $ext = pathinfo($file, PATHINFO_EXTENSION);

  if (in_array($ext, $extKnow)) $ext = "filetype-".$ext;
  elseif ($ext == "conf") $ext = "file-earmark-code";
  elseif (in_array($ext,["zip","gz","rar"])) $ext = "file-earmark-zip";
  else $ext = "file-earmark";

  return <<<HTML
    <i class="bi bi-$ext"></i>
  HTML;
}

// Get path of the folder to display
if (isset($_GET['folder'])) { // Si le paramètre "folder" est défini
  $folderDecode = urldecode($_GET['folder']); // Decode URL-encoded string
  if ($folderDecode != "" && is_dir($rootShareFolder.$folderDecode)) { // Si le dossier existe
    $pathR = realpath($rootShareFolder.$folderDecode);
    if ($pathR == false) {
      $folderAbsolute = $rootShareFolder;  // Utiliser le dossier racine
    } else {
      if (str_contains($pathR, getcwd())) {
        $folderAbsolute = $pathR."/"; // Utiliser le dossier demandé
      } else {
        $folderAbsolute = $rootShareFolder; // Utiliser le dossier racine
      }
    }
  } else {
    $folderAbsolute = $rootShareFolder; // Utiliser le dossier racine
  }
} else {
  $folderAbsolute = $rootShareFolder; // Utiliser le dossier racine
}

// Get the path relative to the root folder
$pathRelative = str_replace($rootShareFolder, "", $folderAbsolute);

function getPathOfPathPart($path) {
  $pathParts = ["Root" => "/"];
  if ($path == "") return $pathParts;
  $pathExplode = explode("/", $path);
  foreach ($pathExplode as $key => $value) {
    if ($value == "") continue;
    $pathParts[$value] = implode("/", array_slice($pathExplode, 0, $key+1));
  }
  return $pathParts;
}

function filter_files($file) {
  global $rootShareFolder, $folderAbsolute;
  $filterFiles = [".", ".."];
  $filterFileFirstPage = ["index.php", "favicon.ico", ".htaccess", ".htpasswd", "dl.php", "includes"];
  if (in_array($file, $filterFiles)) return true;
  if ($folderAbsolute == $rootShareFolder && (in_array($file, $filterFileFirstPage))) return true;
  return false;
}

// Generate the breadcrumb
$pathParts = getPathOfPathPart($pathRelative);
$pathS = "";
$lastKey = array_key_last($pathParts);
foreach ($pathParts as $key => $value) {
  if ($value == "") continue;
  if ($key == $lastKey) {
    $pathS .= <<<HTML
      <li class="breadcrumb-item active" aria-current="page">$key</li>
    HTML;
  } else {
    $valMod = urlencode($value);
    $pathS .= <<<HTML
      <li class="breadcrumb-item" aria-current="page"><a href="?folder=$valMod">$key</a></li>
    HTML;
  }
}

// Generate the folder content
$folderS = "";
$folderFiles = scandir($folderAbsolute);
$iMax = count($folderFiles);

if ($iMax < 3) {
  $folderS .= <<<HTML
    <li class="border border-dark-subtle rounded my-1 px-2 py-1 bg-body-tertiary d-flex justify-content-center nothing-file">
      <span class="text-primary-emphasis">There is nothing in this folder</span>
    </li>
  HTML;
} else {
  foreach ($folderFiles as $i => $file) {
    if (filter_files($file)) continue;
    if (is_dir($folderAbsolute.$file)) {
      $addressToFolder = "?folder=".urlencode($pathRelative.$file);
      $folderS .= <<<HTML
        <li class="border border-dark-subtle rounded my-1 px-2 py-1 bg-body-tertiary d-flex justify-content-between file-selectable">
          <span class="flex-fill" onclick="new function() {document.location.href='$addressToFolder';};">
            $folderIcon
            <span class="text-warning-emphasis">$file</span>
          </span>
          <span>
            <a class="btn btn-outline-primary" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;"
              data-bs-toggle="popover" data-bs-custom-class="custom-popover" data-bs-trigger="focus" tabindex="0" data-bs-title="Download zipped folder" data-bs-content="Chargement..."
              onclick="new function() { window.open('/files/$addressToFiles/', '_blank'); };">
              $zipIcon Visit
            </a>
            <a class="btn btn-outline-warning" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;"
              data-bs-toggle="popover" data-bs-custom-class="custom-popover" data-bs-trigger="focus" tabindex="0" data-bs-title="Download zipped folder" data-bs-content="This feature is not yet implements.">
              $zipIcon
            </a>
          </span>
        </li>
      HTML;
    } else {
      $fileIcon = icon_file($folderAbsolute.$file);
      $addressToFiles = urlencode($pathRelative.$file);
      $folderS .= <<<HTML
        <li class="border border-dark-subtle rounded my-1 px-2 py-1 bg-body-tertiary d-flex justify-content-between file-selectable">
          <span class="flex-fill">
            $fileIcon
            <span class="text-primary-emphasis">$file</span>
          </span>
          <span>
            <a class="btn btn-outline-success" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;"
              onclick="new function() { window.open('dl.php?file=$addressToFiles', '_blank'); };">
              $dlIcon Download
            </a>
          </span>
        </li>
       HTML;
    }
  }
}

// Generate the HTML
echo <<<HTML
<!DOCTYPE html>
<html lang="">
  <head>
    <meta charset="utf-8">
    <title>$titleWebsite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="$descWebsite">
    <meta name="author" content="Octanne">
    <!-- bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="/includes/style.css">
  </head>
  <body style="padding-top: 5em; padding-bottom: 5em;" data-bs-theme="dark">
    <div class="container">
      <nav class="navbar fixed-top bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid justify-content-between">
          <a class="navbar-brand" href="#">$nameWebsite</a>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a type="button" class="btn btn-outline-secondary" href="https://octanne.eu">Back to site</a>
            </li>
          </ul>
        </div>
      </nav>

      <div class="container border border-secondary rounded-2 px-0" style="--bs-border-opacity: .75;">
        <nav class="d-flex flex-column" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
          <ol class="breadcrumb border-bottom p-2 bg-body-secondary">
            $pathS
          </ol>
        </nav>
        <ul class="navbar-nav m-1 px-1 pb-2">
          $folderS
        </ul>
      </div>

      <nav class="navbar fixed-bottom bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid text-align-center justify-content-center">
          <p class="navbar-text m-0" style="text-decoration: bold;" href="#">$footerText</p>
        </div>
      </nav>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script>
      var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
      var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
      })
    </script>
  </body>
</html>
HTML;
