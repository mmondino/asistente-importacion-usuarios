<?php

namespace App\Form;

use Scit\Bundle\UsuarioDomainBundle\Entity\ConjuntoAtributoInstancia;
use Scit\Bundle\UsuarioDomainBundle\Entity\Servicio;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AsignacionServicioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'servicio', 
            EntityType::class, 
            array(            
                'class' => Servicio::class,            
                'choice_label' => 'denominacion',
                'placeholder' => '',
                'constraints' => array(
                    new NotBlank()
                )                
            )
        );
        $builder->add(
            'conjuntoAtributoInstancia', 
            EntityType::class, 
            array(            
                'class' => ConjuntoAtributoInstancia::class,            
                'choice_label' => 'denominacion',
                'placeholder' => '',
                'constraints' => array(
                    new NotBlank()
                )                
            )
        );        
    }
}