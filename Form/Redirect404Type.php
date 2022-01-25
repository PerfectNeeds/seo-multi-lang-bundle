<?php

namespace PN\SeoBundle\Form;

use PN\SeoBundle\Entity\Redirect404;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Redirect404Type extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', UrlType::class)
            ->add('to', UrlType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Redirect404::class,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'Redirect404';
    }

}
