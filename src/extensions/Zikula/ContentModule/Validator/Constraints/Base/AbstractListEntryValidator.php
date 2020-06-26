<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Validator\Constraints\Base;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Translation\TranslatorTrait;
use Zikula\ContentModule\Helper\ListEntriesHelper;
use Zikula\ContentModule\Validator\Constraints\ListEntry;

/**
 * List entry validator.
 */
abstract class AbstractListEntryValidator extends ConstraintValidator
{
    use TranslatorTrait;

    /**
     * @var ListEntriesHelper
     */
    protected $listEntriesHelper;

    public function __construct(TranslatorInterface $translator, ListEntriesHelper $listEntriesHelper)
    {
        $this->setTranslator($translator);
        $this->listEntriesHelper = $listEntriesHelper;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ListEntry) {
            throw new UnexpectedTypeException($constraint, ListEntry::class);
        }
        if (null === $value) {
            return;
        }

        if ('workflowState' === $constraint->propertyName && in_array($value, ['initial', 'deleted'], true)) {
            return;
        }

        $listEntries = $this->listEntriesHelper->getEntries($constraint->entityName, $constraint->propertyName);
        $allowedValues = [];
        foreach ($listEntries as $entry) {
            $allowedValues[] = $entry['value'];
        }

        if (!$constraint->multiple) {
            // single-valued list
            if ('' !== $value && !in_array($value, $allowedValues/*, true*/)) {
                $this->context->buildViolation(
                    $this->trans(
                        'The value "%value%" is not allowed for the "%property%" property.',
                        [
                            '%value%' => $value,
                            '%property%' => $constraint->propertyName
                        ],
                        'validators'
                    )
                )->addViolation();
            }

            return;
        }

        // multi-values list
        $selected = explode('###', $value);
        foreach ($selected as $singleValue) {
            if ('' === $singleValue) {
                continue;
            }
            if (!in_array($singleValue, $allowedValues/*, true*/)) {
                $this->context->buildViolation(
                    $this->trans(
                        'The value "%value%" is not allowed for the "%property%" property.',
                        [
                            '%value%' => $singleValue,
                            '%property%' => $constraint->propertyName
                        ],
                        'validators'
                    )
                )->addViolation();
            }
        }

        $count = count($selected);

        if (null !== $constraint->min && $count < $constraint->min) {
            $this->context->buildViolation(
                $this->translator->trans(
                    'You must select at least "%limit%" choice.|You must select at least "%limit%" choices.',
                    [
                        '%count%' => $count,
                        '%limit%' => $constraint->min
                    ],
                    'validators'
                )
            )->addViolation();
        }
        if (null !== $constraint->max && $count > $constraint->max) {
            $this->context->buildViolation(
                $this->translator->trans(
                    'You must select at most "%limit%" choice.|You must select at most "%limit%" choices.',
                    [
                        '%count%' => $count,
                        '%limit%' => $constraint->max
                    ],
                    'validators'
                )
            )->addViolation();
        }
    }
}
