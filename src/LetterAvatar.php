<?php

namespace YoHang88\LetterAvatar;

use Intervention\Image\ImageManager;

class LetterAvatar {
	/**
	 * I used colors from the material design palette
	 *
	 * @var array
	 */
	public static $colors = [
		"#EF5350", "#B71C1C", "#F06292", "#880E4F", "#BA68C8", "#4A148C",
		"#9575CD", "#311B92", "#7986CB", "#283593", "#2196F3", "#1565C0",
		"#039BE5", "#01579B", "#0097A7", "#006064", "#009688", "#004D40",
		"#43A047", "#1B5E20", "#689F38", "#33691E", "#AFB42B", "#827717",
		"#FDD835", "#F57F17", "#FFC107", "#FF6F00", "#FB8C00", "#E65100",
		"#FF5722", "#BF360C", "#A1887F", "#3E2723", "#757575", "#212121"
	];

	/**
	 * @var string
	 */
	protected $name;


	/**
	 * @var string
	 */
	protected $shape;


	/**
	 * @var int
	 */
	protected $size;

	/**
	 * @var ImageManager
	 */
	protected $image_manager;


	public function __construct( $name, $shape = 'circle', $size = '48' ) {
		$this->setName( $name );
		$this->setImageManager( new ImageManager() );
		$this->setShape( $shape );
		$this->setSize( $size );
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 * @return ImageManager
	 */
	public function getImageManager() {
		return $this->image_manager;
	}

	/**
	 * @param ImageManager $image_manager
	 */
	public function setImageManager( ImageManager $image_manager ) {
		$this->image_manager = $image_manager;
	}

	/**
	 * @return string
	 */
	public function getShape() {
		return $this->shape;
	}

	/**
	 * @param string $shape
	 */
	public function setShape( $shape ) {
		$this->shape = $shape;
	}

	/**
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @param int $size
	 */
	public function setSize( $size ) {
		$this->size = $size;
	}


	/**
	 * @return \Intervention\Image\Image
	 */
	public function generate() {

		$initials = self::getInitials($this->name);

		$color_index = self::unicode_ord($initials[0]) % count(self::$colors);

		if (!empty(self::$colors[$color_index])) {
			$color_index = rand(0, count(self::$colors - 1));
		}

		$color = self::$colors[$color_index];

		if ( $this->shape == 'circle' ) {
			$canvas = $this->image_manager->canvas( 480, 480 );

			$canvas->circle( 480, 240, 240, function ( $draw ) use ( $color ) {
				$draw->background( $color );
			} );

		} else {

			$canvas = $this->image_manager->canvas( 480, 480, $color );
		}

		$canvas->text( $initials, 240, 240, function ( $font ) {
			$font->file( __DIR__ . '/fonts/arial-bold.ttf' );
			$font->size( 220 );
			$font->color( '#ffffff' );
			$font->valign( 'middle' );
			$font->align( 'center' );
		} );

		return $canvas->resize( $this->size, $this->size );
	}

	public function __toString() {
		return (string) $this->generate()->encode( 'data-url' );
	}

	/**
	 * Returns an array of capitalised first letter of each names
	 *
	 * @param $name
	 * @return array
	 */
	public static function getInitials($name) {
		return array_map(function($item) {
			return mb_substr(mb_strtoupper($item), 0, 1, 'UTF-8');
		}, explode(' ', $name));
	}

	/**
	 * Like ord() but for unicode
	 *
	 * @param $character
	 * @return int
	 */
	public static function unicode_ord( $character ) {
		$k  = mb_convert_encoding( $character, 'UCS-2LE', 'UTF-8' );
		$k1 = ord( substr( $k, 0, 1 ) );
		$k2 = ord( substr( $k, 1, 1 ) );

		return $k2 * 256 + $k1;
	}

}
