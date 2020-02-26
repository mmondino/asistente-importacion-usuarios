<?php

namespace App\DataImport\Normalizer;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvFileContentNormalizer implements FileContentNormalizerInterface
{    
    public function normalize($filePath)
    {
        $reader = new Csv();
        $reader->setDelimiter($this->options['field_separator']);
        $reader->setEnclosure('');
        $reader->setSheetIndex(0);
        
        $spreadsheet = $reader->load($filePath);
        
        $sheet = $spreadsheet->getSheet(0);
        
        return $sheet->toArray();                
    }
    
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'field_separator' => ','
        ));
    }    
}

