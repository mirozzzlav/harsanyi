<?php
namespace CustomRestApi;

interface IValidator {
    public function validate($toValidate): bool;
}