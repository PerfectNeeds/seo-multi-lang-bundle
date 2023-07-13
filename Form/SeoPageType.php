<?php

namespace PN\SeoBundle\Form;

use App\CustomBundle\Form\Translation\MaterialTranslationType;
use PN\LocaleBundle\Form\Type\TranslationsType;
use PN\SeoBundle\Entity\SeoPage;
use PN\SeoBundle\Form\Translation\SeoPageTranslationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SeoPageType extends AbstractType
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('title')
            ->add('brief')
            ->add('seo', SeoType::class)
            ->add('translations', TranslationsType::class, [
                'entry_type' => SeoPageTranslationType::class,
                //                    'query_builder' => function(EntityRepository $er) {
                //                        return $er->createQueryBuilder('languages')
                //                                ->where("languages.locale = 'fr'");
                //                    }, // optional
                "label" => false,
                'entry_language_options' => [
                    'en' => [
                        'required' => true,
                    ],
                ],
            ]);

        if ($this->authorizationChecker->isGranted("ROLE_SUPER_ADMIN")) {
            $builder->add('type');
        }
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults(array(
            'data_class' => SeoPage::class
        ));
    }
}
