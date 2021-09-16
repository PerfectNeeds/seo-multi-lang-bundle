<?php

namespace PN\SeoBundle\Form;

use PN\SeoBundle\Form\Translation\SeoTranslationType;
use PN\SeoBundle\Form\Type\SeoSocialsType;
use PN\SeoBundle\Service\SeoFormTypeService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use VM5\EntityTranslationsBundle\Form\Type\TranslationsType;

class SeoType extends AbstractType
{

    protected $em;
    protected $container;
    protected $seoClass;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->seoClass = $container->getParameter("pn_seo_class");
        $this->em = $container->get("doctrine")->getManager();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $multipleLanguages = $options['multipleLanguages'];

        $builder
            ->add('title', TextType::class, ["constraints" => [new NotNull()], "required" => true])
            ->add('slug', TextType::class, ["constraints" => [new NotNull()], "required" => true])
            ->add('metaDescription')
            ->add('focusKeyword')
            ->add('metaKeyword')
            ->add('metaTags')
            ->add('state')
            ->add("seoSocials", SeoSocialsType::class, [
                "label" => false,
                "multipleLanguages" => $multipleLanguages,
            ]);

        if ($multipleLanguages) {
            $builder->add('translations', TranslationsType::class, [
                'entry_type' => SeoTranslationType::class,
                "label" => false,
                'entry_language_options' => [
                    'en' => [
                        'required' => true,
                    ],
                ],
            ]);
        }
        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
    }

    public function onSubmit(FormEvent $event)
    {
        $seoEntity = $event->getData();
        $form = $event->getForm();
        $parentEntity = $form->getRoot()->getData();

        $generatedSlug = $this->container->get(SeoFormTypeService::class)->checkAndGenerateSlug($parentEntity,
            $seoEntity);
        $seoEntity->setSlug($generatedSlug);

        if ($seoEntity->getSeoBaseRoute() == null) {
            $seoBaseRoute = $this->em->getRepository('PNSeoBundle:SeoBaseRoute')->findByEntity($parentEntity);
            $seoEntity->setSeoBaseRoute($seoBaseRoute);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->seoClass,
            "label" => false,
            "seoSocials" => [],
            "multipleLanguages" => true,
        ));
    }

    public function getBlockPrefix()
    {
        return 'pn_bundle_seobundle_seo';
    }

}
