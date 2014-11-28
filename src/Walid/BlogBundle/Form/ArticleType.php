<?php

namespace Walid\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', 'text')
        ->add('content', 'textarea', array('required' => false))
        ->add('image', new ImageType(), array('required' => false))
        ->add('date','date')
        ->add('categories', 'entity', array(
            'label' => 'select class',
            'empty_value' => 'Choose a group',
            'empty_data' => null,
            'class'    => 'WalidBlogBundle:Category',
            'property' => 'name',
            'multiple' => true,
            'required' => false,
            ))
        ->add('save', 'submit')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Walid\BlogBundle\Entity\Article'
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'walid_blogbundle_article';
    }
}
