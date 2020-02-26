<?php

namespace App\DataImport\Normalizer;

use App\Exception\DomainException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Symfony\Component\OptionsResolver\OptionsResolver;

class XlsxFileContentNormalizer 
    extends AbstractFileContentNormalizer 
    implements FileContentNormalizerInterface
{    
    public function normalize($filePath, array $options = array())
    {
        $inputFileType = IOFactory::identify($filePath);
        
        $reader = null;
        
        switch($inputFileType)
        {
            case 'Xls':
                $reader = new Xls();
                break;
            case 'Xlsx':
                $reader = new Xlsx();
                break;            
            default:
                throw new DomainException('Sólo se permiten archivo del tipo XLS ó XLSX');
        }
        
        $spreadsheet = $reader->load($filePath);
        
        $sheet = $spreadsheet->getSheet(0);
        
        // From cell
        
        $fromCell = sprintf(
            '%s%s',
            $this->options['from_column'],
            $this->options['from_row']
        );
        
        // To cell
        
        $toRow = $sheet->getHighestRow();
        
        $toCell = sprintf(
            '%s%s',
            $this->options['to_column'],
            $toRow
        );        
        
        // Cell range
        
        $cellRange = sprintf(
            '%s:%s',
            $fromCell,
            $toCell
        );
        
        return $sheet->rangeToArray($cellRange);
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('from_column');
        $resolver->setRequired('to_column');
        $resolver->setRequired('from_row');
    }
}

