<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class BanWordValidator extends ConstraintValidator
{
    // La méthode "validate" est appelée pour valider une valeur par rapport à une contrainte personnalisée.
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var BanWord $constraint */

        // Si la valeur est nulle ou une chaîne vide, la validation s'arrête immédiatement.
        if (null === $value || '' === $value) {
            return; // Aucune validation n'est nécessaire si la valeur est vide ou nulle.
        }

        // Convertit la valeur en minuscules pour une comparaison insensible à la casse.
        $value = strtolower($value);

        // Parcours la liste des mots interdits fournie par la contrainte (banwords).
        foreach ($constraint->banWords as $banWord) {
            // Si la valeur contient l'un des mots interdits (en utilisant "str_contains" pour vérifier la présence du mot),
            if (str_contains($value, $banWord)) {
                // Crée une violation et ajoute un message d'erreur.
                // "buildViolation" crée une nouvelle violation, puis on personnalise le message d'erreur.
                $this->context->buildViolation($constraint->message)
                    // Remplace le placeholder {{ banword }} dans le message par le mot trouvé.
                    ->setParameter('{{ banWord }}', $banWord)
                    // Ajoute la violation à la liste des violations de la valeur à valider.
                    ->addViolation();
            }
        }
    }
}