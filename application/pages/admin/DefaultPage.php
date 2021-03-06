<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Adriaan Knapen <a.d.knapen@protonmail.com>
 * @date 6-2-2017
 */

class DefaultPage extends PageFrame
{

    public function getViews(): array
    {
        return [
            'dashboard',
        ];
    }

    public function hasAccess(): bool
    {
        return isLoggedInAndHasRole($this->ci, [Role::ROLE_ADMIN]);
    }

    protected function getFormValidationRules()
    {
        return false;
    }

    /**
     * Function which is called after construction and before the views are rendered.
     */
    public function beforeView()
    {
        /** @var User_Consumption_LoginRole $user_Consumption_LoginRole */
        $user_Consumption_LoginRole = $this->ci->User_Consumption_LoginRole;
        /** @var Role $role */
        $role = $this->ci->Role;

        $roleId = $role->getByName(Role::ROLE_USER)[Role::FIELD_ROLE_ID];
        $result = $user_Consumption_LoginRole->get($roleId);

        $totalScore = 0;
        $negativeUsers = [];
        array_walk($result, function($item, $key) use (&$totalScore, &$negativeUsers) {
            $totalScore += $item[Consumption::FIELD_AMOUNT];
            if ($item[Consumption::FIELD_AMOUNT] < 0) {
                $negativeUsers[] = $item[User::FIELD_FIRST_NAME]." ".$item[User::FIELD_LAST_NAME];
            }
        });

        $this->setData('totalScore', $totalScore);
        $this->setData('negativeUsers', $negativeUsers);
    }

    /**
     * Function which is called after the views are rendered.
     */
    public function afterView()
    {
    }

    /**
     * Defines which models should be loaded.
     *
     * @return array;
     */
    protected function getModels(): array
    {
        return [
            Role::class,
            User_Consumption_LoginRole::class,
            Consumption::class,
        ];
    }

    /**
     * Defines which libraries should be loaded.
     *
     * @return array;
     */
    protected function getLibraries(): array
    {
        return [];
    }

    /**
     * Defines which helpers should be loaded.
     *
     * @return array;
     */
    protected function getHelpers(): array
    {
        return [];
    }
}