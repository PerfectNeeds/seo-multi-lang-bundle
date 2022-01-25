<?php

namespace PN\SeoBundle\Form;

use PN\SeoBundle\Entity\SeoBaseRoute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoBaseRouteType extends AbstractType {

    private $entitiesNames = [];

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->entitiesNames = $options['entitiesNames'];

        $builder
                ->add('entityName', ChoiceType::class, [
                    'placeholder' => 'Choose an option',
                    'choices' => $this->entitiesNames
                ])
                ->add('baseRoute');
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => SeoBaseRoute::class,
            'entitiesNames' => FALSE,
        ));
    }
}
