<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class BanWord extends Constraint
{
    public function __construct(
        public string $message = 'This constains a banned word "{{ banWord }}".',  // Message par défaut si un mot interdit est trouvé
        public array $banWords = ['spam', 'viagra'], // Liste des mots interdits par défaut
        ?array $groups = null, // Groupes de validation optionnels
        mixed $payload = null) // Charge utile optionnelle
    {
        // Appelle le constructeur parent avec les autres arguments
        parent::__construct(null, $groups, $payload);
    }
}
