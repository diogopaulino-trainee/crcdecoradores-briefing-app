<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'confirmed' => 'A confirmação de :attribute não coincide.',
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
    ],
    'max' => [
        'string' => 'O campo :attribute não pode ter mais de :max caracteres.',
    ],
    'attributes' => [
        'email' => 'e-mail',
        'password' => 'palavra-passe',
        'name' => 'nome',
        'current_password' => 'palavra-passe atual',
        'nome' => 'nome',
        'numero' => 'número',
        'nif' => 'NIF',
        'morada' => 'morada',
        'codigo_postal' => 'código postal',
        'localidade' => 'localidade',
        'pais_id' => 'país',
        'telefone' => 'telefone',
        'telemovel' => 'telemóvel',
        'website' => 'website',
        'email' => 'e-mail',
        'consentimento_rgpd' => 'consentimento RGPD',
        'observacoes' => 'observações',
        'estado' => 'estado',
        'entidade_id' => 'entidade',
        'funcao' => 'função',
    ],
    'custom' => [
        'email' => [
            'unique' => 'Este endereço de e-mail já está registado.',
        ],
        'nif' => [
            'unique' => 'Este NIF já está registado.',
        ],
    ],
    'codigo_postal' => [
            'regex' => 'O código postal deve ter o formato 1234-567.',
        ],
    'website' => [
        'url' => 'O website deve ser um endereço válido (ex: https://exemplo.com).',
    ],
];
