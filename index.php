<?php
require_once('config.php');
$message = file_get_contents('php://input');
$date = new DateTime();
//file_put_contents('gitlab deployment '.$date->getTimestamp().'.json', $message );

$message = '{"object_kind":"push","event_name":"push","before":"34e5dc70b8f57cae878c97b50aeb513f324ac6d2","after":"e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","ref":"refs/heads/master","checkout_sha":"e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","message":null,"user_id":2,"user_name":"Chad","user_email":"chad@tangle.ca","user_avatar":"http://www.gravatar.com/avatar/6826fa0e424c9598c54a61c2f604cf53?s=80\u0026d=identicon","project_id":3,"project":{"name":"test","description":"","web_url":"http://192.168.0.140/exile/test","avatar_url":null,"git_ssh_url":"git@192.168.0.140:exile/test.git","git_http_url":"http://192.168.0.140/exile/test.git","namespace":"exile","visibility_level":0,"path_with_namespace":"exile/test","default_branch":"master","homepage":"http://192.168.0.140/exile/test","url":"git@192.168.0.140:exile/test.git","ssh_url":"git@192.168.0.140:exile/test.git","http_url":"http://192.168.0.140/exile/test.git"},"commits":[{"id":"da35c62e4be3406926366cae66ce7cda8d5f008c","message":"Commit 1 no new files\n","timestamp":"2016-05-09T16:28:22-06:00","url":"http://192.168.0.140/exile/test/commit/da35c62e4be3406926366cae66ce7cda8d5f008c","author":{"name":"Chad","email":"chadklassen1@gmail.com"},"added":[],"modified":["index.php"],"removed":[]},{"id":"e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","message":"Added new file in subfolder\n","timestamp":"2016-05-09T16:28:59-06:00","url":"http://192.168.0.140/exile/test/commit/e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","author":{"name":"Chad","email":"chadklassen1@gmail.com"},"added":["lib/libfile.php"],"modified":[],"removed":[]}],"total_commits_count":2,"repository":{"name":"test","url":"git@192.168.0.140:exile/test.git","description":"","homepage":"http://192.168.0.140/exile/test","git_http_url":"http://192.168.0.140/exile/test.git","git_ssh_url":"git@192.168.0.140:exile/test.git","visibility_level":0}}';
$hookObj = json_decode($message);

$config = '
  {
    "deployments": [
        {
          "url": "http://192.168.0.140/exile/test.git",
          "type": "FTP",
          "user": "",
          "password": "",
          "host": "tanglemedia.net",
          "path": "/public_html/"
        }
    ]
  }
';


/*
  For start of unit testing for deployment.
 */

$ftp = new FTPConnection();

$deployment = new Deploy();
$deployment->setDeploymentData(json_decode('{"object_kind":"push","event_name":"push","before":"34e5dc70b8f57cae878c97b50aeb513f324ac6d2","after":"e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","ref":"refs/heads/master","checkout_sha":"e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","message":null,"user_id":2,"user_name":"Chad","user_email":"chad@tangle.ca","user_avatar":"http://www.gravatar.com/avatar/6826fa0e424c9598c54a61c2f604cf53?s=80\u0026d=identicon","project_id":3,"project":{"name":"test","description":"","web_url":"http://192.168.0.140/exile/test","avatar_url":null,"git_ssh_url":"git@192.168.0.140:exile/test.git","git_http_url":"http://192.168.0.140/exile/test.git","namespace":"exile","visibility_level":0,"path_with_namespace":"exile/test","default_branch":"master","homepage":"http://192.168.0.140/exile/test","url":"git@192.168.0.140:exile/test.git","ssh_url":"git@192.168.0.140:exile/test.git","http_url":"http://192.168.0.140/exile/test.git"},"commits":[{"id":"da35c62e4be3406926366cae66ce7cda8d5f008c","message":"Commit 1 no new files\n","timestamp":"2016-05-09T16:28:22-06:00","url":"http://192.168.0.140/exile/test/commit/da35c62e4be3406926366cae66ce7cda8d5f008c","author":{"name":"Chad","email":"chadklassen1@gmail.com"},"added":[],"modified":["index.php"],"removed":[]},{"id":"e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","message":"Added new file in subfolder\n","timestamp":"2016-05-09T16:28:59-06:00","url":"http://192.168.0.140/exile/test/commit/e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","author":{"name":"Chad","email":"chadklassen1@gmail.com"},"added":["lib/libfile.php"],"modified":[],"removed":[]}],"total_commits_count":2,"repository":{"name":"test","url":"git@192.168.0.140:exile/test.git","description":"","homepage":"http://192.168.0.140/exile/test","git_http_url":"http://192.168.0.140/exile/test.git","git_ssh_url":"git@192.168.0.140:exile/test.git","visibility_level":0}}'));
$deployment->getConfiguration();
$deployment->run();
