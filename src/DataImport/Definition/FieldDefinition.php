<?php

namespace App\DataImport\Definition;

class FieldDefinition
{
    /**
     * Field position
     * 
     * @var integer
     */
    protected $position;

    /**
     * Field name
     * 
     * @var string
     */
    protected $name;

    /**
     * Field label
     * 
     * @var string
     */
    protected $label;

    /**
     * Field value alignment, for ui purposes
     * 
     * @var string
     */
    protected $valueAlignment;

    /**
     *
     * @var \Symfony\Component\Form\AbstractType
     */
    protected $type;

    /**
     *
     * @var boolean
     */
    protected $breakLine;

    /**
     * Field constraints
     * 
     * @var class
     */
    protected $constraints = array();

    public function __construct($position, $name, $label, $valueAlignment, $breakLine, $type, array $constraints)
    {
        $this->position = $position;
        $this->name = $name;
        $this->label = $label;
        $this->valueAlignment = $valueAlignment;
        $this->breakLine = $breakLine;
        $this->type = $type;
        $this->constraints = $constraints;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function getValueAlignment()
    {
        return $this->valueAlignment;
    }

    public function setValueAlignment($valueAlignment)
    {
        $this->valueAlignment = $valueAlignment;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;
        return $this;
    }

    public function addConstraint(Symfony\Component\Validator\Constraint $constraint)
    {
        $this->constraints[] = $constraint;
        return $this;
    }

    public function getBreakLine()
    {
        return $this->breakLine;
    }

    public function setBreakLine($breakLine)
    {
        $this->breakLine = $breakLine;
        return $this;
    }
}