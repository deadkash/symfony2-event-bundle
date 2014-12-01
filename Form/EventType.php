<?php

namespace Sp\EventBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
    /** @var array  */
    private $types = array();

    /**
     * @param array $types
     */
    public function __construct($types = array())
    {
        $this->types = $types;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'Название'))
            ->add('type', 'choice', array(
                'label' => 'Тип события',
                'choices' => $this->types,
                'empty_data' => '',
                'empty_value' => ''
            ))
            ->add('enabled', null, array('label' => 'Активно', 'required' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sp\EventBundle\Entity\Event'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sp_eventbundle_event';
    }
}
