<?php

namespace App\DataImport\Normalizer;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractFileContentNormalizer
{
    protected $options = array();
    
    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);       
    }    
    
    abstract protected function configureOptions(OptionsResolver $resolver);
}

