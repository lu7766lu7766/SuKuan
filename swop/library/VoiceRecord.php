<?php

/**
 * Created by PhpStorm.
 * User: lu7766
 * Date: 2018/2/8
 * Time: 下午12:47
 */

namespace lib;

class VoiceRecord
{
	static public $sourceExt = ".wav";
	static public $davidExt = ".g729";
	static public $davidAdFolder = "/var/www/html/ad/"; //"C:\\Program Files (x86)\\AssistorCore\\VoiceFiles\\Ad\\";
	static public $sourceFolder = "/var/www/html/download/"; //"C:\\xampp\\htdocs\\aurora02\\download\\";

	static public function uploadFile($userID, $fieldName)
	{
		if (file_exists($_FILES[$fieldName]["error"])) return -1;
		if (!file_exists($_FILES[$fieldName]["tmp_name"])) return false;

		return self::uploadLocal($userID, $fieldName);
	}

	static private function uploadLocal($userID, $fieldName)
	{
		$target_folder = config("voiceManage") . $userID . "/";
		@mkdir($target_folder, 0777, true);
		@mkdir(self::$sourceFolder, 0777, true);

		$fileName = iconv("utf-8", "big5", $_FILES[$fieldName]["name"]);
		$filePath = $target_folder . $fileName;
		$convertFilePath = self::$sourceFolder . $fileName;
		move_uploaded_file($_FILES[$fieldName]["tmp_name"], $filePath);
		// for david to convert
		copy($filePath, $convertFilePath);
		$ip = getenv2("DB_IP");
		$url = "http://{$ip}:60/ConvertFile.atp?User={$userID}&File={$fileName}";
		// dd($url);
		\comm\Http::get($url);
		@unlink($convertFilePath);
		return $fileName;
	}

	static public function getFilesName($userID)
	{
		$res = [];
		$files = self::getFiles($userID);
		// dd($files);
		foreach ($files as $file) {
			if (substr($file, -1) == "/")
				return;
			else
				$res[] = iconv("big5", "utf-8", self::getFileNameWithoutExt(basename($file)) . self::$sourceExt);
		}

		return $res;
	}

	static private function getFiles($userID)
	{
		// dd(self::getCurrentPath($userID));
		return glob(self::getCurrentPath($userID) . "*", GLOB_MARK);
	}

	static private function getCurrentPath($userID)
	{
		return self::$davidAdFolder . $userID . DIRECTORY_SEPARATOR; //"/";
	}


	static private function getFileNameWithoutExt($fileName)
	{
		return strtr($fileName, [self::$davidExt => "", self::$sourceExt => ""]);
	}

	static public function delFile($userID, $fileName)
	{
		$fileName = self::getFileNameWithoutExt($fileName);
		$big5fileName = iconv("utf-8", "big5", $fileName);
		$davidFile = $big5fileName . self::$davidExt;
		$jacFile = $big5fileName . self::$sourceExt;

		$delFilePath = self::getCurrentPath($userID) . $davidFile;
		unlink($delFilePath);

		$targetPath = config("voiceManage") . $userID . "/" . $jacFile;
		@unlink($targetPath);
	}
}
