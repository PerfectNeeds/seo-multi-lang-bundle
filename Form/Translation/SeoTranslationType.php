<?php

namespace PN\SeoBundle\Form\Translation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use PN\SeoBundle\Service\SeoFormTypeService;

class SeoTranslationType extends AbstractType {

    protected $em;
    protected $container;
    protected $seoTranslationClass;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $container->get("doctrine")->getManager();
        $this->seoTranslationClass = $container->getParameter("pn_seo_translation_class");
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title')
                ->add('slug')
                ->add('metaDescription')
                ->add('focusKeyword')
                ->add('metaKeyword')
                ->add('metaTags')
                ->add('state')
        ;
        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
    }

    public function onSubmit(FormEvent $event) {
        $seoEntity = $event->getData();
        $form = $event->getForm();
        $locale = $event->getForm()->getConfig()->getName();
        $parentEntity = $form->getRoot()->getData();
        $generatedSlug = $this->container->get(SeoFormTypeService::class)->checkAndGenerateSlug($parentEntity, $seoEntity, $locale);
        $seoEntity->getSlug($generatedSlug);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => $this->seoTranslationClass
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'pn_bundle_seobundle_seo';
    }

}
