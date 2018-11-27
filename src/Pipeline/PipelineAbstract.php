<?php
/**
 * abstract class for pipelines
 */
namespace Graviton\AnalyticsBase\Pipeline;

/**
 * @author   List of contributors <https://github.com/libgraviton/graviton/graphs/contributors>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://swisscom.ch
 */
abstract class PipelineAbstract {

    protected $params = [];
    protected const EMPTY_STRING = '__EMPTY__';
    protected $cleanCount = 4;

    public function setParams(array $params) {
        $this->params = $params;
    }

    public function hasParam($paramName, $checkMode = 'and') {
        if (is_null($checkMode)) {
            $checkMode = 'and';
        }

        if (!is_array($paramName)) {
            $paramName = [$paramName];
        }

        foreach ($paramName as $singleParamName) {
            $exists = isset($this->params[$singleParamName]);

            if ($exists === true && $checkMode == 'or') {
                return true;
            }

            if ($exists === false && $checkMode == 'and') {
                return false;
            }
        }

        if ($checkMode == 'or') {
            return false;
        }

        return true;
    }

    public function getParam($paramName) {
        if ($this->hasParam($paramName)) {
            return $this->params[$paramName];
        }
        return null;
    }

    public function get() {
        $cleaned = $this->cleanElements($this->getPipeline());
        for ($i = 0; $i < ($this->cleanCount+1); $i++) {
            $cleaned = $this->cleanElements($cleaned);
        }
        return array_values($cleaned);
    }

    private function cleanElements($pipeline) {
        $cleaned = [];
        foreach ($pipeline as $key => $value) {
            if (is_array($value)) {
                $value = $this->cleanElements($value);
            }

            if ((!is_array($value) && $value !== self::EMPTY_STRING) || (is_array($value) && !empty($value))) {
                if (!is_numeric($key)) {
                    $cleaned[$key] = $value;
                } else {
                    $cleaned[] = $value;
                }
            }
        }

        return $cleaned;
    }

    abstract protected function getPipeline();

}
