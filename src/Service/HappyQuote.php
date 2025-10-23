<?php

namespace App\Service;

class HappyQuote
{
    private array $messages = [
        'Believe you can and you are halfway there.',
        'The best way to predict the future is to create it.',
        'Every day may not be good... but there’s something good in every day!',
        'Great work! Keep going!',
    ];

    /**
     * Retourne un message aléatoire.
     */
    public function getHappyMessage(): string
    {
        $index = array_rand($this->messages);
        return $this->messages[$index];
    }

    /**
     * Permet d’ajouter un nouveau message à la liste.
     */
    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * Retourne tous les messages disponibles.
     */
    public function getAllMessages(): array
    {
        return $this->messages;
    }
}
