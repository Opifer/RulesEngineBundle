<?php

namespace Opifer\RulesEngineBundle\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Opifer\RulesEngine\Rule\Rule;
use Opifer\RulesEngine\RulesEngine;

/**
 * Rule Controller
 *
 * Defines the single rule views within the RulesEngine
 */
class RuleController extends Controller
{
    /**
     * @Route(
     *     "/rules/{provider}",
     *     name="opifer.api.rule",
     *     options={"expose"=true}
     * )
     * @Method({"GET"})
     *
     * @param  Request $request
     * @param  string  $provider
     *
     * @return Response
     */
    public function indexAction(Request $request, $provider = null)
    {
        if (is_null($provider)) {
            $provider = 'default';
        }
        
        $provider = $this->get('opifer.rulesengine.provider.pool')->getProvider($provider);

        if ($request->get('context')) {
            $provider->setContext($request->get('context'));
        }

        $rules = $provider->buildRules();
        $data = $this->get('jms_serializer')->serialize($rules, 'json');
        
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }
}
