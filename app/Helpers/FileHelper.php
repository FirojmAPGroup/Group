<?php

namespace App\Helpers;

use File;

class FileHelper
{

  public static function driver()
  {
    return 'uploads';
  }
  public static function disk()
  {
    return \Storage::disk(self::driver());
  }
  public static function path($path = NULL)
  {
    return public_path('uploads/' . $path);
  }

  public static function makeFolder($folder)
  {
    return self::disk()->makeDirectory($folder);
  }

  public static function exists($folder, $filename)
  {
    return $filename ? self::disk()->exists($folder . '/' . $filename) : '';
  }

  public static function folders($folder = NULL)
  {
    if (is_array($folder)) {
      $folder = implode('/', $folder);
    }
    return $folder;
  }
  public static function upload($file, $folder)
  {
    if ($file) {
      $filename = seoText($file->getClientOriginalName());
      $filename = pathinfo($filename, PATHINFO_FILENAME) . '-' . uniqueCode() . '.' . pathinfo($filename, PATHINFO_EXTENSION);
      if (self::disk()->put(self::folders($folder) . '/' . $filename, file_get_contents($file)))
        return $filename;
    }
    return '';
  }
  public static function delete($filename, $folder)
  {
    $folder = self::folders($folder);
    return self::exists($folder, $filename) ? self::disk()->delete($folder . '/' . $filename) : '';
  }
  public static function url($filename, $folder)
  {
    $folder = self::folders($folder);
    return self::exists($folder, $filename) ? self::disk()->url($folder . '/' . $filename) : '';
  }
  public static function extn($url)
  {
    return pathinfo($url, PATHINFO_EXTENSION);
  }
  public static function isImage($url)
  {
    return in_array(strtolower(self::extn($url)), ['jpg', 'jpeg', 'png', 'gif']);
  }
  public static function move($old, $new)
  {
    return File::move($old, $new);
  }
  public static function copy($old, $new)
  {
    return File::copy($old, $new);
  }

  public static function filePreview($url)
  {
    return $url ? '<a href="' . $url . '" target="_blank" class="ml-2 col-cyan mr-2" title="Preview File" ><i class="material-icons">visibility</i></a>' : '';
  }
  public static function previewLarge($url, $width = 100)
  {
    if (!self::extn($url)) return '';
    return '<a href="' . $url . '" target="_blank" title="Preview File" class="col-cyan" ><i class="material-icons">visibility</i></a>';
  }
  public static function imagePreview($url, $width = 100)
  {
    if (!self::extn($url)) return '';
    return '<img height="163px" weight="140px" src="' . $url . '" />';
  }
  public static function videoPreview($url)
  {
    if (!self::extn($url)) return '';
    return '<iframe width="300" height="150" src="' . self::path('content/') . $url . '">
    </iframe>';
  }
}
