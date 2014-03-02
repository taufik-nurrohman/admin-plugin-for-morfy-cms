<?php

/**
 * Admin Authentication Plugin for Morfy CMS
 *
 * @package Morfy
 * @subpackage Plugins
 * @author Taufik Nurrohman <http://latitudu.com>
 * @copyright 2014 Romanenko Sergey / Awilum
 * @version 1.0.2
 *
 */

Morfy::factory()->addAction('before_render', function() {
    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');

    // Configuration data
    $config = Morfy::$config['admin_config'];
    // TXT file to save login token
    $auth = PLUGINS_PATH . '/admin/_.txt';
    // Set global variable `logged_in` as `false`
    Morfy::$config['logged_in'] = false;
    // Prepare the messages
    $notify = "";

    // Logging in...
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['token']) && ! empty($_POST['username']) && ! empty($_POST['password'])) {
            if(Morfy::factory()->checkToken($_POST['token'])) {
                if($_POST['username'] == $config['username'] && $_POST['password'] == $config['password']) {
                    // Login is valid. Now update the token file!
                    fwrite(fopen($auth, 'w'), Morfy::factory()->generateToken());
                } else {
                    // Invalid username or password.
                    $notify .= '<div class="' . $config['classes']['message'] . ' ' . $config['classes']['message'] . '-' . $config['classes']['error'] . '">' . $config['labels']['message_error_invalid'] . '</div>';
                }
            } else {
                // Invalid token.
                $notify .= '<div class="' . $config['classes']['message'] . ' ' . $config['classes']['message'] . '-' . $config['classes']['error'] . '">' . $config['labels']['message_error_token'] . '</div>';
            }
        } else {
            // Some required field is empty.
            $notify .= '<div class="' . $config['classes']['message'] . ' ' . $config['classes']['message'] . '-' . $config['classes']['error'] . '">' . $config['labels']['message_error_required'] . '</div>';
        }
    }

    if( ! file_exists($auth)) {
        // Create an empty TXT file to save your login token if not exist
        $handle = fopen($auth, 'w') or die('Cannot open file: ' . $auth);
        fwrite($handle, "");
    } else {
        // Change `$config['logged_in']` value to `true` if login session is valid
        Morfy::$config['logged_in'] = Morfy::factory()->checkToken(file_get_contents($auth)) ? true : false;
    }

    // Creating login page...
    if(trim(Morfy::factory()->getUrl(), '/') == 'admin/login') {
        $html  = "<!DOCTYPE html>\n";
        $html .= "<html dir=\"ltr\" class=\"" . $config['classes']['page_login'] . "\">\n";
        $html .= "  <head>\n";
        $html .= "    <meta charset=\"utf-8\">\n";
        $html .= (isset($_GET['redirect']) && Morfy::$config['logged_in'] === true) ? "      <meta http-equiv=\"refresh\" content=\"0;url=" . rtrim(Morfy::$config['site_url'], '/') . '/' . $_GET['redirect'] . "\">\n" : "";
        $html .= "    <title>" . $config['labels']['title_login'] . "</title>\n";
        $html .= "    <link href=\"" . Morfy::$config['site_url'] . "/plugins/admin/lib/css/shell.css\" rel=\"stylesheet\">\n";
        $html .= "  </head>\n";
        $html .= "  <body>\n";
        $html .= "    <div class=\"" . $config['classes']['page_wrapper'] . "\">\n";
        if($notify === "" && Morfy::$config['logged_in'] === true) {
            $html .= "    <div class=\"" . $config['classes']['message'] . " " . $config['classes']['message'] . "-" . $config['classes']['success'] . "\">" . $config['labels']['message_logged_in'] . " <a href=\"" . rtrim(Morfy::$config['site_url'], '/') . "/admin/logout\">" . $config['labels']['logout'] . "</a></div>\n";
        } else {
            $html .= "      " . $notify . "<form method=\"post\" action=\"" . rtrim(Morfy::$config['site_url'], '/') . "/admin/login" . (isset($_GET['redirect']) ? '?redirect=' . $_GET['redirect'] : "") . "\">\n";
            $html .= "        <input type=\"hidden\" name=\"token\" value=\"" . Morfy::factory()->generateToken(true) . "\">\n";
            $html .= "        <div><label>" . $config['labels']['username'] . "</label> <input type=\"text\" name=\"username\" placeholder=\"" . $config['labels']['username'] . "\" autofocus></div>\n";
            $html .= "        <div><label>" . $config['labels']['password'] . "</label> <input type=\"password\" name=\"password\" placeholder=\"" . $config['labels']['password'] . "\"></div>\n";
            $html .= "        <div><button type=\"submit\">" . $config['labels']['login'] . "</button></div>\n";
            $html .= "      </form>\n";
        }
        $html .= "    </div>\n";
        $html .= "  </body>\n";
        $html .= "</html>";
        echo $html;
        exit();
    }

    // Creating logout page...
    if(trim(Morfy::factory()->getUrl(), '/') == 'admin/logout') {

        // Destroy the login session...
        if(isset($_SESSION['security_token'])) {
            unset($_SESSION['security_token']);
        }

        // Redirect to login page if login session is not set.
        if( ! isset(Morfy::$config['logged_in']) || Morfy::$config['logged_in'] === false) {
            header('Location: login');
        }

        $html  = "<!DOCTYPE html>\n";
        $html .= "  <html dir=\"ltr\" class=\"" . $config['classes']['page_logout'] . "\">\n";
        $html .= "    <head>\n";
        $html .= "      <meta charset=\"utf-8\">\n";
        $html .= (isset($_GET['redirect'])) ? "      <meta http-equiv=\"refresh\" content=\"0;url=" . rtrim(Morfy::$config['site_url'], '/') . '/' . $_GET['redirect'] . "\">\n" : "";
        $html .= "      <title>" . $config['labels']['title_login'] . "</title>\n";
        $html .= "      <link href=\"" . Morfy::$config['site_url'] . "/plugins/admin/lib/css/shell.css\" rel=\"stylesheet\">\n";
        $html .= "    </head>\n";
        $html .= "  <body>\n";
        $html .= "    <div class=\"" . $config['classes']['page_wrapper'] . "\">\n";
        $html .= "    <div class=\"" . $config['classes']['message'] . " " . $config['classes']['message'] . "-" . $config['classes']['success'] . "\">" . $config['labels']['message_logged_out'] . " <a href=\"" . rtrim(Morfy::$config['site_url'], '/') . "/admin/login\">" . $config['labels']['login'] . "</a></div>\n";
        $html .= "    </div>\n";
        $html .= "  </body>\n";
        $html .= "</html>\n";
        echo $html;
        exit();
    }
});
