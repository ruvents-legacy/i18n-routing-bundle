<?php

namespace Ruwork\RoutingBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference as BaseTemplateReference;

class TemplateExportableReference extends BaseTemplateReference
{
    /**
     * @param array $data
     * @return TemplateExportableReference
     */
    public static function __set_state(array $data)
    {
        $template = new static();

        foreach ($data['parameters'] as $parameter => $value) {
            $template->set($parameter, $value);
        }

        return $template;
    }
}
