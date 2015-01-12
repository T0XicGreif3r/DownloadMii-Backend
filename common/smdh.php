<?php
	/*
		Ported from 3DSExplorer (copyright Eli Sherer under the GPL v3 license)
	*/

	class smdh {
		private $smallIcon;
		private $largeIcon;
		private $fileHandle;
		
		//Decode RGB5A4 Taken from the dolphin project
		private $convert5To8 = array(0x00, 0x08, 0x10, 0x18, 0x20, 0x29, 0x31, 0x39,
									0x41, 0x4A, 0x52, 0x5A, 0x62, 0x6A, 0x73, 0x7B,
									0x83, 0x8B, 0x94, 0x9C, 0xA4, 0xAC, 0xB4, 0xBD,
									0xC5, 0xCD, 0xD5, 0xDE, 0xE6, 0xEE, 0xF6, 0xFF);
		
		private function decodeColor($image, $val) {
			$red = $this->convert5To8[($val >> 11) & 0x1F];
			$green = $this->convert5To8[($val >> 6) & 0x1F];
			$blue = $this->convert5To8[($val >> 1) & 0x1F];
			return imagecolorallocate($image, $red, $green, $blue);
		}
		
		private function decodeTile($image, $iconSize, $tileSize, $ax, $ay) {
			if ($tileSize < 1)
			{
				$tempBytes = unpack('C*', fread($this->fileHandle, 2));
				imagesetpixel($image, $ax, $ay, $this->decodeColor($image, ($tempBytes[2] << 8) + $tempBytes[1]));
			}
			else {
				for ($y = 0; $y < $iconSize; $y += $tileSize) {
					for ($x = 0; $x < $iconSize; $x += $tileSize) {
						$this->decodeTile($image, $tileSize, $tileSize / 2, $x + $ax, $y + $ay);
					}
				}
			}
		}
		
		private function getIcon($width, $height, $tileSize, $imgOffset) {
			$image = imagecreatetruecolor($width, $height);
			fseek($this->fileHandle, $imgOffset);
			
			for ($y = 0; $y < $height; $y += $tileSize) {
				for ($x = 0; $x < $width; $x += $tileSize) {
					$this->decodeTile($image, $tileSize, $tileSize, $x, $y);
				}
			}
			
			return $image;
		}
		
		public function __construct($fileHandle) {
			if (fstat($fileHandle)['size'] != 0x36c0) {
				throw new Exception('Invalid SMDH file size.');
			}
			
			$this->fileHandle = $fileHandle;
		}
		
		public function __destruct() {
			if (isset($this->smallIcon)) {
				imagedestroy($this->smallIcon);
			}
			if (isset($this->largeIcon)) {
				imagedestroy($this->largeIcon);
			}
		}
		
		public function getSmallIcon() {
			if (!isset($this->smallIcon)) {
				$this->smallIcon = $this->getIcon(24, 24, 8, 0x2040);
			}
			
			return $this->smallIcon;
		}
		
		public function getLargeIcon() {
			if (!isset($this->largeIcon)) {
				$this->largeIcon = $this->getIcon(48, 48, 8, 0x24C0);
			}
			
			return $this->largeIcon;
		}
	}
?>