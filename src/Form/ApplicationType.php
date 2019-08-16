<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 25/07/2019
 * Time: 18:55
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    /**
     * Configuration des champs du formulaire
     *
     * @param $label
     * @param $placeholder
     * @param array $options
     * @return array
     */
    protected function getOptions($label, $placeholder, $options = [])
    {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options);
    }
}
