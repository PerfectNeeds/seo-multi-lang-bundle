<?php

namespace PNSeoBundle\Form\Translation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use PN\Utils\General;

class SeoTranslationType extends AbstractType {

    protected $em;
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $container->get("doctrine")->getManager();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title')
                ->add('slug')
                ->add('metaDescription')
                ->add('focusKeyword')
                ->add('state')
        ;
        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
    }

    public function onSubmit(FormEvent $event) {
        $seoEntity = $event->getData();
        $form = $event->getForm();
        $locale = $event->getForm()->getConfig()->getName();
        $parentEntity = $form->getRoot()->getData();
        $generatedSlug = $this->container->get('seo_form_type')->checkAndGenerateSlug($parentEntity, $seoEntity, $locale);
        $seoEntity->getSlug($generatedSlug);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => \PNSeoBundle\Entity\Translation\SeoTranslation::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'pn_bundle_seobundle_seo';
    }

}
