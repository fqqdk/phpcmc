<?php
/**
 * Holds the ResultAmender class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Amends functionality of the PHPUnit_Framework_TestResult class
 */
class ResultAmender extends PHPUnit_Framework_TestResult
{
	/**
	 * Adds special checking of ForeignError exceptions to better handle
	 * errors occuring in code run in separate processes
	 *
	 * @param PHPUnit_Framework_Test $test the test that is being run
	 * @param Exception              $e    the exception that occured
	 * @param mixed                  $time the current time
	 *
	 * @return void
	 */
	public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
	{
		if ($e instanceof ForeignError) {
            $this->errors[] = new ForeignFailure($test, $e);

            if ($this->stopOnFailure) {
                $this->stop();
            }

			foreach ($this->listeners as $listener) {
				$listener->addError($test, $e, $time);
			}

			$this->lastTestFailed = true;
			$this->time          += $time;
			return;
		}

		parent::addError($test, $e, $time);
	}
}

?>