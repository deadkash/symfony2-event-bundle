<?php

namespace Sp\EventBundle\Controller;


use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sp\EventBundle\Entity\EventCondition;
use Sp\EventBundle\Entity\EventConsequence;
use Sp\EventBundle\Form\EventType;
use Sp\EventBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package Sp\EventBundle\Controller
 * @Route("/event")
 */
class DefaultController extends Controller {

    /**
     * @Route("/", name="event")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SpEventBundle:Event')->findBy(array(), array('enabled' => 'DESC', 'name' => 'ASC'));

        return array(
            'entities' => $entities
        );
    }

    /**
     * @Route("/new", name="event_new")
     * @Template()
     * @return array
     */
    public function newAction()
    {
        $entity = new Event();
        $form = $this->createCreateForm($entity);

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{id}/edit", name="event_edit")
     * @Template("SpEventBundle:Default:new.html.twig")
     * @param $id
     * @return array
     */
    public function editAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpEventBundle:Event')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Event entity');

        $form = $this->createEditForm($entity);

        return array(
            'form'      => $form->createView(),
            'entity'    => $entity
        );
    }

    /**
     * @Route("/{id}", name="event_show")
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     */
    public function showAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $eventService = $this->get('event');

        $entity = $em->getRepository('SpEventBundle:Event')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Event entity.');

        $event = $eventService->getEventByType($entity->getType());

        $conditionsForm = $event->createConditionsForm(
            $this->generateUrl('event_add_condition', array('id' => $id)) );
        $conditions = $eventService->getConditionsByEvent($entity);

        $consequencesForm = $event->createConsequencesForm(
            $this->generateUrl('event_add_consequence', array('id' => $id))
        );
        $consequences = $eventService->getConsequencesByEvent($entity);

        return array(
            'entity'            => $entity,
            'conditions_form'   => $conditionsForm->createView(),
            'conditions'        => $conditions,
            'consequences_form' => $consequencesForm->createView(),
            'consequences'      => $consequences,
            'event'             => $event
        );
    }

    /**
     * @Route("/", name="event_create")
     * @Method("POST")
     * @Template("SpEventBundle:Default:new.html.twig")
     * @param Request $request
     * @return array
     */
    public function createAction(Request $request)
    {
        $entity = new Event();
        $form = $this->createCreateForm($entity);
        $form->submit($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $eventType = $entity->getType();
            $event = $this->get('event')->getEventByType($eventType);
            $entity->setCronable( $event->isCronable() );

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * @param $id
     * @internal param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/delete", name="event_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SpEventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('event'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     * @Route("/{id}", name="event_update")
     * @Method("POST")
     * @Template("SpEventBundle:Default:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpEventBundle:Event')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Event entity.');

        $form = $this->createEditForm($entity);
        $form->submit($request);

        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('id' => $id)));
        }

        return array(
            'form'      => $form->createView(),
            'entity'    => $entity
        );
    }

    /**
     * @Route("/{id}/condition/new", name="event_add_condition")
     * @Method("POST")
     * @Template()
     * @param Request $request
     * @param $id
     * @return array
     */
    public function newConditionAction(Request $request, $id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpEventBundle:Event')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Event entity.');

        $conditionType = $request->get('form[condition]', false, true);
        $condition = $this->get('event')->getConditionByType($conditionType);
        $conditionParamsForm = $condition->createForm(
            $this->generateUrl('event_create_condition', array('id' => $id))
        );

        return array(
            'entity'        => $entity,
            'params_form'   => $conditionParamsForm->createView(),
            'condition'     => $condition
        );
    }

    /**
     * @Route("/{id}/condition/{conditionId}/edit", name="condition_edit")
     * @Template("SpEventBundle:Default:newCondition.html.twig")
     * @param $id
     * @param $conditionId
     * @return array
     */
    public function editConditionAction($id, $conditionId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpEventBundle:Event')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Event entity.');

        $eventCondition = $em->getRepository('SpEventBundle:EventCondition')->find($conditionId);
        if (!$eventCondition) throw $this->createNotFoundException('Unable to find Condition entity');

        $conditionType = $eventCondition->getType();
        $condition = $this->get('event')->getConditionByType($conditionType);
        $condition->setSerializedParams($eventCondition->getParams());
        $form = $condition->createForm($this->generateUrl('event_update_condition',
                array('id' => $id, 'conditionId' => $conditionId)), true);

        return array(
            'entity'        => $entity,
            'params_form'   => $form->createView(),
            'condition'     => $condition
        );
    }

    /**
     * @Route("/{id}/consequence/{consequenceId}/edit", name="consequence_edit")
     * @Template("SpEventBundle:Default:newConsequence.html.twig")
     * @param $id
     * @param $consequenceId
     * @return array
     */
    public function editConsequenceAction($id, $consequenceId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpEventBundle:Event')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Event entity.');

        $eventConsequence = $em->getRepository('SpEventBundle:EventConsequence')->find($consequenceId);
        if (!$eventConsequence) throw $this->createNotFoundException('Unable to find Consequence entity');

        $consequenceType = $eventConsequence->getType();
        $consequence = $this->get('event')->getConsequenceByType($consequenceType);
        $consequence->setSerializedParams($eventConsequence->getParams());
        $form = $consequence->createForm($this->generateUrl('event_update_consequence',
                array('id' => $id, 'consequenceId' => $consequenceId)), true);

        return array(
            'entity'        => $entity,
            'params_form'   => $form->createView(),
            'consequence'   => $consequence
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     * @Route("/{id}/consequence/new", name="event_add_consequence")
     * @Method("POST")
     * @Template()
     */
    public function newConsequenceAction(Request $request, $id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpEventBundle:Event')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Event entity.');

        $consequenceType = $request->get('form[consequence]', false, true);
        $consequence = $this->get('event')->getConsequenceByType($consequenceType);
        $consequenceParamsForm = $consequence->createForm(
            $this->generateUrl('event_create_consequence', array('id' => $id))
        );

        return array(
            'entity'        => $entity,
            'params_form'   => $consequenceParamsForm->createView(),
            'consequence'   => $consequence
        );
    }

    /**
     * @Route("/{id}/condition/{conditionId}", name="event_update_condition")
     * @Template("SpEventBundle:Default:newCondition.html.twig")
     * @Method("POST")
     * @param Request $request
     * @param $id
     * @param $conditionId
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateConditionAction(Request $request, $id, $conditionId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpEventBundle:Event')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Event entity.');

        $eventCondition = $em->getRepository('SpEventBundle:EventCondition')->find($conditionId);
        if (!$eventCondition) throw $this->createNotFoundException('Unable to find Condition entity');

        $conditionType = $eventCondition->getType();
        $condition = $this->get('event')->getConditionByType($conditionType);
        $condition->handleRequest($request);

        $form = $condition->createForm($this->generateUrl('event_update_condition',
                array('id' => $id, 'conditionId' => $conditionId)), true);
        $form->submit($request);

        if ($form->isValid()) {

            $eventCondition->setParams( $condition->getSerializedParams() );
            $em->persist($eventCondition);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('id' => $id)));
        }

        return array(
            'entity'        => $entity,
            'params_form'   => $form->createView(),
            'condition'     => $condition
        );
    }

    /**
     * @Route("/{id}/consequence/{consequenceId}", name="event_update_consequence")
     * @Template("SpEventBundle:Default:newConsequence.html.twig")
     * @Method("POST")
     * @param Request $request
     * @param $id
     * @param $consequenceId
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateConsequenceAction(Request $request, $id, $consequenceId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpEventBundle:Event')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Event entity.');

        $eventConsequence = $em->getRepository('SpEventBundle:EventConsequence')->find($consequenceId);
        if (!$eventConsequence) throw $this->createNotFoundException('Unable to find Consequence entity');

        $consequenceType = $eventConsequence->getType();
        $consequence = $this->get('event')->getConsequenceByType($consequenceType);
        $consequence->handleRequest($request);

        $form = $consequence->createForm($this->generateUrl('event_update_consequence',
                array('id' => $id, 'consequenceId' => $consequenceId)), true);
        $form->submit($request);

        if ($form->isValid()) {

            $eventConsequence->setParams( $consequence->getSerializedParams() );
            $em->persist($eventConsequence);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('id' => $id)));
        }

        return array(
            'entity'        => $entity,
            'params_form'   => $form->createView(),
            'consequence'   => $consequence
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     * @Route("/{id}/condition", name="event_create_condition")
     * @Method("POST")
     * @Template("SpEventBundle:Default:newCondition.html.twig")
     */
    public function createConditionAction(Request $request, $id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpEventBundle:Event')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Event entity.');

        $formData = $request->get('form');
        $conditionType = $formData['condition'];
        $condition = $this->get('event')->getConditionByType($conditionType);
        $conditionParamsForm = $condition->createForm(
            $this->generateUrl('event_create_condition', array('id' => $id))
        );
        $conditionParamsForm->submit($request);

        if ($conditionParamsForm->isValid()) {

            $condition->handleRequest($request);
            $eventCondition = new EventCondition();
            $eventCondition->setEvent($entity);
            $eventCondition->setType($conditionType);
            $eventCondition->setParams( $condition->getSerializedParams() );

            $em->persist($eventCondition);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('id' => $id)));
        }

        return array(
            'entity'        => $entity,
            'params_form'   => $conditionParamsForm->createView(),
            'condition'     => $condition
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     * @Route("/{id}/consequence", name="event_create_consequence")
     * @Method("POST")
     * @Template("SpEventBundle:Default:newConsequence.html.twig")
     */
    public function createConsequenceAction(Request $request, $id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpEventBundle:Event')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Event entity.');

        $formData = $request->get('form');
        $consequenceType = $formData['consequence'];
        $consequence = $this->get('event')->getConsequenceByType($consequenceType);
        $consequenceParamsForm = $consequence->createForm(
            $this->generateUrl('event_create_consequence', array('id' => $id))
        );
        $consequenceParamsForm->submit($request);

        if ($consequenceParamsForm->isValid()) {

            $consequence->handleRequest($request);

            $eventConsequence = new EventConsequence();
            $eventConsequence->setEvent($entity);
            $eventConsequence->setType($consequenceType);
            $eventConsequence->setParams( $consequence->getSerializedParams() );

            $em->persist($eventConsequence);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('id' => $id)));
        }

        return array(
            'entity'        => $entity,
            'params_form'   => $consequenceParamsForm->createView(),
            'condition'     => $consequence
        );
    }

    /**
     * @Route("/{id}/condition/{conditionId}", name="condition_delete")
     * @Method("DELETE")
     * @param $id
     * @param $conditionId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function conditionDelete($id, $conditionId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $condition = $em->getRepository('SpEventBundle:EventCondition')->find($conditionId);
        if (!$condition) throw $this->createNotFoundException('Unable to find EventCondition entity');

        $em->remove($condition);
        $em->flush();

        return $this->redirect($this->generateUrl('event_show', array('id' => $id)));
    }

    /**
     * @Route("/{id}/consequence/{consequenceId}", name="consequence_delete")
     * @Method("DELETE")
     * @param $id
     * @param $consequenceId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function consequenceDelete($id, $consequenceId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $consequence = $em->getRepository('SpEventBundle:EventConsequence')->find($consequenceId);
        if (!$consequence) throw $this->createNotFoundException('Unable to find EventConsequence entity');

        $em->remove($consequence);
        $em->flush();

        return $this->redirect($this->generateUrl('event_show', array('id' => $id)));
    }

    /**
     * @param Event $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(Event $entity)
    {
        $types = $this->get('event')->getEventTypes();

        $form = $this->createForm(new EventType($types), $entity, array(
            'action' => $this->generateUrl('event_create'),
            'method' => 'POST'
        ));

        $form->add('submit', 'submit', array('label' => 'Добавить'));

        return $form;
    }

    /**
     * @param Event $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('event_update', array('id' => $entity->getId())),
            'method' => 'POST'
        ));

        $form->remove('type');
        $form->add('submit', 'submit', array('label' => 'Сохранить'));

        return $form;
    }

    /**
     * @param $id
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
} 