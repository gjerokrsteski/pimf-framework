<?php
/**
 * Pimf_Util
 *
 * PHP Version 5
 *
 * A comprehensive collection of PHP utility classes and functions
 * that developers find themselves using regularly when writing web applications.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://krsteski.de/new-bsd-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to gjero@krsteski.de so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2010-2013 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

/**
 * A file uploaded through a form.
 *
 * <code>
 *
 *   // Create a file instance.
 *   $upload = new Pimf_Util_Uploaded(
 *     $_FILES['tmp_name'], $_FILES['name'], $_FILES['type'], $_FILES['size'], $_FILES['error']
 *   );
 *
 *   // ... OR ..
 *
 *   // Create an instance using the factory method for more security.
 *   $upload = Pimf_Util_Uploaded::factory($_FILES);
 *
 *   if ($upload instanceof Pimf_Util_Uploaded) {
 *     $upload->move('path/to/your/images/dir', $upload->getClientOriginalName());
 *   }
 *
 * </code>
 *
 * @package Pimf_Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Util_Uploaded extends Pimf_Util_File
{
  /**
   * Whether the test mode is activated.
   * Local files are used in test mode hence the code should not enforce HTTP uploads.
   * @var bool
   */
  private $test = false;

  /**
   * The original name of the uploaded file.
   * @var string
   */
  private $name;

  /**
   * The mime type provided by the uploader.
   * @var string
   */
  private $mime;

  /**
   * The file size provided by the uploader.
   * @var string
   */
  private $size;

  /**
   * The UPLOAD_ERR_XXX constant provided by the uploader.
   * @var integer
   */
  private $error;

  /**
   * @var array
   */
  private static $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');

  /**
   * Accepts the information of the uploaded file as provided by the PHP global $_FILES.
   *
   * <code>
   *   // Create a file instance.
   *   $file = new Pimf_Util_Uploaded(
   *     $_FILES['tmp_name'], $_FILES['name'], $_FILES['type'], $_FILES['size'], $_FILES['error']
   *   );
   * </code>
   *
   * @param string $path The full temporary path to the file
   * @param bool $name The original file name
   * @param null $mime The type of the file as provided by PHP
   * @param null $size The file size
   * @param null $error The error constant of the upload (one of PHP's UPLOAD_ERR_XXX constants)
   * @param bool $test Whether the test mode is active
   *
   * @throws RuntimeException If file_uploads is disabled
   */
  public function __construct($path, $name, $mime = null, $size = null, $error = null, $test = false)
  {
    if (!ini_get('file_uploads')) {
      throw new RuntimeException(
        'Unable to create file because "file_uploads" is disabled in your php.ini'
      );
    }

    $this->name  = $this->getName($name);
    $this->mime  = $mime ?: 'application/octet-stream';
    $this->size  = $size;
    $this->error = $error ?: UPLOAD_ERR_OK;
    $this->test  = (bool)$test;

    parent::__construct($path, UPLOAD_ERR_OK === $this->error);
  }

  /**
   * Factory for save instance creation.
   *
   * <code>
   *   // Create an instance using the factory method.
   *   $file = Pimf_Util_Uploaded::factory($_FILES);
   * </code>
   *
   * @param mixed $file A $_FILES multi-dimensional array of uploaded file information.
   * @param bool $test Whether the test mode is active for essayer unit-testing.
   * @return null|Pimf_Util_Uploaded
   */
  public static function factory(array $file, $test = false)
  {
    $file = static::heal($file);

    if (is_array($file) && isset($file['name']) && empty($file['name']) === false) {

      $keys = array_keys($file);
      sort($keys);

      if ($keys == self::$fileKeys) {

        if (UPLOAD_ERR_NO_FILE == $file['error']) {
          return null;
        }

        return new self($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error'], $test);
      }
    }

    return null;
  }

  /**
   * Returns the original file name.
   *
   * It is extracted from the request from which the file has been uploaded.
   * Then is should not be considered as a safe value.
   *
   * @return string|null
   */
  public function getClientOriginalName()
  {
    return $this->name;
  }

  /**
   * Returns the file mime type.
   *
   * It is extracted from the request from which the file has been uploaded.
   * Then is should not be considered as a safe value.
   *
   * @return string|null
   */
  public function getClientMimeType()
  {
    return $this->mime;
  }

  /**
   * Returns the file size.
   *
   * It is extracted from the request from which the file has been uploaded.
   * Then is should not be considered as a safe value.
   *
   * @return integer|null
   */
  public function getClientSize()
  {
    return $this->size;
  }

  /**
   * Returns the upload error.
   *
   * If the upload was successful, the constant UPLOAD_ERR_OK is returned.
   * Otherwise one of the other UPLOAD_ERR_XXX constants is returned.
   *
   * @return integer
   */
  public function getError()
  {
    return $this->error;
  }

  /**
   * Returns whether the file was uploaded successfully.
   *
   * @return boolean True if no error occurred during uploading
   */
  public function isValid()
  {
    return $this->error === UPLOAD_ERR_OK;
  }

  /**
   * Moves the file to a new location.
   *
   * @param string $dir
   * @param null $name
   * @return Pimf_Util_File
   * @throws RuntimeException If the file has not been uploaded via Http or can not move the file.
   */
  public function move($dir, $name = null)
  {
    if ($this->isValid()) {

      if ($this->test) {
        return parent::move($dir, $name);
      }

      if (is_uploaded_file($this->getPathname())) {

        $target = $this->getTargetFile($dir, $name);

        if (!@move_uploaded_file($this->getPathname(), $target)) {
          $error = error_get_last();
          throw new RuntimeException(
            "Could not move the file {$this->getPathname()} to $target ({$error['message']})"
          );
        }

        @chmod($target, 0666 & ~umask());

        return $target;
      }
    }

    throw new RuntimeException("The file {$this->getPathname()} has not been uploaded via Http");
  }

  /**
   * Returns the maximum size of an uploaded file in bytes as configured in php.ini
   * @return int
   */
  public static function getMaxFilesize()
  {
    $max = trim(ini_get('upload_max_filesize'));

    if ('' === $max) {
      return PHP_INT_MAX;
    }

    switch (strtolower(substr($max, -1))) {
      case 'g':
        $max *= 1024;
        break;
      case 'm':
        $max *= 1024;
        break;
      case 'k':
        $max *= 1024;
        break;
    }

    return (integer)$max;
  }

  /**
   * Heals a malformed PHP $_FILES array.
   *
   * PHP has a bug that the format of the $_FILES array differs, depending on
   * whether the uploaded file fields had normal field names or array-like
   * field names ("normal" vs. "parent[child]").
   *
   * This method fixes the array to look like the "normal" $_FILES array.
   *
   * @param array $data
   * @return array
   */
  protected static function heal($data)
  {
    if (!is_array($data)) {
      return $data;
    }

    $keys = array_keys($data);
    sort($keys);

    if (self::$fileKeys != $keys || !isset($data['name']) || !is_array($data['name'])) {
      return $data;
    }

    $files = $data;

    foreach (self::$fileKeys as $k) {
      unset($files[$k]);
    }

    foreach (array_keys($data['name']) as $key) {
      $files[$key] = static::heal(
        array(
          'error'    => $data['error'][$key],
          'name'     => $data['name'][$key],
          'type'     => $data['type'][$key],
          'tmp_name' => $data['tmp_name'][$key],
          'size'     => $data['size'][$key]
        )
      );
    }

    return $files;
  }
}
