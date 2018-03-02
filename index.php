<?php

require __DIR__ . '/vendor/autoload.php';

$protocDir = __DIR__ .'/protoc';
echo "Assume protoc is in $protocDir", PHP_EOL;
$command = $protocDir .'/bin/protoc'
	. ' --php_out=' . __DIR__
	. ' --proto_path=' . __DIR__
	. ' --proto_path=' . $protocDir . '/include'
	. ' sample.proto';
exec($command);

require __DIR__ . '/Sample.php';
echo PHP_EOL;
echo PHP_EOL;

$timestamp = new \DateTime('2018-02-25T03:48:17.428086', new \DateTimeZone('UTC'));
$gpbTimestamp = new \Google\Protobuf\Timestamp();
$gpbTimestamp->setSeconds((int) $timestamp->format('U'));
$gpbTimestamp->setNanos((int) $timestamp->format('u'));
echo "PHP Version: ", PHP_VERSION, PHP_EOL;
echo PHP_EOL;
echo "Original as string : ", $timestamp->format('Y-m-d\TH:i:s.uP'), PHP_EOL;
echo "Original seconds   : ", $timestamp->format('U.u'), PHP_EOL;
echo PHP_EOL;

echo "Convert nano string to/from JSON:", PHP_EOL;
$nanoArray = [
    'timestamp' => $timestamp->format('Y-m-d\TH:i:s.u') . '000Z',
];
$message = new Sample();
$message->mergeFromJsonString(json_encode($nanoArray));
echo "Nano input string  : ", $nanoArray['timestamp'], PHP_EOL;
echo "JS Decoded seconds : ", $message->getTimestamp()->getSeconds(), PHP_EOL;
echo "JS Decoded nanos   : ", $message->getTimestamp()->getNanos(), PHP_EOL;
echo PHP_EOL;

echo "Convert to and from protobuf:", PHP_EOL;
$message = new Sample(); // ensure clear start
$message->setTimestamp($gpbTimestamp);
echo "Pre-enc seconds   : ", $message->getTimestamp()->getSeconds(), PHP_EOL;
echo "Pre-enc nanos     : ", $message->getTimestamp()->getNanos(), PHP_EOL;
$pbEnc = $message->serializeToString();
$message = new Sample(); // ensure clear start
$message->mergeFromString($pbEnc);
echo "PB Decoded sec    : ", $message->getTimestamp()->getSeconds(), PHP_EOL;
echo "PB Decoded nsec   : ", $message->getTimestamp()->getNanos(), PHP_EOL;
echo PHP_EOL;

echo "Convert to and from JSON:", PHP_EOL;
$message = new Sample(); // ensure clear start
$message->setTimestamp($gpbTimestamp);
$jsonEnc = $message->serializeToJsonString();
$message = new Sample(); // ensure clear start
$message->mergeFromJsonString($jsonEnc);
echo "JS Decoded sec    : ", $message->getTimestamp()->getSeconds(), PHP_EOL;
echo "JS Decoded nsec   : ", $message->getTimestamp()->getNanos(), PHP_EOL;
