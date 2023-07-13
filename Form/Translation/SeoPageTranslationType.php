<?php

namespace PN\SeoBundle\Form\Translation;

use PN\SeoBundle\Entity\Translation\SeoPageTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoPageTranslationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('brief');
    }


    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults(array(
            'data_class' => SeoPageTranslation::class,
        ));
    }
}
