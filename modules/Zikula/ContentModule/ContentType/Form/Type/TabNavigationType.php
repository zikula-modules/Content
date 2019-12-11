<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\ContentType\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\Common\Content\AbstractContentFormType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\Common\Translator\TranslatorInterface;

/**
 * Tab navigation form type class.
 */
class TabNavigationType extends AbstractContentFormType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $context = isset($options['context']) ? $options['context'] : ContentTypeInterface::CONTEXT_EDIT;
        if (ContentTypeInterface::CONTEXT_EDIT == $context) {
            $builder
                ->add('contentItemIds', TextType::class, [
                    'label' => $this->__('Content item IDs') . ':',
                    'help' => $this->__('A list of Content item IDs semicolon separated, e.g. "3;12". Make sure that the Content item IDs you select already exist. You can disable the individual Content items if you only want to display them in this tab navigation.')
                ])
            ;
        }
        $builder
            ->add('tabTitles', TextType::class, [
                'label' => $this->__('Tab titles') . ':',
                'help' => $this->__('Titles for the tabs, semicolon separated, e.g. "Recent News;Calender".')
            ])
        ;
        if (ContentTypeInterface::CONTEXT_EDIT == $context) {
            $builder
                ->add('tabLinks', TextType::class, [
                    'label' => $this->__('Link names') . ':',
                    'help' => $this->__('Internal named links for the tabs, semicolon separated and no spaces, e.g. "news;calendar".')
                ])
                ->add('tabType', ChoiceType::class, [
                    'label' => $this->__('Navigation type') . ':',
                    'choices' => [
                        $this->__('Tabs') => 1,
                        $this->__('Pills') => 2,
                        $this->__('Stacked pills') . ' (col-sm3/col-sm-9)' => 3
                    ]
                ])
                ->add('tabStyle', TextType::class, [
                    'label' => $this->__('Custom style class') . ':',
                    'help' => $this->__('A CSS class name that will be used on the tab navigation.'),
                    'required' => false,
                    'attr' => [
                        'maxlength' => 50
                    ]
                ])
            ;
        }
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_tabnavigation';
    }
}
