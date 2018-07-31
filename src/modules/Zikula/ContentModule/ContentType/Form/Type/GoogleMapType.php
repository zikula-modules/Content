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
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;

/**
 * Google map form type class.
 */
class GoogleMapType extends AbstractType
{
    use TranslatorTrait;

    /**
     * GoogleMapType constructor.
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
        $builder
            ->add('latitude', TextType::class, [
                'label' => $this->__('Latitude') . ':',
                'help' => $this->__('(a comma-separated numeral that has a precision to 6 decimal places. For example, 40.714728)'),
                'attr' => [
                    'maxlength' => 30
                ]
            ])
            ->add('longitude', TextType::class, [
                'label' => $this->__('Longitude') . ':',
                'help' => $this->__('(a comma-separated numeral that has a precision to 6 decimal places. For example, 40.714728)'),
                'attr' => [
                    'maxlength' => 30
                ]
            ])
            ->add('zoom', RangeType::class, [
                'label' => $this->__('Zoom level') . ':',
                'help' => $this->__('(from 0 for the entire world to 21 for individual buildings)'),
                'attr' => [
                    'min' => 0,
                    'max' => 21
                ]
            ])
            ->add('mapType', ChoiceType::class, [
                'label' => $this->__('Map type') . ':',
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'choices' => [
                    $this->__('Roadmap') => 'roadmap',
                    $this->__('Satellite') => 'satellite',
                    $this->__('Hybrid') => 'hybrid',
                    $this->__('Terrain') => 'terrain'
                ],
                'expanded' => true
            ])
            ->add('height', IntegerType::class, [
                'label' => $this->__('Height of the displayed map') . ':',
                'help' => $this->__('(below 350 pixels the navigation controls will be small)'),
                'attr' => [
                    'maxlength' => 4
                ],
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('text', TextType::class, [
                'label' => $this->__('Description to be shown below the map') . ':',
                'attr' => [
                    'maxlength' => 255
                ]
            ])
            ->add('infoText', TextareaType::class, [
                'label' => $this->__('Text to be shown in the popup window of the marker') . ':',
                'help' => $this->__('(can contain HTML markup. Leave this field empty for disabling the popup window.'),
                'required' => false
            ])
            ->add('streetViewControl', CheckboxType::class, [
                'label' => $this->__('Display the streetview control') . ':',
                'required' => false
            ])
            ->add('directionsLink', CheckboxType::class, [
                'label' => $this->__('Display a link to directions to this location in Google Maps') . ':',
                'required' => false
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_googlemap';
    }
}