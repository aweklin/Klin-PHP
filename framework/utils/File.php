<?php

namespace Framework\Utils;

use \Exception;

use Framework\Utils\Str;

/**
 * Contains various methods to ease working with Files.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
final class File {

    /**
     * 
     * Uploads a given file to the specified destination with the option to or not overwrite the file if it already exists at the destination directory.
     * You can also specify the maximum allowed upload size.
     * 
     * @param string $fileId Specifies the name given to the file input element on the form.
     * @param string $destination Specifies the destination folder for the uploaded file.
     * @param boolean $overwriteIfExists If set to false, the file will not be uploaded if the name already exists at same location.
     * @param array $allowedExtensions Indicates the list of all allowed extensions for the uploaded file. You can use [*.*] to allow any file type.
     * @param int $allowedMaximumUploadSize Indicates the maximum allowed size, based on your production environment setting. Set to -1 if you want to allow unlimited.
     * 
     * @return void
     * 
     */
    public static function upload($fileId, $destination, $overwriteIfExists = true, $allowedExtensions = UPLOAD_ALLOWED_EXTENSIONS, $allowedMaximumUploadSize = -1) {

        // some validations

        $fileName       = $_FILES[$fileId]['name'];
        $fileExtension  =  strtolower(self::getExtension($fileName));
        $allowedExtensions = array_map('strtolower', $allowedExtensions);
        if ($allowedExtensions !== ['*.*'])
            if (!in_array($fileExtension, $allowedExtensions))
                throw new Exception("File with .{$fileExtension} extension is not allowed for upload. Please upload a file with any of the following extensions: " . implode(', ', $allowedExtensions));

        if (!isset($_FILES) || (isset($_FILES) && !$_FILES))
            throw new Exception("No file was uploaded.");
        if (!$_FILES[$fileId])
            throw new Exception("There was no file element found with the id: {$fileId}.");
        /*if (!is_dir($destination))
            throw new Exception("Destination: {$destination} is not a valid directory.");*/
        if (!file_exists($destination))
            mkdir($destination, 0775, true);
        if (!is_writable($destination))
            throw new Exception("Access denied to write to the destination: {$destination}.");
        if (!$_FILES[$fileId]['name'])
            return;

        $fileSize = $_FILES[$fileId]['size'];
        if ($allowedMaximumUploadSize !== -1)
            if ($allowedMaximumUploadSize < $fileSize)
                throw new Exception("You can only upload a file within " . ($allowedMaximumUploadSize < 2048 ? $allowedMaximumUploadSize . "KB." : ($allowedMaximumUploadSize / 1024) . "MB."));

        $path = $destination . DS . $fileName;
        if (file_exists($path) && !$overwriteIfExists)
            throw new Exception("There is already a file named {$fileName} at {$destination}.");
        
        // move the uploaded file
        if (!@move_uploaded_file($_FILES[$fileId]['tmp_name'], $path))
            throw new Exception("Unable to upload {$fileName} to {$destination}");//TODO:: append actual file upload error

    }

    /**
     * 
     * Returns the extension of a given file.
     * 
     * @param string $fileName The file name to retrieve its extension.
     * 
     * @return string
     * 
     */
    public static function getExtension(string $fileName): string {
        if (Str::isEmpty($fileName)) return '';

        $fileParts  = self::getFileInfo($fileName);
        if (empty($fileParts) || (!empty($fileParts) && !isset($fileParts['extension']))) return '';
        $extension  = mb_strtolower($fileParts['extension']);

        return $extension;
    }

    /**
     * Returns the base64 string of the uploaded file.
     * 
     * @param string $fileId Specifies the name given to the file input element on the form.
     * @param array $allowedExtensions Indicates the list of all allowed extensions for the uploaded file. You can use [*.*] to allow any file type.
     * 
     * @return string
     */
    public static function getBase64(string $fileId, $allowedExtensions = UPLOAD_ALLOWED_EXTENSIONS): string {
        // some validations
        if (!isset($_FILES) || (isset($_FILES) && !$_FILES))
            throw new Exception("No file was uploaded.");
        if (!$_FILES[$fileId])
            throw new Exception("There was no file element found with the id: {$fileId}.");
        if (!$_FILES[$fileId]['name'])
            return '';

        $fileName       = $_FILES[$fileId]['name'];
        $fileExtension  = self::getExtension($fileName);
        if ($allowedExtensions !== ['*.*'])
            if (!in_array($fileExtension, $allowedExtensions))
                throw new Exception("File with .{$fileExtension} extension is not allowed for upload. Please upload a file with any of the following extensions: " . implode(', ', $allowedExtensions));

        // extract the data and then the base64 string
        $fileData   = file_get_contents($_FILES[$fileId]['tmp_name']);
        $mimeType   = $_FILES[$fileId]['type'];
        $mimeType0  = explode('/', $mimeType)[0];
        //$base64     = "data:{$mimeType}/{$fileExtension};base64," . base64_encode($fileData);
        $base64     = "data:{$mimeType};base64," . base64_encode($fileData);

        return $base64;
    }

    /**
     * 
     * Returns an information about a file path.
     * 
     * @param string $fileName The file name to retrieve its extension.
     * 
     * @return array
     */
    public static function getFileInfo($fileName): array {
        return pathinfo($fileName);;
    }

    /**
     * Appends the given content to the file path specified.
     * 
     * @param string $fileName The path to the file to update its content.
     * @param string $content The actual text to add to the given file path.
     */
    public static function write($fileName, $content) {
        $handle = fopen($fileName, 'a');
        fwrite($handle, $content);
        fclose($handle);
    }

}