<?php
namespace PhpAPI2 {
    class Reflection {
        public static function GetFnParams($classInstance, $fnRef) {
            $reflectedFn = new \ReflectionMethod($classInstance, $fnRef);
            $result = array();
            foreach ($reflectedFn->getParameters() as $param) {
                array_push($result, $param->name);
            }
            return $result;
        }
    }
}
?>