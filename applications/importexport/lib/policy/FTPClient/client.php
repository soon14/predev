<?php

class importexport_policy_FTPClient_client 
{
	/** @var resource */
	protected $timeout = 90;
	protected $transferMode = null;//暂时只支持被动模式

	protected $system = null;
	protected $features = null;


    /**
     * 连接到FTP服务器
     *
     * @params string $host FTP服务器地址
     * @params int    $port FTP服务器端口
     * @params int    $timeout FTP服务器端口
     *
     * @return bool
     */
    public function connect($host, $port, $timeout=90)
    {

		$this->connection = @fsockopen($host, $port, $errorCode, $errorMessage, $timeout);

        if ( is_resource($this->connection) === false )
		{
			return false;
		}

        stream_set_blocking($this->connection, true);
		stream_set_timeout($this->connection, $this->timeout);

		$response = $this->_getResponse();

        if ( $response['code'] !== 220 )
		{
			return false;
		}

        return true;
    }

	/**
	 *  登录到FTP服务器
     *
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return bool
	 */
	public function login($username, $password)
	{
		$response = $this->_request(sprintf('USER %s', $username));

		if ( $response['code'] !== 331 )
		{
			return false;
		}

		$response = $this->_request(sprintf('PASS %s', $password));

		if ( $response['code'] !== 230 )
		{
			return false;
		}

		return true;
	}

	/**
	 * Return the system name.
	 * @return string|bool If error returns FALSE
	 */
	public function getSystem()
	{
		if ( $this->system === null )
		{
			$this->system = $this->_getSystem();
		}

		return $this->system;
	}

	/**
	 * Return the features.
	 * @return array|bool If error returns FALSE
	 */
	public function getFeatures()
	{
		if ( $this->features === null )
		{
			$this->features = $this->_getFeatures();
		}

		return $this->features;
	}

	/**
	 * Close the connection.
	 * @return void
	 */
	public function disconnect()
	{
		$this->_request('QUIT');
		$this->connection = null;
	}

	/**
	 * 获取当前路径地址
     *
	 * @return string|bool If error, returns FALSE.
	 */
	public function getCurrentDirectory()
	{
		$response = $this->_request('PWD');

		if ( $response['code'] !== 257 )
		{
			return false;
		}

		$from = strpos($response['message'], '"') + 1;
		$to   = strrpos($response['message'], '"') - $from;
		$currentDirectory = substr($response['message'], $from, $to);
		return $currentDirectory;
	}

	/**
	 * Change the current directory on a FTP server.
	 * @param string $directory
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function changeDirectory($directory)
	{
		$response = $this->_request(sprintf('CWD %s', $directory));
		return ( $response['code'] === 250 );
	}

	/**
	 * Remove a directory.
	 * @param string $directory
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function removeDirectory($directory)
	{
		$response = $this->_request(sprintf('RMD %s', $directory));
		return ( $response['code'] === 250 );
	}

	/**
	 * Create a directory.
	 * @param string $directory
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function createDirectory($directory)
	{
		$response = $this->_request(sprintf('MKD %s', $directory));
		return ( $response['code'] === 257 );
	}

	/**
	 * Rename a file or a directory on the FTP server.
	 * @param string $oldName
	 * @param string $newName
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function rename($oldName, $newName)
	{
		$response = $this->_request(sprintf('RNFR %s', $oldName));

		if ( $response['code'] !== 350 )
		{
			return false;
		}

		$response = $this->_request(sprintf('RNTO %s', $newName));

		if ( $response['code'] !== 250 )
		{
			return false;
		}

		return true;
	}

	/**
	 * 删除FTP服务器文件
     *
	 * @param string $filename 文件名称
     *
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function removeFile($filename)
	{
		$response = $this->_request(sprintf('DELE %s', $filename));
		return ( $response['code'] === 250 );
	}

	/**
	 * Set permissions on a file via FTP.
	 * @param string $filename
	 * @param int $mode The new permissions, given as an octal value.
	 * @return bool If success return TRUE, fail return FALSE.
	 * @throws InvalidArgumentException
	 */
	public function setPermission($filename, $mode)
	{
		if ( is_integer($mode) === false or $mode < 0 or 0777 < $mode )
		{
			throw new InvalidArgumentException(sprintf('Invalid permission "%o" was given.', $mode));
		}

		$response = $this->_request(sprintf('SITE CHMOD %o %s', $mode, $filename));
		return ( $response['code'] === 200 );
	}

