<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Client functions for Rocket.Chat authentication.
 *
 * @package     local_rocketchat
 * @copyright   2016 GetSmarter {@link http://www.getsmarter.co.za}
 * @author      2019 Adrian Perez <p.adrian@gmx.ch> {@link https://adrianperez.me}
 * @license     MIT License
 */

namespace local_rocketchat;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/filelib.php');

class client {

    public $authenticated = false;
    public $url;

    private $authtoken;
    private $userid;
    private $username;
    private $password;
    private $api;

    /**
     * Client constructor to get settings for API calls.
     *
     * @throws \dml_exception
     */
    public function __construct() {
        // TODO: Add method to get the instanceurl, to use it in the block_rocketchat.
        $host = get_config('local_rocketchat', 'host');
        $port = !empty($port = get_config('local_rocketchat', 'port')) ? ':' . $port : '';
        $protocol = get_config('local_rocketchat', 'protocol') == 0 ? 'https' : 'http';

        $this->username = get_config('local_rocketchat', 'username');
        $this->password = get_config('local_rocketchat', 'password');

        $this->url = $protocol . $host . $port;
        $this->api = '';

        $this->authenticate();
    }

    public function authentication_headers() {
        return array("X-Auth-Token: " . $this->authtoken, "X-User-Id: " . $this->userid);
    }

    /**
     * @throws \dml_exception
     */
    private function authenticate() {
        $response = $this->request_login_credentials();

        if ($response && $response->status == 'success') {
            $this->store_credentials($response->data);
            $this->authenticated = true;
        }
    }

    /**
     * @return bool|mixed
     * @throws \dml_exception
     */
    private function request_login_credentials() {
        $api = '/api/v1/login';
        $data = array(
            "user" => $this->username,
            "password" => $this->password
        );

        $header = array('Content-Type: application/json');

        return utilities::make_request($this->url, $api, 'post', $data, $header);
    }

    private function store_credentials($data) {
        if (isset($data->authToken) && isset($data->userId)) {
            $this->authtoken = $data->authToken;
            $this->userid = $data->userId;
        }
    }
}
