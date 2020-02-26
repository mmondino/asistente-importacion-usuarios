<?php

namespace App\DataImport\Definition;

use App\DataImport\Normalizer\FileContentNormalizerInterface;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;

abstract class AbstractImportDefinition
{       
    /** @var \Doctrine\ORM\EntityManagerInterface */
    protected $entityManager;
    
    /** @var FileContentNormalizerInterface */
    protected $normalizer;
    
    /** @var FieldDefinition[] */
    protected $fieldsDefinition;
    
    public function __construct(\Doctrine\ORM\EntityManagerInterface $entityManager)
    {             
        $this->entityManager = $entityManager;
        $this->setupNormalizer();
        $this->setupFieldsDefinition();
    }
    
    abstract protected function setupNormalizer();
    
    abstract protected function setupFieldsDefinition();    
    
    public function getNormalizer(): FileContentNormalizerInterface
    {
        return $this->normalizer;
    }    
    
    /**
     * 
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        $validator = Validation::createValidator();

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory();                

        $formBuilder = $formFactory->createBuilder();
        
        foreach($this->fieldsDefinition as $fieldDefinition)
        {
            $formBuilder->add(
                $fieldDefinition->getName(), 
                $fieldDefinition->getType()['class'],
                array(
                    'property_path' => '['.$fieldDefinition->getName().']', 
                    'constraints' => $fieldDefinition->getConstraints()
                ) + $fieldDefinition->getType()['options']
            );
        }

        return $formBuilder->getForm();     
    }            
    
    /**
     * 
     * @param integer $position
     * @return FieldDefinition
     */
    protected function getFieldDefinitionByPosition($position): FieldDefinition
    {
        $fieldDefinitionToReturn = null;
        
        foreach($this->fieldsDefinition as $fieldDefinition)
        {
            if ($fieldDefinition->getPosition() == $position)
            {
                $fieldDefinitionToReturn = $fieldDefinition;
            }
        }
        
        return $fieldDefinitionToReturn;
    }
    
    /**
     * 
     * @param array $record
     * @return array
     */
    public function getRecordWithFieldNames($record = array()): array
    {
        $recordWithFieldName = array();
        
        $index = 0;
        
        foreach($record as $fieldKey => $fieldValue)
        {
            $index++;

            $fieldDefinition = $this->getFieldDefinitionByPosition($index);
            
            $recordWithFieldName[$fieldDefinition->getName()] = $fieldValue;
        }
        
        return $recordWithFieldName;
    }
    
    public function getFieldsDefinitionForUI(): array
    {
        $fieldsDefinition = array();
        
        foreach($this->fieldsDefinition as $fieldDefinition)
        {
            $fieldDefinition = array(
                'name' => $fieldDefinition->getName(),
                'label' => $fieldDefinition->getLabel(),
                'alignment' => $fieldDefinition->getValueAlignment(),
                'breakLine' => $fieldDefinition->getBreakLine()
            );
            
            $fieldsDefinition[] = $fieldDefinition;
        }
        
        return $fieldsDefinition;
    }
}

