<?php

namespace Opifer\RulesEngineBundle\Provider;

use Symfony\Component\HttpFoundation\Request;

interface RoutableProviderInterface
{
    /**
     * Allows to request condition data from ajax requests.
     * This method is called from the RulesEngineController.
     *
     * @param Request $request
     *
     * @return array
     */
    public function getConditionPresets(Request $request);
}
