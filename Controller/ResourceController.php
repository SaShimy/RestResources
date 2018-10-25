<?php

namespace Components\Restresources\Controller;

use Components\Restresources\Model\ResourceInterface;
use Components\Restresources\Repository\ResourceRepositoryInterface;
use Components\Restresources\Service\ResourceFileProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * Class PrototypeController
 * @package SimpleDomicours\Controller\User
 */
class ResourceController extends AbstractController
{
    const GROUP_MINIMAL = 'minimal';
    const DATETIME_FORMAT = "Y-m-d\TH:i:s.v\Z";

    /**
     * @Route("/{resource}", methods={"GET"})
     * @param Request              $request
     * @param ResourceFileProvider $rfp
     * @param                      $resource
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function cgetAction(Request $request, ResourceFileProvider $rfp, $resource)
    {
        $em = $this->getDoctrine()
            ->getManager();
        #Get configuration file {$resource}.resource.yml
        $file = $rfp->getFromResource($resource);
        if (!in_array('CGET', $file['actions']))
        {
            throw new NotFoundHttpException();
        }
        $class = $file['class'];
        #Get $data
        $repository = $em->getRepository($class);
        if (!$repository instanceof ResourceRepositoryInterface)
        {
            throw new \LogicException;
        }
        $parameters = $request->query->all();
        $group = $parameters['_group'] ?? self::GROUP_MINIMAL;
        unset($parameters['_group']);
        $data = $repository->cget($parameters);
        if (!isset($data[0]))
        {
            return new JsonResponse([], 200);
        }
        #Check granted
        $this->denyAccessUnlessGranted(ResourceInterface::CAN_LIST, $data[0]);
        if ($request->get('wrap') === 'true')
        {
            $data = ['data' => $data];
        }
        $content = $this->get('serializer')
            ->serialize($data, 'json', [
                'groups'          => [$group],
                'datetime_format' => self::DATETIME_FORMAT
            ]);

        return new JsonResponse($content, 200, [], true);
    }

    /**
     * @Route("/{resource}/{id}", methods={"GET"})
     * @param Request              $request
     * @param ResourceFileProvider $rfp
     * @param                      $resource
     * @param                      $id
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getAction(Request $request, ResourceFileProvider $rfp, $resource, $id)
    {
        $em = $this->getDoctrine()
            ->getManager();
        #Get configuration file {$resource}.resource.yml
        $file = $rfp->getFromResource($resource);
        if (!in_array('GET', $file['actions']))
        {
            throw new NotFoundHttpException();
        }
        $class = $file['class'];
        #Get $data
        $repository = $em->getRepository($class);
        if (!$repository instanceof ResourceRepositoryInterface)
        {
            throw new \LogicException;
        }
        $data = $repository->get($id);
        #Check granted
        $this->denyAccessUnlessGranted(ResourceInterface::CAN_RETRIEVE, $data);
        $content = $this->get('serializer')
            ->serialize($data, 'json', [
                'groups'          => [$request->get('_group') ?? self::GROUP_MINIMAL],
                'datetime_format' => self::DATETIME_FORMAT
            ]);

        return New JsonResponse($content, 200, [], true);
    }

    /**
     * @Route("/{resource}", methods={"POST"})
     * @param Request              $request
     * @param ResourceFileProvider $rfp
     * @param                      $resource
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function postAction(Request $request, ResourceFileProvider $rfp, $resource)
    {
        #Get configuration file {$resource}.resource.yml
        $file = $rfp->getFromResource($resource);
        if (!in_array('POST', $file['actions']))
        {
            throw new NotFoundHttpException();
        }
        $class = $file['class'];
        #Get $data
        $data = new $class();
        #Check granted
        $this->denyAccessUnlessGranted(ResourceInterface::CAN_CREATE, $data);
        $form = $this->createForm($file['type'], $data, ['method' => 'POST']);

        return $this->processFrom($request, $form, $data, 'POST');
    }

    /**
     * @Route("/{resource}/{id}/{childResource}", methods={"POST"})
     * @param Request              $request
     * @param ResourceFileProvider $rfp
     * @param                      $resource
     * @param                      $id
     * @param                      $childResource
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function postChildAction(Request $request, ResourceFileProvider $rfp, $resource, $id, $childResource)
    {
        $em = $this->getDoctrine()
            ->getManager();
        #Get configuration file {$resource}.resource.yml
        $file = $rfp->getFromResource($resource);
        if (!array_key_exists($childResource, $file['children']))
        {
            throw new NotFoundHttpException();
        }
        $childFile = $rfp->getFromResource($childResource);
        if (!in_array('POST', $childFile['actions']))
        {
            throw new NotFoundHttpException();
        }
        $class = $file['class'];
        $childClass = $childFile['class'];
        #Get $resource
        $repository = $em->getRepository($class);
        if (!$repository instanceof ResourceRepositoryInterface)
        {
            throw new \LogicException;
        }
        #Get $data
        $parent = $repository->get($id);
        $data = new $childClass();
        $setter = "set" . $file['children'][$childResource];
        $data->$setter($parent);
        #Check granted
        $this->denyAccessUnlessGranted(strtoupper("CREATE_{$childResource}"), $parent);
        $form = $this->createForm($childFile['type'], $data, ['method' => 'POST']);

        return $this->processFrom($request, $form, $data, 'POST');
    }

    /**
     * @Route("/{resource}/{id}", methods={"PATCH"})
     * @param Request              $request
     * @param ResourceFileProvider $rfp
     * @param                      $resource
     * @param                      $id
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function patchAction(Request $request, ResourceFileProvider $rfp, $resource, $id)
    {
        $em = $this->getDoctrine()
            ->getManager();
        #Get configuration file {$resource}.resource.yml
        $file = $rfp->getFromResource($resource);
        if (!in_array('PATCH', $file['actions']))
        {
            throw new NotFoundHttpException();
        }
        $class = $file['class'];
        #Get $data
        $repository = $em->getRepository($class);
        if (!$repository instanceof ResourceRepositoryInterface)
        {
            throw new \LogicException;
        }
        $data = $repository->get($id);
        #Check granted
        $this->denyAccessUnlessGranted(ResourceInterface::CAN_UPDATE, $data);
        $form = $this->createForm($file['type'], $data, ['method' => 'PATCH']);

        return $this->processFrom($request, $form, $data, 'PATCH');
    }

    /**
     * @Route("/{resource}/{id}", methods={"DELETE"})
     * @param Request              $request
     * @param ResourceFileProvider $rfp
     * @param                      $resource
     * @param                      $id
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function deleteAction(Request $request, ResourceFileProvider $rfp, $resource, $id)
    {
        $em = $this->getDoctrine()
            ->getManager();
        #Get configuration file {$resource}.resource.yml
        $file = $rfp->getFromResource($resource);
        if (!in_array('DELETE', $file['actions']))
        {
            throw new NotFoundHttpException();
        }
        $class = $file['class'];
        #Get $data
        $repository = $em->getRepository($class);
        if (!$repository instanceof ResourceRepositoryInterface)
        {
            throw new \LogicException;
        }
        $data = $repository->get($id);
        #Check granted
        $this->denyAccessUnlessGranted(ResourceInterface::CAN_DELETE, $data);
        $em->remove($data);
        $em->flush();

        return New JsonResponse('deleted', 200, [], false);
    }

    private function getErrorMessages(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $key => $error)
        {
            if ($form->isRoot())
            {
                $errors['#'][] = $error->getMessage();
            }
            else
            {
                $errors[] = $error->getMessage();
            }
        }
        /** @var FormInterface $child */
        foreach ($form->all() as $child)
        {
            if ($child->isSubmitted() && !$child->isValid())
            {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    protected function processFrom(Request $request, FormInterface $form, $resource, $method)
    {
        #Vars
        $code = ($method === 'POST') ? 201 : 204;
        $headers = array();
        #Handle form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()
                ->getManager();
            $em->persist($resource);
            $em->flush();

            return new JsonResponse($resource->getId(), $code, $headers, false);
        }
        $errors = $this->get('serializer')
            ->serialize($this->getErrorMessages($form), 'json', []);

        return new JsonResponse($errors, 400, [], true);
    }
}
