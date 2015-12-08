<?php

namespace Opifer\RulesEngineBundle\Controller;

use Opifer\RulesEngineBundle\Provider\RoutableProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RulesEngineController extends Controller
{
    /**
     * Condition Action
     *
     * @param Request $request
     * @param string $provider
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function conditionAction(Request $request, $provider)
    {
        $provider = $this->get('opifer.rulesengine.provider.pool')->getProvider($provider);

        if (!$provider instanceof RoutableProviderInterface) {
            throw new \Exception(sprintf('%s must implement Opifer\RulesEngineBundle\Provider\RoutableProviderInterface to perform this action', get_class($provider)));
        }

        return new JsonResponse($provider->getConditionPresets($request));
    }
}