	/**
	 * Return a list of files in the given directory.
	 * @param string $directory
	 * @return array|bool If error, returns FALSE.
	 */
	public function getList($directory)
	{
		$dataConnection = $this->_openPassiveDataConnection();

		if ( $dataConnection === false )
		{
			return false;
		}

		$response = $this->_request(sprintf('NLST %s', $directory));

		if ( $response['code'] !== 150 )
		{
			return false;
		}

		$list = '';

		while ( feof($dataConnection) === false )
		{
			$list .= fread($dataConnection, 1024);
		}

		$list = trim($list);
		$list = preg_split("/[\n\r]+/", $list);

		return $list;
	}

	/**
	 * Return the size of the given file.
	 * @abstract
	 * @param string $filename
	 * @return int|bool If failed to get file size, returns FALSE
	 * @note Not all servers support this feature!
	 */
	public function getFileSize($filename)
	{
		if ( $this->_supports('SIZE') === false )
		{
			return false;
		}

		$this->size_response = $this->_request(sprintf('SIZE %s', $filename));

		if ( $this->size_response['code'] !== 213 )
		{
			return false;
		}

		if ( !preg_match('/^[0-9]{3} (?P<size>[0-9]+)$/', trim($response['message']), $matches) )
		{
			return false;
		}

		return intval($matches['size']);
	}

	/**
	 * Return the last modified time of the given file.
	 * @param string $filename
	 * @return int|bool Returns the last modified time as a Unix timestamp on success, or FALSE on error.
	 * @note Not all servers support this feature!
	 */
	public function getModifiedDateTime($filename)
	{
		if ( $this->_supports('MDTM') === false )
		{
			return false;
		}

		$response = $this->_request(sprintf('MDTM %s', $filename));

		if ( $response['code'] !== 213 )
		{
			return false;
		}

		if ( !preg_match('/^[0-9]{3} (?P<datetime>[0-9]{14})$/', trim($response['message']), $matches) )
		{
			return false;
		}

		return strtotime($matches['datetime'].' UTC');
	}

	/**
	 * Download a file from the FTP server.
	 * @param string $remoteFilename
	 * @param string $localFilename
	 * @param int $mode self::MODE_ASCII or self::MODE_BINARY
	 * @return bool If success return TRUE, fail return FALSE.
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function download($remoteFilename, $localFilename, $mode, $resume)
	{
        $modes = array(
            'MODE_ASCII'  => 'A',
            'MODE_BINARY' => 'I',
        );

		if ( array_key_exists($mode, $modes) === false )
		{
		    return false;
		}

		$localFilePointer = fopen($localFilename, 'wb');

		if ( is_resource($localFilePointer) === false )
		{
		    return false;
		}

        if ( !$this->type_response )
        {
            $this->type_response = $this->_request(sprintf('TYPE %s', $modes[$mode]));
            if ( $this->type_response['code'] !== 200 )
            {
                return false;
            }
        }

        if (!$this->download_dataConnection)
        {
            $this->download_dataConnection = $this->_openPassiveDataConnection();
            if ( $this->download_dataConnection === false )
            {
                return false;
            }
        }

        if( !$this->retr_response )
        {
            $this->retr_response = $this->_request(sprintf('RETR %s', $remoteFilename));
            if ( $this->retr_response['code'] !== 150 )
            {
                return false;
            }
        }

		while ( feof($this->download_dataConnection) === false )
		{
		   fwrite($localFilePointer, fread($this->download_dataConnection, 10240), 10240);
		}

		return true;
	}

	/**
	 * 将文件上传到FTP服务器
     *
	 * @param string $localFilename  本地文件路径
	 * @param string $remoteFilename FTP服务器文件路径
	 * @param string $mode MODE_ASCII or MODE_BINARY 默认使用二进制模式上传
     * @param int    $resume  上传本地文件指针位置
     *
	 * @return bool If success return true else return false
	 */
	public function upload($localFilename, $remoteFilename, $mode, $resume)
	{

        $modes = array(
            'MODE_ASCII'  => 'A',
            'MODE_BINARY' => 'I',
        );

		$localFilePointer = fopen($localFilename, 'rb');

		if ( is_resource($localFilePointer) === false )
		{
            return false;
		}

        if(!$this->type_response){
            $this->type_response = $this->_request(sprintf('TYPE %s', $modes[$mode]));
            if ( $this->type_response['code'] !== 200 )
            {
                return false;
            }
        }

        if(!$this->dataConnection){
            $this->dataConnection = $this->_openPassiveDataConnection();

            if ( $this->dataConnection === false )
            {
                return false;
            }
        }

        if(!$this->appe_response){
            $this->appe_response = $this->_request(sprintf('APPE %s', $remoteFilename));
            if ( $this->appe_response['code'] !== 150 )
            {
                return false;
            }
        }

        fseek($localFilePointer,$resume);
        while ( feof($localFilePointer) === false )
		{
			fwrite($this->dataConnection, fread($localFilePointer, 10240), 10240);
		}
		return true;
	}

