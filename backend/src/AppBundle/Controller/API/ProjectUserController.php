<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectUser;
use AppBundle\Form\ProjectUser\CreateType;
use AppBundle\Security\ProjectVoter;
use MainBundle\Controller\API\ApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/project-users")
 */
class ProjectUserController extends ApiController
{
    /**
     * Get Project User by id.
     *
     * @Route("/{id}", name="app_api_project_users_get")
     * @Method({"GET"})
     *
     * @param ProjectUser $projectUser
     *
     * @return JsonResponse
     */
    public function getAction(ProjectUser $projectUser)
    {
        $project = $projectUser->getProject();
        if (!$project) {
            throw new \LogicException('Project does not exist!');
        }
        $this->denyAccessUnlessGranted(ProjectVoter::VIEW, $project);

        return $this->createApiResponse($projectUser);
    }

    /**
     * Edit a specific Project User.
     *
     * @Route("/{id}", name="app_api_project_users_edit")
     * @Method({"PUT", "PATCH"})
     *
     * @param Request     $request
     * @param ProjectUser $projectUser
     *
     * @return JsonResponse
     */
    public function editAction(Request $request, ProjectUser $projectUser)
    {
        $project = $projectUser->getProject();
        if (!$project) {
            throw new \LogicException('Project does not exist!');
        }
        $this->denyAccessUnlessGranted(ProjectVoter::EDIT, $project);

        $form = $this->createForm(CreateType::class, $projectUser, ['csrf_protection' => false]);
        $this->processForm($request, $form, $request->isMethod(Request::METHOD_PUT));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($projectUser);
            $em->flush();

            return $this->createApiResponse($projectUser, Response::HTTP_ACCEPTED);
        }

        $errors = $this->getFormErrors($form);
        $errors = [
            'messages' => $errors,
        ];

        return $this->createApiResponse($errors, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Delete a specific Project User.
     *
     * @Route("/{id}", name="app_api_project_users_delete")
     * @Method({"DELETE"})
     *
     * @param ProjectUser $projectUser
     *
     * @return JsonResponse
     */
    public function deleteAction(ProjectUser $projectUser)
    {
        $project = $projectUser->getProject();
        if (!$project) {
            throw new \LogicException('Project does not exist!');
        }
        $this->denyAccessUnlessGranted(ProjectVoter::DELETE, $project);

        $em = $this->getDoctrine()->getManager();
        $em->remove($projectUser);
        $em->flush();

        return $this->createApiResponse(null, Response::HTTP_NO_CONTENT);
    }
}