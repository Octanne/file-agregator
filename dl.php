<?php
if(isset($_GET["file"])){

    $rootShareFolder = getcwd()."/";

    // Get parameters
    $file = urldecode($_GET["file"]); // Decode URL-encoded string

    if ($file != "" && file_exists($rootShareFolder.$file)) {
        $pathR = realpath($rootShareFolder.$file);

        if ($pathR == false) {
            die("The path '$rootShareFolder$file' is incorrect!");
        } else {
            $folderAbsolute = $pathR;
        }

        if (str_contains($folderAbsolute, $rootShareFolder) == false) {
            die("The path '$folderAbsolute' is not in the share folder!");
        }

        // Process download
        if(file_exists($folderAbsolute)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($folderAbsolute).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($folderAbsolute));
            flush(); // Flush system output buffer
            readfile($folderAbsolute);
            die();
        } else {
            http_response_code(404);
            die();
        }
    } else {
        die("The file '$rootShareFolder$file' is not found!");
    }
} else {
    die("No file specified!");
}