	/**
	 * 被动模式打开数据连接
     *
	 * @return resource|bool
	 */
	protected function _openPassiveDataConnection()
	{
		$response = $this->_request('PASV');

		if ( $response['code'] !== 227 )
		{
			return false;
		}

		$serverInfo = $this->_parsePassiveServerInfo($response['message']);

		if ( $serverInfo === false )
		{
			return false;
		}

		$dataConnection = fsockopen($serverInfo['host'], $serverInfo['port'], $errorNumber, $errorString, $this->timeout);

		if ( is_resource($dataConnection) === false )
		{
			return false;
		}

		stream_set_blocking($dataConnection, true);
		stream_set_timeout($dataConnection, $this->timeout);

		return $dataConnection;
	}

	/**
	 * 解析返回信息 返回FTP连接地址和端口
	 * @param $message
	 * @return array|bool
	 */
	protected function _parsePassiveServerInfo($message)
	{
		if ( !preg_match('/\((?P<host>[0-9,]+),(?P<port1>[0-9]+),(?P<port2>[0-9]+)\)/', $message, $matches) )
		{
			return false;
		}

		$host = strtr($matches['host'], ',', '.');
		$port = ( $matches['port1'] * 256 ) + $matches['port2']; // low bit * 256 + high bit

		return array(
			'host' => $host,
			'port' => $port,
		);
	}

	/**
	 * 发送请求
	 * @param string $request
	 * @return array
	 */
	protected function _request($request)
	{
		$request = $request."\r\n";

		fputs($this->connection, $request);

		return $this->_getResponse();
	}

	/**
	 * 格式化返回参数
     *
	 * @return array
	 */
	protected function _getResponse()
	{
		$response = array(
			'code'    => 0,
			'message' => '',
		);

		while ( true )
		{
			$line = fgets($this->connection, 8129);
			$response['message'] .= $line;

            //如果是SSH连接 则跳出
            if( stripos($line,'SSH') !== false ){
                break;
            }

			if ( preg_match('/^[0-9]{3} /', $line) )
			{
				break;
			}
		}

		$response['code'] = intval(substr(ltrim($response['message']), 0, 3));

		return $response;
	}

	/**
	 * Return the system name.
	 * @return string|bool If error returns FALSE
	 */
	protected function _getSystem()
	{
		$response = $this->_request('SYST');

		if ( $response['code'] !== 215 )
		{
			return false;
		}

		$tokens = explode(' ', $response['message']);
		return $tokens[1];
	}

	/**
	 * Return the features.
	 * @return array|bool If error returns FALSE
	 */
	protected function _getFeatures()
	{
		$response = $this->_request('FEAT');

		if ( $response['code'] !== 211 )
		{
			return false;
		}

		$lines = explode("\n", $response['message']);
		$lines = array_map('trim', $lines);
		$lines = array_filter($lines);

		if ( count($lines) < 2 )
		{
			return false;
		}

		$lines = array_slice($lines, 1, count($lines) - 2);

		$features = array();

		foreach ( $lines as $line )
		{
			$tokens = explode(' ', $line);
			$feature =$tokens[0];
			$features[$feature] = $line;
		}

		return $features;
	}

	/**
	 * Determine if a specific command supported.
	 * @param string $command
	 * @return bool
	 */
	protected function _supports($command)
	{
		$features = $this->getFeatures();
		return array_key_exists($command, $features);
	}
}
