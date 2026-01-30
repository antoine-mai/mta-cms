<?php namespace Root\Pages\Post;
/**
 * 
**/
use \Root\Core\Response\Response;
use \Root\Core\Request\Request;
use \Root\Core\Controller;
use \Root\Core\Registry;
/**
 * 
**/
class User extends Controller
{
    /**
     * user: /post/user
    **/
    public function index(Request $request): Response
    {
        $auth = Registry::getInstance('Auth');
        $isLoggedIn = $auth->isLoggedIn();
        
        return $this->json([
            'isLoggedIn' => $isLoggedIn,
            'user' => $isLoggedIn ? [
                'username' => $auth->getUser(),
                'role' => 'root'
            ] : null
        ]);
    }

    /**
     * login: /post/user/login
     */
    public function login(Request $request): Response
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $auth = Registry::getInstance('Auth');
        if ($auth->login($username, $password)) {
            return $this->json([
                'success' => true,
                'message' => 'Logged in successfully'
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 200);
    }

    /**
     * logout: /post/user/logout
     */
    public function logout(Request $request): Response
    {
        $auth = Registry::getInstance('Auth');
        $auth->logout();
        return $this->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }


}