<?php
namespace CustomRestApi;
require 'IValidator.php';

class RequiredValidator implements IValidator {
    public function validate($toValidate): bool {
        if (empty($toValidate)) {
            return false;
        }
        return true;
    }
}

class EmailValidator implements IValidator {
    public function validate($toValidate): bool {
        if ($toValidate === "") { //null
            return true;
        }
        return filter_var($toValidate, FILTER_VALIDATE_EMAIL);
    }
}

class MinLengthValidator implements IValidator {
    private int $minLength;
    function __construct(int $minLength) {
        $this->minLength = $minLength;

    }
    public function validate($toValidate): bool {
        if ($toValidate === "") { //null
            return true;
        }
        if (strlen($toValidate) < $this->minLength) {
            return false;
        }
        return true;
    }
}
class PhoneNrValidator implements IValidator {
    public function validate($toValidate): bool {
        if ($toValidate === "") { //null
            return true;
        }
        $val = preg_replace("/\s+/", '', $toValidate);
        return preg_match("/^(\+[0-9]{3})?[0-9 ]{9,}$/", $val) === 1 ? true : false;
    }
}

class AttachmentsValidator implements IValidator {
    const MAX_ATTACHMENT_SIZE = 10 * 1024 * 1024;
    const SUPPORTED_ATTACHMENTS = [
        "application/pdf",
        "image/gif",
        "image/jpeg",
        "image/jpg",
        "application/msword",
        "image/pjpeg",
        "image/x-png",
        "image/png"
    ];

    public function validate($toValidate): bool {
        if ($toValidate === "") { //null
            return true;
        }
        $attachments = $toValidate;

        if (empty($attachments) || !is_array($attachments)) {
            return false;
        }
        if (
            empty($attachments['name']) || 
            empty($attachments['tmp_name']) || 
            empty($attachments['type'])
        ) {
            return false;
        }
        
        $filesSize = 0;
        foreach($attachments['type'] as $fileIndx => $fileType) {
            $fileType = strtolower($fileType);
            if (!in_array($fileType, self::SUPPORTED_ATTACHMENTS)) {
                return false;
            }   
            $tmpFileName = $attachments['tmp_name'][$fileIndx];
            if (empty($tmpFileName)) {
                return false;
            }
            $filesSize += filesize($tmpFileName);
            
            if ($filesSize > self::MAX_ATTACHMENT_SIZE) {
                return false;
            } 
        }
        return true;
    }

}