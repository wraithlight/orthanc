<?php
namespace PhpAPI2 {
    class Param {
        public function __construct(
            $name,
            $value
        ) {
            $this->Name = $name;
            $this->Value = $value;
        }
    }

    class Params {
        public static function GetUriParams($requestUri, $match) {           
            $result = array();

            $uriTree = Segments::ToNodes($requestUri);
            $matchTree = Segments::ToNodes($match);

            for($i = 0; $i < count($matchTree); $i++) {
                Segments::IsParameter($matchTree[$i]) &&
                array_push(
                    $result,
                    new Param(
                        Segments::NormalizeParameter($matchTree[$i]),
                        $uriTree[$i]
                    )
                );
            }

            return $result;
        }
        public static function GetBodyParams($request) {
            return array();
        }
        public static function GetQueryParams($queryString) {
            $result = array();
            if (empty($queryString)) {
                return $result;
            }
            $queries = explode("&", $queryString);
            foreach($queries as $query) {
                $queryKvp = explode("=", $query);
                array_push(
                    $result,
                    new Param(
                        $queryKvp[0],
                        $queryKvp[1]
                    )
                );
            }
            return $result;
        }
    }
}
?>