<?php

namespace Ruwork\RoutingBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference as BaseTemplateReference;

class TemplateReference extends BaseTemplateReference
{
    /**
     * @param array $data
     * @return TemplateReference
     */
    public static function __set_state(array $data)
    {
        $reference = new self();

        foreach ($data['parameters'] as $name => $value) {
            $reference->set($name, $value);
        }

        return $reference;
    }
}
