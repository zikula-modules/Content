<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\ContentType\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Form\Type\Field\EntityTreeType;

/**
 * Table of contents form type class.
 */
class TableOfContentsType extends AbstractType
{
    use TranslatorTrait;

    /**
     * TableOfContentsType constructor.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $inclusionChoices = [
            $this->__('No') => 0,
            $this->__('Yes, unlimited') => 1,
            $this->__('Yes, limited') => 2
        ];
        $builder
            ->add('pageId', EntityTreeType::class, [
                'class' => PageEntity::class,
                'multiple' => false,
                'expanded' => false,
                'use_joins' => false,
                'placeholder' => $this->__('All pages.'),
                'required' => false,
                'label' => $this->__('Page'),
                'attr' => [
                    'title' => $this->__('Choose the start page.')
                ]
            ])
            ->add('includeSelf', CheckboxType::class, [
                'label' => $this->__('Include self into the table of contents') . ':',
                'help' => $this->__('if page isn\'t \'All pages\')'),
                'required' => false
            ])
            ->add('includeNotInMenu', CheckboxType::class, [
                'label' => $this->__('Include subpages that are not in the menus') . ':',
                'required' => false
            ])
            ->add('includeHeading', ChoiceType::class, [
                'label' => $this->__('Include heading items on pages') . ':',
                'choices' => $inclusionChoices
            ])
            ->add('includeHeadingLevel', IntegerType::class, [
                'label' => $this->__('Include heading items up to page level') . ':',
                'help' => $this->__('if heading items are included and not unlimited; select 0 to include the headings only for the selected page'),
                'attr' => [
                    'maxlength' => 10
                ],
                'required' => false
            ])
            ->add('includeSubpage', ChoiceType::class, [
                'label' => $this->__('Include subpages') . ':',
                'choices' => $inclusionChoices
            ])
            ->add('includeSubpageLevel', IntegerType::class, [
                'label' => $this->__('Include subpages into table up to level') . ':',
                'help' => $this->__('if subpages are included and not unlimited'),
                'attr' => [
                    'maxlength' => 10
                ],
                'required' => false
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_tableofcontents';
    }
}
