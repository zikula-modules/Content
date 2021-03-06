<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translation\Extractor\Annotation\Ignore;
use Translation\Extractor\Annotation\Translate;
use Zikula\ContentModule\Form\Type\Field\TranslationType;
use Zikula\ContentModule\Helper\TranslatableHelper;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;

/**
 * Translation form type implementation class.
 */
class TranslateType extends AbstractType
{
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * @var TranslatableHelper
     */
    protected $translatableHelper;

    public function __construct(
        VariableApiInterface $variableApi,
        TranslatableHelper $translatableHelper
    ) {
        $this->variableApi = $variableApi;
        $this->translatableHelper = $translatableHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $hasContentData = false;
        if ('page' === $options['mode']) {
            $this->addPageFields($builder, $options);
        } elseif ('item' === $options['mode']) {
            $this->addItemFields($builder, $options);
            $hasContentData = null !== $options['content_type']
                && 0 < count($options['content_type']->getTranslatableDataFields())
            ;
            if ($hasContentData) {
                $editFormClass = $options['content_type']->getEditFormClass();
                if (null !== $editFormClass && '' !== $editFormClass && class_exists($editFormClass)) {
                    $builder->add(
                        'contentData',
                        $editFormClass,
                        $options['content_type']->getEditFormOptions(ContentTypeInterface::CONTEXT_TRANSLATION)
                    );
                }
            }
        }

        $translatableFields = [];
        $supportedLanguages = $this->translatableHelper->getSupportedLanguages('page');
        if (is_array($supportedLanguages) && 1 < count($supportedLanguages)) {
            $currentLanguage = $this->translatableHelper->getCurrentLanguage();
            if ('page' === $options['mode']) {
                $translatableFields = $this->translatableHelper->getTranslatableFields('page');
            } elseif ('item' === $options['mode']) {
                if ($hasContentData) {
                    $translatableFields = ['contentData', 'additionalSearchText'];
                } else {
                    $translatableFields = ['additionalSearchText'];
                }
            }
            $mandatoryFields = $this->translatableHelper->getMandatoryFields('page');
            foreach ($supportedLanguages as $language) {
                if ($language === $currentLanguage) {
                    continue;
                }
                $builder->add('translations' . $language, TranslationType::class, [
                    'fields' => $translatableFields,
                    'mandatory_fields' => $mandatoryFields[$language],
                    'values' => $options['translations'][$language] ?? [],
                ]);
            }
        }

        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds translatable page fields.
     */
    public function addPageFields(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('title', TextType::class, [
            'label' => 'Title:',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => 'Enter the title of the page.',
            ],
            'required' => true,
        ]);
        $builder->add('metaDescription', TextType::class, [
            'label' => 'Meta description:',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => 'Enter the meta description of the page.',
            ],
            'required' => false,
        ]);
        if ($this->variableApi->get('ZikulaContentModule', 'enableOptionalString1')) {
            $builder->add('optionalString1', TextType::class, [
                'label' => 'Optional string 1:',
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'class' => '',
                    'title' => 'Enter the optional string 1 of the page.',
                ],
                'required' => false,
            ]);
        }
        if ($this->variableApi->get('ZikulaContentModule', 'enableOptionalString2')) {
            $builder->add('optionalString2', TextType::class, [
                'label' => 'Optional string 2:',
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'class' => '',
                    'title' => 'Enter the optional string 2 of the page.',
                ],
                'required' => false,
            ]);
        }
        if ($this->variableApi->get('ZikulaContentModule', 'enableOptionalText')) {
            $builder->add('optionalText', TextareaType::class, [
                'label' => 'Optional text:',
                'help' => 'Note: this value must not exceed %amount% characters.',
                'help_translation_parameters' => [
                    '%amount%' => 2000,
                ],
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 2000,
                    'class' => '',
                    'title' => 'Enter the optional text of the page.',
                ],
                'required' => false,
            ]);
        }
        $builder->add('slug', TextType::class, [
            'label' => 'Permalink:',
            'required' => true,
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => 'validate-unique',
            ],
        ]);
    }

    /**
     * Adds translatable content item fields.
     */
    public function addItemFields(FormBuilderInterface $builder, array $options = []): void
    {
        $helpText = /** @Translate */'You may enter any text which will be used during the site search to find this element.';
        $builder->add('additionalSearchText', TextType::class, [
            'label' => 'Additional search text:',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                /** @Ignore */
                'title' => $helpText,
            ],
            'required' => false,
            /** @Ignore */
            'help' => $helpText,
        ]);
    }

    /**
     * Adds submit buttons.
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options = []): void
    {
        if ('page' !== $options['mode']) {
            $builder->add('prev', SubmitType::class, [
                'label' => 'Previous',
                'icon' => 'fa-arrow-left',
            ]);
        }
        $builder->add('next', SubmitType::class, [
            'label' => 'Next',
            'icon' => 'fa-arrow-right',
            'attr' => [
                'class' => 'btn-primary',
            ],
        ]);
        $builder->add('skip', SubmitType::class, [
            'label' => 'Skip',
            'icon' => 'fa-exchange',
        ]);
        $builder->add('saveandquit', SubmitType::class, [
            'label' => 'Save and quit',
            'icon' => 'fa-floppy-o',
        ]);
        $builder->add('cancel', SubmitType::class, [
            'label' => 'Cancel',
            'validate' => false,
            'icon' => 'fa-times',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_translate';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'page',
                'mode' => 'page',
                'content_type' => null,
                'translations' => [],
            ])
            ->setRequired(['mode'])
            ->setAllowedTypes('mode', 'string')
            ->setAllowedTypes('translations', 'array')
            ->setAllowedValues('mode', ['page', 'item'])
        ;
    }
}
