<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Scit\Bundle\UsuarioDomainBundle\Entity\Usuario;
use Scit\Bundle\UsuarioDomainBundle\Entity\UsuarioServicioAsignado;
use Scit\Bundle\UsuarioDomainBundle\Entity\PersonaFisica;
use Scit\Bundle\UsuarioDomainBundle\Entity\PersonaFisicaProfesion;

class ImportadorProfesionales
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * 
     * @param array $records
     * 
     *     - nombre
     *     - apellido
     *     - documento_numero
     *     - cuit
     *     - matricula
     *     - email
     */
    public function importar($profesion, $serviciosAAsignar, $records)
    {
        $em = $this->entityManager;
        
        $registrosProcesados = 0;
        
        try {                        

            $em->getConnection()->beginTransaction();           
            
            foreach ($records as $k => $record)
            {
                // Busco la persona
                $personaFisica = $em
                    ->getRepository('ScitUsuarioDomainBundle:PersonaFisica')
                    ->findOneBy(array('cuit' => $record['cuit']));

                if (null == $personaFisica)
                {
                    $personaFisica = new PersonaFisica();
                    $personaFisica->setNombre(trim($record['nombre']));
                    $personaFisica->setApellido(trim($record['apellido']));
                    $personaFisica->setCuit(trim($record['cuit']));
                    $personaFisica->setDocumentoNumero(trim($record['documento_numero']));
                    // Forzado a DNI
                    $personaFisica->setDocumentoTipo(1);
                }

                // Busco la profesion asociada
                $personaFisicaProfesion = $em
                    ->getRepository('ScitUsuarioDomainBundle:PersonaFisicaProfesion')
                    ->findOneBy(
                        array(
                            'personaFisica' => $personaFisica->getId(),
                            'profesion' => $profesion->getId()
                        )
                );

                // Si la persona no tiene asignada una profesión se crea
                if (null == $personaFisicaProfesion)
                {
                    $personaFisicaProfesion = new PersonaFisicaProfesion();
                    $personaFisicaProfesion->setMatricula(trim($record['matricula']));
                    $personaFisicaProfesion->setProfesion($profesion);
                    $personaFisicaProfesion->setPersonaFisica($personaFisica);

                    $personaFisica->addProfesion($personaFisicaProfesion);
                }

                // Busco el usuario por cuit       
                $usuario = $em
                    ->getRepository('ScitUsuarioDomainBundle:Usuario')
                    ->findOneBy(array('identificacion' => $record['cuit']));

                // Si no existe un usuario con ese cuit busco el usuario por su email
                if (null == $usuario)
                {
                    $usuario = $em
                        ->getRepository('ScitUsuarioDomainBundle:Usuario')
                        ->findOneBy(array('email' => trim($record['email'])));
                }
                
                // Si no se encontró el usuario ni por cuit ni por email
                // se crea
                if (null == $usuario)
                {
                    $usuario = new Usuario();
                    $usuario->setIdentificacion(trim($record['cuit']));
                    $usuario->setEmail(trim($record['email']));
                    $usuario->setHabilitado(true);
                    $usuario->setPersona($personaFisica);

                    $em->persist($usuario);
                }
                else
                {
                    // Si no es email
                    if (false == strpos($usuario->getEmail(), '@'))
                    {
                        $usuario->setEmail(trim($record['email']));
                        $em->persist($usuario);
                    }
                }

                // Asigno los servicios
                
                $serviciosAsignados = array();
                
                foreach($serviciosAAsignar as $servicioAAsignar)
                {
                    // Busco la vinculación del usuario al servicio
                    $usuarioServicioAsignado = $em
                        ->getRepository('ScitUsuarioDomainBundle:UsuarioServicioAsignado')
                         ->findOneBy(
                            array(
                                'servicio' => $servicioAAsignar['servicio']->getId(),
                                'usuario' => $usuario->getId()
                            )
                    );

                    if (null == $usuarioServicioAsignado)
                    {
                        $usuarioServicioAsignado = new UsuarioServicioAsignado();
                        $usuarioServicioAsignado->setServicio($servicioAAsignar['servicio']);
                        $usuarioServicioAsignado->setUsuario($usuario);                        
                    }
                    
                    $usuarioServicioAsignado->setConjuntoAtributoInstancia($servicioAAsignar['conjuntoAtributoInstancia']);
                    
                    $serviciosAsignados[] = $usuarioServicioAsignado;
                }

                $personaFisica->addUsuario($usuario);

                foreach($serviciosAsignados as $servicioAsignado)
                {
                    $em->persist($servicioAsignado);
                }
                
                $em->persist($personaFisica);
                $em->persist($personaFisicaProfesion);

                $registrosProcesados++;
            }

            $em->flush();

            $em->commit();    
            
            return $registrosProcesados;
        }
        catch (Exception $e)
        {           
            $em->rollback();
            
            throw $e;
        }        
    }
}