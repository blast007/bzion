<?php

use BZIon\Form\IpType;
use BZIon\Form\PlayerType;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class BanController extends CRUDController
{
    public function showAction(Ban $ban)
    {
        return array("ban" => $ban);
    }

    public function listAction()
    {
        return array("bans" => Ban::getBans());
    }

    public function createAction(Player $me)
    {
        return $this->create($me);
    }

    public function editAction(Player $me, Ban $ban)
    {
        return $this->edit($ban, $me, "ban");
    }

    public function deleteAction(Player $me, Ban $ban)
    {
        return $this->delete($ban, $me);
    }

    protected function fill($form, $ban)
    {
        $form->get('player')->get('players')->setData($ban->getVictim());
        $form->get('expiration')->setData($ban->getExpiration());
        $form->get('reason')->setData($ban->getReason());
        $form->get('server_message')->setData($ban->getServerMessage());
        $form->get('server_join_allowed')->setData($ban->allowedServerJoin());
        $form->get('ip_addresses')->setData($ban->getIpAddresses());
    }

    protected function update($form, $ban, $me)
    {
        $ban->setIPs($form->get('ip_addresses')->getData())
            ->setReason($form->get('reason')->getData())
            ->setServerMessage($form->get('server_message')->getData())
            ->setAllowServerJoin($form->get('server_join_allowed')->getData());

        return $ban;
    }

    protected function enter($form, $me)
    {
        return Ban::addBan(
            $form->get('player')->getData()->getId(),
            $me->getId(),
            $form->get('expiration')->getData(),
            $form->get('reason')->getData(),
            $form->get('server_message')->getData(),
            $form->get('ip_addresses')->getData(),
            $form->get('server_join_allowed')->getData()
        );
    }

    public function createForm($edit)
    {
        return Service::getFormFactory()->createBuilder()
            ->add('player', new PlayerType(), array(
                'disabled' => $edit,
            ))
            ->add('expiration', 'datetime')
            ->add('reason', 'text', array(
                'constraints' => new NotBlank(),
            ))
            ->add('server_join_allowed', 'checkbox', array(
                'data' => true,
                'required' => false,
            ))
            ->add('server_message', 'text', array(
                'required' => false,
                'constraints' => new Length(array(
                    'max' => 150,
                ))
            ))
            ->add('ip_addresses', new IpType(), array(
                'required' => false,
            ))
            ->add('enter', 'submit')
            ->getForm();
    }
}
