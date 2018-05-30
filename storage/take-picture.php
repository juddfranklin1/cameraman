<?php
require '../aws-connect.php';
use Aws\S3\Exception\S3Exception;

$s3 = new S3Connection;

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$imageName = 'browser-pic-' . time() . '.webp';

if(isset($_POST['pictureInfo'])){
    $img = str_replace('data:image/webp;base64,', '', $_POST['pictureInfo']);
    $img = str_replace(' ', '+', $img);

    $imageContent = base64_decode($img);
    $imageNameWithDir = '../images/' . $imageName;
    file_put_contents($imageNameWithDir, $imageContent);
    print($imageName);
    
    $result = $s3->connection->putObject([
        'Bucket' => 'juddfranklin-bucket1',
        'Key'    => $imageName,
        'Body'   => $imageContent
    ]);
}else{
    $im = imagecreatefromstring($imageContent);

    if ($im !== false) {
        header('Content-Type: image/png');
        imagepng($im);
        // Below would write a new file to the path provided.
        imagepng($im,'images/' . $imageName);
        imagedestroy($im);
    
        echo $im;
    
        // Send a PutObject request and get the result object.
        $result = $s3->connection->putObject([
             'Bucket' => 'juddfranklin-bucket1',
             'Key'    => $imageName,
             'Body'   => $im
        ]);
    }
    else {
        echo 'An error occurred.';
    }
}
