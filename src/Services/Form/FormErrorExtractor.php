<?php

namespace App\Services\Form;

use Symfony\Component\Form\FormInterface;

class FormErrorExtractor
{
    public function extractErrors(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->extractErrors($child);
            }
        }

        return $errors;
    }
}