<?php

namespace App\DataImport\Normalizer;

interface FileContentNormalizerInterface
{
    /**
     * 
     * @param string $filePath
     * @return array
     */
    public function normalize($filePath);    
}

