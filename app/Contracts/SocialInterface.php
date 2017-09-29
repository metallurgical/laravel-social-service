<?php

namespace App\Contracts;

interface SocialInterface {
    /**
     * Set access token get from social network
     *
     * @param string $token
     * @return mixed
     */
    public function setToken(string $token);
    /**
     * Get jwt token(create from user model)
     *
     * @param bool $usedSupplyData
     * @return string $token
     */
    public function getUserToken(bool $usedSupplyData): string;
    /**
     * Set data that need to be inserted
     *
     * Array key value pair - must have email key for checking existing user
     *
     * @param array $dataToBeInserted
     * @return mixed
     */
    public function setData(array $dataToBeInserted);

}
