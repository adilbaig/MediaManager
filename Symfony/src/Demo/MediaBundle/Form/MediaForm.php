<?php

namespace Demo\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MediaForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', array('required' => true, 'label' => 'Media'))
            ->add('title', 'text', array('required' => false))
            ->add('album', 'text', array('required' => true))
            ->add('genre', 'choice', array('required' => true, 'empty_value' => 'Choose an option', 'choices' => $this->getGenres()))
            ->add('artist', 'text', array('required' => true))
            ->add('submit', 'submit');
    }

    public function getGenres()
    {
        $arr = array(
            'Acoustic',
            'Country',
            'Folk',
            'Hip hop',
            'Pop',
            'Trance',
        );
        
        return array_combine($arr, $arr);
    }
    
    public function getName()
    {
        return 'mediaform';
    }
}

