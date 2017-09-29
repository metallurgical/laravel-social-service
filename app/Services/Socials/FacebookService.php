<?php

namespace App\Services\Socials;

use Facebook\Facebook;

class FacebookService extends SocialService
{
    /**
     * Initialize Social Object Instance
     *
     * Method implement from SocialService Abstract Class
     * @return Object Instance
     */
    protected function init()
    {
        $this->social = new Facebook([
            'app_id' => "APP_ID", // 
            'app_secret' => "APP_SECRET",
            'default_graph_version' => config('services.facebook.default_graph_version'),
        ]);

        return $this;
    }
    /**
     * Get profile from Social Network
     *
     * Method implement from SocialService Abstract Class
     * @return Json|Array Response
     */
    public function getProfile()
    {
        $response = $this->social->get('/me?fields=id,name,email,gender', $this->token);

        return $response->getGraphUser();
    }
}
