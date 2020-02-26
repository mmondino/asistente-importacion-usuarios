<?php

namespace App\Controller;

use App\DataImport\Importer\Importer;
use App\Form\DatosGeneralesType;
use App\ImportCase\ImportProfesionales\ImportProfesionalesDefinition;
use App\Service\FileUploader;
use App\Service\ImportadorProfesionales;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{  
    /**
     * @Route("/admin/import/wizard", name="admin_import_wizard", methods="get")
     */
    public function wizard(Request $request, ImportProfesionalesDefinition $importDefinition)
    {
        //$importDefinition = new ImportProfesionalesDefinition();                                
        
        $form = $this->createForm(
            DatosGeneralesType::class, 
            array(
                'asignacionServicios' => array(
                    array('servicio' => null, 'conjuntoAtributoInstancia' => null),
                    array('servicio' => null, 'conjuntoAtributoInstancia' => null),
                    array('servicio' => null, 'conjuntoAtributoInstancia' => null),
                    array('servicio' => null, 'conjuntoAtributoInstancia' => null),
                    array('servicio' => null, 'conjuntoAtributoInstancia' => null)
                )
            )
        );        
        
        $fieldsDefinition = $importDefinition->getFieldsDefinitionForUI();
        
        return $this->render(
            'import/wizard.html.twig', 
            array(
                'fieldsDefinition' => $fieldsDefinition,
                'form' => $form->createView()
            )
        );       
    }
    
    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }    
    
    /**
     * @Route("/admin/import/step0", name="admin_import_step0", methods="post")
     */
    public function step0(Request $request)
    {    
        $form = $this->createForm(DatosGeneralesType::class);
        
        $form->handleRequest($request);        
        
        $errors = array();
        
        if ($form->isSubmitted() && !$form->isValid())
        {
            $errors = $this->getErrorsFromForm($form);
        }
        
        $response = array(
            'errors' => $errors,
            'errores_generales' => count($errors) > 0 ? array('Debe completar todos los campos'): null,
            'no_avanzar_al_siguiente_paso' => count($errors) > 0
        );
        
        return $this->json($response);      
    }    
    
    /**
     * @Route("/import/step1", name="admin_import_step1", methods="post")
     */
    public function step1(Request $request, FileUploader $fileUploader, ImportProfesionalesDefinition $importDefinition)
    {        
        $file = $request->files->get('file');
        
        if (!$file)
        {
            $response = array(
                'no_avanzar_al_siguiente_paso' => true,
                'errores_generales' => array('Debe seleccionar un archivo')
            );

            return $this->json($response);            
        }
        elseif (!in_array($file->getClientOriginalExtension(), array('xls', 'xlsx')))
        {
            $response = array(
                'no_avanzar_al_siguiente_paso' => true,
                'errores_generales' => array('Debe seleccionar un archivo xls ó xlsx. Tipo de archivo seleccionado: ' . $file->getClientOriginalExtension())
            );

            return $this->json($response);              
        }
        
        $fileName = $fileUploader->upload($file);
        
        $filePath = sprintf(
            '%s%s%s',
            $fileUploader->getTargetDirectory(),
            DIRECTORY_SEPARATOR,
            $fileName
        );
        
        //$importDefinition = new ImportProfesionalesDefinition();

        $importer = new Importer();
        
        $erroresGenerales = array();
        $noAvanzarAlSiguientePaso = false;
        
        $records = array();
        
        try
        {        
            $records = $importer->previewData($importDefinition, $filePath);
        }
        catch(DomainException $e)
        {
            $noAvanzarAlSiguientePaso = true;
            $erroresGenerales[] = $e->getMessage();
        }
        catch(\Exception $e)
        {
            $noAvanzarAlSiguientePaso = true;
            $erroresGenerales[] = $e->getMessage();
        }        
        
        $recordsIndexWithErrors = array();
        
        foreach($records as $key => $record)
        {
            if (count($record['errors']) > 0)
            {
                $recordsIndexWithErrors[] = $key;
            }
        }
        
        $response = array(
            'no_avanzar_al_siguiente_paso' => $noAvanzarAlSiguientePaso,
            'errores_generales' => $erroresGenerales,
            'records' => $records, 
            'records_index_with_errors' => $recordsIndexWithErrors
        );
        
        return $this->json($response);
    }
    
    /**
     * @Route("/import/step2", name="admin_import_step2", methods="post")
     */
    public function step2(Request $request, FileUploader $fileUploader, ImportProfesionalesDefinition $importDefinition, ImportadorProfesionales $importadorProfesionales)
    {
        $form = $this->createForm(DatosGeneralesType::class);        
        
        $form->handleRequest($request);        
        
        $errors = array();
        
        if ($form->isSubmitted() && !$form->isValid())
        {
            $errors = $this->getErrorsFromForm($form);
            
        
            $response = array(
                'errors' => $errors,
                'no_avanzar_al_siguiente_paso' => false
            );

            return $this->json($response);            
        }        
        
        $file = $request->files->get('file');
        
        $fileName = $fileUploader->upload($file);
        
        $filePath = sprintf(
            '%s%s%s',
            $fileUploader->getTargetDirectory(),
            DIRECTORY_SEPARATOR,
            $fileName
        );
        
        //$importDefinition = new ImportProfesionalesDefinition();

        $importer = new Importer();
        
        $records = $importer->previewData($importDefinition, $filePath);
        
        $recordsIndexWithErrors = array();
        
        foreach($records as $key => $record)
        {
            if (count($record['errors']) > 0)
            {
                $recordsIndexWithErrors[] = $key;
            }
        }
        
        $erroresGenerales = array();
        
        // Si existen errores de validación devueltos en la previsualización
        if (count($recordsIndexWithErrors) > 0)
        {        
            $response = array(
                'errores_generales' => $erroresGenerales,
                'records' => $records, 
                'records_index_with_errors' => $recordsIndexWithErrors
            );
        }
        else
        {                    
            // Cargo los nuevos
            
            $formData = $form->getData();
            
            $importadorProfesionales->importar(
                $formData['profesion'], 
                $formData['asignacionServicios'], 
                $records
            );                        
            
            $response = array(
                'errores_generales' => $erroresGenerales,
                'records' => $records, 
                'records_index_with_errors' => $recordsIndexWithErrors
            );            
        }
        
        return $this->json($response);
    }    
}

