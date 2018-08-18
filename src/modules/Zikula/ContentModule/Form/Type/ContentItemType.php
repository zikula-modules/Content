<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Helper\ListEntriesHelper;

/**
 * Content item editing form type implementation class.
 */
class ContentItemType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ListEntriesHelper
     */
    private $listHelper;

    /**
     * @var string
     */
    private $stylingClasses;

    /**
     * ContentItemType constructor.
     *
     * @param TranslatorInterface $translator
     * @param ListEntriesHelper $listHelper
     * @param string stylingClasses
     */
    public function __construct(
        TranslatorInterface $translator,
        ListEntriesHelper $listHelper,
        $stylingClasses
    ) {
        $this->setTranslator($translator);
        $this->listHelper = $listHelper;
        $this->stylingClasses = $stylingClasses;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (null !== $options['content_type']) {
            $editFormClass = $options['content_type']->getEditFormClass();
            if (null !== $editFormClass && '' !== $editFormClass && class_exists($editFormClass)) {
                $builder->add('contentData', $editFormClass, $options['content_type']->getEditFormOptions(ContentTypeInterface::CONTEXT_EDIT));
            }
        }
        $builder->add('active', CheckboxType::class, [
            'label' => $this->__('Active') . ':',
            'attr' => [
                'title' => $this->__('active ?')
            ],
            'required' => false,
        ]);

        $builder->add('activeFrom', DateTimeType::class, [
            'label' => $this->__('Active from') . ':',
            'attr' => [
                'class' => 'validate-daterange-page'
            ],
            'required' => false,
            'empty_data' => '',
            'with_seconds' => true,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text'
        ]);

        $builder->add('activeTo', DateTimeType::class, [
            'label' => $this->__('Active to') . ':',
            'attr' => [
                'class' => 'validate-daterange-page'
            ],
            'required' => false,
            'empty_data' => '',
            'with_seconds' => true,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text'
        ]);

        $listEntries = $this->listHelper->getEntries('contentItem', 'scope');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('scope', ChoiceType::class, [
            'label' => $this->__('Scope') . ':',
            'empty_data' => '1',
            'attr' => [
                'title' => $this->__('Choose the scope.')
            ],
            'required' => true,
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);

        $choices = [];
        $userClasses = explode("\n", $this->stylingClasses);
        foreach ($userClasses as $class) {
            list($value, $text) = explode('|', $class);
            $value = trim($value);
            $text = trim($text);
            if (!empty($text) && !empty($value)) {
                $choices[$text] = $value;
            }
        }

        $builder->add('stylingClasses', ChoiceType::class, [
            'label' => $this->__('Styling classes') . ':',
            'empty_data' => [],
            'attr' => [
                'title' => $this->__('Choose any additional styling classes.')
            ],
            'required' => false,
            'choices' => $choices,
            'multiple' => true
        ]);

        $builder->add('additionalSearchText', TextType::class, [
            'label' => $this->__('Additional search text') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'title' => $this->__('You may enter any text which will be used during the site search to find this element.')
            ],
            'required' => false,
            'help' => $this->__('You may enter any text which will be used during the site search to find this element.')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contentitem';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContentItemEntity::class,
            'content_type' => null
        ]);
    }
}
