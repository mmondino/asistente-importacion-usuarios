<?php

namespace App\DataImport\Importer;

use App\DataImport\Definition\ImportDefinitionInterface;

class Importer
{
    /**
     * 
     * @param ImportDefinitionInterface $importDefinition
     * @param string $filePath
     * @return array
     */
    public function previewData(ImportDefinitionInterface $importDefinition, string $filePath)
    {
        $normalizer = $importDefinition->getNormalizer();
        
        $records = $normalizer->normalize($filePath);
        
        $previewRecords = array();
        
        // summary data
        $totalRecordsProcessed = 0;
        $recordsWithErrorsCount = 0;
        
        $index = 0;
        
        foreach($records as $record)
        {
            $index++;
            $totalRecordsProcessed++;
                    
            $errors = array();                        
            
            $recordWithFieldNames = $importDefinition->getRecordWithFieldNames($record);                        
            
            $form = $importDefinition->getForm();
            
            $form->submit($recordWithFieldNames);

            if ($form->isValid())
            {
                // store
            }
            else
            {              
                $recordsWithErrorsCount++;
                
                foreach($recordWithFieldNames as $fieldName => $fieldValue)
                {                                                         
                    $formFieldErrors = $form->get($fieldName)->getErrors();                                        
                    
                    $errorsMessage = array();
                    
                    foreach($formFieldErrors as $formFieldError)
                    {
                        $errorsMessage[] = $formFieldError->getMessage();
                    }
                    
                    if (count($errorsMessage))
                    {
                        $errors[$fieldName] = $fieldName . ': ' . implode(', ', $errorsMessage);
                    }
                }                                
            }
            
            $recordWithFieldNames['errors'] = $errors;
            
            $previewRecords[] = $recordWithFieldNames;
        }      
        
        return $previewRecords;
    }
    
    public function importData(ImportDefinitionInterface $importDefinition, array $normalizedData = array())
    {    
        $records = $normalizedData;
        
        foreach($records as $record)
        {
            unset($record['errors']);
            
            $errors = array();            
            
            $form = $importDefinition->getForm();
            
            $form->submit($record);

            if ($form->isValid())
            {
                // store
            }
            else
            {                 
                foreach($record as $fieldName => $fieldValue)
                {                    
                    $errorAsString = (string)$form->get($fieldName)->getErrors(true, true);
                    
                    if ('' !== $errorAsString)
                    {
                        $errors[$fieldName] = $errorAsString;
                    }
                }
                                
                $record['errors'] = $errors;
            }
            
            print_r($record);
            
            print_r($form->getData());
        }         
    }            
}

