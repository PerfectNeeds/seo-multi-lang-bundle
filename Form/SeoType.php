<?php

namespace PNSeoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotNull;
use PNSeoBundle\Form\Type\SeoSocialsType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use VM5\EntityTranslationsBundle\Form\Type\TranslationsType;
use PNSeoBundle\Form\Translation\SeoTranslationType;
use PN\Utils\General;

class SeoType extends AbstractType {

    protected $em;
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $container->get("doctrine")->getManager();
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title', TextType::class, ["constraints" => [new NotNull()], "required" => true])
                ->add('slug', TextType::class, ["constraints" => [new NotNull()], "required" => true])
                ->add('metaDescription')
                ->add('focusKeyword')
                ->add('state')
                ->add("seoSocials", SeoSocialsType::class, [
                    "label" => false
                ])
                ->add('translations', TranslationsType::class, [
                    'entry_type' => SeoTranslationType::class,
                    "label" => false,
                    'entry_language_options' => [
                        'en' => [
                            'required' => true,
                        ]
                    ],
                ])
        ;
        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
    }

    public function onSubmit(FormEvent $event) {
        $seoEntity = $event->getData();
        $form = $event->getForm();
        $parentEntity = $form->getRoot()->getData();

        $generatedSlug = $this->container->get('seo_form_type')->checkAndGenerateSlug($parentEntity, $seoEntity);
        $seoEntity->setSlug($generatedSlug);

        if ($seoEntity->getSeoBaseRoute() == null) {
            $seoBaseRoute = $this->em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($parentEntity);
            $seoEntity->setSeoBaseRoute($seoBaseRoute);
        }
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'PNSeoBundle\Entity\Seo',
            "label" => false,
            "seoSocials" => []
        ));
    }

    public function getBlockPrefix() {
        return 'pn_bundle_seobundle_seo';
    }

}
