<?php

namespace App\ImportCase\ImportProfesionales;

use App\DataImport\Definition\AbstractImportDefinition;
use App\DataImport\Definition\FieldDefinition;
use App\DataImport\Definition\ImportDefinitionInterface;
use App\DataImport\Normalizer\XlsxFileContentNormalizer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImportProfesionalesDefinition extends AbstractImportDefinition implements ImportDefinitionInterface
{               
    protected function setupNormalizer()
    {        
        $options = array(            
            'from_column' => 'A',
            'to_column' => 'F',
            'from_row' => 2
        );

        $this->normalizer = new XlsxFileContentNormalizer($options);       
    }    
    
    protected function setupFieldsDefinition()
    {
        $fieldsDefinition = array();
        
        // nombre
        $fieldDefinition = new FieldDefinition(
            1, 
            'nombre', 
            'Nombre', 
            'left',
            false,
            ['class' => TextType::class, 'options' => []], 
            [
                new NotBlank(), 
                new Length(['max' => 50])                
            ]
        );                   
        $fieldsDefinition[] = $fieldDefinition;
        
        // apellido
        $fieldDefinition = new FieldDefinition(
            2, 
            'apellido', 
            'Apellido', 
            'left',
            false,
            ['class' => TextType::class, 'options' => []], 
            [new Length(['max' => 50])]
        );                   
        $fieldsDefinition[] = $fieldDefinition;        
           
        // documento numero
        $fieldDefinition = new FieldDefinition(
            3, 
            'documento_numero', 
            'N° Documento',
            'left',
            false,
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
            false,
            ['class' => TextType::class, 'options' => []],
            [new NotBlank(), new Length(['max' => 50])]
        );                   
        $fieldsDefinition[] = $fieldDefinition;    
        
        // matrícula
        $fieldDefinition = new FieldDefinition(
            5, 
            'matricula', 
            'Matrícula',
            'left',
            false,
            ['class' => TextType::class, 'options' => []],
            [new Length(['max' => 50])]
        );                   
        $fieldsDefinition[] = $fieldDefinition;          

        // email
        $fieldDefinition = new FieldDefinition(
            6, 
            'email', 
            'Email',
            'left',
            false,
            ['class' => TextType::class, 'options' => []],
            [
                new Length(['max' => 50]), 
                new Email(),                 
                new Callback([
                    'callback' => [$this, 'checkUniqueEmail'],
                ])
            ]
        );                   
        $fieldsDefinition[] = $fieldDefinition;          
        
        $this->fieldsDefinition = $fieldsDefinition;
    }    
    
    public function checkUniqueEmail($data, \Symfony\Component\Validator\Context\ExecutionContextInterface $context)
    {
        $form = $context->getRoot();
        $record = $form->getData();
        
        if ($data)
        {          
            $usuarios = $this->entityManager
                ->getRepository('ScitUsuarioDomainBundle:Usuario')
                ->findBy(array('email' => $data));
            
            foreach($usuarios as $usuario)
            {
                if ($usuario->getIdentificacion() != $record['cuit'])
                {
                    $context->buildViolation('Email duplicado')
                        ->atPath('email')
                        ->addViolation();                    
                }
            }            
        }
    }
}

