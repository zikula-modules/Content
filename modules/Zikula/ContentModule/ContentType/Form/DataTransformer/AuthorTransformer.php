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

namespace Zikula\ContentModule\ContentType\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Zikula\UsersModule\Constant as UsersConstant;
use Zikula\UsersModule\Entity\RepositoryInterface\UserRepositoryInterface;
use Zikula\UsersModule\Entity\UserEntity;

/**
 * Author transformer class.
 *
 * This data transformer treats author identifiers and user objects.
 */
class AuthorTransformer implements DataTransformerInterface
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Transforms the object values to the normalised value.
     *
     * @param UserEntity|integer|null $value The object values
     *
     * @return int Normalised value
     */
    public function transform($value)
    {
        if (null === $value || '' === $value) {
            return UsersConstant::USER_ID_ANONYMOUS;
        }

        if (is_numeric($value)) {
            // select user to verify it exists
            /** @var UserEntity $user */
            $user = $this->userRepository->find($value);
            if (null === $user) {
                return UsersConstant::USER_ID_ANONYMOUS;
            }

            return $value;
        }

        return $value;
    }

    /**
     * Transforms an user entity back to the identifier.
     *
     * @param UserEntity $value The user
     *
     * @return int The user identifier
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return UsersConstant::USER_ID_ANONYMOUS;
        }

        if ($value instanceof UserEntity) {
            return $value->getUid();
        }

        return (int)$value;
    }
}
