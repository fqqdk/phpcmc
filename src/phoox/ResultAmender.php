<?php
/**
 * Holds the ResultAmender class
 *
 * @author fqqdk <simon.csaba@ustream.tv>
 */

/**
 * Description of ResultAmender
 */
class ResultAmender extends PHPUnit_Framework_TestResult
{
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