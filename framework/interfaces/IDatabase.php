<?php

namespace Framework\Interfaces;

interface IDatabase {

    function hasError() : bool;
    function getErrorMessage() : string;

}