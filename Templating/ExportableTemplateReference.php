<?php

namespace Ruwork\RoutingBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;

class ExportableTemplateReference extends TemplateReference
{
    /**
     * @param array $data
     * @return ExportableTemplateReference
     */
    public function __set_state(array $data)
    {
        return new self(
            $data['parameters']['bundle'],
            $data['parameters']['controller'],
            $data['parameters']['name'],
            $data['parameters']['format'],
            $data['parameters']['engine']
        );
    }
}
