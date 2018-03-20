<?php

interface importexport_policy_FTPClient_ObservableInterface
{
	/**
	 * Set an observer.
	 * @param Suin_FTPClient_ObserverInterface $observer
	 */
	public function setObserver(importexport_policy_FTPClient_ObserverInterface $observer);
}
