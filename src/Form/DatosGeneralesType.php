<?php

namespace App\Form;

use Scit\Bundle\UsuarioDomainBundle\Entity\Profesion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DatosGeneralesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'profesion', 
            EntityType::class, 
            array(            
                'class' => Profesion::class,            
                'choice_label' => 'denominacion',                
                'placeholder' => '',
                'constraints' => array(
                    new NotBlank()
                )
            )
        );

        $builder->add('asignacionServicios', CollectionType::class, array(
            'entry_type' => AsignacionServicioType::class,
            'entry_options' => array('label' => false),
            'allow_add' => true,
        ));        
    }
}