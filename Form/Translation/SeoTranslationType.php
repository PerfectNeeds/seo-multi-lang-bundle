<?php

namespace PN\SeoBundle\Form\Translation;

use Doctrine\ORM\EntityManagerInterface;
use PN\SeoBundle\Service\SeoFormTypeService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class SeoTranslationType extends AbstractType
{

    private $em;
    private $seoFormTypeService;
    private $seoTranslationClass;

    public function __construct(
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $em,
        SeoFormTypeService $seoFormTypeService
    ) {
        $this->seoFormTypeService = $seoFormTypeService;
        $this->em = $em;
        $this->seoTranslationClass = $parameterBag->get("pn_seo_translation_class");
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                "required" => false,
                "attr" => ["maxlength" => 255],
                "constraints" => [
                    new Length(["max" => 255]),
                ],
            ])
            ->add('slug', TextType::class, [
                "required" => false,
                "attr" => ["maxlength" => 255],
                "constraints" => [
                    new Length(["max" => 255]),
                ],
            ])
            ->add('focusKeyword', TextType::class, [
                "required" => false,
                "attr" => ["maxlength" => 255],
                "constraints" => [
                    new Length(["max" => 255]),
                ],
            ])
            ->add('metaKeyword', TextType::class, [
                "required" => false,
                "attr" => ["maxlength" => 255],
                "constraints" => [
                    new Length(["max" => 255]),
                ],
            ])
            ->add('metaDescription')
            ->add('metaTags')
            ->add('state');
        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
    }

    public function onSubmit(FormEvent $event)
    {
        $seoEntity = $event->getData();
        $form = $event->getForm();
        $locale = $event->getForm()->getConfig()->getName();
        $parentEntity = $form->getRoot()->getData();
        $generatedSlug = $this->seoFormTypeService->checkAndGenerateSlug($parentEntity,
            $seoEntity, $locale);
        $seoEntity->getSlug($generatedSlug);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->seoTranslationClass,
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'pn_bundle_seobundle_seo';
    }

}
