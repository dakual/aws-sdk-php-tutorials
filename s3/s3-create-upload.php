<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\ObjectUploader;
use Aws\Exception\AwsException;

$s3Client = new S3Client([
  'profile' => 'default',
  'region'  => 'eu-central-1',
  'version' => 'latest'
]);

$bucket = 'bucket-name';
$key    = 'test-file.zip';

function createBucket()
{
  global $s3Client, $bucket, $key;

  try {
    $result = $s3Client->createBucket([
        'Bucket' => $bucket,
    ]);

    echo 'The bucket\'s location is: ' .
        $result['Location'] . '. The bucket\'s effective URI is: ' . 
        $result['@metadata']['effectiveUri'] . PHP_EOL;

        return true;
  } catch (AwsException $e) {
    echo 'Error: ' . $e->getAwsErrorMessage() . PHP_EOL;
    if (strstr($e->getAwsErrorMessage(), 'you already own it'))
      return true;
  }

  return false;
}

function uploadFile() 
{
  global $s3Client, $bucket, $key;

  $return   = null;
  $source   = fopen('./'.$key, 'rb');
  $uploader = new ObjectUploader(
      $s3Client,
      $bucket,
      $key,
      $source
  );

  do {
    try {
        $result = $uploader->upload();
        if ($result["@metadata"]["statusCode"] == '200') {
            echo 'File successfully uploaded to ' . $result["ObjectURL"] . PHP_EOL;
        }
        print($result . PHP_EOL);
    } catch (MultipartUploadException $e) {
        rewind($source);
        $uploader = new MultipartUploader($s3Client, $source, [
            'state' => $e->getState(),
        ]);
    }
  } while (!isset($result));

  fclose($source);
}

if( createBucket() ) {
  uploadFile();
}
