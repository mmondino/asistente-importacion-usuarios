<?php

namespace App\DataImport\Definition;

use App\DataImport\Normalizer\FileContentNormalizerInterface;
use Symfony\Component\Form\FormInterface;

interface ImportDefinitionInterface
{
    public function getNormalizer(): FileContentNormalizerInterface;
    
    public function getForm(): FormInterface;
    
    public function getRecordWithFieldNames($record = array()): array;
}

