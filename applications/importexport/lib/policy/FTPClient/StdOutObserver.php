<?php

class importexport_policy_FTPClient_StdOutObserver implements importexport_policy_FTPClient_ObserverInterface
{
	/**
	 * @abstract
	 * @param string $request
	 * @return void
	 */
	public function updateWithRequest($request)
	{
		echo 'PUT > '.$request;
	}

	/**
	 * @abstract
	 * @param string $message
	 * @param int $code
	 * @return void
	 */
	public function updateWithResponse($message, $code)
	{
		echo 'GET < '.$message;
	}
}
