<?php
/**
 * Holds the CollectListener class
 *
 * @author fqqdk <fqqdk@freemail.hu>
 */

/**
 * Description of CollectListener
 */
interface CollectListener
{
	/**
	 * This event is fired when an error occurs during class map collection
	 *
	 * @param string $error the error message
	 *
	 * @return void
	 */
	public function error($error);

	/**
	 * This event is fired when the search is started
	 *
	 * @return void
	 */
	public function searchStarted();

	/**
	 * This event is fired when the search is completed
	 *
	 * @return void
	 */
	public function searchCompleted();
}

?>