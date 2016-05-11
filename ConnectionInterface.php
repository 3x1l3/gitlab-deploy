<?php
/**
 * This file is intended to be used with Deploy class. It will only call methods
 * from this interface thus this is what is to be used. to define the connections.
 * Default being FTP for now.
 */
interface ConnectionInterface
{
    /**
     * Constructor that will take 3 parameters host, user, pass for the sake of
     * connecting to the server in question.
     *
     * @param string $host Hostname to connect to
     * @param string $user Username to use to connect to host
     * @param string $pass Password to use to connect to host along with username.
     */
    public function __construct($host, $user, $pass);
    /**
     * Init the connection. So we can put files to the remote server.
     *
     * @return bool Did it initialize correctly?
     */
    public function initialize();
    /**
     * The concrete class needs a put method. RemotePath and local path.
     *
     * @param string $remotePath Path to put the file on the remote server.
     * @param mixed  $localPath  Path to local file that will be copied.
     *
     * @return bool Was it successful?
     */
    public function put($remotePath, $localPath);
}
