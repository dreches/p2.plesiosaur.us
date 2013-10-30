<?php
class users_controller extends base_controller {

    public function __construct() {
        parent::__construct();
//	echo "users_controller construct called<br><br>";
    }

    public function index() {
        echo "This is the index page";
    }

    public function signup() {

	# Set up the view
	$this->template->content = View::instance('v_users_signup');

	# Render the view
	echo $this->template;
    }


    public function p_signup() {

	$_POST['created'] = Time::now();
	$_POST['modified'] = $_POST['created'];
	$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);
	$_POST['token'] = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string());


//	echo "<pre>";
//	print_r($_POST);
//	echo "</pre>";

        DB::instance(DB_NAME)->insert_row('users', $_POST);

	Router::redirect('/users/login/');

    }

    public function login() {

	$this->template->content = View::instance('v_users_login');

	echo $this->template;

    }

  
    public function p_login() {

	$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

//      echo "<pre>";
//	print_r($_POST);
//	echo "</pre>";

	$q = 'SELECT token
	      FROM users
	      WHERE email = "'.$_POST['email'].'"
	      AND password = "'.$_POST['password'].'"';

//	echo $q."<br><br>";

	$token = DB::instance(DB_NAME)->select_field($q);

//	echo $token."<br><br>";

	if($token) {
	    setcookie('token', $token, strtotime('+1 month'), '/');
//	    echo "You are now logged in!<br><br>Go back to the <a href='/'>Home page</a><br>";
	    Router::redirect('/');
	}
	else {
	    echo "Login failed. <a href='/'>Go back</a>";
	}

    }


    public function logout() {

        $new_token = sha1(TOKEN_SALT.$this->user->email.Utils::generate_random_string());

	$data = Array('token' => $new_token);

	DB::instance(DB_NAME)->update('users',$data, 'WHERE user_id = '.$this->user->user_id);

	setcookie('token', '', strtotime('-1 year'), '/');

	Router::redirect('/');

    }

    public function profile($user_name = NULL) {


    	if(!$this->user) {
	    //Router::redirect('/');
	    die('Members only!<br><a href="/users/login">Log in</a><br>');
	}


        $this->template->content = View::instance('v_users_profile');
	$this->template->title = "Profile";

	$client_files_head = Array('/css/profile.css','/css/master.css');
	$this->template->client_files_head = Utils::load_client_files($client_files_head);

	$client_files_body = Array('/js/master.js');
	$this->template->client_files_body = Utils::load_client_files($client_files_body);

	$this->template->content->user_name = $user_name;

        echo $this->template;

/*
        # leave off the .php or it can't find it
        $view = View::instance('v_users_profile');

	$view->user_name = $user_name;

	echo $view;

        if($user_name == NULL) {
	    echo "No user specified";
	}
	else {
	    echo "This is the profile for ".$user_name;
	}
*/

    }

} #eoc
