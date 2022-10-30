<?php

namespace PN\SeoBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use PN\LocaleBundle\Form\Type\TranslationsType;
use PN\SeoBundle\Form\Translation\SeoTranslationType;
use PN\SeoBundle\Form\Type\SeoSocialsType;
use PN\SeoBundle\Repository\SeoBaseRouteRepository;
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
use Symfony\Component\Validator\Constraints\NotNull;

class SeoType extends AbstractType
{

    private EntityManagerInterface $em;
    private SeoFormTypeService $seoFormTypeService;
    private SeoBaseRouteRepository $seoBaseRouteRepository;
    private $seoClass;

    public function __construct(
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $em,
        SeoFormTypeService $seoFormTypeService,
        SeoBaseRouteRepository $seoBaseRouteRepository
    ) {
        $this->seoClass = $parameterBag->get("pn_seo_class");
        $this->em = $em;
        $this->seoFormTypeService = $seoFormTypeService;
        $this->seoBaseRouteRepository = $seoBaseRouteRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $multipleLanguages = $options['multipleLanguages'];

        $builder
            ->add('title', TextType::class, [
                "required" => true,
                "attr" => ["maxlength" => 255],
                "constraints" => [
                    new Length(["max" => 255]),
                    new NotNull(),
                ],
            ])
            ->add('slug', TextType::class, [
                "required" => true,
                "attr" => ["maxlength" => 255],
                "constraints" => [
                    new Length(["max" => 255]),
                    new NotNull(),
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

        $generatedSlug = $this->seoFormTypeService->checkAndGenerateSlug($parentEntity,
            $seoEntity);
        $seoEntity->setSlug($generatedSlug);

        if ($seoEntity->getSeoBaseRoute() == null) {
            $seoBaseRoute = $this->seoBaseRouteRepository->findByEntity($parentEntity);
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

    public function getBlockPrefix(): string
    {
        return 'pn_bundle_seobundle_seo';
    }

}
