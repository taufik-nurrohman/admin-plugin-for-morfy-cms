Admin Authentication Plugin for Morfy CMS
=========================================

Configuration
-------------

Place the `admin` folder with its contents in `plugins` folder. Then update your `config.php` file:

    <?php
        return array(
    
            ...
            ...
            ...
    
            'plugins' => array(
                'markdown',
                'sitemap',
                'admin' // <= Activation
            ),
            'admin_config' => array( // <= Configuration
                'username' => 'admin', // <= Change with your username
                'password' => 'test123', // <= Change with your password
                'classes' => array( // <= List of item's HTML classes
                    'page_login' => 'page-login',
                    'page_logout' => 'page-logout',
                    'page_wrapper' => 'page-wrapper',
                    'message' => 'message',
                    'error' => 'error',
                    'success' => 'success'
                ),
                'labels' => array( // <= List of item's readable text or labels
                    'username' => 'Username',
                    'password' => 'Password',
                    'login' => 'Login',
                    'logout' => 'Logout',
                    'title_login' => 'Administrator',
                    'title_logout' => 'Administrator',
                    'message_logged_in' => 'You are logged in.',
                    'message_logged_out' => 'You are logged out.',
                    'message_error_invalid' => 'Invalid username or password.',
                    'message_error_required' => 'Please fill out the login form.',
                    'message_error_token' => 'Invalid token.'
                )
            )
        );

Change the value of `username` and `password` as you want. **Done.** You can access the login and logout page from this URL:

* `http://localhost/{{test_morfy}}/admin/login`
* `http://localhost/{{test_morfy}}/admin/logout`

Once you logged in, the plugin will automatically add a new global variable called `Morfy::$config['logged_in']` with a value set to `true`

You can use it as a conditional check to display something that can only be viewed by the administrator. For example:

    if(Morfy::$config['logged_in']) {
        echo '<p>You are logged in.</p>';
    }
