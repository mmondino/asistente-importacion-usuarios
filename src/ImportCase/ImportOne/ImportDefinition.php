<?php

namespace App\ImportCase\ImportOne;

use App\DataImport\Definition\AbstractImportDefinition;
use App\DataImport\Definition\FieldDefinition;
use App\DataImport\Definition\ImportDefinitionInterface;
use App\DataImport\Normalizer\XlsxFileContentNormalizer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImportDefinition extends AbstractImportDefinition implements ImportDefinitionInterface
{               
    protected function setupNormalizer()
    {        
        $options = array(            
            'from_column' => 'A',
            'to_column' => 'E',
            'from_row' => 2
        );

        $this->normalizer = new XlsxFileContentNormalizer($options);       
    }    
    
    protected function setupFieldsDefinition()
    {
        $fieldsDefinition = array();
        
        // apellido
        $fieldDefinition = new FieldDefinition(
            1, 
            'apellido', 
            'Apellido', 
            'left',
            ['class' => TextType::class, 'options' => []], 
            [new NotBlank(), new Length(['max' => 50])]
        );                   
        $fieldsDefinition[] = $fieldDefinition;
        
        // nombre
        $fieldDefinition = new FieldDefinition(
            2, 
            'nombre', 
            'Nombre', 
            'left',
            ['class' => TextType::class, 'options' => []], 
            [new NotBlank(), new Length(['max' => 5])]
        );                   
        $fieldsDefinition[] = $fieldDefinition;        
        
        // dni
        $fieldDefinition = new FieldDefinition(
            3, 
            'dni', 
            'DNI', 
            'left',
            ['class' => TextType::class, 'options' => []], 
            [new NotBlank(), new Length(['max' => 50])]
        );                   
        $fieldsDefinition[] = $fieldDefinition;        

        // cuit
        $fieldDefinition = new FieldDefinition(
            4, 
            'cuit', 
            'CUIT',
            'left',
            ['class' => TextType::class, 'options' => []],
            [new NotBlank(), new Length(['max' => 50])]
        );                   
        $fieldsDefinition[] = $fieldDefinition;                
        
        // email
        $fieldDefinition = new FieldDefinition(
            5, 
            'email', 
            'Email',
            'left',
            ['class' => TextType::class, 'options' => []], 
            [new NotBlank(), new Email()]
        );                   
        $fieldsDefinition[] = $fieldDefinition;        
        
        $this->fieldsDefinition = $fieldsDefinition;
    }    
}

