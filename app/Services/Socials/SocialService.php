<?php

namespace App\Services\Socials;

use App\Contracts\SocialInterface;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Auth;
use Carbon\Carbon;
/**
 * Class SocialService
 * @package App\Services\Socials
 *
 * Implement SocialInterface Interface
 */
abstract class SocialService implements SocialInterface
{
    /**
     * Store access token get from social apps
     *
     * @var $token
     */
    protected $token;
    /**
     * Store social instance
     *
     * @var $social
     */
    protected $social;
    /**
     * Store array key-value pair for inserting into user table
     *
     * @var $dataToBeInserted
     */
    private $dataToBeInserted;
    /**
     * Initialize method. Should create an instance of any class.
     * This method should assign Social Object into protected member($this->social)
     *
     * @return Object
     */
    abstract protected function init();
    /**
     * Get profile from Social Network, should return valid json/array response
     *
     * @return Json|Array Response
     */
    abstract public function getProfile();
    /**
     * Method implement from Social Interface
     *
     * @param string $token
     */
    public function setToken(string $token)
    {
        /**
         * Only string are accepted
         */
        if(is_numeric($token) || is_array($token))
            throw new \InvalidArgumentException('Token must be type of string');
        /**
         * Argument $token is compulsory
         */
        if(!$token)
            throw new \BadFunctionCallException('Token key is missing');

        $this->token = $token;
        /**
         * Call child/sub-class's implementation of abstract Init Method
         */
        $this->init();

        return $this;
    }
    /**
     * Check existing user before insert into user table
     *
     * @param bool $usedSupplyData
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    private function checkExistingUser(bool $usedSupplyData = false)
    {
        /**
         * If $usedSupplyData false, then use the email from profile
         */
        if(!$usedSupplyData) {
            /**
             * Call child/sub-class's implementation of getProfile abstract Method
             */
            $profile = $this->getProfile();
            $result = User::where('email', '=', $profile['email'])->first();
        }
        /**
         * Use the email from user provided
         */
        else
            $result = User::where('email', '=', $this->dataToBeInserted['email'])->first();

        return $result;

    }
    /**
     * Implement method from SocialInterface
     *
     * @param bool $usedSupplyData
     * @return mixed
     * @throws \Exception
     */
    public function getUserToken(bool $usedSupplyData = false) :string
    {
        /**
         * $dataToBeInserted must have key-value pair to get started
         */
        if(empty($this->dataToBeInserted))
            throw new \Exception('No data was supplied, nothing can be used');
        /**
         * If user already exist, then get it instance
         */
        $user = $this->checkExistingUser($usedSupplyData);
        /**
         * Else create the new user with activation ready
         */
        if(!$user) {
            $this->dataToBeInserted['password'] = $this->token;
            // $this->dataToBeInserted['activated_at'] = Carbon::now();
            $user = User::create($this->dataToBeInserted);
        }
        /**
         * Log in the user from user instance
         */
        Auth::login($user);
        /**
         * Create JWT from particular user
         */
        $jwtToken = JWTAuth::fromUser($user);

        return $jwtToken;

    }
    /**
     * Implement method form SocialInterface
     *
     * @param array $data
     * @return $this
     * @throws \Exception
     */
    public function setData(array $data)
    {
        /**
         * Argument $data expect data type of array
         */
        if(is_numeric($data) || is_string($data))
            throw new \InvalidArgumentException('Argument must be type of array key value pair');
        /**
         * Since email are common use case for user registration, so we required it
         */
        if(!array_key_exists('email', $data))
            throw new \Exception('Missing email key');

        $this->dataToBeInserted = $data;

        return $this;
    }

}
