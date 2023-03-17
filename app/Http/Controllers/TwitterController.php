<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TwitterAPIExchange;

class TwitterController extends Controller
{
    public function index()
    {
        // Set up the Twitter API credentials
        $settings = [
            'oauth_access_token' => "YOUR_OAUTH_ACCESS_TOKEN",
            'oauth_access_token_secret' => "YOUR_OAUTH_ACCESS_TOKEN_SECRET",
            'consumer_key' => "YOUR_CONSUMER_KEY",
            'consumer_secret' => "YOUR_CONSUMER_SECRET"
        ];

        // Set up the Twitter API exchange
        $url = 'https://api.twitter.com/1.1/users/search.json';
        $getfield = '?q=launchpad';
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($settings);

        // Perform the search and retrieve the results
        $response = json_decode($twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest(), true);

        // Filter and format the data for display
        $profiles = [];
        foreach ($response as $profile) {
            if (stripos($profile['description'], 'launchpad') !== false) {
                $profiles[] = [
                    'name' => $profile['name'],
                    'totalFollowersCount' => $profile['followers_count'],
                    'verifiedFollowersCount' => count(array_filter($profile['followers'], function ($follower) {
                        return $follower['verified'];
                    })),
                    'location' => isset($profile['location']) ? $profile['location'] : ''
                ];
            }
        }

        // Pass the data to the view for display
        return view('profiles', ['profiles' => $profiles]);
    }
}
