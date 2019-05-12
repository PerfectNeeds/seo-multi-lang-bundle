<?php

namespace PN\SeoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoBaseRouteType extends AbstractType {

    private $entitiesNames = [];

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->entitiesNames = $options['entitiesNames'];

        $builder
                ->add('entityName', ChoiceType::class, [
                    'placeholder' => 'Choose an option',
                    'choices' => $this->entitiesNames
                ])
                ->add('baseRoute');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'PN\SeoBundle\Entity\SeoBaseRoute',
            'entitiesNames' => FALSE,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'pn_bundle_seobundle_seobaseroute';
    }

}
